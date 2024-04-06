<?php

namespace Atome\MagentoPayment\Services\Payment\API;

class CancelPaymentRequest extends Request
{
    protected $referenceId;

    public function getMethod()
    {
        return 'POST';
    }

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl("payments/{$this->referenceId}/cancel");
    }

    /**
     * @param string $referenceId
     * @return CancelPaymentRequest
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        return $this;
    }


}
