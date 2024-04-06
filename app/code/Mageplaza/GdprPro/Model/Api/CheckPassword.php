<?php

namespace Mageplaza\GdprPro\Model\Api;

use Exception;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CheckPassword
 * @package Mageplaza\GdprPro\Model\Api
 */
class CheckPassword
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;
    /**
     * @var CustomerRegistry
     */
    protected $_customerRegistry;

    /**
     * @var Encryptor
     */
    protected $_encryptor;

    /**
     * CheckPassword constructor.
     *
     * @param Encryptor $encryptor
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Encryptor $encryptor,
        CustomerRegistry $customerRegistry,
        CustomerRepositoryInterface $customerRepository

    ) {
        $this->_encryptor          = $encryptor;
        $this->_customerRegistry   = $customerRegistry;
        $this->_customerRepository = $customerRepository;

    }

    /**
     * @param int $customerIdLogged
     * @param string $email
     * @param string $password
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function checkPassword($customerIdLogged, $email, $password)
    {
        if ($this->_customerRepository->getById($customerIdLogged)->getEmail() !== $email) {
            return false;
        }

        $customerSecure       = $this->_customerRegistry->retrieveSecureData($customerIdLogged);
        $customerPasswordHash = $customerSecure->getPasswordHash();

        return $this->_encryptor->validateHash($password, $customerPasswordHash);
    }
}
