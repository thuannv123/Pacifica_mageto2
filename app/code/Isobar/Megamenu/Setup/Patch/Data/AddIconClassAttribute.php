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

class AddIconClassAttribute implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * AddMegaMenuAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     * @param Data $helperData
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory,
        Data $helperData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->helperData = $helperData;
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
            'mm_icon_class',
            [
                'type'          => 'varchar',
                'label'         => 'Icon Class',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  120,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return "2.0.6";
    }
}
