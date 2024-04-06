<?php

namespace Isobar\ConfigurableProductChildDataManagement\Plugin\Model\Indexer\Fulltext\Action;

use Isobar\ConfigurableProductChildDataManagement\Model\Config as ConfigData;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as EavCollectionFactory;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class DataProviderPlugin
{
    private ConfigData $configData;

    private EavCollectionFactory $eavCollectionFactory;

    private ProductResource $productResource;

    private ProductCollectionFactory $productCollectionFactory;

    private $_disableAttributeIds = [];

    private $_isEnables = [];

    /**
     * @param ConfigData $config
     * @param EavCollectionFactory $eavCollectionFactory
     * @param ProductResource $productResource
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        ConfigData $config,
        EavCollectionFactory $eavCollectionFactory,
        ProductResource $productResource,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->configData = $config;
        $this->eavCollectionFactory = $eavCollectionFactory;
        $this->productResource = $productResource;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param DataProvider $subject
     * @param $result
     * @param $indexData
     * @param $productData
     * @param $storeId
     * @return array
     */
    public function afterPrepareProductIndex(
        DataProvider $subject,
        $result,
        $indexData,
        $productData,
        $storeId
    ): array
    {
        if (!$this->isEnable($storeId) || $productData['type_id'] != 'configurable') {
            return $result;
        }

        $disableAttributeIds = $this->getDisableAttributeIds($storeId);

        foreach ($result as $indexId => &$productIds)
        {
            if (!in_array($indexId, $disableAttributeIds)) {
                continue;
            }

            if (!is_array($productIds)) {
                continue;
            }

            foreach ($productIds as $productId => &$sortId)
            {
                if (!$this->isSimpleProduct($productId)) {
                    continue;
                }

                unset($productIds[$productId]);
            }
        }

        return $result;
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function isEnable($storeId)
    {
       if (!isset($this->_isEnables[$storeId])) {
           $this->_isEnables[$storeId] = $this->configData->isEnable($storeId);
       }

       return $this->_isEnables[$storeId];
    }

    /**
     * @param $storeId
     * @return array
     */
    private function getDisableAttributeIds($storeId)
    {
        if (!isset($this->_disableAttributeIds[$storeId])) {
            $disabledAttributeCodes = $this->configData->getDisabledAttributes($storeId);
            $disabledAttributeCodes = explode(',', $disabledAttributeCodes);

            /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection */
            $attributeCollection = $this->eavCollectionFactory->create();
            $attributeCollection->addFieldToSelect('attribute_id')
                ->addFieldToFilter('entity_type_id', $this->productResource->getTypeId())
                ->addFieldToFilter('attribute_code', ['in' => $disabledAttributeCodes])
                ->load();

            $this->_disableAttributeIds[$storeId] = $attributeCollection->getColumnValues('attribute_id');
        }

        return $this->_disableAttributeIds[$storeId];
    }

    /**
     * @param $productId
     * @return bool
     */
    private function isSimpleProduct($productId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('entity_id', $productId)
            ->addFieldToFilter('type_id', 'simple')
            ->setPageSize(1);

        $product = $productCollection->getFirstItem();

        return ($product && $product->getId());
    }
}
