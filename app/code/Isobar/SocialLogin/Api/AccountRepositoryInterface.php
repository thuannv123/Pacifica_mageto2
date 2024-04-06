<?php
namespace Isobar\SocialLogin\Api;

use Isobar\SocialLogin\Api\Data\AccountInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Account Repository Interface
 */
interface AccountRepositoryInterface
{
    /**
     * Save Account
     * @param Data\AccountInterface $account
     * @return AccountInterface
     * @throws CouldNotSaveException
     */
    public function save(AccountInterface $account);

    /**
     * Get Account by id
     * @param int $accountId
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    public function get($accountId);

    /**
     * Get Account by id
     * @param string $type
     * @param string $socialId
     * @param int|null $websiteId
     * @return AccountInterface
     * @throws NoSuchEntityException
     * @deprecated
     * @see \Isobar\SocialLogin\Api\AccountSearchInterface
     */
    public function getBySocialId($type, $socialId, $websiteId = null);

    /**
     * Delete Account
     * @param AccountInterface $account
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(AccountInterface $account);
}
