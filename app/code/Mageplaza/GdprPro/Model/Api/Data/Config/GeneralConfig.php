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
use Mageplaza\Gdpr\Model\Api\Data\Config\GeneralConfig as GeneralConfigS;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class CookieRestrictionConfig
 * @package Mageplaza\GdprPro\Model\Api\Data\Config
 */
class GeneralConfig extends GeneralConfigS implements \Mageplaza\GdprPro\Api\Data\Config\GeneralConfigInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param Data $helperData
     * @param Request $request
     */
    public function __construct(
        Data $helperData,
        Request $request
    ) {
        $this->helperData = $helperData;
        parent::__construct($helperData, $request);
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return new DataObject([
            "allow_verify_password"        => $this->getAllowVerifyPassword(),
            "allow_download"               => $this->getAllowDownload(),
            "download_customer_message"    => $this->getDownloadCustomerMessage(),
            "allow_tac_register_customer"  => $this->getAllowTacRegisterCustomer(),
            "tac_title_checkbox"           => $this->getTacTitleCheckbox(),
            "tac_content"                  => $this->getTacContent(),
            "auto_delete_customer_account" => $this->getAutoDeleteCustomerAccount(),
            "time_auto_delete"             => $this->getTimeAutoDelete(),
            "enable"                       => $this->getEnable(),
            "allow_delete_customer"        => $this->getAutoDeleteCustomerAccount(),
            "delete_customer_message"      => $this->getDeleteCustomerMessage(),
            "allow_delete_default_address" => $this->getAllowDeleteDefaultAddress()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getAllowVerifyPassword()
    {
        return $this->helperData
            ->getConfigGeneral(self::ALLOW_VERIFY_PASSWORD, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getAllowDownload()
    {
        return $this->helperData->getConfigGeneral(self::ALLOW_DOWNLOAD, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getDownloadCustomerMessage()
    {
        return $this->helperData
            ->getConfigGeneral(self::DOWNLOAD_CUSTOMER_MESSAGE, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getAllowTacRegisterCustomer()
    {
        return $this->helperData
            ->getConfigGeneral(self::ALLOW_TAC_REGISTER_CUSTOMER, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getTacTitleCheckbox()
    {
        return $this->helperData->getConfigGeneral(self::TAC_TITLE_CHECKBOX, $this->storeId);
    }

    /**
     * @return string|void
     */
    public function getTacContent()
    {
        return $this->helperData->getConfigGeneral(self::TAC_CONTENT, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getAutoDeleteCustomerAccount()
    {
        return $this->helperData
            ->getConfigGeneral(self::AUTO_DELETE_CUSTOMER_ACCOUNT, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getTimeAutoDelete()
    {
        return $this->helperData->getConfigGeneral(self::TIME_AUTO_DELETE, $this->storeId);
    }
}
