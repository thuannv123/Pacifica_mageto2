<?php

namespace Isobar\Customer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateCustomerAttribute implements DataPatchInterface
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

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'gender');
        $attribute->addData([
            'used_in_forms' => ['customer_account_create', 'customer_account_edit', 'adminhtml_customer'],
        ]);
        $attribute->save();

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'group_id');
        $attribute->addData([
            'used_in_forms' => ['customer_account_edit', 'adminhtml_customer'],
        ]);
        $attribute->save();

        $customerSetup->updateAttribute(Customer::ENTITY, 'created_at', ['is_visible' => '0']);
        $customerSetup->updateAttribute('customer_address', 'city', ['sort_order' => '80']);
        $customerSetup->updateAttribute('customer_address', 'country_id', ['sort_order' => '90']);
        $customerSetup->updateAttribute('customer_address', 'region', ['sort_order' => '100', 'is_required' => '0']);
        $customerSetup->updateAttribute('customer_address', 'region_id', ['sort_order' => '100', 'is_required' => '0']);
        $customerSetup->updateAttribute(Customer::ENTITY, 'disable_auto_group_change', ['is_visible' => '0']);
        $customerSetup->updateAttribute(Customer::ENTITY, 'dob', ['is_required' => '1']);
        $customerSetup->updateAttribute(Customer::ENTITY, 'gender', ['is_required' => '1']);
        $customerSetup->updateAttribute(Customer::ENTITY, 'updated_at', ['is_visible' => '0']);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [\Isobar\Customer\Setup\Patch\Data\CustomerAddressAttribute::class, \Isobar\Customer\Setup\Patch\Data\CustomerAttribute::class];
    }

    public function getAliases()
    {
        return [];
    }
}
