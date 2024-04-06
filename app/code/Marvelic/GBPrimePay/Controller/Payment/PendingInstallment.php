<?php
namespace Marvelic\GBPrimePay\Controller\Payment;

class PendingInstallment extends \GBPrimePay\Payments\Controller\Checkout\PendingInstallment
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
     * @return \Magento\Framework\Controller\ResultInterface\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        // $postData = $_POST;
        $postData = $this->getRequest()->getParams();
        if (isset($postData['resultCode'])) {
            $referenceNo = $postData['referenceNo'];
            $_orderId = substr($postData['referenceNo'], 7);
            $_gbpReferenceNo = $postData['gbpReferenceNo'];
            $_gbpReferenceNum = substr($postData['gbpReferenceNo'], 3);
            $resultRedirect = $this->RedirectFactory->create();
            $orderId = $this->getIncrementIdByOrderId($_orderId);
            $order = $this->getQuoteByOrderId($orderId);
            $_getCustomerId = $order->getCustomerId();
            $payment = $order->getPayment();
            $transaction_form_additional = $payment->getAdditionalInformation("transaction_form");
            if ($postData['resultCode']) {
                $isLogin = $this->customerSession->isLoggedIn();
                if ($isLogin) {
                } else {
                    if (!empty($_getCustomerId)) {
                        $transaction_form = $this->reloadCustomerId($payment, $_getCustomerId, $transaction_form_additional);
                    }
                }
                if ($postData['resultCode'] === '00') {
                    if ($orderId) {
                        $_getOrderCompleteStatus = $this->getOrderCompleteStatus($orderId);
                        if ($_getOrderCompleteStatus != 0) {

                            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                            $this->checkoutSession->setLastOrderId($order->getId());
                            $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                            $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                            $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());

                            if (empty($transaction_form)) {
                                $transaction_form = $transaction_form_additional;
                            }

                            $this->checkoutRegistry->register('referenceNo', $postData['referenceNo'], false);
                            $this->checkoutRegistry->register('gbpReferenceNo', $postData['gbpReferenceNo'], false);
                            $this->checkoutRegistry->register('amount', $postData['amount'], false);
                            $this->checkoutRegistry->register('orderId', $orderId, false);
                            $this->checkoutRegistry->register('transaction_form', $transaction_form, false);
                            $this->checkoutRegistry->register('payStatus', "success", false);
                        }
                    }
                } else {
                    if ($orderId) {
                        if (empty($transaction_form)) {
                            $transaction_form = $transaction_form_additional;
                        }
                        $order_note = "Payment Failure, Transaction cannot be authorized";
                        $this->failureOrder($orderId, "canceled", $order_note);
                        $this->checkoutRegistry->register('referenceNo', $postData['referenceNo'], false);
                        $this->checkoutRegistry->register('gbpReferenceNo', $postData['gbpReferenceNo'], false);
                        $this->checkoutRegistry->register('amount', $postData['amount'], false);
                        $this->checkoutRegistry->register('orderId', $orderId, false);
                        $this->checkoutRegistry->register('transaction_form', $transaction_form, false);
                        $this->checkoutRegistry->register('payStatus', "failure", false);
                    } else {
                        $this->checkoutRegistry->register('referenceNo', '', false);
                        $this->checkoutRegistry->register('gbpReferenceNo', '', false);
                        $this->checkoutRegistry->register('amount', '', false);
                        $this->checkoutRegistry->register('orderId', '', false);
                        $this->checkoutRegistry->register('transaction_form', '', false);
                        $this->checkoutRegistry->register('payStatus', "failure", false);
                    }
                }
            } else {
                $this->checkoutRegistry->register('referenceNo', '', false);
                $this->checkoutRegistry->register('gbpReferenceNo', '', false);
                $this->checkoutRegistry->register('amount', '', false);
                $this->checkoutRegistry->register('orderId', '', false);
                $this->checkoutRegistry->register('transaction_form', '', false);
                $this->checkoutRegistry->register('payStatus', "failure", false);
            }

            return $this->PageFactory->create();
        } else {
            $resultRedirect = $this->RedirectFactory->create();
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }

    public function modifyCsp(array $appliedPolicies): array
    {
        $appliedPolicies[] = new \Magento\Csp\Model\Policy\FetchPolicy(
            'form-action',
            false,
            ['https://api.gbprimepay.com/web/krungsri_gateway/receive/success', 'https://api.gbprimepay.com/web/ktc_gateway/success', 'https://api.gbprimepay.com/web/ktc_gateway/cancel', 'https://api.gbprimepay.com/web/ktc_gateway/fail', 'https://api.gbprimepay.com/web/bbl_gateway/receive/goback/success', 'https://api.gbprimepay.com/web/bbl_gateway/receive/goback/fail', 'https://api.gbprimepay.com/web/bbl_gateway/receive/goback/cancel', 'https://api.gbprimepay.com/web/thanachat_gateway/receive/go_back', 'https://api.gbprimepay.com/web/scb_gateway/receive/goback', 'https://api.gbprimepay.com/web/gateway/receive/goback', 'https://api.gbprimepay.com/gbp/gateway/receive/goback', 'https://api.globalprimepay.com/web/scb_gateway/receive/goback', 'https://api.globalprimepay.com/gbp/gateway/pay/scbInstallment'],
            ['https']
        );

        return $appliedPolicies;
    }
}
