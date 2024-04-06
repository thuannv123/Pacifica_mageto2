<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Isobar\CustomerDataMigration\Model\ResourceModel;

use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\AccountConfirmation;

/**
 * Class Customer
 * @package Isobar\CustomerDataMigration\Model\ResourceModel
 */
class Customer extends \Magento\Customer\Model\ResourceModel\Customer
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagementInterface;

    public function __construct(
        \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Validator\Factory $validatorFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = [],
        AccountConfirmation $accountConfirmation = null
    ) {
        parent::__construct($context,$entitySnapshot,$entityRelationComposite,$scopeConfig,$validatorFactory,$dateTime,$storeManager);
        $this->resource = $resource;
        $this->accountManagementInterface = $accountManagementInterface;
    }

    /**
     * @return array
     */
    public function getCustomerListToSendResetPassword()
    {
        $connection = $this->resource->getConnection();
        $bind = ['is_password_reset_require' => 1,'reset_password_email_sent' => 0];
        $select = $connection->select()->from(
            $this->resource->getTableName('customer_entity'),
            ['website_id','email','entity_id']
        )->where(
            'is_password_reset_require = :is_password_reset_require'
        )->where(
            'reset_password_email_sent = :reset_password_email_sent'
        );

        return $connection->fetchAll($select, $bind);
    }

    /**
     * @param $email
     * @param $websiteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCustomerToSendResetPassword($entity_id)
    {
        $connection = $this->resource->getConnection();
        return $connection->update(
            $this->resource->getTableName('customer_entity'),
            ['reset_password_email_sent' => 1],
            $this->resource->getConnection()->quoteInto('entity_id = ?', $entity_id)
        );
    }

    /**
     * @param $entity_id
     * @return int
     */
    public function updateCustomerToChangedPassword($entity_id)
    {
        $connection = $this->resource->getConnection();
        return $connection->update(
            $this->resource->getTableName('customer_entity'),
            ['is_password_reset' => 1],
            $this->resource->getConnection()->quoteInto('entity_id = ?', $entity_id)
        );
    }
}
