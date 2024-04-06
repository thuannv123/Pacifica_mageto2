<?php
namespace Isobar\LogDataPayment\Controller\LogCheckoutSuccess;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Isobar\LogDataPayment\Logger\LoggerCheckout;

/**
 * Class Index
 * @package Isobar\LogDataPayment\Controller\LogCheckoutSuccess
 */
class Index extends Action
{
    /**
     * @var LoggerCheckout
     */
    protected $_logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param LoggerCheckout $logger
     */
    public function __construct(
        Context $context,
        LoggerCheckout $logger
    )
    {
        $this->_logger = $logger;
        return parent::__construct($context);
    }

    /**
     * add log checkout success order
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $purchaseEvent = $this->getRequest()->getParam('payment');
            $createdAt = $this->getRequest()->getParam('created_at');
            $storeCode = $this->getRequest()->getParam('store_code');
            $customerName = $this->getRequest()->getParam('customer_name');
            $customerEmail = $this->getRequest()->getParam('customer_email');
            $totalOrder = $this->getRequest()->getParam('grand_total');
            $order_id = $this->getRequest()->getParam('order_id');
            $this->_logger->info('Purchase event: Payment with '.$purchaseEvent.', Order id: '.$order_id.', Total order: '.$totalOrder.', Created at: '.$createdAt.', Store code: '.$storeCode.', Customer name: '.$customerName.', Customer email: '.$customerEmail);
        } catch (\Exception $e) {
            $this->_logger->info('Error: '.$e->getMessage());
        }
    }
}
