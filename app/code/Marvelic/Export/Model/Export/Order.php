<?php

namespace Marvelic\Export\Model\Export;

use DateTime;
use Firebear\ImportExport\Model\ExportJob\Processor;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ImportExport\Model\Export\Factory as ExportFactory;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIteratorFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Firebear\ImportExport\Model\Export\Dependencies\Config as ExportConfig;
use Firebear\ImportExport\Model\Source\Factory as SourceFactory;
use Firebear\ImportExport\Helper\Data as Helper;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
class Order extends \Firebear\ImportExport\Model\Export\Order
{
    /**
     * Order Repository
     *
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @param LoggerInterface $logger
     * @param ConsoleOutput $output
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ExportFactory $collectionFactory
     * @param CollectionByPagesIteratorFactory $resourceColFactory
     * @param OrderCollectionFactory $orderColFactory
     * @param ResourceConnection $resource
     * @param ExportConfig $exportConfig
     * @param SourceFactory $sourceFactory
     * @param Helper $helper
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param JsonSerializer $jsonSerializer
     * @param CustomerFactory $customerFactory
     * @param OrderInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        LoggerInterface $logger,
        ConsoleOutput $output,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ExportFactory $collectionFactory,
        CollectionByPagesIteratorFactory $resourceColFactory,
        OrderCollectionFactory $orderColFactory,
        ResourceConnection $resource,
        ExportConfig $exportConfig,
        SourceFactory $sourceFactory,
        Helper $helper,
        StatusCollectionFactory $statusCollectionFactory,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        JsonSerializer $jsonSerializer,
        CustomerFactory $customerFactory,
        ModuleManager $moduleManager,
        FilterBuilder $filterBuilder,
        OrderInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct(
            $logger,
            $output,
            $scopeConfig,
            $storeManager,
            $collectionFactory,
            $resourceColFactory,
            $orderColFactory,
            $resource,
            $exportConfig,
            $sourceFactory,
            $helper,
            $statusCollectionFactory,
            $productRepository,
            $searchCriteriaBuilder,
            $jsonSerializer,
            $customerFactory,
            $moduleManager,
            $filterBuilder,
            $data
        );
        $this->orderRepository = $orderRepository;
    }

    public function exportItem($item)
    {
        $exportData = $this->_getExportData($item);
        if (!$this->checkItemByProductFilter($exportData)) {
            return;
        }
        /* skip order if at least one child entity is not valid */
        foreach ($this->filters as $table => $result) {
            /* check valid of child table (exclude sales_order) */
            if (false === $result && ($table != 'sales_order' || $table != 'sales_order_product')) {
                return;
            }
            if (is_array($result)) {
                foreach ($result as $field => $isValid) {
                    if (false === $isValid) {
                        return;
                    }
                }
            }
        }

