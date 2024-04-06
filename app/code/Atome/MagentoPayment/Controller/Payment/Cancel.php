<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Controller\Payment;

use Atome\MagentoPayment\Enum\ExceptionCode;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Service\OrderService;

class Cancel extends AtomeAction
{
    protected $checkoutSession;
    protected $paymentGatewayConfig;
    protected $controllerContext;
    protected $jsonFactory;

    public function __construct(
        Context              $context,
        Session              $checkoutSession,
        PaymentGatewayConfig $paymentGatewayConfig,
        JsonFactory          $jsonFactory
    )
    {
        parent::__construct($context);
        $this->controllerContext = $context;
        $this->checkoutSession = $checkoutSession;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        Logger::instance()->info('action Cancel: begin');
        $queryParams = $this->getRequest()->getParams();
        Logger::instance()->info('action Cancel: queryParams => ' . json_encode($queryParams));
        $orderId = $queryParams['orderId'] ?? null;
        if ($orderId) {
            try {
                $order = ObjectManager::getInstance()->get(OrderRepository::class)->get($orderId);
                if (empty($order)) {
                    throw new Exception("no this order: " . $orderId);
                }
                $payment = $order->getPayment();
                if ($payment->getMethod() !== Atome::METHOD_CODE) {
                    throw new Exception("Wrong payment gateway: " . $payment->getMethod());
                }

                if ($order->getStatus() !== $this->paymentGatewayConfig->getNewOrderState()) {
                    throw new Exception("Wrong order status: " . $order->getStatus());
                }
                $this->_objectManager->get(OrderService::class)->cancel($orderId);

            } catch (LocalizedException $e) {
                Logger::instance()->error('action cancel LocalizedException: ' . $e->getMessage());
                if ($e->getCode() !== ExceptionCode::PAYMENT_IS_PROCESSING) {
                    $error = $e->getMessage();
                }
            } catch (Exception $e) {
                Logger::instance()->error("action cancel : " . $e->getMessage());
                $error = get_class($e) . ': ' . $e->getMessage();
            }
        } else {
            $error = "unsupported payment action";
        }

        $resp = $this->jsonFactory->create();
        $respData = [];
        $respData['orderId'] = $orderId;

        if (!empty($error)) {
            $respData['error'] = true;
            $respData['message'] = $error;
        } else {
            $respData['deleted'] = true;
        }
        $resp->setData($respData);
        Logger::instance()->info('action Cancel: respData => ' . json_encode($respData));
        return $resp;
    }

}
