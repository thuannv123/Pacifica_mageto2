<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Plugin\Adminhtml;

use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\UrlInterface;

class PluginBefore
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * PluginBefore constructor.
     * @param RequestInterface $request
     * @param \Magento\Framework\Registry $registry
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Registry $registry,
        UrlInterface $backendUrl
    ) {
        $this->_request = $request;
        $this->_backendUrl = $backendUrl;
        $this->_coreRegistry = $registry;
    }

    /* Add button before PushButtons method */
    public function beforePushButtons(
        $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if ($context->getRequest()->getFullActionName() == 'sales_order_view') {
            $order_id = $context->getRequest()->getParam('order_id');
            $order = $this->_coreRegistry->registry('current_order');
            if ($order->getStatus() == 'Pending_BeamCheckout') {
                $url = $context->getUrl('beamcheckout/purchase/check/order_id/' . $order_id);
                $buttonList->add(
                    'checkPurchaseIdButton',
                    ['label' => __('Check PurchaseId'), 'onclick' => 'setLocation("' . $url . '")', 'class' => 'check-purchase-id']
                );
            }
        }
    }
}
