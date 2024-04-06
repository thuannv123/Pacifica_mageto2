<?php
namespace Isobar\SocialLogin\Model\ResourceModel\Account;

use Isobar\SocialLogin\Model\Account;
use Isobar\SocialLogin\Model\ResourceModel\Account as AccountResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Account::class, AccountResource::class);
    }
}
