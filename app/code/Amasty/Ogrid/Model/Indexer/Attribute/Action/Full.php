<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Indexer\Attribute\Action;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Eav\Model\Config;

class Full
{
    /**
     * @var array|null
     */
    protected $_searchableAttributes;

    /**
     * @var CollectionFactory
     */
    protected $_productAttributeCollectionFactory;

    /**
     * @var IndexIteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @var Config
     */
    protected $_eavConfig;

    public function __construct(
        CollectionFactory $productAttributeCollectionFactory,
        \Amasty\Ogrid\Model\Indexer\Attribute\Action\IndexIteratorFactory $indexIteratorFactory,
        Config $eavConfig
    ) {
        $this->_productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->_iteratorFactory = $indexIteratorFactory;
        $this->_eavConfig = $eavConfig;
    }

    public function getSearchableAttribute($attribute)
    {
        $attributes = $this->getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        } elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }

        return $this->getEavConfig()->getAttribute(\Magento\Catalog\Model\Category::ENTITY, $attribute);
    }

    public function getFilteredSearchableAttributes(array $attributesHash, $backendType)
    {
        $attributes = [];
        foreach ($this->getSearchableAttributes() as $attributeId => $attribute) {
            if (in_array($attribute->getAttributeCode(), $attributesHash) &&
                $attribute->getBackendType() == $backendType) {
                $attributes[$attributeId] = $attribute;
            }
        }

        return $attributes;
    }

    public function getSearchableAttributes()
    {
        if (null === $this->_searchableAttributes) {
            $this->_searchableAttributes = [];

            $attributesCollection = $this->_productAttributeCollectionFactory->create();
            $attributesCollection->join(
                ['ogrid_attribute' => $attributesCollection->getTable('amasty_ogrid_attribute')],
                'ogrid_attribute.attribute_id = main_table.attribute_id',
                'ogrid_attribute.entity_id as ogrid_attribute_entity_id'
            );

            /** @var \Magento\Eav\Model\Entity\Attribute[] $attributes */
            $attributes = $attributesCollection->getItems();

            $entity = $this->getEavConfig()
                ->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

            $this->_searchableAttributes = $attributes;
        }

        return $this->_searchableAttributes;
    }

    protected function getEavConfig()
    {
        return $this->_eavConfig;
    }

    public function rebuildIndex(array $attributesHash, $itemsIds = null)
    {
        $staticFields = [];
        $specialFields = [];
        foreach ($this->getFilteredSearchableAttributes($attributesHash, 'static') as $attribute) {
            $attributeId = $attribute->getId();
            $attributeCode = $attribute->getAttributeCode();

            if ($attribute->getFrontendInput() == 'weee') {
                $specialFields[$attributeId] = $attributeCode;
            } else {
                $staticFields[$attributeId] = $attributeCode;
            }
        }

        return $this->_iteratorFactory->create([
            'itemsIds' => $itemsIds,
            'staticFields' => $staticFields,
            'fields' => [
                'int' => array_keys($this->getFilteredSearchableAttributes($attributesHash, 'int')),
                'varchar' => array_keys($this->getFilteredSearchableAttributes($attributesHash, 'varchar')),
                'text' => array_keys($this->getFilteredSearchableAttributes($attributesHash, 'text')),
                'decimal' => array_keys($this->getFilteredSearchableAttributes($attributesHash, 'decimal')),
                'datetime' => array_keys($this->getFilteredSearchableAttributes($attributesHash, 'datetime')),
            ],
            'specialFields' => $specialFields
        ]);
    }
}
