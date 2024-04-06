<?php

namespace Marvelic\GBPrimePay\Controller\Payment;

class AfterPlaceQrcreditOrder extends \GBPrimePay\Payments\Controller\Checkout\AfterPlaceQrcreditOrder
{
    protected $gbprimepayCheckoutHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order $orderPayment,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Payment\Transaction\Builder $transactionBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $checkoutRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \GBPrimePay\Payments\Logger\Logger $gbprimepayLogger,
        \GBPrimePay\Payments\Helper\ConfigHelper $configHelper,
        \GBPrimePay\Payments\Controller\Checkout\CsrfValidator $CsrfValidator,
        \GBPrimePay\Payments\Model\GBPrimePayDirect $gbprimepayDirect,
        \GBPrimePay\Payments\Model\GBPrimePayInstallment $gbprimepayInstallment,
        \GBPrimePay\Payments\Model\GBPrimePayQrcode $gbprimepayQrcode,
        \GBPrimePay\Payments\Model\GBPrimePayQrcredit $gbprimepayQrcredit,
        \GBPrimePay\Payments\Model\GBPrimePayQrwechat $gbprimepayQrwechat,
        \GBPrimePay\Payments\Model\GBPrimePayBarcode $gbprimepayBarcode,
        \Magento\Sales\Model\ResourceModel\Order\Payment\Collection $collectionFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Quote\Api\CartManagementInterface $placeManagement,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \Marvelic\GBPrimePay\Helper\GBPrimePayCheckout $gbprimepayCheckoutHelper,
        $params = []
    ) {
        parent::__construct(
            $context,
            $paymentHelper,
            $formKeyValidator,
            $orderFactory,
            $quoteFactory,
            $orderPayment,
            $invoiceCollectionFactory,
            $invoiceService,
            $transactionFactory,
            $transactionBuilder,
            $orderRepository,
            $quoteRepository,
            $checkoutSession,
            $checkoutRegistry,
            $resultPageFactory,
            $resultRedirectFactory,
            $customerRepository,
            $customerSession,
            $checkoutData,
            $resultJsonFactory,
            $orderSender,
            $invoiceSender,
            $moduleManager,
            $storeManager,
            $gbprimepayLogger,
            $configHelper,
            $CsrfValidator,
            $gbprimepayDirect,
            $gbprimepayInstallment,
            $gbprimepayQrcode,
            $gbprimepayQrcredit,
            $gbprimepayQrwechat,
            $gbprimepayBarcode,
            $collectionFactory,
            $orderManagement,
            $placeManagement,
            $paymentFactory
        );
        $this->gbprimepayCheckoutHelper = $gbprimepayCheckoutHelper;
    }

    public function execute()
    {
        try {
            $raw_post = @file_get_contents('php://input');
            $payload  = json_decode($raw_post);
            $referenceNo = $payload->{'referenceNo'};
            $_orderId = substr($payload->{'referenceNo'}, 7);
            $_transaction_id = $payload->{'merchantDefined1'};
            $_gbpReferenceNo = $payload->{'gbpReferenceNo'};
            $_gbpReferenceNum = substr($payload->{'gbpReferenceNo'}, 3);
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("QR Visa Callback Handler //" . print_r($payload, true));
            }
            $orderId = $this->getIncrementIdByOrderId($_orderId);
            $order = $this->getQuoteByOrderId($orderId);
            $_amount = $order->getBaseCurrency()->formatTxt($payload->{'amount'});
            $payment_type = "gbprimepay_qrcredit";
            $order_note = "Payment Authorized, Pay with QR Visa amount: " . $_amount . ". Reference ID: " . "\"$_gbpReferenceNum\"";
            if ($payload->{'resultCode'} === '00') {
                if ($orderId) {

                    if (
                        $order->getStatus() == \Magento\Sales\Model\Order::STATE_CANCELED &&
                        $order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED
                    ) {
                        $this->gbprimepayCheckoutHelper->unCancelOrder($order);
                    }

                    if ($order->canInvoice() && !$order->hasInvoices()) {
                        $this->generateInvoice($orderId, $payment_type);
                        $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                        $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                        $this->checkoutSession->clearQuote();
                    }
                }
            }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("AfterPlaceQrcreditOrder error//" . $exception->getMessage());
            }
            $this->cancelOrder();
            $this->checkoutSession->restoreQuote();

            return $this->jsonFactory->create()->setData([
                'success' => false,
                'error' => true,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
