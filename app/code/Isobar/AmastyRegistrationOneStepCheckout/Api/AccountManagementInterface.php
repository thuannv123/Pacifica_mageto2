<?php
namespace Isobar\AmastyRegistrationOneStepCheckout\Api;

use Isobar\AmastyRegistrationOneStepCheckout\Api\Data\CustomerRegisterInterface;

interface AccountManagementInterface
{
    /**
     * @param string $cartId
     * @param string[] $customerRegister
     * @return mixed
     */
    public function saveRegister($cartId, $customerRegister);
}