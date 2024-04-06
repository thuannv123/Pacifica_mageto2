<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Controller\Payment;

use Atome\MagentoPayment\Enum\ExceptionCode;
use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Throwable;

class Callback extends AtomeAction
{
    public function execute()
    {
        Logger::instance()->info(__METHOD__ . ' callback start.');

        $queryParams = $this->getRequest()->getParams();

        Logger::instance()->info(__METHOD__ . ' params:' . json_encode($queryParams));

        $orderId = $queryParams['orderId'] ?? null;
        $callback = null;
        try {
            $callback = ObjectManager::getInstance()
                ->create(\Atome\MagentoPayment\Services\Payment\Callback::class);

            $callback->setOrderId($orderId)
                ->setDebugPaymentStatus($queryParams['debugPaymentStatus'] ?? null)
                ->setDebugSecret($queryParams['debugSecret'] ?? null)
                ->handle();

        } catch (LocalizedException $e) {
            Logger::instance()->exception($e, __METHOD__);

            if ($e->getCode() !== ExceptionCode::PAYMENT_IS_PROCESSING) {
                $error = $e->getMessage();
            }
        } catch (Throwable $e) {
            Logger::instance()->exception($e, __METHOD__);
            $error = get_class($e) . ': ' . $e->getMessage();
        }

        $respData = [
            'orderId' => $orderId,
            'code' => 200,
            'message' => 'OK'
        ];
        if ($callback) {
            $respData['reservedOrderId'] = $callback->getIncrementId() ?: '';
            $respData['isOrderCreated'] = (boolean)$callback->getIncrementId();
        }

        if (!empty($error)) {
            $respData['code'] = 500;
            $respData['message'] = $error;
            $respData['error'] = true;
        }

        Logger::instance()->info(__METHOD__ . ' callback end:' . json_encode($respData));

        return $this->jsonResponse($respData, empty($error) ? 200 : 400);
    }
}
