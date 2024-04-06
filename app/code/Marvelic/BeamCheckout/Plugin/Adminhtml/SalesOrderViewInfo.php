<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Plugin\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Marvelic\BeamCheckout\Helper\BeamCheckoutSalesOrder;

class SalesOrderViewInfo extends Template
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var BeamCheckoutSalesOrder
     */
    protected $beamCheckoutSalesOrder;

    /**
     * SalesOrderViewInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param RequestInterface $request
     * @param LayoutInterface $layout
     * @param \Magento\Framework\Registry $registry
     * @param BeamCheckoutSalesOrder $beamCheckoutSalesOrder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        RequestInterface $request,
        LayoutInterface $layout,
        \Magento\Framework\Registry $registry,
        BeamCheckoutSalesOrder $beamCheckoutSalesOrder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->layout = $layout;
        $this->registry = $registry;
        $this->beamCheckoutSalesOrder = $beamCheckoutSalesOrder;
    }

    /* Append payment link block to order view info */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result
    ) {
        $order = $subject->getOrder();
        $methodCode = $order->getPayment()->getMethodInstance()->getCode();
        if ($methodCode == 'beamcheckout_creditcard' || $methodCode == 'beamcheckout_ewallet' || $methodCode == 'beamcheckout_qrcode') {
            $paymentlink = $this->beamCheckoutSalesOrder->getPaymentLinkByOrderId($order->getId());
            if ($paymentlink) {
                if ($subject->getLayout()->getBlock('sales_order_edit')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_invoice_view')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_invoice')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_invoice_create')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_invoice_create')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_creditmemo_view')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_creditmemo')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_creditmemo_create')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_create_creditmemo')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_shipment_create')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_shipment_new')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
                if ($subject->getLayout()->getBlock('sales_shipment_view')) {
                    $block = $subject->getLayout()->getBlock('payment_link_block_shipment')->setData('payment_link', $paymentlink);
                    if ($block !== false && $subject->getNameInLayout() == 'order_info') {
                        $result = $result . $block->toHtml();
                    }
                }
            }
        }
        return $result;
    }
}
