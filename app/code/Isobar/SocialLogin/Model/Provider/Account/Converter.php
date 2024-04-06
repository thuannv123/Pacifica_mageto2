<?php
namespace Isobar\SocialLogin\Model\Provider\Account;

use Isobar\SocialLogin\Model\Provider\AccountInterface as ProviderAccountInterface;
use Isobar\SocialLogin\Api\Data\AccountInterface;

/**
 * Class Converter
 */
class Converter implements ConverterInterface
{
    /**
     * @var \Isobar\SocialLogin\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * @param \Isobar\SocialLogin\Model\AccountFactory $accountFactory
     */
    public function __construct(
        \Isobar\SocialLogin\Model\AccountFactory $accountFactory
    ) {
        $this->accountFactory = $accountFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(ProviderAccountInterface $providerAccount)
    {
        $account = $this->initAccount();

        $account->setType($providerAccount->getType())
            ->setFirstName($providerAccount->getFirstName())
            ->setLastName($providerAccount->getLastName())
            ->setEmail($providerAccount->getEmail())
            ->setImage($providerAccount->getImage())
            ->setSocialId($providerAccount->getSocialId());

        return $account;
    }

    /**
     * Init account model
     *
     * @return AccountInterface
     */
    protected function initAccount()
    {
        return $this->accountFactory->create();
    }
}
