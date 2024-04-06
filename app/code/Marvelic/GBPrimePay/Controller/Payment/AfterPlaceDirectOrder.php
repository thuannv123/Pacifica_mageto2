<?php
/**
 * GBPrimePay_Payments extension
 * @package GBPrimePay_Payments
 * @copyright Copyright (c) 2020 GBPrimePay Payments (https://gbprimepay.com/)
 */

namespace Marvelic\GBPrimePay\Controller\Payment;

use Magento\Framework\App\ResponseInterface;

class AfterPlaceDirectOrder extends \GBPrimePay\Payments\Controller\Checkout\AfterPlaceDirectOrder
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

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $postData = $_POST;
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("Payment Success");
                $this->gbprimepayLogger->debug("Data request Credit_Card:  //" . json_encode($postData));
            }
            $referenceNo = $postData['referenceNo'];
            $_orderId = substr($postData['referenceNo'], 7);
            $_transaction_id = $postData['merchantDefined1'];
            $_gbpReferenceNo = $postData['gbpReferenceNo'];
            $_gbpReferenceNum = substr($postData['gbpReferenceNo'], 3);
            $orderId = $this->getIncrementIdByOrderId($_orderId);    
            $order = $this->getQuoteByOrderId($orderId);
            $payment = $order->getPayment();
            $transaction_form = $payment->getAdditionalInformation("transaction_form");
            $isSave = $payment->getAdditionalInformation("isSave");
            $_amount = $order->getBaseCurrency()->formatTxt($postData['amount']);
            $payment_type = "gbprimepay_direct";
            $order_note = "Payment Authorized, Pay with Credit Card amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
            if ($postData['resultCode'] === '00') {
                if ($orderId) {
                    if ($isSave) {
                        $storedcard = $this->gbprimepayDirect->_storedcard($order->getPayment(), $order->getBaseGrandTotal());
                    }

                    if (
                        $order->getStatus() == \Magento\Sales\Model\Order::STATE_CANCELED &&
                        $order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED 
                    ) {
                        $this->gbprimepayCheckoutHelper->unCancelOrder($order);
                    }

                    if ($order->canInvoice() && !$order->hasInvoices()) {
                        
                        $this->generateInvoice($orderId, $payment_type);
                        $this->gbprimepayLogger->debug("Created invoice");
                        $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                        $this->gbprimepayLogger->debug("Created transaction");
                        $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                        
                        $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                        $this->checkoutSession->setLastOrderId($order->getId());
                        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                        $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                        $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                    }
                }else{
                    $this->gbprimepayLogger->debug("Error Order Id is none ". json_encode($orderId));
                }
            }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("AfterPlaceDirectOrder error//" . $exception->getMessage());
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