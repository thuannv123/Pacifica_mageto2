<?php

namespace Marvelic\Quote\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class QuoteAddressValidator
{
    protected $customerSession;

    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }
	public function beforeValidateForCart(
        \Magento\Quote\Model\QuoteAddressValidator $subject, 
        CartInterface $cart, 
        AddressInterface $address
    ) {
        if ($this->customerSession->isLoggedIn() && $cart->getCustomerIsGuest()) {
            $cart->setCustomerIsGuest(0);
        }
        return [$cart, $address];
    }
}
