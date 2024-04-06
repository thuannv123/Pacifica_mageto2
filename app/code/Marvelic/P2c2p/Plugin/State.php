<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Marvelic\P2c2p\Plugin;

use Magento\Sales\Model\Order;
/**
 * Checking order status and adjusting order status before saving
 */
class State
{
    /**
     * Check order status and adjust the status before save
     *
     * @param Order $order
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function beforeCheck(
        \Magento\Sales\Model\ResourceModel\Order\Handler\State $subject,
        Order $order)
    {
        $currentState = $order->getState();

        if (!$order->isCanceled() && !$order->canUnhold() && !$order->canInvoice()) {
            if (in_array($currentState, [Order::STATE_PROCESSING, Order::STATE_COMPLETE])
                && !$order->canCreditmemo()
                && !$order->canShip()
                && $order->getIsNotVirtual()
            ) {
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/debug_2c2p_closed.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info(json_encode($order));
            }
        }
        return [$order];
    }
}
