<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->renameColumn($installer);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addColumnEntityOrder($installer);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->insertOrderEntityData($installer);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->addConstraintsKey($installer);
        }

        $installer->endSetup();
    }

    public function renameColumn(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $installer->getTable('beamcheckout_purchase');

        if ($connection->tableColumnExists($table, 'order_id') && !$connection->tableColumnExists($table, 'increment_id')) {
            $connection->changeColumn(
                $table,
                'order_id',
                'increment_id',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => false,
                    'comment' => 'Order Increment ID',
                    Table::ACTION_NO_ACTION
                ]
            );

            $connection->dropIndex(
                $table,
                'increment_id'
            );
        }
    }

    public function addColumnEntityOrder(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $installer->getTable('beamcheckout_purchase');

        if ($connection->tableColumnExists($table, 'order_id') === false) {
            $connection->addColumn(
                $table,
                'order_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Order Entity Id',
                    'after' => 'id'
                ]
            );
        }
    }

    public function insertOrderEntityData(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $purchaseTable = $installer->getTable('beamcheckout_purchase');
        $orderTable = $installer->getTable('sales_order');

        if (
            $connection->tableColumnExists($purchaseTable, 'increment_id') &&
            $connection->tableColumnExists($purchaseTable, 'order_id')
        ) {
            $select = $connection->select()
                ->from(
                    ['o' => $orderTable],
                    ['o.entity_id', 'o.increment_id']
                )
                ->join(
                    ['p' => $purchaseTable],
                    'o.increment_id = p.increment_id'
                )
                ->where('o.increment_id = p.increment_id')
                ->group([
                    'o.entity_id',
                    'o.increment_id',
                    'p.order_id',
                    'p.increment_id',
                    'p.purchaseId',
                    'p.paymentLink',
                    'p.id'
                ]);

            $data = $connection->fetchAll($select);

            if (!empty($data)) {
                foreach ($data as $key => $val) {
                    $updateData = ['order_id' => $val['entity_id']];
                    $where = ['increment_id = ?' => $val['increment_id']];
                    $connection->update($purchaseTable, $updateData, $where);
                }
            }
        }
    }

    public function addConstraintsKey(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $table = $installer->getTable('beamcheckout_purchase');

        if ($connection->tableColumnExists($table, 'order_id') === true) {
            $connection->addForeignKey(
                $installer->getFkName($table, 'order_id', 'sales_order', 'entity_id'),
                $table,
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        }
    }
}
