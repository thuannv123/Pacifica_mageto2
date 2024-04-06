<?php

namespace Isobar\OrderAttachment\Plugin\Block\Adminhtml\Order;

use Magento\Backend\Model\UrlInterface;
use Magento\OfflinePayments\Model\Banktransfer;
use Magento\Sales\Model\Order;

class View
{
    private UrlInterface $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $view
     * @param ...$args
     * @return array[]|void
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view, ...$args)
    {
        $order = $view->getOrder();
        if ($order->getPayment()->getMethod() != Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE || $order->getStatus() != Order::STATE_PAYMENT_REVIEW) {
            return [...$args];
        }

        $message = 'Are you sure you want to do this?';
        $url = $this->url->getUrl(
            'order_attachment/order/approvebanktransfer',
            ['_query' => ['order_id' => $view->getOrderId()]]
        );

        $view->addButton(
            'order_approvebanktransfer',
            [
                'label' => __('Approve Bank Transfer'),
                'class' => 'approve-bank-transfer',
                'onclick' => "confirmSetLocation('{$message}', '{$url}')"
            ]
        );

        return [...$args];
    }
}