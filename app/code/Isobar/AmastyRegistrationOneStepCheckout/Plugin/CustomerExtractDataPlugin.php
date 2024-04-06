<?php

namespace Isobar\AmastyRegistrationOneStepCheckout\Plugin;

use Amasty\CheckoutCore\Model\Sales\OrderCustomerExtractor;
use Amasty\CheckoutCore\Api\QuotePasswordsRepositoryInterface;
use Amasty\CheckoutCore\Model\QuotePasswordsFactory;
use Amasty\CheckoutCore\Model\QuotePasswords;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CustomerExtractDataPlugin
{
     /**
     * @var QuotePasswordsRepositoryInterface
     */
    private $quotePasswordsRepository;

    /**
     * @var \Amasty\CheckoutCore\Model\QuotePasswordsFactory
     */
    private $quotePasswordsFactory;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * @param QuotePasswordsRepositoryInterface $quotePasswordsRepository
     * @param QuotePasswordsFactory $quotePasswordsFactory
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        QuotePasswordsRepositoryInterface $quotePasswordsRepository,
        QuotePasswordsFactory $quotePasswordsFactory,
        TimezoneInterface $timezone
    )
    {
        $this->quotePasswordsRepository = $quotePasswordsRepository;
        $this->quotePasswordsFactory = $quotePasswordsFactory;
        $this->timezone = $timezone;
    }

    /**
     * @param OrderCustomerExtractor $subject
     * @param CustomerInterface $result
     * @param OrderInterface $order
     * @return CustomerInterface
     */
    public function afterExtract(OrderCustomerExtractor $subject, CustomerInterface $result, OrderInterface $order): CustomerInterface
    {
        /** @var QuotePasswords $passwordQuote */
        $passwordQuote = $this->getPasswordQuote($order->getQuoteId());

        if ($passwordQuote->hasData()) {
            $dob = $this->timezone->date($passwordQuote->getData('dob'))->format('Y-m-d');
            $result->setFirstname($passwordQuote->getData('firstname'));
            $result->setLastname($passwordQuote->getData('lastname'));
            $result->setDob($dob);
            $result->setTaxvat($passwordQuote->getData('taxvat'));
            $result->setGender($passwordQuote->getData('gender'));
            $result->setData('mobile_number', $passwordQuote->getData('mobile_number'));

            $result->setCustomAttribute('mobile_number', $passwordQuote->getData('mobile_number'));

            $extensionAttributes = $result->getExtensionAttributes();
            $extensionAttributes->setAssistanceAllowed((int)$passwordQuote->getData('assistance_allowed'));
            if($passwordQuote->getData('is_subscribed') != null){
                $extensionAttributes->setIsSubscribed((int)$passwordQuote->getData('is_subscribed'));
            }

            $result->setExtensionAttributes($extensionAttributes);

            foreach ($result->getAddresses() as &$address) {
                $addressData = $address->__toArray();

                if (!$addressData['subdistrict']) {
                    $address->setData('subdistrict', '0');
                    $address->setCustomAttribute('subdistrict', '0');
                }
            }
        }

        return $result;
    }

    /**
     * @param int $quoteId
     *
     * @return QuotePasswords
     */
    private function getPasswordQuote($quoteId)
    {
        try {
            $quotePassword = $this->quotePasswordsRepository->getByQuoteId($quoteId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $quotePassword = $this->quotePasswordsFactory->create();
        }

        /** @var QuotePasswords $quotePassword */
        return $quotePassword;
    }
}
