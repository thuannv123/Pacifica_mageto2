<?php

namespace Atome\MagentoPayment\Services\Payment\API;

use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Payment\Adapter\Http\ApiCall;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

abstract class Request
{
    /**
     * @var ApiCall
     */
    protected $apiCall;
    /**
     * @var PaymentGatewayConfig
     */
    protected $paymentGatewayConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected $method = 'GET';
    protected $url = '';

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;


    public function __construct()
    {
        $this->apiCall = ObjectManager::getInstance()->create(ApiCall::class);
        $this->paymentGatewayConfig = ObjectManager::getInstance()->create(PaymentGatewayConfig::class);;
        $this->objectManager = ObjectManager::getInstance();
    }


    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws LocalizedException
     */
    public function request()
    {
        $this->response = $resp = $this->apiCall->send(
            $this->getUrl(),
            $this->getPayload(),
            $this->getMethod()
        );

        if (!($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300)) {
            throw new LocalizedException(
                __('Atome API Error: HTTP=%1, Code=%2, Message=%3', $resp->getStatusCode(), '', (string)$this->response->getBody())
            );
        }

        return $resp;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getWrappedResponse()
    {
        $responseClass = substr(get_class($this), 0, -7) . 'Response';
        if (class_exists($responseClass)) {
            $responseInstance = $this->objectManager->create($responseClass);
        } else {
            $responseInstance = $this->objectManager->create(NormalResponse::class);
        }

        $responseInstance->setData(@json_decode((string)$this->response->getBody(), true) ?: null);

        return $responseInstance;
    }

    abstract public function getUrl();

    public function getMethod()
    {
        return 'GET';
    }

    public function getPayload()
    {
        return [];
    }
}
