<?php

namespace Marvelic\GBPrimePay\Cron;

use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory as PaymentCollectionFactory;
use Magento\Sales\Model\Order;
use GBPrimePay\Payments\Model\GBPrimePayBarcode;
use GBPrimePay\Payments\Model\GBPrimePayDirect;
use GBPrimePay\Payments\Model\GBPrimePayInstallment;
use GBPrimePay\Payments\Model\GBPrimePayQrcode;
use GBPrimePay\Payments\Model\GBPrimePayQrcredit;
use GBPrimePay\Payments\Model\GBPrimePayQrwechat;
use Magento\Framework\Api\SearchCriteriaBuilder;

class GBPrimePayCheckOrder
{
    protected $curl;

    protected $_storeManager;

    protected $configHelper;

    protected $request;

    protected $paymentCollectionFactory;

    protected $methodCodes = [
        GBPrimePayBarcode::CODE,
        GBPrimePayDirect::CODE,
        GBPrimePayInstallment::CODE,
        GBPrimePayQrcode::CODE,
        GBPrimePayQrcredit::CODE,
        GBPrimePayQrwechat::CODE
    ];

    protected $paymentStatuses = [
        Order::STATE_CANCELED,
        Order::STATE_CLOSED,
        Order::STATE_COMPLETE,
        Order::STATE_HOLDED,
        Order::STATE_PROCESSING
    ];
    protected $paymentStates = [
        Order::STATE_CANCELED,
        Order::STATE_CLOSED,
        Order::STATE_COMPLETE,
        Order::STATE_HOLDED,
        Order::STATE_PROCESSING
    ];

    protected $statusAndstate = [
        'new',
        'pending',
        'pending_payment'
    ];

    protected $orderRepository;

    protected $gbprimePayPurchaseCollection;

    protected $curlReferenceNo;

    protected $gbprimePayCheckoutHelper;

    protected $checkoutSession;

