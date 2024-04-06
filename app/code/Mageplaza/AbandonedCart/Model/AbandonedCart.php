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
 * @category    Mageplaza
 * @package     Mageplaza_AbandonedCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AbandonedCart\Model;

use Exception;
use Magento\Customer\Model\CustomerFactory;
use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\CouponFactory;
use Magento\SalesRule\Model\Service\CouponManagementService;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AbandonedCart\Helper\Data;
use Mageplaza\AbandonedCart\Helper\Sms as SmsHelper;
use Mageplaza\AbandonedCart\Model\Sms\Twilio;
use Psr\Log\LoggerInterface;
use Twilio\Exceptions\ConfigurationException;

/**
 * Class AbandonedCart
 * @package Mageplaza\AbandonedCart\Model
 */
class AbandonedCart
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CouponGenerationSpecInterfaceFactory
     */
    protected $generationSpecFactory;

    /**
     * @var CouponManagementService
     */
    protected $couponManagementService;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var Token
     */
    protected $abandonedCartToken;

    /**
     * @var LogsFactory
     */
    protected $abandonedCartLogs;

    /**
     * Object manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var CouponFactory
     */
    protected $couponFactory;

    /**
     * @var Timezone
     */
    protected $templateFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepositoryInterface;

    /**
     * @var array Coupon config for stores
     */
    protected $couponConfigs = [];

    /**
     * @var AreaList
     */
    protected $areaList;

    /**
     * @var Template
     */
    protected $emailTemplate;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var SmsHelper
     */
    protected $smsHelper;
    /**
     * @var Twilio
     */
    protected $twilioModel;

    /**
     * AbandonedCart constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param Data $helper
     * @param SmsHelper $smsHelper
     * @param LoggerInterface $logger
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Random $mathRandom
     * @param CustomerFactory $customerFactory
     * @param Token $abandonedCartToken
     * @param LogsFactory $abandonedCartLogs
     * @param ObjectManagerInterface $objectManager
     * @param CouponFactory $couponFactory
     * @param FactoryInterface $templateFactory
     * @param RuleRepositoryInterface $ruleRepositoryInterface
     * @param CouponManagementService $couponManagementService
     * @param CouponGenerationSpecInterfaceFactory $generationSpecFactory
     * @param AreaList $areaList
     * @param Template $emailTemplate
     * @param Escaper $escaper
     * @param TimezoneInterface $timezone
     * @param ResolverInterface $localeResolver
     * @param Sms\Twilio $twilioModel
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        Data $helper,
        SmsHelper $smsHelper,
        LoggerInterface $logger,
        DateTime $date,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Random $mathRandom,
        CustomerFactory $customerFactory,
        Token $abandonedCartToken,
        LogsFactory $abandonedCartLogs,
        ObjectManagerInterface $objectManager,
        CouponFactory $couponFactory,
        FactoryInterface $templateFactory,
        RuleRepositoryInterface $ruleRepositoryInterface,
        CouponManagementService $couponManagementService,
        CouponGenerationSpecInterfaceFactory $generationSpecFactory,
        AreaList $areaList,
        Template $emailTemplate,
        Escaper $escaper,
        TimezoneInterface $timezone,
        ResolverInterface $localeResolver,
        Twilio $twilioModel
    ) {
        $this->objectManager           = $objectManager;
        $this->quoteFactory            = $quoteFactory;
        $this->helper                  = $helper;
        $this->date                    = $date;
        $this->logger                  = $logger;
        $this->storeManager            = $storeManager;
        $this->transportBuilder        = $transportBuilder;
        $this->customerFactory         = $customerFactory;
        $this->mathRandom              = $mathRandom;
        $this->abandonedCartToken      = $abandonedCartToken;
        $this->abandonedCartLogs       = $abandonedCartLogs;
        $this->generationSpecFactory   = $generationSpecFactory;
        $this->couponManagementService = $couponManagementService;
        $this->couponFactory           = $couponFactory;
        $this->templateFactory         = $templateFactory;
        $this->ruleRepositoryInterface = $ruleRepositoryInterface;
        $this->areaList                = $areaList;
        $this->emailTemplate           = $emailTemplate;
        $this->escaper                 = $escaper;
        $this->timezone                = $timezone;
        $this->localeResolver          = $localeResolver;
        $this->smsHelper               = $smsHelper;
        $this->twilioModel             = $twilioModel;
    }

    /**
     * @throws ConfigurationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareForAbandonedCart()
    {
        foreach ($this->storeManager->getStores() as $store) {
            if ($this->helper->isEnabled($store->getId())) {
                $this->prepareEmailForStore($store->getId());
                $this->prepareSmsForStore($store->getId());
            }
        }
    }

    /**
     * Send email
     *
     * @param int $storeId
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareEmailForStore($storeId)
    {
        $configs         = $this->helper->getEmailConfig($storeId);
        $quoteCollection = $this->quoteFactory->create()->getCollection();
        $timeMeasure = $this->helper->getRealtimeConfig('time_measure', $storeId) ?: 0;

        if ($quoteCollection === null) {
            return $this;
        }

        $day     = 86400;
        $current = strtotime($this->date->date());

        $lastSendEmail = $configs ? $current - max(array_column($configs, 'send')) - $day : 0;

        $lastSendDate = $this->date->date('Y-m-d H:i:s', $lastSendEmail);

        $quoteCollection->addFieldToFilter('items_count', ['neq' => '0'])
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_email', ['neq' => null]);
        $quoteCollection->getSelect()->joinLeft(
            ['customer_log' => $quoteCollection->getTable('customer_log')],
            'main_table.customer_id = customer_log.customer_id',
            []
        )->where(
            'IF(customer_log.last_login_at IS NULL , updated_at, customer_log.last_login_at ) >= ? OR created_at >= ?',
            $lastSendDate,
            $lastSendDate
        );


        /** @var Quote $quote */
        foreach ($quoteCollection as $quote) {
            $quoteUpdatedTime = strtotime($quote->getUpdatedAt());
            if ($quoteUpdatedTime < 0) {
                $quoteUpdatedTime = strtotime($quote->getCreatedAt());
            }
            $quoteId = $quote->getId();
            $recover= $this->abandonedCartLogs->create()->getCollection()
                ->addFieldToFilter('quote_id', $quote->getId())
                ->addFieldToFilter('status', ['eq' => 2])
                ->getFirstItem()->getData('id');
            if (!$this->helper->getSendEmailRecoverConfig($storeId) && $recover) {
                continue;
            }
            foreach ($configs as $configId => $config) {
                $validateEmail = $this->abandonedCartToken->validateEmail($quote, $configId);
                $time          = $quoteUpdatedTime + $config['send'] + $timeMeasure*60;
                if ($validateEmail && $time <= $current) {
                    $coupon = [];
                    if ((bool) $config['coupon']) {
                        try {
                            $coupon = $this->createCoupon($quote->getStoreId());
                        } catch (Exception $e) {
                            $this->logger->critical($e);
                        }
                    }
                    $newCartToken = $this->mathRandom->getUniqueHash();

                    if ($quote->getCustomerId()) {
                        $customer       = $this->customerFactory->create()->load($quote->getCustomerId());
                        $mpAceBlacklist = $customer->getData('mp_ace_blacklist');
                        if (!$mpAceBlacklist) {
                            $this->sendMail($quote, $config, $newCartToken, $coupon);
                        }
                    } else {
                        $this->sendMail($quote, $config, $newCartToken, $coupon);
                    }

                    $this->abandonedCartToken->saveToken($quoteId, $configId, $newCartToken);
                }
            }
        }

        return $this;
    }

    /**
     * @param Quote $quote
     *
     * @return bool
     */
    protected function checkSendSms($quote)
    {
        $current        = $this->date->date('Y-m-d');
        $quoteUpdatedAt = $quote->getUpdatedAt();

        if ($current != $this->date->date('Y-m-d', $quoteUpdatedAt)) {
            return false;
        }

        $from = $this->date->date($quoteUpdatedAt);
        $to   = $this->date->date();

        $smsHistory = $this->abandonedCartLogs->create()->getCollection()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->addFieldToFilter('sms_content', ['notnull' => true])
            ->addFieldToFilter('created_at', ['from' => $from, 'to' => $to])
            ->getFirstItem();

        $data = $smsHistory ? $smsHistory->getData() : [];

        return ($this->smsHelper->getSmsConfig('enabled') && count($data) == 0);
    }

    /**
     * Send SMS
     *
     * @param $storeId
     *
     * @return $this
     * @throws ConfigurationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareSmsForStore($storeId)
    {
        $quoteCollection = $this->quoteFactory->create()->getCollection();
        $timeMeasure = $this->helper->getRealtimeConfig('time_measure', $storeId) ?: 0;

        if ($quoteCollection === null) {
            return $this;
        }

        $current      = strtotime($this->date->date());
        $lastSendSms  = $current - $this->smsHelper->getSendAfter() - 84600;
        $lastSendDate = $this->date->date('Y-m-d H:i:s', $lastSendSms);

        $quoteCollection->addFieldToFilter('items_count', ['neq' => '0'])
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_email', ['neq' => null]);
        $quoteCollection->getSelect()->joinLeft(
            ['customer_log' => $quoteCollection->getTable('customer_log')],
            'main_table.customer_id = customer_log.customer_id',
            []
        )->where(
            'IF(customer_log.last_login_at IS NULL , main_table.updated_at, customer_log.last_login_at ) >= ? OR main_table.created_at >= ?',
            $lastSendDate,
            $lastSendDate
        );

        /** @var Quote $quote */
        foreach ($quoteCollection as $quote) {
            $quoteUpdatedTime = strtotime($quote->getUpdatedAt());
            if ($quoteUpdatedTime < 0) {
                $quoteUpdatedTime = strtotime($quote->getCreatedAt());
            }

            if ($this->checkSendSms($quote)) {
                $store     = $this->storeManager->getStore($quote->getStoreId());
                $sendTime  = $quoteUpdatedTime + $this->smsHelper->getSendAfter() + $timeMeasure*60;
                $current   = strtotime($this->date->date());
                $recipient = $this->smsHelper->getRecipient($quote);
                $quoteLog  = $this->abandonedCartLogs->create()->getCollection()
                    ->addFieldToFilter('quote_id', $quote->getId())
                    ->addFieldToFilter('email_content', ['notnull' => true])->getLastItem();

                if ($sendTime <= $current && $recipient != '') {
                    try {
                        $data   = $this->smsHelper->getSmsData($quote);
                        $coupon = [];
                        if ($quoteLog && $quoteLog->getData('coupon_code')) {
                            $coupon['coupon_code'] = $quoteLog->getData('coupon_code');
                            $currentCoupon         = $this->couponFactory->create()->loadByCode($quoteLog->getData('coupon_code'));
                            $coupon['to_date']     = $currentCoupon->getData('mp_ace_expires_at');
                        } else {
                            $coupon = $this->createCoupon($quote->getStoreId());
                        }
                    } catch (Exception $e) {
                        $this->logger->critical($e);
                    }

                    $token               = $this->mathRandom->getUniqueHash();
                    $data['cart_url']    = $this->smsHelper->getAbandonedCartUrl($quote, $token);
                    $data['coupon_code'] = isset($coupon['coupon_code']) ? $coupon['coupon_code'] : '';
                    $data['to_date']     = isset($coupon['to_date']) ? $this->getCreatedAtFormatted(
                        $coupon['to_date'],
                        2,
                        $store
                    ) : '';

                    $messageTemplate = $this->smsHelper->getSmsConfig('message');
                    $messageContent  = $this->smsHelper->generateMessageContent($data, $messageTemplate);

                    if ($this->twilioModel->send($messageContent, $recipient)) {
                        $this->abandonedCartLogs->create()->saveSmsLogs(
                            $quote->getId(),
                            $data['customer_email'],
                            $data['customer_name'],
                            $this->smsHelper->getSenderPhoneNumber(),
                            $messageContent,
                            $data['coupon_code'],
                            true
                        );
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param array $config
     * @param string $newCartToken
     * @param array $coupon
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function sendMail($quote, $config, $newCartToken, $coupon = [])
    {
        $customerEmail = $quote->getCustomerEmail();
        $customerName  = trim($quote->getFirstname() . ' ' . $quote->getLastname());

        if (!$customerName) {
            $customer = $quote->getCustomerId() ? $quote->getCustomer() : null;
            if ($customer && $customer->getId()) {
                $customerName = trim($customer->getFirstname() . ' ' . $customer->getLastname());
            } else {
                $customerName = explode('@', $customerEmail)[0];
            }
        }

        $couponCode = isset($coupon['coupon_code']) ? $coupon['coupon_code'] : '';

        /** @var Store $store */
        $store = $this->storeManager->getStore($quote->getStoreId());

        /** @var TemplateInterface $template */
        $template = $this->templateFactory->get($config['template'])
            ->setOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()]);

        $vars = [
            'quoteId'       => $quote->getId(),
            'customer_name' => ucfirst($customerName),
            'coupon_code'   => $couponCode,
            'to_date'       => isset($coupon['to_date']) ? $this->getCreatedAtFormatted(
                $coupon['to_date'],
                2,
                $store
            ) : '',
            'sender'        => $config['sender'],
            'checkout_url'  => $template->getUrl($store, 'abandonedcart/checkout/cart', [
                'id'      => $quote->getId(),
                'token'   => $newCartToken,
                '_nosid'  => true,
                '_query'  => $this->helper->getUrlSuffix($store),
                '_secure' => $store->isUrlSecure()
            ])
        ];

        $areaObject = $this->areaList->getArea($this->emailTemplate->getDesignConfig()->getArea());
        $areaObject->load(Area::PART_TRANSLATE);

        $transport = $this->transportBuilder->setTemplateIdentifier($config['template'])
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
            ->setFromByScope($config['sender'],$store->getId())
            ->addTo($customerEmail, $customerName)
            ->setTemplateVars($vars)
            ->getTransport();

        try {
            $transport->sendMessage();
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $this->logger->error($e->getMessage());
        }

        if (!isset($config['ignore_log'])) {
            $emailBody = $template->setVars($vars)->processTemplate();

            $subject = $this->escaper->escapeHtml($template->getSubject());
            $this->abandonedCartLogs->create()->saveLogs(
                $quote,
                $customerEmail,
                $customerName,
                $config['sender'],
                $subject,
                $emailBody,
                $success,
                $couponCode
            );
        }
    }

    /**
     * @param Logs $log
     *
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendAgain($log)
    {
        $store = $this->storeManager->getStore();
        $this->transportBuilder->setTemplateIdentifier('send_again')
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
            ->setTemplateVars([
                'body'    => htmlspecialchars_decode($log->getEmailContent()),
                'subject' => $log->getSubject()
            ])
            ->setFrom($log->getSender())
            ->addTo($log->getCustomerEmail(), $log->getCustomerName())
            ->getTransport()
            ->sendMessage();

        $log->setUpdatedAt($this->date->date());
        if($log->getData('status') !== '2')
        {
            $log->setStatus(true);
        }
    }

    /**
     * Generate Coupon Code by Configuration
     *
     * @param null $storeId
     *
     * @return $this|array
     * @throws InputException
     * @throws LocalizedException
     * @throws Exception
     */
    public function createCoupon($storeId = null)
    {
        $coupon       = [];
        $couponConfig = $this->getCouponConfig($storeId);
        if (!empty($couponConfig)) {
            $couponSpec  = $this->generationSpecFactory->create(['data' => $couponConfig]);
            $couponCodes = $this->couponManagementService->generate($couponSpec);
            $couponCode  = $couponCodes[0];

            $coupon = $this->couponFactory->create()->loadByCode($couponCode);
            $coupon->setMpGeneratedByAbandonedCart(1);
            if ($couponConfig['valid']) {
                $expirationDate = strtotime($this->date->date()) + $couponConfig['valid'] * 3600;
                if (!$coupon->getMpAceExpiresAt()
                    || ($coupon->getMpAceExpiresAt() && strtotime($coupon->getMpAceExpiresAt()) > $expirationDate)) {
                    try {
                        $coupon->setMpAceExpiresAt($this->date->date('Y-m-d H:i:s', $expirationDate))->save();
                    } catch (Exception $e) {
                        $this->logger->critical($e);
                    }
                }
            }
            if ($couponCode) {
                $coupon = [
                    'coupon_code' => $couponCode,
                    'to_date'     => $coupon->getMpAceExpiresAt() ?: ''
                ];
            }
        }

        return $coupon;
    }

    /**
     * @param int $storeId
     *
     * @return mixed
     */
    protected function getCouponConfig($storeId)
    {
        if (!isset($this->couponConfigs[$storeId])) {
            $couponConfig = [];
            if ($ruleId = $this->helper->getCouponConfig('rule', $storeId)) {
                $couponConfig = [
                    'rule_id'  => $ruleId,
                    'quantity' => 1,
                    'length'   => (int) $this->helper->getCouponConfig('length', $storeId) ?: 5,
                    'format'   => $this->helper->getCouponConfig('format', $storeId),
                    'prefix'   => $this->helper->getCouponConfig('prefix', $storeId),
                    'suffix'   => $this->helper->getCouponConfig('suffix', $storeId),
                    'dash'     => (int) $this->helper->getCouponConfig('dash', $storeId),
                    'valid'    => (int) $this->helper->getCouponConfig('valid', $storeId)
                ];
            }
            $this->couponConfigs[$storeId] = $couponConfig;
        }

        return $this->couponConfigs[$storeId];
    }

    /**
     * @param string $date
     * @param int $format
     * @param Store $store
     *
     * @return string
     * @throws Exception
     */
    public function getCreatedAtFormatted($date, $format, $store)
    {
        return $this->timezone->formatDateTime(
            new \DateTime($date),
            $format,
            $format,
            $this->localeResolver->getDefaultLocale(),
            $this->timezone->getConfigTimezone('store', $store)
        );
    }
}
