<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */
namespace Amasty\Ogrid\Model;

use Amasty\Ogrid\Model\ResourceModel\Attribute as AttributeResource;
use Amasty\Ogrid\Model\Attribute\Product\Weee\Formatter as WeeeFormatter;

class Attribute extends \Magento\Framework\Model\AbstractModel
{
    public const TABLE_ALIAS = 'ogrid_attribute_index';
    public const ATTRIBUTE_DB_ALIAS_PREFIX = 'amasty_ogrid_product_attrubute_';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var WeeeFormatter
     */
    private $weeFormatter;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        WeeeFormatter $weeFormatter,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_imageHelper = $imageHelper;
        $this->_urlBuilder = $urlBuilder;
        $this->weeFormatter = $weeFormatter;
    }

    protected function _construct()
    {
        $this->_init(AttributeResource::class);
    }

    public function getAttributeDbAlias(): string
    {
        return self::ATTRIBUTE_DB_ALIAS_PREFIX . $this->getAttributeCode();
    }

    public function addFieldToSelect($collection)
    {
        $collection->getSelect()->columns([
            $this->getAttributeDbAlias() => Attribute::TABLE_ALIAS . '.' . $this->getAttributeCode()
        ]);
    }

    public function modifyItem(&$item, $config = [])
    {
        // This is a bad approach, it's a not responsibility of data model

        $attributeDbAlias = $this->getAttributeDbAlias();

        switch ($this->getFrontendInput()) {
            case 'media_image':
                $product = new \Magento\Framework\DataObject(
                    ['thumbnail' => $item[$attributeDbAlias]]
                );

                $imageHelper = $this->_imageHelper->init($product, 'product_listing_thumbnail');
                $item[$attributeDbAlias . '_src'] = $imageHelper->getUrl();

                $origImageHelper = $this->_imageHelper->init($product, 'product_listing_thumbnail_preview');
                $item[$attributeDbAlias . '_orig_src'] = $origImageHelper->getUrl();
                $item[$attributeDbAlias . '_link'] = $this->_urlBuilder->getUrl(
                    'sales/order/view',
                    ['order_id' => $item['order_id']]
                );

                break;

            case 'weee':
                if (isset($item[$attributeDbAlias])) {
                    $item[$attributeDbAlias] = $this->weeFormatter->format($item[$attributeDbAlias]);
                }

                break;

            default:
                break;
        }
    }

    public function addFieldToFilter($orderItemCollection, $value)
    {
        $orderItemCollection->addFieldToFilter(
            Attribute::TABLE_ALIAS . '.' . $this->getAttributeCode(),
            ['like' => '%' . $value . '%']
        );
    }
}
