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
 * Class AddMegaMenuAttributes
 * @package Isobar\Megamenu\Setup\Patch\Data
 */
class AddMegaMenuAttributes implements DataPatchInterface, PatchVersionInterface
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
    public static function getVersion()
    {
        return "2.0.4";
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
            'mm_turn_on',
            [
                'type'          => 'int',
                'label'         => 'Turn on megamenu for this category',
                'input'         => 'select',
                'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'default'       =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  10,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_image',
            [
                'type'          => 'varchar',
                'label'         => 'Category image',
                'input'         => 'image',
                'backend'       => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  50,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_label',
            [
                'type'          => 'varchar',
                'label'         => 'Label',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  110,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_label_background',
            [
                'type'          => 'varchar',
                'label'         => 'Label Background Color',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  120,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_configurator',
            [
                'type'          => 'text',
                'label'         => 'Configurator',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  130,
                'frontend'      => 'Isobar\Megamenu\Model\Attribute\Frontend\Configurator',
                'backend'       => 'Isobar\Megamenu\Model\Attribute\Backend\Configurator',
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_show_subcategories',
            [
                'type'          => 'int',
                'label'         => 'Show subcategories for this category',
                'input'         => 'select',
                'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'default'       =>  true,
                'group'         =>  $groupName,
                'sort_order'    =>  140,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_number_of_subcategories',
            [
                'type'          => 'varchar',
                'label'         => 'Number of subcategories to show (if empty show all)',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  150,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_view_mode',
            [
                'type' => 'varchar',
                'label' => 'View Mode',
                'input' => 'select',
                'source' => 'Isobar\Megamenu\Model\Category\Attribute\Source\ViewMode',
                'required' => false,
                'sort_order' => 160,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => $groupName,
                'default' =>  'static',
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_color',
            [
                'type'          => 'varchar',
                'label'         => 'Color',
                'input'         => 'text',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  120,
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_menu_type',
            [
                'type' => 'varchar',
                'label' => 'Menu Type',
                'input' => 'select',
                'source' => 'Isobar\Megamenu\Model\Category\Attribute\Source\MenuType',
                'required' => false,
                'sort_order' => 20,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => $groupName,
                'default' =>  'horizontal',
            ]
        );
        $catalogSetup->addAttribute(
            Category::ENTITY,
            'mm_background_image',
            [
                'type'          => 'varchar',
                'label'         => 'Category background image',
                'input'         => 'image',
                'backend'       => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'global'        =>  ScopedAttributeInterface::SCOPE_STORE,
                'required'      =>  false,
                'group'         =>  $groupName,
                'sort_order'    =>  55,
            ]
        );
    }
}
