<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Setup\Patch\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class DropExistingForeignKey implements SchemaPatchInterface, PatchVersionInterface
{
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    public function apply()
    {

        $this->schemaSetup->startSetup();
        $tableName = $this->schemaSetup->getTable('beamcheckout_purchase');

        $existingForeignKeys = $this->schemaSetup->getConnection()->getForeignKeys($tableName);
        foreach ($existingForeignKeys as $key) {
            if ($key['FK_NAME'] == 'BEAMCHECKOUT_PURCHASE_ORDER_ID_SALES_ORDER_INCREMENT_ID') {
                $this->schemaSetup->getConnection()->dropForeignKey($key['TABLE_NAME'], $key['FK_NAME']);
            }
        }
        $this->schemaSetup->endSetup();
    }

    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.4.6';
    }

    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }
}
