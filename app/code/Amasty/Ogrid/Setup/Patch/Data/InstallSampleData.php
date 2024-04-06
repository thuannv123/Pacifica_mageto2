<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Setup\Patch\Data;

use Amasty\Ogrid\Model\Indexer\Attribute\Processor;
use Magento\Eav\Model\Config;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var Processor
     */
    protected $productAttributesIndexerProcessor;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $setup,
        Config $eavConfig,
        Processor $productAttributesIndexerProcessor
    ) {
        $this->moduleResource = $moduleResource;
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
        $this->productAttributesIndexerProcessor = $productAttributesIndexerProcessor;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_Ogrid');

        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install fixtures, because setup_version is a marker.
        if (!$setupDataVersion) {
            $this->install($this->setup);
        }
    }

    private function install(ModuleDataSetupInterface $setup)
    {
        $columns = [
            'attribute_id',
            'attribute_code',
            'frontend_label'
        ];

        $entityTypeId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getEntityTypeId();

        $select = $setup->getConnection()->select()->from(
            $setup->getTable('eav_attribute'),
            []
        )->where(
            'entity_type_id = ?',
            $entityTypeId
        )->where(
            'attribute_code in (?)',
            ['thumbnail', 'description', 'activity', 'category_gear']
        )->columns($columns);

        $query = $setup->getConnection()
            ->insertFromSelect($select, $setup->getTable('amasty_ogrid_attribute'), $columns);

        $setup->getConnection()->query($query);

        $this->productAttributesIndexerProcessor->markIndexerAsInvalid();
    }
}
