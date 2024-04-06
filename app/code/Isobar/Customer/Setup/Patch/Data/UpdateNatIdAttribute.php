<?php

namespace Isobar\Customer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateNatIdAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CustomerSetup
     */
    private $customerSetupFactory;
    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer:: ENTITY, 'Nat_ID');

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'Nat_ID',
            [
                'label' => 'Customer National ID or Passport',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => true,
                'attribute_model' => \Magento\Customer\Model\Attribute::class,
                'backend' => \Isobar\Customer\Model\Customer\Attribute\Backend\NatID::class,
                'unique' => true,
                'default' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => true
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'Nat_ID');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();

        $customerSetup->updateAttribute('customer_address', 'region_id', ['is_required' => true]);
        $customerSetup->updateAttribute('customer_address', 'region', ['is_required' => true]);
        $customerSetup->updateAttribute('customer_address', 'postcode', ['is_required' => true]);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [\Isobar\Customer\Setup\Patch\Data\SubDistrictAttribute::class, \Isobar\Customer\Setup\Patch\Data\CustomerAttribute::class];
    }

    public function getAliases()
    {
        return [];
    }
}
