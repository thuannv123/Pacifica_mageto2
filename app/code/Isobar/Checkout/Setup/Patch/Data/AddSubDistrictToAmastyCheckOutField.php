<?php

namespace Isobar\Checkout\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddSubDistrictToAmastyCheckOutField implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->_eavAttribute = $eavAttribute;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $subDistrictId = $this->_eavAttribute->getIdByCode('customer_address', 'subdistrict');
        $data[] = [
            'attribute_id' => $subDistrictId,
            'label' => 'Sub District',
            'sort_order' => 90,
            'required' => 0,
            'width' => 100,
            'enabled' => 1,
            'store_id' => 0
        ];

        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('amasty_amcheckout_field'),
            ['attribute_id', 'label', 'sort_order', 'required', 'width', 'enabled', 'store_id'],
            $data
        );
        $this->moduleDataSetup->endSetup();
    }
    public function getAliases()
    {
        return [];
    }
    public static function getDependencies()
    {
        return [];
    }
}
