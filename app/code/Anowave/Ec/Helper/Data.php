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

namespace Anowave\Ec\Helper;

use Magento\Store\Model\Store;
use Anowave\Package\Helper\Package;
use Magento\Framework\Registry;

class Data extends \Anowave\Package\Helper\Package
{
	/**
	 * Variant delimiter
	 *
	 * @var string
	 */
	const VARIANT_DELIMITER = '-';
	
	/**
	 * Variant attributes delimiter
	 *
	 * @var string
	 */
	const VARIANT_DELIMITER_ATT = ':';
	
	/**
	 * Asunc events
	 * 
	 * @var boolean
	 */
	const USE_ASYNC_EVENTS = false;
	
	/**
	 * Package name
	 * @var string
	 */
	protected $package = 'MAGE2-GTM';
	
	/**
	 * Config path 
	 * @var string
	 */
	protected $config = 'ec/general/license';
	
	/**
	 * Order products array 
	 * 
	 * @var array
	 */
	private $_orders = []; 
	
	/**
	 * Brand map (lazy load)
	 * 
	 * @var array
	 */
	private $_brandMap = [];
	
	/**
	 * Category map 
	 * 
	 * @var array
	 */
	private $_categories = [];
	
	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository = null;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * Customer session
	 * 
	 * @var \Magento\Customer\Model\Session $session
	 */
	protected $session = null;
	
	/**
	 * Group registry 
	 * 
	 * @var \Magento\Customer\Model\GroupRegistry
	 */
	protected $groupRegistry = null;
	
	/**
	 * Order collection factory 
	 * 
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $orderCollectionFactory = null;

	/**
	 * Order config
	 *
	 * @var \Magento\Sales\Model\Order\Config
	 */
	protected $orderConfig = null;
	
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry = null;
	
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext = null;

	/**
	 * @var \Magento\Catalog\Helper\Data
	 */
	protected $catalogData = null;
	
	/**
	 * @var Magento\Customer\Model\Customer
	 */
	protected $customer = null;
	
	/**
	 * @var \Magento\Catalog\Model\Product\Attribute\Repository
	 */
	protected $productAttributeRepository = null;
	
	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
	 */
	protected $optionCollection;
	
	/**
	 * @var \Magento\Eav\Model\Config
	 */
	protected $eavConfig;
	
	/**
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $eventManager = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Datalayer
	 */
	protected $dataLayer = null;
	
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager = null;
	
	/**
	 * @var \Magento\Framework\App\ProductMetadataInterface
	 */
	protected $productMetadata;
	
	/**
	 * @var \Magento\Framework\Module\ModuleListInterface
	 */
	protected $moduleList;
	
	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
	protected $customerRepositoryInterface;
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
	 */
	protected $attribute;
	
	/**
	 * @var \Anowave\Ec\Helper\Attributes
	 */
	protected $attributes;
	
	/**
	 * @var \Anowave\Ec\Helper\Bridge
	 */
	protected $bridge;
	
	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\PrivateData
	 */
	protected $privateData;
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
	 */
	protected $categoryCollectionFactory;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie\Directive;
	 */
	protected $directive;
	
	/**
	 * @var \Anowave\Ec\Helper\Json
	 */
	protected $jsonHelper;
	
	/**
	 * @var \Magento\CatalogInventory\Api\StockRegistryInterface
	 */
	protected $stockItemInterface;
	
	/**
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $salesOrderCollection;
	
	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $urlInt;
	
	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart;
	
	/**
	 * @var \Magento\Catalog\Model\Layer\Resolver
	 */
	protected $layerResolver;
	
	/**
	 * @var \Anowave\Ec\Model\Facebook\ConversionsApi
	 */
	protected $facebook_conversions_api;
	
	/**
	 * @var \Magento\Framework\App\ScopeResolverInterface
	 */
	protected $scopeResolver;
	
	/**
	 * @var \Anowave\Ec\Model\Facebook\ConversionsApiFactory
	 */
	protected $facebookConversionsApiFactory;
	
	/**
	 * @var \Anowave\Ec\Model\Logger
	 */
	protected $logger;
	
	/**
	 * @var \Magento\Framework\Data\Form\FormKey;
	 */
	protected $formKey;
	
	
	/**
	 * Check if returning customer
	 * 
	 * @var boolean
	 */
	private $returnCustomer = false;
	
	/**
	 * Current store categories 
	 * 
	 * @var array
	 */
	private $currentCategories = [];
	
	/**
	 * Disable module by current IP 
	 * 
	 * @var string
	 */
	private $disabled_by_ip = null;
	
	/**
	 * Disable logged customer simulation by current IP
	 * 
	 * @var string
	 */
	private $disable_by_ip_auth = null;

	/**
	 * Constructor
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
	 * @param \Magento\Customer\Model\SessionFactory $sessionFactory
	 * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	 * @param \Magento\Sales\Model\Order\Config $orderConfig
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Magento\Catalog\Helper\Data $catalogData
	 * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
	 * @param \Magento\Eav\Model\Config $eavConfig
	 * @param \Anowave\Ec\Helper\Datalayer $dataLayer
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
	 * @param \Magento\Framework\Module\ModuleListInterface $moduleList
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
	 * @param \Anowave\Ec\Helper\Attributes $attributes
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
	 * @param \Anowave\Ec\Helper\Bridge $bridge
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	 * @param \Anowave\Ec\Model\Cookie\PrivateData $privateData
	 * @param \Anowave\Ec\Model\Cookie\Directive $directive
	 * @param \Anowave\Ec\Helper\Json $jsonHelper
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
	 * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemInterface
	 * @param \Anowave\Ec\Model\Cache $cache
	 * @param \Magento\Framework\UrlInterface $urlInt
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
	 * @param \Anowave\Ec\Model\Facebook\ConversionsApiFactory $facebookConversionsApiFactory
	 * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context, 
		\Magento\Framework\Registry $registry,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
	    \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
		\Magento\Customer\Model\SessionFactory $sessionFactory,
		\Magento\Customer\Model\GroupRegistry $groupRegistry,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Catalog\Helper\Data $catalogData,
		\Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection,
		\Magento\Eav\Model\Config $eavConfig,
		\Anowave\Ec\Helper\Datalayer $dataLayer,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata,
		\Magento\Framework\Module\ModuleListInterface $moduleList,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Anowave\Ec\Helper\Attributes $attributes,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
		\Anowave\Ec\Helper\Bridge $bridge,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Anowave\Ec\Model\Cookie\PrivateData $privateData,
		\Anowave\Ec\Model\Cookie\Directive $directive,
		\Anowave\Ec\Helper\Json $jsonHelper,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
	    \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemInterface,
	    \Anowave\Ec\Model\Cache $cache,
	    \Magento\Framework\UrlInterface $urlInt,
	    \Magento\Checkout\Model\Cart $cart,
	    \Magento\Catalog\Model\Layer\Resolver $layerResolver,
	    \Anowave\Ec\Model\Facebook\ConversionsApiFactory $facebookConversionsApiFactory,
	    \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
	    \Anowave\Ec\Model\Logger $logger,
	    \Magento\Framework\Data\Form\FormKey $formKey,
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set registry 
		 * 
		 * @var \Magento\Framework\Registry
		 */
		$this->registry = $registry;
		
		/**
		 * Set product repository
		 * 
		 * @var \Magento\Catalog\Api\ProductRepositoryInterface
		 */
		$this->productRepository = $productRepository;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Magento\Catalog\Model\CategoryRepository $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
		
		/**
		 * Set category collection factory 
		 * 
		 * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
		 */
		$this->categoryCollectionFactory = $categoryCollectionFactory;
		
		/**
		 * Set Group Registry 
		 * 
		 * @var \Magento\Customer\Model\GroupRegistry
		 */
		$this->groupRegistry = $groupRegistry;
		
		/**
		 * Set session
		 * 
		 * @var \Magento\Customer\Model\Session $session
		 */
		$this->session = $sessionFactory->create();
		
		/**
		 * Set order collection factory 
		 * 
		 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
		 */
		$this->orderCollectionFactory = $orderCollectionFactory;
		
		/**
		 * Set order config 
		 * 
		 * @var \Magento\Sales\Model\Order\Config
		 */
		$this->orderConfig = $orderConfig;
		
		/**
		 * Set context 
		 * 
		 * @var \Magento\Framework\App\Http\Context
		 */
		$this->httpContext = $httpContext;
		
		/**
		 * Set catalog data 
		 * 
		 * @var \Magento\Catalog\Helper\Data
		 */
		$this->catalogData = $catalogData;
		
		/**
		 * Set attribute repository 
		 * 
		 * @var \Magento\Catalog\Model\Product\Attribute\Repository
		 */
		$this->productAttributeRepository = $productAttributeRepository;
		
		/**
		 * Set option collection
		 * 
		 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
		 */
		$this->optionCollection = $optionCollection;
		
		/**
		 * Default collection filter(s) and sorting
		 */
		$this->optionCollection->setPositionOrder('asc')->setStoreFilter(0);
		
		/**
		 * Set scope config 
		 * 
		 * @var \Magento\Framework\App\Config\ScopeConfigInterface
		 */
		$this->scopeConfig = $context->getScopeConfig();
		
		/**
		 * Set event manager 
		 * 
		 * @var \Magento\Framework\Event\ManagerInterface
		 */
		$this->eventManager = $context->getEventManager();
		
		/**
		 * Set dataLayer 
		 * 
		 * @var \Anowave\Ec\Helper\Datalayer
		 */
		$this->dataLayer = $dataLayer;
		
		/**
		 * Set eav config 
		 * 
		 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
		 */
		$this->eavConfig = $eavConfig;
		
		/**
		 * Set Store Manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set meta data 
		 * 
		 * @var \Magento\Framework\App\ProductMetadataInterface $productMetadata
		 */
		$this->productMetadata = $productMetadata;
		
		/**
		 * Set module list 
		 * 
		 * @var \Magento\Framework\Module\ModuleListInterface $moduleList
		 */
		$this->moduleList = $moduleList;
		
		/**
		 * Set customer repository interface
		 * 
		 * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
		 */
		$this->customerRepositoryInterface = $customerRepositoryInterface;
		
		/**
		 * Set attributes
		 * 
		 * @var \Anowave\Ec\Helper\Attributes $attributes
		 */
		$this->attributes = $attributes;
		
		/**
		 * Set attribute 
		 * 
		 * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
		 */
		$this->attribute = $attribute;
		
		/**
		 * Set bridge 
		 * 
		 * @var \Anowave\Ec\Helper\Data $bridge
		 */
		$this->bridge = $bridge;
		
		/**
		 * Set RedirectInterface
		 * 
		 * @var \Magento\Framework\App\Response\RedirectInterface $redirect
		 */
		$this->redirect = $redirect;
		
		/**
		 * Set private data
		 * 
		 * @var \Anowave\Ec\Model\Cookie\PrivateData $privateData
		 */
		$this->privateData = $privateData;
		
		/**
		 * Set cookie directive 
		 * 
		 * @var \Anowave\Ec\Helper\Data $directive
		 */
		$this->directive = $directive;
		
		/**
		 * Set meta data 
		 * 
		 * @var \Magento\Framework\App\ProductMetadataInterface $productMetadata
		 */
		$this->productMetadata = $productMetadata;
		
		/**
		 * Set JSON helper 
		 * 
		 * @var \Anowave\Ec\Helper\Json $jsonHeler
		 */
		$this->jsonHelper = $jsonHelper;
		
		/**
		 * Set stock item repository 
		 * 
		 * @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemInterface
		 */
		$this->stockItemInterface = $stockItemInterface;
		
		/**
		 * Set order sales collection
		 * 
		 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
		 */
		$this->salesOrderCollection = $salesOrderCollection;
		
		/**
		 * Set URL Interface 
		 * 
		 * @var \Magento\Framework\UrlInterface $urlInt
		 */
		$this->urlInt = $urlInt;
		
		/**
		 * Set cart 
		 * 
		 * @var \Magento\Checkout\Model\Cart $cart
		 */
		$this->cart = $cart;
		
		/**
		 * @var \Magento\Catalog\Model\Layer\Resolver $layerResolver
		 */
		$this->layerResolver = $layerResolver;
		
		/**
		 * Set scope resolver 
		 * 
		 * @var \Magento\Framework\App\ScopeResolverInterface $scopeResolver
		 */
		$this->scopeResolver = $scopeResolver;
		
		/**
		 * Set Facebook Conversions API 
		 * 
		 * @var \Anowave\Ec\Model\Facebook\ConversionsApi
		 */
		
		$this->facebookConversionsApiFactory = $facebookConversionsApiFactory;
		
		/**
		 * Set logger 
		 * 
		 * @var \Anowave\Ec\Helper\Data $logger
		 */
		$this->logger = $logger;
		
