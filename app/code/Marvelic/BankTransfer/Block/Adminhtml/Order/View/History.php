<?php

namespace Marvelic\BankTransfer\Block\Adminhtml\Order\View;

class History extends \Magento\Sales\Block\Adminhtml\Order\View\History
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $salesData,
            $registry,
            $adminHelper,
            $data
        );
    }

    public function getStatuses()
    {
        $state = $this->getOrder()->getState();
        $paymentMethod = $this->getOrder()->getPayment()->getMethod();
        if ($paymentMethod == 'banktransfer' && $state == 'payment_review') {
            $statuses = $this->getOrder()->getConfig()->getOrderStateStatuses($state);
            return $statuses;
        }
        $statuses = $this->getOrder()->getConfig()->getStateStatuses($state);
        return $statuses;
    }
}
