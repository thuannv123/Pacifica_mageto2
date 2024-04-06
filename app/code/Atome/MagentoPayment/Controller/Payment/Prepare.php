<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Controller\Payment;

use Atome\MagentoPayment\Enum\AdditionalInformationKey;
use Atome\MagentoPayment\Enum\PaymentStatus;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Payment\API\CreatePaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\CreatePaymentResponse;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentResponse;
use Atome\MagentoPayment\Services\Price\PriceService;
use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Service\OrderService;

class Prepare extends AtomeAction
{
    use SimulatePayment;

    protected $checkoutSession;
    protected $paymentGatewayConfig;

    /**
     * @var PriceService
     */
    protected $priceService;

    public function __construct(
        Context              $context,
        CheckoutSession      $checkoutSession,
        PaymentGatewayConfig $paymentGatewayConfig,
        PriceService         $priceService
    )
    {
        parent::__construct($context);

        $this->checkoutSession = $checkoutSession;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->priceService = $priceService;
    }

    public function execute()
    {
        $orderId = 0;
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            if (!$order) {
                throw new Exception(__('Order not found.'));
            }
            $payment = $order->getPayment();
            if (!$payment) {
                throw new Exception(__('Order Payment is empty.'));
            }
            $method = $order->getPayment()->getMethod();
            if ($method !== Atome::METHOD_CODE) {
                throw new Exception(__('Payment method code mismatch `' . Atome::METHOD_CODE . '`.'));
            }

            $oldReferenceId = $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID);

            $orderId = $order->getEntityId();

            if ($oldReferenceId && $alreadyPaidResponse = $this->alreadyPaid($oldReferenceId, $order)) {
                Logger::instance()->info(json_encode([
                    'message' => 'payment already paid.',
                    'orderId' => $orderId,
                    'paymentId' => $payment->getEntityId(),
                    'oldReferenceId' => $oldReferenceId,
                    'oldAmountFormatted' => $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED),
                    'oldCurrencyCode' => $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE)
                ]));
                return $alreadyPaidResponse;
            }

            return $this->createPayment($order, $payment);
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            Logger::instance()->error(json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]));

            $this->checkoutSession->restoreQuote();
            if ($orderId) {
                $this->cancelOrder($orderId);
            }

            return $this->redirectResponse('checkout/cart');
        }
    }

    /**
     * @param Order $order
     * @param OrderPaymentInterface $payment
     * @return Json|RedirectFactory
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    protected function createPayment($order, $payment)
    {
        $createPaymentRequest = new CreatePaymentRequest();
        $createPaymentRequest->setOrder($order);

        if ($this->isSimulationEnvironment()) {
            $createPaymentResponse = $createPaymentRequest->getSimulateCreatePaymentResponse();
        } else {
            $createPaymentRequest->request();
            /** @var CreatePaymentResponse $createPaymentResponse */
            $createPaymentResponse = $createPaymentRequest->getWrappedResponse();
        }

        if (!$createPaymentResponse->getReferenceId()) {
            return $this->jsonResponse(['error' => true, 'message' => $createPaymentResponse->getMessage()]);
        }

        $this->setPaymentAdditionalInformation($payment, $createPaymentRequest, $createPaymentResponse);

        ObjectManager::getInstance()->get(\Magento\Sales\Model\ResourceModel\Order::class)->save($order);

        return $this->redirectResponse($createPaymentResponse->getRedirectUrl());
    }

    /**
     * @param $payment
     * @param CreatePaymentRequest $createPaymentRequest
     * @param CreatePaymentResponse $createPaymentResponse
     * @return void
     * @throws Exception
     */
    protected function setPaymentAdditionalInformation($payment, $createPaymentRequest, $createPaymentResponse)
    {
        $payment->setAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID, $createPaymentResponse->getReferenceId());
        $payment->setAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED, $createPaymentResponse->getAmount());
        $payment->setAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE, $createPaymentResponse->getCurrency());

        if ($this->paymentGatewayConfig->isDebugEnabled()) {
            $this->setDebugSecret($payment, $createPaymentRequest);
        } else {
            $payment->unsAdditionalInformation(AdditionalInformationKey::PAYMENT_DEBUG_SECRET);
        }
    }

    /**
     * @param $payment
     * @param CreatePaymentRequest $createPaymentRequest
     * @return void
     * @throws Exception
     */
    protected function setDebugSecret($payment, $createPaymentRequest)
    {
        $debugSecret = trim(base64_encode(random_bytes(9)) ?? '', '=');
        $debugSecret = str_replace(['+', '/'], ['-', '.'], $debugSecret);
        $payment->setAdditionalInformation(AdditionalInformationKey::PAYMENT_DEBUG_SECRET, $debugSecret);

        foreach ($createPaymentRequest->getPayload() as $k => $v) {
            if (substr($k, -3) === 'Url') {
                $url = $v;
                $url .= (strpos($v, '?') === false ? '?' : '&') . 'debugSecret=' . rawurlencode($debugSecret ?? '');
                Logger::instance()->debug("debug url: $k: $url");
            }
        }
    }

    /**
     * @param $referenceId
     * @param Order $order
     * @return RedirectFactory|void
     * @throws LocalizedException
     */
    protected function alreadyPaid($referenceId, $order)
    {
        $orderId = $order->getEntityId();

        $getPaymentRequest = new GetPaymentRequest();
        $getPaymentRequest->setReferenceId($referenceId);

        try {
            $getPaymentRequest->request();
        } catch (\Throwable $throwable) {
            if ($throwable->getCode() !== 404) {
                Logger::instance()->error($throwable->getMessage());
            }

            return null;
        }

        /** @var GetPaymentResponse $getPaymentResponse */
        $getPaymentResponse = $getPaymentRequest->getWrappedResponse();
        if ($getPaymentResponse->getStatus() === PaymentStatus::PAID) {
            return $this->redirectResponse("atome/payment/result?type=result&orderId={$orderId}");
        } else if (
            $getPaymentResponse->getMerchantReferenceId() == $order->getIncrementId() &&
            $getPaymentResponse->getAmount() == $this->priceService->format($order->getGrandTotal()) &&
            $getPaymentResponse->getCurrency() == $order->getOrderCurrencyCode() &&
            $getPaymentResponse->getStatus() == PaymentStatus::PROCESSING
        ) {
            return $this->redirectResponse($getPaymentResponse->getRedirectUrl());
        }

        return null;
    }


    protected function cancelOrder($orderId)
    {
        /** @var OrderService $orderService */
        $orderService = $this->_objectManager->get(OrderService::class);
        $orderService->cancel($orderId);
    }
}

