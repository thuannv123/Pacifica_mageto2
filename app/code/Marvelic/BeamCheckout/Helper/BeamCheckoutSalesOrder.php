<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Marvelic\BeamCheckout\Model\BeamCheckoutPurchaseFactory;

class BeamCheckoutSalesOrder extends AbstractHelper
{
    /**
     * @var InvoiceService
     */
    protected $invoiceService;
    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var BeamCheckoutPurchaseFactory
     */
    protected $beamcheckoutPurchaseCollection;

    /**
     * BeamCheckoutSalesOrder constructor.
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param TransactionFactory $transactionFactory
     * @param BuilderInterface $transactionBuilder
     * @param BeamCheckoutPurchaseFactory $beamcheckoutPurchaseCollection
     */
    public function __construct(
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        TransactionFactory $transactionFactory,
        BuilderInterface $transactionBuilder,
        BeamCheckoutPurchaseFactory $beamcheckoutPurchaseCollection
    ) {
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transactionFactory = $transactionFactory;
        $this->transactionBuilder = $transactionBuilder;
        $this->beamcheckoutPurchaseCollection = $beamcheckoutPurchaseCollection;
    }

    /* Write log */
    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_sales_order.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    /* Prepare Invoice */
    public function prepareInvoice($order)
    {
        $invoice = $this->invoiceService->prepareInvoice($order, []);
        if (!$invoice) {
            return;
        }
        if (!$invoice->getTotalQty()) {
            return;
        }
        $invoice->setRequestedCaptureCase('online');
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
        $transactionSave->save();
        if (!$invoice->getEmailSent()) {
            try {
                $this->invoiceSender->send($invoice);
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
        return $this;
    }

    /* Create Transaction */
    public function createTransaction($order = null, $paymentData = array())
    {
        try {
            //get payment object from order object
            $payment = $order->getPayment();
            $payment->setLastTransId($paymentData['id']);
            $payment->setTransactionId($paymentData['id']);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
            );
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );
            $message = __('The authorized amount is %1.', $formatedPrice);
            //get the object of builder class
            $trans = $this->transactionBuilder;
            $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($paymentData['id'])
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
                )
                ->setFailSafe(true)
                //build method creates the transaction and returns the object
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
            return  $transaction->save()->getTransactionId();
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }

    /* Get Beam Checkout Order Id by Purchase Id */
    public function getOrderIdByPurchaseId($purchaseId)
    {
        $beamcheckoutPurchase = $this->beamcheckoutPurchaseCollection->create();
        $beamcheckoutPurchase->load($purchaseId, 'purchaseId');
        $orderId = $beamcheckoutPurchase->getBeamOrderId();

        return $orderId;
    }

    /* Get Beam Checkout Purchase Id by Order Id*/
    public function getPurchaseIdByOrderId($orderId)
    {
        $beamcheckoutPurchase = $this->beamcheckoutPurchaseCollection->create();
        $beamcheckoutPurchase->load($orderId, 'order_id');
        $purchaseId = $beamcheckoutPurchase->getBeamPurchaseId();

        return $purchaseId;
    }

    /* Get Beam Checkout Payment Link by Order Id */
    public function getPaymentLinkByOrderId($orderId)
    {
        $beamcheckoutPurchase = $this->beamcheckoutPurchaseCollection->create();
        $beamcheckoutPurchase->load($orderId, 'order_id');
        $paymentLink = $beamcheckoutPurchase->getBeamPaymentLink();

        return $paymentLink;
    }
}
