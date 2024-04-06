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

namespace Anowave\Ec\Block;

use Magento\Framework\View\Element\Template;

class Track extends Template
{
	/**
	 * Google Tag Manager Data
	 *
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper;
	
	/**
	 * @var \Magento\Framework\DataObject
	 */
	protected $adwords;
	
	/**
	 * @var \Anowave\Ec\Helper\Datalayer
	 */
	protected $dataLayer;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var \Magento\Directory\Model\CurrencyFactory
	 */
	protected $currencyFactory;
	
	/**
	 * @var \Magento\Framework\Filesystem\Driver\File
	 */
	protected $driverFile;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;
	
	/**
	 * Load inline
	 * 
	 * @var string
	 */
	private $inline = false;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
	    \Magento\Directory\Model\CurrencyFactory $currencyFactory,
	    \Magento\Framework\Filesystem\Driver\File $driverFile,
	    \Psr\Log\LoggerInterface $logger,
		array $data = []
	) 
	{
		/**
		 * Set Helper
		 * 
		 * @var \Anowave\Ec\Helper\Data 
		 */
		$this->_helper = $helper;
		
		/**
		 * Set dataLayer 
		 * 
		 * @var \Anowave\Ec\Helper\Datalayer
		 */
		$this->dataLayer = $dataLayer;
		
		/**
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
		 */
		$this->protocol = $protocol;
		
		
		/**
		 * Set currency factory 
		 * 
		 * @var \Magento\Directory\Model\CurrencyFactory $currencyFactory
		 */
		$this->currencyFactory = $currencyFactory;
		
		/**
		 * Set logger 
		 * 
		 * @var \Anowave\Ec\Block\Track $logger
		 */
		$this->logger = $logger;
		
		/**
		 * Set driver file 
		 * 
		 * @var \Magento\Framework\Filesystem\Driver\File $driverFile
		 */
		$this->driverFile = $driverFile;

		/**
		 * Parent constructor
		 */
		parent::__construct($context, $data);
		
		/**
		 * Make block non-cachable
		 * 
		 * @var boolean
		 */
		$this->_isScopePrivate = false;
	}
	
	/**
	 * Make block non-cachable
	 *
	 * @see \Magento\Framework\View\Element\AbstractBlock::isScopePrivate()
	 */
	public function isScopePrivate()
	{
		return false;
	}
	
	/**
	 * Get helper
	 * 
	 * @return \Anowave\Ec\Helper\Data
	 */
	public function getHelper()
	{
		if ($this->_helper)
		{
			return $this->_helper;
		}
		else 
		{
			throw new \Exception('\Anowave\Ec\Helper\Data is not instantiated.');
		}
	}
	
	/**
	 * Get sales order collection
	 * 
	 * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	public function getSalesOrderCollection()
	{
		return $this->_helper->getSalesOrderCollection();
	}

	/**
	 * Determine page type
	 * 
	 * @return string
	 */
	public function getPageType()
	{
		switch($this->getRequest()->getControllerName())
		{
			case 'index':		return 'home';
			case 'cart':		return 'cart';
			case 'category':	return 'category';
			case 'product': 	return 'product';
			case 'result':		return 'searchresults';
				default:		return 'other';
		}
	}
	
	/**
	 * Get multicheckout push
	 * 
	 * @param string $step
	 * @return @return JSON|false
	 */
	public function getMultiCheckoutPush($step)
	{
	    return $this->_helper->getMultiCheckoutPush($this,$step);
	}

	/**
	 * Get purchase google tag params
	 *
	 * @return JSON|false
	 */
	public function getPurchaseGoogleTagParams()
	{
		return $this->_helper->getPurchaseGoogleTagParams($this);
	}
	
	/**
	 * Get visitor push
	 *
	 * @return JSON|false
	 */
	public function getVisitorPush()
	{
		return $this->_helper->getVisitorPush($this);
	}
	
	/**
	 * Get details push
	 *
	 * @return JSON|false
	 */
	public function getDetailPushForward()
	{
		return $this->_helper->getDetailPushForward($this);
	}

	/**
	 * Get impressions push
	 *
	 * @return JSON|false
	 */
	public function getImpressionPushForward()
	{
		return $this->_helper->getImpressionPushForward($this);
	}

	/**
	 * Get search push
	 *
	 * @return JSON|false
	 */
	public function getSearchPush()
	{
		return $this->_helper->getSearchPush($this);
	}
	
	public function getConversion()
	{
		return $this->_helper->getConversion($this);
	}
	
	/**
	 * Get store configuration variable 
	 * 
	 * @param string $config
	 */
	public function getConfig($config)
	{
		return $this->_helper->getConfig($config);
	}
	
	public function getBodySnippet()
	{
		return $this->getHelper()->getBodySnippet();
	}
	
	public function getHeadSnippet()
	{
		return $this->getHelper()->getHeadSnippet();
	}
	
	/**
	 * Get after <body> content
	 * 
	 * @return string
	 */
	public function afterBody()
	{
		return $this->getHelper()->afterBody();
	}

	/**
	 * Get Adwords Data Object
	 * 
	 * @return \Magento\Framework\DataObject
	 */
	public function getAdwords()
	{
		if (!$this->adwords)
		{
			$this->adwords = new \Magento\Framework\DataObject(
			[
				'google_conversion_id' 			=> $this->getConfig('ec/adwords/conversion_id'),
				'google_conversion_label' 		=> $this->getConfig('ec/adwords/conversion_label'),
				'google_conversion_language'	=> $this->getConfig('ec/adwords/conversion_language'),
				'google_conversion_format'		=> $this->getConfig('ec/adwords/conversion_format'),
				'google_conversion_color' 		=> $this->getConfig('ec/adwords/conversion_color'),
				'google_conversion_currency' 	=> $this->getConfig('ec/adwords/conversion_currency')
			]);
		}
		
		return $this->adwords;
	}
	
	/**
	 * Get converted Adwords revenue 
	 * 
	 * @return float
	 */
	public function getAdwordsRevenue() : float
	{
	    $revenue = (float) $this->getRevenue();

	    /**
	     * Get current currency 
	     * 
	     * @var string $current_currency
	     */
	    $current_currency = $this->getHelper()->getCurrency();
	    
	    /**
	     * Get adwords currency
	     */
	    $adwords_currency = $this->getAdWordsCurrency();
	    
	    if ($adwords_currency)
	    {
    	    if (strtolower($current_currency) !== strtolower($adwords_currency))
    	    {
    	        $rate = $this->currencyFactory->create()->load($current_currency)->getAnyRate($adwords_currency);
    
    	        $revenue *= $rate;
    	    }
	    }

	    return $revenue;
	}
	
	public function getAdWordsCurrency() : string
	{
	    return (string) $this->getConfig('ec/adwords/conversion_currency');
	}
	
	/**
	 * Get orders collection
	 * 
	 * @return array
	 */
	public function getOrders()
	{
		return $this->getHelper()->getOrders($this);
	}
	
	/**
	 * Get transaction revenue
	 * 
	 * @return float
	 */
	public function getRevenue()
	{
		$revenue = 0;
		
		foreach ($this->getHelper()->getOrders($this) as $order)
		{
			$revenue = $this->getHelper()->getRevenue($order);
		}
		
		return $revenue;
	}
	
	/**
	 * Get datalayer helper 
	 * 
	 * @return \Anowave\Ec\Helper\Datalayer
	 */
	public function getDatalayer()
	{
		return $this->dataLayer;
	}
	
	/**
	 * Load inline script 
	 * 
	 * @return string
	 */
	public function getScript() : string
	{
	    $content = [];
	    
	    if ($this->inline)
	    {
    	    foreach(['Anowave_Ec' => 'Anowave/Ec/view/frontend/web/js/ec.js'] as $module => $script)
    	    {
    	        try
    	        {
    	            $path = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . $script;
    	            
    	            $content[] = $this->driverFile->fileGetContents($path);
    	            
    	        }
    	        catch (\Exception $e)
    	        {
    	            $this->logger->debug($e->getMessage());
    	        }
    	    }
	    }

	    return join(PHP_EOL, $content);
	}
	
	/**
	 * Escape strig 
	 * 
	 * @param string $string
	 */
	public function escape($string)
	{
		return $this->_helper->escape($string);
	}
	
	/**
	 * No cache lifetime
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::getCacheLifetime()
	 */
	protected function getCacheLifetime()
	{
		return null;
	}
	
	/**
	 * Do not load from cache
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::_loadCache()
	 */
	protected function _loadCache()
	{
		$collectAction = function () 
		{
			if ($this->hasData('translate_inline')) 
			{
				$this->inlineTranslation->suspend($this->getData('translate_inline'));
			}
			
			$this->_beforeToHtml();
			
			return $this->_toHtml();
		};
		
		$html = $collectAction();
		
		if ($this->hasData('translate_inline')) 
		{
			$this->inlineTranslation->resume();
		}
		
		return $html;
	}
	
	/**
	 * Never save cache for this block
	 * 
	 * @see \Magento\Framework\View\Element\AbstractBlock::_saveCache()
	 */
	protected function _saveCache($data)
	{
		return false;
	}
	
	/**
	 * Render GTM
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->_helper->isActive())
		{
			return '';	
		}
		
		return $this->_helper->filter
		(
			parent::_toHtml()
		);
	}
}
