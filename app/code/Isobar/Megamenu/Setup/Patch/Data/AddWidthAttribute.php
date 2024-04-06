<?php

namespace Isobar\Megamenu\Setup\Patch\Data;

use Isobar\Megamenu\Helper\Data;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class AddWidthAttribute
 * @package Isobar\Megamenu\Setup\Patch\Data
 */
class AddWidthAttribute implements PatchVersionInterface, DataPatchInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddWidthAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Data $helperData
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Data $helperData,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->helperData = $helperData;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $groupName = $this->helperData->getAttributeGroup();

        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_width',
            [
                'type'          => 'varchar',
                'label'         => 'Width',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'default'       =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  170,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return "2.0.7";
    }
}
