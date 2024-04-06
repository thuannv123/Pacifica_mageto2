<?php

namespace Isobar\OrderAttachment\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Psr\Log\LoggerInterface;

class ApproveBankTransfer extends \Magento\Backend\App\Action
{
    private OrderRepositoryInterface $orderRepository;

    private RedirectFactory $redirectFactory;

    private InvoiceService $invoiceService;

    private Transaction $transaction;

    private LoggerInterface $logger;

    private Order\Email\Sender\InvoiceSender $invoiceSender;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param RedirectFactory $redirect
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param Transaction $transaction
     * @param LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RedirectFactory $redirect,
        InvoiceService $invoiceService,
        Order\Email\Sender\InvoiceSender $invoiceSender,
        Transaction $transaction,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->orderRepository = $orderRepository;
        $this->redirectFactory = $redirect;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceSender;
        $this->transaction = $transaction;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $orderId = $this->_request->getParam('order_id');
        $result = $this->redirectFactory->create();


        try {
            $order = $this->orderRepository->get($orderId);

            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);

            $transactionSave = $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();

            $this->invoiceSender->send($invoice);

            $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING);
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['order_id' => $orderId]);
            $this->messageManager->addErrorMessage('Cannot approve and invoice for order.');
        }

        return $result->setUrl($this->_redirect->getRefererUrl());
    }
}
