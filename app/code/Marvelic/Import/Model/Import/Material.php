<?php

namespace Marvelic\Import\Model\Import;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Material
 */
class Material extends AbstractEntity
{
    const ENTITY_CODE = 'material';

    /**
     * If we should check column names
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     */
    protected $logInHistory = true;

    /**
     * Permanent entity columns.
     */
    protected $_permanentAttributes = [
        'sku'
    ];

    /**
     * Valid column names
     */
    protected $validColumnNames = [
        'sku',
        'store_view_code',
        'attribute_code'
    ];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Courses constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        StoreManagerInterface $storeManager
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->storeManager = $storeManager;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Import data
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function _importData(): bool
    {
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteEntity();
                break;
            case Import::BEHAVIOR_REPLACE:
                break;
            case Import::BEHAVIOR_APPEND:
                break;
        }

        return true;
    }

    /**
     * Delete entities
     *
     * @return bool
     */
    protected function deleteEntity(): bool
    {
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);

                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    if (isset($rowData['store_view_code'])) {
                        $stores = $this->storeManager->getStores();
                        foreach ($stores as $store) {
                            if ($store->getCode() === $rowData['store_view_code']) {
                                $storeId = $store->getId();
                                $rows[$rowNum]['store_id'] = $storeId;
                            }
                        }
                    }
                    if (isset($rowData['sku'])) {
                        $rows[$rowNum]['sku'] = $rowData['sku'];
                    }
                    if (isset($rowData['attribute_code'])) {
                        $rows[$rowNum]['attribute_code'] = $rowData['attribute_code'];
                    }
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        if ($rows) {
            return $this->deleteEntityFinish($rows);
        }

        return false;
    }

    /**
     * Delete entities
     *
     * @param array $entityIds
     *
     * @return bool
     */
    private function deleteEntityFinish(array $rows): bool
    {
        if (!empty($rows)) {
            try {
                foreach ($rows as $val) {
                    if (isset($val['sku'])) {
                        $select = $this->connection->select()
                            ->from(
                                $this->connection->getTableName('catalog_product_entity'),
                                ['row_id']
                            )
                            ->where('sku = ?', $val['sku']);

                        $rowIds = $this->connection->fetchCol($select);
                    }
                    if (isset($val['attribute_code'])) {
                        $select = $this->connection->select()
                            ->from(
                                $this->connection->getTableName('eav_attribute'),
                                ['attribute_id']
                            )
                            ->where('attribute_code = ?', $val['attribute_code']);

                        $rowAttributes = $this->connection->fetchCol($select);
                    }

                    if (!empty($rowIds) && !empty($rowAttributes)) {
                        $this->countItemsDeleted += $this->connection->delete(
                            $this->connection->getTableName('catalog_product_entity_varchar'),
                            [
                                $this->connection->quoteInto('attribute_id IN (?)', $rowAttributes),
                                $this->connection->quoteInto('store_id = ?', $val['store_id']),
                                $this->connection->quoteInto('row_id  IN (?)', $rowIds)
                            ]
                        );
                    }
                }
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }
}
