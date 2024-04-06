<?php

namespace Isobar\ImportExport\Helper;

use Firebear\ImportExport\Traits\Import\Entity as ImportTrait;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Symfony\Component\Console\Output\ConsoleOutput;
use Psr\Log\LoggerInterface;

class UpdateStatus
{
    use ImportTrait;
    const CANCELLED = 'canceled';
    const COMPLETE = 'complete';
    const HOLDED = 'holded';
    const UNHOLD = 'unhold';
    const REFUND = 'refund';

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var SalesData
     */
    private $salesData;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface
     */
    private $shipmentValidator;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var \Magento\Sales\Model\Service\CreditmemoService
     */
    private $creditmemoService;

    /**
     * @var Order\CreditmemoFactory
     */
    private $creditmemoFactory;

    /**
     * @var Order\Invoice
     */
    private $invoice;
    /**
     * UpdateStatus constructor.
     * @param InvoiceSender $invoiceSender
     * @param ShipmentSender $shipmentSender
     * @param ShipmentFactory $shipmentFactory
     * @param InvoiceService $invoiceService
     * @param SalesData $salesData
     * @param OrderRepositoryInterface $orderRepository
     * @param TransactionFactory $transactionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ShipmentValidatorInterface $shipmentValidator
     * @param LoggerInterface $logger
     * @param ConsoleOutput $output
     */
    public function __construct(
        InvoiceSender $invoiceSender,
        ShipmentSender $shipmentSender,
        ShipmentFactory $shipmentFactory,
        InvoiceService $invoiceService,
        SalesData $salesData,
        OrderRepositoryInterface $orderRepository,
        TransactionFactory $transactionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentValidatorInterface $shipmentValidator,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        LoggerInterface $logger,
        ConsoleOutput $output
    ) {
        $this->invoiceSender =          $invoiceSender;
        $this->shipmentSender =         $shipmentSender;
        $this->shipmentFactory =        $shipmentFactory;
        $this->invoiceService =         $invoiceService;
        $this->salesData =              $salesData;
        $this->transactionFactory =     $transactionFactory;
        $this->orderRepository =        $orderRepository;
        $this->shipmentSender =         $shipmentSender;
        $this->shipmentValidator =      $shipmentValidator ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ShipmentValidatorInterface::class);

