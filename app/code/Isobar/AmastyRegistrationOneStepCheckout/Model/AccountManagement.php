<?php

namespace Isobar\AmastyRegistrationOneStepCheckout\Model;

use Isobar\AmastyRegistrationOneStepCheckout\Api\AccountManagementInterface;
use Isobar\AmastyRegistrationOneStepCheckout\Api\Data\CustomerRegisterInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Amasty\CheckoutCore\Model\Config;
use Amasty\CheckoutCore\Api\QuotePasswordsRepositoryInterface;
use Amasty\CheckoutCore\Model\QuotePasswords;
use Amasty\CheckoutCore\Model\QuotePasswordsFactory;
use \Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;

class AccountManagement implements AccountManagementInterface
{
    private Config $config;

    private QuoteIdMaskFactory $quoteIdMaskFactory;

    private QuotePasswordsRepositoryInterface $quotePasswordsRepository;

    private QuotePasswordsFactory $quotePasswordsFactory;

    private EncryptorInterface $encryptor;

    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuotePasswordsRepositoryInterface $quotePasswordsRepository
     * @param QuotePasswordsFactory $quotePasswordsFactory
     * @param EncryptorInterface $encryptor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config                            $config,
        QuoteIdMaskFactory                $quoteIdMaskFactory,
        QuotePasswordsRepositoryInterface $quotePasswordsRepository,
        QuotePasswordsFactory             $quotePasswordsFactory,
        EncryptorInterface                $encryptor,
        LoggerInterface                   $logger
    )
    {
        $this->config = $config;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quotePasswordsRepository = $quotePasswordsRepository;
        $this->quotePasswordsFactory = $quotePasswordsFactory;
        $this->encryptor = $encryptor;
        $this->logger = $logger;
    }

    /**
     * @param string $cartId
     * @param string[] $customerRegister
     * @return bool
     */
    public function saveRegister($cartId, $customerRegister)
    {
        if ($this->config->getAdditionalOptions('create_account') === '2'
            && $customerRegister['password'] === $customerRegister['password-confirmation']) {
            try {
                /** @var QuoteIdMask $quoteIdMask */
                $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

                /** @var QuotePasswords $quotePassword */
                $quotePassword = $this->getPasswordQuote($quoteIdMask->getQuoteId());

                $passwordHash = $this->createPasswordHash($customerRegister['password']);
                $quotePassword->setPasswordHash($passwordHash);
                $quotePassword->setQuoteId($quoteIdMask->getQuoteId());
                $quotePassword->setData('firstname', $customerRegister['firstname']);
                $quotePassword->setData('lastname', $customerRegister['lastname']);
                if(isset($customerRegister['is_subscribed'])){
                    $quotePassword->setData('is_subscribed', $customerRegister['is_subscribed']);
                }
                $quotePassword->setData('dob', $customerRegister['dob']);
                $quotePassword->setData('taxvat', $customerRegister['taxvat']);
                $quotePassword->setData('gender', $customerRegister['gender']);
                $quotePassword->setData('nat_id', isset($customerRegister['Nat_ID']) ? $customerRegister['Nat_ID'] : '');
                $quotePassword->setData('mobile_number', $customerRegister['mobile_number']);
                $quotePassword->setData('assistance_allowed', $customerRegister['assistance_allowed']);

                $this->quotePasswordsRepository->save($quotePassword);
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }

        return true;

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

    /**
     * Create a hash for the given password
     *
     * @param string $password
     * @return string
     */
    private function createPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
    }
}