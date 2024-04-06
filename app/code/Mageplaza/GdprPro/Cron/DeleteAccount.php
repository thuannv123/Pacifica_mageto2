<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Cron;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GdprPro\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class DeleteAccount
 * @package Mageplaza\GdprPro\Cron
 */
class DeleteAccount
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var CollectionFactory
     */
    protected $_customerFactory;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZone;

    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var
     */
    protected $_pageFactory;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * DeleteAccount constructor.
     *
     * @param LoggerInterface $logger
     * @param TimezoneInterface $timeZone
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $customerFactory
     * @param Data $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $registry
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        Registry $registry,
        LoggerInterface $logger,
        TimezoneInterface $timeZone,
        CollectionFactory $customerFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->_helperData         = $helperData;
        $this->_registry           = $registry;
        $this->logger              = $logger;
        $this->_timeZone           = $timeZone;
        $this->_customerFactory    = $customerFactory;
        $this->_transportBuilder   = $transportBuilder;
        $this->_storeManager       = $storeManager;
        $this->_resourceConnection = $resourceConnection;
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        if (!$this->_helperData->isAutoDeleteAccount()) {
            return $this;
        }

        $day             = (int) $this->_helperData->getTimeAutoDelete();
        $dayBeforeDelete = (int) $this->_helperData->getDayBeforeAutoDelete();

        if (isset($day) && $day > 0) {
            $timeEnd     = strtotime($this->_timeZone->date()->format('Y-m-d H:i:s')) - $day * 24 * 60 * 60;
            $customerIds = $this->getCustomerIds($timeEnd);

            if (isset($dayBeforeDelete) && $dayBeforeDelete > 0 && $this->_helperData->isEnabledEmailBeforeDelete()) {
                $timeBeforeEnd = strtotime($this->_timeZone->date()->format('Y-m-d H:i:s')) - $dayBeforeDelete * 24 * 60 * 60;
                $ids           = $this->getCustomerIds($timeBeforeEnd);
                foreach ($ids as $id) {
                    try {
                        $customer = $this->_customerRepository->getById($id);
                        if ($customer) {
                            $this->sendMail($customer->getEmail(), date('m-d-Y', $timeEnd));
                        }
                    } catch (Exception $e) {
                        $this->logger->critical($e);
                    }
                }
            }

            foreach ($customerIds as $id) {
                try {
                    if ($this->_customerRepository->getById($id)) {
                        $this->_registry->register('isSecureArea', true, true);
                        $customer = $this->_customerRepository->getById($id);
                        if ($customer) {
                            $this->sendDeletedMail($customer->getEmail());
                        }
                        $this->_customerRepository->deleteById($id);
                    }
                } catch (Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }

        return $this;
    }

    /**
     * @param $time
     *
     * @return array
     */
    public function getCustomerIds($time)
    {
        $connection = $this->_resourceConnection->getConnection();
        $tableName  = $this->_resourceConnection->getTableName('customer_log');
        $sql        = $connection->select()->from($tableName, 'customer_id')->where(
            'last_login_at <= ?',
            date('Y-m-d H:i:s', $time)
        );

        return $connection->fetchCol($sql);
    }

    /**
     * @param $email
     *
     * @param $timeRemove
     *
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendMail(
        $email,
        $timeRemove
    ) {
        /** @var $customer Customer */
        $customer  = $this->_customerFactory->create()->addFieldToFilter('email', $email)->getLastItem();
        $store     = $this->_storeManager->getStore();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_helperData->getEmailTemplateBeforeDelete())
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
            ->setTemplateVars(
                [
                    'customer_firstname' => $customer->getFirstname(),
                    'email'              => $email,
                    'timeRemove'         => $timeRemove
                ]
            )
            ->setFrom($this->_helperData->getSenderEmail($store->getId()))
            ->addTo($email, $customer->getName())
            ->getTransport();

        $transport->sendMessage();
    }

    /**
     * @param $email
     *
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendDeletedMail(
        $email
    ) {
        /** @var $customer Customer */
        $customer  = $this->_customerFactory->create()->addFieldToFilter('email', $email)->getLastItem();
        $store     = $this->_storeManager->getStore();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_helperData->getEmailTemplateAfterDelete())
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
            ->setTemplateVars(
                [
                    'customer_firstname' => $customer->getFirstname(),
                    'email'              => $email
                ]
            )
            ->setFrom($this->_helperData->getSenderEmail($store->getId()))
            ->addTo($email, $customer->getName())
            ->getTransport();

        $transport->sendMessage();
    }
}
