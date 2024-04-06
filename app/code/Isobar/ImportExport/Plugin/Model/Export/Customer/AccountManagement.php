<?php

namespace Isobar\ImportExport\Plugin\Model\Export\Customer;

use Magento\Customer\Api\Data\CustomerInterface;

class AccountManagement
{
    /**
     * @param $subject
     * @param CustomerInterface $customer
     * @param null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(
        $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        //$customer->setData('exported', 0);
        $customer->setCustomAttribute('exported', 0);
        return [$customer, $password, $redirectUrl];
    }
}