    protected $searchCriteriaBuilder;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Marvelic\GBPrimePay\Helper\GBPrimePayCheckout $gbprimePayCheckoutHelper,
        \GBPrimePay\Payments\Helper\ConfigHelper $configHelper,
        PaymentCollectionFactory $paymentCollectionFactory,
        \Marvelic\GBPrimePay\Model\GBPrimePayPurchaseFactory $gbprimePayPurchaseCollection,
        \Marvelic\GBPrimePay\Helper\CurlReferenceNo $curlReferenceNo,
        \Magento\Checkout\Model\Session $checkoutSession,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->configHelper = $configHelper;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->gbprimePayPurchaseCollection = $gbprimePayPurchaseCollection;
        $this->curlReferenceNo = $curlReferenceNo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->gbprimePayCheckoutHelper = $gbprimePayCheckoutHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        try {
            $paymentMethods = $this->methodCodes;
            $statuses = $this->paymentStatuses;
            $states = $this->paymentStates;

            $payments = $this->getPaymentsByMethodsAndStatuses($paymentMethods, $statuses, $states);

            foreach ($payments as $payment) {
                // Process each payment as needed
                $order = $payment->getOrder();
                $orderId = $order->getEntityId();

                $purchaseCollection = $this->gbprimePayPurchaseCollection
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $orderId)
                    ->load();
                foreach ($purchaseCollection as $item) {
                    $field = [
                        'referenceNo' => $item->getData('referenceNo')
                    ];
                    $response = $this->curlReferenceNo->sendCurlReferenceNo(json_encode($field));

                    if (isset($response['resultCode']) && $response['resultCode'] === '02' && $response['resultMessage'] === 'Invalid referenceNo.') {
                        $itemreferenceno = $this->getReferenceNoByCreatedAt($order, $orderId);
                        $field = [
                            'referenceNo' => $itemreferenceno
                        ];

                        $response = $this->curlReferenceNo->sendCurlReferenceNo(json_encode($field));

                        $this->log('Reference No: ' . $itemreferenceno);
                        $jsonData = json_encode($response, JSON_PRETTY_PRINT);
                        $formattedData = str_replace("<br />", "", $jsonData);
                        $this->log($formattedData);

                        if (isset($response['resultCode']) && $response['resultCode'] === '00') {
                            if (isset($response['txn']['status']) && $response['txn']['status'] == 'S') {
                                $payment_type = $order->getPayment()->getMethod();
                                $_transaction_id = $response['txn']['merchantDefined1'];
                                $_gbpReferenceNum = substr($response['txn']['gbpReferenceNo'], 3);
                                $_amount = $order->getBaseCurrency()->formatTxt($response['txn']['amount']);
                                $methodTitle = $order->getPayment()->getMethodInstance()->getTitle();
                                $order_note = "Payment Authorized, $methodTitle amount: " . $_amount . ". Reference ID: " . "\"$_gbpReferenceNum\"";
                                $_orderId = substr($response['txn']['referenceNo'], 7);
                                $orderIncrementId = $this->gbprimePayCheckoutHelper->getIncrementIdByOrderId($_orderId);
                                if ($order->canInvoice() && !$order->hasInvoices()) {
                                    $this->gbprimePayCheckoutHelper->generateInvoice($orderIncrementId, $payment_type);
                                    $this->gbprimePayCheckoutHelper->generateTransaction($orderIncrementId, $_transaction_id, $_gbpReferenceNum);
                                    $this->gbprimePayCheckoutHelper->setOrderStateAndStatus($orderIncrementId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);

                                    $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                                    $this->checkoutSession->setLastOrderId($order->getId());
                                    $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                                    $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                                    $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                                } else {
                                    if (
                                        in_array($order->getStatus(), $this->statusAndstate)
                                        && in_array($order->getState(), $this->statusAndstate)
                                    ) {
                                        $order->setState(Order::STATE_PROCESSING)
                                            ->setStatus(Order::STATE_PROCESSING);
                                        $this->orderRepository->save($order);
                                    }
                                }
                            } else {
                                if (isset($response['txn']['status']) && $response['txn']['status'] == 'D') {
                                    if ($order->canCancel()) {
                                        $order->cancel();
                                        $order->addStatusHistoryComment('Cancel Order because order still pending and did not paid.');
                                        $this->orderRepository->save($order);
                                    }
                                }
                            }
                        }
                    } else {
                        $this->log('Data reponse after call to api');
                        $jsonData = json_encode($response, JSON_PRETTY_PRINT);
                        $formattedData = str_replace("<br />", "", $jsonData);
                        $this->log($formattedData);

                        if (isset($response['resultCode']) && $response['resultCode'] === '00') {
                            if (isset($response['txn']['status']) && $response['txn']['status'] == 'S') {
                                $payment_type = $order->getPayment()->getMethod();
                                $_transaction_id = $response['txn']['merchantDefined1'];
                                $_gbpReferenceNum = substr($response['txn']['gbpReferenceNo'], 3);
                                $_amount = $order->getBaseCurrency()->formatTxt($response['txn']['amount']);
                                $methodTitle = $order->getPayment()->getMethodInstance()->getTitle();
                                $order_note = "Payment Authorized, $methodTitle amount: " . $_amount . ". Reference ID: " . "\"$_gbpReferenceNum\"";
                                $_orderId = substr($response['txn']['referenceNo'], 7);
                                $orderIncrementId = $this->gbprimePayCheckoutHelper->getIncrementIdByOrderId($_orderId);
                                if ($order->canInvoice() && !$order->hasInvoices()) {
                                    $this->gbprimePayCheckoutHelper->generateInvoice($orderIncrementId, $payment_type);
                                    $this->gbprimePayCheckoutHelper->generateTransaction($orderIncrementId, $_transaction_id, $_gbpReferenceNum);
                                    $this->gbprimePayCheckoutHelper->setOrderStateAndStatus($orderIncrementId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);

                                    $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                                    $this->checkoutSession->setLastOrderId($order->getId());
                                    $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                                    $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                                    $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                                } else {
                                    if (
                                        in_array($order->getStatus(), $this->statusAndstate)
                                        && in_array($order->getState(), $this->statusAndstate)
                                    ) {
                                        $order->setState(Order::STATE_PROCESSING)
                                            ->setStatus(Order::STATE_PROCESSING);
                                        $this->orderRepository->save($order);
                                    }
                                }
                            } else {
                                if (isset($response['txn']['status']) && $response['txn']['status'] == 'D') {
                                    if ($order->canCancel()) {
                                        $order->cancel();
                                        $order->addStatusHistoryComment('Cancel Order because order still pending and did not paid.');
                                        $this->orderRepository->save($order);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->log('An error occurred: ' . $e->getMessage());
        }

        return $this;
    }

    public function getPaymentsByMethodsAndStatuses(array $paymentMethods, array $statuses, array $states)
    {
        $paymentCollection = $this->paymentCollectionFactory->create();
        $paymentCollection->join(
            ['order' => $paymentCollection->getTable('sales_order')],
            'main_table.parent_id = order.entity_id',
            []
        );
        $paymentCollection->join(
            ['txn' => $paymentCollection->getTable('sales_order_payment')],
            'main_table.entity_id = txn.parent_id',
            []
        );
        $paymentCollection->addFieldToFilter('main_table.method', ['in' => $paymentMethods]);
        $paymentCollection->addFieldToFilter('order.status', ['nin' => $statuses]);
        $paymentCollection->addFieldToFilter('order.state', ['nin' => $states]);

        return $paymentCollection;
    }

    public function getReferenceNoByCreatedAt($order, $orderId)
    {
        $orderDate = $order->getCreatedAt();
        $timestamp = strtotime($orderDate);
        $formattedDate = date('Y-m-d\TH:i:sP', $timestamp);
        $t = strtotime($formattedDate);
        $itemreferenceno = '' . substr($t, 4, 5) . '00' . $orderId;
        return $itemreferenceno;
    }

    public function log($data)
    {
        if ($this->configHelper->getCanDebug()) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/gbprimepay/crontab.log');
        } else {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/crontab_gbprimepay.log');
        }
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}
