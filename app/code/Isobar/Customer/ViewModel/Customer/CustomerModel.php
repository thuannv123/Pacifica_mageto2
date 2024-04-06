<?php

namespace Isobar\Customer\ViewModel\Customer;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class CustomerModel
 * @package Isobar\Customer\ViewModel\Customer
 */
class CustomerModel implements ArgumentInterface
{
    /** @var CustomerFactory  */
    protected $customerFactory;

    /**
     * CustomerModel constructor.
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerFactory $customerFactory
    ) {
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param $customerData
     * @return bool
     * @throws LocalizedException
     */
    public function isSocialAccount($customerData)
    {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($customerData->getWebsiteId());
        $customer->loadByEmail($customerData->getEmail());

        if ($customer->getPasswordHash()) {
            return false;
        }

        return true;
    }
}
