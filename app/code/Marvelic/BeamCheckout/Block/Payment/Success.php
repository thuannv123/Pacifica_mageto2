<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Block\Payment;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderFactory;

    /**
     * Success constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Sales\Model\Order $orderFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\Order $orderFactory
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->_layout = $layout;
        $this->httpContext = $httpContext;
        $this->orderFactory = $orderFactory;
    }

    /* Get the Beam Checkout order ID from the request parameters */
    public function getBeamCheckoutOrderId()
    {
        $order_id = $this->request->getParam('id');
        return $order_id;
    }

    /* Get the Magento order ID corresponding to the Beam Checkout order ID */
    public function getOrderId()
    {
        $increid = $this->getBeamCheckoutOrderId();
        $orderInfo = $this->orderFactory->loadByIncrementId($increid);
        $orderId = $orderInfo->getId();
        return $orderId;
    }

    /* Get the continue URL for the success page */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
