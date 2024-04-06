<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Indexer;

use Amasty\Ogrid\Model\ResourceModel\Attribute\Collection;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Amasty\Ogrid\Model\ResourceModel\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Convert\DataObject as ObjectConverter;

class IndexerHandler implements IndexerInterface
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * @var array
     */
    protected $_fields;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var Batch
     */
    protected $_batch;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var int
     */
    protected $_batchSize;

    /**
     * @var IndexScopeResolver
     */
    protected $_indexScopeResolver;

    /**
     * @var IndexStructure
     */
    protected $_indexStructure;

    /**
     * @var AttributeCollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @var ObjectConverter
     */
    protected $_objectConverter;

    /**
     * @var Collection
     */
    protected $_attributeCollection;

    /**
     * @var array
     */
    protected $_attributesHash = [];

    public function __construct(
        ResourceConnection $resource,
        IndexStructure $indexStructure,
        Config $eavConfig,
        Batch $batch,
        IndexScopeResolver $indexScopeResolver,
        AttributeCollectionFactory $attributeCollectionFactory,
        ObjectConverter $objectConverter,
        array $data,
        $batchSize = 200
    ) {
        $this->_indexScopeResolver = $indexScopeResolver;
        $this->_resource = $resource;
        $this->_batch = $batch;
        $this->_eavConfig = $eavConfig;
        $this->_data = $data;
        $this->_fields = [];

        $this->_batchSize = $batchSize;
        $this->_indexStructure = $indexStructure;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_objectConverter = $objectConverter;
    }

    protected function getAttributeCollection()
    {
        if ($this->_attributeCollection === null) {
            $this->_attributeCollection = $this->_attributeCollectionFactory->create()
                ->addFieldToFilter('attribute_id', ['notnull' => true]);
        }
        return $this->_attributeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        foreach ($this->_batch->getItems($documents, $this->_batchSize) as $batchDocuments) {
            $this->insertDocuments($batchDocuments, $dimensions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        foreach ($this->_batch->getItems($documents, $this->_batchSize) as $batchDocuments) {
            $this->_resource->getConnection()
                ->delete($this->getTableName($dimensions), ['item_id in (?)' => $batchDocuments]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cleanIndex($dimensions)
    {
        $this->_indexStructure->create(
            $this->getIndexName(),
            $this->_objectConverter->toOptionHash(
                $this->getAttributeCollection()->getItems(),
                'attribute_id',
                'attribute_code'
            ),
            $dimensions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($dimensions = [])
    {
        return true;
    }

    /**
     * @param Dimension[] $dimensions
     * @return string
     */
    private function getTableName($dimensions)
    {
        return $this->_indexScopeResolver->resolve($this->getIndexName(), $dimensions);
    }

    /**
     * @return string
     */
    private function getIndexName()
    {
        return $this->_data['indexer_id'];
    }

    private function insertDocuments(array $documents, array $dimensions)
    {
        $attributesHash = $this->getAttributeHash();

        $documents = $this->_prepareFields($documents, $attributesHash);
        if (empty($documents)) {
            return;
        }

        $this->_resource->getConnection()->insertOnDuplicate(
            $this->getTableName($dimensions),
            $documents,
            $attributesHash
        );
    }

    /**
     * @param array $documents
     * @return array
     */
    protected function _prepareFields(array $documents, array $attributes)
    {
        $insertDocuments = [];

        foreach ($documents as $entityId => $document) {
            $attributesData = [];

            foreach ($attributes as $attributeId => $attributeCode) {

                if (array_key_exists($attributeId, $document)) {
                    $attributesData[$attributeCode] = $document[$attributeId];
                } else {
                    $attributesData[$attributeCode] = null;
                }
            }
            //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $insertDocuments[$entityId ] = array_merge([
                'order_item_id' => $entityId
            ], $attributesData);
        }

        return $insertDocuments;
    }

    public function getIndexedAttributesHash($dimensions)
    {
        return $this->_indexStructure->getIndexedAttributes(
            $this->getIndexName(),
            $this->_objectConverter->toOptionHash(
                $this->getAttributeCollection()->getItems(),
                'attribute_id',
                'attribute_code'
            ),
            $dimensions
        );
    }

    public function getNoneIndexedAttributesHash()
    {
        return $this->_objectConverter->toOptionHash(
            $this->getAttributeCollection()->getItems(),
            'attribute_id',
            'attribute_code'
        );
    }

    public function setAttributeHash($attributesHash)
    {
        $this->_attributesHash = $attributesHash;

        return $this;
    }

    public function getAttributeHash()
    {
        return $this->_attributesHash;
    }
}
