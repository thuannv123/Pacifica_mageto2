<?php
namespace Marvelic\CustomTableData\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ResourceConnection;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ResourceConnection $resourceConnection
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->syncOrderState();
        }
        $setup->endSetup();
    }

    private function syncOrderState(){
        $connection = $this->resourceConnection->getConnection();
        $tableSalesOrderGrid = $connection->getTableName('sales_order_grid');
        $tableSalesOrder = $connection->getTableName('sales_order');
        $query = "UPDATE `" . $tableSalesOrderGrid . "` LEFT JOIN `" . $tableSalesOrder . "` ON `" . $tableSalesOrderGrid . "`.entity_id = `" . $tableSalesOrder . "`.entity_id SET `" . $tableSalesOrderGrid . "`.status = `" . $tableSalesOrder . "`.status WHERE `" . $tableSalesOrderGrid . "`.status != `" . $tableSalesOrder . "`.status";
        $connection->query($query);
    }
}