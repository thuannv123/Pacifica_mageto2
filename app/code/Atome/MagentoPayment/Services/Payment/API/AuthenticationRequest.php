<?php

namespace Atome\MagentoPayment\Services\Payment\API;

class AuthenticationRequest extends Request
{

    protected $callbackUrl;
    protected $country;
    protected $configuration;

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl('auth');
    }

    public function getMethod()
    {
        return 'POST';
    }


    public function getPayload()
    {
        return [
            "callbackUrl" => $this->callbackUrl,
            "countryCode" => strtoupper($this->country),
            "platform" => "MAGENTO",
            'pluginConfiguration' => $this->configuration ?: []
        ];
    }

    /**
     * @param mixed $callbackUrl
     * @return AuthenticationRequest
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * @param mixed $country
     * @return AuthenticationRequest
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param mixed $configuration
     * @return AuthenticationRequest
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }
}
