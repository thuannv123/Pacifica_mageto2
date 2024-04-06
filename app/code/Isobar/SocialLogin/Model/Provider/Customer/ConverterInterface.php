<?php
namespace Isobar\SocialLogin\Model\Provider\Customer;

use Isobar\SocialLogin\Exception\CustomerConvertException;
use Isobar\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface ConverterInterface
 */
interface ConverterInterface
{
    /**
     * Convert provider account to customer
     *
     * @param ProviderAccountInterface $providerAccount
     * @return CustomerInterface
     * @throws CustomerConvertException
     */
    public function convert(ProviderAccountInterface $providerAccount);
}
