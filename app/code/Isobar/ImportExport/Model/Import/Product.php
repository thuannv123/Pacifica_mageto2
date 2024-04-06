<?php

namespace Isobar\ImportExport\Model\Import;

use Firebear\ImportExport\Model\Import\Context;
use Firebear\ImportExport\Model\Import\SourceManager;
use Firebear\ImportExport\Model\Import\UploaderFactory;
use Firebear\ImportExport\Model\JobRepository;
use Firebear\ImportExport\Ui\Component\Listing\Column\Import\Source\Configurable\Type\Options as TypeOptions;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Isobar\ImportExport\Api\ConfigurableTypeOptionsInterface;
use Magento\CatalogImportExport\Model\Import\Product as MagentoProduct;
use Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel;
use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Swatches\Model\Swatch;
use function array_merge;
use Exception;
use function explode;
use Firebear\ImportExport\Api\UrlKeyManagerInterface;
use Firebear\ImportExport\Helper\Additional;
use Firebear\ImportExport\Helper\Data as FirebearImportExportData;
use Firebear\ImportExport\Model\Cache\Type\ImportProduct as ImportProductCache;
use Firebear\ImportExport\Model\Import;
use Firebear\ImportExport\Model\Import\Product\CategoryProcessor;
use Firebear\ImportExport\Model\Import\Product\Image as ImportImage;
use Firebear\ImportExport\Model\Import\Product\ImageProcessor as ImportImageProcessor;
use Firebear\ImportExport\Model\Import\Product\OptionFactory;
use Firebear\ImportExport\Model\Import\Product\ConfigurationVariations;
use Firebear\ImportExport\Model\Import\Product\Price\Rule\ConditionFactory as ConditionFactoryAlias;
use Firebear\ImportExport\Model\ResourceModel\Job\CollectionFactory;
use Firebear\ImportExport\Model\Source\Import\Config;
use Firebear\ImportExport\Model\Translation\Translator;
use Firebear\ImportExport\Api\Data\SeparatorFormatterInterface;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data as CatalogHelperData;
use Magento\Catalog\Helper\Product as CatalogHelperProduct;
use Magento\Catalog\Model\CategoryLinkRepository;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\ActionFactory;
use Magento\Catalog\Model\Product\Link as ProductLink;
use Magento\Catalog\Model\Product\Media\ConfigInterface;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\LinkFactory;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\CatalogImportExport\Model\Import\Product\SkuProcessor;
use Magento\CatalogImportExport\Model\Import\Product\StoreResolver;
use Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor;
use Magento\CatalogImportExport\Model\Import\Product\Type\Factory;
use Magento\CatalogImportExport\Model\Import\Product\Validator;
use Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory;
use Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Eav\Model\EntityFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory as AttributeGroupCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\ImportExport\Model\Import\Config as ImportConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory as SwatchCollectionFactory;
use Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory as TaxClassCollectionFactory;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use function strtolower;
use function version_compare;
use \Laminas\Validator\Exception\ExceptionInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ObjectManager;
use Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Import\Attributes\SystemOptions;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as TypeConfigurable;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Firebear\ImportExport\Model\Export\RowCustomizer\ProductVideo;
use InvalidArgumentException;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\Framework\App\ResourceConnection;
use Magento\CatalogImportExport\Model\Import\Product\StatusProcessor;
use Firebear\ImportExport\Model\Import\Product\Type\Downloadable;
use Symfony\Component\Console\Output\ConsoleOutput;

class Product extends \Firebear\ImportExport\Model\Import\Product
{
    const IMPORT_PRODUCT_LOG_CONF_PATH = 'firebear_importexport/import_product_log/enable';
    const ATTR_COLOR = 'color';

    private ProductResource $productResource;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SwatchCollectionFactory
     */
    protected $swatchCollectionFactory;

    /**
     * Product entity link field
     *
     * @var string
     */
    private $productEntityLinkField;

    /**
     * @var SeparatorFormatterInterface
     */
    private $separatorFormatter;

    private $cachedSwatchOptions = [];
    private $importCollection;
    private $_isRowCategoryMapped;
    private $lastSku;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Stock Item Importer
     *
     * @var StockItemImporterInterface
     */
    private $stockItemImporter;

    /**
     * Product entity identifier field
     *
     * @var string
     */
    private $productEntityIdentifierField;

    private $storeIds = [];

    private $isSplitConfigurableByColorType = false;

    private $cacheMediaGallery = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var StatusProcessor
     */
    private $statusProcessor;

    protected $mediaGallerySimple = [];

    private $isSplitConfigurableWithoutColorType = false;

    /**
     * @param Context $context
     * @param ImportImage $importImage
     * @param ImportImageProcessor $importImageProcessor
     * @param FirebearImportExportData $helper
     * @param CacheInterface $cache
     * @param Additional $additional
     * @param ManagerInterface $eventManager
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param StockStateProviderInterface $stockStateProvider
     * @param CatalogHelperData $catalogData
     * @param ImportConfig $importConfig
     * @param Config $fireImportConfig
     * @param ResourceModelFactory $resourceFactory
     * @param OptionFactory $optionFactory
     * @param AttributeSetCollectionFactory $setColFactory
     * @param Factory $productTypeFactory
     * @param LinkFactory $linkFactory
     * @param ProductFactory $proxyProdFactory
     * @param Filesystem $filesystem
     * @param ItemFactory $stockResItemFac
     * @param TimezoneInterface $localeDate
     * @param DateTime $dateTime
     * @param IndexerRegistry $indexerRegistry
     * @param StoreResolver $storeResolver
     * @param SkuProcessor $skuProcessor
     * @param Validator $validator
     * @param ObjectRelationProcessor $objectRelationProcessor
     * @param TransactionManagerInterface $transactionManager
     * @param TaxClassProcessor $taxClassProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param Url $productUrl
     * @param AttributeFactory $attributeFactory
     * @param EntityFactory $eavEntityFactory
     * @param AttributeGroupCollectionFactory $groupCollectionFactory
     * @param CatalogHelperProduct $productHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ProductRepositoryInterface $productRepository
     * @param ProductCollectionFactory $collectionFactory
     * @param GroupFactory $groupFactory
     * @param WebsiteFactory $websiteFactory
     * @param CategoryProcessor $categoryProcessor
     * @param UploaderFactory $uploaderFactory
     * @param TaxClassCollectionFactory $collectionTaxFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $importCollectionFactory
     * @param ConditionFactoryAlias $priceRuleConditionFactory
     * @param Data $swatchesHelperData
     * @param Media $swatchHelperMedia
     * @param SwatchCollectionFactory $swatchCollectionFactory
     * @param ConfigInterface $mediaConfig
     * @param Manager $moduleManager
     * @param ProductLink $productLink
     * @param UrlKeyManagerInterface $urlKeyManager
     * @param CategoryLinkRepository $categoryLinkRepository
     * @param ActionFactory $productActionFactory
     * @param Translator $translator
     * @param SourceManager $sourceManager
     * @param ConfigurationVariations $configurationVariations
     * @param SeparatorFormatterInterface $separatorFormatter
     * @param ProductResource $productResource
     * @param JobRepository $jobRepository
     * @param RequestInterface $request
     * @param Json $serializer
     * @param LoggerInterface $logger
     * @param array $data
     * @param array $integrations
     * @param array $dateAttrCodes
     * @param CatalogConfig|null $catalogConfig
     * @param DateTimeFactory|null $dateTimeFactory
     * @param ResourceConnection $resourceConnection
     * @param StatusProcessor $statusProcessor
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        ImportImage $importImage,
        ImportImageProcessor $importImageProcessor,
        FirebearImportExportData $helper,
        CacheInterface $cache,
        Additional $additional,
        ManagerInterface $eventManager,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        StockStateProviderInterface $stockStateProvider,
        CatalogHelperData $catalogData,
        ImportConfig $importConfig,
        Config $fireImportConfig,
        ResourceModelFactory $resourceFactory,
        OptionFactory $optionFactory,
        AttributeSetCollectionFactory $setColFactory,
        Factory $productTypeFactory,
        LinkFactory $linkFactory,
        ProductFactory $proxyProdFactory,
        Filesystem $filesystem,
        ItemFactory $stockResItemFac,
        TimezoneInterface $localeDate,
        DateTime $dateTime,
        IndexerRegistry $indexerRegistry,
        StoreResolver $storeResolver,
        SkuProcessor $skuProcessor,
        Validator $validator,
        ObjectRelationProcessor $objectRelationProcessor,
        TransactionManagerInterface $transactionManager,
        TaxClassProcessor $taxClassProcessor,
        ScopeConfigInterface $scopeConfig,
        Url $productUrl,
        AttributeFactory $attributeFactory,
        EntityFactory $eavEntityFactory,
        AttributeGroupCollectionFactory $groupCollectionFactory,
        CatalogHelperProduct $productHelper,
        ProductMetadataInterface $productMetadata,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $collectionFactory,
        GroupFactory $groupFactory,
        WebsiteFactory $websiteFactory,
        CategoryProcessor $categoryProcessor,
        UploaderFactory $uploaderFactory,
        TaxClassCollectionFactory $collectionTaxFactory,
        StoreManagerInterface $storeManager,
        CollectionFactory $importCollectionFactory,
        ConditionFactoryAlias $priceRuleConditionFactory,
        Data $swatchesHelperData,
        Media $swatchHelperMedia,
        SwatchCollectionFactory $swatchCollectionFactory,
        ConfigInterface $mediaConfig,
        Manager $moduleManager,
        ProductLink $productLink,
        UrlKeyManagerInterface $urlKeyManager,
        CategoryLinkRepository $categoryLinkRepository,
        ActionFactory $productActionFactory,
        Translator $translator,
        SourceManager $sourceManager,
        ConfigurationVariations $configurationVariations,
        SeparatorFormatterInterface $separatorFormatter,
        ProductResource $productResource,
        JobRepository $jobRepository,
        RequestInterface $request,
        Json $serializer,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        StatusProcessor $statusProcessor = null,
        array $data = [],
        array $integrations = [],
        array $dateAttrCodes = [],
        CatalogConfig $catalogConfig = null,
        DateTimeFactory $dateTimeFactory = null
    ) {
        $this->productResource = $productResource;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->separatorFormatter = $separatorFormatter;
        $this->productCollectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;

        if (interface_exists(StockItemImporterInterface::class)) {
            $this->stockItemImporter = ObjectManager::getInstance()
                ->get(\Firebear\ImportExport\Model\Import\StockItemImporterInterface::class);
        }
        $this->statusProcessor = $statusProcessor ?: ObjectManager::getInstance()
            ->get(StatusProcessor::class);

        parent::__construct(
            $context,
            $importImage,
            $importImageProcessor,
            $helper,
            $cache,
            $additional,
            $eventManager,
            $stockRegistry,
            $stockConfiguration,
            $stockStateProvider,
            $catalogData,
            $importConfig,
            $fireImportConfig,
            $resourceFactory,
            $optionFactory,
            $setColFactory,
            $productTypeFactory,
            $linkFactory,
            $proxyProdFactory,
            $filesystem,
            $stockResItemFac,
            $localeDate,
            $dateTime,
            $indexerRegistry,
            $storeResolver,
            $skuProcessor,
            $validator,
            $objectRelationProcessor,
            $transactionManager,
            $taxClassProcessor,
            $scopeConfig,
            $productUrl,
            $attributeFactory,
            $eavEntityFactory,
            $groupCollectionFactory,
            $productHelper,
            $productMetadata,
            $productRepository,
            $collectionFactory,
            $groupFactory,
            $websiteFactory,
            $categoryProcessor,
            $uploaderFactory,
            $collectionTaxFactory,
            $storeManager,
            $importCollectionFactory,
            $priceRuleConditionFactory,
            $swatchesHelperData,
            $swatchHelperMedia,
            $swatchCollectionFactory,
            $mediaConfig,
            $moduleManager,
            $productLink,
            $urlKeyManager,
            $categoryLinkRepository,
            $productActionFactory,
            $translator,
            $sourceManager,
            $configurationVariations,
            $separatorFormatter,
            $jobRepository,
            $request,
            $data,
            $integrations,
            $dateAttrCodes,
            $catalogConfig,
            $dateTimeFactory
        );
    }

    /**
     * @param $field
     * @param $value
     * @return \Magento\Framework\DataObject
     */
    public function getConfigurableProductsByPrimaryAttribute($field, $value)
    {
        return $this->productCollectionFactory->create()
            ->addFieldToFilter('type_id', 'configurable')
            ->addAttributeToFilter($field, $value)
            ->getFirstItem();
    }


    /**
     * @param array $rowData
     * @param array $attrs
     * @param array $configurableData
     */
    public function prepareConfigurableVariation(array $rowData, array $attrs, $visibilityData, array &$configurableData = array())
    {
        if (
            !empty($this->_parameters['configurable_switch']) && isset($rowData['product_type'])
            && ($rowData['product_type'] == 'simple' || $rowData['product_type'] == 'virtual')
        ) {
            $simpleValAttr = empty($this->_parameters['copy_simple_value']) ? []
                : array_column($this->_parameters['copy_simple_value'], 'copy_simple_value_attributes');
            $field = $this->_parameters['configurable_field'];
            $skuConf = null;
            // $isSplitColorTypeOption = false;
            if (isset($rowData[$field])) {
                switch ($this->_parameters['configurable_type']) {
                    case TypeOptions::FIELD:
                        if ($rowData[$field] && $this->getCorrectSkuAsPerLength($rowData) != $rowData[$field]) {
                            $skuConf = $rowData[$field];
                        }
                        break;
                    case TypeOptions::PART_UP:
                        $array = explode($this->_parameters['configurable_part'], $rowData[$field]);
                        if (count($array) > 1) {
                            $skuConf = $array[0];
                        }
                        break;
                    case TypeOptions::PART_DOWN:
                        $array = explode($this->_parameters['configurable_part'], $rowData[$field]);
                        if (count($array) > 1) {
                            $skuConf = $array[count($array) - 1];
                        }
                        break;
                    case TypeOptions::SUB_UP:
                        $skuConf = substr($rowData[$field], 0, $this->_parameters['configurable_symbols']);
                        break;
                    case TypeOptions::SUB_DOWN:
                        $skuConf = substr($rowData[$field], -$this->_parameters['configurable_symbols']);
                        break;
                    case ConfigurableTypeOptionsInterface::SPLIT_COLOR_ATTRIBUTE:
                        $this->isSplitConfigurableByColorType = true;

                        //get primary variation
                        $skuConf = $rowData[$field];
                        break;
                    case ConfigurableTypeOptionsInterface::SPLIT_WITHOUT_COLOR_ATTRIBUTE:
                        if ($rowData[$field] && $this->getCorrectSkuAsPerLength($rowData) != $rowData[$field]) {
                            $this->isSplitConfigurableWithoutColorType = true;
                            $skuConf = $rowData[$field];
                        }
                        break;
                }
            }
            if ($this->_replaceFlag && !isset($this->_oldSku[mb_strtolower($skuConf)])) {
                $skuConf = null;
            }
            if ($skuConf) {
                $newData = $rowData;
                $arrayConf = [];
                if ($this->isSplitConfigurableByColorType) {
                    $arrayConf[$field] = $rowData[$field];
                }

                if ($this->isSplitConfigurableWithoutColorType) {
                    $arrayConf[$field] = $rowData[$field];
                }

                if (!empty($this->_parameters['configurable_variations'])) {
                    foreach ($this->_parameters['configurable_variations'] as $attrField) {
                        if (isset($newData[$attrField]) && trim($newData[$attrField]) !== '') {
                            $arrayConf[$attrField] = $newData[$attrField];
                        }
                    }
                }
                if (!empty($arrayConf)) {
                    $arrayConf['sku'] = (string) $newData['sku'];
                    if (in_array(ProductInterface::VISIBILITY, $simpleValAttr)) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::VISIBILITY] =
                            $attrs[ProductInterface::VISIBILITY] ?? Visibility::VISIBILITY_BOTH;
                    }
                    if (in_array(ProductInterface::STATUS, $simpleValAttr)) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::STATUS] =
                            $attrs[ProductInterface::STATUS] ?? Status::STATUS_ENABLED;
                    }

                    if ($visibilityData['doesVisibilitySet'] && isset($attrs[ProductInterface::VISIBILITY])) {
                        $arrayConf[ProductInterface::VISIBILITY] = $attrs[ProductInterface::VISIBILITY];
                    }

                    if ($visibilityData['doesStatusSet'] && isset($attrs[ProductInterface::STATUS])) {
                        $arrayConf[ProductInterface::STATUS] = $attrs[ProductInterface::STATUS];
                    }

