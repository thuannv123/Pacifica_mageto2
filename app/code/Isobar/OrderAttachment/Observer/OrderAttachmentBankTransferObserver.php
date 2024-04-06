<?php

namespace Isobar\OrderAttachment\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Banktransfer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderAttachmentBankTransferObserver implements ObserverInterface
{
    private OrderRepositoryInterface $orderRepository;

    private LoggerInterface $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(OrderRepositoryInterface $orderRepository, LoggerInterface $logger)
    {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderId();

        try {
            $order = $this->orderRepository->get($orderId);
            $paymentMethod = $order->getPayment()->getMethod();

            if ($order->getStatus() != Order::STATE_PENDING_PAYMENT && $order->getStatus() != 'pending') {
                return;
            }

            if ($paymentMethod == Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE) {
                $order->setState(Order::STATE_PAYMENT_REVIEW);
                $order->setStatus(Order::STATE_PAYMENT_REVIEW);
                $this->orderRepository->save($order);
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), ['order_id' => $orderId]);
        }
    }
}