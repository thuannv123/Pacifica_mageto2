<?php

namespace Isobar\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;

class ProductAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeManagementInterface
     */
    private $productAttributeManagement;

    /**
     * AddProductAttributeExample constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param  \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Catalog\Api\ProductAttributeManagementInterface $productAttributeManagement
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Catalog\Api\ProductAttributeManagementInterface $productAttributeManagement,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->eavSetupFactory = $eavSetupFactory;
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
        $this->moduleDataSetup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, 'Default');
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

        $eavSetup->removeAttribute(Product::ENTITY, 'material');

        /* ADD ATTRIBUTE */
        $eavSetup->addAttribute(
            Product::ENTITY,
            'material',
            [
                'label' => 'Material',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('material');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            395
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'brand',
            [
                'label' => 'Brand',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('brand');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            450
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'class',
            [
                'label' => 'Class',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('class');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            340
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'class_desc',
            [
                'label' => 'Class description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('class_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            350
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'collection',
            [
                'label' => 'Collection',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('collection');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            380
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'collecttion_desc',
            [
                'label' => 'Collection description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('collecttion_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            385
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_desc',
            [
                'label' => 'Color Description',
                'input' => 'text',
                'required' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('color_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            315
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_group_desc',
            [
                'label' => 'Color group description',
                'input' => 'select',
                'required' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('color_group_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            375
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'country_code',
            [
                'label' => 'Country Code',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('country_code');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            440
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'default_alias',
            [
                'label' => 'Default Alias',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('default_alias');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            300
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'department',
            [
                'label' => 'Department',
                'input' => 'text',
                'required' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('department');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            330
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'dept_class',
            [
                'label' => 'Department Class',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('dept_class');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            345
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'department_desc',
            [
                'label' => 'Department Description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('department_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            335
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'dept_class_sub',
            [
                'label' => 'Department sub class',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('dept_class_sub');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            360
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'factory_type',
            [
                'label' => 'Factory Type',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('factory_type');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            435
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'gender_desc',
            [
                'label' => 'Gender description',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('gender_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            420
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'handbag_size',
            [
                'label' => 'Handbag Size',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('handbag_size');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            425
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'hardware_color',
            [
                'label' => 'Hardware Color',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('hardware_color');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            460
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'hardware_color_desc',
            [
                'label' => 'Hardware Color Description',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('hardware_color_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            465
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'intro_date',
            [
                'label' => 'Introduction Date',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('intro_date');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            370
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'material_desc',
            [
                'label' => 'Material description',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('material_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            400
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'silhouette',
            [
                'label' => 'Silhouette',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('silhouette');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            405
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'silhouette_desc',
            [
                'label' => 'Silhouette Description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('silhouette_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            410
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'source_code',
            [
                'label' => 'Source Code',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('source_code');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            445
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'style',
            [
                'label' => 'Style',
                'input' => 'text',
                'required' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('style');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            305
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'style_color',
            [
                'label' => 'Style Color',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('style_color');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            320
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'style_desc',
            [
                'label' => 'Style Description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('style_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            310
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'style_group',
            [
                'label' => 'Style group',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'searchable' => false,
                'comparable' => false,
                'filterable' => true,
                'filterable_in_search' => true,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('style_group');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            430
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'sub_class',
            [
                'label' => 'Sub Class',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('sub_class');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            355
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'sub_class_desc',
            [
                'label' => 'Sub class description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('sub_class_desc');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            365
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'subcollection',
            [
                'label' => 'Sub collection',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => false,
                'default' => null,
                'unique' => false,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => false,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('subcollection');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            390
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'upccode',
            [
                'label' => 'UPC Code',
                'input' => 'text',
                'required' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'system' => true,
                'default' => null,
                'unique' => true,
                'validate_rules' => null,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => false,
                'searchable' => true,
                'comparable' => false,
                'filterable' => false,
                'filterable_in_search' => false,
                'used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'used_for_sort_by' => false,
                'user_defined' => true,
            ]
        );
        $model = $this->productAttributeRepository->get('upccode');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            455
        );

        /* UPDATE PRODUCT ATTRIBUTE */
        //Color
        $eavSetup->updateAttribute(
            'catalog_product',
            'color',
            [
                'is_required' => true,
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_filterable_in_grid' => false,
                'is_filterable' => false,
                'is_used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
            ]
        );

        //Gender
        $eavSetup->updateAttribute(
            'catalog_product',
            'gender',
            [
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_filterable_in_search' => true,
                'is_used_for_promo_rules' => false,
                'is_visible_on_front' => false,
            ]
        );
        $model = $this->productAttributeRepository->get('gender');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            415
        );

        //Price
        $eavSetup->updateAttribute(
            'catalog_product',
            'price',
            [
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable' => false,
                'is_comparable' => false,
                'is_filterable' => false,
                'is_filterable_in_search' => true,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
            ]
        );

        //Size
        $eavSetup->updateAttribute(
            'catalog_product',
            'size',
            [
                'is_global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_filterable_in_search' => true,
                'is_used_for_promo_rules' => false,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
            ]
        );
        $model = $this->productAttributeRepository->get('size');
        $this->productAttributeManagement->assign(
            $attributeSetId,
            $attributeGroupId,
            $model->getAttributeCode(),
            325
        );

        //Status
        $eavSetup->updateAttribute(
            'catalog_product',
            'status',
            [
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable' => false,
                'is_filterable' => true,
                'is_filterable_in_search' => true,
                'is_html_allowed_on_front' => true,
                'used_in_product_listing' => false,
            ]
        );

        /* ADD OPTIONS */
        //Gender
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'gender');
        $options = [
            'values' => [
                'MEN',
                'WMN',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Gender description
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'gender_desc');
        $options = [
            'values' => [
                'MEN',
                'WMN',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Factory Type
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'factory_type');
        $options = [
            'values' => [
                'No Factory',
                'Factory Exclusive',
                'Full Price to Factory Store',
                'Factory Exclusive Design',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Color group description
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'color_group_desc');
        $options = [
            'values' => [
                'Multi',
                'Red',
                'White',
                'Blue',
                'Yellow',
                'Green',
                'Purple',
                'Black',
                'Pink',
                'Beige',
                'Grey',
                'Brown',
                'Orange',
                'Silver',
                'Denim',
                'Misc',
                'null',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Status
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'status');
        $options = [
            'values' => [
                'Released',
                'Dropped from Line/Cancelled',
                'Dropped',
                'Inactive',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Material description
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'material_desc');
        $options = [
            'values' => [
                'No',
                'Leather',
                'Other Leather',
                'PVC',
                'Polysatin',
                'Other',
                'Cubic Zirconia',
                'Cotton',
                'Mixed Material',
                'Silk',
                'Jacquard',
                'Suede',
                'Nylon',
                'Cotton Twill',
                'PU Split Leather',
                'Wool',
                'Logo Reps',
                'Alligator',
                'Denim',
                'Synthetic',
                'Ostrich',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Handbag Size
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'handbag_size');
        $options = [
            'values' => [
                'Mini',
                'Small',
                'Medium',
                'Large',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Hardware Color Description
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'hardware_color_desc');
        $options = [
            'values' => [
                'Black Copper',
                'Brass',
                'Silver',
                'Old Brass',
                'Black',
                'Sliver',
                'Nickel',
                'Black Antique Nickel',
                'Light Antique Nickel',
                'Gold',
                'Matte Black',
            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        //Size
        $attributeId = $eavSetup->getAttributeId('catalog_product', 'size');
        $options = [
            'values' => [
                'L',
                'M',
                'S',
                'XL',
                'XS',
                '00',
                '0',
                '2',
                '4',
                '6',
                '8',
                '10',
                '12',
                '14',
                'XXS',
                '42',
                '44',
                '46',
                '48',
                '50',
                '52',
                '54',
                '5.5 B',

            ],
            'attribute_id' => $attributeId,
        ];
        $eavSetup->addAttributeOption($options);

        $this->moduleDataSetup->endSetup();
        return $this;
    }
}
