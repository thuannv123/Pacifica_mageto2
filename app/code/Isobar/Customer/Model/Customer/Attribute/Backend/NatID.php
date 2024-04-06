<?php

namespace Isobar\Customer\Model\Customer\Attribute\Backend;

use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class NatID extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Prepare object for save
     *
     * @param DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave($object)
    {
        $this->validate($object);
        return parent::beforeSave($object);
    }
}
