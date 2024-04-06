<?php
namespace Isobar\SocialLogin\Model;

use Isobar\SocialLogin\Api\AccountSearchInterface;
use Isobar\SocialLogin\Api\Data\AccountInterface;
use Isobar\SocialLogin\Model\ResourceModel\Account\Collection;
use Isobar\SocialLogin\Model\ResourceModel\Account\CollectionFactory as AccountCollectionFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Config\Share as ShareConfig;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AccountSearch.
 */
class AccountSearch implements AccountSearchInterface
{
    /**
     * @var AccountCollectionFactory
     */
    private $accountCollectionFactory;

    /**
     * @var ShareConfig
     */
    private $shareConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param AccountCollectionFactory $accountCollectionFactory
     * @param ShareConfig $shareConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        AccountCollectionFactory $accountCollectionFactory,
        ShareConfig $shareConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->shareConfig = $shareConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySocialId($type, $socialId, $websiteId = null)
    {
        /** @var Collection $accountCollection */
        $accountCollection = $this->accountCollectionFactory->create();
        $accountCollection->addFieldToFilter(AccountInterface::TYPE, $type)
            ->addFieldToFilter(AccountInterface::SOCIAL_ID, $socialId)
            ->join(
                ['cs' => $accountCollection->getTable('customer_entity')],
                'main_table.customer_id = cs.entity_id',
                ['website_id']
            );

        if ($this->shareConfig->isWebsiteScope()) {
            $websiteId = $websiteId ?: $this->storeManager->getWebsite()->getId();
            $accountCollection->addFieldToFilter(CustomerInterface::WEBSITE_ID, $websiteId);
        }

        /** @var AccountInterface $account */
        $account = $accountCollection->getFirstItem();

        if (!$account->getId()) {
            throw new NoSuchEntityException(__('Account with social_id "%1" does not exist.', $socialId));
        }

        return $account;
    }
}
