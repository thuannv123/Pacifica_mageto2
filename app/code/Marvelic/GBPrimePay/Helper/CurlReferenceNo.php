<?php

namespace Marvelic\GBPrimePay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class CurlReferenceNo extends AbstractHelper
{
    protected $configHelper;

    const TEST_URL = 'https://api.globalprimepay.com/';
    
    const PRODUCTION_URL = 'https://api.gbprimepay.com/';

    public function __construct(
        Context $context,
        \GBPrimePay\Payments\Helper\ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    public function sendCurlReferenceNo($fields)
    {
        $endpoint = 'v1/check_status_txn';

        // GBPrime Pay secret key
        if ($this->configHelper->getEnvironment() === 'prelive') {
            $secretKey = $this->configHelper->getTestSecretKey();
            $baseUrl = self::TEST_URL;
        } else {
            $secretKey = $this->configHelper->getLiveSecretKey();
            $baseUrl = self::PRODUCTION_URL;
        }

        // GBPrime Pay API endpoint
        $url = $baseUrl . $endpoint;

        $key = base64_encode("{$secretKey}" . ":");

        // Initialize cURL
        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );

        $type = 'POST';
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        // Check for errors
        if ($body === false) {
            $error = curl_error($ch);
            return $error;
            // Handle the error
        }

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);
        // $print = rtrim($body, '[]');
        // print_r($print);
        return $json;
    }
}
