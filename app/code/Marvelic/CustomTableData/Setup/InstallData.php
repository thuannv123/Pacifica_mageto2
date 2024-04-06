<?php

namespace Marvelic\CustomTableData\Setup;

use Exception;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\App\ResourceConnection;

class InstallData implements InstallDataInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    public function __construct(
         ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

     /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('setup_module');
        $query = "DELETE FROM `" . $table ."`  WHERE `" . $table . "`.module = 'Hoolah_Hoolah'";
        $connection->query($query);
    }
}