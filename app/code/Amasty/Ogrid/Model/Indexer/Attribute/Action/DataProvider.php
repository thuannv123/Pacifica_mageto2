<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Indexer\Attribute\Action;

use Amasty\Base\Model\Serializer;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class DataProvider
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var Full
     */
    private $actionFull;

    /**
     * @var string
     */
    private $separator = ', ';

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ResolveProductIds
     */
    private $productIdsResolver;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ResourceConnection $resource,
        Product $product,
        ResolveProductIds $productIdsResolver,
        Serializer $serializer
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->product = $product;
        $this->productIdsResolver = $productIdsResolver;
        $this->serializer = $serializer;
    }

    public function getSearchableItems(
        array $staticFields,
        $itemsIds = null,
        $lastItemId = 0,
        $limit = 100
    ) {
        $select = $this->connection->select()
            ->from(
                ['e' => $this->getTable('sales_order_item')],
                ['item_id', 'product_id', 'store_id']
            );

        $groupByItemId = false;
        if (in_array('category_ids', $staticFields)) {
            $groupByItemId = true;
            $select->join(
                ['category' => $this->getTable('catalog_category_product')],
                'e.product_id = category.product_id',
                'GROUP_CONCAT(category_id) as category_ids'
            );
            unset($staticFields[array_search('category_ids', $staticFields)]);
        }

        $select->join(
            ['product' => $this->getTable('catalog_product_entity')],
            'e.product_id = product.entity_id',
            $staticFields
        );

        if ($itemsIds !== null) {
            $select->where('e.item_id IN (?)', $itemsIds);
        }

        $select->where('e.item_id > ?', $lastItemId)->limit($limit)->order('e.item_id');

        if ($groupByItemId) {
            $select->group('e.item_id');
        }

        $result = $this->connection->fetchAll($select);

        return $result;
    }

    public function setActionFull(Full $actionFull)
    {
        $this->actionFull = $actionFull;
    }

    public function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    protected function unifyField($field, $backendType = 'varchar')
    {
        if ($backendType == 'datetime') {
            $expr = $this->connection->getDateFormatSql($field, '%Y-%m-%d %H:%i:%s');
        } else {
            $expr = $field;
        }
        return $expr;
    }

    public function getItemAttributes(
        array $productsItems,
        array $attributeTypes
    ) {
        $ifStoreValue = $this->connection->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
        $result = [];
        $id = $this->product->getResource()->getLinkField();

        foreach ($productsItems as $storeId => $products) {
            if ($id == 'row_id') {
                // on EE version row_id for join tables should be used
                $productIds = $this->productIdsResolver->getRowIdsFromEntityIds(array_keys($products));
            } else {
                $productIds = $products;
            }

            $selects = [];
            foreach ($attributeTypes as $backendType => $attributeIds) {
                if ($attributeIds) {
                    $tableName = $this->getTable('catalog_product_entity_' . $backendType);
                    $selects[] = $this->connection->select()->from(
                        ['t_default' => $tableName],
                        [$id, 'attribute_id']
                    )->joinLeft(
                        ['t_store' => $tableName],
                        $this->connection->quoteInto(
                            't_default.' . $id . '=t_store.' . $id .
                            ' AND t_default.attribute_id=t_store.attribute_id' .
                            ' AND t_store.store_id = ?',
                            $storeId
                        ),
                        ['value' => $this->unifyField($ifStoreValue, $backendType)]
                    )->where(
                        't_default.store_id = ?',
                        0
                    )->where(
                        't_default.attribute_id IN (?)',
                        $attributeIds
                    )->where(
                        't_default.' . $id . ' IN (?)',
                        array_keys($productIds)
                    );
                }
            }

            if ($selects) {
                $select = $this->connection->select()->union($selects, \Magento\Framework\DB\Select::SQL_UNION_ALL);

                $query = $this->connection->query($select);
                while ($row = $query->fetch()) {
                    if ($id == 'row_id' && array_key_exists($row[$id], $productIds)) {
                        $productId = $productIds[$row[$id]];
                    } else {
                        $productId = $row[$id];
                    }

                    if (array_key_exists($productId, $products)) {
                        foreach ($products[$productId] as $itemId) {
                            $result[$itemId][$row['attribute_id']] = $row['value'];
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function collectItemSpecialAttributes(
        array $productsItems,
        array $specialAttributes,
        array &$itemAttributes
    ) {
        foreach ($specialAttributes as $attributeId => $attributeCode) {
            $attribute = $this->actionFull->getSearchableAttribute($attributeId);
            if ($attribute->getFrontendInput() == 'weee') {
                $this->collectWeeItemAttributes($productsItems, $attributeId, $itemAttributes);
            }
        }
    }

    private function collectWeeItemAttributes(
        array $productsItems,
        $attributeId,
        array &$specialItemAttributes
    ) {
        foreach ($productsItems as $products) {
            $productIds = array_keys($products);

            $select = $this->connection->select()
                ->from(
                    $this->getTable('weee_tax'),
                    [
                        'entity_id',
                        'country',
                        'value',
                        'state',
                        'website_id'
                    ]
                )
                ->where('entity_id IN (?)', $productIds)
                ->where('attribute_id = ?', $attributeId);

            $rows = $this->connection->fetchAll($select);
            if ($rows) {
                $rowsByEntityId = [];

                foreach ($rows as $row) {
                    $entityId = $row['entity_id'];
                    unset($row['entity_id']);

                    $rowsByEntityId[$entityId][] = $row;
                }

                foreach ($productIds as $productId) {
                    if (isset($rowsByEntityId[$productId])) {
                        foreach ($products[$productId] as $itemId) {
                            $specialItemAttributes[$itemId][$attributeId] = $rowsByEntityId[$productId];
                        }
                    }
                }
            }
        }
    }

    public function processAttributeValue($attribute, $value)
    {
        $frontendInput = $attribute->getFrontendInput();

        if (in_array($frontendInput, ['select', 'multiselect'])) {
            return '';
        }
        if ($frontendInput == 'weee') {
            return $this->serializer->serialize($value);
        }

        return $value;
    }

    public function prepareItemIndex($indexData, $itemData)
    {
        $storeId = $itemData['store_id'];
        $index = [];

        foreach ($indexData as $entityId => $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {

                $value = $this->getAttributeValue($attributeId, $attributeValue, $storeId);

                if (!empty($value)) {
                    if (isset($index[$attributeId])) {
                        $index[$attributeId][$entityId] = $value;
                    } else {
                        $index[$attributeId] = [$entityId => $value];
                    }
                }
            }
        }

        return $this->prepareEntityIndex($index, $this->separator);
    }

    protected function prepareEntityIndex($index, $separator = ' ')
    {
        $indexData = [];
        foreach ($index as $attributeId => $value) {
            $indexData[$attributeId] = is_array($value) ? implode($separator, $value) : $value;
        }
        return $indexData;
    }

    protected function getAttributeValue($attributeId, $valueId, $storeId)
    {
        $attribute = $this->actionFull->getSearchableAttribute($attributeId);

        $value = $this->processAttributeValue($attribute, $valueId);

        if ($value !== false && $attribute->usesSource()) {
            $attribute->setStoreId($storeId);
            $valueText = array_filter((array) $attribute->getSource()->getIndexOptionText($valueId));
            $value = implode($this->separator, $valueText);
        }

        $value = preg_replace(
            '/\\s+/siu',
            ' ',
            trim(strip_tags((string)$value))
        );

        return $value;
    }
}
