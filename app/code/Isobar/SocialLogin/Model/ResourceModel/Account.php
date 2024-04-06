<?php
namespace Isobar\SocialLogin\Model\ResourceModel;

/**
 * Account Resource
 */
class Account extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('isobar_social_account', 'account_id');
    }
}
