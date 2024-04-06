<?php

namespace Marvelic\SocialLogin\Block\Adminhtml\Customer\Edit\Tab\Social;

use Marvelic\SocialLogin\Api\Data\AccountInterface;
use Isobar\SocialLogin\Model\Account\ImageProvider;
use Marvelic\SocialLogin\Model\ResourceModel\Account\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;

/**
 * Class AccountsList
 */
class AccountsList extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $accountCollectionFactory;

    /**
     * @var \Marvelic\SocialLogin\Model\ResourceModel\Account\Collection
     */
    protected $accounts;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var ImageProvider
     */
    private $imageProvider;

    /**
     * @param Context $context
     * @param CollectionFactory $accountCollectionFactory
     * @param Registry $registry
     * @param ImageProvider $imageProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $accountCollectionFactory,
        Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        ImageProvider $imageProvider,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->imageProvider = $imageProvider;
    }

    /**
     * Get social links accounts
     *
     * @return \Marvelic\SocialLogin\Model\ResourceModel\Account\Collection
     */
    public function getAccounts()
    {
        if (!$this->accounts) {
            $collection = $this->initAccountCollection();
            $collection->addFieldToFilter(AccountInterface::CUSTOMER_ID, $this->getCustomerId());
            $this->accounts = $collection;
        }
        return $this->accounts;
    }

    /**
     * @return \Marvelic\SocialLogin\Model\ResourceModel\Account\Collection
     */
    protected function initAccountCollection()
    {
        return $this->accountCollectionFactory->create();
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    public function getCustomerSocial($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        return $customer;
    }
    public function getAccountImageUrl()
    {
        return $this->imageProvider->getPlaceholderImageUrl();
    }
}
