<?php 

namespace Marvelic\AnowaveEc4\Helper;

class DataEc extends \Anowave\Ec\Helper\Data{
	const VARIANT_DELIMITER = '-';
	const VARIANT_DELIMITER_ATT = ':';
	const USE_ASYNC_EVENTS = false;
	protected $package = 'MAGE2-GTM';
	protected $config = 'ec/general/license';
	private $_orders = []; 
	private $_brandMap = [];
	protected $productRepository = null;
	protected $categoryRepository;
	protected $session = null;
	protected $groupRegistry = null;
	protected $orderCollectionFactory = null;
	protected $orderConfig = null;
	protected $registry = null;
	protected $httpContext = null;
	protected $catalogData = null;
	protected $customer = null;
	protected $productAttributeRepository = null;
	protected $optionCollection;
	protected $eavConfig;
	protected $eventManager = null;
	protected $dataLayer = null;
	protected $request;
	protected $storeManager = null;
	protected $productMetadata;
	protected $moduleList;
	protected $customerRepositoryInterface;
	protected $attribute;
	protected $attributes;
	protected $bridge;
	protected $redirect;
	protected $privateData;
	protected $categoryCollectionFactory;
	protected $directive;
	protected $jsonHelper;
	protected $stockItemInterface;
	protected $salesOrderCollection;
	protected $urlInt;
	protected $cart;
	protected $layerResolver;
	protected $facebook_conversions_api;
	protected $scopeResolver;
	protected $facebookConversionsApiFactory;
	protected $logger;
	protected $formKey;

    public function __construct(
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
    ){
        parent::__construct(
            $context,
            $registry,
            $productRepository,
            $categoryRepository,
            $categoryCollectionFactory,
            $sessionFactory,
            $groupRegistry,
            $orderCollectionFactory,
            $orderConfig,
            $httpContext,
            $catalogData,
            $productAttributeRepository,
            $optionCollection,
            $eavConfig,
            $dataLayer,
            $storeManager,
            $productMetadata,
            $moduleList,
            $customerRepositoryInterface,
            $attributes,
            $attribute,
            $bridge,
            $redirect,
            $privateData,
            $directive,
            $jsonHelper,
            $salesOrderCollection,
            $stockItemInterface,
            $cache,
            $urlInt,
            $cart,
            $layerResolver,
            $facebookConversionsApiFactory,
            $scopeResolver,
            $logger,
            $formKey,
            $data
        );
        $this->productRepository = $productRepository;
    }
    public function getIdentifierItem(\Magento\Framework\Api\ExtensibleDataInterface $item)
	{
		if($item->getProductType() === "configurable"){
			return $item->getSku();
		}

	    $product = $item->getProduct();
	    if (!$product){
	        if ($item->getProductId()){
	            try{
	                $product = $this->productRepository->getById($item->getProductId());
	            }
	            catch (\Exception $e){
	                return $item->getSku();
	            }
	        }
	    }
	    
	    
	    return $this->getIdentifier($product);
	    
	}
}