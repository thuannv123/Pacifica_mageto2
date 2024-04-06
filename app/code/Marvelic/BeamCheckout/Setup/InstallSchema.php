<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Prepare database for install
         */
        $installer->startSetup();

        try {
            // Required tables
            $statusTable = $installer->getTable('sales_order_status');
            $statusStateTable = $installer->getTable('sales_order_status_state');

            // Insert statuses
            $installer->getConnection()->insertArray(
                $statusTable,
                array('status', 'label'),
                array(array('status' => 'Pending_BeamCheckout', 'label' => 'Pending BeamCheckout'))
            );

            // Insert states and mapping of statuses to states
            $installer->getConnection()->insertArray(
                $statusStateTable,
                array(
                    'status',
                    'state',
                    'is_default',
                    'visible_on_front'
                ),
                array(
                    array(
                        'status' => 'Pending_BeamCheckout',
                        'state' => 'Pending_BeamCheckout',
                        'is_default' => 0,
                        'visible_on_front' => 1
                    )
                )
            );
        } catch (\Exception $e) {
        }
        /**
         * Create beamcheckout_purchase table
         */
        if (!$installer->tableExists('beamcheckout_purchase')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('beamcheckout_purchase')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'order_id',
                Table::TYPE_TEXT,
                50,
                ['unsigned' => true, 'nullable' => false],
                'Order Id'
            )->addColumn(
                'purchaseId',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Purchase Id'
            )->addColumn(
                'paymentLink',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Payment Link'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addForeignKey(
                $installer->getFkName('beamcheckout_purchase', 'order_id', 'sales_order', 'increment_id'),
                'order_id',
                $installer->getTable('sales_order'),
                'increment_id',
                Table::ACTION_CASCADE
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
