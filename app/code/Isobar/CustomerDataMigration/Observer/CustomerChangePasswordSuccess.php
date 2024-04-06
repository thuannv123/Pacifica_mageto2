<?php

namespace Isobar\CustomerDataMigration\Observer;

class CustomerChangePasswordSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Isobar\CustomerDataMigration\Model\ResourceModel\Customer
     */
    protected $_customer;

    /**
     * CustomerChangePasswordSuccess constructor.
     * @param \Isobar\CustomerDataMigration\Model\ResourceModel\Customer $customer
     */
    public function __construct(
        \Isobar\CustomerDataMigration\Model\ResourceModel\Customer $customer
    ) {
        $this->_customer = $customer;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getData('customer');
        $this->_customer->updateCustomerToChangedPassword($customer->getId());
    }
}
