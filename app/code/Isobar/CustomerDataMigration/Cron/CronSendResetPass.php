<?php

namespace Isobar\CustomerDataMigration\Cron;

use \Psr\Log\LoggerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\NoSuchEntityException;

class CronSendResetPass
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagementInterface;
    /**
     * @var \Isobar\CustomerDataMigration\Model\ResourceModel\Customer
     */
    protected $_customer;

    /**
     * CronSendResetPass constructor.
     * @param \Isobar\CustomerDataMigration\Model\ResourceModel\Customer $customer
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface,
        \Isobar\CustomerDataMigration\Model\ResourceModel\Customer $customer,
        LoggerInterface $logger
    ) {
        $this->_customer = $customer;
        $this->logger = $logger;
        $this->accountManagementInterface = $accountManagementInterface;
    }

    public function execute()
    {
        $listCustomer = $this->_customer->getCustomerListToSendResetPassword();
        $this->logger->alert(__('count listCustomer').' '.count($listCustomer));
        if (count($listCustomer) > 0) {
            foreach ($listCustomer as $item) {
                $email = $item['email'];
                $websiteId = $item['website_id'];
                $entity_id = $item['entity_id'];
                try {
                    $send_mail = $this->accountManagementInterface->initiatePasswordReset($email, AccountManagement::EMAIL_RESET,$websiteId);
                        if ($send_mail) {
                            $this->_customer->updateCustomerToSendResetPassword($entity_id);
                            $this->logger->alert(__('Send mail success').' '.$email);
                        }
                } catch (NoSuchEntityException $e) {
                    $this->logger->warning($e->getMessage());
                } catch (\Exception $exception) {
                    $this->logger->warning(__('We\'re unable to send the password reset email.').' '.$exception);
                }
            }
        }

    }
}