		/**
		 * Sey form key
		 * 
		 * @var \Magento\Framework\Data\Form\FormKey $formKey
		 */
		$this->formKey = $formKey;
	}
	
	/**
	 * Get order revenue
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return number|number|NULL
	 */
	public function getRevenue(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		switch ((int) $this->getConfig('ec/tax/revenue'))
		{
			case \Anowave\Ec\Model\System\Config\Source\Tax::INCL_TAX: return $order->getGrandTotal();
			case \Anowave\Ec\Model\System\Config\Source\Tax::EXCL_TAX: return $order->getSubtotal();
		}
		
		/**
		 * By default return incl. tax
		 */
		return $order->getGrandTotal();
	}
	
	/**
	 * Get item price 
	 * 
	 * @param \Magento\Sales\Model\Order\Item $item
	 * @return number|NULL
	 */
	public function getRevenueProduct(\Magento\Sales\Model\Order\Item $item)
	{
		switch ((int) $this->getConfig('ec/tax/revenue_product'))
		{
			case \Anowave\Ec\Model\System\Config\Source\TaxItem::INCL_TAX: return $item->getPriceInclTax();
			case \Anowave\Ec\Model\System\Config\Source\TaxItem::EXCL_TAX: return $item->getPrice();
		}
		
		return $item->getPrice();
	}
	
	/**
	 * Get AdWords ecomm_totalvalue
	 *
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return number|number|NULL
	 */
	public function getRevenueAdWords(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		switch ((int) $this->getConfig('ec/tax/ecomm_totalvalue'))
		{
			case \Anowave\Ec\Model\System\Config\Source\Tax::INCL_TAX: return $order->getGrandTotal();
			case \Anowave\Ec\Model\System\Config\Source\Tax::EXCL_TAX: return $order->getSubtotal();
		}
		
		/**
		 * By default return incl. tax
		 */
		return $order->getGrandTotal();
	}

	/**
	 * Get checkout push 
	 * 
	 * @param unknown $block
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCheckoutPush($block, \Magento\Framework\Registry $registry)
	{
		/**
		 * Get data 
		 * 
		 * @var StdClass $data
		 */
		$data = $this->getCheckoutProducts($block, $registry);
		
		/**
		 * Update total value
		 */
		$data->google_tag_params->ecomm_totalvalue = (float) $this->cart->getQuote()->getGrandTotal();
		
		$checkout = 
		[
		    'push' =>
		    [
		        'event' => 'checkout',
		        'ecommerce' =>
		        [
		            'currencyCode' 	=> $this->getStore()->getCurrentCurrencyCode(),
		            'checkout' =>
		            [
		                'actionField' =>
		                [
		                    'step' => \Anowave\Ec\Helper\Constants::CHECKOUT_STEP_SHIPPING
		                ],
		                'products' => $data->products
		            ]
		        ]
		    ],
		    'google_tag_params' => $data->google_tag_params,
		    'total'	=> (float) $this->cart->getQuote()->getGrandTotal()
		];
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'checkout' => $checkout
		    ]
	    );
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_get_checkout', ['transport' => $transport]);
		
		
		/**
		 * Return
		 */
		return $this->getJsonHelper()->encode
		(
			$transport->getCheckout(),JSON_PRETTY_PRINT
		);
	}
	
	/**
	 * Get multicheckout push 
	 * 
	 * @param string $step
	 * @param \Anowave\Ec\Block\Track $block
	 */
	public function getMultiCheckoutPush(\Anowave\Ec\Block\Track $block, $step)
	{
	    /**
	     * Get data
	     *
	     * @var StdClass $data
	     */
	    $data = $this->getCheckoutProducts($block, $this->registry);
	    
	    /**
	     * Update total value
	     */
	    $data->google_tag_params->ecomm_totalvalue = (float) $this->cart->getQuote()->getGrandTotal();
	    
	    /**
	     * 
	     * @var integer $currentStep
	     */
	    $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_LOGIN;
	    
	    switch ($step)
	    {
	        case 'login':
	        case 'register':
	            $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_LOGIN;
	            break;
	        case 'addresses':
	            $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_ADDRESSES;
	            break;
	        case 'shipping': 
	            $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_SHIPPING;
	              break;
	        case 'billing': 
	            $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_BILLING;
	            break;
	        case 'overview': 
	            $current = \Anowave\Ec\Helper\Constants::MULTI_CHECKOUT_STEP_OVERVIEW;
	            break;
	    }

	    /**
	     * Return
	     */
	    return $this->getJsonHelper()->encode
	    (
	        [
	            'push' =>
	            [
	                'event' => 'checkout',
	                'ecommerce' =>
	                [
	                    'currencyCode' 	=> $this->getStore()->getCurrentCurrencyCode(),
	                    'checkout' =>
	                    [
	                        'actionField' =>
	                        [
	                            'type' => 'multicheckout',
	                            'step' => $current
	                        ],
	                        'products' => $data->products
	                    ]
	                ]
	            ],
	            'google_tag_params' => $data->google_tag_params,
	            'total'	=> (float) $this->cart->getQuote()->getGrandTotal()
	            
	        ],
	        JSON_PRETTY_PRINT
        );
	}
	
	/**
	 * Get cart push 
	 * 
	 * @param unknown $block
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCartPush($block, \Magento\Framework\Registry $registry)
	{
		/**
		 * Get data
		 *
		 * @var StdClass $data
		 */
		$data = $this->getCheckoutProducts($block, $registry);
		
		/**
		 * Update total value
		 */
		$data->google_tag_params->ecomm_totalvalue = (float) $this->cart->getQuote()->getGrandTotal();
		
		/**
		 * Return
		 */
		return $this->getJsonHelper()->encode($data);
	}
	
	/**
	 * Get checkout products 
	 * 
	 * @param unknown $block
	 * @param \Magento\Framework\Registry $registry
	 */
	public function getCheckoutProducts($block, \Magento\Framework\Registry $registry)
	{
		/**
		 * Products 
		 * 
		 * @var array $products
		 */
		$products = [];
		
		/**
		 * AdWords Dynamic Remarketing parameters 
		 * 
		 * @var StdClass $google_tag_params
		 */
		$google_tag_params = (object)
		[
			'ecomm_prodid' 			=> [],
			'ecomm_pvalue' 			=> [],
			'ecomm_pname' 			=> [],
			'ecomm_totalvalue' 		=> 0
		];
		
		foreach ($this->cart->getQuote()->getAllVisibleItems() as $item)
		{
			/**
			 * Get all product categories
			 */
			$categories = $this->getCurrentStoreProductCategories($item->getProduct());
			

			if (!$categories)
			{
				/**
				 * Load product by id
				 */
				$categories = $this->getCurrentStoreProductCategories
				(
					$this->productRepository->getById
					(
						$item->getProduct()->getId()
					)
				);
			}
			
			/**
			 * Cases when product does not exist in any category
			 */
			if (!$categories)
			{
				$categories[] = $this->getStoreRootDefaultCategoryId();
			}
			
			try 
			{
    			/**
    			 * Load last category 
    			 */
    			$category = $this->categoryRepository->get
    			(
    				end($categories)
    			);
			
			}
			catch (\Exception $e)
			{
			    $category = $this->categoryRepository->get
			    (
			        $this->getStoreRootDefaultCategoryId()
		        );
			}
			
			/**
			 * Variant
			 * 
			 * @var array $variant
			 */
			$variant = [];
			
			$data = new \Magento\Framework\DataObject(array
			(
				'id' 		=> 		 	$this->getIdentifierItem($item),
				'name' 		=> 		 	$item->getName(),
				'price' 	=> (float)  $item->getPriceInclTax(),
				'quantity' 	=> (int) 	$item->getQty(),
				'category'	=> 		 	$this->getCategory($category),
			    'list'      =>          $this->getCategoryList($category),
				'brand'		=> 		 	$this->getBrand
				(
					$item->getProduct()
				),
			    $this->getStockDimensionIndex(true) => $this->getStock($item->getProduct())
			));

			/**
			 * AdWords Dynamic Remarketing 
			 * 
			 * @var \Magento\Framework\DataObject $ecomm
			 */
			$ecomm = new \Magento\Framework\DataObject(array
			(
				'id' 		=> 		 	$this->getAdwordsEcommProdId($item),
				'name' 		=> 		 	$data->getName(),
				'price' 	=> (float)  $data->getPrice(),
			));
			
			if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
			{
				$variant = [];

				/**
				 * Get buy request 
				 * 
				 * @var []
				 */
				$buyRequest = $item->getProductOptionByCode('info_buyRequest');
				
				/**
				 * Check if buy request is set
				 */
				if ($buyRequest)
				{
					/**
					 * Get info buy request
					 *
					 * @var \Magento\Framework\DataObject
					 */
					$info = new \Magento\Framework\DataObject($buyRequest);
				}
				else 
				{
					/**
					 * Try to obtain buy request as custom option
					 * 
					 * @var []
					 */
					$buyRequest = $item->getProduct()->getCustomOption('info_buyRequest');
					
					if (isset($buyRequest['value']))
					{
						if (false === $value = @unserialize($buyRequest['value']))
						{
							$value = @json_decode($buyRequest['value'], true);
						}
						
						if ($value)
						{
							$info = new \Magento\Framework\DataObject($value);
						}
					}
					else
					{
						$info = new \Magento\Framework\DataObject([]);
					}
				}

				if (isset($info) && $info->getSuperAttribute())
				{
					/**
					 * Construct variant
					 */
					foreach ((array) $info->getSuperAttribute() as $id => $option)
					{
						$attribute = $this->attribute->create()->load($id);
						
						if ($attribute->usesSource())
						{
							$name = $this->getAttributeLabel($attribute);
							$text = $attribute->getSource()->getOptionText($option);
							
							if ($this->useDefaultValues())
							{
								/**
								 * Get current store
								 * 
								 * @var int
								 */
								$currentStore = $attribute->getSource()->getAttribute()->getStoreId();
								
								/**
								 * Change default store
								 */
								$attribute->getSource()->getAttribute()->setStoreId(0);
								
								/**
								 * Get text
								 * 
								 * @var string
								 */
								$text = $attribute->getSource()->getOptionText($option);
								
								/**
								 * Restore store
								 */
								$attribute->getSource()->getAttribute()->setStoreId($currentStore);
							}
							
							$variant[] = join(self::VARIANT_DELIMITER_ATT, array($name, $text));
	
						}
					}
				}
			
				if (!$this->useSimples())
				{
					$data->setId
					(
						$this->getIdentifier($item->getProduct())
					);
						
					$data->setName
					(
						$item->getProduct()->getName()
					);
					
					$ecomm->setId
					(
						$this->getAdwordsEcommProdId
						(
							$item->getProduct()
						)
					);
					
					$ecomm->setName
					(
						$item->getProduct()->getName()
					);
				}
				
				/**
				 * Load configurable
				 */
				$configurable = $this->productRepository->getById
				(
					$item->getProductId()
				);
			
				if (!$this->useSimples())
				{
					$data->setId
					(
						$this->getIdentifier($configurable)
					);
					
					$data->setName
					(
						$configurable->getName()
					);
					
					$ecomm->setId
					(
						$this->getAdwordsEcommProdId($configurable)
					);
					
					$ecomm->setName
					(
						$configurable->getName()
					);
				}
				else 
				{
					if ($option = $item->getOptionByCode('simple_product')) 
					{
						$data->setId
						(
							$this->getAdwordsEcommProdId($option->getProduct())
						);
						
						$data->setName
						(
							$option->getProduct()->getName()
						);
						
						$ecomm->setId
						(
							$this->getAdwordsEcommProdId($option->getProduct())
						);
						
						$ecomm->setName
						(
							$option->getProduct()->getName()
						);
					}
				}
				
				$data->setBrand
				(
					$this->getBrand($configurable)
				);
					
				/**
				 * Push variant to data
				 *
				 * @var array
				 */
				$data->setVariant(join(self::VARIANT_DELIMITER, $variant));
				
				/**
				 * Push variant id into dataLayer[] object
				 */
				if($item->getHasChildren())
				{
				    $child = $item->getOptionByCode('simple_product');
				    
				    if ($child && $child->getId())
				    {
				        $data->setVariantId
				        (
				            $this->getIdentifier($child->getProduct())
			            );
				    }
				}
			}
			
			/**
			 * Custom and additional options tracking
			 */
			try
			{
				/**
				 * Get order options
				 * 
				 * @var array $options
				 */
				$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
				
				if (isset($options['options']))
				{
					$selection = [];
					
					foreach ($options['options'] as $option)
					{
						$selection[] = join(self::VARIANT_DELIMITER_ATT, [$option['label'], $option['value']]);
					}
					
					if ($selection)
					{
						$variant = (string) $data->getVariant();
						
						$variant .= join(self::VARIANT_DELIMITER, $selection);
						
						$data->setVariant($variant);
					}
				}
				
				/**
				 * Get additional options
				 */
				$options = $item->getProduct()->getCustomOptions();

				if ($options && isset($options['additional_options']))
				{
					if ($options['additional_options']->getValue())
					{
						$additional_options = json_decode($options['additional_options']->getValue());
						
						$selection = [];
						
						foreach ($additional_options as $option)
						{
						    $selection[] = join(self::VARIANT_DELIMITER_ATT, [$option->label, join(chr(32), array_filter((array) $option->value))]);
						}
						
						if ($selection)
						{
							$variant = (string) $data->getVariant();
							
							$variant .= join(self::VARIANT_DELIMITER, $selection);
							
							$data->setVariant($variant);
						}
					}
				}
			}
			catch (\Exception $e){}

			/**
			 * Add product
			 */
			$products[] = $data->getData();
			
			/**
			 * AdWords Dynamic Remarketing
			 */
			$google_tag_params->ecomm_prodid[] = $ecomm->getId();
			$google_tag_params->ecomm_pvalue[] = $ecomm->getPrice();
			$google_tag_params->ecomm_pname[]  = $ecomm->getName();
		}
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
			[
				'attributes' => $this->attributes->getAttributes()
			]
		);
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_get_checkout_attributes', ['transport' => $transport]);
		
		/**
		 * Get response
		 */
		$attributes = $transport->getAttributes();
		
		foreach ($products as &$product)
		{
			foreach ($attributes as $key => $value)
			{
				$product[$key] = $value;
			}
		}
		
		unset($product);
		
		/**
		 * Notify other plugins for checkout products
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'products' => $products
		    ]
	    );
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_get_checkout_products', ['transport' => $transport]);
		
		$products = $transport->getProducts();
		
		return (object)
		[
			'products' 			=> $products,
			'google_tag_params' => $google_tag_params
		];
	}
	
	/**
	 * Impressions push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 */
	public function getImpressionPushForward($block)
	{
		if (!$this->registry->registry('current_category') || 'category' !== $this->request->getControllerName())
		{
			return false;	
		}
		
		if ($this->usePostRenderImpressionPayloadModel())
		{
		    return false;
		}
		
		try 
		{
			$list = $block->getLayout()->getBlock('category.products.list');
			
			if ($list)
			{
				$category = $this->registry->registry('current_category');
				
				$response = 
				[
					'ecommerce' => 
					[
						'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
						'actionField' => 
						[
							'list' => $this->getCategoryList($category)
						],
						'impressions' => []
					]
				];
				
				/**
				 * Get loaded collection 
				 * 
				 * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
				 */
				$collection = $this->getLoadedCollection($list);

				/**
				 * Set default position
				 * 
				 * @var integer $position
				 */
				$position = 1;
				
				/**
				 * Consider pagination
				 * 
				 * @var int $p
				 */
				$p = (int) $collection->getCurPage();
				
				if ($p > 1)
				{
					$position += (($p-1) * (int) $collection->getPageSize());
				}
				
				/**
				 * Push data 
				 * 
				 * @var []
				 */
				$data = [];
				
				$taxonomy = (object) 
				[
					'list' => $this->getCategoryList($category),
					'name' => $this->getCategory($category)
				];
				
				/**
				 * Get grid mode e.g. list or grid. Magento 2.2 or lower does not support getMode() method.
				 */
				if (method_exists($list, 'getMode'))
				{
				    $mode = $list->getMode();
				}
				else 
				{
				    $mode = null;
				}
				
				/**
				 * Get current sorting parameter 
				 * 
				 * @var string $sort
				 */
				$sort = $list->getData('sort_by');
				
				foreach ($collection as $product)
				{
					$entity = 
					[
						'list' 								=> $taxonomy->list,
						'category'							=> $taxonomy->name,
						'id'								=> $this->getIdentifier($product),
						'name'								=> $product->getName(),
						'brand'								=> $this->getBrand
						(
							$product
						),
						'price'								=> $this->getPrice($product),
						$this->getStockDimensionIndex(true) => $this->getStock($product),
						'position'							=> $position++
					];
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->attributes->getAttributes(), 
							'entity'	 => $entity,
						    'product'    => $product
						]
					);
					
					/**
					 * Notify others
					 */
					$this->eventManager->dispatch('ec_get_impression_item_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					/**
					 * Add entity to impression array
					 */
					$response['ecommerce']['impressions'][] = array_merge($entity, $attributes);	
				}
				
				$response['currentStore'] = $this->getStoreName();
				
				/**
				 * Current category state 
				 * 
				 * @var array $categoryState
				 */
				$categoryState = [];
				
				if ($sort)
				{
					$categoryState['sort'] = $sort;
				}
				
				if ($mode)
				{
					$categoryState['mode'] = $mode;
				}

				if ($categoryState)
				{
					$response['currentCategory'] = $categoryState;
				}
			}

			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'response' => $response
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_impression_data_after', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$response = $transport->getResponse();
			
			/**
			 * Facebook data
			 *
			 * @var []
			 */
			
			$content_name =  $taxonomy->name;
			
			$fbq = 
			[
				'content_name'		=> $content_name,
				'content_category' 	=> $content_name,
				'content_type' 		=> 'product',
				'content_ids' 		=> array_map
				(
				    function($entity) {return $entity['id']; }, $response['ecommerce']['impressions'] ?: []
				)
 			];

			return (object) 
			[
				'push' 				=> $this->getJsonHelper()->encode($response),
				'google_tag_params' => array
				(
					'ecomm_pagetype' => 'category',
					'ecomm_category' => $this->escape($taxonomy->name)
				),
				'fbq' => $this->getJsonHelper()->encode($fbq)
			];
		}
		catch (\Exception $e){}
		
		return false;
	}

	/**
	 * Get loaded product collection from product list block 
	 *  
	 * @param \Magento\Catalog\Block\Product\ListProduct $list
	 */
	protected function getLoadedCollection(\Magento\Catalog\Block\Product\ListProduct $list)
	{
		$collection = $list->getLoadedProductCollection();
		
		/**
		 * Get toolbar
		 */
		$toolbar = $list->getToolbarBlock();
		
		if ($toolbar)
		{
			$orders = $list->getAvailableOrders();
			
			if ($orders) 
			{
				$toolbar->setAvailableOrders($orders);
			}
			
			$sort = $list->getSortBy();
			
			if ($sort) 
			{
				$toolbar->setDefaultOrder($sort);
			}
			
			$dir = $list->getDefaultDirection();
			
			if ($dir) 
			{
				$toolbar->setDefaultDirection($dir);
			}
			
			$modes = $list->getModes();
			
			if ($modes)
			{
				$toolbar->setModes($modes);
			}
			
			$collection->setCurPage($toolbar->getCurrentPage());

			$limit = (int) $toolbar->getLimit();
			
			if ($limit) 
			{
				$collection->setPageSize($limit);
			}
			
			if ($toolbar->getCurrentOrder()) 
			{
				$collection->setOrder($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());
			}
		}
		
		return $collection;
	}
	
	/**
	 * Get detail push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass|boolean
	 */
	public function getDetailPushForward($block)
	{
		$info = $block->getLayout()->getBlock('product.info');
		
		if ($info)
		{
		    /**
		     * @var Magento\Catalog\Model\Category $category
		     */
			$category = $this->registry->registry('current_category');
			
			/**
			 * Pick category from layer resolver
			 */
			if (!$category)
			{
			    $category = $this->layerResolver->get()->getCurrentCategory();
			}

			/**
			 * Check if category isn't root category
			 */
			if ($category)
			{
			    if ((int) $category->getId() === (int) $this->getStoreRootDefaultCategoryId())
			    {
			        $category = null;
			    }
			}
			
			/**
			 * Pick category from list of categories product is assigned to
			 */
			if (!$category)
			{
			    $categories = $this->getCurrentStoreProductCategories($info->getProduct());
				
				if (!$categories && $info->getProduct()->getCategoryIds())
				{
				    $categories = $info->getProduct()->getCategoryIds();
				}

				/**
				 * Cases when product does not exist in any category
				 */
				if (!$categories)
				{
					$categories[] = $this->getStoreRootDefaultCategoryId();
				}
				
				try 
				{
    				/**
    				 * Load last category
    				*/
    				$category = $this->categoryRepository->get
    				(
    					end($categories)
    				);
				}
				catch (\Exception $e) 
				{
				    $categories = $info->getProduct()->getCategoryIds();
				    
				    if (!$categories)
				    {
				        $categories = [$this->getStoreRootDefaultCategoryId()];
				    }
				    
				    $category = $this->categoryRepository->get
				    (
				        end($categories)
			        );
				}
			}
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'attributes' => $this->attributes->getAttributes(),
				    'product'    => $info->getProduct()
				]
			);

			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_detail_attributes', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$attributes = $transport->getAttributes();
			
			$data = 
			[
				'ecommerce' => 
				[
					'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
					'detail' => 
					[
					    'actionField' => 
					    [
					        'list' => $this->getCategoryList($category)
					    ],
						'products' => 
						[
							array_merge
							(
								[
									'id' 								=> $this->getIdentifier($info->getProduct()),
									'name' 								=> $info->getProduct()->getName(),
									'price' 							=> $this->getPrice($info->getProduct()),
									'brand'								=> $this->getBrand
									(
										$info->getProduct()
									),
									'category'							=> $this->getCategory($category),
									$this->getStockDimensionIndex(true)	=> $this->getStock($info->getProduct()),
									'quantity' 							=> 1
								], 
								$attributes
							)
						]
					]
				]
			];
			
			/**
			 * There is discrepancy between Google's specification with regards to whether 'list' parameter should be used in 'detail' JSON (
			 * 
			 * @see https://developers.google.com/tag-manager/enhanced-ecommerce#details
			 * @see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-detail-view 
			 * 
			 * @todo Pending feedback from Google
			 */
			if (1 === (int) $this->getConfig('ec/options/use_detail_list'))
			{
				$data['ecommerce']['detail']['actionField']['list'] = $this->getCategoryList($category);
			}
			
			
			$data['currentStore'] = $this->getStoreName();
			
			/**
			 * Persist data in dataLayer
			 */
			$this->dataLayer->merge($data);
			
			/**
			 * Prepare Related & Upsells impressions
			 */
			$data['ecommerce']['impressions'] = [];
			
			/**
			 * Related
			 */
			try 
			{
				$list = $block->getLayout()->getBlock('catalog.product.related');
				
				if ($list)
				{
					/**
					 * Set default position
					 *
					 * @var integer $position
					 */
					$position = 1;

					/**
					 * Push data
					 *
					 * @var []
					 */
					
					foreach ($this->bridge->getLoadedItems($list) as $product)
					{
						/**
						 * Create transport object
						 *
						 * @var \Magento\Framework\DataObject $transport
						 */
						$transport = new \Magento\Framework\DataObject
						(
							[
								'attributes' => $this->attributes->getAttributes(),
							    'product'    => $product
							]
						);
						
						/**
						 * Notify others
						 */
						$this->eventManager->dispatch('ec_get_impression_related_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$entity = array_merge
						(
							[
								'list' 			=> \Anowave\Ec\Helper\Constants::LIST_RELATED,
								'category'		=> \Anowave\Ec\Helper\Constants::LIST_RELATED,
								'id'			=> $this->getIdentifier($product),
								'name'			=> $product->getName(),
								'brand'			=> $this->getBrand
								(
									$product
								),
								'price'			=> $this->getPrice($product),
								'position'		=> $position++
							], 
							$attributes
						);
						
						$data['ecommerce']['impressions'][] = $entity;
					}
				}
			}
			catch (\Exception $e){}
			
			/**
			 * Upsells
			 */
			try 
			{
				$list = $block->getLayout()->getBlock('product.info.upsell');
				
				if ($list)
				{
					/**
					 * Set default position
					 *
					 * @var integer $position
					 */
					$position = 1;
					
					/**
					 * Push data
					 *
					 * @var []
					 */

					foreach ($this->bridge->getLoadedItems($list) as $product)
					{
						/**
						 * Create transport object
						 *
						 * @var \Magento\Framework\DataObject $transport
						 */
						$transport = new \Magento\Framework\DataObject
						(
							[
								'attributes' => $this->attributes->getAttributes(),
							    'product'    => $product
							]
						);
						
						/**
						 * Notify others
						 */
						$this->eventManager->dispatch('ec_get_impression_upsell_attributes', ['transport' => $transport]);
						
						/**
						 * Get response
						 */
						$attributes = $transport->getAttributes();
						
						$entity = array_merge
						(
							[
								'list' 			=> \Anowave\Ec\Helper\Constants::LIST_UP_SELL,
								'category'		=> \Anowave\Ec\Helper\Constants::LIST_UP_SELL,
								'id'			=> $this->getIdentifier($product),
								'name'			=> $product->getName(),
								'brand'			=> $this->getBrand
								(
									$product
								),
								'price'			=> $this->getPrice($product),
								'position'		=> $position++
							], 
							$attributes
						);
						
						$data['ecommerce']['impressions'][] = $entity;
					}
				}
				
				
			}
			catch (\Exception $e){}
			
			/**
			 * Create transport object
			 *
			 * @var \Magento\Framework\DataObject $transport
			 */
			$transport = new \Magento\Framework\DataObject
			(
				[
					'response' => $data,
				    'product'  => $info->getProduct()
				]
			);
			
			/**
			 * Notify others
			 */
			$this->eventManager->dispatch('ec_get_detail_data_after', ['transport' => $transport]);
			
			/**
			 * Get response
			 */
			$data = $transport->getResponse();
			
			/**
			 * Get product
			 */
			$product = $info->getProduct();
			
			/**
			 * Child items (configurable)
			 * 
			 * @var array $children
			 */
			$children = [];
			
			$google_tag_params = 
			[
			    'ecomm_pagetype' 	=> 		   'product',
			    'ecomm_category'	=> 		   $this->escape($this->getCategory($category)),
			    'ecomm_prodid'		=> 		   $this->escape
			    (
			        $this->getAdwordsEcommProdId($product)
		        ),
			    'ecomm_totalvalue'	=> (float) $this->getPrice($product)
			];
			
			if(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE === $product->getTypeId())
			{
			    if ($this->useSimples() && $this->useVariantSku())
			    {
			        if (null !== $child = $this->getFirstChild($product))
			        {
			            $google_tag_params['ecomm_prodid'] = $this->escape
			            (
			                $this->getAdwordsEcommProdId($child)
		                );
			        } 
			    }
			}

			/**
			 * Return
			 */
			return (object) 
			[
				'push' 				=> $this->getJsonHelper()->encode($data),
			    'fbq'				=> $this->getJsonHelper()->encode($this->getFacebookViewContentTrack($product, $category)),
				'google_tag_params' => $google_tag_params,
				'group'             => $this->getDetailGroup($info, $category)
			];
		}
		
		return false;
	}

	/**
	 * Get grouped products
	 * 
	 * @param unknown $block
	 * @param unknown $category
	 * @return string
	 */
	public function getDetailGroup(\Magento\Catalog\Block\Product\View $block, \Magento\Catalog\Model\Category $category)
	{
		$group = [];
		
		if (\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE == $block->getProduct()->getTypeId())
		{
			foreach ($block->getProduct()->getTypeInstance(true)->getAssociatedProducts($block->getProduct()) as $product)
			{
				$group[] = 
				[
					'id' 		=> $product->getId(),
					'sku'		=> $product->getSku(),
					'name' 		=> $product->getName(),
					'price' 	=> $this->getPrice($product),
					'brand'		=> $this->getBrand($product),
					'category'	=> $this->getCategory($category)
				];
			}
		}
		
		return $this->getJsonHelper()->encode($group);
	}
	
	/**
	 * Get purchase payload collection
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return string
	 */
	public function getPurchasePayloadCollection($block) : string
	{
	    $payload = [];
	    
	    foreach ($this->getOrders($block) as $order)
	    {
	        $payload[] = $this->getPurchasePayload($order);
	    }
	    
	    return $this->getJsonHelper()->encode($payload);
	}
	
	/**
	 * Get purchase payload 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * @return string
	 */
	public function getPurchasePayload($order)
	{
		$products = [];
		$response = [];
		
		if ($order->getIsVirtual())
		{
		    $address = $order->getBillingAddress();
		}
		else
		{
		    $address = $order->getShippingAddress();
		}
		
		$response =
		[
		    'ecommerce' =>
		    [
		        'currencyCode' => $this->getStore()->getCurrentCurrencyCode(),
		        'purchase' 	   =>
		        [
		            'actionField' =>
		            [
		                'id' 			      => $order->getIncrementId(),
		                'revenue' 		      => (float) $this->getRevenue($order),
		                'tax'			      =>            (float) $order->getTaxAmount(),
		                'shipping' 		      =>            (float) $order->getShippingAmount(),
		                'coupon'		      => strtoupper((string) $order->getCouponCode()),
		                'affiliation' 	      =>            (string) $this->getStore()->getName(),
		                'shipping_country'    => $address->getCountryId()
		            ],
		            'products' => []
		        ]
		    ],
		    'facebook' =>
		    [
		        'revenue' 	=> (float) $order->getGrandTotal(),
		        'subtotal' 	=> (float) $order->getSubtotal()
		    ],
		    'payment' =>
		    [
		        'method' => $order->getPayment()->getMethodInstance()->getTitle()
		    ],
		    'shipping' =>
		    [
		        'method' => $order->getShippingDescription()
		    ],
		    'event' => 'purchase'
		];
		
		foreach ($order->getAllVisibleItems() as $item)
		{
		    $variant = [];
		    
		    $category = $this->registry->registry('current_category');
		    
		    if (!$category)
		    {
		        /**
		         * Get all product categories
		         */
		        $categories = $this->getCurrentStoreProductCategories($item->getProduct());
		        
		        /**
		         * Cases when product does not exist in any category
		         */
		        if (!$categories)
		        {
		            $categories[] = $this->getStoreRootDefaultCategoryId();
		        }
		        
		        
		        try
		        {
		            /**
		             * Load last category
		             */
		            $category = $this->categoryRepository->get
		            (
		                end($categories)
	                );
		            
		        }
		        catch (\Exception $e)
		        {
		            $category = $this->categoryRepository->get
		            (
		                $this->getStoreRootDefaultCategoryId()
	                );
		        }
		    }
		    
		    $data = new \Magento\Framework\DataObject
		    (
		        [
		            'id' 		=> 		 	$this->getIdentifier($item->getProduct()),
		            'name' 		=> 		 	$item->getName(),
		            'price' 	=> (float) 	$this->getRevenueProduct($item),
		            'quantity' 	=> (int) 	$item->getQtyOrdered(),
		            'category'	=> 		 	$this->getCategory($category),
		            'brand'		=> 		 	$this->getBrand
		            (
		                $item->getProduct()
	                ),
		            $this->getStockDimensionIndex(true) => $this->getStock($item->getProduct())
		        ]
		    );
		    
		    if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
		    {
		        $variant = [];
		        
		        /**
		         * Get buy request
		         *
		         * @var []
		         */
		        $buyRequest = $item->getProductOptionByCode('info_buyRequest');
		        
		        /**
		         * Check if buy request is set
		         */
		        if ($buyRequest)
		        {
		            /**
		             * Get info buy request
		             *
		             * @var \Magento\Framework\DataObject
		             */
		            $info = new \Magento\Framework\DataObject($buyRequest);
		        }
		        else
		        {
		            /**
		             * Try to obtain buy request as custom option
		             *
		             * @var []
		             */
		            $buyRequest = $item->getProduct()->getCustomOption('info_buyRequest');
		            
		            if (isset($buyRequest['value']))
		            {
		                $value = unserialize($buyRequest['value']);
		                
		                $info = new \Magento\Framework\DataObject($value);
		            }
		            else
		            {
		                $info = new \Magento\Framework\DataObject([]);
		            }
		        }
		        
		        /**
		         * Construct variant
		         */
		        foreach ($info->getSuperAttribute() as $id => $option)
		        {
		            /**
		             * Load attribute
		             *
		             * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
		             */
		            $attribute = $this->attribute->create()->load($id);
		            
		            if ($attribute->usesSource())
		            {
		                $name = $this->getAttributeLabel($attribute);
		                $text = $attribute->getSource()->getOptionText($option);
		                
		                if ($this->useDefaultValues())
		                {
		                    /**
		                     * Get current store
		                     *
		                     * @var int
		                     */
		                    $currentStore = $attribute->getSource()->getAttribute()->getStoreId();
		                    
		                    /**
		                     * Change default store
		                     */
		                    $attribute->getSource()->getAttribute()->setStoreId(0);
		                    
		                    /**
		                     * Get text
		                     *
		                     * @var string
		                     */
		                    $text = $attribute->getSource()->getOptionText($option);
		                    
		                    /**
		                     * Restore store
		                     */
		                    $attribute->getSource()->getAttribute()->setStoreId($currentStore);
		                }
		                
		                $variant[] = join(self::VARIANT_DELIMITER_ATT, array($name, $text));
		            }
		        }
		        
		        if (!$this->useSimples())
		        {
		            $data->setId
		            (
		                $this->getIdentifier($item->getProduct())
	                );
		            
		            $data->setName
		            (
		                $item->getProduct()->getName()
	                );
		            
		            if($item->getHasChildren())
		            {
		                if ($item->getChildrenItems())
		                {
    		                foreach($item->getChildrenItems() as $child)
    		                {
    		                    $data->setVariantId
    		                    (
    		                        $this->getIdentifier($child)
    		                    );
    		                }
		                }
		                else 
		                {
		                    $child = $item->getOptionByCode('simple_product');
		                    
		                    if ($child && $child->getId())
		                    {
		                        $data->setVariantId
		                        (
		                            $this->getIdentifier($child->getProduct())
	                            );
		                    }
		                }
		            }
		        }
		        else
		        {
		            if($item->getHasChildren())
		            {
		                if ($item->getChildrenItems())
		                {
    		                foreach($item->getChildrenItems() as $child)
    		                {
    		                    $data->setName
    		                    (
    		                        $child->getName()
    	                        );
    		                    
    		                    $data->setVariantId
    		                    (
    		                        $this->getIdentifier($child)
    		                    );
    		                }
		                }
		                else 
		                {
		                    $child = $item->getOptionByCode('simple_product');
		                    
		                    if ($child && $child->getId())
		                    {
		                        $data->setName
		                        (
		                            $child->getProduct()->getName()
	                            );
		                        
		                        $data->setVariantId
		                        (
		                            $this->getIdentifier($child->getProduct())
	                            );
		                    }
		                }
		            }
		        }
		        
		        /**
		         * Push variant to data
		         *
		         * @var array
		         */
		        $data->setVariant(join(self::VARIANT_DELIMITER, $variant));
		    }
		    
		    if (\Magento\Bundle\Model\Product\Type::TYPE_CODE == $item->getProduct()->getTypeId())
		    {
		        $bundles = [];
		        
		        /**
		         * Get bundle 
		         * 
		         * @var \Magento\Catalog\Model\Product $bundle
		         */
		        $bundle = $item->getProduct();
		        
		        
		        /**
		         * Get buy request 
		         * 
		         * @var array
		         */
		        $buyRequest = $item->getProductOptionByCode('info_buyRequest');
		          
		        if ($buyRequest && isset($buyRequest['bundle_option']))
		        {
		            $options = [];
		            
		            foreach ($buyRequest['bundle_option'] as $option => $selections)
		            {
		                $collection = $bundle->getTypeInstance(true)->getSelectionsCollection($option,$bundle);
		                
		                foreach ($collection as $id => $entity)
		                {
		                    if (in_array($id, (array) $selections))
		                    {
		                        $bundles[] = $entity->getName();
		                    }
		                }
		            }  
		        }

		        if ($bundles)
		        {
		            $response['ecommerce']['purchase']['bundles'] = $bundles;
		        }
		    }
		    
		    /**
		     * Track custom options
		     */
		    
		    try
		    {
		        /**
		         * Custom options tracking
		         *
		         * @var array $options
		         */
		        $options = $item->getProductOptions();
		        
		        if ($options && is_array($options))
		        {
		            if (isset($options['options']))
		            {
		                $selection = [];
		                
		                foreach ($options['options'] as $option)
		                {
		                    $selection[] = join(self::VARIANT_DELIMITER_ATT, [$option['label'], $option['value']]);
		                }
		                
		                if ($selection)
		                {
		                    $variant = (string) $data->getVariant();
		                    
		                    $variant .= join(self::VARIANT_DELIMITER, $selection);
		                    
		                    $data->setVariant($variant);
		                }
		            }
		        }
		        
		        /**
		         * Additional options tracking (added via additional_options[])
		         */
		        if (isset($options['additional_options']))
		        {
		            $additional_options = $options['additional_options'];
		            
		            $selection = [];
		            
		            foreach ($additional_options as $option)
		            {
		                $selection[] = join(self::VARIANT_DELIMITER_ATT, [$option['label'], $option['value']]);
		            }
		            
		            if ($selection)
		            {
		                $variant = (string) $data->getVariant();
		                
		                $variant .= join(self::VARIANT_DELIMITER, $selection);
		                
		                $data->setVariant($variant);
		            }
		        }
		    }
		    catch (\Exception $e){}
		    
		    /**
		     * Track product specific coupon
		     */
		    
		    if (null !== $item->getAppliedRuleIds())
		    {
		        /**
		         * Get applied rules
		         *
		         * @var array $rules
		         */
		        $rules = explode(chr(44), (string) $item->getAppliedRuleIds());
		        
		        /**
		         * Add coupon parameter if any applied rules.
		         *
		         * By default Magento 2.x does not support multiple coupon codes applied at once so order coupon code should match product coupon code
		         *
		         * @todo Implement multiple coupon codes supported by 3rd parties
		         */
		        if ($rules)
		        {
		            $data->setCoupon(strtoupper((string) $order->getCouponCode()));
		        }
		    }
		    
		    /**
		     * Create transport object
		     *
		     * @var \Magento\Framework\DataObject $transport
		     */
		    $transport = new \Magento\Framework\DataObject
		    (
		        [
		            'product' 	 => $data,
		            'quote_item' => $item
		        ]
	        );
		    
		    /**
		     * Notify others
		     */
		    $this->eventManager->dispatch('ec_order_products_product_get_after', ['transport' => $transport]);
		    
		    /**
		     * Get product
		     */
		    $data = $transport->getProduct();
		    
		    /**
		     * Add product
		     */
		    $products[] = $data->getData();
		}
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'products' => $products
		    ]
	    );
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_order_products_get_after', ['transport' => $transport]);
		
		/**
		 * Get products
		 */
		$products = $transport->getProducts();
		
		/**
		 * Set products
		 */
		$response['ecommerce']['purchase']['products'] = $products;
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'attributes' => $this->attributes->getAttributes(),
		        'products'   => $products
		    ]
	    );
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_get_purchase_attributes', ['transport' => $transport]);
		
		/**
		 * Get response
		 */
		$attributes = $transport->getAttributes();
		
		foreach ($response['ecommerce']['purchase']['products'] as &$product)
		{
		    foreach ($attributes as $key => $value)
		    {
		        $product[$key] = $value;
		    }
		}
		
		unset($product);
		
		$response['currentStore'] = $this->getStoreName();
		
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'response' => $response, 
		        'order'    => $order
		    ]
	    );
		
		/**
		 * Notify others
		 */
		$this->eventManager->dispatch('ec_get_purchase_push_after', ['transport' => $transport]);
		
		/**
		 * Get response
		 */
		$response = $transport->getResponse();

		return $response;
	}
	
	/**
	 * Get purchase google tag params 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass
	 */
	public function getPurchaseGoogleTagParams($block)
	{
		$google_tag_params = (object) 
		[
			'ecomm_prodid' 			=> [],
			'ecomm_pvalue' 			=> [],
			'ecomm_pname' 			=> [],
			'ecomm_totalvalue' 		=> 0
		];
		
		foreach ($this->getOrders($block) as $order)
		{
			foreach ($order->getAllVisibleItems() as $item)
			{
				$data = new \Magento\Framework\DataObject(array
				(
					'id'  			=> $this->escape($item->getSku()),
					'name' 			=> $this->escape($item->getName()),
					'price' 		=> $item->getPrice(),
					'ecomm_prodid' 	=> $this->getAdwordsEcommProdId($item)
				));
				
				/**
				 * Change values if configurable
				 */
				if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
				{
					if (!$this->useSimples())
					{
						$data->setId
						(
							$this->escape($item->getProduct()->getSku())
						);
						
						$data->setName
						(
							$this->escape($item->getProduct()->getName())
						);
						
						$data->setEcommProdid
						(
							$this->escape
							(
								$this->getAdwordsEcommProdId
								(
									$item->getProduct()
								)
							)
						);
					}
					else 
					{
						if($item->getHasChildren()) 
						{
						    if ($item->getChildrenItems())
						    {
    							foreach($item->getChildrenItems() as $child) 
    							{
    								$data->setEcommProdid
    								(
    									$this->escape
    									(
    										$this->getAdwordsEcommProdId
    										(
    											$child
    										)
    									)
    								);
    							}
						    }
						    else 
						    {
						        $child = $item->getOptionByCode('simple_product');
						        
						        if ($child && $child->getId())
						        {
						            $data->setEcommProdid
						            (
						                $this->escape
						                (
						                    $this->getAdwordsEcommProdId
						                    (
						                        $child
					                        )
					                    )
					                );
						        }
						    }
						}
					}
				}
				
				$google_tag_params->ecomm_prodid[] 		= 		  $data->getEcommProdid();
				$google_tag_params->ecomm_pvalue[] 		= (float) $data->getPrice();
				$google_tag_params->ecomm_pname[] 		= 		  $data->getName();
			}
			
			/**
			 * Set total value
			 */
			$google_tag_params->ecomm_totalvalue += (float) $this->getRevenueAdWords($order);
		}
		
		return $google_tag_params;
	}
	
	/**
	 * Get orders
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 */
	public function getOrders($block)
	{
		return $this->getOrdersCollection
		(
			(array) $block->getOrderIds()
		);
	}
	
	/**
	 * Get orders collection 
	 * 
	 * @param array $order_ids
	 * @return array
	 */
	public function getOrdersCollection(array $order_ids = [])
	{
		if (!$this->_orders)
		{
			if (!$order_ids)
			{
				$this->_orders = [];
			}
			else 			
			{
				$collection = $this->getSalesOrderCollection()->create();
				
				/**
				 * Filter applicable order ids
				 */
				$collection->addFieldToFilter('entity_id', ['in' => $order_ids]);
				
				foreach ($collection as $order)
				{
					if ($order->getPayment())
					{
						/**
						 * Get filter-out method 
						 * 
						 * @var [] $filter
						 */
						$filter = $this->getOrderFilterOutMethods();
						
						/**
						 * Get order payment method 
						 * 
						 * @var string $method
						 */
						$method = $order->getPayment()->getMethod();
						
						if (!in_array($method, $filter))
						{
							$this->_orders[] = $order;
						}
					}
					else 
					{
						$this->_orders[] = $order;
					}	
				}
			}
		}
		
		return $this->_orders;
	}
	
	/**
	 * Get Search push 
	 * 
	 * @param \Magento\Framework\View\Element\Template $block
	 * @return StdClass|boolean
	 */
	public function getSearchPush($block)
	{
	    if ($this->usePostRenderImpressionPayloadModel())
	    {
	        return false;
	    }
	    
		try 
		{
			if ('catalogsearch' === $this->request->getModuleName())
			{
				$list = $block->getLayout()->getBlock('search_result_list');
				
				if ($list)
				{
					$response = array
					(
						'ecommerce' 	=> array
						(
							'currencyCode' 	=> $this->getStore()->getCurrentCurrencyCode(),
							'actionField' => 
							[
								'list' => __('Search Results')
							],
							'impressions' => []
						)
					);
					
					$data = [];

					$collection = $this->getLoadedCollection($list);
					
					$position = 1;
					
					/**
					 * Adjust position depending on pagination.
					 */
					try
					{
					    $position = (int) $list->getToolbarBlock()->getLimit() * (int) $collection->getCurPage();
					}
					catch (\Exception $e)
					{
					    $position = 1;
					}
					
					foreach ($collection  as $product)
					{
						/**
						 * Get all product categories
						 */
						$categories = $this->getCurrentStoreProductCategories($product);
						
						/**
						 * Cases when product does not exist in any category
						 */
						if (!$categories)
						{
							$categories[] = $this->getStoreRootDefaultCategoryId();
						}
						
						/**
						 * Load last category
						 */
						$category = $this->categoryRepository->get
						(
							end($categories)
						);
						
						$response['ecommerce']['impressions'][] = array
						(
							'list' 			=> __('Search Results')->__toString(),
							'category'		=> $this->getCategory($category),
							'id'			=> $this->getIdentifier($product),
							'name'			=> $product->getName(),
							'brand'			=> $this->getBrand
							(
								$product
							),
							'price'			=> $this->getPrice($product),
							'position'		=> $position++
						);
					}
					
					$response['currentStore'] = $this->getStoreName();
				}
	
				/**
				 * Create transport object
				 *
				 * @var \Magento\Framework\DataObject $transport
				 */
				$transport = new \Magento\Framework\DataObject
				(
					[
						'attributes' => $this->attributes->getAttributes()
					]
				);
				
				/**
				 * Notify others
				 */
				$this->eventManager->dispatch('ec_get_search_attributes', ['transport' => $transport]);
				
				/**
				 * Get response
				 */
				$attributes = $transport->getAttributes();
				
				foreach ($response['ecommerce']['impressions'] as &$product)
				{
					foreach ($attributes as $key => $value)
					{
						$product[$key] = $value;
					}
				}
				
				unset($product);
	
				return (object) 
				[
					'push' 				=> $this->getJsonHelper()->encode($response),
					'google_tag_params' => array
					(
						'ecomm_pagetype' 	=> 'category',
						'ecomm_category'	=> __('Search Results')
					)
				];
			}
		}
		catch (\Exception $e){}
		
		return false;
	}

	/**
	 * Get visitor push
	 * 
	 * @param \Magento\Framework\View\Element\AbstractBlock $block
	 */
	public function getVisitorPush($block = null)
	{
		/**
		 * Get customer group ($this->orderConfig->getVisibleOnFrontStatuses())
		 */
		$data = array
		(
			'visitorLoginState' 		=> $this->isLogged() ? __('Logged in') : __('Logged out'),
			'visitorLifetimeValue' 		=> 0,
			'visitorExistingCustomer' 	=> __('No')
		);
		
		if ($this->isLogged())
		{
			$data['visitorId'] = (int) $this->getCustomer()->getId();
			
			/**
			 * Get statuses 
			 * 
			 * @var array $statuses
			 */
			$statuses = 
			[
			    \Magento\Sales\Model\Order::STATE_COMPLETE,
			    \Magento\Sales\Model\Order::STATE_CLOSED,
			    \Magento\Sales\Model\Order::STATE_PROCESSING
			];
			
			
			/**
			 * Get customer order(s)
			 * 
			 * @var array
			 */
			$orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('customer_id', $this->getCustomer()->getId())->addFieldToFilter('status',['in' => $statuses])->setOrder('created_at','desc');
			
			$total = 0;
			
			foreach ($orders as $order)
			{
				$total += $order->getGrandTotal();
			}
	
			$data['visitorLifetimeValue'] = $total;
			
			if ($total > 0)
			{
				$data['visitorExistingCustomer'] = __('Yes');
				
				/**
				 * Returning customer 
				 * 
				 * @var \Anowave\Ec\Helper\Data $returnCustomer
				 */
				$this->returnCustomer = true;
			}
			
			$group = $this->groupRegistry->retrieve
			(
				$this->getCustomer()->getGroupId()
			);

			/**
			 * Push visitor group
			 */
			$data['visitorType'] = $group->getCustomerGroupCode();
			
			/**
			 * Push number of orders
			 */
			$data['visitorLifetimeOrders'] = $orders->getSize();
		}
		else 
		{
			$group = $this->groupRegistry->retrieve(0);
			
			$data['visitorType'] = $group->getCustomerGroupCode();
		}
		
		$data['currentStore'] = $this->getStoreName();
		
		/**
		 * Create transport object
		 *
		 * @var \Magento\Framework\DataObject $transport
		 */
		$transport = new \Magento\Framework\DataObject
		(
		    [
		        'visitor' => $data
		    ]
		);
		
		/**
		 * Notify others
		 */
		$this->getEventManager()->dispatch('ec_get_visitor_data', ['transport' => $transport]);
		
		$data = $transport->getVisitor();
		
		return $this->getJsonHelper()->encode($data);
	}

	/**
	 * Get Facebook Pixel Product View content 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @param \Magento\Catalog\Model\Category $category
	 * @return []
	 */
	public function getFacebookViewContentTrack(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Category $category)
	{
		return 
		[
			'content_type' 		=> 'product',
			'content_name' 		=> $product->getName(),
			'content_category' 	=> $this->getCategory($category),
			'content_ids' 		=> $this->getFacebookRemarketingId($product),
			'currency' 			=> $this->getStore()->getCurrentCurrencyCode(),
			'value' 			=> $this->getPrice($product)
		];
	}
	
	public function getFacebookInitiateCheckoutTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	public function getFacebookAddToCartTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	public function getFacebookPurchaseTrack()
	{
		return $this->getJsonHelper()->encode([]);
	}
	
	/**
	 * Use Facebook Pixel tracking
	 */
	public function facebook()
	{
		return 1 === (int) $this->getConfig('ec/facebook/active');
	}
	
	/**
	 * Get facebook pixel tracking code 
	 * 
	 * @return string
	 */
	public function getFacebookPixelCode() : string
	{
		$this->getFacebookAdvancedMatchingParameters();
		
		if ($this->facebook())
		{
			return (string) $this->getConfig('ec/facebook/facebook_pixel_code');
		}
		else 
		{
			return '';
		}
	}
	
	/**
	 * Get facebook pixel trigger event
	 * 
	 * @return string
	 */
	public function getFacebookCookieTriggerEvent() : string 
	{
	    $event = (string) $this->getConfig('ec/facebook/facebook_consent_trigger_event');
	    
	    if ($event)
	    {
	        return $event;
	    }
	        
	    return \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT;
	}
	
	/**
	 * Get Facebook Pixel code
	 * 
	 * @return string
	 */
	public function getFacebookPixelCodePlain() : string
	{
	    /**
	     * Plain snippet code
	     *
	     * @var string $plain
	     */
	    $plain = '';
	    
	    /**
	     * Create DOMDocument instance
	     * 
	     * @var \DOMDocument $dom
	     */
	    $dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
	    
	    /**
	     * Get Facebook Pixel code 
	     * 
	     * @var string $pixel
	     */
	    $pixel = $this->getFacebookPixelCode();
	    
	    if (!$pixel)
	    {
	        return $plain;
	    }
	    
	    /**
	     * Load Facebook Pixel code
	     */
	    @$dom->loadHTML($pixel);
	   
	    foreach($dom->getElementsByTagName('script') as $script)
	    {
	        $plain = trim($script->textContent);
	    }
	    
	    return $plain;
	}

	
	/**
	 * Get Facebook Pixel Advanced Matching Parameters 
	 * 
	 * @return string
	 */
	public function getFacebookAdvancedMatchingParameters()
	{
		/**
		 * Default parameters 
		 * 
		 * @var array $params
		 */
		$params = [];
		
		if ($this->facebook() && $this->isLogged())
		{
			$params['em'] = md5($this->getCustomer()->getEmail());
			$params['fn'] = $this->getCustomer()->getFirstname();
			$params['ln'] = $this->getCustomer()->getLastname();
			
			switch ((int) $this->getCustomer()->getGender())
			{
				case 1: $params['ge'] = 'm';
					break;
				case 2: $params['ge'] = 'f';
					break;
				default: 
					$params['ge'] = null;
					break;
			}
			
			if ($this->getCustomer()->getDob())
			{
				$params['db'] = $this->getCustomer()->getDob();
			}
		}
		
		return $params;
	}
	
	/**
	 * Get customer
	 */
	public function getCustomer()
	{ 
		if (!$this->customer)
		{
			if ($this->registry->registry('cache_session_customer_id') > 0)
			{
				$this->customer = $this->customerRepositoryInterface->getById($this->registry->registry('cache_session_customer_id'));
			}
			else if($this->session->getCustomerId())
			{
			    $this->customer = $this->session->getCustomer();
			}
		}
	
		return $this->customer;
	}
	
	/**
	 * Get customer email 
	 * 
	 * @param \Magento\Sales\Model\Order $order
	 * @return string
	 */
	public function getCustomerEmail(\Magento\Sales\Model\Order $order) : string
	{
	    return $order->getCustomerEmail();
	}
	
	/**
	 * Get current visitor id
	 * 
	 * @return number
	 */
	public function getVisitorId()
	{
		if ($this->isLogged())
		{
			return (int) $this->getCustomer()->getId();
		}
		else 
		{
			return 0;
		}
	}
	
	/**
	 * Get Super Attributes
	 */
	public function getSuper()
	{
		$super = [];
		
		if ($this->registry->registry('current_product'))
		{
			$product = $this->registry->registry('current_product');
			
			if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $product->getTypeId())
			{
				$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
								 	
			 	foreach($attributes as $attribute)
			 	{
			 		$object = $attribute->getProductAttribute();
			 		
			 		$super[] = array
			 		(
			 			'id' 				=> $object->getAttributeId(),
			 			'label' 			=> $this->getAttributeLabel($object),
			 			'code'				=> $object->getAttributeCode(),
			 			'options'			=> $this->getAttributeOptions($attribute)
			 		);
			 	}
			}
		}

		return $this->getJsonHelper()->encode($super);
	}
	
	/**
	 * Get configurable simple products 
	 * 
	 * @return string
	 */
	public function getConfigurableSimples()
	{
		$simples = [];
		
		if ($this->registry->registry('current_product'))
		{
			/**
			 * Get current product 
			 * 
			 * @var Ambiguous $product
			 */
			$product = $this->registry->registry('current_product');
			
			if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $product->getTypeId())
			{
				foreach ($product->getTypeInstance()->getUsedProducts($product) as $simple)
				{
					$simples[$simple->getId()] = 
					[
						'id' 		=> $this->getIdentifier($simple),
						'name' 		=> $simple->getName(),
						'price'		=> $simple->getPrice()
					];
				}
			}
		}
		
		return $this->getJsonHelper()->encode($simples);
	}
	
	/**
	 * Get bundle items
	 */
	public function getBundle()
	{
		$bundles = [];
		$options = [];
		
		if (null !== $product = $this->registry->registry('current_product'))
		{
			if (\Magento\Bundle\Model\Product\Type::TYPE_CODE === $product->getTypeId())
			{
				foreach ($product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product),$product) as $bundle) 
				{
					$bundles[$bundle->getOptionId()][$bundle->getId()] = 
					[
						'id' 		=> $this->getIdentifier($bundle),
						'name' 		=> $bundle->getName(),
						'price'		=> $bundle->getPrice(),
						'quantity' 	=> $bundle->getSelectionQty(),
					];
				}

				foreach ($product->getTypeInstance(true)->getOptionsCollection($product) as $option) 
				{
					$options[$option->getOptionId()] = 
					[
						'option_title' => $option->getDefaultTitle(),
						'option_type'  => $option->getType()
						
					];
				}
			}
		}
		
		return $this->jsonHelper->encode(
		[
			'bundles' => $bundles,
			'options' => $options
		]);
	}
	
	/**
	 * Get attribute label 
	 * 
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
	 */
	protected function getAttributeLabel(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
	{
		return ($this->useDefaultValues() ? $attribute->getFrontendLabel() : $attribute->getStoreLabel());
	}
	
	/**
	 * Get attribute options
	 * 
	 * @param Object $attribute
	 */
	protected function getAttributeOptions($attribute)
	{
		$options = [];
		
		foreach ($attribute->getOptions() as $option)
		{
			$options[] = $option;
		}
			
		if ($this->useDefaultValues())
		{
			try 
			{
				foreach ($options as &$option)
				{
					$this->optionCollection->clear();
					$this->optionCollection->getSelect()->reset(\Zend_Db_Select::WHERE);
					$this->optionCollection->getSelect()->where('main_table.option_id IN (?)',[$option['value_index']]);
					$this->optionCollection->getSelect()->group('main_table.option_id');
					
					/**
					 * Set admin label
					 *
					 * @var string
					*/
					$option['admin_label'] = $this->optionCollection->getFirstitem()->getValue();
				}
				
				unset($option);
			}
			catch (\Exception $e)
			{
				return [];
			}
		}
			
		return $options;
	}
	
	/**
	 * Get final price of product 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getPrice(\Magento\Catalog\Model\Product $product)
	{
		/**
		 * Get final price
		 *
		 * @var float
		 */
		
		switch ($product->getTypeId())
		{
			case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
				
			    $products = $product->getTypeInstance()->getAssociatedProducts($product);
			    
			    if ($products)
			    {
			        $minimal = (float) $products[0]->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
			        
			        foreach ($products as $entity)
			        {
			            $price = (float) $entity->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
			            
			            if ($price < $minimal)
			            {
			                $minimal = $price;
			            }
			            
			        }
			        
			        $price = round($minimal,2, PHP_ROUND_HALF_UP);
			    }
			    else
			    {
			        $price = 0;
			    }

			    break;
				
			case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
				
				$price = (float) $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();

				break;
				
			default: 
				
				$price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
				
				break;
				
		}
		
		/**
		 * Allow others to modify price
		 */
		$this->eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => 1]);
		
		return $price;
	}
	
	/**
	 * Get stock status 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getStock(\Magento\Catalog\Model\Product $product)
	{
	    try 
	    {
	        $stock = $this->stockItemInterface->getStockItem($product->getId())->getIsInStock() ? __('In stock') : __('Out of stock');
	    }
	    catch (\Exception $e)
	    {
	        $stock = __('Missing stock data');
	    }
	    
		return $stock;
	}
	
	/**
	 * Get category 
	 * 
	 * @param \Magento\Catalog\Model\Category $category
	 */
	public function getCategory(\Magento\Catalog\Model\Category $category)
	{
		if (0 !== (int) $this->getConfig('ec/options/use_segments'))
		{
			return $this->getCategorySegments($category);
		}
		
		return $category->getName();
	}
	
	/**
	 * Get detail list (correlates with category)
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @param \Magento\Catalog\Model\Category $category
	 * 
	 * @return string
	 */
	public function getCategoryDetailList(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Category $category)
	{
		return $category->getName();
	}
	
	/**
	 * Get category list name
	 * 
	 * @param \Magento\Catalog\Model\Category $category
	 */
	public function getCategoryList(\Magento\Catalog\Model\Category $category)
	{
		return $category->getName();
	}
	
	/**
	 * Retrieve category and it's parents separated by chr(47)
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function getCategorySegments(\Magento\Catalog\Model\Category $category)
	{
		$segments = [];
	
		foreach ($category->getParentCategories() as $parent)
		{
			$segments[] = $parent->getName();
		}
	
		if (!$segments)
		{
			$segments[] = $category->getName();
		}
	
		return trim(join(chr(47), $segments));
	}
	
	/**
	 * Get product brand 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getBrand(\Magento\Catalog\Model\Product $product)
	{
		if (array_key_exists((int) $product->getId(), $this->_brandMap))
		{
			return $this->_brandMap[$product->getId()];
		}

		switch ($product->getTypeId())
		{
			case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
			case \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL: 
			case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
			case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
				
				$attributes = array_filter([$this->getConfig('ec/options/use_brand_attribute')]);
				
				if (!$attributes)
				{
					$attributes = ['manufacturer'];
				}
				
				foreach ($attributes as $code)
				{
					$attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code);
					
					if ($attribute->getId() && $attribute->usesSource())
					{
						$brand = $product->getAttributeText($code);
						
						if (is_array($brand))
						{
						    $brand = join($this->getBrandDelimueter(), $brand);
						}
						else
						{
						    $brand = (string) $brand;
						}

						if (!$brand)
						{
							/**
							 * Get value 
							 * 
							 * @var string $value
							 */
							$value = (int) $product->getResource()->getAttributeRawValue($product->getId(), $code, $this->getStore()->getId());
							
							if ($value > 0)
							{
								/**
								 * Get text representation
								 */
								$brand = $attribute->getSource()->getOptionText($value);
							}
						}
						
						$this->_brandMap[(int) $product->getId()] = $brand;
						
						return $brand;
					}
					else 
					{
						/**
						 * Static brands
						 */
						
						$brand = $product->getResource()->getAttributeRawValue($product->getId(), $code, $this->getStore()->getId());
						
						if ($brand)
						{
							$this->_brandMap[(int) $product->getId()] = $brand;
							
							return $brand;
						}
					}
				}
				break;
		}
		
		/**
		 * Return empty brand
		 */
		return '';
	}
	
	/**
	 * Get Facebook value key
	 */
	public function getFacebookValueKey()
	{
		$key = $this->getConfig('ec/facebook/facebook_value');
		
		if (!in_array($key, array('revenue','subtotal')))
		{
			$key = \Anowave\Ec\Model\System\Config\Source\Value::KEY_REVENUE;
		}
		
		return $key;
	}
	
	/**
	 * Get current store
	 */
	public function getStore()
	{
		return $this->storeManager->getStore();
	}
	
	/**
	 * Set store name
	 */
	public function getStoreName()
	{
		return $this->getStore()->getName();
	}
	
	public function getCurrency()
	{
		return $this->getStore()->getCurrentCurrencyCode();
	}

	/**
	 * Get body snippet
	 * 
	 * @return String
	 */
	public function getBodySnippet()
	{
		return $this->getConfig('ec/general/code_body');
	}
	
	/**
	 * Get head snippet
	 * 
	 * @return String
	 */
	public function getHeadSnippet()
	{
		return $this->getConfig('ec/general/code_head');
	}
	
	/**
	 * Get Google Optimize Page Hiding Snippet
	 * 
	 * @return mixed
	 */
	public function getGoogleOptimizePageHidingSnippet()
	{
		return $this->getConfig('ec/optimize/use_optimize_page_hiding_snippet');
	}
	
	/**
	 * Check for standalone optimize implementation
	 * 
	 * @return boolean
	 */
	public function getGoogleOptimizeIsStandalone()
	{
		if (!$this->isBetaMode())
		{
			return false;
		}
		
		/**
		 * Check Optimize Container Id
		 */
		if ('' === $this->getGoogleOptimizeContainerId())
		{
			return false;
		}
		
		/**
		 * Check Google Analytics Id
		 */
		if ('' === $this->getGoogleOptimizeAnalyticsId())
		{
			return false;
		}
		
		return \Anowave\Ec\Model\System\Config\Source\Optimize\Implementation::I_STANDALONE === (int) $this->getConfig('ec/optimize/implementation');
	}
	
	/**
	 * Check for assisted optimize implementation
	 * 
	 * @return boolean
	 */
	public function getGoogleOptimizeIsAssisted()
	{
		return \Anowave\Ec\Model\System\Config\Source\Optimize\Implementation::I_ASSISTED === (int) $this->getConfig('ec/optimize/implementation');
	}
	
	/**
	 * Get Google Optimize Container ID
	 *
	 * @return mixed
	 */
	public function getGoogleOptimizeContainerId()
	{
		return trim((string) $this->getConfig('ec/optimize/use_optimize_container_id'));
	}
	
	/**
	 * Google Google Optimize Universal Analytics ID
	 * 
	 * @return string
	 */
	public function getGoogleOptimizeAnalyticsId()
	{
		return trim((string) $this->getConfig('ec/general/account'));
	}
	
	/**
	 * Check if contact form has been submitted
	 * 
	 * @return JSON|boolean
	 */
	public function getContactEvent()
	{
		$event = $this->session->getContactEvent();
		
		if ($event)
		{
			$this->session->unsetData('contact_event');
			
			return $event;
		}
		
		return false;
	}
	
	public function getCartUpdateEvent()
	{
		$event = $this->session->getCartUpdateEvent();
		
		if ($event)
		{
			$this->session->unsetData('cart_update_event');
			
			return $event;
		}
		
		return false;
	}
	
	/**
	 * Check if contact form has been submitted
	 *
	 * @return JSON|boolean
	 */
	public function getNewsletterEvent()
	{
		$event = $this->session->getNewsletterEvent();
		
		if ($event)
		{
			$this->session->unsetData('newsletter_event');
			
			return $event;
		}
		
		return false;
	}
	
	public function getCustomerRegisterEvent()
	{
	    $event = $this->session->getCustomerRegisterEvent();
	    
	    if ($event)
	    {
	        $this->session->unsetData('customer_register_event');
	        
	        return $event;
	    }
	    
	    return false;
	}
	
	public function getCustomerLoginEvent()
	{
	    $event = $this->session->getCustomerLoginEvent();
	    
	    if ($event)
	    {
	        $this->session->unsetData('customer_login_event');
	        
	        return $event;
	    }
	    
	    return false;
	}

	/**
	 * Get Facebook Events 
	 * 
	 * @return array
	 */
	public function getFacebookEvents()
	{
		$events = [];
		
		/**
		 * Get complete registration event 
		 */
		if (false != $event = $this->getFacebookCompleteRegistrationEvent())
		{
			$events['CompleteRegistration'] = $event;
		}
		
		return array_filter($events);
	}
	
	/**
	 * Check if contact form has been submitted
	 *
	 * @return JSON|boolean
	 */
	public function getFacebookCompleteRegistrationEvent()
	{
		$event = $this->session->getFacebookCompleteRegistrationEvent();
		
		if ($event)
		{
			$this->session->unsetData('facebook_complete_registration_event');
			
			return $event;
		}
		
		return false;
	}
	
	public function getStoreRootDefaultCategoryId()
	{
		$roots = $this->getAllStoreRootCategories();
		
		if ($roots)
		{
			return (int) reset($roots);
			
		}
		return null;
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
	
	/**
	 * Get page type 
	 * 
	 * @return string
	 */
	public function getPageType() : string
	{
	    /**
	     * Set default type 
	     * 
	     * @var string $type
	     */
	    $type = 'other';
	    
	    /**
	     * Check if current page is homepage
	     */
	    if ($this->getIsHomePage())
	    {
	        $type = 'home';
	    }
	    
	    /**
	     * Check if current page is category page
	     */
	    if ($this->registry->registry('current_category') || 'category' === $this->request->getControllerName())
	    {
	        $type = 'category';
	    }

	    /**
	     * Check if current page is product page 
	     */
	    if ($this->registry->registry('current_product') || 'product' === $this->request->getControllerName())
	    {
	        $type = 'product';
	    }
	    
	    /**
	     * Check if current page is search results
	     */
	    if ('result' === $this->request->getControllerName())
	    {
	        $type = 'searchresults';
	    }
	    
	    if ('cart' === $this->request->getControllerName())
	    {
	        $type = 'cart';
	    }
	    
	    if ('checkout' === $this->request->getModuleName() && ('index' === $this->request->getControllerName() && 'index' === $this->request->getActionName()))
	    {
	        $type = 'checkout';
	    }
	    
	    if ('onepage' === $this->request->getControllerName() && 'success' === $this->request->getActionName())
	    {
	        $type = 'purchase';
	    }
	    
	    if (function_exists('wp'))
	    {
	        return 'wordpress';
	    }
	    
	    return $this->getJsonHelper()->encode($type);
	}
	
	/**
	 * Check if homepage 
	 * 
	 * @return bool
	 */
	public function getIsHomePage() : bool
	{
	    return $this->urlInt->getUrl('') === $this->urlInt->getUrl('*/*/*', ['_current'=>true, '_use_rewrite'=>true]);
	}
	
	
	/**
	 * Get an associative array of [store_id => root_category_id] values for all stores
	 * 
	 * @return array
	 */
	public function getAllStoreRootCategories()
	{
		$roots = [];
		
		foreach ($this->storeManager->getStores() as $store)
		{
			if ($store->getId() == $this->getStore()->getId())
			{
				$roots[$store->getId()] = $store->getRootCategoryId();
			}
		}
		
		return $roots;
	}

	/**
	 * Check if module is active
	 * 
	 * @return boolean
	 */
	public function isActive() : bool
	{
	    if ($this->isDisabledByIp())
	    {
	        return false;
	    }

		return 0 !== (int) $this->getConfig('ec/general/active');
	}
	
	
	/**
	 * Check if customer is logged in
	 */
	public function isLogged()
	{
	    /**
	     * Simulate non logged user for cache warmers
	     */
	    if ($this->isAuthDisabledByIp())
	    {
	        return false;
	    }
	    
	    if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH))
	    {
	        return true;
	    }
	    else if($this->session->isLoggedIn())
	    {
	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Disable by IP 
	 * 
	 * @return bool
	 */
	public function isDisabledByIp() : bool
	{
	    return $this->isIpMatch('ec/general/disable_by_ip');
	}
	
	/**
	 * Disable non-id by IP
	 *
	 * @return bool
	 */
	public function isAuthDisabledByIp() : bool
	{
	    return $this->isIpMatch('ec/general/disable_by_ip_auth');
	}
	
	/**
	 * Check if config IP list matches current IP 
	 * 
	 * @param string $config
	 * @return bool
	 */
	public function isIpMatch($config) : bool
	{
	    $ips = (string) $this->getConfig($config);
	    
	    if ($ips)
	    {
	        if (!(php_sapi_name() == 'cli'))
	        {
	            $ips = array_filter(explode(PHP_EOL, trim($ips)));
	            $ips = array_map(function($ip)
	            {
	                return trim($ip);
	                
	            }, $ips);
	            
	            if ($ips && $_SERVER)
	            {
	                if (in_array($_SERVER['REMOTE_ADDR'], $ips))
	                {
	                    return true;
	                }
	            }
	            
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * Check if AdWords Conversion Tracking is active and can be triggered (with consent check)
	 * 
	 * @param bool $consent
	 * @return boolean
	 */
	public function isAdwordsConversionTrackingActive($consent = false)
	{
		$active = 1 === (int) $this->getConfig('ec/adwords/conversion');
		
		/**
		 * Check for consent
		 */
		if ($consent && $this->supportCookieDirective())
		{
			if ($this->isCookieConsentAccepted())
			{
				return $active;
			}

			return false;
		}

		return $active;
	}
	
	/**
	 * Check if Google Customer Reviews is enabled
	 * 
	 * @return boolean
	 */
	public function isCustomerReviewsActive() : bool
	{
	    return 1 === (int) $this->getConfig('ec/customer_reviews/enable');
	}
	
	/**
	 * Add GTIN to customer reviews 
	 * 
	 * @return bool
	 */
	public function isCustomerReviewsGTIN() : bool
	{
	    return 1 === (int) $this->getConfig('ec/customer_reviews/use_gtin');
	}
	
	/**
	 * Get GTIN attribute
	 *
	 * @return string
	 */
	public function getCustomerReviewsGTINAttribute()
	{
	    $attribute = (string) $this->getConfig('ec/customer_reviews/gtin');
	    
	    return $attribute ? $attribute : null;
	}
	
	/**
	 * Check if module is in beta mode 
	 * 
	 * @return boolean
	 */
	public function isBetaMode()
	{
		return 1 === (int) $this->getConfig('ec/beta/mode');
	}
	
	/**
	 * Check if using GTAG implementation 
	 * 
	 * @return boolean
	 */
	public function useAdwordsConversionTrackingGtag()
	{
		return 1 === (int) $this->getConfig('ec/adwords/gtag');
	}
	
	public function usePrivateFallback()
	{
		return 1 === (int) $this->getConfig('ec/options/use_private_fallback');
	}
	
	/**
	 * Remove confirmation
	 * 
	 * @return string
	 */
	public function useRemoveConfirm()
	{
		return $this->getJsonHelper()->encode($this->getUseRemoveConfirm());
	}
	
	/**
	 * Get localStorage flag
	 * 
	 * @return string
	 */
	public function useLocalStorage()
	{
		return $this->getJsonHelper()->encode($this->getUseLocalStorage());
	}
	
	/**
	 * Use measurement protocol to track admin/offline orders
	 * @return bool
	 */
	public function useMeasurementProtocol() : bool
	{
	    return 1 === (int) $this->helper->getConfig('ec/gmp/use_measurement_protocol');
	}
	
	/**
	 * Use only measurement protocol for tracking transactions
	 * 
	 * @return bool
	 */
	public function useMeasurementProtocolOnly() : bool
	{
	    return 1 === (int) $this->getConfig('ec/gmp/use_measurement_protocol_only');
	}
	
	/**
	 * Use only measurement protocol for tracking cancelled orders
	 *
	 * @return bool
	 */
	public function useMeasurementProtocolCancel() : bool
	{
	    return 1 === (int) $this->getConfig('ec/gmp/use_measurement_protocol_cancel');
	}
	
	/**
	 * Use only measurement protocol for tracking refunds
	 *
	 * @return bool
	 */
	public function useMeasurementProtocolRefund() : bool
	{
	    return 1 === (int) $this->getConfig('ec/gmp/use_measurement_protocol_refund');
	}
	
	/**
	 * Local storage 
	 * 
	 * @return boolean
	 */
	public function getUseLocalStorage()
	{
		return 1 === (int) $this->getConfig('ec/options/use_local_storage');
	}
		
	/**
	 * Remove confirmation
	 *
	 * @return string
	 */
	public function getUseRemoveConfirm()
	{
		return 1 === (int) $this->getConfig('ec/options/use_remove_confirm');
	}
	
	/**
	 * Get brand delimiter 
	 * 
	 * @return string
	 */
	public function getBrandDelimueter() : string
	{
	    $delimiter = $this->getConfig('ec/options/use_brand_attribute_delimiter');
	    
	    if (is_null($delimiter))
	    {
	        $delimiter = chr(44);
	    }
	    
	    return $delimiter;
	}
	
	/**
	 * Get Gtag site tag
	 *
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagSiteTag()
	{
		return (string) $this->getConfig('ec/adwords/gtag_global_site_tag');
	}
	
	/**
	 * Gtag "send to" parameter
	 * 
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagSendToParameter()
	{
		return (string) $this->getConfig('ec/adwords/gtag_send_to');
	}
	
	
	/**
	 * Get AdWords Conversion Tracking conversion event JSON 
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return string
	 */
	public function getAdwordsConversionTrackingGtagConvesionEvent(\Magento\Sales\Api\Data\OrderInterface $order)
	{
		return $this->getJsonHelper()->encode(
		[
			'send_to' 			=> $this->getAdwordsConversionTrackingGtagSendToParameter(),
			'value' 			=> $this->getRevenue($order),
			'currency' 			=> $this->getStore()->getCurrentCurrencyCode(),
			'transaction_id' 	=> $order->getIncrementId(),
		    'new_customer'      => $this->getIsNewCustomer($order)
		], JSON_UNESCAPED_SLASHES);
	}
	
	/**
	 * Get enhanced conversions variable 
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return string
	 */
	public function getEnhancedConversionVariable(\Magento\Sales\Api\Data\OrderInterface $order)
	{
	    $variable = [];
	    
	    if ($this->supportEnhancedConversions())
	    {
	        try 
	        {
    	        $shippingAddress = $order->getShippingAddress();
    	        
    	        if ($shippingAddress)
    	        {
    	            $variable['email']         = $shippingAddress->getEmail();
    	            $variable['phone_number']  = $shippingAddress->getTelephone();
    	            $variable['first_name']    = $shippingAddress->getFirstname();
    	            $variable['last_name']     = $shippingAddress->getLastname();
    	            $variable['street']        = is_array($shippingAddress->getStreet()) ? $shippingAddress->getStreet()[0] : $shippingAddress->getStreet();
    	            $variable['city']          = $shippingAddress->getCity();
    	            $variable['region']        = $shippingAddress->getRegionCode();
    	            $variable['country']       = $shippingAddress->getCountryId();
    	            $variable['postal_code']   = $shippingAddress->getPostcode();
    	        }
	        }
	        catch (\Exception $e)
	        {
	            $variable['error'] = __('Shipping address cannot be found');
	        }
	    }

	    return $this->getJsonHelper()->encode($variable);
	}

	/**
	 * Get initial binding parameters 
	 * 
	 * @return string
	 */
	public function getInitialBinding()
	{
		return $this->jsonHelper->encode
		(
			[
				'performance' => $this->supportPerformance()	
			]
		);
	}
	
	/**
	 * Use default admin labels for product variants
	 * 
	 * @return boolean	
	 */
	public function useDefaultValues()
	{
		return 1 === (int) $this->getConfig('ec/options/use_skip_translate');
	}
	
	/**
	 * Use simple SKU(s) instead of configurable parent SKU. Applicable for configurable products only.
	 * 
	 * @return boolean
	 */
	public function useSimples() : bool
	{
		return 1 === (int) $this->getConfig('ec/options/use_simples');
	}
	
	/**
	 * Use variant sku instead of configurable in product detail
	 * 
	 * @return bool
	 */
	public function useVariantSku() : bool
	{
	    return 1 === (int) $this->getConfig('ec/options/use_variant_sku');
	}
	
	/**
	 * Use placeholders
	 *
	 * @return boolean
	 */
	public function usePlaceholders()
	{
		return 1 === (int) $this->getConfig('ec/selectors/beta_placeholders');
	}
	
	/**
	 * Enable debug mode
	 * 
	 * @return bool
	 */
	public function useDebugMode() : bool
	{
	    return 1 === (int) $this->getConfig('ec/selectors/debug');
	}
	
	/**
	 * Pre-render impression payload model 
	 * 
	 * @return boolean
	 */
	public function usePreRenderImpressionPayloadModel()
	{
	    return \Anowave\Ec\Model\System\Config\Source\PayloadModel\Impression::MODEL_PRE_RENDER === (int) $this->getImpressionPayloadModel();
	}
	
	/**
	 * Post-render impression payload model
	 *
	 * @return boolean
	 */
	public function usePostRenderImpressionPayloadModel()
	{
	    return \Anowave\Ec\Model\System\Config\Source\PayloadModel\Impression::MODEL_POST_RENDER === (int) $this->getImpressionPayloadModel();
	}
	
	/**
	 * Use summary callback 
	 * 
	 * @return bool
	 */
	public function useSummary() : bool 
	{
	    return 1 === (int) $this->getConfig('ec/options/use_summary');
	}
	
	/**
	 * Reset dataLayer[] object before each new push
	 * 
	 * @return bool
	 */
	public function useReset() : bool
	{
	    return 1 === (int) $this->getConfig('ec/options/use_reset');
	}
	
	/**
	 * Performance API support
	 *
	 * @return boolean
	 */
	public function supportPerformance()
	{
		return 1 === (int) $this->getConfig('ec/performance/enable');
	}
	
	/**
	 * Check for AMP support
	 * 
	 * @return boolean
	 */
	public function supportAmp()
	{
		return 1 === (int) $this->getConfig('ec/amp/enable');
	}
	
	/**
	 * Support extended AdWords Dynamic Remarketing (for other sites)
	 * 
	 * @return boolean
	 */
	public function supportDynx()
	{
		return 1 === (int) $this->getConfig('ec/adwords/dynx');
	}
	
	/**
	 * Support AdWords Enhanced Conversions 
	 * 
	 * @return bool
	 */
	public function supportEnhancedConversions() : bool
	{
	    return 1 === (int) $this->getConfig('ec/adwords/allow_enhanced_conversions');
	}
	
	/**
	 * Support Facebook Conversions API 
	 * 
	 * @return bool
	 */
	public function supportFacebookConversionsApi() : bool
	{
	    return 1 === (int) $this->getConfig('ec/facebook/facebook_conversions_api');
	}
	
	/**
	 * Add support for internal search
	 * 
	 * @return boolean
	 */
	public function supportInternalSearch()
	{
		return 1 === (int) $this->getConfig('ec/search/enable');
	}
	
	/**
	 * Check if cookie directive support is enabled 
	 * 
	 * @return boolean
	 */
	public function supportCookieDirective()
	{
		return 1 === (int) $this->getConfig('ec/cookie/enable');
	}
	
	/**
	 * Preselect GDPR choices 
	 * 
	 * @return bool
	 */
	public function getSegmentCheckall() : bool
	{
	    return 1 === (int) $this->getConfig('ec/cookie/mode_segment_checkall');
	}
	
	/**
	 * Get built-in Google Consent
	 * 
	 * @return string
	 */
	public function getGoogleBuiltinConsent() : string
	{
	    return json_encode(
	    [
	        'ad_storage'               => 'denied',
	        'analytics_storage'        => 'denied',
	        'functionality_storage'    => 'denied',
	        'personalization_storage'  => 'denied',
	        'security_storage'         => 'denied'
	    ]);
	}
	/**
	 * Check if cookie consent is accepted 
	 * 
	 * @return boolean
	 */
	public function isCookieConsentAccepted()
	{
		if (1 === (int) $this->directive->get())
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get Facebook Conversions API Pixel Id
	 *
	 * @return bool
	 */
	public function getFacebookConversionsApiPixelId() : string
	{
	    return (string) $this->getConfig('ec/facebook/facebook_conversions_api_pixel_id');
	}
	
	/**
	 * Get Facebook Conversions API Pixel Id
	 *
	 * @return bool
	 */
	public function getFacebookConversionsApiAccessToken() : string
	{
	    return (string) $this->getConfig('ec/facebook/facebook_conversions_api_access_token');
	}
	
	/**
	 * Get Facebook Conversions API test event code
	 * @return string
	 */
	public function getFacebookConversionsApiTestEventCode() : string
	{
	    return (string) $this->getConfig('ec/facebook/facebook_conversions_api_test_event_code');
	}
	
	/**
	 * Get cookie directive content 
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveContent()
	{
		return $this->getConfig('ec/cookie/content');
	}
	
	/**
	 * Get cookie consent mode
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveConsentMode()
	{
		return $this->getConfig('ec/cookie/mode');
	}
	
	/**
	 * Check if consent mode is segment 
	 * 
	 * @return boolean
	 */
	public function getCookieDirectiveIsSegmentMode()
	{
		return \Anowave\Ec\Model\System\Config\Source\Consent\Mode::SEGMENT === (int) $this->getCookieDirectiveConsentMode();	
	}
	
	/**
	 * Get cookie directive conent segments
	 * 
	 * @return string[]
	 */
	public function getCookieDirectiveConsentSegments()
	{
		if ($this->getCookieDirectiveIsSegmentMode())
		{
			return 
			[
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT,
				\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT,
			];
		}
		
		return 
		[
			\Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT
		];
	}
	
	/**
	 * Get cookie directive background color
	 * 
	 * @return mixed
	 */
	public function getCookieDirectiveBackgroundColor()
	{
		return $this->getConfig('ec/cookie/content_background_color');
	}
	
	/**
	 * Get border radius 
	 * 
	 * @return string
	 */
	public function getCookieDirectiveRadius() : string
	{
	    return sprintf('%dpx', (int) $this->getConfig('ec/cookie/content_border_radius'));
	}
	
	
	/**
	 * Get filter-out payment methods
	 * 
	 * @return []
	 */
	public function getOrderFilterOutMethods()
	{
		$methods = (string) $this->getConfig('ec/options/use_disable_payment_method_tracking');
		
		return array_filter(explode(chr(44), $methods));
	}
	
	/**
	 * Get cookie directive text color
	 *
	 * @return mixed
	 */
	public function getCookieDirectiveTextColor()
	{
		return $this->getConfig('ec/cookie/content_text_color');
	}
	
	/**
	 * Get cookie directive accept link color
	 *
	 * @return mixed
	 */
	public function getCookieDirectiveTextAcceptColor()
	{
		return $this->getConfig('ec/cookie/content_accept_color');
	}
	
	/**
	 * Get cookie directive accept link color
	 *
	 * @return mixed
	 */
	public function getCookieDirectiveCheckboxColor()
	{
	    return $this->getConfig('ec/cookie/content_checkbox_color');
	}
	
	/**
	 * Get payload model 
	 * 
	 * @return integer
	 */
	public function getImpressionPayloadModel()
	{
	    return (int) $this->getConfig('ec/options/impression_payload_model');
	}
	
	/**
	 * Get internal search dimensions
	 * 
	 * @return number
	 */
	public function getInternalSearchDimension()
	{
		$dimension = (int) $this->getConfig('ec/search/dimension');
		
		if (!$dimension)
		{
			$dimension = \Anowave\Ec\Helper\Constants::INTERNAL_SEARCH_DEFAULT_DIMENSION;
		}
		
		return $dimension;
	}
	
	/**
	 * Get default stock dimension index
	 * 
	 * @return number
	 */
	public function getStockDimensionIndex($key = false)
	{
		$dimension = (int) $this->getConfig('ec/dimensions/stock');
		
		if (!$dimension)
		{
			$dimension = \Anowave\Ec\Helper\Constants::INTERNAL_STOCK_DEFAULT_DIMENSION;
		}
		
		/**
		 * Return dimension as dimension[index] pair
		 */
		if ($key)
		{
			return "dimension{$dimension}";
		}
		
		return $dimension;
	}

	/**
	 * Add internal search dimension 
	 * 
	 * @param array $attributes
	 */
	public function addInternalSearchDimension(array &$attributes = [])
	{
		if ($this->supportInternalSearch())
		{
			if (null !== $query = $this->request->getParam('q'))
			{
				/**
				 * Basic sanitization
				 */
				$query = preg_replace('/[^a-zA-Z0-9]/i','', strip_tags($query));
				
				/**
				 * Add query to parameters
				 */
				$attributes["dimension{$this->getInternalSearchDimension()}"] = $query;
			}
		}
		
		return $attributes;
	}
	
	
	/**
	 * Check if current Magento is Enterprise (EE) edition
	 * 
	 * @return boolean
	 */
	public function isEnterprise()
	{
		return $this->productMetadata->getEdition() === 'Enterprise';
	}
	
	/**
	 * Check if current Magento is Community (CE) edition
	 *
	 * @return boolean
	 */
	public function isCommunity()
	{
		return $this->productMetadata->getEdition() === 'Community';
	}
	
	/**
	 * Get product categories 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 */
	public function getCurrentStoreProductCategories(\Magento\Catalog\Model\Product $product)
	{
	    return $this->getCurrentStoreCategories((array) $product->getCategoryIds());
	}
	
	/**
	 * Get current store categories for product 
	 * 
	 * @param array $intersect
	 * @return array
	 */
	public function getCurrentStoreCategories(array $intersect = [])
	{
	    if (!$this->currentCategories)
	    {
	        $collection = $this->categoryCollectionFactory->create()->addAttributeToSelect('entity_id')->setStore($this->getStoreManager()->getStore());
	        
	        $this->currentCategories = [];
  
	        foreach ($collection as $entity)
	        {
	            $this->currentCategories[] = (int) $entity->getEntityId();
	        }
	    }
	    
	    if ($this->currentCategories)
	    {
	        $intersect = array_filter($intersect, function($category)
	        {
	            try 
	            {
	                if (!in_array($category, $this->_categories))
	                {
	                    $this->_categories[] = (int) $this->categoryRepository->get($category)->getId();
	                }
	                
	                return true;
	            }
	            catch (\Exception $e)
	            {
	                return false;
	            }
	        });

	        return array_filter($this->currentCategories, function($category) use ($intersect)
	        {
	            return in_array($category, $intersect);
	        });
	    }

	    return $this->currentCategories;
	}
	
	/**
	 * Check if customer is returning customer
	 * 
	 * @return boolean
	 */
	public function getIsReturnCustomer()
	{
		return $this->getJsonHelper()->encode($this->returnCustomer);
	}
	
	/**
	 * Get if new customer 
	 * 
	 * @param \Magento\Sales\Api\Data\OrderInterface $order
	 * @return bool
	 */
	public function getIsNewCustomer(\Magento\Sales\Api\Data\OrderInterface $order) : bool
	{
	    return false;
	}
	
	/**
	 * Get Facebook remarketing identifier 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @return mixed
	 */
	public function getFacebookRemarketingId(\Magento\Catalog\Model\Product $product)
	{
	    return $this->getAttribute($product, 'ec/facebook/facebook_content_id');
	}
	
	/**
	 * Get Adwords remarketing identifier
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @return mixed
	 */
	public function getAdwordsRemarketingId(\Magento\Catalog\Model\Product $product)
	{
	    return $this->getAttribute($product, 'ec/adwords/ecomm_prodid');
	}
	
	/**
	 * Get attribute 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @param string $attribute
	 * @return mixed
	 */
	public function getAttribute(\Magento\Catalog\Model\Product $product, $config)
	{
	    $attribute = strtolower($this->getConfig($config));
	    
	    if ('id' === $attribute)
	    {
	        return $product->getId();
	    }
	    
	    return $product->getData($attribute);
	}
	
	/**
	 * Get ecomm_prodid
	 * 
	 * @param \Magento\Framework\Api\ExtensibleDataInterface $item
	 * @return string
	 */
	public function getAdwordsEcommProdId(\Magento\Framework\Api\ExtensibleDataInterface $item)
	{
		/**
		 * Get attribute 
		 * 
		 * @var string $attribute
		 */
		$attribute = strtolower
		(
			$this->getConfig('ec/adwords/ecomm_prodid')
		);
		
		switch ($attribute)
		{
			case \Anowave\Ec\Model\System\Config\Source\Id::ID_ID: 	
			{
				/**
				 * Checkout/cart items
				 */
				if ($item instanceof \Magento\Quote\Model\Quote\Item)
				{
					return (int) $item->getProductId();	
				}

				/**
				 * Purchase items
				 */
				if ($item instanceof \Magento\Sales\Model\Order\Item)
				{
					return (int) $item->getProductId();
				}
				
				return (int) $item->getId();
			}
			case \Anowave\Ec\Model\System\Config\Source\Id::ID_SKU: 
			{
				return (string) $item->getSku();
			}
			default:
				
				$value = null;
				
				/**
				 * Checkout/cart items
				 */
				if ($item instanceof \Magento\Quote\Model\Quote\Item)
				{
					$value = $item->getProduct()->getData($attribute);
				}
				
				/**
				 * Purchase items
				 */
				if ($item instanceof \Magento\Sales\Model\Order\Item)
				{
					$value = $item->getProduct()->getData($attribute);
				}
				
				if ($value)
				{
					return $value;
				}

				/**
				 * Get item attribute value
				 */
				$value = $item->getData($attribute);
				
				if ($value)
				{
					return $value;
				}
				
				return (string) $item->getSku();
		}
		
		return (string) $item->getSku();
	}

	/**
	 * Get first product child (for configurable products) 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @return Magento\Catalog\Model\Product|NULL
	 */
	public function getFirstChild($product)
	{
	    if(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE === $product->getTypeId())
	    {
    	    /**
    	     * Get variants (children)
    	     */
    	    $children = $product->getTypeInstance()->getUsedProducts($product);
    	    
    	    /**
    	     * Sort children to find the lowest price possible
    	     */
    	    usort($children, function($a, $b)
    	    {
    	        return $a->getFinalPrice() <=> $b->getFinalPrice();
    	    });
    	    
    	    if ($children)
    	    {
    	        $child = array_shift($children);
    	        
    	        return $child;
    	    } 
	    }
	    
	    return null;
	}
	
	/**
	 * Get module version
	 * 
	 * @return float
	 */
	public function getVersion()
	{
		return $this->moduleList->getOne('Anowave_Ec')['setup_version'];
	}
	
	/**
	 * Get identifier 
	 * 
	 * @param \Magento\Catalog\Model\Product $product
	 * @return mixed
	 */
	public function getIdentifier(\Magento\Framework\Model\AbstractExtensibleModel $product)
	{
	    $identifier = (string) $this->getConfig('ec/options/identifier');
	    
	    switch($identifier)
	    {
	        case \Anowave\Ec\Model\System\Config\Source\Id::ID_ID:  return $product->getId();
	        case \Anowave\Ec\Model\System\Config\Source\Id::ID_SKU: return $product->getSku();
	        default:
	            try 
	            {
	                return $product->getData($identifier);
	            }
	            catch (\Exception $e) {}
	            break;
	    }
	    
	    return $product->getSku();
	}
	
	/**
	 * Get quote/sales order item identifier
	 * 
	 * @param \Magento\Framework\Api\ExtensibleDataInterface $item
	 * @return mixed
	 */
	public function getIdentifierItem(\Magento\Framework\Api\ExtensibleDataInterface $item)
	{
	    $product = $item->getProduct();
	    
	    if (!$product)
	    {
	        if ($item->getProductId())
	        {
	            try
	            {
	                $product = $this->productRepository->getById($item->getProductId());
	            }
	            catch (\Exception $e)
	            {
	                return $item->getSku();
	            }
	        }
	    }
	    
	    
	    return $this->getIdentifier($product);
	    
	}
	
	/**
	 * Category items selector 
	 * 
	 * @return XPath (string)
	 */
	public function getListSelector()
	{  
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_SELECTOR;
	}
	
	/**
	 * Get cross sell list items selector
	 * 
	 * @return string
	 */
	public function getListCrossSellSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_crosssell'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_CROSS_SELECTOR;
	}
	
	/**
	 * Add to wishlist selector
	 *
	 * @return XPath (string)
	 */
	public function getWishlistSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/add_wishlist'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_ADD_WISHLIST_SELECTOR;
	}
	
	/**
	 * Add to wishlist selector
	 *
	 * @return XPath (string)
	 */
	public function getCompareSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/add_compare'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_ADD_COMPARE_SELECTOR;
	}
	
	/**
	 * NewProduct widget selector
	 *
	 * @return XPath (string)
	 */
	public function getListWidgetSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_SELECTOR;
	}
	
	/**
	 * NewProduct widget click selector
	 *
	 * @return XPath (string)
	 */
	public function getListWidgetClickSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget_click'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_CLICK_SELECTOR;
	}
	
	/**
	 * Get widget add to cart selector
	 * 
	 * @return string
	 */
	public function getListWidgetCartCategorySelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_widget_cart'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_WIDGET_CART_SELECTOR;
	}
	
	
	/**
	 * Category items click selector
	 *
	 * @return XPath (string)
	 */
	public function getListClickSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/click'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_LIST_CLICK_SELECTOR;
	}
	
	/**
	 * Category items add to wishlist selector
	 *
	 * @return XPath (string)
	 */
	public function getListWishlistSelector()
	{
	    if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_wishlist'))
	    {
	        return $selector;
	        
	    }
	    
	    return \Anowave\Ec\Helper\Constants::XPATH_LIST_WISHLIST_SELECTOR;
	}
	
	/**
	 * Category items add to compare selector
	 *
	 * @return XPath (string)
	 */
	public function getListCompareSelector()
	{
	    if ('' !== $selector = (string) $this->getConfig('ec/selectors/list_compare'))
	    {
	        return $selector;
	        
	    }
	    
	    return \Anowave\Ec\Helper\Constants::XPATH_LIST_COMPARE_SELECTOR;
	}
	
	/**
	 * Add to cart selector (product detail page)
	 *
	 * @return XPath (string)
	 */
	public function getCartSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_SELECTOR;
	}
	
	/**
	 * Add to cart selector (direct button from categories)
	 *
	 * @return XPath (string)
	 */
	public function getCartCategorySelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart_list'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_CATEGORY_SELECTOR;
	}
	
	/**
	 * Remove from cart selector
	 *
	 * @return XPath (string)
	 */
	public function getDeleteSelector()
	{
		if ('' !== $selector = (string) $this->getConfig('ec/selectors/cart_delete'))
		{
			return $selector;
			
		}
		
		return \Anowave\Ec\Helper\Constants::XPATH_CART_DELETE_SELECTOR;
	}
	
	/**
	 * Get customer reviews badge position
	 * 
	 * @return mixed
	 */
	public function getCustomerReviewsPosition()
	{
	    return $this->getConfig('ec/customer_reviews/position');
	}
	
	public function getCustomerReviewsMerchantId()
	{
	    return $this->getConfig('ec/customer_reviews/merchant_id');
	}
	
	/**
	 * Get customer reviews dalivery date offset 
	 * 
	 * @return mixed
	 */
	public function getCustomerReviewsDeliveryOffset()
	{
	    return $this->getConfig('ec/customer_reviews/delivery_date');
	}
	
	/**
	 * Get customer reviews delivery 
	 * 
	 * @param number $offset
	 */
	public function getCustomerReviewsDeliveryDate($offset)
	{
	    $date = time();
	    
	    try 
	    {
	        $date = strtotime($this->getCustomerReviewsDeliveryOffset(), $offset);
	    }
	    catch (\Exception $e){}
	    
	    return date('Y-m-d', $date);
	}
	
	/**
	 * Get customer reviews payload 
	 * 
	 * @param \Magento\Framework\View\Element\AbstractBlock $block
	 */
	public function getCustomerReviewsPayload($block)
	{
	    /**
	     * Products array 
	     * 
	     * @var array $products
	     */
	    $products = [];
	    
	    /**
	     * Payload 
	     * 
	     * @var array $payload
	     */
	    $payload = 
	    [
	        'merchant_id'              => $this->getCustomerReviewsMerchantId(),
	        'email'                    => null,
	        'order_id'                 => null,
	        'delivery_country'         => null,
	        'estimated_delivery_date'  => null,
	        'products'                 => [],
	        'opt_in_style'             => $this->getConfig('ec/customer_reviews/position')
	    ];
	    
	    foreach ($this->getOrders($block) as $order)
	    {
	        if ($this->isCustomerReviewsGTIN())
	        {
	            $attribute = $this->getCustomerReviewsGTINAttribute();
	            
	            if ($attribute)
	            {
        	        foreach ($order->getAllVisibleItems() as $item)
        	        {
        	            $gtin = $this->escape($item->getData($attribute));
        	            
        	            if ($gtin)
        	            {
            	            $payload['products'][] = 
            	            [
            	                'gtin' => $gtin
            	            ];
        	            }
        	        }
	            }
	        }
	        
	        $payload['order_id'] = $order->getIncrementId();
	        
	        if ($order->getShippingAddress())
	        {
	           $payload['delivery_country'] = $order->getShippingAddress()->getCountryId();
	        }
	        
	        /**
	         * Set email
	         */
	        $payload['email'] = $this->getCustomerEmail($order);
	        
	        /**
	         * Set delivery date
	         */
	        $payload['estimated_delivery_date'] = $this->getCustomerReviewsDeliveryDate(time());
	    }
	    
	    if (!$payload['products'])
	    {
	        unset($payload['products']);
	    }

	    return $this->getJsonHelper()->encode($payload);
	}
	
	/**
	 * Get search attributes 
	 * 
	 * @return string
	 */
	public function getSearchAttributes()
	{
		return $this->attributes->getAttributes();
	}
	/**
	 * Get event manager 
	 * 
	 * @return \Magento\Framework\Event\ManagerInterface
	 */
	public function getEventManager()
	{
		return $this->eventManager;
	}
	
	/**
	 * Get category repository 
	 * 
	 * @return \Magento\Catalog\Model\CategoryRepository
	 */
	public function getCategoryRepository()
	{
	    return $this->categoryRepository;
	}
	
	/**
	 * Get sales order collection
	 *
	 * @return \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	public function getSalesOrderCollection()
	{
		return $this->salesOrderCollection;
	}
	
	/**
	 * Get JSON helper 
	 * 
	 * @return \Anowave\Ec\Helper\Json
	 */
	public function getJsonHelper()
	{
		return $this->jsonHelper;
	}
	
	/**
	 * Get layer resolver
	 * 
	 * @return \Magento\Catalog\Model\Layer\Resolver
	 */
	public function getLayerResolver()
	{
	    return $this->layerResolver;
	}
	
	/**
	 * Get attributes
	 * 
	 * @return \Anowave\Ec\Helper\Attributes
	 */
	public function getAttributes()
	{
	    return $this->attributes;
	}
	
	/**
	 * Get store manager
	 * 
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager()
	{
	    return $this->storeManager;
	}
	
	/**
	 * Get request 
	 * 
	 * @return \Magento\Framework\App\Request\Http
	 */
	public function getRequest()
	{
	    return $this->request;
	}
	
	public function getProductRepository()
	{
	    return $this->productRepository;
	}
	
	/**
	 * Get form key 
	 * 
	 * @return string
	 */
	public function getFormKey() : string
	{
	    return $this->formKey->getFormKey();
	}
	
	/**
	 * Get Facebook Conversions API endpoint 
	 * 
	 * @return \Anowave\Ec\Model\Facebook\ConversionsApi
	 */
	public function getFacebookConversionsApi()
	{
	    if (!$this->facebook_conversions_api)
	    {
	        $user_data = [];
	        
	        if ($this->isLogged())
	        {
	            /**
	             * Get customer
	             *
	             * @var \Magento\Customer\Model\Data\Customer $customer
	             */
	            $customer = $this->getCustomer();
	            
	            if ($customer)
	            {
	                if ($customer->getEmail())
	                {
	                    $user_data['email'] = strtolower(trim($customer->getEmail()));
	                }
	                
	                if ($customer->getFirstname())
	                {
	                    $user_data['first_name'] = strtolower(trim($customer->getFirstname()));
	                }
	                
	                if ($customer->getLastname())
	                {
	                    $user_data['last_name'] = strtolower(trim($customer->getLastname()));
	                }
	                
	                if ($customer->getGender())
	                {
	                    $user_data['gender'] = $this->eavConfig->getAttribute('customer', 'gender')->getSource()->getOptionText($customer->getGender());
	                }
	                
	                foreach ($customer->getAddresses() as $address)
	                {
	                    if ($address->getTelephone())
	                    {
	                        $user_data['phone'] =  preg_replace('/[^0-9]+/i', '', $address->getTelephone());
	                    }
	                    
	                    if ($address->getCity())
	                    {
	                        $user_data['city'] = strtolower(preg_replace('/[^a-zA-Z]/i','',$address->getCity()));
	                    }
	                    
	                    if ($address->getPostcode())
	                    {
	                        $user_data['zip_code'] = $address->getPostcode();
	                    }
	                    
	                    if ($address->getCountryId())
	                    {
	                        $user_data['country_code'] = strtolower($address->getCountryId());
	                    }
	                    
	                    if ($address->getRegion())
	                    {
	                        if (is_string($address->getRegion()))
	                        {
	                            $region = preg_replace('/[^a-z]/i','', $address->getRegionCode() ?? '');
	                        }
	                        elseif (is_object($address->getRegion()))
	                        {
	                            $region = preg_replace('/[^a-z]/i','', $address->getRegion()->getRegionCode() ?? '');
	                        }
	                        else 
	                        {
	                            $region = null;
	                        }

	                        if ($region)
	                        {
	                            $user_data['state'] = $region;
	                        }
	                    }
	                }
	                
	                $user_data = array_map(function($data)
	                {
	                    return hash('sha256', $data);
	                    
	                }, $user_data);
	            }
	        }
	        
	        $this->facebook_conversions_api = $this->facebookConversionsApiFactory->create(
            [
                'pixel_id'                          => $this->getFacebookConversionsApiPixelId(),
                'access_token'                      => $this->getFacebookConversionsApiAccessToken(),
                'test_event_code'                   => $this->getFacebookConversionsApiTestEventCode(),
                'user_data'                         => $user_data,
                'cookie_directive'                  => $this->supportCookieDirective(),
                'cookie_directive_constent_granted' => $this->isCookieConsentAccepted(), 
                'logger'                            => $this->logger
            ]); 
	        
	        /**
	         * Activate Facebook Conversons API
	         */
	        if ($this->supportFacebookConversionsApi())
	        {
	            $this->facebook_conversions_api->enable();
	        }
	    }
	    
	    return $this->facebook_conversions_api;
	}
	
	/**
	 * Returns information whether moving JS to footer is enabled
	 *
	 * @return bool
	 */
	public function isDeferEnabled(): bool
	{
	    return $this->scopeConfig->isSetFlag('dev/js/move_script_to_bottom',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	/**
	 * Escape quotes
	 * 
	 * @param string $string
	 * @return string
	 */
	public function escape($data)
	{
		return addcslashes((string) $data, '\'');
	}
	
	/**
	 * Escape string for HTML5 data attribute 
	 * 
	 * @param string $data
	 * @return string
	 */
	public function escapeDataArgument($data)
	{
		return str_replace(array('"','\''), array('&quot;','&apos;'), $data);
	}
}