        foreach ($exportData as $row) {
            /**
             * skip empty rows
             */
            if (!array_filter($row)) {
                continue;
            }
            if (isset($row[self::COLUMN_INCREMENT_ID]) && $row[self::COLUMN_INCREMENT_ID] == null) {
                continue;
            }
            // if (isset($row[self::COLUMN_INCREMENT_ID])) {
            //     $incrementId = $row[self::COLUMN_INCREMENT_ID];
            //     $row['order_increment_id'] = $incrementId;
            // }
            $row = array_filter($row, function ($value, $key) {
                return is_scalar($value) || is_null($value);
            }, ARRAY_FILTER_USE_BOTH);
            $this->getWriter()->writeRow($row);
            $this->_processedEntitiesCount++;
        }
    }

    protected function _getExportData($item)
    {
        $orderId = $item->getId();
        $customerId = $item->getCustomerId();
        $deps = $this->_parameters['behavior_data']['deps'];
        $children = $this->_exportConfig['order']['fields'] ?? [];
        $this->lastEntityId = $orderId;
        $this->filters = [];
        $isOneLine = $this->_parameters['behavior_data']['export_in_one_line'] ?? 0;

        if (!count($this->_default) && !$this->_isNested()) {
            $tables = array_keys($this->_prefixData);
            foreach ($tables as $table) {
                if (!in_array($table, $deps)) {
                    continue;
                }
                if (empty($this->_describeTable[$table])) {
                    if ($table == 'sales_order_product') {
                        $this->_describeTable[$table] = array_fill_keys($this->_getExportAttributeCodes(), '');
                    } elseif ($table == 'customer_entity') {
                        $this->_describeTable[$table] = array_fill_keys($this->_getCustomerExportAttributeCodes(), '');
                    } else {
                        $this->_describeTable[$table] = $this->_connection->describeTable(
                            $this->_resourceModel->getTableName($table)
                        );
                    }
                }
                $prefix = $this->_prefixData[$table] ?? $table;
                $row = [];
                if (!($table == 'sales_order_address' && $isOneLine)) {
                    foreach (array_keys($this->_describeTable[$table]) as $column) {
                        $row[$prefix . ':' . $column] = '';
                    }
                }

                if ($table !== 'sales_order_product') {
                    $row = $this->_updateData($row, $table);
                }
                $this->_default = array_merge($this->_default, $row);
            }
        }

        $exportData = $item->toArray();
        unset($exportData['store_name']);

        $exportData['customer_group'] = $this->getCustomerGroup(
            $exportData['customer_group_id'] ?? null
        );

        $exportData['status_label'] = isset($exportData['status'])
            ? $this->_getStatusLabel($exportData['status'])
            : '';

        $exportData = $this->_updateData($exportData, 'sales_order');
        $this->_exportData = [0 => array_merge($exportData, $this->_default)];
        $this->_exportBaseData = array_merge($exportData, $this->_default);
        foreach ($children as $table => $param) {
            if ($param['parent'] == 'sales_order' && in_array($table, $deps)) {
                $this->_prepareChildEntity(
                    [$orderId],
                    $table,
                    $param['parent_field'],
                    $param['main_field'],
                    $customerId
                );
            }
        }
        $this->sortFields();
        return $this->_exportData;
    }
    private function sortFields()
    {
        $allFields = $this->_parameters['all_fields'];
        $deps = $this->_parameters['dependencies'];
        $replaceCodes = $this->_parameters['replace_code'];
        $filterSelect = $this->_parameters['export_filter'];
        if ($allFields) {
            $newData = [];
            $i = 0;
            foreach ($this->_exportData as $key => &$exportRow) {
                if (!$exportRow) {
                    continue;
                }
                foreach ($deps as $id => $dep) {
                    $fieldName = $replaceCodes[$id];
                    if ($dep == 'sales_order_address') {
                        $isOneLine = $this->_parameters['behavior_data']['export_in_one_line'] ?? 0;
                        $address_type = '';
                        if ($isOneLine) {
                            foreach ($filterSelect as $key_select => $value_select) {
                                if (strpos($key_select, 'address') !== false) {
                                    $address_type = $value_select;
                                }
                            }

                            if ($address_type != '') {
                                $address_type = strtolower($address_type);
                                $newData[$address_type . ':' . $fieldName] = $exportRow[$address_type . ':' . $fieldName] ?? '';
                            } else {
                                $newData['shipping:' . $fieldName] = $exportRow['shipping:' . $fieldName] ?? '';
                                $newData['billing:' . $fieldName] = $exportRow['billing:' . $fieldName] ?? '';
                            }
                        } else {
                            $newData[$fieldName] = $exportRow[$fieldName] ?? '';
                        }
                    } else {
                        $newData[$fieldName] = $exportRow[$fieldName] ?? '';
                    }
                }
                if ($i >= 1) {
                    foreach ($newData as $key => $valueRowChange) {
                        $j = $i;
                        $data = $this->_exportData[$j - 1];
                        foreach ($data as $key_arr => $value) {
                            if (strpos($key, 'address') !== false && $valueRowChange == '') {
                                if ($key_arr == $key) {
                                    $newData[$key] = $data[$key_arr];
                                }
                            }
                        }
                    }
                }
                $i++;
                $exportRow = $newData;
            }
        }
    }

    private function checkItemByProductFilter($exportData)
    {
        $exportFilterParams = $this->_parameters[Processor::EXPORT_FILTER_TABLE] ?? [];
        $attributeTypes = $this->getExportAttrTypes();
        $result = true;
        foreach ($exportFilterParams as $filterData) {
            $filterEntityType = $filterData['entity'] ?? '';
            $filterFieldName = $filterData['field'] ?? '';
            $filterFieldValue = $filterData['value'] ?? '';
            if ($filterEntityType !== 'sales_order_product') {
                continue;
            }
            $result = false;
            foreach ($exportData as $exportRow) {
                if ($result) {
                    continue;
                }
                $exportAttributeValue = $exportRow['product:' . $filterFieldName] ?? '';
                if ($filterFieldValue && $exportAttributeValue) {
                    $filterValueType = !empty($attributeTypes[$filterFieldName]) ?
                        $this->getAttributeType($attributeTypes[$filterFieldName]) : '';
                    if ('text' == $filterValueType) {
                        if ($exportAttributeValue == $filterFieldValue) {
                            $result = true;
                        }
                    } elseif ('int' == $filterValueType) {
                        if (is_array($filterFieldValue) && count($filterFieldValue) == 2) {
                            $from = array_shift($filterFieldValue);
                            $to = array_shift($filterFieldValue);
                            if (is_numeric($from)) {
                                if ($exportAttributeValue >= $from) {
                                    $result = true;
                                }
                            }
                            if (is_numeric($to) && $result) {
                                if ($exportAttributeValue > $to) {
                                    $result = false;
                                }
                            }
                        } else {
                            if ($exportAttributeValue == $filterFieldValue) {
                                $result = true;
                            }
                        }
                    } elseif ('date' == $filterValueType) {
                        if (is_array($filterFieldValue) && count($filterFieldValue) == 2) {
                            $from = array_shift($filterFieldValue);
                            $to = array_shift($filterFieldValue);
                            $exportDate = (new DateTime($exportAttributeValue))->getTimestamp();

                            if ($from == 'NaN') {
                                $from = '';
                            }
                            if ($to == 'NaN') {
                                $to = '';
                            }
                            if (is_scalar($from) && !empty($from)) {
                                $date = (new DateTime($from))->getTimestamp();
                                if ($exportDate >= $date) {
                                    $result = true;
                                }
                            }
                            if (is_scalar($to) && !empty($to) && $result) {
                                $date = (new DateTime($to))->getTimestamp();
                                if ($exportDate > $date) {
                                    $result = false;
                                }
                            }
                        }
                    } elseif ('select' == $filterValueType) {
                        $selectedValue = $this->options[$filterFieldName][$filterFieldValue] ?? '';
                        if (
                            $selectedValue &&
                            $selectedValue == $exportAttributeValue
                        ) {
                            $result = true;
                        }
                    }
                }
            }
            if (!$result) {
                return $result;
            }
        }
        return $result;
    }

    private function getAttributeType($type)
    {
        if (in_array($type, ['int', 'decimal', 'price'])) {
            return 'int';
        }
        if (in_array($type, ['varchar', 'text', 'textarea'])) {
            return 'text';
        }
        if (in_array($type, ['select', 'multiselect', 'boolean'])) {
            return 'select';
        }
        if (in_array($type, ['datetime', 'date'])) {
            return 'date';
        }
        return 'not';
    }

    protected function _prepareChildEntity($entityIds, $table, $parentIdField, $entityIdField, $customerId = [])
    {
        $rowId = 0;
        $initialEntityData = $this->_exportBaseData;
        $listTable = $this->_parameters['behavior_data']['deps'];
        $filterSelect = $this->_parameters['export_filter'];
        $isOneLine = $this->_parameters['behavior_data']['export_in_one_line'] ?? 0;
        $orderEntityIds = $entityIds;
        foreach ($filterSelect as $key => $value) {
            if ($key == 'updated_at') {
                continue;
            }
            foreach ($listTable as $id => $tableName) {
                if ($table == $tableName) {
                    if ($this->_connection->tableColumnExists($table, $key)) {
                        $select = $this->_connection->select()->from(
                            $this->_resourceModel->getTableName($table)
                        )->where(
                            $parentIdField . ' IN (?)',
                            $entityIds
                        )->where(
                            $key . ' IN (?)',
                            $value
                        );
                        $checkRow = $this->_connection->query($select)->fetchAll();
                        if (count($checkRow) != 0) {
                            $stmt = $this->_connection->query($select);
                        }
                    }
                }
            }
        }
        if (!isset($stmt)) {
            $select = $this->_connection->select()->from(
                $this->_resourceModel->getTableName($table)
            )->where(
                $parentIdField . ' IN (?)',
                $entityIds
            );
            $stmt = $this->_connection->query($select);
        }
        $prefix = $this->_prefixData[$table] ?? $table;
        $prefix2 = '';
        $isOneLine = $this->_parameters['behavior_data']['export_in_one_line'] ?? 0;

        $deps = $this->_parameters['behavior_data']['deps'];
        $children = $this->_exportConfig['order']['fields'] ?? [];
        $entityIds = [];
        $productIds = [];
        $orderItemAndProductIdPairs = [];
        $this->prepareCustomer($customerId);
        if ($this->_isNested()) {
            $exportData = [];
            while ($row = $stmt->fetch()) {
                $entityIds[] = $row[$entityIdField];
                if ($table == 'sales_order_item') {
                    $orderItemAndProductIdPairs[$row['item_id']] = $row['product_id'];
                }
                $exportData[] = ['item' => $this->_updateData($row, $table)];
            }
            $this->_exportData[0][$prefix] = $exportData;
        } else {
            while ($row = $stmt->fetch()) {
                $entityIds[] = $row[$entityIdField];
                if ($table == 'sales_order_item') {
                    $orderItemAndProductIdPairs[$row['item_id']] = $row['product_id'];
                    $row['downloadable_link_data'] = '';
                    if (
                        !empty($row['product_type']) &&
                        $row['product_type'] == 'downloadable' &&
                        !empty($row['item_id'])
                    ) {
                        $row['downloadable_link_data'] = $this->jsonSerializer->serialize(
                            $this->getDownloadableItemData($row['item_id'])
                        );
                    }
                }
                if (
                    $table == 'sales_order_address'
                    && isset($row[OrderAddressInterface::ADDRESS_TYPE])
                    && $isOneLine
                ) {
                    $addressType = $row[OrderAddressInterface::ADDRESS_TYPE];
                    $prefix2 = $addressType;
                } elseif (
                    $table == 'sales_order_address'
                    && isset($row[OrderAddressInterface::ADDRESS_TYPE])
                ) {
                    $prefix2 = 'address';
                }

                foreach ($row as $column_change_tax => $test4) {
                    if ($row[$column_change_tax] === "simple") {
                        if ($row["parent_item_id"] == null) {
                            continue;
                        } else {
                            $select = $this->_connection->select('row_total_incl_tax', 'price_incl_tax,discount_amount')->from(
                                $this->_resourceModel->getTableName($table)
                            )->where(
                                'item_id IN (?)',
                                $row["parent_item_id"]
                            );
                            $data_table_save = $this->_connection->query($select)->fetch();
                            foreach ($data_table_save as $key_id => $value_data_save) {
                                if (
                                    $key_id === "row_total_incl_tax" ||
                                    $key_id === "price_incl_tax" ||
                                    $key_id === "discount_amount"
                                ) {
                                    $row[$key_id] = $value_data_save;
                                }
                            }
                        }
                    }
                }
                foreach ($row as $column => $value) {
                    if ($table == 'sales_order_address') {

                        if ($column == 'street') {
                            $row = $this->prepareStreetFields($row, $prefix2);
                        } else {

                            $row[$prefix2 . ':' . $column] = $value;
                        }
                    } else {
                        $row[$prefix . ':' . $column] = $value;
                    }
                    unset($row[$column]);
                }
                $row = $this->_updateData($row, $table);
                $exportData = $this->_exportData[$rowId] ?? [];
                if ($rowId) {
                    $initialEntityData['line_type'] = '';
                } else {
                    $initialEntityData['line_type'] = 'order';
                }

                $initialEntityData = $this->sortStreetFields($initialEntityData);

                $this->_exportData[$rowId] = array_merge($initialEntityData, $exportData, $row);
                if ($table != 'sales_order_address' || !$isOneLine) {
                    $rowId++;
                } else {
                    $initialEntityData = array_merge($initialEntityData, $row);
                }
            }
        }
        if (!empty($orderItemAndProductIdPairs)) {
            ksort($orderItemAndProductIdPairs);
            $productIds = array_values($orderItemAndProductIdPairs);
        }

        if (!count($entityIds)) {
            if (
                !isset($this->_parameters[Processor::EXPORT_FILTER_TABLE]) ||
                !is_array($this->_parameters[Processor::EXPORT_FILTER_TABLE])
            ) {
                $exportFilter = [];
            } else {
                $exportFilter = $this->_parameters[Processor::EXPORT_FILTER_TABLE];
            }

            foreach ($exportFilter as $filter) {
                if ($filter['entity'] == $table) {
                    $this->filters[$table] = false;
                } else {
                    foreach ($children as $childTable => $param) {
                        if ($filter['entity'] == $childTable && $param['parent'] == $table) {
                            $this->filters[$childTable] = false;
                        }
                    }
                }
            }
            return;
        }

        if (in_array($table, $deps)) {
            foreach ($children as $childTable => $param) {
                if ($param['parent'] == $table && in_array($childTable, $deps)) {
                    if ($childTable == 'sales_order_product') {
                        if (!empty($productIds) && !empty($orderEntityIds)) {
                            $this->prepareProducts($orderEntityIds);
                        }
                    } else {
                        $this->_prepareChildEntity(
                            $entityIds,
                            $childTable,
                            $param['parent_field'],
                            $param['main_field']
                        );
                    }
                }
            }
        }
    }

    /**
     * Prepare StreetFields
     *
     * @param [] $data
     * @param string $prefix
     * @return mixed
     */
    protected function prepareStreetFields($data, string $prefix)
    {
        $column = 'street';
        $streetList = preg_split('/\r\n|\r|\n/', $data[$column]);
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($streetList) && isset($streetList[$i - 1])) {
                if ($i == 1) {
                    if (isset($streetList[$i])) {
                        $data[$prefix . ':' . $column] = $streetList[$i - 1] . ', ' . $streetList[$i];
                    } else {
                        $data[$prefix . ':' . $column] = $streetList[$i - 1];
                    }
                } else {
                    $data[$prefix . ':' . $column . $i] = $streetList[$i - 1];
                }
            } else {
                $data[$prefix . ':' . $column . $i] = '';
            }
        }
        return $data;
    }
    
    private function getDownloadableItemData($itemId)
    {
        $select = $this->_connection->select()
            ->from(['dlp' => $this->_resourceModel->getTableName('downloadable_link_purchased')])
            ->join(['dlpi' => 'downloadable_link_purchased_item'], 'dlpi.order_item_id  = dlp.order_item_id ')
            ->where('dlp.order_item_id = ?', $itemId);
        return $this->_connection->fetchAll($select);
    }

    protected function prepareProducts($orderEntityIds)
    {
        $rowId = 0;

        if ($this->_isNested()) {
            $exportData = [];
        }
        $items = [];
        foreach ($orderEntityIds as $entityId) {
            $items = $this->getProductFromOrderEntityId($entityId);

            foreach ($items as $item) {
                $sku = $item->getSku();
                $product = $this->productRepository->get($sku);
                if (!empty($product)) {
                    $row = [];
                    $fields = $this->_getExportAttributeCodes();
                    foreach ($fields as $field) {
                        if ('media_gallery' == $field) {
                            continue;
                        }
                        $value = $product->getData($field);
                        $fieldName = $this->_isNested() ? $field : 'product:' . $field;
                        $row[$fieldName] = is_array($value)
                            ? implode(',', $value)
                            : $this->prepareFieldValue($field, $value);
                    }

                    $instr = $this->_scopeFields('sales_order_product');
                    $allFields = $this->_parameters['all_fields'];
                    if (!$allFields) {
                        $row = $this->_changedColumns($row, $instr, 'sales_order_product');
                    } else {
                        $row = $this->_addPartColumns($row, $instr, 'sales_order_product');
                    }

                    if ($this->_isNested()) {
                        $exportData[] = ['item' => $row];
                    } else {
                        $this->_exportData[$rowId] = array_merge($this->_exportData[$rowId] ?? [], $row);
                        $rowId++;
                    }
                }
            }
        }

        if ($this->_isNested() && !empty($exportData)) {
            $this->_exportData[0]['product'] = $exportData;
        }
    }

    private function prepareFieldValue($code, $value)
    {
        return $this->options[$code][$value] ?? $value;
    }

    private function getProductFromOrderEntityId($entityId)
    {
        $items = [];

        try {
            $order = $this->orderRepository->load($entityId);
            $items = array_merge($items, $order->getAllItems());
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }

        return $items;
    }
}
