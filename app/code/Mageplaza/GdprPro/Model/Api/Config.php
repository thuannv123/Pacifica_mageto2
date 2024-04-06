<?php

namespace Mageplaza\GdprPro\Model\Api;

use Magento\Framework\DataObject;
use Mageplaza\GdprPro\Model\Api\Data\Config\AnonymiseConfig;
use Mageplaza\GdprPro\Model\Api\Data\Config\CookieRestrictionConfig;
use Mageplaza\GdprPro\Model\Api\Data\Config\EmailConfig;
use Mageplaza\GdprPro\Model\Api\Data\Config\GeneralConfig;

/**
 * Class Config
 * @package Mageplaza\GdprPro\Model\Api
 */
class Config
{
    /**
     * @var GeneralConfig
     */
    protected $_generalConfig;
    /**
     * @var AnonymiseConfig
     */
    protected $_anonymiseConfig;
    /**
     * @var EmailConfig
     */
    protected $_emailConfig;
    /**
     * @var CookieRestrictionConfig
     */
    protected $_cookieRestrictionConfig;

    /**
     * @param GeneralConfig $generalConfig
     * @param CookieRestrictionConfig $cookieRestrictionConfig
     * @param EmailConfig $emailConfig
     * @param AnonymiseConfig $anonymiseConfig
     */
    public function __construct(
        GeneralConfig $generalConfig,
        CookieRestrictionConfig $cookieRestrictionConfig,
        EmailConfig $emailConfig,
        AnonymiseConfig $anonymiseConfig
    ) {
        $this->_generalConfig           = $generalConfig;
        $this->_cookieRestrictionConfig = $cookieRestrictionConfig;
        $this->_emailConfig             = $emailConfig;
        $this->_anonymiseConfig         = $anonymiseConfig;
    }

    /**
     * @return DataObject
     */
    public function getConfig()
    {
        return new DataObject(
            [
                'general_config' => $this->_generalConfig->getConfig(),
                'cookie_restriction_config' => $this->_cookieRestrictionConfig->getConfig(),
                'email_config' => $this->_emailConfig->getConfig(),
                'anonymise_config' => $this->_anonymiseConfig->getConfig(),
            ]
        );
    }
}