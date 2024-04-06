<?php

namespace Isobar\Checkout\Setup\Patch\Data;

use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\CheckoutCore\Model\ResourceModel\Field as AmastyCheckoutFieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\Field\CollectionFactory as AmastyCheckoutFieldCollectionFactory;
use Amasty\CheckoutCore\Model\FieldFactory as AmastyCheckoutFieldFactory;

class UpdateSubDistrictToAmastyCheckOutField implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var Attribute
     */
    protected $_eavAttribute;

    private AmastyCheckoutFieldResource $amastyCheckoutFieldResource;

    private AmastyCheckoutFieldFactory $amastyCheckoutFieldFactory;

    private AmastyCheckoutFieldCollectionFactory $amastyCheckoutFieldCollectionFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Attribute $eavAttribute
     * @param AmastyCheckoutFieldResource $amastyCheckoutFieldResource
     * @param AmastyCheckoutFieldFactory $amastyCheckoutFieldFactory
     * @param AmastyCheckoutFieldCollectionFactory $amastyCheckoutFieldCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Attribute $eavAttribute,
        AmastyCheckoutFieldResource $amastyCheckoutFieldResource,
        AmastyCheckoutFieldFactory $amastyCheckoutFieldFactory,
        AmastyCheckoutFieldCollectionFactory $amastyCheckoutFieldCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->_eavAttribute = $eavAttribute;
        $this->amastyCheckoutFieldResource = $amastyCheckoutFieldResource;
        $this->amastyCheckoutFieldFactory = $amastyCheckoutFieldFactory;
        $this->amastyCheckoutFieldCollectionFactory = $amastyCheckoutFieldCollectionFactory;
    }

    /**
     * @return UpdateSubDistrictToAmastyCheckOutField|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $subDistrictId = $this->_eavAttribute->getIdByCode('customer_address', 'subdistrict');

        $fieldStore = $this->amastyCheckoutFieldCollectionFactory->create()
            ->addFieldToFilter('attribute_id', $subDistrictId)->getFirstItem();


        $field = $this->amastyCheckoutFieldFactory->create();
        $field->setData([
            'id' => $fieldStore->getData('id'),
            'required' => 1,
        ]);

        $this->amastyCheckoutFieldResource->save($field);

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }
}
