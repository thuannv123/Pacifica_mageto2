<?php

namespace Marvelic\BankTransfer\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ResourceConnection;

class UpgradeData implements UpgradeDataInterface
{
    protected $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }
    /**
     * Remove data instructions from the database.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $connection  = $this->resource->getConnection();
            // Get the table name
            $tableName = $connection->getTableName('core_config_data');
            // Check if the column value exists in the table
            $select = $connection->select()
                ->from($tableName)
                ->where('path = ?', 'payment/banktransfer/instructions');
            $result = $connection->fetchOne($select);
            if ($result == true) {
                $connection->query("DELETE FROM `core_config_data` WHERE `path` = 'payment/banktransfer/instructions'");
            }
        }
    }
}
