<?php

namespace Marvelic\GBPrimePay\Helper;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\SerializerInterface as SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use GBPrimePay\Payments\Helper\Constant as Constant;

class ConfigHelper extends \GBPrimePay\Payments\Helper\ConfigHelper
{
    protected $localeResolver;

    public function __construct(
        Context $context,
        EncryptorInterface $encryptorInterface,
        SerializerInterface $SerializerInterface,
        UrlInterface $urlBuilder,
        FormKey $formKey,
        Repository $assetRepo,
        Session $checkoutSession,
        ResolverInterface $localeResolver
    ) {
        parent::__construct(
            $context,
            $encryptorInterface,
            $SerializerInterface,
            $urlBuilder,
            $formKey,
            $assetRepo,
            $checkoutSession
        );
        $this->localeResolver = $localeResolver;
    }

    public function getMerchantId()
    {
        if ($this->getEnvironment() === 'prelive') {
            $configkey = $this->getTestPublicKey();
            $url = Constant::URL_CHECKPUBLICKEY_TEST;
        } else {
            $configkey = $this->getLivePublicKey();
            $url = Constant::URL_CHECKPUBLICKEY_LIVE;
        }

        if (empty($configkey)) {
            return false;
        }

        $type = 'GET';

        $key = base64_encode("{$configkey}" . ":");
        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "Authorization: Basic {$key}",
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        $json = json_decode($body, true);
        if (isset($json['error'])) {
            return false;
        }

        curl_close($ch);

        if (isset($json['merchantId']) && !empty($json['merchantId'])) {
            return $json['merchantId'];
        } else {
            $localeCode = $this->localeResolver->getLocale();
            if (isset($localeCode)) {
                if ($localeCode == 'en_US') {
                    return throw new CouldNotSaveException(
                        __('Unable to connect to payment provider. Please try again later.')
                    );
                } else if ($localeCode == 'th_TH') {
                    return throw new CouldNotSaveException(
                        __('ไม่สามารถเชื่อมต่อกับผู้ให้บริการชำระเงินได้ กรุณาลองใหม่อีกครั้งในภายหลัง')
                    );
                }
            }
        }
    }
}
