<?php

namespace Isobar\Megamenu\Setup\Patch\Data;

use Isobar\Megamenu\Helper\Data;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class RemoveAttributeMenuData
 * @package Isobar\Megamenu\Setup\Patch\Data
 */
class RemoveAttributeMenuData implements DataPatchInterface, PatchVersionInterface
{
    const  CODE_SHOW = 'mm_show_subcategories';
    const  CODE_NUMBER = 'mm_number_of_subcategories';
    const  CODE_VIEW = 'mm_view_mode';

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * AddMegaMenuAttributes constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
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
        return "2.0.5";
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $catalogSetup->removeAttribute(Category::ENTITY, self::CODE_SHOW);
        $catalogSetup->removeAttribute(Category::ENTITY, self::CODE_NUMBER);
        $catalogSetup->removeAttribute(Category::ENTITY, self::CODE_VIEW);
    }
}
