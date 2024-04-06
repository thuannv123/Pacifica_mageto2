<?php

namespace Isobar\BirkenstockSetup\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpdateAttributesTextIsFilterable implements DataPatchInterface
{
    const CATALOG_PRODUCT_ATTRIBUTES_UPDATE = [
        'color_id',
        'prod_ref',
        'style_color_id',
        'style_id',
        'width_id'
    ];

    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup, EavSetupFactory $eavSetupFactory)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [
            AddColorGroupDescIdAttributeToProduct::class,
            AddColorIdAttributeToProduct::class,
            AddProdRefAttributeToProduct::class,
            AddStyleColorIdAttributeToProduct::class,
            AddStyleIdAttributeToProduct::class,
            AddWidthAttributeToProduct::class
        ];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return UpdateAttributesTextIsFilterable|void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        foreach (self::CATALOG_PRODUCT_ATTRIBUTES_UPDATE as $attributeCode) {
            $eavSetup->updateAttribute(
                Product::ENTITY,
                $attributeCode,
                'is_filterable',
                '0'
            );

            $eavSetup->updateAttribute(
                Product::ENTITY,
                $attributeCode,
                'is_filterable_in_search',
                '0'
            );
        }
        $this->moduleDataSetup->endSetup();
    }
}
