<?php

namespace Marvelic\GBPrimePay\Controller\Payment;

use Magento\Framework\App\ResponseInterface;
use GBPrimePay\Payments\Helper\Constant;

class PendingQrcode extends \GBPrimePay\Payments\Controller\Checkout\PendingQrcode
{
    protected $gbprimePayPurchaseCollection;

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
        \Marvelic\GBPrimePay\Model\GBPrimePayPurchaseFactory $gbprimePayPurchaseCollection,
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
        $this->gbprimePayPurchaseCollection = $gbprimePayPurchaseCollection;
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
            $transactionId = $this->getRequest()->getParam('key');
            $_orderId = $this->getRequest()->getParam('id');
            $orderId = $this->getIncrementIdByOrderId($_orderId);
            $order = $this->getQuoteByOrderId($orderId);
            $payment = $order->getPayment();
            $_getEntityId = $order->getEntityId();
            $_getIncrementId = $order->getIncrementId();
            $_getOrderByIncrementId = $this->getOrderIdByIncrementId($_getIncrementId);
            $_getOrderByEntityId = $this->getIncrementIdByOrderId($_getEntityId);
            if (($_orderId == $_getEntityId) && ($_getIncrementId == $_getOrderByEntityId)) {

                $transaction_getid = $order->getId();
                $_transaction_id = $this->transactiondigit($transaction_getid);
                //$_transaction_id_old = $this->_config->getGBPTransactionID();
                //$_transaction_key_old = $this->_config->getGBPTransactionKEY();
                //$generateitem = $this->_config->getGBPTransactionITEM();
                $_transaction_key = $this->_config->generateID();
                $transaction_form = $payment->getAdditionalInformation("transaction_form");
                $ordercompletestatus = $this->getOrderCompleteStatus($_getOrderByEntityId);

                if ($ordercompletestatus != 0) {
                    $this->gbprimepayLogger->debug("set checkout_session and done payment //" . json_encode($payment));

                    $this->checkoutRegistry->register('order_generate_qrcode', 0, false);
                    $this->checkoutRegistry->register('order_complete_qrcode', $ordercompletestatus, false);
                    $this->checkoutRegistry->register('order_id_qrcode', $orderId, false);
                    $this->checkoutRegistry->register('key_id_qrcode', $transaction_form, false);
                } else {
                    if ($this->_config->getEnvironment() === 'prelive') {
                        $url = Constant::URL_QRCODE_TEST;
                        $itemtoken = $this->_config->getTestTokenKey();
                    } else {
                        $url = Constant::URL_QRCODE_LIVE;
                        $itemtoken = $this->_config->getLiveTokenKey();
                    }
                    $customer_full_name = $order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname');
                    $itemquoteno = $_transaction_id;
                    $itemcustomerAddress = '';
                    $itemcustomerAddress .= '' . $customer_full_name . ' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('company') . ' ';
                    $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet()) > 0) ? $order->getBillingAddress()->getStreet()[0] : '' . ' ';
                    $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet()) > 1) ? $order->getBillingAddress()->getStreet()[1] : '' . ' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('city') . ' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('region') . ' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('postcode') . '';
                    $itemcustomerTelephone = '' . $order->getBillingAddress()->getTelephone();
                    $callgetMerchantId = $this->_config->getMerchantId();
                    $_transaction_key = $this->_config->generateID();

                    $itemdetail = 'Charge for order ' . $_getIncrementId;
                    $itemreferenceno = '' . substr(time(), 4, 5) . '00' . $_orderId;
                    $itemresponseurl = $this->_config->getresponseUrl('response_qrcode');
                    $itembackgroundurl = $this->_config->getresponseUrl('background_qrcode');
                    $amount = $order->getBaseGrandTotal();
                    $itemamount = number_format((($amount * 100) / 100), 2, '.', '');
                    $itemcustomerEmail = $order->getCustomerEmail();
                    $itemcustomerAddress = $itemcustomerAddress;
                    $itemcustomerTelephone = $itemcustomerTelephone;
                    $itemmerchantDefined1 = $this->_config->generateID();
                    $itemmerchantDefined2 = $order->getCustomerId();
                    $itemmerchantDefined3 = $itemquoteno;
                    $itemmerchantDefined4 = $itemreferenceno;
                    $itemmerchantDefined5 = $_getIncrementId;
                    $field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n$itemtoken\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$itemamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$itemreferenceno\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"backgroundUrl\"\r\n\r\n$itembackgroundurl\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"detail\"\r\n\r\n$itemdetail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerName\"\r\n\r\n$customer_full_name\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerEmail\"\r\n\r\n$itemcustomerEmail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerAddress\"\r\n\r\n$itemcustomerAddress\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerTelephone\"\r\n\r\n$itemcustomerTelephone\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;  name=\"merchantDefined1\"\r\n\r\n$itemmerchantDefined1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined2\"\r\n\r\n$itemmerchantDefined2\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined3\"\r\n\r\n$itemmerchantDefined3\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined4\"\r\n\r\n$itemmerchantDefined4\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined5\"\r\n\r\n$itemmerchantDefined5\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";

                    if ($this->_config->getCanDebug()) {
                        $this->log("----------Data sent to API for QrCode----------");
                        $this->log(json_encode($field));
                    }
                    if ($amount) {
                        $callback = $this->_config->sendQRCurl("$url", $field, 'POST');

                        if ($this->_config->getCanDebug()) {
                            $this->log("----------Data reponse to API for QrCode----------");
                            $this->log(json_encode($callback));
                        }

                        if ($callback == "Incomplete information") {
                        } else {
                            $purchaseDataCollection = $this->gbprimePayPurchaseCollection->create();
                            $purchaseData = [
                                'order_id' => $_orderId,
                                'referenceNo' => $itemreferenceno,
                            ];
                            $purchaseDataCollection->setData($purchaseData);
                            $purchaseDataCollection->save();
                            
                            $this->checkoutRegistry->register('order_generate_qrcode', $callback, false);
                            $this->checkoutRegistry->register('order_complete_qrcode', 0, false);
                            $this->checkoutRegistry->register('order_id_qrcode', $orderId, false);
                            $this->checkoutRegistry->register('key_id_qrcode', $transaction_form, false);
                            $this->sendEmailCustomer($order);
                        }
                    } else {
                        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                    }
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
            $result = $this->PageFactory->create();
            return $result;
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("PendingQrcode error //" . $exception->getMessage());
            }
            $this->cancelOrder();
            $this->checkoutSession->restoreQuote();
        }
    }
    function transactiondigit($string)
    {
        $strInt = intval($string);
        $strLen = 9;
        $strPad = str_pad(($strInt), $strLen, "0", STR_PAD_LEFT);
        return $strPad;
    }

    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/gbprimepay/qrcode_request.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}
