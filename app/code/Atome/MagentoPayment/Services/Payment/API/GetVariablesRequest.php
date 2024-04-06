<?php

namespace Atome\MagentoPayment\Services\Payment\API;

class GetVariablesRequest extends Request
{

    protected $country;

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl('variables/' . strtoupper($this->country));
    }

    /**
     * @param string $country
     * @return GetVariablesRequest
     */
    public function setCountry($country)
    {
        $this->country = strtoupper($country);
        return $this;
    }
}
