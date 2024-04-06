<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Model\Api\Measurement;

class Protocol
{
    const DEFAULT_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:93.0) Gecko/20100101 Firefox/93.0';
    
	/**
	 * Client ID
	 *
	 * @var UUID
	 */
	protected $cid = null;
	
	/** 
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * @var \Magento\Sales\Model\OrderFactory
	 */
	protected $orderFactory;
	
	/**
	 * @var \Magento\Framework\Session\SessionManagerInterface
	 */
	protected $session = null;
	
	/**
	 * @var \Magento\Config\Model\ResourceModel\Config
	 */
	protected $resourceConfig;
	
	/**
	 * Errors array 
	 * 
	 * @var array
	 */
	protected $errors = [];
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
	 */
	protected $attribute;
	
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;
	
	/**
	 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory
	 */
	protected $transactionFactory;
	
	/**
	 * @var \Anowave\Ec\Model\Random
	 */
	protected $random;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Magento\Sales\Model\OrderFactory $orderFactory
	 * @param \Magento\Framework\Session\SessionManagerInterface $session
	 * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
	 * @param \Anowave\Ec\Model\Random $random
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Config\Model\ResourceModel\Config $resourceConfig,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	    \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory,
	    \Anowave\Ec\Model\Random $random
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data
		 */
		$this->helper = $helper;
		
		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set product factory 
		 * 
		 * @var \Magento\Catalog\Model\ProductFactory
		 */
		$this->productFactory = $productFactory;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set order factory 
		 * 
		 * @var \Magento\Sales\Model\OrderFactory
		 */
		$this->orderFactory = $orderFactory;
		
		/**
		 * Set session 
		 * 
		 * @var \Magento\Framework\Session\SessionManagerInterface
		 */
		$this->session = $session;
		
		/**
		 * Set resource config
		 * 
		 * @var \Magento\Config\Model\ResourceModel\Config
		 */
		$this->resourceConfig = $resourceConfig;
		
		/**
		 * Set attribute factory 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $attribute
		 */
		$this->attribute = $attribute;
		
		/**
		 * Set scope config
		 * 
		 * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
		 */
		$this->scopeConfig = $scopeConfig;
		
		/**
		 * Set transaction factory 
		 * 
		 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionFactory
		 */
		$this->transactionFactory = $transactionFactory;
		
		/**
		 * Set random model
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $random
		 */
		$this->random = $random;
	}
	
	/**
	 * Log message 
	 * 
	 * @param string $message
	 */
	public function log($message)
	{
		$log = $this->helper->getConfig('ec/logs/log');

		if ($log)
		{
			$log = (array) @unserialize($log);
			
			array_unshift($log, $message);
		}
		else 
		{
			$log = [$message];
		}
		
		/**
		 * Limit log to latest 10 events
		 * 
		 * @var []
		 */
		$log = array_filter(array_slice($log,0,10));
		
		/**
		 * Save log
		 */
		$this->resourceConfig->saveConfig('ec/logs/log', serialize($log), \Magento\Framework\App\Config::SCOPE_TYPE_DEFAULT, 0);
		
		return $this;
	}
	
	/**
	 * Track order by id
	 * 
	 * @param int $id
	 */
	public function purchaseById($id = null)
	{
		$order = $this->orderFactory->create()->load($id);
		
		if ($order && $order->getId())
		{
			return $this->purchase($order);
		}
		
		return false;
	}
	
	/**
	 * Track order
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * @param string $reverse
	 * @throws \Exception
	 * @return \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	public function purchase(\Magento\Sales\Model\Order $order, $reverse = false)
	{
	    /**
	     * Reset errors
	     *
	     * @var \Anowave\Ec\Model\Api\Measurement\Protocol $errors
	     */
	    $this->errors = [];
	    
		/**
		 * Get default parameters
		 *
		 * @var []
		 */
		$default = $this->getDefaultParameters($order);
		
		/**
		 * Purchase payload
		 *
		 * @var []
		*/
		$default['pa']	= 'purchase';
		$default['ni']  = 1;
		$default['ti']	= $order->getIncrementId();
		$default['tr']	= (float) $this->helper->getRevenue($order);
		$default['ts']	= (float) $order->getShippingInclTax();
		$default['tt']	= (float) $order->getTaxAmount();
		$default['ta']	= $this->helper->escape
		(
			$this->helper->getStoreName()
		);
		
		$default['cu'] = $order->getOrderCurrencyCode();
		
		/**
		 * Check if this is transaction reversal
		 */
		if ($reverse)
		{
			$default['tr'] *= -1;
			$default['ts'] *= -1;
			$default['tt'] *= -1;
		}
		
		/**
		 * Default start position
		 *
		 * @var int
		*/
		$index = 1;

		/**
		 * Loop products
		 */
		foreach ($this->getProducts($order) as $product)
		{
			$default["pr{$index}id"] = 			@$product['id'];
			$default["pr{$index}nm"] = 			@$product['name'];
			$default["pr{$index}ca"] = 			@$product['category'];
			$default["pr{$index}pr"] = 	(float) @$product['price'];
			$default["pr{$index}qt"] = 	(int)   @$product['quantity'];
			$default["pr{$index}br"] = (string) @$product['brand'];
			
			/**
			 * Check if reverse and reverse quantity
			 */
			if ($reverse)
			{
				$default["pr{$index}qt"] *= -1;
				$default["pr{$index}pr"] *= -1;
			}
		
			$index++;
		}

		/**
		 * Init CURL
		 *
		 * @var Resource
		 */
		$analytics = curl_init('https://ssl.google-analytics.com/collect');
			
		curl_setopt($analytics, CURLOPT_HEADER, 		0);
		curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($analytics, CURLOPT_POST, 			1);
		curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($analytics, CURLOPT_USERAGENT,		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			
		/**
		 * Get Universal Analytics ID
		 *
		 * @var string
		*/
		$ua = $this->getUA($order);
		
		if ($ua)
		{
			$data = $default;
			
			curl_setopt($analytics, CURLOPT_POSTFIELDS, utf8_encode
			(
				http_build_query($data)
			));
		}
		else 
		{
		    $this->errors[] = __('Please specify Universal Analytics Tracking ID in General options');
		    
		    return false;
		}
		
		
		
		try
		{
			$response = curl_exec($analytics);

			if (!curl_error($analytics) && $response)
			{
				return $this;
			}
			else 
			{
				throw new \Exception(curl_error($analytics));
			}
		}
		catch (\Exception $e)
		{
			$this->errors[] = $e->getMessage();
		}
		
		if ($this->errors)
		{
		    return false;
		}

		return $this;
	}
	
	/**
	 * Cancel and reverse order 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 */
	public function cancel(\Magento\Sales\Model\Order $order)
	{
	    /**
	     * Reset errors
	     * 
	     * @var \Anowave\Ec\Model\Api\Measurement\Protocol $errors
	     */
	    $this->errors = [];
	    
	    try 
	    {
	        $collection = $this->transactionFactory->create()->addFieldToFilter('ec_order_id', $order->getId())->addFieldToFilter('ec_track', \Anowave\Ec\Helper\Constants::FLAG_TRACKED);
    	    
    	    /**
    	     * Check if order is found inside the ae_ec table
    	     */
    	    if (!$collection->getSize())
    	    {
    	        $this->errors[] = __('Reverse transaction to Google Analytics skipped.');
    	        
    	        return false;
    	    }
	    }
	    catch (\Exception $e)
	    {
	        $this->errors[] = $e->getMessage();
	    }
	    
	    /**
	     * Reverse transaction
	     */
	    $this->purchase($order, true);
	    
	    /**
	     * Check errors
	     */
		if ($this->errors)
		{
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Get UA-ID
	 *
	 * @param \Magento\Sales\Model\Order $order
	 * @return string
	 */
	public function getUA(\Magento\Sales\Model\Order $order = null)
	{
		if ($order && $order->getId())
		{
			return trim
			(
				(string) $this->scopeConfig->getValue('ec/general/account', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStore())
			);
		}

		return trim
		(
			(string) $this->helper->getConfig('ec/general/account')
		);
	}

	/**
	 * Get default parameters 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * 
	 * @return array
	 */
	protected function getDefaultParameters(\Magento\Sales\Model\Order $order = null)
	{
		return array
		(
			'v' 	=> 1,
			'tid' 	=> $this->getUA($order),
			'cid' 	=> $this->getCID(),
			't'		=> 'pageview',
			'dp'	=> "/{$this->getDp()}",
			'dh'	=> PHP_SAPI == 'cli' ? $this->getBaseHost()      : $_SERVER['HTTP_HOST'],
			'ua'	=> PHP_SAPI == 'cli' ? $this->getBaseUserAgent() : $_SERVER['HTTP_USER_AGENT']
		);
	}
	
	/**
	 * Get base URL host
	 * 
	 * @return string
	 */
	protected function getBaseHost() : string
	{
	    return parse_url($this->helper->getStoreManager()->getStore()->getBaseUrl(), PHP_URL_HOST);
	}
	
	/**
	 * Get default user agent
	 * 
	 * @return string
	 */
	protected function getBaseUserAgent() : string
	{
	    return static::DEFAULT_USER_AGENT;
	}

	/**
	 * Get Client ID
	 *
	 * @var UUID
	 */
	public function getCID()
	{
		if (!$this->cid)
		{
			/**
			 * Load CID from session
			 *
			 * @var UUID
			 */
			$this->cid = $this->session->getCID();
				
			if (!$this->cid)
			{
			    /**
			     * Generate randon CID
			     * 
			     * @var \Anowave\Ec\Model\Api\Measurement\Protocol $cid
			     */
				$this->cid = $this->random->getCID();

				/**
				 * Set CID into session
				 */
				$this->session->setCID($this->cid);
			}
		}

		return $this->cid;
	}
	
	/**
	 * Set client id 
	 * 
	 * @param string $cid
	 * @return \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	public function setCID($cid = null)
	{
	    if ($cid)
	    {
	       $this->cid = $cid;
	    }
	    
	    return $this;
	}

	/**
	 * Get document path
	 *
	 * @return string
	 */
	protected function getDp()
	{
		return ltrim(str_replace(array('http://', 'https://', (string) @$_SERVER['HTTP_HOST']), '', (string) @$_SERVER['HTTP_REFERER']),'/');
	}

	/**
	 * Get order products array
	 *
	 * @param Mage_Sales_Model_Order $order
	 * @return []
	 */
	public function getProducts(\Magento\Sales\Model\Order $order = null)
	{
		/**
		 * Order products array
		 * 
		 * @var []
		 */
		$products = [];
		
		if ($order->getIsVirtual())
		{
			$address = $order->getBillingAddress();
		}
		else
		{
			$address = $order->getShippingAddress();
		}

		foreach ($order->getAllVisibleItems() as $item)
		{
			$collection = [];
			
			if ($item->getProduct())
			{
				$entity = $this->productFactory->create()->load
				(
					$item->getProduct()->getId()
				);
				
				$collection = $entity->getCategoryIds();
			}

			if ($collection)
			{
				$category = $this->categoryRepository->get(end($collection));
			}
			else 
			{
				$category = null;
			}

			/**
			 * Get product name
			*/
			$args = new \stdClass();
				
			$args->id 	= $this->helper->getIdentifierItem($item);
			$args->name = $item->getName();

			/**
			 * Product variant(s)
			 * 
			 * @var []
			 */
			$variant = [];

			if ('configurable' === $item->getProductType())
			{
				$options = (array) $item->getProductOptions();
				
				if (isset($options['info_buyRequest']))
				{
					$info = new \Magento\Framework\DataObject($options['info_buyRequest']);
					
					/**
					 * Construct variant
					 */
					foreach ((array) $info->getSuperAttribute() as $id => $option)
					{
						/**
						 * Load attribute 
						 * 
						 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
						 */
						$attribute = $this->attribute->create()->load($id);
						
						if ($attribute->usesSource())
						{
							$variant[] = join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER_ATT, array
							(
								$this->helper->escape($attribute->getFrontendLabel()),
								$this->helper->escape($attribute->getSource()->getOptionText($option))
							));
						}
					}
				}
			}

			$data = 
			[
				'name' 		=> $this->helper->escape($args->name),
				'id'		=> $this->helper->escape($args->id),
				'price' 	=> $item->getPrice(),
				'quantity' 	=> $item->getQtyOrdered(),
				'variant'	=> join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant)
			];
			
			if ($category)
			{
				$data['category'] = $this->helper->escape($category->getName());
			}
			
			$products[] = $data;
		}

		return $products;
	}
	
	public function fallback($params = [])
	{
		/**
		 * Get default parameters 
		 * 
		 * @var array $default
		 */
		$default = $this->getDefaultParameters();
		
		$payload = function() use ($default)
		{
			return $default;
		};
		
		if (isset($params['data']) && is_array($params['data']) && count($params['data']) > 0)
		{
			$ecommerce = reset($params['data'])['ecommerce'];
			
			if (isset($ecommerce['detail']))
			{
				$payload = function() use ($default, $ecommerce)
				{
					$default['t'] 	= 'event';
					$default['ec'] 	= 'Ecommerce';
					$default['ea'] 	= 'Detail';
					$default['pa']	= 'detail';
					$default['ni']  = 1;
					
					$index = 1;
					
					foreach ($ecommerce['detail']['products'] as $product)
					{
						$default["pr{$index}id"] = $product['id'];
						$default["pr{$index}nm"] = $product['name'];
						$default["pr{$index}ca"] = $product['category'];
						$default["pr{$index}pr"] = $product['price'];
						$default["pr{$index}br"] = $product['brand'];
						
						$index++;
					}
					
					return $default;
				};
			}
			elseif (isset($ecommerce['impressions']))
			{
				$payload = function() use ($default, $ecommerce)
				{
					$index = 1;
					
					foreach ($ecommerce['impressions'] as $product)
					{
						$default["il1nm"] 				= $product['list'];
						$default["il1pi{$index}nm"] 	= $product['name'];
						$default["il1pi{$index}id"] 	= $product['id'];
						$default["il1pi{$index}ca"] 	= $product['category'];
						$default["il1pi{$index}pr"] 	= $product['price'];
						$default["il1pi{$index}br"] 	= $product['brand'];
						
						$index++;
					}
					
					return $default;
				};
			}
			elseif (isset($ecommerce['purchase']))
			{
				$payload = function() use ($default, $ecommerce)
				{
					$default['pa']	= 'purchase';
					$default['ni']  = 1;
					$default['ti']	= $ecommerce['purchase']['actionField']['id'];
					$default['tr']	= $ecommerce['purchase']['actionField']['revenue'];
					$default['ts']	= $ecommerce['purchase']['actionField']['shipping'];
					$default['tt']	= $ecommerce['purchase']['actionField']['tax'];
					$default['ta']	= $ecommerce['purchase']['actionField']['affiliation'];
					
					$index = 1;
					
					foreach ($ecommerce['purchase']['products'] as $product)
					{
						$default["pr{$index}id"] = $product['id'];
						$default["pr{$index}nm"] = $product['name'];
						$default["pr{$index}ca"] = $product['category'];
						$default["pr{$index}pr"] = $product['price'];
						$default["pr{$index}br"] = $product['brand'];
						
						$index++;
					}
					
					return $default;
				};
			}
		}
		
		$analytics = curl_init('https://ssl.google-analytics.com/collect');
		
		curl_setopt($analytics, CURLOPT_HEADER, 		0);
		curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($analytics, CURLOPT_POST, 			1);
		curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($analytics, CURLOPT_USERAGENT,		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		
		/**
		 * Get Universal Analytics ID
		 *
		 * @var string
		 */
		$ua = $this->getUA();
		
		if ($ua)
		{
			$data = $payload();
			
			curl_setopt($analytics, CURLOPT_POSTFIELDS, utf8_encode
			(
				http_build_query($data)
			));
		}
		
		try
		{
			$response = curl_exec($analytics);
			
			if (!curl_error($analytics) && $response)
			{
				return true;
			}
		}
		catch (\Exception $e){}
		
		return false;
	}
	
	/**
	 * Get root category id
	 *
	 * @param unknown $store
	 * @throws \Exception
	 */
	public function getStoreRootCategoryId($store)
	{
		if (is_int($store))
		{
			$store = $this->storeManager->getStore($store);
		}
	
		if (is_string($store))
		{
			foreach ($this->storeManager->getStores() as $model)
			{
				if ($model->getCode() == $store)
				{
					$store = $model;
						
					break;
				}
			}
		}
	
		if ($store instanceof \Magento\Store\Model\Store)
		{
			return $store->getRootCategoryId();
		}
	
		throw new \Exception("Store $store does not exist anymore");
	}
	
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Build variant parameter
	 *
	 * @var [] $variant
	 * @return string
	 */
	protected function getVariant($variant = array())
	{
		return join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant);
	}
}