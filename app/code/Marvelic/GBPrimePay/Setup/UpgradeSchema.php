<?php

namespace Marvelic\GBPrimePay\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            if (!$setup->tableExists('gbprimepay_purchase')) {
                try {
                    $table = $setup->getConnection()->newTable(
                        $setup->getTable('gbprimepay_purchase')
                    )->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'ID'
                    )->addColumn(
                        'order_id',
                        Table::TYPE_INTEGER,
                        50,
                        ['unsigned' => true, 'nullable' => false],
                        'Order Id'
                    )->addColumn(
                        'referenceNo',
                        Table::TYPE_TEXT,
                        50,
                        ['nullable' => true],
                        'Reference No'
                    )->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )->addForeignKey(
                        $setup->getFkName('gbprimepay_purchase', 'order_id', 'sales_order', 'entity_id'),
                        'order_id',
                        $setup->getTable('sales_order'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    );
                    $setup->getConnection()->createTable($table);
                } catch (\Exception $e) {
                    $setup->endSetup();
                    throw $e;
                }
            }
        }

        $setup->endSetup();
    }
}
