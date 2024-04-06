<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller;

use Marvelic\BeamCheckout\Controller\AbstractAction;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;

abstract class AbstractCheckoutAction extends AbstractAction
{
    const ROUTE_PATTERN_CHECKOUT_CART_PATH = 'checkout/cart';
    const ROUTE_PATTERN_CHECKOUT_CART_ARGS = [];

    protected $_checkoutSession;
    protected $_orderFactory;

    public function __construct(Context $context, Session $checkoutSession, OrderFactory $orderFactory)
    {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
    }

    /* Get Magento checkout session. */
    protected function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /* Get Magento OrderFactory object. */
    protected function getOrderFactory()
    {
        return $this->_orderFactory;
    }

    /* Get Magento Order object. */
    protected function getOrderDetailByOrderId($orderId)
    {

        $order = $this->getOrderFactory()->create()->loadByIncrementId($orderId);

        if (!$order->getId()) {
            return null;
        }

        return $order;
    }
}
