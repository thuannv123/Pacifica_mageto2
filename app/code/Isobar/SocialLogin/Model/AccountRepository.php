<?php
namespace Isobar\SocialLogin\Model;

use Isobar\SocialLogin\Api\AccountRepositoryInterface;
use Isobar\SocialLogin\Api\AccountSearchInterface;
use Isobar\SocialLogin\Api\Data\AccountInterface;
use Isobar\SocialLogin\Exception\InvalidCustomerException;
use Isobar\SocialLogin\Model\ResourceModel\Account\CollectionFactory as AccountCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AccountRepository.
 */
class AccountRepository implements AccountRepositoryInterface
{
    /**
     * @var ResourceModel\Account
     */
    protected $resource;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var AccountCollectionFactory
     */
    protected $accountCollectionFactory;

    /**
     * @var AccountSearchInterface
     */
    private $accountSearch;

    /**
     * @param ResourceModel\Account $resource
     * @param AccountFactory $accountFactory
     * @param AccountCollectionFactory $accountCollectionFactory
     * @param AccountSearchInterface $accountSearch
     */
    public function __construct(
        ResourceModel\Account $resource,
        AccountFactory $accountFactory,
        AccountCollectionFactory $accountCollectionFactory,
        AccountSearchInterface $accountSearch
    ) {
        $this->resource = $resource;
        $this->accountFactory = $accountFactory;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->accountSearch = $accountSearch;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountInterface $account)
    {
        $this->validate($account);

        try {
            $this->resource->save($account);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function get($accountId)
    {
        /** @var AccountInterface $account */
        $account = $this->accountFactory->create();
        $this->resource->load($account, $accountId);
        if (!$account->getId()) {
            throw new NoSuchEntityException(__('Account with id "%1" does not exist.', $accountId));
        }
        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySocialId($type, $socialId, $websiteId = null)
    {
        return $this->accountSearch->getBySocialId($type, $socialId, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccountInterface $account)
    {
        try {
            $this->resource->delete($account);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Validate account
     *
     * @param AccountInterface $account
     * @return bool
     * @throws InvalidSocialAccountException
     */
    protected function validate(AccountInterface $account)
    {
        if ($this->isSocialAccountExist($account)) {
            throw new InvalidCustomerException(__('Social account already exists'));
        }

        return true;
    }

    /**
     * Is social account exist
     *
     * @param AccountInterface $account
     * @return bool
     */
    protected function isSocialAccountExist(AccountInterface $account)
    {
        try {
            $existAccount = $this->getBySocialId($account->getType(), $account->getSocialId());
            $isAccountExist = $existAccount->getId() !== $account->getId();
        } catch (NoSuchEntityException $e) {
            $isAccountExist = false;
        }
        return $isAccountExist;
    }
}
