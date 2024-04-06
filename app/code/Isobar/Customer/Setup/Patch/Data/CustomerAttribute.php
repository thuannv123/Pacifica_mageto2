<?php

namespace Isobar\Customer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class CustomerAttribute implements DataPatchInterface, PatchRevertableInterface
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

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet Set */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /* ADD ATTRIBUTE */
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'age_group',
            [
                'label' => 'Age Group',
                'user_defined' => true,
                'system' => false,
                'input' => 'select',
                'required' => false,
                'default' => null,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'age_group');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'alias',
            [
                'label' => 'Alias',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'alias');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'area_code',
            [
                'label' => 'Area Code',
                'user_defined' => true,
                'system' => true,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'area_code');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_checkout'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'balance_points',
            [
                'label' => 'Balance Points',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment'  => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'balance_points');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'cust_languagepref',
            [
                'label' => 'Customer Language Preference',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'cust_languagepref');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'customer_promo_cd',
            [
                'label' => 'Customer Promo Code',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_promo_cd');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'email_opt_in',
            [
                'label' => 'Email Opt In',
                'user_defined' => true,
                'system' => false,
                'source' => Boolean::class,
                'input' => 'boolean',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'visible' => true
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'email_opt_in');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'expiry_date',
            [
                'label' => 'Expiry Date',
                'user_defined' => true,
                'system' => false,
                'input' => 'date',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'expiry_date');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'home_phone',
            [
                'label' => 'Home Phone',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'home_phone');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'issue_date',
            [
                'label' => 'Issue Date',
                'user_defined' => true,
                'system' => false,
                'input' => 'date',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'issue_date');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'last_update_time',
            [
                'label' => 'Last Update Time',
                'user_defined' => true,
                'system' => false,
                'input' => 'date',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'last_update_time');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'member_no',
            [
                'label' => 'Member Number',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'member_no');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'member_status',
            [
                'label' => 'Member Status',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'member_status');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'member_type',
            [
                'label' => 'Member Type',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'member_type');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'membership_tier',
            [
                'label' => 'Membership Tier',
                'user_defined' => true,
                'system' => true,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'visible' => true
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'membership_tier');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'mobile_number',
            [
                'label' => 'Mobile Number',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'visible' => true
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'mobile_number');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'mobile_opt_in',
            [
                'label' => 'Mobile Opt In',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'used_in_forms' => ['customer_account_create', 'customer_account_edit'],
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'mobile_opt_in');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'no_dm',
            [
                'label' => 'No DM',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'no_dm');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'no_edm',
            [
                'label' => 'No EDM',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'no_edm');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'no_phone',
            [
                'label' => 'No Phone',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'no_phone');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'no_sms',
            [
                'label' => 'No SMS',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'no_sms');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'staff_code',
            [
                'label' => 'Staff Code',
                'user_defined' => true,
                'system' => true,
                'input' => 'text',
                'required' => false,
                'default' => null,
                'validate_rules' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => false
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'staff_code');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'title',
            [
                'label' => 'Title',
                'user_defined' => true,
                'system' => false,
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'required' => true,
                'default' => null,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'is_used_for_customer_segment' => false,
                'sort_order' => null,
                'visible' => true
            ]
        );
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'title');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
        ]);
        $attribute->save();
        $customerSetup -> addAttribute(
            Customer:: ENTITY,
            'Nat_ID',
            [
                'label' => 'Customer National ID or Passport',
                'user_defined' => true,
                'system' => false,
                'input' => 'text',
                'required' => true,
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
        ]);
        $attribute->save();
        /* ADD OPTIONS */
        $attributeId = $customerSetup->getAttributeId('customer', 'title');
        $options = [
            'values' => [
                'Mr.',
                'Ms.',
                'Mrs.'
            ],
            'attribute_id' => $attributeId,
        ];
        $customerSetup->addAttributeOption($options);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'date');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
