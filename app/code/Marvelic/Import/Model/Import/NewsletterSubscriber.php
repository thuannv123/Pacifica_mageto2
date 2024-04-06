<?php

namespace Marvelic\Import\Model\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\ImportExport\Model\ImportFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Firebear\ImportExport\Model\Import\Context;

class NewsletterSubscriber extends \Firebear\ImportExport\Model\Import\NewsletterSubscriber
{
    protected $connection;
    protected $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        SubscriberFactory $subscriberFactory,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $importFactory,
            $subscriberFactory,
            $customerFactory,
            $storeManager,
            $encryptor,
            $data
        );
        $this->scopeConfig = $scopeConfig;
        $this->connection = $resource->getConnection();
    }
    /**
     * Validate status string
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     */
    protected function validateStatus(array $rowData, $rowNumber)
    {
        if (!empty($rowData[self::COL_STATUS])) {
            if (!in_array($rowData[self::COL_STATUS], $this->status)) {
                $this->addRowError(self::ERROR_STATUS_VALUE, $rowNumber);
            }
        }
    }

    protected function save(array $rowData)
    {
        $subscriber = $this->subscriberFactory->create();
        $email = $rowData[self::COL_SUBSCRIBER_EMAIL] ?? null;
        $storeId = $rowData[self::COL_STORE_ID] ?? null;

        if (!empty($email) && !empty($storeId)) {
            $websiteId = (int)$this->storeManager->getStore($storeId)->getWebsiteId();
            $subscriber->loadBySubscriberEmail($email, $websiteId);
            if ($subscriber->getId()) {
                unset($rowData[self::COL_SUBSCRIBER_EMAIL]);
            }
        } elseif (!empty($rowData[self::COL_SUBSCRIBER_ID])) {
            $subscriber->load($rowData[self::COL_SUBSCRIBER_ID]);
            unset($rowData[self::COL_SUBSCRIBER_ID]);
        }

        if ($subscriber->getId()) {
            $this->countItemsUpdated++;
        } else {
            $this->countItemsCreated++;
            $rowData[self::COL_SUBSCRIBER_ID] = null;
        }

        unset($rowData[self::COL_CUSTOMER_ID]);
        $subscriber->addData($rowData);

        if (!$subscriber->getCustomerId()) {
            $customer = $this->customerFactory->create();
            if ($customer->getSharingConfig()->isWebsiteScope()) {
                $store = empty($rowData[self::COL_STORE_ID])
                    ? $this->storeManager->getDefaultStoreView()
                    : $this->storeManager->getStore($rowData[self::COL_STORE_ID]);

                $customer->setWebsiteId($store->getWebsiteId());
            }

            $customer->loadByEmail($email);
            if ($customer->getId()) {
                $subscriber->setCustomerId($customer->getId());
            } else {
                if (
                    !empty($rowData['firstname']) &&
                    !empty($rowData['lastname']) &&
                    !empty($rowData[self::COL_PASSWORD_HASH])
                ) {
                    $customer->setFirstname($rowData['firstname']);
                    $customer->setLastname($rowData['lastname']);
                    $customer->setEmail($subscriber->getSubscriberEmail());
                    $customer->setPasswordHash($this->getPasswordHash($rowData));
                    $customer->save();

                    $subscriber->setCustomerId($customer->getId());
                }
            }
        }
        $date = $subscriber->getData('change_status_at');
        if (!empty($date)) {
            $configTimezone = new \DateTimeZone($this->scopeConfig->getValue('general/locale/timezone'));
            $utcTimezone = new \DateTimeZone('UTC');
            $time = new \DateTime($date, $configTimezone);
            $dateImport = $time->setTimezone($utcTimezone)->format('Y-m-d H:i:s');
        } else {
            $dateImport = date('Y-m-d H:i:s');
        }
        try {
            $select = $this->connection->select()
                ->from('newsletter_subscriber')
                ->where('store_id = ?', $storeId)
                ->where('subscriber_email = ?', $subscriber->getEmail());

            $result = $this->connection->fetchAll($select);

            if (!empty($result)) {
                $customerId  = $this->customerFactory->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($subscriber->getEmail())
                    ->getId();
                foreach ($result as $rs) {
                    if ($rs['customer_id'] == $customerId) {
                        $updateData = [
                            'change_status_at' => $dateImport,
                            'customer_id' => $rs['customer_id'] ?: 0,
                            'subscriber_status' => $subscriber->getStatus(),
                            'subscriber_confirm_code' => $subscriber->getData('subscriber_confirm_code') ?: $this->randomSequence()
                        ];
                    } else {
                        $updateData = [
                            'change_status_at' => $dateImport,
                            'customer_id' => $customerId ?: 0,
                            'subscriber_status' => $subscriber->getStatus(),
                            'subscriber_confirm_code' => $subscriber->getData('subscriber_confirm_code') ?: $this->randomSequence()
                        ];
                    }
                    if (!empty($updateData)) {
                        $this->connection->update(
                            'newsletter_subscriber',
                            $updateData,
                            [
                                'store_id = ?' => $storeId,
                                'subscriber_email = ?' => $subscriber->getEmail()
                            ]
                        );
                    }
                }
            } else {
                $insertData = [
                    'store_id' => $storeId,
                    'change_status_at' => $dateImport,
                    'customer_id' => $subscriber->getCustomerId() ?: 0,
                    'subscriber_email' => $subscriber->getEmail(),
                    'subscriber_status' => $subscriber->getStatus(),
                    'subscriber_confirm_code' => $subscriber->getData('subscriber_confirm_code') ?: $this->randomSequence()
                ];

                $this->connection->insertOnDuplicate(
                    'newsletter_subscriber',
                    $insertData,
                    ['store_id', 'change_status_at', 'customer_id', 'subscriber_status', 'subscriber_confirm_code']
                );
            }
        } catch (\Exception $e) {
            $this->addLogWriteln($e->getMessage(), $this->getOutput(), 'error');
        }
        return $this;
    }
    public function randomSequence($length = 32)
    {
        $id = '';
        $par = [];
        $char = array_merge(range('a', 'z'), range(0, 9));
        $charLen = count($char) - 1;
        for ($i = 0; $i < $length; $i++) {
            $disc = \Magento\Framework\Math\Random::getRandomNumber(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id . $char[$disc];
        }
        return $id;
    }
}
