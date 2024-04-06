<?php
namespace Isobar\SocialLogin\Block\Adminhtml\Customer\Edit\Tab\Social;

use Isobar\SocialLogin\Api\Data\AccountInterface;
use Isobar\SocialLogin\Model\Account\ImageProvider;
use Isobar\SocialLogin\Model\ResourceModel\Account\CollectionFactory;
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
     * @var \Isobar\SocialLogin\Model\ResourceModel\Account\Collection
     */
    protected $accounts;

    /**
     * @var Registry
     */
    protected $registry;

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
        ImageProvider $imageProvider,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->registry = $registry;
        $this->imageProvider = $imageProvider;
    }

    /**
     * Get social links accounts
     *
     * @return \Isobar\SocialLogin\Model\ResourceModel\Account\Collection
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
     * @return \Isobar\SocialLogin\Model\ResourceModel\Account\Collection
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

    /**
     * Get account image url.
     *
     * @param AccountInterface $account
     * @return string
     */
    public function getAccountImageUrl(AccountInterface $account)
    {
        return $this->imageProvider->getAccountImageUrl($account);
    }
}
