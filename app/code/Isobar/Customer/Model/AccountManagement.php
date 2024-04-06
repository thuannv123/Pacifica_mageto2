<?php

namespace Isobar\Customer\Model;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterfaceFactory;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\AccountManagement as AccountManagementCustomer;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ForgotPasswordToken\GetCustomerByToken;
use Magento\Customer\Model\Metadata\Validator;
use Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Random;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils as StringHelper;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use \Isobar\CustomerDataMigration\Helper\Data;
/**
 * Class AccountManagement
 * @package Isobar\Customer\Model
 */
class AccountManagement extends AccountManagementCustomer
{
    /** @var CustomerFactory  */
    protected $customerFactory;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var CredentialsValidator  */
    protected $credentialsValidator;

    /** @var CustomerRegistry  */
    protected $customerRegistry;

    /** @var SessionCleanerInterface  */
    protected $sessionCleaner;

    /** @var AddressRegistry  */
    protected $addressRegistry;
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var ManagerInterface
     */
    private $getByToken;
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * AccountManagement constructor.
     *
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param FilterBuilder $filterBuilder
     * @param CustomerFactory $customerFactory
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param Random $mathRandom
     * @param Validator $validator
     * @param ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerMetadataInterface $customerMetadataService
     * @param CustomerRegistry $customerRegistry
     * @param PsrLogger $logger
     * @param Encryptor $encryptor
     * @param ConfigShare $configShare
     * @param StringHelper $stringHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param DataObjectProcessor $dataProcessor
     * @param Registry $registry
     * @param CustomerViewHelper $customerViewHelper
     * @param DateTime $dateTime
     * @param CustomerModel $customerModel
     * @param ObjectFactory $objectFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param CredentialsValidator $credentialsValidator
     * @param DateTimeFactory $dateTimeFactory
     * @param AccountConfirmation $accountConfirmation
     * @param SessionManagerInterface $sessionManager
     * @param SaveHandlerInterface $saveHandler
     * @param CollectionFactory $visitorCollectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AddressRegistry $addressRegistry
     * @param GetCustomerByToken $getByToken
     * @param AllowedCountries $allowedCountriesReader
     * @param SessionCleanerInterface $sessionCleaner
     */
    public function __construct(
        Data $helperData,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        CustomerFactory $customerFactory,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        Random $mathRandom,
        Validator $validator,
        ValidationResultsInterfaceFactory $validationResultsDataFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerMetadataInterface $customerMetadataService,
        CustomerRegistry $customerRegistry,
        PsrLogger $logger,
        Encryptor $encryptor,
        ConfigShare $configShare,
        StringHelper $stringHelper,
        CustomerRepositoryInterface $customerRepository,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        DataObjectProcessor $dataProcessor,
        Registry $registry,
        CustomerViewHelper $customerViewHelper,
        DateTime $dateTime,
        CustomerModel $customerModel,
        ObjectFactory $objectFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CredentialsValidator $credentialsValidator,
        DateTimeFactory $dateTimeFactory,
        AccountConfirmation $accountConfirmation,
        SessionManagerInterface $sessionManager,
        SaveHandlerInterface $saveHandler,
        CollectionFactory $visitorCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AddressRegistry $addressRegistry,
        GetCustomerByToken $getByToken,
        AllowedCountries $allowedCountriesReader,
        SessionCleanerInterface $sessionCleaner
    ) {
        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter,
            $credentialsValidator,
            $dateTimeFactory,
            $accountConfirmation,
            $sessionManager,
            $saveHandler,
            $visitorCollectionFactory,
            $searchCriteriaBuilder,
            $addressRegistry,
            $getByToken,
            $allowedCountriesReader,
            $sessionCleaner
        );
        $this->_helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->credentialsValidator = $credentialsValidator;
        $this->customerRegistry = $customerRegistry;
        $this->sessionCleaner = $sessionCleaner;
        $this->addressRegistry = $addressRegistry;
        $this->eventManager = $eventManager;
        $this->getByToken = $getByToken;
    }

    /**
     * Check and change password for customer who was register by social app.
     *
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws InvalidEmailOrPasswordException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function changePassword($email, $currentPassword, $newPassword)
    {
        try {
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
            $customer->loadByEmail($email);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        // if customer was not register by social app
        if ($customer->getPasswordHash()) {
            return parent::changePassword($email, $currentPassword, $newPassword);
        } else {
            return $this->changePasswordForCustomerSocial($email, $newPassword);
        }
    }

    /**
     * Change Password For Customer Social
     *
     * @param $customerEmail
     * @param $newPassword
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function changePasswordForCustomerSocial($customerEmail, $newPassword)
    {
        $customer = $this->customerRepository->get($customerEmail);
        $this->credentialsValidator->checkPasswordDifferentFromEmail($customerEmail, $newPassword);
        $this->checkPasswordStrength($newPassword);
        $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerSecure->setRpToken(null);
        $customerSecure->setRpTokenCreatedAt(null);
        $customerSecure->setPasswordHash($this->createPasswordHash($newPassword));
        $this->sessionCleaner->clearFor((int)$customer->getId());
        $this->disableAddressValidation($customer);
        $this->customerRepository->save($customer);

        return true;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }

    /**
     * @inheritdoc
     */
    public function authenticate($username, $password)
    {
        try {
            $customer = $this->customerRepository->get($username);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        $customerId = $customer->getId();

        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }
        try {
            $customerData = $this->customerFactory->create()->load($customerId);
            if (!empty($customerData) && !empty($customerData->getData('is_password_reset_require')) && $customerData->getData('is_password_reset_require') == 1
            && $customerData->getData('is_password_reset') != 1
            ) {
                throw new \Magento\Framework\Exception\CouldNotDeleteException(__($this->_helperData->getConfigMessage($this->_storeManager->getStore()->getId())));
            }
            $this->getAuthentication()->authenticate($customerId, $password);
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }
        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__("This account isn't confirmed. Verify and try again."));
        }

        $customerModel = $this->customerFactory->create()->updateData($customer);
        $this->eventManager->dispatch(
            'customer_customer_authenticated',
            ['model' => $customerModel, 'password' => $password]
        );

        $this->eventManager->dispatch('customer_data_object_login', ['customer' => $customer]);

        return $customer;
    }

    /**
     * @return \Magento\Customer\Model\AuthenticationInterface|mixed
     */
    private function getAuthentication()
    {

        return $this->authentication;
    }

    /**
     * @inheritdoc
     */
    public function resetPassword($email, $resetToken, $newPassword)
    {
        if (!$email) {
            $customer = $this->getByToken->execute($resetToken);
            $email = $customer->getEmail();
        } else {
            $customer = $this->customerRepository->get($email);
        }
        // No need to validate customer and customer address while saving customer reset password token
        $this->disableAddressValidation($customer);
        $this->setIgnoreValidationFlag($customer);

        //Validate Token and new password strength
        $this->validateResetPasswordToken($customer->getId(), $resetToken);
        $this->credentialsValidator->checkPasswordDifferentFromEmail(
            $email,
            $newPassword
        );
        $this->checkPasswordStrength($newPassword);
        //Update secure data
        $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerSecure->setRpToken(null);
        $customerSecure->setRpTokenCreatedAt(null);
        $customerSecure->setPasswordHash($this->createPasswordHash($newPassword));
        $this->sessionCleaner->clearFor((int)$customer->getId());
        $this->customerRepository->save($customer);
        $this->eventManager->dispatch('customer_change_password_success', ['customer' => $customer]);
        return true;
    }

    /**
     * @param CustomerModel $customer
     */
    private function setIgnoreValidationFlag($customer)
    {
        $customer->setData('ignore_validation_flag', true);
    }

    /**
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @return bool
     * @throws ExpiredException
     * @throws InputException
     * @throws InputMismatchException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateResetPasswordToken($customerId, $resetPasswordLinkToken)
    {
        if ($customerId !== null && $customerId <= 0) {
            throw new InputException(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['value' => $customerId, 'fieldName' => 'customerId']
                )
            );
        }

        if ($customerId === null) {
            //Looking for the customer.
            $customerId = $this->getByToken
                ->execute($resetPasswordLinkToken)
                ->getId();
        }
        if (!is_string($resetPasswordLinkToken) || empty($resetPasswordLinkToken)) {
            $params = ['fieldName' => 'resetPasswordLinkToken'];
            throw new InputException(__('"%fieldName" is required. Enter and try again.', $params));
        }
        $customerSecureData = $this->customerRegistry->retrieveSecureData($customerId);
        $rpToken = $customerSecureData->getRpToken();
        $rpTokenCreatedAt = $customerSecureData->getRpTokenCreatedAt();
        if (!Security::compareStrings($rpToken, $resetPasswordLinkToken)) {
            throw new InputMismatchException(__('The password token is mismatched. Reset and try again.'));
        } elseif ($this->isResetPasswordLinkTokenExpired($rpToken, $rpTokenCreatedAt)) {
            throw new ExpiredException(__('The password token is expired. Reset and try again.'));
        }
        return true;
    }
}
