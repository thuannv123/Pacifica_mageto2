<?php

namespace Atome\MagentoPayment\Services\Payment\API;

class GetPaymentRequest extends Request
{

    protected $referenceId;

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl("payments/{$this->referenceId}");
    }

    /**
     * @param mixed $referenceId
     * @return GetPaymentRequest
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        return $this;
    }
}
