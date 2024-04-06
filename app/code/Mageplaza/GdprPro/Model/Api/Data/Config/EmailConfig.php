<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Gdpr
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Model\Api\Data\Config;

use Magento\Framework\DataObject;
use Magento\Framework\Webapi\Rest\Request;
use Mageplaza\GdprPro\Api\Data\Config\EmailConfigInterface;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class EmailConfig
 * @package Mageplaza\GdprPro\Model\Api\Data\Config
 */
class EmailConfig implements EmailConfigInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var mixed
     */
    protected $storeId;

    /**
     * EmailConfig constructor.
     *
     * @param Data $helperData
     * @param Request $request
     */
    public function __construct(
        Data $helperData,
        Request $request
    ) {
        $this->helperData = $helperData;
        $this->request    = $request;
        if (isset($this->request->getBodyParams()['storeId'])) {
            $this->storeId = $this->request->getBodyParams()['storeId'];

        }
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return new DataObject([
            "enable"                   => $this->getEnable(),
            "sender"                   => $this->getSender(),
            "confirmation"             => $this->getConfirmation(),
            "before_delete_account"    => $this->getBeforeDeleteAccount(),
            "admin_notification_email" => $this->getAdminNotificationEmail()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEnable()
    {
        return $this->helperData->isEmailEnable($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getSender()
    {
        return $this->helperData->getSenderEmail($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getConfirmation()
    {
        return new DataObject([
            self::ENABLE   => $this->helperData->getEnableEmailConfirm($this->storeId),
            self::TEMPLATE => $this->helperData->getEmailConfirmTemplate($this->storeId),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getAdminNotificationEmail()
    {

        return new DataObject([
            self::ENABLE   => $this->helperData->getEmailConfig('admin_notification_email/enable', $this->storeId),
            self::RECEIVER => $this->helperData->getReceiverInf($this->storeId),
            self::TEMPLATE => $this->helperData->getEmailAdminNofTemplate($this->storeId),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBeforeDeleteAccount()
    {
        return new DataObject([
            self::ENABLE                  => $this->helperData->getEmailConfig('before_delete_account/enable',
                $this->storeId),
            self::TIME_BEFORE_AUTO_DELETE => $this->helperData->getDayBeforeAutoDelete($this->storeId),
            self::TEMPLATE                => $this->helperData->getEmailTemplateBeforeDelete($this->storeId),
            self::TEMPLATE_AFTER          => $this->helperData->getEmailTemplateAfterDelete($this->storeId),
        ]);
    }
}