        $this->searchCriteriaBuilder =  $searchCriteriaBuilder;
        $this->orderManagement = $orderManagement;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->invoice = $invoice;
        $this->_logger = $logger;
        $this->output = $output;
    }

    public function execute($orderStatus)
    {
        $status = $orderStatus['status'];
        $incrementId = $orderStatus['increment_id'];
        $trackingNumber = $orderStatus['tracking_number'];
        $order = $this->getOrder($incrementId);

        if (!$this->isNeedToBeUpdated($order, $status)) {
            return true;
        }
        switch ($status) {
            case self::COMPLETE:
                $carrier = !empty($trackingNumber) ? $this->getCarrierInfo($trackingNumber) : null;
                $this->createInvoice($order, $carrier);
                break;
            case self::CANCELLED:
                $this->cancelOrder($order);
                break;
            case self::REFUND:
                $this->refundOrder($order);
                break;
            case self::HOLDED:
                $this->holdOrder($order);
                break;
            case self::UNHOLD:
                $this->unHoldOrder($order);
                break;
            default:
                break;
        }
    }

    /**
     * @param $order Order
     * @throws LocalizedException
     */
    public function refundOrder($order)
    {
        if (!$order->hasInvoices() || $order->getBaseTotalRefunded() > 0) {
            return false;
        }
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $invoiceincrementid = $invoice->getIncrementId();
        }

        /**
         * @var $invoiceobj \Magento\Sales\Model\Order\Invoice
         */
        $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
        $creditmemo = $this->creditmemoFactory->createByOrder($order);

        // Don't set invoice if you want to do offline refund
        $creditmemo->setInvoice($invoiceobj);
        if ($creditmemo->canRefund()) {
            // apply payment online
            $this->creditmemoService->refund($creditmemo, false);
        } else {
            // apply payment offline
            if (empty($order->getPayment()->getCcLast4())) {
                $this->creditmemoService->refund($creditmemo, true);
            }
        }
    }

    /**
     * @param $order Order
     */
    public function unHoldOrder($order)
    {
        try {
            $this->orderManagement->unHold($order->getEntityId());
        } catch (\Exception $e) {
            $this->addLogWriteln(__($e->getMessage()), $this->output, 'error');
        }
    }

    /**
     * @param $order Order
     */
    public function holdOrder($order)
    {
        try {
            $this->orderManagement->hold($order->getEntityId());
        } catch (\Exception $e) {
            $this->addLogWriteln(__($e->getMessage()), $this->output, 'error');
        }
    }

    /**
     * @param $order Order
     */
    public function cancelOrder($order)
    {
        try {
            $this->orderManagement->cancel($order->getEntityId());
        } catch (\Exception $e) {
            $this->addLogWriteln(__($e->getMessage()), $this->output, 'error');
        }
    }

    private function getCarrierInfo($trackingNum)
    {
        $data[1] = [
            'carrier_code' => 'dhl',
            'title' => 'DHL',
            'number' => $trackingNum,
        ];
        return $data;
    }

    private function getOrder($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId, 'eq')->create();
        $order = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = reset($order);
        return $order;
    }

    /**
     * Check if status change
     *
     * @param Order $order
     * @param string $status
     *
     * @return bool
     */
    private function isNeedToBeUpdated($order, $status)
    {
        if ($order->getStatus() == $status) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $order Order
     * @return bool
     * @throws LocalizedException
     */
    public function createInvoice($order, $carrier)
    {
        $hasInvoice = false;
        if (!$order->canInvoice()) {
            $this->addLogWriteln(__('The order does not allow an invoice to be created.'), $this->output, 'error');
        }
        if ($order->hasInvoices()) {
            $invoices = $order->getInvoiceCollection();
            $invoiceId = 0;
            foreach ($invoices as $invoice) {
                $invoiceId = $invoice->getIncrementId();
            }
            $invoice = $this->invoice->loadByIncrementId($invoiceId);
            $hasInvoice = true;
        } else {
            $invoice = $this->invoiceService->prepareInvoice($order);
            if ($invoice->canCapture()) {
                $invoice->setRequestedCaptureCase('online');
            }
            if (!$invoice) {
                $this->addLogWriteln(__('The invoice can\'t be saved at this time. Please try again later.'), $this->output, 'error');
            }

            if (!$invoice->getTotalQty()) {
                $this->addLogWriteln(__("The invoice can't be created without products. Add products and try again."), $this->output, 'error');
            }

            $invoice->register();

            $invoice->getOrder()->setIsInProcess(true);
        }
        if ($order->hasShipments()) {
            return true;
        }
        $transactionSave = $this->transactionFactory->create()
        ->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        );

        $shipment = $this->_prepareShipment($invoice->getOrder(), $carrier);

        if ($shipment) {
            $transactionSave->addObject($shipment);
        }
        $transactionSave->save();

        // send invoice/shipment emails
        try {
            if ($this->salesData->canSendNewInvoiceEmail() && !$hasInvoice) {
                $this->invoiceSender->send($invoice);
            }
        } catch (\Exception $e) {
            $this->addLogWriteln(__($e->getMessage()), $this->output, 'error');
        }
        if ($shipment) {
            try {
                if ($this->salesData->canSendNewShipmentEmail()) {
                    $this->shipmentSender->send($shipment);
                }
            } catch (\Exception $e) {
                $this->addLogWriteln(__($e->getMessage()), $this->output, 'error');
            }
        }
        return true;
    }

    /**
     * @param $order Order
     * @param $carrier
     * @return \Magento\Sales\Model\Order\Shipment
     */
    protected function _prepareShipment($order, $carrier)
    {
        $itemArr = [];
        $orderItems = $order->getItems();
        foreach ($orderItems as $item) {
            $itemArr[$item->getId()] = (int)$item->getQtyOrdered();
        }
        $shipment = $this->shipmentFactory->create(
            $order,
            $itemArr,
            $carrier
        );
        if (!$shipment->getTotalQty()) {
            return null;
        }

        return $shipment->register();
    }
}
