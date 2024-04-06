<?php
namespace Isobar\SocialLogin\Api;

/**
 * Account Search Interface
 */
interface AccountSearchInterface
{
    /**
     * Get Account by social id.
     *
     * @param string $type
     * @param string $socialId
     * @param int|null $websiteId
     * @return \Isobar\SocialLogin\Api\Data\AccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySocialId($type, $socialId, $websiteId = null);
}
