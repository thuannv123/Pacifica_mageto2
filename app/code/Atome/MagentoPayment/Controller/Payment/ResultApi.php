<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Controller\Payment;

use Magento\Framework\Controller\ResultFactory;

class ResultApi extends Result
{

    public function execute()
    {
        $orderId = $this->_request->getParam('orderId');

        if (!$orderId) {
            throw new \Exception('Invalid parameter.');
        }

        $refresh = false;
        if ($order = $this->getOrder($orderId)) {
            /*
             * Once we have any result page ready, we can refresh the page to redirect the user to the result page
             */
            $refresh = (boolean)$this->getRedirectUrl(
                $order->getState(),
                $order->getStatus()
            );
        }

        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setData([
                'refresh' => $refresh,
            ]);
    }


}
