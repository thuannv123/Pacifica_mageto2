<?php

namespace Marvelic\SocialLogin\Model\ResourceModel;

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
        $this->_init('mageplaza_social_customer', 'social_customer_id');
    }
}
