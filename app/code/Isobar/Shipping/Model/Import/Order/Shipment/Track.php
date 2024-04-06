<?php

namespace Isobar\Shipping\Model\Import\Order\Shipment;

use Firebear\ImportExport\Model\Import\Context;
use Firebear\ImportExport\Model\ResourceModel\Order\Helper;
use Isobar\Shipping\Model\Config;
use Magento\Framework\DB\TransactionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Shipping\Model\ShipmentNotifierFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

class Track extends \Firebear\ImportExport\Model\Import\Order\Shipment\Track
{
    protected Config $shippingConfig;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @param Context $context
     * @param Helper $resourceHelper
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param ShipmentNotifierFactory $notifierFactory
     * @param ShipmentFactory $shipmentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param TransactionFactory $transactionFactory
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param Config $shippingConfig
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Context $context,
        Helper $resourceHelper,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        ShipmentNotifierFactory $notifierFactory,
        ShipmentFactory $shipmentFactory,
        OrderRepositoryInterface $orderRepository,
        TransactionFactory $transactionFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Config $shippingConfig
    )
    {
        $this->shippingConfig = $shippingConfig;
        parent::__construct($context, $resourceHelper, $shipmentCollectionFactory, $notifierFactory, $shipmentFactory, $orderRepository, $transactionFactory ,$searchCriteriaBuilderFactory);
    }

    /**
     * @param array $rowData
     * @return array[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareDataForUpdate(array $rowData)
    {
        $toCreate = [];
        $toUpdate = [];

        list($createdAt, $updatedAt) = $this->_prepareDateTime($rowData);
        /* auto generate shipment and order ids */
        if (!empty($rowData[self::COLUMN_SHIPMENT_INCREMENT_ID])) {
            $shipmentId = $this->_getExistShipmentId($rowData);
            if (empty($this->shipmentIdsMap[$shipmentId])) {
                $this->shipmentIdsMap[$shipmentId] = $shipmentId;
            }
            $rowData[self::COLUMN_SHIPMENT_ID] = $shipmentId;

            if (empty($rowData[self::COLUMN_ORDER_ID])) {
                $orderId = $this->_getOrderIdByShipment($rowData);
                if (empty($this->orderIdsMap[$orderId])) {
                    $this->orderIdsMap[$orderId] = $orderId;
                }
                $rowData[self::COLUMN_ORDER_ID] = $orderId;
            }

            if (empty($this->shipmentIdsMap[$shipmentId])) {
                $this->shipmentIdsMap[$shipmentId] = $shipmentId;
            }
            $rowData[self::COLUMN_SHIPMENT_ID] = $shipmentId;
        }

        if (!empty($this->_currentOrderId)) {
            $itemsToShip = [];
            $itemsToInvoice = [];
            $order = null;
            /* create order */
            if ((!empty($this->_parameters['generate_shipment_by_track']) ||
                !empty($this->_parameters['generate_invoice_by_track']))
            ) {
                $order = $this->orderRepository->get(
                    $this->_getExistOrderId()
                );

                /* check if the column of the skus is empty or not */
                if (!empty($rowData['skus'])) {
                    $data = $this->getSkus($rowData['skus']);
                }
                foreach ($order->getAllVisibleItems() as $item) {

                    if (!isset($data[$item->getSku()]) && !empty($rowData['skus'])) {
                        continue;
                    }

                    if ($item->canShip()) {
                        /* if sku columns not empty, it takes the minimum qty */
                        if (!empty($rowData['skus'])) {
                            $qty = min($data[$item->getSku()], $item->getQtyToShip());
                        } else {
                            $qty = $item->getQtyToShip();
                        }
                        if (0 < $qty) {
                            $itemsToShip[$item->getId()] = $qty;
                        }
                    }

                    if ($item->canInvoice()) {
                        /* if sku columns not empty, it takes the minimum qty */
                        if (!empty($rowData['skus'])) {
                            $qty = min($data[$item->getSku()], $item->getQtyToInvoice());
                        } else {
                            $qty = $item->getQtyToInvoice();
                        }
                        if (0 < $qty) {
                            $itemsToInvoice[$item->getId()] = $qty;
                        }
                    }
                }
            }

            /* create shipment */
            if (!empty($this->_parameters['generate_shipment_by_track']) &&
                empty($this->_currentShipmentId) &&
                0 < count($itemsToShip)
            ) {

                $shipment = $this->shipmentFactory->create($order, $itemsToShip);
                if ($shipment->getTotalQty()) {
                    $shipment->register();

                    $transaction = $this->transactionFactory->create();
                    $transaction->addObject(
                        $shipment
                    )->addObject(
                        $order
                    )->save();

                    $this->addLogWriteln(
                        __('generate shipment with id %1', $shipment->getIncrementId()),
                        $this->output,
                        'info'
                    );

                    $shipmentId = $shipment->getId();
                    if (empty($this->shipmentIdsMap[$shipmentId])) {
                        $this->shipmentIdsMap[$shipmentId] = $shipmentId;
                    }
                    $rowData[self::COLUMN_SHIPMENT_ID] = $shipmentId;

                    if ($rowData['carrier_code'] == Config::CARRIER_CODE)
                    {
                        $rowData['track_url'] = $this->shippingConfig->getUrl(
                                ScopeInterface::SCOPE_STORE, $order->getStoreId()
                            ) . $rowData['track_number'];
                    }

                }
            }

            /* create invoice */
            if (!empty($this->_parameters['generate_invoice_by_track']) &&
                0 < count($itemsToInvoice)
            ) {
                $invoice = $order->prepareInvoice($itemsToInvoice);
                if ($invoice->getTotalQty()) {
                    $invoice->register();

                    $transaction = $this->transactionFactory->create();
                    $transaction->addObject(
                        $invoice
                    )->addObject(
                        $order
                    )->save();

                    $this->addLogWriteln(
                        __('generate invoice with id %1', $invoice->getIncrementId()),
                        $this->output,
                        'info'
                    );
                }
            }

            if (count($itemsToShip) > 0 && count($itemsToInvoice) > 0) {
                //change order status
                if (isset($rowData['status']) && !empty($rowData['status'])) {
                    $order->setData('status', $rowData['status']);
                    $order->setData('state', $rowData['status']);
                    $order->save();
                }
            }
        }

        if (empty($rowData[self::COLUMN_SHIPMENT_ID])) {
            return [
                self::ENTITIES_TO_CREATE_KEY => [],
                self::ENTITIES_TO_UPDATE_KEY => []
            ];
        }

        $newEntity = false;
        $entityId = $this->_getExistEntityId($rowData);
        if (!$entityId) {
            /* create new entity id */
            $newEntity = true;
            $entityId = $this->_getNextEntityId();
            $key = $rowData[self::COLUMN_ENTITY_ID] ?? $entityId;
            $this->_newEntities[$key] = $entityId;
        }

        $entityRow = [
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'entity_id' => $entityId,
            'parent_id' => $this->_getShipmentId($rowData),
            'order_id' => $this->_getExistOrderId()
        ];


        /* prepare data */
        $entityRow = $this->_prepareEntityRow($entityRow, $rowData);
        if ($newEntity) {
            $toCreate[] = $entityRow;
        } else {
            $toUpdate[] = $entityRow;
        }
        return [
            self::ENTITIES_TO_CREATE_KEY => $toCreate,
            self::ENTITIES_TO_UPDATE_KEY => $toUpdate
        ];
    }
}