                    $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] = array_merge(
                        ($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] ?? []),
                        $this->configVariations->getAttrsImage($attrs, $this->_imagesArrayKeys)
                    );
                    $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['website_ids']
                        = $this->configVariations->getWebsiteArray($rowData, $this->getMultipleValueSeparator());
                    $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['attribute_set_id']
                        = $this->configVariations->getAttributeSetIdBySku($rowData, $this->_attrSetNameToId);
                    foreach ($simpleValAttr as $attrCode) {
                        if ($attrCode == 'category_ids' && isset($rowData[$attrCode])) {
                            $categoryIds = explode($this->getMultipleValueSeparator(), $rowData[$attrCode]);
                            if (!empty($categoryIds) && !is_array($categoryIds)) {
                                $categoryIds[] = $categoryIds;
                            }
                            $categoriesForCopy = [];
                            foreach ($categoryIds as $categoryId) {
                                $categoryId = (int)$categoryId;
                                $existingCategory = $this->categoryProcessor->getCategoryById($categoryId);
                                if (!empty($existingCategory) && $existingCategory) {
                                    $categoriesForCopy[] = $categoryId;
                                }
                            }
                            $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids'] =
                                $categoriesForCopy;
                            continue;
                        } elseif ($attrCode == 'additional_images') {
                            $bunchUploadedImages = $this->importImageProcessor->getBunchUploadedImages();
                            foreach (explode($this->getMultipleValueSeparator(), $rowData['_media_image']) as $image) {
                                $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['_media_image'][] =
                                    $bunchUploadedImages[$image] ?? '';
                            }
                            continue;
                        } elseif ($attrCode == 'additional_image_labels') {
                            if (isset($rowData['_media_image_label']) && !empty($rowData['_media_image_label'])) {
                                $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['_media_image_label'] =
                                    $rowData['_media_image_label'] ?? '';
                            } else {
                                $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['_media_image_label'] =
                                    $rowData['additional_images_label'] ?? '';
                            }
                            continue;
                        } elseif ($attrCode == 'category_ids' && isset($this->categoriesCache[$rowData['sku']])) {
                            $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids'] =
                                array_keys($this->categoriesCache[$rowData['sku']]);
                            continue;
                        } elseif (
                            $attrCode == \Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Import\Attributes\SystemOptions::RELATED_PRODUCT_ATTRIBUTE ||
                            $attrCode == \Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Import\Attributes\SystemOptions::CROSS_SELLS_PRODUCT_ATTRIBUTE ||
                            $attrCode == \Firebear\ImportExport\Ui\Component\Listing\Column\Entity\Import\Attributes\SystemOptions::UP_SELLS_PRODUCT_ATTRIBUTE
                        ) {
                            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][$attrCode] =
                                $rowData[$attrCode] ?? '';
                            continue;
                        }
                        if (isset($attrs[$attrCode])) {
                            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][$attrCode] =
                                $attrs[$attrCode] ?? '';
                        }
                        // $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][$attrCode] =
                        //     $attrs[$attrCode] ?? '';
                    }

                    if (
                        empty($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['image_label']) &&
                        isset($rowData['image_label']) &&
                        !empty($rowData['image_label'])
                    ) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['image_label'] = $rowData['image_label'];
                    }

                    if (
                        empty($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['small_image_label']) &&
                        isset($rowData['small_image_label']) &&
                        !empty($rowData['small_image_label'])
                    ) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['small_image_label'] = $rowData['small_image_label'];
                    }

                    if (
                        empty($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['thumbnail_label']) &&
                        isset($rowData['thumbnail_label']) &&
                        !empty($rowData['thumbnail_label'])
                    ) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['thumbnail_label'] = $rowData['thumbnail_label'];
                    }

                    $simpleProductImage = [];
                    if (!empty($this->_parameters['configurable_create'])) {
                        $simpleProductImage = $this->configVariations->getAttrsImage($attrs, $this->_imagesArrayKeys);
                    }
                    // $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] = array_merge(
                    //     ($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] ?? []),
                    //     $this->configVariations->getAttrsImage($attrs, $this->_imagesArrayKeys)
                    // );
                    $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] = array_merge(
                        ($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] ?? []),
                        $simpleProductImage
                    );

                    if (isset($rowData['_store'])) {
                        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['store_view_code'] =
                            $rowData['_store'];
                    }

                    $configurableData[(string) $skuConf][] = $arrayConf;
                }
            }
        }
    }

    /**
     * @param $simpleProducts
     * @param $field
     * @param $data
     * @return bool
     */
    private function hasSameAttributeValueInConfigurableProduct($simpleProducts, $field, $data)
    {
        foreach ($simpleProducts as $simpleProduct) {
            if ($simpleProduct[$field] == $data) {
                return true;
            }
        }
        return false;
    }

    /**
     * Save products data.
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function saveProductsData()
    {
        foreach ($this->_productTypeModels as $productTypeModel) {
            if ($productTypeModel instanceof Downloadable) {
                $productTypeModel->clearObject();
            }
        }
        $this->saveProducts();
        foreach ($this->_productTypeModels as $productTypeModel) {
            $productTypeModel->saveData();
        }

        $this->_saveLinks();
        $this->_saveStockItem();

        // $this->_saveMediaGallerySimple();

        if (!empty($this->_parameters['image_resize'])) {
            $this->addLogWriteln(__('Start resizing images for the bunch'), $this->getOutput(), 'info');
            $this->importImageProcessor->processImageResize();
            $this->addLogWriteln(__('Resizing images for the bunch is complete'), $this->getOutput(), 'info');
        }

        if ($this->_replaceFlag) {
            $this->getOptionEntity()->clearProductsSkuToId();
        }
        $this->getOptionEntity()->importData();
        $verbosity = false;
        if (!$this->helper->getProcessor()->inConsole) {
            $verbosity = ConsoleOutput::VERBOSITY_VERBOSE;
        }
        if (is_array($this->integrations)) {
            /**
             * @var $moduleKey
             * @var Import\Product\Integration\AbstractIntegration $integration
             */
            foreach ($this->integrations as $moduleKey => $integration) {
                if ($this->manager->isEnabled($moduleKey)) {
                    $integration->setLogger($this->getLogger());
                    $integration->setAdapter($this);
                    $integration->setDataSourceModel($this->_dataSourceModel);
                    $integration->importData($verbosity);
                }
            }
        }
        foreach ($this->productLinkData as $idConfigProduct => $typesLink) {
            $this->addProductLinks($idConfigProduct, $typesLink);
        }

        return $this;
    }


    /**
     * Gather and save information about product entities.
     *
     * @return \Firebear\ImportExport\Model\Import\Product
     * @throws LocalizedException
     * @throws ExceptionInterface
     * @throws Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function saveProducts()
    {
        $this->translator = $this->translator->init($this->_parameters);
        $this->cache->save(
            $this->getSerializer()->serialize($this->_parameters),
            'import_parameters',
            ['config_scopes']
        );
        $existingImages = [];
        $existingUpload = [];
        $entityLinkField = $this->getProductEntityLinkField();
        if (!empty($this->_parameters['import_source']) && $this->_parameters['import_source'] != 'file') {
            $this->_initSourceType($this->_parameters['import_source']);
        }
        $configurableData = [];

        $isPriceGlobal = $this->_catalogData->isPriceGlobal();
        $productLimit = null;
        $productsQty = null;
        $this->importImage->setConfig($this->_parameters);

        while ($nextBunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = $entityRowsUp = [];
            $attributes = [];
            $attributeDefaultData = [];
            $this->websitesCache = $this->categoriesCache = $this->categoryProductPosition = [];
            $this->categoryProcessor->setRowCategoryPosition($this->categoryProductPosition);
            $mediaGallery = $uploadedImages = [];
            $tierPrices = [];
            $previousType = $prevAttributeSet = null;
            $existingImages = $this->getExistingImages($nextBunch);
            $existingAttributeImages = $this->importImageProcessor->getExistingAttributeImages($nextBunch);
            if ($this->sourceType && $this->_parameters['image_import_source']) {
                $nextBunch = $this->prepareImagesFromSource($nextBunch);
            }

            $prevData = [];
            $createValuesAllowed = (bool)$this->scopeConfig->getValue(
                Import::CREATE_ATTRIBUTES_CONF_PATH,
                ScopeInterface::SCOPE_STORE
            );
            $storeIds = $this->getStoreIds();
            foreach ($nextBunch as $rowNum => $rowData) {
                $time = explode(" ", microtime());
                $startTime = $time[0] + $time[1];
                $mappedCategories = [];
                $mappedCategoriesPosition = [];
                if (isset($rowData[self::COL_CATEGORY])) {
                    $categoriesMapping = $this->categoriesMapping($rowData);
                    $rowData[self::COL_CATEGORY] = $categoriesMapping[self::COL_CATEGORY];
                    $rowData[self::COL_CATEGORY . '_position'] = $categoriesMapping[self::COL_CATEGORY . '_position'];
                    $mappedCategories = $categoriesMapping['mappedCategories'];
                    $mappedCategoriesPosition = $categoriesMapping['mappedCategoriesPosition'];

                    if (!empty($categoriesMapping[self::COL_CATEGORY . '_position'])) {
                        $rowData[self::COL_CATEGORY . '_position'] =
                            $categoriesMapping[self::COL_CATEGORY . '_position'];
                    }
                }

                $rowData = $this->joinIdenticalyData($rowData);
                if (isset($rowData['_attribute_set']) && isset($rowData['attribute_set_code'])) {
                    if (isset($rowData['update_attribute_set']) && ((int)($rowData['update_attribute_set']) > 0)) {
                        $rowData['_attribute_set'] = $rowData['attribute_set_code'];
                    } else {
                        unset($rowData['attribute_set_code']);
                    }
                }
                $oldSkus = $this->skuProcessor->reloadOldSkus()->getOldSkus();
                $sku = strtolower($this->getCorrectSkuAsPerLength($rowData));

                if (!isset($oldSkus[$sku])) {
                    if (
                        !isset($rowData['_attribute_set'])
                        || (isset($rowData['_attribute_set']) && empty($rowData['_attribute_set']))
                    ) {
                        $collectSets = $this->_attrSetIdToName;
                        reset($collectSets);
                        $rowData['_attribute_set'] = current($collectSets);
                    }
                }

                if (
                    isset($this->_parameters['remove_related_product'])
                    && $this->_parameters['remove_related_product'] == 1
                ) {
                    $this->removeRelatedProducts($this->getCorrectSkuAsPerLength($rowData));
                }

                if (
                    isset($this->_parameters['remove_crosssell_product'])
                    && $this->_parameters['remove_crosssell_product'] == 1
                ) {
                    $this->removeCrosssellProducts($this->getCorrectSkuAsPerLength($rowData));
                }

                if (
                    isset($this->_parameters['remove_upsell_product'])
                    && $this->_parameters['remove_upsell_product'] == 1
                ) {
                    $this->removeUpsellProducts($this->getCorrectSkuAsPerLength($rowData));
                }

                $rowData = $this->checkAdditionalImages($rowData);
                $rowData = $this->customChangeData($rowData);
                $rowData = $this->applyCategoryLevelSeparator($rowData);

                if (!$this->validateRow($rowData, $rowNum) || !$this->validateRowByProductType($rowData, $rowNum)) {
                    $this->addLogWriteln(
                        __('product with sku: %1 is not valided', $this->getCorrectSkuAsPerLength($rowData)),
                        $this->output,
                        'info'
                    );
                    $this->notValidedSku[] = strtolower($this->getCorrectSkuAsPerLength($rowData));
                    unset($nextBunch[$rowNum]);
                    continue;
                } else {
                    $rowData = $this->stripSlashes($rowData);
                }

                $productType = isset($rowData[self::COL_TYPE]) ?
                    strtolower($rowData[self::COL_TYPE]) :
                    $this->skuProcessor->getNewSku($this->getCorrectSkuAsPerLength($rowData))['type_id'];
                // custom
                if ($productType) {
                    $productTypeModel = $this->_productTypeModels[$productType];
                    if ($createValuesAllowed) {
                        $rowData = $this->createAttributeValues(
                            $productTypeModel,
                            $rowData
                        );
                    }
                }

                if (
                    !isset($rowData[self::COL_ATTR_SET]) ||
                    !isset($this->_attrSetNameToId[$rowData[self::COL_ATTR_SET]])
                ) {
                    $this->addRowError(ValidatorInterface::ERROR_INVALID_ATTR_SET, $rowNum);
                    $this->addLogWriteln(
                        __(
                            'product with sku: %1 is not valided. ' .
                                'Invalid value for Attribute Set column (set doesn\'t exist?)',
                            $this->getCorrectSkuAsPerLength($rowData)
                        ),
                        $this->output,
                        'info'
                    );
                    $this->notValidedSku[] = strtolower($this->getCorrectSkuAsPerLength($rowData));
                    unset($nextBunch[$rowNum]);
                    continue;
                }
                $urlKey = null;
                $isUpdate = $this->onlyUpdate || isset($this->_oldSku[$sku]);
                if (!($isUpdate && empty($rowData[self::URL_KEY]))) {
                    $urlKey = $this->getProductUrlKey($rowData);
                }

                if ($urlKey) {
                    if (!empty($rowData[self::URL_KEY])) {
                        // If url_key column and its value were in the CSV file
                        $rowData[self::URL_KEY] = $urlKey;
                    } elseif ($this->isNeedToChangeUrlKey($rowData)) {
                        // If url_key column was empty or even not declared in the CSV file but by the rules
                        // it is need to be setteed. In case when url_key is generating from name column we
                        // have to ensure that the bunch of products will pass for the event with url_key column.
                        $nextBunch[$rowNum][self::URL_KEY] = $rowData[self::URL_KEY] = $urlKey;
                    } elseif (isset($rowData[self::URL_KEY]) || isset($rowData[self::COL_NAME])) {
                        $rowData[self::URL_KEY] = $urlKey;
                    }
                }

                $this->urlKeys = [];
                $rowData = $this->adjustBundleTypeAttributes($rowData);

                if (empty($this->getCorrectSkuAsPerLength($rowData))) {
                    $rowData = array_merge($prevData, $this->deleteEmpty($rowData));
                } else {
                    $prevData = $rowData;
                }
                $sku = $this->getCorrectSkuAsPerLength($rowData);
                if ($this->onlyUpdate) {
                    $collectionUpdate = $this->collectionFactory->create()->addFieldToFilter(
                        self::COL_SKU,
                        $this->getCorrectSkuAsPerLength($rowData)
                    );
                    if (!$collectionUpdate->getSize()) {
                        $this->addLogWriteln(__('product with sku: %1 does not exist', $sku), $this->output, 'info');
                        unset($nextBunch[$rowNum]);
                        continue;
                    }
                }
                if ($this->getErrorAggregator()->isErrorLimitExceeded()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    unset($nextBunch[$rowNum]);
                    $this->notValidedSku[] = strtolower($this->getCorrectSkuAsPerLength($rowData));

                    continue;
                }

                if (isset($rowData['_attribute_set']) && isset($this->_attrSetNameToId[$rowData['_attribute_set']])) {
                    $this->skuProcessor->setNewSkuData(
                        $this->getCorrectSkuAsPerLength($rowData),
                        'attr_set_id',
                        $this->_attrSetNameToId[$rowData['_attribute_set']]
                    );
                }
                $rowScope = $this->getRowScope($rowData);
                $rowSku = $this->getCorrectSkuAsPerLength($rowData);
                $checkSku = $rowSku;

                if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
                    $checkSku = strtolower($rowSku);
                }
                if (!$rowSku) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                } elseif (self::SCOPE_STORE == $rowScope) {
                    // set necessary data from SCOPE_DEFAULT row
                    $rowData[self::COL_TYPE] = $this->skuProcessor->getNewSku($checkSku)['type_id'];
                    $rowData['attribute_set_id'] = $this->skuProcessor->getNewSku($checkSku)['attr_set_id'];
                    $rowData[self::COL_ATTR_SET] = $this->skuProcessor->getNewSku($checkSku)['attr_set_code'];
                }

                // Entity phase
                if (!isset($this->_oldSku[$checkSku])) {
                    // new row
                    if (!$productLimit || $productsQty < $productLimit) {
                        if (isset($rowData['has_options'])) {
                            $hasOptions = $rowData['has_options'];
                        } else {
                            $hasOptions = 0;
                        }
                        $entityRowsIn[$rowSku] = [
                            'attribute_set_id' => $this->skuProcessor->getNewSku($checkSku)['attr_set_id'],
                            'type_id' => $this->skuProcessor->getNewSku($checkSku)['type_id'],
                            'sku' => $rowSku,
                            'has_options' => $hasOptions,
                            'created_at' => $this->_localeDate->date()->format(DateTime::DATETIME_PHP_FORMAT),
                            'updated_at' => $this->_localeDate->date()->format(DateTime::DATETIME_PHP_FORMAT),
                        ];
                        $productsQty++;
                    } else {
                        $rowSku = null;
                        // sign for child rows to be skipped
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        continue;
                    }
                } else {
                    $array = [
                        'updated_at' => $this->_localeDate->date()->format(DateTime::DATETIME_PHP_FORMAT),
                        $entityLinkField => $this->_oldSku[$checkSku][$entityLinkField],
                    ];
                    $array['attribute_set_id'] = $this->skuProcessor->getNewSku($checkSku)['attr_set_id'];
                    $array['type_id'] = $productType;
                    // existing row
                    $entityRowsUp[] = $array;
                }

                // Categories phase
                if (!array_key_exists($rowSku, $this->categoriesCache)) {
                    $this->categoriesCache[$rowSku] = [];
                }

                $rowData['rowNum'] = $rowNum;
                $categoryIds = $this->getCategories($rowData);
                if (isset($rowData['category_ids'])) {
                    $catIds = explode($this->getMultipleValueSeparator(), $rowData['category_ids']);
                    $finalCatId = [];
                    foreach ($catIds as $catId) {
                        $catId = (int)$catId;
                        $existingCat = $this->categoryProcessor->getCategoryById($catId);
                        if (is_int($catId) && $catId > 0 && $existingCat && $existingCat->getId()) {
                            $finalCatId[] = $catId;
                        }
                    }
                    $categoryIds = array_merge($categoryIds, $finalCatId);
                }

                $categoryIds = array_merge($categoryIds, $mappedCategories);

                foreach ($categoryIds as $id) {
                    $this->categoriesCache[$rowSku][$id] = true;
                }

                $catIds = [];
                if ($this->isSkuExist($rowSku)) {
                    if (!isset($this->_oldSku[strtolower($rowData[self::COL_SKU])]['entity_id'])) {
                        $entityId = $this->_oldSku[strtolower($rowData[self::COL_SKU])]['row_id'];
                        $this->skuProcessor->setNewSkuData($rowData[self::COL_SKU], 'entity_id', $entityId);
                    } else {
                        $entityId = $this->_oldSku[strtolower($rowData[self::COL_SKU])]['entity_id'];
                    }
                    $oldCategoryIds = $this->getCategoryLinks($entityId);

                    if (
                        !empty($categoryIds)
                        && isset(
                            $this->_parameters['remove_product_categories'],
                            $this->_oldSku[strtolower($rowData[self::COL_SKU])]
                        )
                        && $this->_parameters['remove_product_categories'] > 0
                    ) {
                        foreach ($oldCategoryIds as $oldCategoryId) {
                            if (!in_array($oldCategoryId['category_id'], $categoryIds, false)) {
                                $this->categoriesCache[$rowSku][$oldCategoryId['category_id']] = false;
                            }
                        }
                    }
                }

                if (!isset($this->categoryProductPosition[$rowSku])) {
                    $this->categoryProductPosition[$rowSku] = [];
                }
                $this->categoryProductPosition[$rowSku] += $this->categoryProcessor->getRowCategoryPosition();
                $this->categoryProductPosition[$rowSku] += $mappedCategoriesPosition;

                if (isset($rowData[self::COL_CATEGORY]) && empty($rowData[self::COL_CATEGORY])) {
                    foreach ($catIds as $categoryId) {
                        $this->categoryLinkRepository->deleteByIds($categoryId, $rowData[self::COL_SKU]);
                        $this->categoriesCache[$rowSku] = [];
                        $this->categoryProductPosition[$rowSku] = [];
                    }
                }

                unset($rowData['rowNum']);
                if (!array_key_exists($rowSku, $this->websitesCache)) {
                    $this->websitesCache[$rowSku] = [];
                }
                // Product-to-Website phase
                if (!empty($rowData[self::COL_PRODUCT_WEBSITES])) {
                    $websiteCodes = explode($this->getMultipleValueSeparator(), $rowData[self::COL_PRODUCT_WEBSITES]);
                    foreach ($websiteCodes as $websiteCode) {
                        $websiteId = $this->storeResolver->getWebsiteCodeToId($websiteCode);
                        $this->websitesCache[$rowSku][$websiteId] = true;
                    }
                }
                // Price rules
                $rowData = $this->applyPriceRules($rowData);
                $fixedName = __("Fixed");
                $fixed = $fixedName;
                if (isset($rowData['_tier_price_value_type'])) {
                    $fixed = $rowData['_tier_price_value_type'] == $fixedName;
                }
                // Tier prices phase
                if (!empty($rowData['_tier_price_website'])) {
                    $tierPrices[$rowSku][] = [
                        'all_groups' => $rowData['_tier_price_customer_group'] == self::VALUE_ALL,
                        'customer_group_id' => $rowData['_tier_price_customer_group'] ==
                            self::VALUE_ALL ? 0 : $rowData['_tier_price_customer_group'],
                        'qty' => $rowData['_tier_price_qty'],
                        'value' => ($fixed) ? $rowData['_tier_price_price'] : 0,
                        'website_id' => self::VALUE_ALL == $rowData['_tier_price_website'] || $isPriceGlobal
                            ? 0
                            : $this->storeResolver->getWebsiteCodeToId(
                                $rowData['_tier_price_website']
                            ),
                        'percentage_value' => (!$fixed) ? $rowData['_tier_price_price'] : 0,
                    ];
                    $tierPrices = array_merge($tierPrices, $this->getTierPrices($rowData, $rowSku));
                } else {
                    $tierPrices += $this->getTierPrices($rowData, $rowSku);
                }
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addLogWriteln(__('product with sku: %1 is not valided', $sku), $this->output, 'info');
                    unset($nextBunch[$rowNum]);
                    continue;
                }
                // Media gallery phase
                if (
                    $this->publisher && isset($this->_parameters['deferred_images']) &&
                    $this->_parameters['deferred_images']
                ) {
                    $this->importImage->addMediaGalleryRows($rowData);
                    $this->log('-----------Deferred Import Images-----------');
                } else {
                    $this->importImageProcessor->setConfig($this->_parameters);
                    $this->processMediaGalleryRows(
                        $rowData,
                        $mediaGallery,
                        $existingImages,
                        $uploadedImages,
                        $rowNum,
                        $existingAttributeImages
                    );
                }

                if (!$productType === null) {
                    $previousType = $productType;
                }
                $prevAttributeSet = null;
                if (isset($rowData[self::COL_ATTR_SET])) {
                    $prevAttributeSet = $rowData[self::COL_ATTR_SET];
                }
                if (self::SCOPE_NULL == $rowScope) {
                    // for multiselect attributes only
                    if (!$prevAttributeSet === null) {
                        $rowData[self::COL_ATTR_SET] = $prevAttributeSet;
                    }
                    if ($productType === null && !$previousType === null) {
                        $productType = $previousType;
                    }
                    if ($productType === null) {
                        continue;
                    }
                }
                if (!$productType) {
                    $tempProduct = $this->skuProcessor->getNewSku($checkSku);
                    if (isset($tempProduct['type_id'])) {
                        $productType = $tempProduct['type_id'];
                    }
                }
                if ($productType) {
                    $rowScope = empty($rowData[self::COL_STORE]) ? self::SCOPE_DEFAULT : self::SCOPE_STORE;
                    $rowStore = (self::SCOPE_STORE == $rowScope)
                        ? $this->storeResolver->getStoreCodeToId($rowData[self::COL_STORE])
                        : 0;
                    $productTypeModel = $this->_productTypeModels[$productType];

                    if (!empty($rowData['tax_class_name'])) {
                        $rowData['tax_class_name'] = $this->getCurrentTaxClass($rowData['tax_class_name']);
                        $rowData['tax_class_id'] =
                            $this->taxClassProcessor->upsertTaxClass($rowData['tax_class_name'], $productTypeModel);
                    }

                    if (
                        $this->getBehavior() == Import::BEHAVIOR_APPEND ||
                        empty($this->getCorrectSkuAsPerLength($rowData))
                    ) {
                        if (
                            isset($this->_parameters['clear_attribute_value'])
                            && $this->_parameters['clear_attribute_value'] == 0
                        ) {
                            $rowData = $productTypeModel->clearEmptyData($rowData);
                        }
                    }

                    if (
                        isset($this->_parameters['clear_attribute_value'])
                        && $this->_parameters['clear_attribute_value'] == 1
                    ) {
                        $rowData[self::COL_STORE] = null;
                    }

                    $visibilityData = [
                        'doesStatusSet' => false,
                        'doesVisibilitySet' => false
                    ];
                    if (isset($rowData[ProductInterface::STATUS])) {
                        $visibilityData['doesStatusSet'] = true;
                    }
                    if (isset($rowData[ProductInterface::VISIBILITY])) {
                        $visibilityData['doesVisibilitySet'] = true;
                    }

                    $rowData = $productTypeModel->prepareAttributesWithDefaultValueForSave(
                        $rowData,
                        !isset($this->_oldSku[$checkSku])
                    );
                    $this->prepareConfigurableVariation($prevData, $rowData, $visibilityData, $configurableData);
                    //google translation data
                    if ($this->translator->isTranslatorSet()) {
                        $translateAttributes = $this->_parameters['translate_attributes'] ?? [];
                        $translateStore = (int)($this->_parameters['translate_store_ids'] ?? 0);
                    }

                    $skuLower = strtolower($rowSku);

                    // retrieves attributes
                    $attributeList = [];
                    foreach ($rowData as $attrCode => $attrValue) {
                        $attributeList[$attrCode] = $this->retrieveAttributeByCode($attrCode);
                    }

                    // attributes default values
                    $attributesDefaultValues = $this->getDefaultAttributesValue($attributeList, $skuLower, $rowStore);

                    foreach ($rowData as $attrCode => $attrValue) {
                        $attribute = $this->retrieveAttributeByCode($attrCode);
                        if ('multiselect' != $attribute->getFrontendInput() && self::SCOPE_NULL == $rowScope) {
                            // skip attribute processing for SCOPE_NULL rows
                            continue;
                        }
                        $attrId = $attribute->getId();
                        $backModel = $attribute->getBackendModel();
                        $attrTable = $attribute->getBackend()->getTable();
                        $storeIds = [0];

                        if (
                            'datetime' == $attribute->getBackendType()
                            && (in_array($attribute->getAttributeCode(), $this->dateAttrCodes)
                                || $attribute->getIsUserDefined()
                            )
                        ) {
                            $attrValue = $this->dateTime->formatDate($attrValue, false);
                        } elseif ('datetime' == $attribute->getBackendType() && strtotime($attrValue)) {
                            $attrValue = gmdate(
                                'Y-m-d H:i:s',
                                $this->_localeDate->date($attrValue)->getTimestamp()
                            );
                        }

                        $defaultValue = $attributesDefaultValues[$this->getAttributeDefaultValueKey($skuLower, $attribute->getId(), 0)] ?? false;
                        $storeValue = $attributesDefaultValues[$this->getAttributeDefaultValueKey($skuLower, $attribute->getId(), $rowStore)] ?? false;

                        if (!isset($this->adminAttributeValue[$rowSku])) {
                            $this->adminAttributeValue = [$rowSku => []];
                        }

                        if (false === $defaultValue && $rowStore == 0) {
                            $this->adminAttributeValue[$rowSku][$attrCode] = $attrValue;
                        }

                        /*
                         * If storeValue exists and the default value is same as new value then remove it
                         */
                        if ($storeValue && $defaultValue === (string)$attrValue && $rowStore > 0) {
                            // $this->_deleteStoreAttributeValue($attribute, $rowSku, $rowStore);
                            if (isset($attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID])) {
                                $defaultStoreIdValue =
                                    $attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID];
                                if ($defaultStoreIdValue === (string)$attrValue) {
                                    $this->_deleteStoreAttributeValue($attribute, $rowSku, $rowStore);
                                }
                            } else {
                                $this->_deleteStoreAttributeValue($attribute, $rowSku, $rowStore);
                            }
                        }

                        if (
                            $storeValue && (float)$defaultValue === (float)$attrValue
                            && $rowStore > 0 && is_numeric($attrValue)
                        ) {
                            if (isset($attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID])) {
                                $defaultStoreIdValue =
                                    $attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID];
                                if ((float)$defaultStoreIdValue === (float)$attrValue) {
                                    $this->_deleteStoreAttributeValue($attribute, $rowSku, $rowStore);
                                }
                            } else {
                                $this->_deleteStoreAttributeValue($attribute, $rowSku, $rowStore);
                            }
                        }

                        if ($this->translator->isTranslatorSet()) {
                            if (
                                !empty($translateAttributes)
                                && !empty($translateStore)
                                && !isset($attributes[$attrTable][$rowSku][$attrId][$translateStore])
                                && in_array($attrCode, $translateAttributes, true)
                            ) {
                                $storeValue = $this->translator
                                    ->translateAttributeValue($attrValue, $attrCode, $translateStore);
                                $attributes[$attrTable][$rowSku][$attrId][$translateStore] = $storeValue;
                            } elseif (isset($translateStore) && $translateStore > 0) {
                                $this->_deleteStoreAttributeValue($attribute, $rowSku, $translateStore);
                            }
                        }
                        if ($defaultValue && ($defaultValue === (string)$attrValue)) {
                            if (isset($attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID])) {
                                $defaultStoreIdValue =
                                    $attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID];
                                if ($defaultStoreIdValue === (string)$attrValue) {
                                    $attributeDefaultData[$attrTable][$rowSku][$attrId] = true;
                                    continue;
                                }
                            } else {
                                $attributeDefaultData[$attrTable][$rowSku][$attrId] = true;
                                continue;
                            }
                        }

                        if (
                            $defaultValue && ((float)$defaultValue === (float)$attrValue)
                            && is_numeric($attrValue)
                        ) {
                            if (isset($attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID])) {
                                $defaultStoreIdValue =
                                    $attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID];
                                if ((float)$defaultStoreIdValue === (float)$attrValue) {
                                    $attributeDefaultData[$attrTable][$rowSku][$attrId] = true;
                                    continue;
                                }
                            } else {
                                $attributeDefaultData[$attrTable][$rowSku][$attrId] = true;
                                continue;
                            }
                        }

                        $adminValue = $this->adminAttributeValue[$rowSku][$attrCode] ?? false;
                        if (false !== $adminValue && $adminValue === $attrValue && $rowStore > 0) {
                            continue;
                        }

                        if (self::SCOPE_STORE == $rowScope) {
                            if (self::SCOPE_WEBSITE == $attribute->getIsGlobal()) {
                                // check website defaults already set
                                if (!isset($attributes[$attrTable][$rowSku][$attrId][$rowStore])) {
                                    // $storeIds = $this->storeResolver->getStoreIdToWebsiteStoreIds($rowStore);
                                    $storeIds = $this->prepareStoreIdToWebsiteStoreIds(
                                        $this->storeResolver->getStoreIdToWebsiteStoreIds($rowStore)
                                    );
                                }
                            } elseif (self::SCOPE_STORE == $attribute->getIsGlobal()) {
                                $storeIds = [$rowStore];
                            } elseif (self::SCOPE_DEFAULT == $attribute->getIsGlobal()) {
                                // $storeIds = [0];
                                $storeIds[] = Store::DEFAULT_STORE_ID;
                            }

                            if (!isset($this->_oldSku[$checkSku])) {
                                // $storeIds[] = 0;
                                $storeIds[] = Store::DEFAULT_STORE_ID;
                            }
                        }
                        $storeIds = array_unique($storeIds);
                        sort($storeIds);

                        foreach ($storeIds as $storeId) {
                            if ($storeId == Store::DEFAULT_STORE_ID) {
                                if (isset($attributeDefaultData[$attrTable][$rowSku][$attrId])) {
                                    continue;
                                }
                            }
                            if (!isset($attributes[$attrTable][$rowSku][$attrId][$storeId])) {
                                if (
                                    isset($this->_oldSku[$checkSku])
                                    && in_array($attrCode, ['image', 'small_image', 'thumbnail', 'swatch_image'])
                                    && !in_array(Store::DEFAULT_STORE_ID, $storeIds) && !$defaultValue
                                ) {
                                    $attributes[$attrTable][$rowSku][$attrId][Store::DEFAULT_STORE_ID] = $attrValue;
                                } else {
                                    $attributes[$attrTable][$rowSku][$attrId][$storeId] = $attrValue;
                                }
                            }
                        }
                        // restore 'backend_model' to avoid 'default' setting
                        $attribute->setBackendModel($backModel);
                    }

                    $time = explode(" ", microtime());
                    $endTime = $time[0] + $time[1];
                    $totalTime = $endTime - $startTime;
                    $totalTime = round($totalTime, 5);
                    $this->addLogWriteln(__('product with sku: %1 .... %2s', $sku, $totalTime), $this->output, 'info');
                }
            }
            if (method_exists($this, '_saveProductEntity')) {
                $this->_saveProductEntity(
                    $entityRowsIn,
                    $entityRowsUp
                );
            } else {
                $this->saveProductEntity(
                    $entityRowsIn,
                    $entityRowsUp
                );
            }
            if ((bool)$this->scopeConfig->getValue(self::IMPORT_PRODUCT_LOG_CONF_PATH)) {
                $logEntityRowsIn = $this->serializer->serialize($entityRowsIn);
                $this->logger->info('Imported products with Firebear: ' . $logEntityRowsIn);
                $logEntityRowsUp = $this->serializer->serialize($entityRowsUp);
                $this->logger->info('Updated products with Firebear: ' . $logEntityRowsUp);
            }

            $isCached = $this->_parameters['cache_products'] ?? false;
            if ($isCached) {
                $this->saveProductsCache($entityRowsIn, $entityRowsUp);
            }
            $this->afterSaveNewEntities($entityRowsIn);
            $this->addLogWriteln(__('Imported: %1 rows', count($entityRowsIn)), $this->output, 'info');
            $this->addLogWriteln(__('Updated: %1 rows', count($entityRowsUp)), $this->output, 'info');
            $this->_saveProductAttributes($attributes);
            $this->_saveProductWebsites(
                $this->websitesCache
            )->_saveProductCategories(
                $this->categoriesCache
            )->_saveProductTierPrices(
                $tierPrices
            );
            if (
                $this->publisher && isset($this->_parameters['deferred_images']) &&
                $this->_parameters['deferred_images']
            ) {
                $this->importImage->publishBranch();
                $this->log('-----------Deferred Import Images-----------');
            } else {
                $this->log('-----------Start Log-----------');
                if (!empty($mediaGallery)) {
                    $productLinkIdField = $this->getProductEntityLinkField();
                    foreach ($mediaGallery as $storeId => $storeMediaGalleryData) {
                        foreach ($storeMediaGalleryData as $sku => $productMediaGalleryData) {
                            $productId = $this->skuProcessor->getNewSku($sku)[$productLinkIdField];
                            $productMediaGalleryValueData[$productId] = $productMediaGalleryValueData[$productId] ?? [];
                            foreach ($productMediaGalleryData as $data) {
                                if (
                                    isset($data['value']) &&
                                    !empty($data['value']) &&
                                    !is_array($data['value']) &&
                                    !is_int($data['value'])
                                ) {
                                    if (!in_array($data['value'], $productMediaGalleryValueData[$productId])) {
                                        $productMediaGalleryValueData[$productId][] = $data['value'];
                                        $mediaGalleryValueData[] = [
                                            'attribute_id' => $data['attribute_id'],
                                            'value' => $data['value'],
                                        ];
                                        $mediaGalleryValueToEntityData[] = [
                                            'value' => $data['value'],
                                            $productLinkIdField => $productId,
                                        ];
                                    }
                                    $mediaGalleryValues[] = $data['value'];
                                    $mediaGalleryValueToStoreData[] = [
                                        'value' => $data['value'],
                                        'store_id' => $storeId,
                                        $productLinkIdField => $productId,
                                        'label' => $data['label'],
                                        'position' => $data['position'],
                                        'disabled' => $data['disabled'],
                                    ];
                                } else {
                                    // Update additional_image
                                    if (isset($data['value_id'])) {
                                        $mediaGalleryValueForProduct[] = [
                                            'value_id' => $data['value_id'],

                                        ];
                                        $oldMediaGallerys = $this->resourceConnection->getConnection()->fetchAll(
                                            $this->resourceConnection->getConnection()->select()
                                                ->from($this->getResource()->getTable('catalog_product_entity_media_gallery_value'))
                                                ->where('value_id = ?', $data['value_id'])
                                        );
                                        if (!empty($oldMediaGallerys)) {
                                            if (
                                                isset($data['label']) &&
                                                isset($data['position']) &&
                                                isset($data['disabled']) &&
                                                isset($data['row_id']) &&
                                                isset($data['store_id'])
                                            ) {
                                                $this->resourceConnection->getConnection()->update(
                                                    $this->getResource()->getTable('catalog_product_entity_media_gallery_value'),
                                                    [
                                                        'label' => $data['label'],
                                                        'position' => $data['position'],
                                                        'disabled' => $data['disabled']
                                                    ],
                                                    [
                                                        'row_id = ?' => $data['row_id'],
                                                        'value_id = ?' => $data['value_id'],
                                                        'store_id = ?' => $data['store_id']
                                                    ]
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (
                        !empty($mediaGalleryValues) &&
                        !empty($mediaGalleryValueData) &&
                        !empty($mediaGalleryValueToEntityData) &&
                        !empty($mediaGalleryValueToStoreData) &&
                        !empty($productMediaGalleryValueData)
                    ) {
                        if (
                            isset($data['value']) &&
                            !empty($data['value']) &&
                            !is_array($data['value']) &&
                            !is_int($data['value'])
                        ) {
                            try {
                                $mediaValueIdValueMap = [];
                                $connection = $this->resourceConnection->getConnection();
                                $oldMediaValues = $connection->fetchCol(
                                    $connection->select()
                                        ->from($this->getResource()->getTable('catalog_product_entity_media_gallery'), ['value_id'])
                                        ->where('value IN (?)', $mediaGalleryValues)
                                );

                                // $this->log('Old media query: ' . (string) $connection->select()
                                //     ->from($this->getResource()->getTable('catalog_product_entity_media_gallery'), ['value_id'])
                                //     ->where('value IN (?)', $mediaGalleryValues));

                                if ($mediaGalleryValueData) {
                                    $mediaGalleryTable = $this->getResource()->getTable('catalog_product_entity_media_gallery');
                                    foreach ($mediaGalleryValueData as $key => $mediaGalleryValData) {
                                        if (isset($mediaGalleryValData['value'])) {
                                            $sql = "INSERT IGNORE INTO " . $mediaGalleryTable . " (attribute_id, value) VALUES ('" . $mediaGalleryValData['attribute_id'] . "', '" . $mediaGalleryValData['value'] . "')";
                                            $connection->query($sql);
                                            // $this->log('Insert Media Gallery ' . $key . ': ' . $sql);
                                        }
                                    }
                                }

                                $newMediaSelect = $connection->select()
                                    ->from($this->getResource()->getTable('catalog_product_entity_media_gallery'), ['value_id', 'value'])
                                    ->where('value IN (?)', $mediaGalleryValues);
                                if ($oldMediaValues) {
                                    $newMediaSelect->where('value_id NOT IN (?)', $oldMediaValues);
                                }
                                // $this->log('New media Select: ' . $newMediaSelect->__toString());

                                $mediaValueIdValueMap = $connection->fetchPairs($newMediaSelect);
                                $productIdMediaValueIdMap = $this->getProductIdMediaValueIdMapIsobar(
                                    $productMediaGalleryValueData,
                                    $mediaValueIdValueMap
                                );
                                $mediaGalleryValueToEntityData = $this->prepareMediaGalleryValueToEntityDataIsobar(
                                    $mediaGalleryValueToEntityData,
                                    $productIdMediaValueIdMap
                                );

                                if (!empty($mediaGalleryValueToEntityData)) {
                                    $mediaGalleryEntityTable = $this->getResource()->getTable(
                                        'catalog_product_entity_media_gallery_value_to_entity'
                                    );
                                    foreach ($mediaGalleryValueToEntityData as $key => $mediaGalleryValToEntityData) {
                                        if (isset($mediaGalleryValToEntityData['value_id']) && is_int($mediaGalleryValToEntityData['value_id'])) {
                                            $sql = "INSERT IGNORE INTO " . $mediaGalleryEntityTable . " (value_id, row_id) VALUES ('" . $mediaGalleryValToEntityData['value_id'] . "', '" . $mediaGalleryValToEntityData['row_id'] . "')";
                                            $connection->query($sql);
                                            // $this->log('Insert Media Gallery To Entity Table' . $key . ': ' . $sql);
                                        }
                                    }
                                }

                                $mediaGalleryValueToStoreData = $this->prepareMediaGalleryValueDataIsobar(
                                    $mediaGalleryValueToStoreData,
                                    $productIdMediaValueIdMap
                                );

                                if (!empty($mediaGalleryValueToStoreData)) {
                                    $mediaGalleryValueTable = $this->getResource()->getTable(
                                        'catalog_product_entity_media_gallery_value'
                                    );
                                    foreach ($mediaGalleryValueToStoreData as $key => $mediaGalleryValToStoreData) {
                                        if (isset($mediaGalleryValToStoreData['value_id']) && is_int($mediaGalleryValToStoreData['value_id'])) {

                                            if (!empty($mediaGalleryValToStoreData['label'])) {
                                                $label = addslashes($mediaGalleryValToStoreData['label']);
                                            } else {
                                                $label = $mediaGalleryValToStoreData['label'];
                                            }

                                            $sql = "INSERT INTO " . $mediaGalleryValueTable . " (value_id, store_id, label, position, disabled, row_id) VALUES ('" . $mediaGalleryValToStoreData['value_id'] . "', '" . $mediaGalleryValToStoreData['store_id'] . "', '" . $label . "', '" . $mediaGalleryValToStoreData['position'] . "', '" . $mediaGalleryValToStoreData['disabled'] . "', '" . $mediaGalleryValToStoreData['row_id'] . "')";
                                            $connection->query($sql);
                                            // $this->log('Insert Media Gallery To Entity Table' . $key . ': ' . $sql);
                                        }
                                    }
                                }
                            } catch (\Throwable $exception) {
                                if ($mediaValueIdValueMap) {
                                    $connection = $this->resourceConnection->getConnection();
                                    $connection->delete(
                                        $this->getResource()->getTable(
                                            'catalog_product_entity_media_gallery_value'
                                        ),
                                        $connection->quoteInto('value_id IN (?)', array_keys($mediaValueIdValueMap))
                                    );
                                }
                                throw $exception;
                            }
                        }
                    }
                }
                $this->log('-----------End Log-----------');
                $this->cacheMediaGallery = $mediaGallery;
                // $this->mediaGallerySimple = $mediaGallery;
            }
            $this->_saveProductCategoriesPosition($this->categoryProductPosition);

            $this->_eventManager->dispatch(
                'catalog_product_import_bunch_save_after',
                ['adapter' => $this, 'bunch' => $nextBunch]
            );
        }
        if (!empty($configurableData)) {
            $configurableImportData = $this->splitConfigurableDataByColor($configurableData);
            if ($this->isSplitConfigurableWithoutColorType) {
                $configurableImportData = $this->splitConfigurableDataWithoutColor($configurableData);
            }
            $this->generateCategoriesForConfigurableProductVariations($configurableImportData);
            $this->copyFirstGalleryToConfigurableProduct($configurableImportData);
            $this->saveConfigurationVariations($configurableImportData, $existingImages);
        }

        // if (!empty($this->_parameters['image_resize'])) {
        //     $this->addLogWriteln(__('Start resizing images for the bunch'), $this->getOutput(), 'info');
        //     $this->importImageProcessor->processImageResize();
        //     $this->addLogWriteln(__('Resizing images for the bunch is complete'), $this->getOutput(), 'info');
        // }

        $this->cache->clean([ImportProductCache::BUFF_CACHE]);
        return $this;
    }

    /**
     * Save product attributes.
     *
     * @param array $attributesData
     * @return $this
     */
    protected function _saveProductAttributes(array $attributesData)
    {
        $linkField = $this->getProductEntityLinkField();
        $statusAttributeId = (int) $this->retrieveAttributeByCode('status')->getId();
        foreach ($attributesData as $tableName => $skuData) {
            $linkIdBySkuForStatusChanged = [];
            $tableData = [];
            foreach ($skuData as $sku => $attributes) {
                $linkId = $this->_oldSku[strtolower($sku)][$linkField];
                foreach ($attributes as $attributeId => $storeValues) {
                    foreach ($storeValues as $storeId => $storeValue) {
                        if ($attributeId === $statusAttributeId) {
                            $this->statusProcessor->setStatus($sku, $storeId, $storeValue);
                            $linkIdBySkuForStatusChanged[strtolower($sku)] = $linkId;
                        }
                        $tableData[] = [
                            $linkField => $linkId,
                            'attribute_id' => $attributeId,
                            'store_id' => $storeId,
                            'value' => $storeValue,
                        ];
                    }
                }
            }
            if ($linkIdBySkuForStatusChanged) {
                $this->statusProcessor->loadOldStatus($linkIdBySkuForStatusChanged);
            }
            $this->resourceConnection->getConnection()->insertOnDuplicate($tableName, $tableData, ['value']);
        }

        return $this;
    }

    /**
     * @param array $configurableImportData
     * @return void
     */
    protected function copyFirstGalleryToConfigurableProduct(array $configurableImportData)
    {
        if (!$this->cacheMediaGallery) {
            return;
        }

        $mediaGallery = [];
        foreach ($configurableImportData as $configurableSku => $variations) {
            if (!count($variations)) {
                continue;
            }

            $firstVariation = reset($variations);

            foreach ($this->cacheMediaGallery as $storeId => $imageData) {
                if (isset($imageData[$firstVariation['sku']])) {
                    $mediaGallery[$storeId][$configurableSku] = $imageData[$firstVariation['sku']];
                }
            }
        }

        if (!empty($mediaGallery)) {
            $this->cacheMediaGallery = $mediaGallery;
        }
    }

    /**
     * @param $attributeName
     * @param ProductModel $product
     * @return array|bool|string|null
     */
    private function getProductAttribute($attributeName, ProductModel $product)
    {
        $attributeValue = $this->productResource->getAttributeRawValue($product->getId(), $attributeName, 0);
        if (is_array($attributeValue) && !count($attributeValue)) {
            return null;
        }

        return $attributeValue;
    }

    /**
     * @param ProductModel $product
     * @return array
     */
    private function prepareVariation(ProductModel $product)
    {
        $rowData = $attrs = $product->getData();
        $simpleValAttr = empty($this->_parameters['copy_simple_value']) ? []
            : array_column($this->_parameters['copy_simple_value'], 'copy_simple_value_attributes');

        $arrayConf = [];

        if (!empty($this->_parameters['configurable_variations'])) {
            foreach ($this->_parameters['configurable_variations'] as $attrField) {
                if (
                    $product->getAttributeText($attrField)
                    && trim($product->getAttributeText($attrField)) !== ''
                ) {
                    $arrayConf[$attrField] = $product->getAttributeText($attrField);
                }
            }
        }

        $arrayConf['sku'] = (string) $rowData['sku'];

        if (in_array(ProductInterface::VISIBILITY, $simpleValAttr)) {
            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::VISIBILITY] =
                $attrs[ProductInterface::VISIBILITY] ?? Visibility::VISIBILITY_BOTH;
        }
        if (in_array(ProductInterface::STATUS, $simpleValAttr)) {
            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::STATUS] =
                $attrs[ProductInterface::STATUS] ?? Status::STATUS_ENABLED;
        }
        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] = array_merge(
            ($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] ?? []),
            $this->configVariations->getAttrsImage($attrs, $this->_imagesArrayKeys)
        );
        $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['website_ids']
            = $this->configVariations->getWebsiteArray($rowData, $this->getMultipleValueSeparator());
        $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['attribute_set_id']
            = $this->configVariations->getAttributeSetIdBySku($rowData, $this->_attrSetNameToId);
        foreach ($simpleValAttr as $attrCode) {
            if ($attrCode == 'category_ids' && isset($rowData[$attrCode])) {
                $categoryIds = explode($this->getMultipleValueSeparator(), $rowData[$attrCode]);
                if (!empty($categoryIds) && !is_array($categoryIds)) {
                    $categoryIds[] = $categoryIds;
                }
                $categoriesForCopy = [];
                foreach ($categoryIds as $categoryId) {
                    $categoryId = (int)$categoryId;
                    $existingCategory = $this->categoryProcessor->getCategoryById($categoryId);
                    if (!empty($existingCategory) && $existingCategory) {
                        $categoriesForCopy[] = $categoryId;
                    }
                }
                $arrayConf[ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids'] =
                    $categoriesForCopy;
                continue;
            }
            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE][$attrCode] =
                $attrs[$attrCode] ?? $this->getProductAttribute($attrCode, $product);
        }

        $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] = array_merge(
            ($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE] ?? []),
            $this->configVariations->getAttrsImage($attrs, $this->_imagesArrayKeys)
        );

        /** add prefix for upc_code for configurable product */
        if (isset($arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['upccode'])) {
            $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['upccode'] =
                'configurable_' . $arrayConf[ConfigurationVariations::FIELD_COPY_VALUE]['upccode'];
        }

        return $arrayConf;
    }

    /**
     * @param array $rowData
     * @param $colorValue
     * @return mixed
     */
    protected function sortProductByColor(array $rowData = [], $colorValue = null)
    {
        foreach ($rowData as $key => $data) {
            if ($data['color'] == $colorValue) {
                unset($rowData[$key]);
                array_unshift($rowData, $data);
                break;
            }
        }
        return $rowData;
    }

    /**
     * @param $configurableData
     * @return array
     */
    public function splitConfigurableDataByColor($configurableData)
    {
        if (!$this->isSplitConfigurableByColorType) {
            return $configurableData;
        }

        $data = [];
        foreach ($configurableData as $configSku => $variationData) {
            $oldVariation = [];

            foreach ($variationData as $keyVariation => $variation) {
                /** add prefix for upc_code for configurable product */
                if (isset($variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'])) {
                    $variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'] =
                        'configurable_' . $variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'];
                }

                /** copy simple categories to configurable product */
                $variationData[$keyVariation][ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids']
                    = $this->getProductCategories($variation['sku']);
            }

            $oldConfig = $this->getConfigurableProductsByPrimaryAttribute("style_id", $configSku);
            if ($oldConfig->getId()) {
                $oldSimpleList = $oldConfig->getTypeInstance()->getUsedProducts($oldConfig);
                if ($oldSimpleList) {

                    foreach ($oldSimpleList as $simple) {
                        if ($this->hasSameAttributeValueInConfigurableProduct(
                            $variationData,
                            'color',
                            $simple->getAttributeText('color')
                        )) {
                            continue;
                        }

                        $oldVariation[] = $this->prepareVariation($simple);
                    }
                }
            }

            $fullVariation = array_merge($oldVariation, $variationData);

            foreach ($fullVariation as $variation) {
                $newConfigurationSku = $configSku . "-" . ucfirst(strtolower($variation["color"]));
                $data[$newConfigurationSku] = $this->sortProductByColor($fullVariation, $variation['color']);
            }
        }
        return $data;
    }

    public function splitConfigurableDataWithoutColor($configurableData)
    {
        $data = [];
        foreach ($configurableData as $configSku => $variationData) {

            foreach ($variationData as $keyVariation => $variation) {
                /** add prefix for upc_code for configurable product */
                if (isset($variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'])) {
                    $variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'] =
                        'configurable_' . $variationData[$keyVariation][ConfigurationVariations::FIELD_COPY_VALUE]['upccode'];
                }
            }
            $data[$configSku] = $variationData;
        }
        return $data;
    }

    /**
     * @param $configurableProductVariations
     * @return void
     */
    protected function generateCategoriesForConfigurableProductVariations(&$configurableProductVariations)
    {
        // Loop through each configurable product variation
        foreach ($configurableProductVariations as $configurableSku => &$variations) {

            // Get the configurable product by SKU
            $product = $this->getConfigurableProductsByPrimaryAttribute('sku', $configurableSku);

            // If the product doesn't exist or has no ID, skip to the next variation
            if (!$product || !$product->getId()) {
                continue;
            }

            // Add the SKU to the SKU processor
            $this->skuProcessor->addNewSku($configurableSku, $product->getData());

            // Get the categories for the first variation
            $firstVariation = reset($variations);
            $firstVariationKey = array_keys($variations)[0];

            //if sku not presented in import file by checking categoriesCache property.
            if (
                !isset($this->categoriesCache[$firstVariation['sku']])
                || !is_array($this->categoriesCache[$firstVariation['sku']])
            ) {
                continue;
            }

            $categories = [
                $configurableSku => $this->categoriesCache[$firstVariation['sku']]
            ];

            // Save the categories for the configurable product
            $this->_saveProductCategories($categories);

            // Remove configurable_import_param when this configurable product was created.
            unset($variations[$firstVariationKey][ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids']);
        }
    }


    /**
     * Get product entity link field
     *
     * @return string
     * @throws Exception
     */
    protected function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->getMetadataPool()
                ->getMetadata(ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }

    /**
     * Whether a url key is needed to be change.
     *
     * @param array $rowData
     * @return bool
     */
    protected function isNeedToChangeUrlKey(array $rowData): bool
    {
        $urlKey = $this->getUrlKey($rowData);
        $productExists = $this->isSkuExist($rowData[self::COL_SKU]);
        $markedToEraseUrlKey = isset($rowData[self::URL_KEY]);
        // The product isn't new and the url key index wasn't marked for change.
        if (!$urlKey && $productExists && !$markedToEraseUrlKey) {
            // Seems there is no need to change the url key
            return false;
        }
        return true;
    }

    /**
     * Check if product exists for specified SKU
     *
     * @param string $sku
     *
     * @return bool
     */
    protected function isSkuExist($sku)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            $sku = strtolower($sku);
        }
        return isset($this->_oldSku[$sku]);
    }

    /**
     * @param $sku
     * @param $attributeId
     * @param int $storeId
     * @return string
     */
    protected function getAttributeDefaultValueKey($sku, $attributeId, $storeId = 0)
    {
        return implode('-', [$sku, $attributeId, $storeId]);
    }

    /**
     * @param array $attributeList
     * @param $sku
     * @param int $rowStoreId
     * @return array|false
     * @throws Exception
     */
    protected function getDefaultAttributesValue(array $attributeList, $sku, $rowStoreId = 0)
    {
        $resultList = [];
        $defaultStoreId = 0;
        $storeIdList = [];
        $storeIdList[] = $defaultStoreId;
        if ($rowStoreId != $defaultStoreId) {
            $storeIdList[] = $rowStoreId;
        }

        $linkField = $this->getProductEntityLinkField();

        if (!isset($this->_oldSku[$sku][$linkField])) {
            return false;
        }
        $linksFieldId = $this->_oldSku[$sku][$linkField];

        $attributeIdListByGroup = [];
        foreach ($attributeList as $attribute) {
            $attributeIdListByGroup[$attribute->getBackend()->getTable()][] = $attribute->getId();
        }

        foreach ($attributeIdListByGroup as $table => $attrIdList) {
            $select = $this->resourceConnection->getConnection()->select()
                ->from($table, ['*'])
                ->where('attribute_id in(?)', $attrIdList)
                ->where('store_id in(?)', $storeIdList)
                ->where($linkField . ' = ?', $linksFieldId);

            $attrs = $this->resourceConnection->getConnection()->fetchAll($select);
            if (!empty($attrs)) {
                foreach ($attrs as $attr) {
                    $resultKey = $this->getAttributeDefaultValueKey($sku, $attr['attribute_id'], $attr['store_id']);
                    $resultList[$resultKey] = $attr['value'];
                }
            }
        }

        return $resultList;
    }

    /**
     * @param MagentoProduct\Type\AbstractType $productTypeModel
     * @param array $rowData
     * @return array
     * @throws LocalizedException
     */
    public function createAttributeValues(
        MagentoProduct\Type\AbstractType $productTypeModel,
        array $rowData
    ) {
        $options = [];
        if (isset($rowData[self::COL_ATTR_SET])) {
            $attributeSet = $rowData[self::COL_ATTR_SET];
            foreach ($rowData as $attrCode => $attrValue) {
                /**
                 * Add attribute to set & set's group
                 */
                if (preg_match('/^(attribute\|).+/', $attrCode)) {
                    $columnData = explode('|', $attrCode);
                    $columnData = $this->prepareAttributeData($columnData);
                    // might be already inside additional_attributes
                    if (isset($rowData[$columnData['attribute_code']])) {
                        unset($rowData[$attrCode]);
                        continue;
                    } else {
                        $rowData[$columnData['attribute_code']] = $rowData[$attrCode];
                        unset($rowData[$attrCode]);
                        $attrCode = $columnData['attribute_code'];
                    }
                }

                /**
                 * Prepare new values
                 */
                $attrParams = $productTypeModel->retrieveAttribute($attrCode, $attributeSet);
                if (!empty($attrParams)) {
                    if (
                        !$attrParams['is_static'] &&
                        isset($rowData[$attrCode]) &&
                        trim((string)$rowData[$attrCode]) !== ''
                    ) {
                        $empty = $this->_parameters['_import_empty_attribute_value_constant'] ?? null;
                        if (($attrParams['type'] == 'select' || $attrParams['type'] == 'multiselect')
                            && trim($rowData[$attrCode]) == $empty
                        ) {
                            continue;
                        }

                        switch ($attrParams['type']) {
                            case 'select':
                                $swatchOptionData = [];
                                $swatchOptions = [];
                                $arraySwatchOptionData = [];
                                /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute\Interceptor $attribute */
                                $attribute = $this->retrieveAttributeByCode($attrCode);
                                if ($this->swatchesHelperData->isVisualSwatch($attribute)) {
                                    $arraySwatchOptionData = $this->prepareSwatchOptionData($rowData[$attrCode]);
                                    $swatchOptionData = (count($arraySwatchOptionData) > 0) ? $arraySwatchOptionData : $rowData[$attrCode];
                                    $swatchOptions = $this->getSwatchesByOptionsId(
                                        $attrParams['options'],
                                        $attrParams['id']
                                    );
                                }
                                if ($this->swatchesHelperData->isTextSwatch($attribute)) {
                                    $swatchOptionData = $rowData[$attrCode];
                                    $swatchOptions = $this->getSwatchesByOptionsId(
                                        $attrParams['options'],
                                        $attrParams['id']
                                    );
                                }
                                if ((isset($attrParams['additional_data']['update_product_preview_image'])
                                        || isset($attrParams['additional_data']['use_product_image_for_swatch']))
                                    && $this->verifySwatchString($rowData[$attrCode])
                                ) {
                                    $swatchOptionData = $rowData[$attrCode];
                                    $swatchOptions = $this->getSwatchesByOptionsId(
                                        $attrParams['options'],
                                        $attrParams['id']
                                    );
                                }
                                $lowerAttrValue = strtolower($rowData[$attrCode]);
                                // no attribute option
                                $storeCode = isset($rowData[self::COL_STORE_VIEW_CODE])
                                    ? $rowData[self::COL_STORE_VIEW_CODE] : false;
                                $scopeStore = self::SCOPE_STORE == $this->getRowScope($rowData);
                                $attrOptions = $attrParams['options'];
                                if ($scopeStore && $storeCode && !empty($attrParams['options_store'][$storeCode])) {
                                    if (
                                        isset($attrOptions[$lowerAttrValue]) &&
                                        !isset($attrParams['options_store'][$storeCode][$lowerAttrValue])
                                    ) {
                                        $attrParams['options_store'][$storeCode][$lowerAttrValue] =
                                            $attrOptions[$lowerAttrValue];
                                    }
                                    $attrOptions = $attrParams['options_store'][$storeCode];
                                }

                                if (!isset($attrOptions[$lowerAttrValue])) {
                                    $options[$attrParams['id']][] = [
                                        'sort_order' => count($attrParams['options']) + 1,
                                        'value' => $rowData[$attrCode],
                                        'code' => $attrCode,
                                        'swatch_option' => $swatchOptionData,
                                    ];
                                } elseif (
                                    !empty($swatchOptionData) &&
                                    !array_key_exists($attrOptions[$lowerAttrValue], $swatchOptions)
                                ) { // no attribute swatch option
                                    $newSwatchOptions[$attrParams['id']][$attrOptions[$lowerAttrValue]] =
                                        $swatchOptionData;
                                } elseif (array_key_exists($attrOptions[$lowerAttrValue], $swatchOptions)) {
                                    // swatch attribute option exist
                                    $swatchOption = $swatchOptions[$attrOptions[$lowerAttrValue]];
                                    if ($this->swatchesHelperData->isVisualSwatch($attribute)) {
                                        // but has different value or type
                                        if (
                                            isset($attrParams['additional_data']['update_product_preview_image'])
                                            || isset($attrParams['additional_data']['use_product_image_for_swatch'])
                                            && !is_array($swatchOptionData)
                                        ) {
                                            break; //continue 2
                                        }
                                        if (!empty($diff = array_diff_assoc($swatchOptionData, $swatchOption))) {
                                            if ((array_key_exists('type', $diff) &&
                                                    $diff['type'] == Swatch::SWATCH_TYPE_VISUAL_COLOR)
                                                || (!array_key_exists('type', $diff) &&
                                                    $swatchOption['type'] == Swatch::SWATCH_TYPE_VISUAL_COLOR)
                                            ) {
                                                $this->updateSwatchOption($swatchOption, $diff);
                                            } elseif ($this->ifVisualSwatchOptionDifferent($swatchOption, $diff)) {
                                                $diff['value'] = $this->uploadVisualSwatchFile($diff['value']);
                                                $this->updateSwatchOption($swatchOption, $diff);
                                            }
                                        }
                                    }
                                }
                                break;
                            case 'multiselect':
                                $separator = $this->_parameters['_import_multiple_value_separator'] ?
                                    $this->_parameters['_import_multiple_value_separator'] :
                                    MagentoProduct::PSEUDO_MULTI_LINE_SEPARATOR;
                                $separator = $this->separatorFormatter->format($separator);
                                $values = explode($separator, $rowData[$attrCode]);
                                $storeCode = isset($rowData[self::COL_STORE_VIEW_CODE]) ? $rowData[self::COL_STORE_VIEW_CODE] : false;
                                $scopeStore = self::SCOPE_STORE == $this->getRowScope($rowData);
                                foreach ($values as $value) {
                                    $value = trim($value);
                                    if (!isset($attrParams['options'][strtolower($value)])) {
                                        $options[$attrParams['id']][] = [
                                            'sort_order' => count($attrParams['options']) + 1,
                                            'value' => $value,
                                            'code' => $attrCode,
                                            'store_id' => $scopeStore ?: Store::DEFAULT_STORE_ID
                                        ];
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            }

            /**
             * Create new values
             */
            if (!empty($options)) {
                $connection = $this->resourceConnection->getConnection();
                $resource = $this->getResource();
                foreach ($options as $attributeId => $optionsArray) {
                    foreach ($optionsArray as $option) {
                        /**
                         * @see \Magento\Eav\Model\ResourceModel\Entity\Attribute::_updateAttributeOption()
                         */
                        $table = $resource->getTable('eav_attribute_option');
                        $data = ['attribute_id' => $attributeId, 'sort_order' => $option['sort_order']];
                        $connection->insert($table, $data);
                        $intOptionId = $connection->lastInsertId($table);
                        /**
                         * @see \Magento\Eav\Model\ResourceModel\Entity\Attribute::_updateAttributeOptionValues()
                         */
                        $table = $resource->getTable('eav_attribute_option_value');
                        // $data = ['option_id' => $intOptionId, 'store_id' => 0, 'value' => $option['value']];
                        $data = [
                            'option_id' => $intOptionId,
                            'store_id' => Store::DEFAULT_STORE_ID,
                            'value' => $option['value']
                        ];
                        $connection->insert($table, $data);
                        if (isset($option['swatch_option']) && !empty($option['swatch_option'])) {
                            $this->insertNewSwatchOption(
                                $connection,
                                $resource,
                                $intOptionId,
                                $option['swatch_option'],
                                $attributeId,
                                $option['code']
                            );
                        } elseif (isset($option['swatch_option']) && $option['value'] !== null) {
                            $this->insertNewSwatchOption(
                                $connection,
                                $resource,
                                $intOptionId,
                                $option['value'],
                                $attributeId,
                                $option['code']
                            );
                        }
                        foreach ($this->_productTypeModels as $productTypeModel) {
                            $productTypeModel->addAttributeOption(
                                $option['code'],
                                strtolower($option['value']),
                                $intOptionId
                            );
                        }
                    }
                }
            }
            if (!empty($newSwatchOptions)) {
                $connection = $this->resourceConnection->getConnection();
                $resource = $this->getResource();
                foreach ($newSwatchOptions as $attributeId => $swatchOption) {
                    foreach ($swatchOption as $optionId => $swatchData) {
                        $this->insertNewSwatchOption($connection, $resource, $optionId, $swatchData, $attributeId);
                    }
                }
            }
        }

        return $rowData;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function verifySwatchString(string $value)
    {
        return (strpos($value, 'type=') !== false && strpos($value, 'value=') !== false)
            ? true
            : false;
    }

    /**
     * @param AdapterInterface $connection
     * @param ResourceModel $resource
     * @param int $optionId
     * @param array $swatchData
     * @param int $attributeId
     * @return \Firebear\ImportExport\Model\Import\Product
     * @throws LocalizedException
     */
    protected function insertNewSwatchOption($connection, $resource, $optionId, $swatchData, $attributeId, $attrCode = null)
    {
        if (!isset($swatchData['type']) && !isset($swatchData['value'])) {
            $typeId = Swatch::SWATCH_TYPE_TEXTUAL;
            if ($attrCode != null) {
                $attribute = $this->retrieveAttributeByCode($attrCode);
                if ($this->swatchesHelperData->isVisualSwatch($attribute)) {
                    $typeId = Swatch::SWATCH_TYPE_VISUAL_COLOR;
                }
            }
            $table = $resource->getTable('eav_attribute_option_swatch');
            $data = [
                'option_id' => $optionId,
                'store_id' => 0,
                'type' => $typeId,
                'value' => $swatchData,
            ];
            $connection->insert($table, $data);
            $this->cachedSwatchOptions[$attributeId][$optionId] = $data;
        } else {
            if ($swatchData['type'] == Swatch::SWATCH_TYPE_VISUAL_IMAGE) {
                $swatchData['value'] = $this->uploadVisualSwatchFile($swatchData['value']);
            }
            if ($swatchData['value']) {
                $table = $resource->getTable('eav_attribute_option_swatch');
                $data = [
                    'option_id' => $optionId,
                    'store_id' => 0,
                    'type' => $swatchData['type'],
                    'value' => $swatchData['value'],
                ];
                $connection->insert($table, $data);
                $this->cachedSwatchOptions[$attributeId][$optionId] = $data;
            }
        }

        return $this;
    }

    /**
     * Returns Swatch option data for Attribute Option Ids
     *
     * @param array $optionIds
     * @param int $attributeId
     *
     * @return array
     */
    protected function getSwatchesByOptionsId($optionIds, $attributeId)
    {
        if (!isset($this->cachedSwatchOptions[$attributeId]) || empty($this->cachedSwatchOptions[$attributeId])) {
            $this->cachedSwatchOptions[$attributeId] = [];
            $swatchCollection = $this->swatchCollectionFactory->create();
            $swatchCollection->addFilterByOptionsIds($optionIds);
            foreach ($swatchCollection as $item) {
                $this->cachedSwatchOptions[$attributeId][$item['option_id']] = $item->getData();
            }
        }

        return $this->cachedSwatchOptions[$attributeId];
    }

    /**
     * @param $data
     * @param array $existingImages
     * @return $this
     */
    protected function saveConfigurationVariations($data, $existingImages = [])
    {
        if (!empty($data)) {
            $configurableProductsData = [];
            $configData = $data;
            /**
             * @var string $skuConf
             * @var array $elements
             */
            foreach ($data as $skuConf => $elements) {
                $skuConf = (string) $skuConf;
                if (count($elements) < 1) {
                    continue;
                }
                $firstElement = current($elements) ?? [];
                $configurableProductVisibility =
                    $firstElement[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::VISIBILITY] ??
                    Visibility::VISIBILITY_BOTH;
                $configurableProductStatus =
                    $firstElement[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::STATUS] ??
                    Status::STATUS_ENABLED;
                $fieldCopyValue = $firstElement[ConfigurationVariations::FIELD_COPY_VALUE] ?? [];

                foreach ($elements as &$elementLink) {
                    unset($elementLink[ConfigurationVariations::FIELD_COPY_VALUE]);
                    unset($elementLink[ConfigurationVariations::FIELD_CONF_IMPORT]);
                }
                if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
                    $checkSku = mb_strtolower($skuConf);
                } else {
                    $checkSku = $skuConf;
                }
                $additionalRows = [];
                $changeAttributes = [];
                $mediaGallery = [];
                $updateData = [];

                //Custom get store
                if ((isset($fieldCopyValue[self::COL_STORE])) || (isset($fieldCopyValue['store_view_code']))) {
                    $storeIds[] = Store::DEFAULT_STORE_ID;
                } else {
                    $storeIds[] = Store::DEFAULT_STORE_ID;
                }

                if (!empty($fieldCopyValue)) {
                    $updateData['eav_attributes'] = $fieldCopyValue;
                    unset(
                        $updateData['eav_attributes'][SystemOptions::RELATED_PRODUCT_ATTRIBUTE],
                        $updateData['eav_attributes'][SystemOptions::UP_SELLS_PRODUCT_ATTRIBUTE],
                        $updateData['eav_attributes'][SystemOptions::CROSS_SELLS_PRODUCT_ATTRIBUTE],
                        $updateData['eav_attributes']['category_ids'],
                        $updateData['eav_attributes']['_media_image'],
                        $updateData['eav_attributes']['_media_image_label']
                    );
                }
                try {
                    $this->addLogWriteln(__('Configure variations for SKU:%1', $skuConf), $this->output, 'info');
                    if ($this->isNeedToCreateConfigurableProduct($checkSku)) {
                        try {
                            $updateData[self::COL_SKU] = $skuConf;
                            if (empty($updateData['eav_attributes'][self::COL_NAME])) {
                                $updateData['eav_attributes'][self::COL_NAME] = $skuConf;
                            }
                            $updateData['eav_attributes'][ProductInterface::STATUS] =
                                $configurableProductStatus ?? Status::STATUS_ENABLED;
                            $updateData['eav_attributes'][ProductInterface::VISIBILITY] =
                                $configurableProductVisibility ?? Visibility::VISIBILITY_BOTH;

                            $updateData['attribute_set_id']
                                = $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['attribute_set_id'];
                            $updateData['type_id'] = TypeConfigurable::TYPE_CODE;
                            $updateData['website_ids']
                                = $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['website_ids'];
                            $updateData['category_ids']
                                = $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids'] ??
                                '';
                            if (empty($updateData['eav_attributes'][self::URL_KEY])) {
                                // $storeIds[] = Store::DEFAULT_STORE_ID;
                                $updateData[self::COL_NAME] = $updateData['eav_attributes'][self::COL_NAME];
                                $updateData = $this->generateUrlKeyConfigurable($updateData, $storeIds);
                                if (
                                    !empty($this->_parameters['enable_configurable_product_url_pattern'])
                                    && $this->_parameters['enable_configurable_product_url_pattern'] == '1'
                                ) {
                                    $updateData['eav_attributes'][self::URL_KEY] = $this->getConfigurableProductUrlKey($updateData);
                                } else {
                                    $updateData['eav_attributes'][self::URL_KEY] = $updateData[self::URL_KEY];
                                }
                                unset($updateData[self::URL_KEY]);
                                unset($updateData[self::COL_NAME]);
                            }
                            $this->configVariations->saveNewProduct($updateData);
                            if (!empty($updateData['website_ids'])) {
                                foreach ($updateData['website_ids'] as $websiteId) {
                                    $this->websitesCache[$skuConf][$websiteId] = true;
                                }
                            }
                            $configurableProductsData[$skuConf] = $updateData;
                            if (!empty($updateData['eav_attributes']['visibility'])) {
                                $configurableProductsData[$skuConf]['visibility'] =
                                    $updateData['eav_attributes']['visibility'];
                            }
                            if (!empty($updateData['eav_attributes']['status'])) {
                                $configurableProductsData[$skuConf]['status'] =
                                    $updateData['eav_attributes']['status'];
                            }
                            if (!empty($updateData['eav_attributes']['url_key'])) {
                                $urlKey = $updateData['eav_attributes'][self::URL_KEY];
                                if (!empty($this->_parameters['generate_url']) && $this->_parameters['generate_url'] == '1') {
                                    if ($this->isDuplicateUrlKey($urlKey, $skuConf, $this->getStoreIds()) == true) {
                                        if (!empty($this->_parameters['enable_configurable_product_url_pattern']) && $this->_parameters['enable_configurable_product_url_pattern'] == '1') {
                                            $configurableProductsData[$skuConf]['url_key'] = $this->getConfigurableProductUrlKey($updateData);
                                            $configurableProductsData[$skuConf]['eav_attributes'][self::URL_KEY] = $configurableProductsData[$skuConf]['url_key'];
                                        } else {
                                            $configurableProductsData[$skuConf]['url_key'] = $this->productUrl->formatUrlKey($urlKey . '-' . $skuConf);
                                            $configurableProductsData[$skuConf]['eav_attributes'][self::URL_KEY] = $configurableProductsData[$skuConf]['url_key'];
                                        }
                                    } else {
                                        if (!empty($this->_parameters['enable_configurable_product_url_pattern']) && $this->_parameters['enable_configurable_product_url_pattern'] == '1') {
                                            $configurableProductsData[$skuConf]['url_key'] = $urlKey;
                                            $configurableProductsData[$skuConf]['eav_attributes'][self::URL_KEY] = $configurableProductsData[$skuConf]['url_key'];
                                        } else {
                                            $configurableProductsData[$skuConf]['url_key'] = $updateData['eav_attributes']['url_key'];
                                        }
                                    }
                                } else if (
                                    !empty($this->_parameters['enable_configurable_product_url_pattern']) &&
                                    $this->_parameters['enable_configurable_product_url_pattern'] == '1'
                                ) {
                                    $configurableProductsData[$skuConf]['url_key'] = $urlKey;
                                    $configurableProductsData[$skuConf]['eav_attributes'][self::URL_KEY] = $configurableProductsData[$skuConf]['url_key'];
                                } else {
                                    $configurableProductsData[$skuConf]['url_key'] = $urlKey;
                                    $configurableProductsData[$skuConf]['eav_attributes'][self::URL_KEY] = $configurableProductsData[$skuConf]['url_key'];
                                }
                            }
                            $updateData = $configurableProductsData[$skuConf];
                            $entityLinkField = $this->getProductEntityLinkField();
                            $this->skuProcessor->addNewSku($skuConf, $updateData);
                            $this->_oldSku[strtolower($skuConf)] = [
                                'type_id' => TypeConfigurable::TYPE_CODE,
                                'attr_set_id'
                                => $updateData['attribute_set_id'],
                                $entityLinkField => $updateData[$entityLinkField],
                                $this->getProductIdentifierField() => $updateData[$this->getProductIdentifierField()],
                                'supported_type' => true,
                            ];

                            $parentProductId = $updateData[$this->getProductEntityLinkField()];
                            foreach ($fieldCopyValue as $key => $attr) {
                                switch ($key) {
                                    case SystemOptions::RELATED_PRODUCT_ATTRIBUTE:
                                        $this->productLinkData[$parentProductId]['type'][] =
                                            ProductLink::LINK_TYPE_RELATED;
                                        break;
                                    case SystemOptions::UP_SELLS_PRODUCT_ATTRIBUTE:
                                        $this->productLinkData[$parentProductId]['type'][] =
                                            ProductLink::LINK_TYPE_UPSELL;
                                        break;
                                    case SystemOptions::CROSS_SELLS_PRODUCT_ATTRIBUTE:
                                        $this->productLinkData[$parentProductId]['type'][] =
                                            ProductLink::LINK_TYPE_CROSSSELL;
                                        break;
                                }
                            }
                        } catch (LocalizedException $e) {
                            $this->addLogWriteln($e->getMessage(), $this->output, 'error');
                        }
                    } else {
                        if ($this->isSkuExist($checkSku)) {
                            $productParent = $this->getExistingSku($checkSku);
                            if (isset($configData[$skuConf])) {
                                $firstElement = current($configData[$skuConf]);
                                $configurableProductStatus =
                                    $firstElement[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::STATUS] ?? [];
                                $configurableProductVisibility =
                                    $firstElement[ConfigurationVariations::FIELD_COPY_VALUE][ProductInterface::VISIBILITY] ?? [];
                                if (!empty($configurableProductVisibility)) {
                                    $updateData['eav_attributes'][ProductInterface::VISIBILITY] =
                                        $configurableProductVisibility;
                                }
                                if (!empty($configurableProductStatus)) {
                                    $updateData['eav_attributes'][ProductInterface::STATUS] =
                                        $configurableProductStatus;
                                }

                                $parentProductId = $productParent[$this->getProductEntityLinkField()];
                                foreach ($fieldCopyValue as $key => $attr) {
                                    switch ($key) {
                                        case SystemOptions::RELATED_PRODUCT_ATTRIBUTE:
                                            $this->productLinkData[$parentProductId]['type'][] =
                                                ProductLink::LINK_TYPE_RELATED;
                                            break;
                                        case SystemOptions::UP_SELLS_PRODUCT_ATTRIBUTE:
                                            $this->productLinkData[$parentProductId]['type'][] =
                                                ProductLink::LINK_TYPE_UPSELL;
                                            break;
                                        case SystemOptions::CROSS_SELLS_PRODUCT_ATTRIBUTE:
                                            $this->productLinkData[$parentProductId]['type'][] =
                                                ProductLink::LINK_TYPE_CROSSSELL;
                                            break;
                                    }
                                }

                                $updateData['attribute_set_id']
                                    = $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['attribute_set_id'];
                                $updateData['type_id'] = TypeConfigurable::TYPE_CODE;
                                $updateData['website_ids']
                                    = $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['website_ids'];
                                $updateData['category_ids'] =
                                    $firstElement[ConfigurationVariations::FIELD_CONF_IMPORT]['category_ids'] ?? '';
                                $updateData[$this->getProductEntityLinkField()]
                                    = $productParent[$this->getProductEntityLinkField()];
                                $updateData[$this->getProductIdentifierField()]
                                    = $productParent[$this->getProductIdentifierField()];
                                $this->configVariations->updateProduct($updateData);
                                if ($productParent['type_id'] != TypeConfigurable::TYPE_CODE) {
                                    $this->configVariations->updateTypeProductToConfigurable(
                                        (int)$updateData[$this->getProductIdentifierField()]
                                    );
                                }
                            }
                        } else {
                            $this->addLogWriteln(
                                __(
                                    'Configurable Product for sku "%1" not created before. Turn on feature to create ' .
                                        'configurable product on the fly',
                                    $skuConf
                                ),
                                $this->getOutput(),
                                'error'
                            );

                            continue;
                        }
                    }

                    if (
                        isset($this->_parameters['remove_images'])
                        && array_key_exists($skuConf, $existingImages)
                        && $this->_parameters['remove_images'] == 1
                    ) {
                        $this->removeExistingImages($existingImages[$skuConf]);
                        unset($existingImages[$skuConf]);
                    }

                    foreach ($this->_imagesArrayKeys as $fieldImage) {
                        if ($fieldImage === '_media_image') {
                            $copyValues = $firstElement[ConfigurationVariations::FIELD_COPY_VALUE];
                            if (isset($copyValues[$fieldImage])) {
                                $mediaImage = $copyValues[$fieldImage];
                                $mediaImageLabel = $copyValues['_media_image_label'] ?? '';
                                $mediaGallery[Store::DEFAULT_STORE_ID][$skuConf][] = [
                                    'attribute_id' => $this->getMediaGalleryAttributeId(),
                                    'label' => $mediaImageLabel,
                                    'position' => 1,
                                    'disabled' => '0',
                                    'value' => $mediaImage,
                                ];
                                continue;
                            }
                        } else {
                            $copyValues = $firstElement[ConfigurationVariations::FIELD_COPY_VALUE];
                            if (
                                isset($copyValues['image_label']) && !empty($copyValues['image_label']) ||
                                isset($copyValues['small_image_label']) && !empty($copyValues['small_image_label']) ||
                                isset($copyValues['thumbnail_label']) && !empty($copyValues['thumbnail_label'])
                            ) {
                                $image = $updateData['eav_attributes']['image'];
                                $smallImage = $updateData['eav_attributes']['small_image'];
                                $thumbImage = $updateData['eav_attributes']['thumbnail'];
                                if (($image == $smallImage) && ($smallImage == $thumbImage)) {
                                    $valueImage = $image;
                                    $mediaImageLabel = $copyValues['image_label'];
                                } else {
                                    $valueImage = $updateData['eav_attributes']['thumbnail'];
                                    $mediaImageLabel = $copyValues['thumbnail_label'];
                                }
                                $mediaGallery[Store::DEFAULT_STORE_ID][$skuConf][] = [
                                    'attribute_id' => $this->getMediaGalleryAttributeId(),
                                    'label' => $mediaImageLabel,
                                    'position' => 1,
                                    'disabled' => '0',
                                    'value' => $valueImage
                                ];
                                continue;
                            }
                        }
                        if ($fieldImage === ProductVideo::VIDEO_URL_COLUMN) {
                            continue;
                        }
                        if (empty($updateData['eav_attributes'][$fieldImage])) {
                            continue;
                        }
                        $attributeChange = $this->retrieveAttributeByCode($fieldImage);
                        $attrId = $attributeChange->getId();
                        $attrTable = $attributeChange->getBackend()->getTable();
                        $attrValue = $updateData['eav_attributes'][$fieldImage];
                        $urlAttribute = $this->retrieveAttributeByCode(self::URL_KEY);
                        $urlAttrTable = $urlAttribute->getBackend()->getTable();
                        $urlAttrId = $urlAttribute->getId();
                        if (!isset($changeAttributes[$attrTable][$checkSku][$attrId][0]) && !empty($attrValue)) {
                            $changeAttributes[$attrTable][$skuConf][$attrId][0] = $attrValue;

                            if (
                                isset($changeAttributes[$urlAttrTable]) &&
                                isset($changeAttributes[$urlAttrTable][$skuConf]) &&
                                isset($changeAttributes[$urlAttrTable][$skuConf][$urlAttrId]) &&
                                isset($changeAttributes[$urlAttrTable][$skuConf][$urlAttrId][0])
                            ) {
                                if ($urlKey != $configurableProductsData[$skuConf][self::URL_KEY]) {
                                    $changeAttributes[$urlAttrTable][$skuConf][$urlAttrId][0] = $configurableProductsData[$skuConf][self::URL_KEY];
                                } else {
                                    $changeAttributes[$urlAttrTable][$skuConf][$urlAttrId][0] = $urlKey;
                                }
                            }

                            if (
                                version_compare($this->productMetadata->getVersion(), '2.2.4', '>=') ||
                                strpos($this->getProductMetadata()->getVersion(), '1.0.0') !== false
                            ) {
                                if (isset($mediaGallery[Store::DEFAULT_STORE_ID][$skuConf])) {
                                    $existingValues = [];

                                    foreach ($mediaGallery[Store::DEFAULT_STORE_ID][$skuConf] as $index => $item) {
                                        $existingValues[$item['value']] = $index;
                                    }
                                    if (!isset($existingValues[$attrValue])) {
                                        $mediaGallery[Store::DEFAULT_STORE_ID][$skuConf][] = [
                                            'attribute_id' => $this->getMediaGalleryAttributeId(),
                                            'label' => '',
                                            'position' => 1,
                                            'disabled' => '0',
                                            'value' => $attrValue,
                                        ];
                                    } else {
                                        $firstIndex = $existingValues[$attrValue];
                                        $mediaGallery[Store::DEFAULT_STORE_ID][$skuConf] = [$mediaGallery[Store::DEFAULT_STORE_ID][$skuConf][$firstIndex]];
                                    }
                                } else {
                                    $mediaGallery[Store::DEFAULT_STORE_ID][$skuConf] = [
                                        [
                                            'attribute_id' => $this->getMediaGalleryAttributeId(),
                                            'label' => '',
                                            'position' => 1,
                                            'disabled' => '0',
                                            'value' => $attrValue,
                                        ]
                                    ];
                                }
                            } else {
                                $mediaGallery[$skuConf][] = [
                                    'attribute_id' => $this->getMediaGalleryAttributeId(),
                                    'label' => '',
                                    'position' => 1,
                                    'disabled' => '0',
                                    'value' => $attrValue,
                                ];
                            }
                        }
                    }

                    $vars = [];
                    $attributes = [];
                    $visAttribute = $this->retrieveAttributeByCode(ProductInterface::VISIBILITY);
                    $statusAttribute = $this->retrieveAttributeByCode(ProductInterface::STATUS);
                    $visAttrTable = $visAttribute->getBackend()->getTable();
                    $visAttrId = $visAttribute->getId();
                    $statusAttrTable = $statusAttribute->getBackend()->getTable();
                    $statusAttrId = $statusAttribute->getId();
                    foreach ($elements as $element) {
                        $position = 0;
                        foreach ($element as $attributeCode => $field) {
                            if ($attributeCode != ProductInterface::SKU && !empty($field)) {
                                if (!in_array($attributeCode, $attributes)) {
                                    $attributes[] = $attributeCode;
                                }
                                $vars['fields'][] = [
                                    'code' => $attributeCode,
                                    'value' => $field,
                                ];
                            } else {
                                if ($attributeCode == ProductInterface::SKU) {
                                    foreach ($storeIds as $storeId) {
                                        if (empty($changeAttributes[$visAttrTable][$field][$visAttrId][$storeId])) {
                                            $changeAttributes[$visAttrTable][$field][$visAttrId][$storeId] =
                                                $element[ProductInterface::VISIBILITY] ?? 1;
                                        }
                                        if (empty($changeAttributes[$statusAttrTable][$field][$statusAttrId][$storeId])) {
                                            $changeAttributes[$statusAttrTable][$field][$statusAttrId][$storeId] =
                                                $element[ProductInterface::STATUS] ?? 1;
                                        }
                                    }
                                }
                                $vars[$attributeCode] = $field;
                            }
                        }
                        $vars['position'] = $position;
                        $position++;
                        $additionalRows[] = $vars;
                    }
                    $attributeValues = [];
                    $ids = [];
                    $configurableAttributesData = [];
                    $position = 0;
                    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute|null $attributeConf */
                    $attributeConf = null;
                    foreach ($attributes as $attribute) {
                        foreach ($additionalRows as $list) {
                            $attributeConf = $this->retrieveAttributeByCode($attribute);
                            $value = [];
                            if (isset($list['fields'])) {
                                foreach ($list['fields'] as $item) {
                                    if ($item['code'] == $attribute) {
                                        $value = $item['value'];
                                        $existingSku = $this->getExistingSku(isset($list['sku']) ? $list['sku'] : '');
                                        $simpleProductId = is_array($existingSku)
                                            ? $existingSku[$this->getProductIdentifierField()] : false;
                                        if (!in_array($simpleProductId, $ids)) {
                                            $ids[] = $simpleProductId;
                                        }
                                    }
                                }
                            }
                            if (!empty($attributeConf)) {
                                $attributeValues[$attribute][] = [
                                    'label' => $attribute,
                                    'attribute_id' => $attributeConf->getId(),
                                    'value_index' => $value,
                                ];
                            }
                        }
                        if (!empty($attributeConf)) {
                            $configurableVariation = $this->_parameters['configurable_variations'] ?? [];
                            if (
                                !empty($configurableVariation) &&
                                in_array(
                                    strtolower($attributeConf->getAttributeCode()),
                                    array_map('strtolower', $configurableVariation)
                                )
                            ) {
                                $configurableAttributesData[] =
                                    [
                                        'attribute_id' => $attributeConf->getId(),
                                        'code' => $attributeConf->getAttributeCode(),
                                        'label' => $attributeConf->getStoreLabel(),
                                        'position' => $position++,
                                        'values' => $attributeValues[$attribute],
                                    ];
                            }
                        }
                    }

                    if (!empty($mediaGallery)) {
                        //Check if exists coppy first media gallery from simple product
                        if (!empty($this->cacheMediaGallery)) {
                            foreach ($mediaGallery as $itemKey => $itemMedia) {
                                foreach ($itemMedia as $keyMedia => $valuesMedia) {
                                    if (isset($this->cacheMediaGallery[0][$keyMedia])) {
                                        $mergedValues = [];
                                        $existingValues = [];

                                        foreach ($valuesMedia as $valueMedia) {
                                            $existingValues[] = $valueMedia['value'];
                                        }

                                        foreach ($this->cacheMediaGallery[0][$keyMedia] as $valueMediaGalleryCache) {
                                            if (isset($valueMediaGalleryCache['value'])) {
                                                if (!in_array($valueMediaGalleryCache['value'], $existingValues)) {
                                                    $mergedValues[] = $valueMediaGalleryCache;
                                                }
                                            }
                                        }

                                        $mediaGallery[$itemKey][$keyMedia] = array_merge($valuesMedia, $mergedValues);
                                    }
                                }
                            }
                        }

                        $productLinkIdField = $this->getProductEntityLinkField();
                        foreach ($mediaGallery as $storeId => $storeMediaGalleryData) {
                            foreach ($storeMediaGalleryData as $sku => $productMediaGalleryData) {
                                $productId = $this->skuProcessor->getNewSku($sku)[$productLinkIdField];
                                $productMediaGalleryValueData[$productId] = $productMediaGalleryValueData[$productId] ?? [];
                                foreach ($productMediaGalleryData as $data) {
                                    if (
                                        isset($data['value']) &&
                                        !empty($data['value']) &&
                                        !is_array($data['value']) &&
                                        !is_int($data['value'])
                                    ) {
                                        if (!in_array($data['value'], $productMediaGalleryValueData[$productId])) {
                                            $productMediaGalleryValueData[$productId][] = $data['value'];
                                            $mediaGalleryValueData[] = [
                                                'attribute_id' => $data['attribute_id'],
                                                'value' => $data['value'],
                                            ];
                                            $mediaGalleryValueToEntityData[] = [
                                                'value' => $data['value'],
                                                $productLinkIdField => $productId,
                                            ];
                                        }
                                        $mediaGalleryValues[] = $data['value'];
                                        $mediaGalleryValueToStoreData[] = [
                                            'value' => $data['value'],
                                            'store_id' => $storeId,
                                            $productLinkIdField => $productId,
                                            'label' => $data['label'],
                                            'position' => $data['position'],
                                            'disabled' => $data['disabled'],
                                        ];
                                    } else {
                                        // Update additional_image
                                        if (isset($data['value_id'])) {
                                            $mediaGalleryValueForProduct[] = [
                                                'value_id' => $data['value_id'],

                                            ];
                                            $oldMediaGallerys = $this->resourceConnection->getConnection()->fetchAll(
                                                $this->resourceConnection->getConnection()->select()
                                                    ->from($this->getResource()->getTable('catalog_product_entity_media_gallery_value'))
                                                    ->where('value_id = ?', $data['value_id'])
                                            );
                                            if (!empty($oldMediaGallerys)) {
                                                if (
                                                    isset($data['label']) &&
                                                    isset($data['position']) &&
                                                    isset($data['disabled']) &&
                                                    isset($data['row_id']) &&
                                                    isset($data['store_id'])
                                                ) {
                                                    $this->resourceConnection->getConnection()->update(
                                                        $this->getResource()->getTable('catalog_product_entity_media_gallery_value'),
                                                        [
                                                            'label' => $data['label'],
                                                            'position' => $data['position'],
                                                            'disabled' => $data['disabled']
                                                        ],
                                                        [
                                                            'row_id = ?' => $data['row_id'],
                                                            'value_id = ?' => $data['value_id'],
                                                            'store_id = ?' => $data['store_id']
                                                        ]
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (
                            !empty($mediaGalleryValues) &&
                            !empty($mediaGalleryValueData) &&
                            !empty($mediaGalleryValueToEntityData) &&
                            !empty($mediaGalleryValueToStoreData) &&
                            !empty($productMediaGalleryValueData)
                        ) {
                            if (
                                isset($data['value']) &&
                                !empty($data['value']) &&
                                !is_array($data['value']) &&
                                !is_int($data['value'])
                            ) {
                                try {
                                    $mediaValueIdValueMap = [];
                                    $connection = $this->resourceConnection->getConnection();
                                    $oldMediaValues = $connection->fetchCol(
                                        $connection->select()
                                            ->from($this->getResource()->getTable('catalog_product_entity_media_gallery'), ['value_id'])
                                            ->where('value IN (?)', $mediaGalleryValues)
                                    );

                                    if ($mediaGalleryValueData) {
                                        $mediaGalleryTable = $this->getResource()->getTable('catalog_product_entity_media_gallery');
                                        foreach ($mediaGalleryValueData as $key => $mediaGalleryValData) {
                                            if (isset($mediaGalleryValData['value'])) {
                                                $sql = "INSERT IGNORE INTO " . $mediaGalleryTable . " (attribute_id, value) VALUES ('" . $mediaGalleryValData['attribute_id'] . "', '" . $mediaGalleryValData['value'] . "')";
                                                $connection->query($sql);
                                            }
                                        }
                                    }

                                    $newMediaSelect = $connection->select()
                                        ->from($this->getResource()->getTable('catalog_product_entity_media_gallery'), ['value_id', 'value'])
                                        ->where('value IN (?)', $mediaGalleryValues);
                                    if ($oldMediaValues) {
                                        $newMediaSelect->where('value_id NOT IN (?)', $oldMediaValues);
                                    }

                                    $mediaValueIdValueMap = $connection->fetchPairs($newMediaSelect);
                                    $productIdMediaValueIdMap = $this->getProductIdMediaValueIdMapIsobar(
                                        $productMediaGalleryValueData,
                                        $mediaValueIdValueMap
                                    );
                                    $mediaGalleryValueToEntityData = $this->prepareMediaGalleryValueToEntityDataIsobar(
                                        $mediaGalleryValueToEntityData,
                                        $productIdMediaValueIdMap
                                    );

                                    if (!empty($mediaGalleryValueToEntityData)) {
                                        $mediaGalleryEntityTable = $this->getResource()->getTable(
                                            'catalog_product_entity_media_gallery_value_to_entity'
                                        );
                                        foreach ($mediaGalleryValueToEntityData as $key => $mediaGalleryValToEntityData) {
                                            if (isset($mediaGalleryValToEntityData['value_id']) && is_int($mediaGalleryValToEntityData['value_id'])) {
                                                $sql = "INSERT IGNORE INTO " . $mediaGalleryEntityTable . " (value_id, row_id) VALUES ('" . $mediaGalleryValToEntityData['value_id'] . "', '" . $mediaGalleryValToEntityData['row_id'] . "')";
                                                $connection->query($sql);
                                            }
                                        }
                                    }

                                    $mediaGalleryValueToStoreData = $this->prepareMediaGalleryValueDataIsobar(
                                        $mediaGalleryValueToStoreData,
                                        $productIdMediaValueIdMap
                                    );

                                    if (!empty($mediaGalleryValueToStoreData)) {
                                        $mediaGalleryValueTable = $this->getResource()->getTable(
                                            'catalog_product_entity_media_gallery_value'
                                        );
                                        foreach ($mediaGalleryValueToStoreData as $key => $mediaGalleryValToStoreData) {
                                            if (isset($mediaGalleryValToStoreData['value_id']) && is_int($mediaGalleryValToStoreData['value_id'])) {

                                                if (!empty($mediaGalleryValToStoreData['label'])) {
                                                    $label = addslashes($mediaGalleryValToStoreData['label']);
                                                } else {
                                                    $label = $mediaGalleryValToStoreData['label'];
                                                }

                                                $sql = "INSERT INTO " . $mediaGalleryValueTable . " (value_id, store_id, label, position, disabled, row_id) VALUES ('" . $mediaGalleryValToStoreData['value_id'] . "', '" . $mediaGalleryValToStoreData['store_id'] . "', '" . $label . "', '" . $mediaGalleryValToStoreData['position'] . "', '" . $mediaGalleryValToStoreData['disabled'] . "', '" . $mediaGalleryValToStoreData['row_id'] . "')";
                                                $connection->query($sql);
                                            }
                                        }
                                    }
                                } catch (\Throwable $exception) {
                                    if ($mediaValueIdValueMap) {
                                        $connection = $this->resourceConnection->getConnection();
                                        $connection->delete(
                                            $this->getResource()->getTable(
                                                'catalog_product_entity_media_gallery_value'
                                            ),
                                            $connection->quoteInto('value_id IN (?)', array_keys($mediaValueIdValueMap))
                                        );
                                    }
                                    throw $exception;
                                }
                            }
                        }
                    }
                    if (!empty($changeAttributes)) {
                        $this->_saveProductAttributes($changeAttributes);
                    }
                    if (!empty($updateData[$this->getProductEntityLinkField()])) {
                        $this->saveCollectData(
                            $updateData[$this->getProductEntityLinkField()],
                            $configurableAttributesData,
                            $ids
                        );
                        if (!empty($updateData['entity_id'])) {
                            $stockData = [];
                            $defaultScopeConfig = $this->stockConfiguration->getDefaultScopeId();
                            $stockData[$skuConf] = [
                                'is_in_stock' => 1,
                                'product_id' => $updateData['entity_id'],
                                'website_id' => $defaultScopeConfig,
                                'stock_id' => $this->stockRegistry->getStock($defaultScopeConfig)->getStockId(),
                            ];
                            try {
                                $this->stockItemImporter->import($stockData);
                            } catch (Exception $exception) {
                                $this->addLogWriteln($exception->getMessage(), $this->getOutput(), 'info');
                                $this->getLogger()->debug($exception);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->getErrorAggregator()->addError(
                        $e->getCode(),
                        ProcessingError::ERROR_LEVEL_NOT_CRITICAL,
                        null,
                        null,
                        $e->getMessage()
                    );
                }
            }
            $this->_eventManager->dispatch(
                'catalog_product_import_bunch_save_after',
                ['adapter' => $this, 'bunch' => $configurableProductsData]
            );
        }

        return $this;
    }

    /**
     * @return $this|MagentoProduct
     * @throws LocalizedException
     * @throws Exception
     */
    protected function _saveValidatedBunches()
    {
        $_currentRowSkus = [];
        $source = $this->_getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();
        $skuSet = [];
        $file = null;
        $jobId = null;
        if (isset($this->_parameters['file'])) {
            $file = $this->_parameters['file'];
            $this->_dataSourceModel->setFile($file);
        }
        if (isset($this->_parameters['job_id'])) {
            $jobId = $this->_parameters['job_id'];
            $this->_dataSourceModel->setJobId($jobId);
        }
        $this->cache->clean(ImportProductCache::ROW_SKUS_CACHE_ID . $jobId);
        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                if (!empty($this->_parameters['use_only_fields_from_mapping'])) {
                    $this->useOnlyFieldsFromMapping($bunchRows, true);
                }
                if (!empty($this->_parameters['generate_url'])) {
                    $bunchRows = $this->findUrlKeyDuplicates($bunchRows);
                }
                $this->addLogWriteln(__('Saving Validated Bunches'), $this->output, 'info');

                $this->_dataSourceModel->saveBunches(
                    $this->getEntityTypeCode(),
                    $this->getBehavior(),
                    $jobId,
                    $file,
                    $bunchRows
                );
                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen($this->getSerializer()->serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                $rowData = $source->current();
                $colStoreViewCode = $rowData[self::COL_STORE_VIEW_CODE] ?? '';
                $storeViews = explode($this->getMultipleValueSeparator(), $colStoreViewCode);
                foreach ($storeViews as $storeView) {
                    $rowKey = count($bunchRows);
                    $rowData[self::COL_STORE_VIEW_CODE] = $storeView;
                    try {
                        $this->currentSku = $rowData[self::COL_SKU];
                        $isCached = $this->_parameters['cache_products'] ?? false;
                        if ($isCached) {
                            $currentRowHash = sha1(trim(implode('', $rowData)));
                            $cache = $this->cache->load($currentRowHash);
                            if ($cache) {
                                $_currentRowSkus[] = mb_strtolower($rowData[self::COL_SKU]);
                                $this->addLogWriteln(
                                    __('Product %1 has not changed', $rowData['sku']),
                                    $this->output,
                                    'info'
                                );
                                $source->next();
                                continue 2;
                            }
                            $this->originalImportRows[strtolower($rowData['sku'])][] = $currentRowHash;
                        }
                        if (array_key_exists('sku', $rowData)) {
                            $skuSet[$rowData['sku']] = true;
                        }
                        $rowData = $this->getBundleSpecialAttributeMap($rowData);
                        $invalidAttr = [];
                        foreach ($rowData as $attrName => $element) {
                            if (is_string($element)) {
                                if (!mb_check_encoding($element, 'UTF-8')) {
                                    unset($rowData[$attrName]);
                                    $invalidAttr[] = $attrName;
                                }
                            }
                        }
                        if (!empty($invalidAttr)) {
                            $this->addRowError(
                                AbstractEntity::ERROR_CODE_ILLEGAL_CHARACTERS,
                                $this->_processedRowsCount,
                                implode(',', $invalidAttr)
                            );
                        }
                    } catch (InvalidArgumentException $e) {
                        $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                        $this->_processedRowsCount++;
                        $source->next();
                        continue;
                    }
                    $rowData = $this->helper->trimArrayValues($rowData);
                    if (isset($rowData['configurable_variations']) && $rowData['configurable_variations']) {
                        $this->checkAttributePresenceInAttributeSet($rowData);
                    }
                    $rowData[self::COL_SKU] = $this->getCorrectSkuAsPerLength($rowData);
                    $_currentRowSkus[] = mb_strtolower($rowData[self::COL_SKU]);
                    $rowData = $this->customFieldsMapping($rowData);
                    $rowData = $this->customBunchesData($rowData);

                    $this->_processedRowsCount++;

                    $productSku = strtolower($this->getCorrectSkuAsPerLength($rowData));
                    $oldSkus = $this->skuProcessor->getOldSkus();
                    if ($this->onlyUpdate || $this->onlyAdd) {
                        if (!isset($oldSkus[$productSku]) && $this->onlyUpdate) {
                            $source->next();
                            continue 2;
                        } elseif (isset($oldSkus[$productSku]) && $this->onlyAdd) {
                            $source->next();
                            continue 2;
                        }
                    }

                    if ($this->onlyUpdate && empty($this->_parameters['clear_attribute_value'])) {
                        foreach ($rowData as $key => $value) {
                            if ('' === $value) {
                                unset($rowData[$key]);
                            }
                        }
                    }

                    if ($this->getBehavior() == Import::BEHAVIOR_REPLACE) {
                        if (isset($rowData['attribute_set_code'])) {
                            $rowData['_attribute_set'] = $rowData['attribute_set_code'];
                        }
                    }

                    /* specify url_key for to avoid product repository load
                    in \Magento\CatalogUrlRewrite\Observer\AfterImportDataObserver*/
                    if ((empty($rowData[self::URL_KEY]) && empty($oldSkus[$productSku])) ||
                        !empty($this->_parameters['enable_product_url_pattern'])
                    ) {
                        $rowData[self::URL_KEY] = $this->onlyUpdate ? '' : $this->getProductUrlKey($rowData);
                    }
                    if (empty($oldSkus[$productSku]) || !empty($rowData[self::URL_KEY])) {
                        $rowData = $this->processUrlKey($rowData);
                    }
                    if ($this->validateRow($rowData, $source->key())) {
                        // add row to bunch for save
                        $rowData = $this->_prepareRowForDb($rowData);
                        $rowSize = strlen($this->getSerializer()->serialize($rowData));

                        $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                        if (isset($rowData[self::COL_TYPE]) && !$this->validateRowByProductType($rowData, $rowKey)) {
                            $this->addRowError(ValidatorInterface::ERROR_TYPE_UNSUPPORTED, $rowKey);
                        }

                        if (($rowData['sku'] !== $this->getLastSku()) &&
                            ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded)
                        ) {
                            $startNewBunch = true;
                            $nextRowBackup = [$rowKey => $rowData];
                        } else {
                            $rowData['origin_row_number'] = $source->key();
                            $bunchRows[$rowKey] = $rowData;
                            $currentDataSize += $rowSize;
                        }
                        $this->setLastSku($rowData['sku']);
                    }
                }

                $source->next();
            }
        }
        if (
            !empty($this->_parameters['configurable_switch']) &&
            empty($this->_parameters['configurable_create']) &&
            !empty($_currentRowSkus)
        ) {
            $this->cache->save(
                $this->getSerializer()->serialize($_currentRowSkus),
                ImportProductCache::ROW_SKUS_CACHE_ID . $jobId
            );
        }
        $this->updateExistingProductNotInImportList($_currentRowSkus);
        $this->getOptionEntity()->validateAmbiguousData();
        $this->_processedEntitiesCount = (count($skuSet)) ?: $this->_processedRowsCount;

        $this->cache->clean([ImportProductCache::BUFF_CACHE]);
        $this->cache->save(
            $this->getSerializer()->serialize($this->originalImportRows),
            sha1(ImportProductCache::BUFF_CACHE)
        );

        return $this;
    }

    /**
     * @param array $rowData
     * @param null $storeIds
     * @return array
     * @throws Exception
     */
    protected function generateUrlKeyConfigurable(array $rowData, $storeIds = null)
    {
        $productEntityLinkField = $this->getProductEntityLinkField();
        $sku = $this->getCorrectSkuAsPerLength($rowData);
        $urlKey = $rowData[self::URL_KEY] ?? '';
        $name = $rowData[self::COL_NAME] ?? '';
        if ($this->isSkuExist($sku)) {
            $exiting = $this->getExistingSku($sku);
            if (!$urlKey) {
                $attr = $this->retrieveAttributeByCode(self::URL_KEY);
                $select = $this->getConnection()->select()
                    ->from($attr->getBackendTable(), ['value'])
                    ->where($productEntityLinkField . ' = (?)', $exiting['entity_id'])
                    ->where('attribute_id = (?)', $attr->getAttributeId());
                $urlKey = $this->getConnection()->fetchOne($select);
            }
            if (!$name) {
                $attr = $this->retrieveAttributeByCode(self::COL_NAME);
                $select = $this->getConnection()->select()
                    ->from($attr->getBackendTable(), ['value'])
                    ->where($productEntityLinkField . ' = (?)', $exiting['entity_id'])
                    ->where('attribute_id = (?)', $attr->getAttributeId());
                $name = $this->getConnection()->fetchOne($select);
                if (!$urlKey) {
                    $urlKey = $name;
                }
            }
        } else {
            $urlKey = isset($rowData[self::URL_KEY])
                ? $urlKey
                : $name;
        }
        if ($storeIds === null) {
            $storeIds = $this->getStoreIds();
        }
        $urlKey = ($urlKey != '') ?
            $this->productUrl->formatUrlKey($urlKey)
            : $this->productUrl->formatUrlKey($name);
        $isDuplicate = $this->isDuplicateUrlKey($urlKey, $sku, $storeIds);
        $storeId = $this->getRowStoreId($rowData);
        if (
            isset($this->_parameters['generate_url']) &&
            $this->_parameters['generate_url'] == 1
        ) {
            if ($isDuplicate || $this->urlKeyManager->isUrlKeyExist($sku, $urlKey, $storeId)) {
                $urlKey = $this->productUrl->formatUrlKey(
                    $name . '-' . $sku
                );
            }
        }
        $rowData[self::URL_KEY] = $urlKey;
        $this->urlKeyManager->addUrlKeys($sku, $urlKey, $storeId);
        return $rowData;
    }

    /**
     * @param array $rowData
     * @return string
     * @throws Exception
     */
    protected function getConfigurableProductUrlKey($rowData)
    {
        if (
            isset($this->_parameters['enable_configurable_product_url_pattern']) &&
            $this->_parameters['enable_configurable_product_url_pattern'] === '1' &&
            isset($this->_parameters['configurable_product_url_pattern']) &&
            !empty($this->_parameters['configurable_product_url_pattern']) &&
            $this->validateConfigurableProductUrlPattern($this->_parameters['configurable_product_url_pattern'])
        ) {
            return $this->generateUrlKeyByPatternforConfigurable($rowData);
        } else {
            return $this->getUrlKey($rowData);
        }
    }

    /**
     * @return array
     */
    protected function getConfigurableProductUrlPatternVariables()
    {
        $this->prepareUrlGeneratePatternForConfigurable();
        $result = [];
        foreach ($this->urlPatternData['fields'] as $key => $patternField) {
            $result[] = "[$patternField]";
        }
        foreach ($this->urlPatternData['functions_with_parameters'] as $function => $parameters) {
            $result[] = "[$function($parameters)]";
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function prepareUrlGeneratePatternForConfigurable()
    {
        if ($this->urlPatternData['cache'] == 0) {
            $pattern = $this->_parameters['configurable_product_url_pattern'];
            preg_match_all("/(?<=[[])[^]]+/", $pattern, $out, PREG_SET_ORDER);
            $functionsWithParameters = [];
            $fields = [];
            foreach ($out as $key => $value) {
                $pos = strripos($value[0], '(');
                if ($pos === false) {
                    $fields[] = $value[0];
                } else {
                    preg_match_all("/\((.+?|())\)/", $value[0], $functionParameters, PREG_SET_ORDER);
                    if (isset($functionParameters[0][1])) {
                        $str = strpos($value[0], "(");
                        $functionName = substr($value[0], 0, $str);
                        if (function_exists($functionName)) {
                            if (in_array($functionName, $this->urlPatternData['allowed_functions'])) {
                                if (!empty($functionParameters[0][1])) {
                                    $functionsWithParameters[$functionName] = $functionParameters[0][1];
                                } else {
                                    $functionsWithParameters[$functionName] = '';
                                }
                            } else {
                                $this->addLogWriteln(
                                    __(
                                        'Product Url Pattern can contain php functions: "%1" ',
                                        implode(", ", $this->urlPatternData['allowed_functions'])
                                    ),
                                    $this->getOutput(),
                                    'error'
                                );
                            }
                        }
                    }
                }
            }
            $this->urlPatternData = [
                'allowed_functions' => $this->urlPatternData['allowed_functions'],
                'fields' => $fields,
                'functions_with_parameters' => $functionsWithParameters,
                'cache' => 1
            ];

            return $this->urlPatternData;
        }
    }

    /**
     * @param string $productUrlPattern
     * @return bool
     */
    protected function validateConfigurableProductUrlPattern($productUrlPattern)
    {
        foreach ($this->getConfigurableProductUrlPatternVariables() as $variable) {
            if (strpos($productUrlPattern, $variable) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $rowData
     * @param $storeIds
     * @return string
     * @throws Exception
     */
    protected function generateUrlKeyByPatternforConfigurable($rowData, $storeIds = null)
    {
        $productEntityLinkField = $this->getProductEntityLinkField();
        $sku = $this->getCorrectSkuAsPerLength($rowData);
        $name = $rowData[self::COL_NAME] ?? '';
        if ($this->isSkuExist($sku)) {
            $exiting = $this->getExistingSku($sku);
            if (!$name) {
                $attr = $this->retrieveAttributeByCode(self::COL_NAME);
                $select = $this->getConnection()->select()
                    ->from($attr->getBackendTable(), ['value'])
                    ->where($productEntityLinkField . ' = (?)', $exiting['entity_id'])
                    ->where('attribute_id = (?)', $attr->getAttributeId());
                $name = $this->getConnection()->fetchOne($select);
            }
        }
        if ($storeIds === null) {
            $storeIds = $this->getStoreIds();
        }
        $replacement = $this->replacementInPatternValue($name, $sku, $rowData);
        $urlKey = str_replace(
            $this->getConfigurableProductUrlPatternVariables(),
            $replacement,
            $this->_parameters['configurable_product_url_pattern']
        );
        $urlKey = $this->productUrl->formatUrlKey($urlKey);
        $isDuplicate = $this->isDuplicateUrlKey($urlKey, $sku, $storeIds);
        $storeId = $this->getRowStoreId($rowData);
        if ($isDuplicate && $this->urlKeyManager->isUrlKeyExist($sku, $urlKey, $storeId)) {
            $urlKey = $this->productUrl->formatUrlKey(
                $name . '-' . $sku
            );
        }
        $rowData[self::URL_KEY] = $urlKey;
        $this->urlKeyManager->addUrlKeys($sku, $urlKey, $storeId);
        return $urlKey;
    }

    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/import_product.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    /**
     * Get media values IDs per products IDs
     *
     * @param array $productMediaGalleryValueData
     * @param array $mediaValueIdValueMap
     * @return array
     */
    private function getProductIdMediaValueIdMapIsobar(
        array $productMediaGalleryValueData,
        array $mediaValueIdValueMap
    ): array {
        $productIdMediaValueIdMap = [];
        foreach ($productMediaGalleryValueData as $productId => $productMediaGalleryValues) {
            foreach ($productMediaGalleryValues as $productMediaGalleryValue) {
                foreach ($mediaValueIdValueMap as $valueId => $value) {
                    if ($productMediaGalleryValue === $value) {
                        $productIdMediaValueIdMap[$productId][$value] = $valueId;
                        unset($mediaValueIdValueMap[$valueId]);
                        break;
                    }
                }
            }
        }
        return $productIdMediaValueIdMap;
    }

    /**
     * Prepare media entity gallery value to entity data for insert
     *
     * @param array $mediaGalleryValueToEntityData
     * @param array $productIdMediaValueIdMap
     * @return array
     */
    public function prepareMediaGalleryValueToEntityDataIsobar(
        array $mediaGalleryValueToEntityData,
        array $productIdMediaValueIdMap
    ): array {
        $productLinkIdField = $this->getProductEntityLinkField();
        foreach ($mediaGalleryValueToEntityData as $index => $data) {
            $productId = $data[$productLinkIdField];
            if (isset($data['value'])) {
                $value = $data['value'];
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (isset($productIdMediaValueIdMap[$productId][$val])) {
                            $mediaGalleryValueToEntityData[$index]['value_id'] = $productIdMediaValueIdMap[$productId][$val];
                            unset($mediaGalleryValueToEntityData[$index]['value']);
                        }
                    }
                } else {
                    if (isset($productIdMediaValueIdMap[$productId][$value])) {
                        $mediaGalleryValueToEntityData[$index]['value_id'] = $productIdMediaValueIdMap[$productId][$value];
                        unset($mediaGalleryValueToEntityData[$index]['value']);
                    }
                }
                if (isset($mediaGalleryValueToEntityData[$index]['value'])) {
                    unset($mediaGalleryValueToEntityData[$index]['value']);
                }
            }
        }
        return $mediaGalleryValueToEntityData;
    }

    /**
     * Prepare media entity gallery value data for insert
     *
     * @param array $mediaGalleryValueData
     * @param array $productIdMediaValueIdMap
     * @return array
     */
    public function prepareMediaGalleryValueDataIsobar(
        array $mediaGalleryValueData,
        array $productIdMediaValueIdMap
    ): array {
        $productLinkIdField = $this->getProductEntityLinkField();
        $lastPositions = $this->getLastMediaPositionPerProduct(array_keys($productIdMediaValueIdMap));
        foreach ($mediaGalleryValueData as $index => $data) {
            $productId = $data[$productLinkIdField];
            if (isset($data['value'])) {
                $value = $data['value'];
                $position = $data['position'];
                $storeId = $data['store_id'];
                $mediaGalleryValueData[$index]['value_id'] = $productIdMediaValueIdMap[$productId][$value];
                $lastPosition = $lastPositions[$storeId][$productId]
                    ?? $lastPositions[Store::DEFAULT_STORE_ID][$productId]
                    ?? 0;
                $mediaGalleryValueData[$index]['position'] = $position + $lastPosition;
                unset($mediaGalleryValueData[$index]['value']);
            }
        }
        return $mediaGalleryValueData;
    }

    /**
     * Get the last media position for each product per store from the given list
     *
     * @param array $productIds
     * @return array
     */
    public function getLastMediaPositionPerProduct(array $productIds): array
    {
        $result = [];
        if ($productIds) {
            $productKeyName = $this->getProductEntityLinkField();
            // this result could be achieved by using GROUP BY. But there is no index on position column, therefore
            // it can be slower than the implementation below
            $positions = $this->resourceConnection->getConnection()->fetchAll(
                $this->resourceConnection->getConnection()
                    ->select()
                    ->from($this->getResource()->getTable(
                        'catalog_product_entity_media_gallery_value'
                    ), [$productKeyName, 'store_id', 'position'])
                    ->where("$productKeyName IN (?)", $productIds)
            );
            // Find the largest position for each product
            foreach ($positions as $record) {
                $productId = $record[$productKeyName];
                $storeId = $record['store_id'];
                if (!isset($result[$storeId][$productId])) {
                    $result[$storeId][$productId] = 0;
                }
                $result[$storeId][$productId] = $result[$storeId][$productId] < $record['position']
                    ? $record['position']
                    : $result[$storeId][$productId];
            }
        }

        return $result;
    }

    public function _saveMediaGallerySimple()
    {

        if (empty($this->mediaGallerySimple) || !$this->mediaGallerySimple) {
            return;
        } else {
            if (!empty($this->mediaGallerySimple)) {
                $mediaGallery = $this->mediaGallerySimple;
                $mediaGalleryDataGlobal = array_replace_recursive(...$mediaGallery);
                if (!empty($mediaGalleryDataGlobal)) {
                    if (empty($mediaGallery[Store::DEFAULT_STORE_ID])) {
                        $mediaGallery[Store::DEFAULT_STORE_ID] = $mediaGalleryDataGlobal;
                    }
                    $connection = $this->resourceConnection->getConnection();
                    $mediaInputArray = [];
                    foreach ($mediaGalleryDataGlobal as $productSku => $mediaGalleryRows) {
                        $productId = $this->skuProcessor->getNewSku($productSku)[$this->getProductEntityLinkField()] ?? '';
                        foreach ($mediaGalleryRows as $mediaGalleryRow) {
                            if (isset($mediaGalleryRow['value'])) {
                                $mediaGalleryRow['row_id'] = $productId;
                                $mediaGalleryData[] = [
                                    'attribute_id' => $mediaGalleryRow['attribute_id'],
                                    'value' => $mediaGalleryRow['value'],
                                    'disabled' => $mediaGalleryRow['disabled']
                                ];
                                if (!empty($mediaGalleryData)) {
                                    $connection->insertOnDuplicate(
                                        'catalog_product_entity_media_gallery',
                                        $mediaGalleryData,
                                        ['attribute_id', 'value', 'disabled']
                                    );
                                }
                                $newMediaSelect = $connection->select()->from('catalog_product_entity_media_gallery', 'value_id')
                                    ->where('value = ?', $mediaGalleryRow['value'])
                                    ->limit(1);
                                $newMediaValues = $connection->fetchAssoc($newMediaSelect);
                                foreach ($newMediaValues as $value_id => $values) {
                                    $mediaGalleryRow['value_id'] = $value_id;
                                }
                                $mediaInputArray[] = $mediaGalleryRow;
                            } else {
                                // Update additional_image
                                if (isset($mediaGalleryRow['value_id'])) {
                                    $mediaGalleryValueForProduct[] = [
                                        'value_id' => $mediaGalleryRow['value_id']
                                    ];
                                    $oldMediaGallerys = $this->resourceConnection->getConnection()->fetchAll(
                                        $this->resourceConnection->getConnection()->select()
                                            ->from($this->getResource()->getTable('catalog_product_entity_media_gallery_value'))
                                            ->where('value_id = ?', $mediaGalleryRow['value_id'])
                                    );
                                    if (!empty($oldMediaGallerys)) {
                                        if (
                                            isset($mediaGalleryRow['label']) &&
                                            isset($mediaGalleryRow['position']) &&
                                            isset($mediaGalleryRow['disabled']) &&
                                            isset($mediaGalleryRow['row_id']) &&
                                            isset($mediaGalleryRow['store_id'])
                                        ) {
                                            $this->resourceConnection->getConnection()->update(
                                                $this->getResource()->getTable('catalog_product_entity_media_gallery_value'),
                                                [
                                                    'label' => $mediaGalleryRow['label'],
                                                    'position' => $mediaGalleryRow['position'],
                                                    'disabled' => $mediaGalleryRow['disabled']
                                                ],
                                                [
                                                    'row_id = ?' => $mediaGalleryRow['row_id'],
                                                    'value_id = ?' => $mediaGalleryRow['value_id'],
                                                    'store_id = ?' => $mediaGalleryRow['store_id']
                                                ]
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $multiInsertData = [];
                    $dataForSkinnyTable = [];
                    foreach ($mediaGallery as $storeId => $storeMediaGalleryData) {
                        foreach ($storeMediaGalleryData as $mediaGalleryRows) {
                            foreach ($mediaInputArray as $insertValue) {
                                if (isset($insertValue) && !empty($insertValue)) {
                                    $valueArr = [
                                        'value_id' => $insertValue['value_id'],
                                        'store_id' => $storeId,
                                        'row_id' => $insertValue['row_id'],
                                        'label' => $insertValue['label'],
                                        'position' => $insertValue['position'],
                                        'disabled' => $insertValue['disabled'],
                                    ];
                                    $multiInsertData[] = $valueArr;
                                    $dataForSkinnyTable[] = [
                                        'value_id' => $insertValue['value_id'],
                                        'row_id' => $insertValue['row_id'],
                                    ];
                                }
                            }
                        }
                    }
                    if (!empty($multiInsertData)) {
                        $connection->insertOnDuplicate(
                            'catalog_product_entity_media_gallery_value',
                            $multiInsertData,
                            ['value_id', 'store_id', 'row_id', 'label', 'position', 'disabled']
                        );
                    }
                    if (!empty($dataForSkinnyTable)) {
                        $connection->insertOnDuplicate(
                            'catalog_product_entity_media_gallery_value_to_entity',
                            $dataForSkinnyTable,
                            ['value_id', 'row_id']
                        );
                    }
                }
            }
        }
    }
}
