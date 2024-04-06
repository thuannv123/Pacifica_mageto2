<?php
namespace Isobar\SocialLogin\Model\Provider\Account;

use Isobar\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Isobar\SocialLogin\Api\Data\AccountInterface;

/**
 * Interface ConverterInterface
 */
interface ConverterInterface
{
    /**
     * Convert provider account to social account
     *
     * @param ProviderAccountInterface $providerAccount
     * @return AccountInterface
     */
    public function convert(ProviderAccountInterface $providerAccount);
}
