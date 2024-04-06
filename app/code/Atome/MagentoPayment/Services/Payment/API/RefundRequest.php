<?php

namespace Atome\MagentoPayment\Services\Payment\API;

use Atome\MagentoPayment\Services\Price\PriceService;
use Magento\Framework\App\ObjectManager;

class RefundRequest extends Request
{
    protected $referenceId;

    protected $amount;

    public function getUrl()
    {
        return $this->paymentGatewayConfig->getApiUrl("payments/{$this->referenceId}/refund");
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getPayload()
    {
        return ['refundAmount' => ObjectManager::getInstance()->get(PriceService::class)->format($this->amount)];
    }


    /**
     * @param string $referenceId
     * @return RefundRequest
     */
    public function setReferenceId($referenceId)
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    /**
     * @param float $amount
     * @return RefundRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
}
