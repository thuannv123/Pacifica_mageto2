<?php

namespace Atome\MagentoPayment\Services\Payment;

use Atome\MagentoPayment\Enum\AdditionalInformationKey;
use Atome\MagentoPayment\Enum\ExceptionCode;
use Atome\MagentoPayment\Enum\PaymentStatus;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentResponse;
use Atome\MagentoPayment\Services\Price\PriceService;
use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Service\OrderService;
use Throwable;

class Callback
{

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $debugSecret;

    /**
     * @var string
     */
    protected $debugPaymentStatus;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;
    /**
     * @var OrderPaymentInterface
     */
    protected $payment;

    /**
     * @var PaymentGatewayConfig
     */
    protected $paymentGatewayConfig;

    /**
     * @var GetPaymentResponse
     */
    protected $getPaymentResponse;

    public function __construct(PaymentGatewayConfig $paymentGatewayConfig)
    {
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }

    /**
     * @param int $orderId
     * @return Callback
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @param mixed $debugSecret
     * @return Callback
     */
    public function setDebugSecret($debugSecret)
    {
        $this->debugSecret = $debugSecret;
        return $this;
    }

    /**
     * @param string $debugPaymentStatus
     * @return Callback
     */
    public function setDebugPaymentStatus($debugPaymentStatus)
    {
        $this->debugPaymentStatus = $debugPaymentStatus;
        return $this;
    }

    public function getIncrementId()
    {
        return $this->order->getIncrementId();
    }

    protected function setOrder()
    {
        $this->order = ObjectManager::getInstance()->create(OrderRepository::class)->get($this->orderId);

        return $this;
    }

    protected function setQuote()
    {
        $quoteId = $this->order->getQuoteId();

        $quote = ObjectManager::getInstance()->create(Quote::class);
        ObjectManager::getInstance()->create(\Magento\Quote\Model\ResourceModel\Quote::class)
            ->loadByIdWithoutStore($quote, $quoteId);

        $this->quote = $quote;

        return $this;
    }


    protected function setPayment()
    {
        $this->payment = $this->order->getPayment();

        return $this;
    }

    protected function setPaymentResponse()
    {
        if ($this->paymentGatewayConfig->isDebugEnabled()) {
            $paymentDebugSecret = $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_DEBUG_SECRET);
            if ($paymentDebugSecret && $paymentDebugSecret == $this->debugSecret) {
                $this->getPaymentResponse = $this->getPaymentByMock();
                return $this;
            }
        }

        $this->getPaymentResponse = $this->getPaymentByAPI();

