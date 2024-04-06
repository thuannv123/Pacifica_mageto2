<?php

namespace Atome\MagentoPayment\Services\Payment\Context;


/**
 * @method getCode()
 * @method getMessage()
 * @method getReferenceId()
 * @method getCurrency()
 * @method getAmount()
 * @method getRefundableAmount()
 * @method getStatus()
 * @method getRedirectUrl()
 * @method getQrCodeUrl()
 * @method getPaymentResultUrl()
 * @method getPaymentCancelUrl()
 */
class PaymentResponse
{
    protected $_data;

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) === 'get') {
            $k = lcfirst(substr($method, 3));
            return $this->_data[$k] ?? null;
        }
        throw new \RuntimeException('unknown method call: ' . $method);
    }
}
