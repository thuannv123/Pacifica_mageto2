<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Curl extends AbstractHelper
{
    /** 
     * @var ScopeConfigInterface
     */
    protected $configSettings;

    /**
     * Payment account setting
     *
     * @var string
     */
    const XML_PATH_BEAMCHECKOUT_SETTING = 'payment/beamcheckout/account_setting';

    /**
     * Curl constructor.
     * @param Context $context
     * @param ScopeConfigInterface $configSettings
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $configSettings
    ) {
        $this->configSettings = $configSettings;
        parent::__construct($context);
    }

    /* Send data to Beam to get Purchase Id and Payment Link */
    public function sendCurlRequest($url, $fields, $storeId)
    {
        // Set the username and password for API
        $configSettings = $this->configSettings->getValue(self::XML_PATH_BEAMCHECKOUT_SETTING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $username = $configSettings['merchantId'];
        $password = $configSettings['apiKey'];
        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    /* Send Purchase Id to Beam to get payment information */
    public function sendCurlPurchaseId($url, $fields, $storeId)
    {
        $configSettings = $this->configSettings->getValue(self::XML_PATH_BEAMCHECKOUT_SETTING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        $username = $configSettings['merchantId'];
        $password = $configSettings['apiKey'];
        //open connection
        $url .= '/' . $fields;
        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