        return $this;

    }

    /**
     * @return GetPaymentResponse
     * @throws LocalizedException
     */
    protected function getPaymentByAPI()
    {
        $getPaymentRequest = new GetPaymentRequest();
        $getPaymentRequest->setReferenceId($this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID));
        $getPaymentRequest->request();
        return $getPaymentRequest->getWrappedResponse();
    }

    protected function getPaymentByMock()
    {
        $mockData = [
            'referenceId' => $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID),
            'currency' => $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE),
            'amount' => $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED),
            'status' => $this->debugPaymentStatus ?: PaymentStatus::PAID,
        ];

        Logger::instance()->debug('Mock GetPaymentResponse', [
            'method' => __METHOD__,
            'mock_data' => $mockData
        ]);

        return ObjectManager::getInstance()->create(GetPaymentResponse::class)->setData($mockData);
    }

    public function handle()
    {
        $this->setOrder();
        $this->setQuote();
        $this->setPayment();
        $this->setPaymentResponse();

        $this->validate();
        $this->handleByStatus($this->getPaymentResponse->getStatus());
    }


    protected function handleByStatus($status)
    {
        switch ($status) {
            case PaymentStatus::PROCESSING:
                throw new LocalizedException(
                    __("Atome payment is processing. Please wait a while."),
                    null,
                    ExceptionCode::PAYMENT_IS_PROCESSING
                );
            case PaymentStatus::CANCELLED:
                $this->whenPaymentFailed(PaymentStatus::CANCELLED);
                break;
            case PaymentStatus::FAILED:
                $this->whenPaymentFailed(PaymentStatus::FAILED);
                break;
            case PaymentStatus::REFUNDED:
                $this->whenPaymentFailed(PaymentStatus::REFUNDED);
                break;
            case PaymentStatus::PAID:
                $this->whenPaymentPaid();
                break;
        }
    }

    protected function whenPaymentPaid()
    {
        $connection = ObjectManager::getInstance()
            ->get(ResourceConnection::class)
            ->getConnection('sales');

        try {
            $connection->beginTransaction();

            if (!$this->hasInvoice()) {
                $this->createInvoice();
            }

            $status = $this->paymentGatewayConfig->getOrderStatus();
            $state = $this->paymentGatewayConfig->getOrderState();
            $this->order->setState($state)->setStatus($status);
            $this->order->addStatusToHistory($this->order->getStatus(), 'Successful payment with Atome');

            $this->order->setBaseCustomerBalanceInvoiced(null);
            $this->order->setCustomerBalanceInvoiced(null);

            ObjectManager::getInstance()
                ->get(OrderRepository::class)
                ->save($this->order);

            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
            Logger::instance()->error(
                $e->getMessage(),
                [
                    'method' => __METHOD__,
                    'trace' => $e->getTraceAsString()
                ]
            );

            throw $e;
        }

        $this->sendEmail();
    }


    protected function createInvoice()
    {
        $referenceId = $this->getPaymentResponse->getReferenceId();

        $this->payment->capture();
        $orderAmountFormatted = $this->order->getBaseCurrency()->formatTxt($this->order->getGrandTotal());

        $transaction = ObjectManager::getInstance()
            ->get(\Magento\Sales\Model\Order\Payment\Transaction\Repository::class)
            ->save(
                ObjectManager::getInstance()
                    ->get(BuilderInterface::class)
                    ->setPayment($this->payment)
                    ->setOrder($this->order)
                    ->setTransactionId($referenceId)
                    ->setFailSafe(true)
                    // can not use payment here, or it will not be displayed in the Order View => Transactions list
                    ->build(TransactionInterface::TYPE_CAPTURE)
            );

        $this->payment->setLastTransId($referenceId);
        $this->payment->setTransactionId($referenceId);
        $this->payment->addTransactionCommentsToOrder(
            $transaction,
            __('The paid amount: %1.', $orderAmountFormatted)
        );
        $this->payment->setParentTransactionId(null);
        try {
            ObjectManager::getInstance()
                ->get(Repository::class)
                ->save($this->payment);
        } catch (AlreadyExistsException $alreadyExistsException) {
            /*
             * ---------------------------------------------------
             * When a unique key constraint violation error occurs,
             * it indicates that the record has been successfully created by another parallel request,
             * so this error can be simply ignored.
             * ---------------------------------------------------
             */
            Logger::instance()->info(
                $alreadyExistsException->getMessage(),
                ['method' => __METHOD__, 'orderId' => $this->orderId]
            );
        }
    }

    /**
     * @return bool
     */
    protected function hasInvoice()
    {
        foreach ($this->order->getInvoiceCollection() as $invoice) {
            if ($invoice->getTransactionId() == $this->getPaymentResponse->getReferenceId()) {
                return true;
            }
        }

        return false;
    }


    protected function validate()
    {
        if (!$this->order instanceof Order) {
            throw new Exception("Order `{$this->orderId}` not found.");
        }

        if ($this->payment->getMethod() !== Atome::METHOD_CODE) {
            throw new Exception("Payment method code mismatch `" . Atome::METHOD_CODE . '`');
        }

        // fixme 验证是否已回调过

        $paymentAmountFormatted = $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED);
        $paymentCurrencyCode = $this->payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE);

        if ($this->getPaymentResponse->getAmount() != $paymentAmountFormatted || $this->getPaymentResponse->getCurrency() != $paymentCurrencyCode) {
            throw new LocalizedException(__('There are issues when processing your payment. Invalid payment request.'));
        }

        $isAmountMismatch = $this->getPaymentResponse->getAmount() != ObjectManager::getInstance()->create(PriceService::class)->format($this->order->getGrandTotal());
        $isCurrencyMismatch = $this->getPaymentResponse->getCurrency() != $this->order->getOrderCurrencyCode();
        if ($isAmountMismatch || $isCurrencyMismatch) {
            throw new LocalizedException(__('There are issues when processing your payment. The payment amount or currency does not match the order.'));
        }

        $isOrderStateMismatch = !in_array($this->order->getState(), [
            Order::STATE_PENDING_PAYMENT,
            Order::STATE_PROCESSING,
            Order::STATE_COMPLETE,
            $this->paymentGatewayConfig->getNewOrderState()
        ]);

        if ($isOrderStateMismatch) {
            throw new LocalizedException(__("The order has wrong state: {$this->order->getStatus()}"));
        }
    }


    protected function whenPaymentFailed($paymentStatus)
    {
        ObjectManager::getInstance()->create(OrderService::class)->cancel($this->orderId);
        $this->order->addStatusToHistory(
            $this->order->getStatus(),
            "Payment status change to " . $paymentStatus
        );

        $this->quote->setIsActive(false);
        ObjectManager::getInstance()->create(\Magento\Quote\Model\ResourceModel\Quote::class)->save($this->quote);
    }

    protected function sendEmail()
    {
        if ($this->order && $this->quote && $this->hasInvoice()) {
            $this->sendPaymentNewEmail($this->quote, $this->order);
        }
    }


    /**
     * @param Quote $quote
     * @param Order $order
     */
    private function sendPaymentNewEmail($quote, $order)
    {
        $this->order->setCanSendNewEmailFlag(true);

        $orderSender = ObjectManager::getInstance()
            ->get(OrderSender::class);

        if ($this->paymentGatewayConfig->getOrderEmailSendBy() === 'atome') {
            try {
                Logger::instance()->info("send order confirmation email by atome: quote={$quote->getEntityId()},orderId={$order->getEntityId()}, order={$order->getIncrementId()}");
                $sendRes = $orderSender->send($order);
                Logger::instance()->info("send order confirmation email by atome result:" . json_encode($sendRes));

                Logger::instance()->info("send invoice email by atome: quote={$quote->getEntityId()},orderId={$order->getEntityId()}, order={$order->getIncrementId()}");
                $this->sendInvoiceEmail($order->getEntityId());
                Logger::instance()->info("send invoice email by atome: quote={$quote->getEntityId()},orderId={$order->getEntityId()}, order={$order->getIncrementId()}");

            } catch (\Exception $e) {
                Logger::instance()->error("send new payment email by atome exception: failed to send email by atome: " . json_encode($e));
            }
        } else {
            $this->sendEmailByMagento($quote, $order, $orderSender);
        }
    }

    protected function sendEmailByMagento($quote, $order, $orderSender)
    {
        // a flag to set that there will be redirect to third party after confirmation
        $emailRedirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();
        // we only want to send to customer about new order when there is no redirect to third party
        if (!$emailRedirectUrl && $order->getCanSendNewEmailFlag()) {
            try {
                Logger::instance()->info("send new payment email by default: quote={$quote->getEntityId()}, order={$order->getIncrementId()}, url=$emailRedirectUrl");
                $asyncSending = ObjectManager::getInstance()->get(\Magento\Framework\App\Config\ScopeConfigInterface::class)->getValue('sales_email/general/async_sending');
                if ($asyncSending || !$order->getEmailSent()) {
                    Logger::instance()->info("send new payment email by default: begin sending");
                    $sendRes = $orderSender->send($order);
                    Logger::instance()->info("send result: " . json_encode($sendRes));
                }
            } catch (\Exception $e) {
                Logger::instance()->error("failed to send email by default: " . json_encode($e));
            }
        } else {
            Logger::instance()->info("skip new payment email by default: quote={$quote->getEntityId()}, order={$order->getIncrementId()}, url=$emailRedirectUrl");
        }
    }

    protected function sendInvoiceEmail($orderId)
    {
        $orderRep = ObjectManager::getInstance()->get(OrderRepository::class);
        $order = $orderRep->get($orderId);
        foreach ($order->getInvoiceCollection() as $invoice) {
            $invoiceSender = ObjectManager::getInstance()->get(\Magento\Sales\Model\Order\Email\Sender\InvoiceSender::class);
            $invoiceSender->send($invoice);
        }
    }
}
