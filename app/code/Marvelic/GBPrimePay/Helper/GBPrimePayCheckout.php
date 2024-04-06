<?php

namespace Marvelic\GBPrimePay\Helper;

class GBPrimePayCheckout extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $orderFactory;

    protected $gbprimepayLogger;

    protected $orderPayment;

    protected $invoiceCollectionFactory;

    protected $invoiceService;

    protected $_config;

    protected $transactionFactory;

    protected $invoiceSender;

    protected $orderRepository;

    protected $transactionBuilder;

    protected $stockIndexerProcessor;

    protected $resource;

    protected $getAssignedStockIdForWebsite;

    protected $websiteModel;

    protected $reservationBuilder;

    protected $serializer;

    protected $appendReservationsInterface;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \GBPrimePay\Payments\Logger\Logger $gbprimepayLogger,
        \Magento\Sales\Model\Order $orderPayment,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Payment\Transaction\Builder $transactionBuilder,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \GBPrimePay\Payments\Helper\ConfigHelper $configHelper,
        \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor,
        \Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite,
        \Magento\Store\Model\Website $websiteModel,
        \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface $reservationBuilder,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservationsInterface
    ) {
        $this->orderFactory = $orderFactory;
        $this->gbprimepayLogger = $gbprimepayLogger;
        $this->orderPayment = $orderPayment;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->_config = $configHelper;
        $this->invoiceSender = $invoiceSender;
        $this->orderRepository = $orderRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->stockIndexerProcessor = $stockIndexerProcessor;
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
        $this->websiteModel = $websiteModel;
        $this->reservationBuilder = $reservationBuilder;
        $this->serializer = $serializer;
        $this->appendReservationsInterface = $appendReservationsInterface;

        parent::__construct($context);
    }

    public function getIncrementIdByOrderId($entityId)
    {
        try {
            $orderModel = $this->orderFactory->create();
            $order = $orderModel->loadByAttribute('entity_id', $entityId);
            $orderId = $order->getIncrementId();
            return $orderId;
        } catch (\Exception $e) {
            $this->gbprimepayLogger->addCritical($e->getMessage());
        }
    }

    public function generateInvoice($orderId, $payment_type)
    {
        try {
            $order = $this->orderPayment->loadByIncrementId($orderId);
            $invoices = $this->invoiceCollectionFactory->create()
                ->addAttributeToFilter('order_id', array('eq' => $order->getId()));
            $invoices->getSelect()->limit(1);
            if ((int)$invoices->count() !== 0) {
                return null;
            }
            if (!$order->canInvoice()) {
                return null;
            }
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();
            if (!$invoice->getEmailSent()) {
                try {
                    $gbdevdebug = $this->_config->GBdevDebug();
                    if ($gbdevdebug == "true") {
                        $this->gbprimepayLogger->info("Before Call Send Email for Invoice #" . $invoice->getId() . " --- Call Send function");
                    }
                    $this->invoiceSender->send($invoice);
                } catch (\Exception $e) {
                    $this->gbprimepayLogger->addCritical($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->gbprimepayLogger->addCritical($e->getMessage());
        }
        return $invoice;
    }

    public function generateTransaction($orderId, $transaction_id, $_gbpReferenceNum)
    {
        try {
            $order = $this->orderPayment->loadByIncrementId($orderId);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $payment = $order->getPayment();
            $dataCode = $payment->getAdditionalInformation();
            $payment->setAdditionalInformation("transaction_id", $transaction_id);
            $payment->setAdditionalInformation("gbp_reference_no", $_gbpReferenceNum);
            $payment->setLastTransId($transaction_id);
            $payment->setIsTransactionClosed(0);
            $payment->setShouldCloseParentTransaction(0);
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('Authorized amount is %1.', $formatedPrice);
            $trans = $this->transactionBuilder;
            $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($transaction_id)
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array)$payment->getAdditionalInformation()]
                )
                ->setFailSafe(true)
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId($transaction_id);

            $payment->save();
            $order->save();
            $transaction->save();
        } catch (\Exception $e) {
            $this->gbprimepayLogger->addCritical($e->getMessage());
        }
    }

    public function setOrderStateAndStatus($orderId, $status, $order_note)
    {
        $order = $this->orderPayment->loadByIncrementId($orderId);
        $order->setCanSendNewEmailFlag(true);
        //$this->sendEmailCustomer($order);
        $order->setState($status);
        $order->setStatus($status);
        $order->addStatusToHistory($status, $order_note, true);
        $order->setIsCustomerNotified(true);
        $this->saveOrder($order);
    }
    public function setOrderStatePendingStatus($orderId, $status, $order_note)
    {
        $order = $this->orderPayment->loadByIncrementId($orderId);
        $order->setCanSendNewEmailFlag(false);
        $order->setState($status);
        $order->setStatus($status);
        $order->addStatusToHistory($status, $order_note, false);
        $order->setIsCustomerNotified(false);
        $this->saveOrder($order);
    }
    public function saveOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        try {
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->gbprimepayLogger->addCritical($e->getMessage());
        }
    }

    public function unCancelOrder($order)
    {
        if (!($order)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Order'));
        }

        if ($order->isCanceled()) {
            $productStockQty = [];
            $productSkus = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $productStockQty[$item->getProductId()] = $item->getQtyCanceled();
                foreach ($item->getChildrenItems() as $child) {

                    $productStockQty[$child->getProductId()] = $item->getQtyCanceled();
                    $productSkus[$child->getProductId()] = $child->getSku();

                    $child->setQtyCanceled(0);
                    $child->setTaxCanceled(0);
                    $child->setDiscountTaxCompensationCanceled(0);
                }
                if ($item->getChildrenItems() == null) {
                    $productStockQty[$item->getProductId()] = $item->getQtyCanceled();
                    $productSkus[$item->getProductId()] = $item->getSku();
                }
                $item->setQtyCanceled(0);
                $item->setTaxCanceled(0);
                $item->setDiscountTaxCompensationCanceled(0);
            }

            $order->setSubtotalCanceled(0);
            $order->setBaseSubtotalCanceled(0);
            $order->setTaxCanceled(0);
            $order->setBaseTaxCanceled(0);
            $order->setShippingCanceled(0);
            $order->setBaseShippingCanceled(0);
            $order->setDiscountCanceled(0);
            $order->setBaseDiscountCanceled(0);
            $order->setTotalCanceled(0);
            $order->setBaseTotalCanceled(0);
            $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT)
                ->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);

            $comment = 'UnCancel order if duplicate data response from GbprimePay';
            $order->addStatusHistoryComment($comment, false);

            /* Reverting inventory */
            $stockId = $this->getStockIdFromWebsiteId($order->getStore()->getWebsiteId());
            $reservation = [];
            foreach ($productSkus as $proId => $sku) {
                $reservation[] = $this->reservationBuilder
                    ->setSku($sku)
                    ->setQuantity(-$productStockQty[$proId])
                    ->setStockId($stockId)
                    ->setMetadata($this->serializer->serialize(
                        [
                            'event_type' => 'order_placed',
                            'object_type' => 'order',
                            'object_id' => (int)$order->getId(),
                            'object_increment_id' => (string)$order->getIncrementId()
                        ]
                    ))
                    ->build();
            }
            $this->appendReservationsInterface->execute($reservation);

            $productIds = [];
            foreach ($productStockQty as $id => $qty) {
                $productIds[] = $id;
            }
            if (!empty($productIds)) {
                $this->stockIndexerProcessor->reindexList($productIds);
            }
            $order->setInventoryProcessed(true);

            $order->save();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot un-cancel this order.'));
        }
    }

    public function getStockIdFromWebsiteId($websiteId)
    {
        $websiteCode = $this->websiteModel->load($websiteId, 'website_id')->getCode();
        if (!empty($websiteCode)) {
            return $this->getAssignedStockIdForWebsite->execute($websiteCode);
        }
    }
}
