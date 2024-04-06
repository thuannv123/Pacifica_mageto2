<?php

namespace Marvelic\BankTransfer\Controller\Adminhtml\Order;

use Magento\Sales\Model\Order;

class AddComment extends \Magento\Sales\Controller\Adminhtml\Order\AddComment
{
    protected $orderFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Psr\Log\LoggerInterface $logger,
        Order $orderFactory
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );

        $this->orderFactory = $orderFactory;
    }

    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The comment is missing. Enter and try again.')
                    );
                }

                $order->setStatus($data['status']);
                $notify = $data['is_customer_notified'] ?? false;
                $visible = $data['is_visible_on_front'] ?? false;

                if ($notify && !$this->_authorization->isAllowed(self::ADMIN_SALES_EMAIL_RESOURCE)) {
                    $notify = false;
                }

                $comment = trim(strip_tags($data['comment']));
                $history = $order->addStatusHistoryComment($comment, $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->save();

                $paymentMethod = $order->getPayment()->getMethod();

                if ($paymentMethod == 'banktransfer') {
                    $state = $data['status'];
                    if ($state == 'canceled') {
                        foreach ($order->getAllItems() as $item) {
                            if ($data['status'] != Order::STATE_PROCESSING && $item->getQtyToRefund()) {
                                if ($item->isProcessingAvailable()) {
                                    $state = Order::STATE_PROCESSING;
                                } else {
                                    $state = Order::STATE_COMPLETE;
                                }
                            }
                            $item->cancel();
                        }

                        $this->orderFactory->setSubtotalCanceled($this->orderFactory->getSubtotal() - $this->orderFactory->getSubtotalInvoiced());
                        $this->orderFactory->setBaseSubtotalCanceled($this->orderFactory->getBaseSubtotal() - $this->orderFactory->getBaseSubtotalInvoiced());

                        $this->orderFactory->setTaxCanceled($this->orderFactory->getTaxAmount() - $this->orderFactory->getTaxInvoiced());
                        $this->orderFactory->setBaseTaxCanceled($this->orderFactory->getBaseTaxAmount() - $this->orderFactory->getBaseTaxInvoiced());

                        $this->orderFactory->setShippingCanceled($this->orderFactory->getShippingAmount() - $this->orderFactory->getShippingInvoiced());
                        $this->orderFactory->setBaseShippingCanceled($this->orderFactory->getBaseShippingAmount() - $this->orderFactory->getBaseShippingInvoiced());

                        $this->orderFactory->setDiscountCanceled(abs((float) $this->orderFactory->getDiscountAmount()) - $this->orderFactory->getDiscountInvoiced());
                        $this->orderFactory->setBaseDiscountCanceled(
                            abs((float) $this->orderFactory->getBaseDiscountAmount()) - $this->orderFactory->getBaseDiscountInvoiced()
                        );

                        $this->orderFactory->setTotalCanceled($this->orderFactory->getGrandTotal() - $this->orderFactory->getTotalPaid());
                        $this->orderFactory->setBaseTotalCanceled($this->orderFactory->getBaseGrandTotal() - $this->orderFactory->getBaseTotalPaid());

                        $this->orderFactory->setState($state)
                            ->setStatus($state);
                        if (!empty($comment)) {
                            $this->orderFactory->addStatusHistoryComment($comment, false);
                        }

                        $order->setState($state)->setStatus($state);
                        $order->save();

                        $orderCommentSender = $this->_objectManager
                            ->create(\Magento\Sales\Model\Order\Email\Sender\OrderCommentSender::class);

                        $orderCommentSender->send($order, $notify, $comment);
                        
                        return $this->resultPageFactory->create();
                    }
                }

                $order->save();
                /** @var OrderCommentSender $orderCommentSender */
                $orderCommentSender = $this->_objectManager
                    ->create(\Magento\Sales\Model\Order\Email\Sender\OrderCommentSender::class);

                $orderCommentSender->send($order, $notify, $comment);

                return $this->resultPageFactory->create();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add order history.')];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
