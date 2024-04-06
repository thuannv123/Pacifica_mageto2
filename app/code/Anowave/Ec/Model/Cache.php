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

namespace Anowave\Ec\Model;

class Cache extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{
	const CACHE_LISTING 				= 'ec_cache_listing_';
	const CACHE_DETAILS 				= 'ec_cache_details_';
	const CACHE_LISTING_WIDGET 			= 'ec_cache_listing_widget_';
	const CACHE_LISTING_PRODUCT_WIDGET 	= 'ec_cache_listing_product_widget_';
	
	/**
	 * Cache type code unique among all cache types
	 */
	const TYPE_IDENTIFIER = 'ec_cache';
	
	/**
	 * Cache tag used to distinguish the cache type from all other cache
	 */
	const CACHE_TAG = 'EC';
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Magento\Framework\HTTP\Header
	 */
	protected $headerService;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry = null;
	
	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepositoryFactory;

	/**
	 * @var \Magento\Framework\App\Cache\StateInterface
	 */
	protected $cacheState;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\HTTP\Header $headerService
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepositoryFactory
	 * @param \Magento\Framework\App\Cache\StateInterface $cacheState
	 */
	public function __construct
	(
		\Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\HTTP\Header $headerService,
		\Magento\Framework\Registry $registry,
		\Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepositoryFactory,
	    \Magento\Framework\App\Cache\StateInterface $cacheState
	)
	{
		parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
		
		/**
		 * Set store manager
		 *
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set header service
		 *
		 * @var \Anowave\Ec\Model\Cache $headerService
		 */
		$this->headerService = $headerService;
		
		/**
		 * Set registry
		 *
		 * @var \Anowave\Ec\Model\Cache $registry
		 */
		$this->registry = $registry;
		
		/**
		 * Set customer repository interface
		 *
		 * @var \Anowave\Ec\Model\Cache $customerRepositoryInterface
		 */
		$this->customerRepositoryFactory = $customerRepositoryFactory;
		
		/**
		 * @var \Magento\Framework\App\Cache\StateInterface $cacheState
		 */
		$this->cacheState = $cacheState;
	}
	
	/**
	 * Enforce marking with a tag
	 *
	 * {@inheritdoc}
	 */
	public function save($data, $identifier, array $tags = [], $lifeTime = null)
	{
		if (!$this->useCache())
		{
			return false;
		}
		
		return parent::save(serialize($data), $this->getCacheId($identifier), [self::CACHE_TAG], 600);
	}
	
	/**
	 * Load cache
	 *
	 * @see \Magento\Framework\Cache\Frontend\Decorator\Bare::load()
	 */
	public function load($identifier)
	{ 
		if (!$this->useCache())
		{
			return false;
		}
		
		return unserialize($this->_getFrontend()->load($this->getCacheId($identifier)));
	}
	
	/**
	 * Generate unique cache id
	 *
	 * @param string $prefix
	 */
	protected function generateCacheId($prefix)
	{
		/**
		 * Push current store to make cache store specific
		 *
		 * @var int
		 */
		$p[] = $this->storeManager->getStore()->getId();
		
		/**
		 * Add website id
		 */
		$p[] = $this->storeManager->getStore()->getWebsiteId();
		
		/**
		 * Add currency
		 */
		$p[] = $this->storeManager->getStore()->getCurrentCurrencyCode();
		
		/**
		 * Check for mobile users
		 */
		if (version_compare(phpversion(), '8.0.0', '<')) 
		{
		    $p[] = \Zend_Http_UserAgent_Mobile::match($this->headerService->getHttpUserAgent(),$_SERVER);
		}
		else 
		{
		    if (!(php_sapi_name() == 'cli'))
		    {
		        if(!empty($_SERVER['HTTP_USER_AGENT']))
		        {
		            $p[] = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
		        };
		    }
		    else 
		    {
		        $p[] = php_sapi_name();
		    }
		}
		
		/**
		 * Push request URI
		 *
		 * @var string
		 */
		$p[] =
		[
			$_SERVER['REQUEST_URI']
		];
		
		foreach (array($_GET, $_POST, $_FILES) as $request)
		{
			if ($request)
			{
				$p[] = $request;
			}
		}
		
		if ($this->registry->registry('cache_session_customer_id') > 0)
		{
			$customer = $this->customerRepositoryFactory->create()->getById($this->registry->registry('cache_session_customer_id'));
			
			/**
			 * Add customer group to key
			 */
			$p[] = $customer->getGroupId();
		}
		
		$p = md5(serialize($p));
		
		/**
		 * Merge
		 */
		return "{$prefix}_{$p}";
	}
	
	/**
	 * Getenerate unique cache id
	 *
	 * @param string $identifier
	 */
	public function getCacheId($identifier)
	{
		return $this->generateCacheId($identifier);
	}
	
	/**
	 * Check if can use cache
	 *
	 * @return bool
	 */
	protected function useCache()
	{
	    return $this->cacheState->isEnabled(static::TYPE_IDENTIFIER);
	}
}