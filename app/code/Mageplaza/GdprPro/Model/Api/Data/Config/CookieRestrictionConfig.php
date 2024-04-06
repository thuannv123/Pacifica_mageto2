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
use Mageplaza\GdprPro\Api\Data\Config\CookieRestrictionConfigInterface;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class CookieRestrictionConfig
 * @package Mageplaza\GdprPro\Model\Api\Data\Config
 */
class CookieRestrictionConfig implements CookieRestrictionConfigInterface
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
     * CookieRestrictionConfig constructor.
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
            "enable"           => $this->getEnable(),
            "block_access"     => $this->getBlockAccess(),
            "message"          => $this->getMessage(),
            "policy_page"      => $this->getPolicyPage(),
            "button_text"      => $this->getButtonText(),
            "apply_for"        => $this->getApplyFor(),
            "location"         => $this->getLocation(),
            "specific_country" => $this->getSpecificCountry(),
            "custom_css"       => $this->getCustomCss()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEnable()
    {
        return $this->helperData->getCookieConfig('enable',$this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getBlockAccess()
    {
        return $this->helperData->isBlockAccess($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->helperData->getCookieMessage($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getPolicyPage()
    {
        return $this->helperData->getCookiePolicyUrl($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getButtonText()
    {
        return $this->helperData->getCookieButtonText($this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getApplyFor()
    {
        return $this->helperData->getCookieConfig(self::APPLY_FOR, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getLocation()
    {
        return $this->helperData->getCookieConfig(self::LOCATION, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getSpecificCountry()
    {
        return $this->helperData->getCookieConfig(self::SPECIFIC_COUNTRY, $this->storeId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomCss()
    {
        return $this->helperData->getCustomCss($this->storeId);
    }
}
