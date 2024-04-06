<?php

namespace Atome\MagentoPayment\Controller\Payment;


use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;

class Ping extends AtomeAction
{

    /**
     * This method is used by Atome to verify that the server callbacks are normal
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        return $this->jsonResponse(["code" => 200, "message" => "OK"]);
    }
}
