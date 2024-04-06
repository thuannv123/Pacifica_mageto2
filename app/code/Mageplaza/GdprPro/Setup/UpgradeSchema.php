<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mageplaza\Core\Helper\AbstractData;
use Zend_Db_Exception;

/**
 * Class UpgradeSchema
 * @package Mageplaza\GdprPro\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var AbstractData
     */
    protected $helperData;

    /**
     * InstallSchema constructor.
     *
     * @param AbstractData $helperData
     */
    public function __construct(AbstractData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$this->helperData->versionCompare('2.3.0')) {
            if (version_compare($context->getVersion(), '1.0.1', '<')) {
                if (!$installer->tableExists('mageplaza_gdpr_download_logs')) {
                    $table = $installer->getConnection()
                        ->newTable($installer->getTable('mageplaza_gdpr_download_logs'))
                        ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ], 'Download Log ID')
                        ->addColumn('customer_name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Customer Name')
                        ->addColumn('customer_id', Table::TYPE_INTEGER, 255, ['nullable' => false], 'Customer ID')
                        ->addColumn('customer_email', Table::TYPE_TEXT, 255, ['nullable => false'], 'Customer Email')
                        ->addColumn('store_id', Table::TYPE_INTEGER, 255, ['nullable' => false], 'Store Id')
                        ->addColumn('customer_group_id', Table::TYPE_INTEGER, null, [], 'Customer Group Id')
                        ->addColumn('file_type', Table::TYPE_TEXT, 255, [], 'File Type')
                        ->addColumn('path', Table::TYPE_TEXT, 255, [], 'Path')
                        ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                        ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                        ->setComment('Personal Data Download Logs');

                    $installer->getConnection()->createTable($table);
                }

                if (!$installer->tableExists('mageplaza_gdpr_delete_customer_logs')) {
                    $table = $installer->getConnection()
                        ->newTable($installer->getTable('mageplaza_gdpr_delete_customer_logs'))
                        ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ], 'Delete ID')
                        ->addColumn('customer_name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Customer Name')
                        ->addColumn('customer_id', Table::TYPE_INTEGER, 255, ['nullable' => false], 'Customer ID')
                        ->addColumn('customer_email', Table::TYPE_TEXT, 255, ['nullable => false'], 'Customer Email')
                        ->addColumn('status', Table::TYPE_BOOLEAN, null, [], 'Status')
                        ->addColumn('store_id', Table::TYPE_INTEGER, 255, ['nullable' => false])
                        ->addColumn('order_count', Table::TYPE_INTEGER, null, [], 'Order Count')
                        ->addColumn('grand_total', Table::TYPE_FLOAT, 255, [], 'Grand Total')
                        ->addColumn('refunded', Table::TYPE_FLOAT, null, [], 'Refunded')
                        ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                        ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                        ->setComment('Customer Log Delete Your Account');

                    $installer->getConnection()->createTable($table);
                }
            }
        }

        $installer->endSetup();
    }
}
