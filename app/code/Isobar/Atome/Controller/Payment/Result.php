<?php

namespace Isobar\Atome\Controller\Payment;

use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Sales\Model\Order;

class Result extends \Atome\MagentoPayment\Controller\Payment\Result
{

    /**
     * @param $orderState
     * @param $orderStatus
     * @return string|null
     */
    protected function getRedirectUrl($orderState, $orderStatus)
    {
        $redirect = null;
        if ($orderStatus === $this->paymentGatewayConfig->getOrderStatus()) {
            /*
             * If the status has been changed to the status set by the merchant,
             * it means the payment is successful and the callback has been completed,
             * and the success page can be displayed.
             *
             * This also prevents merchants from using unconventional order processes,
             * such as setting the state to remain as PENDING_PAYMENT after payment is complete.
             */
            $this->messageManager->addSuccessMessage(__("Atome Payment Completed"));
            return 'checkout/onepage/success?type=atome';
        }

        /*
         * Otherwise we use state to determine what result should be displayed
         */
        switch ($orderState) {
            case Order::STATE_NEW:
            case Order::STATE_PENDING_PAYMENT:
            case Order::STATE_HOLDED:
            case Order::STATE_PAYMENT_REVIEW:
                break;
            case Order::STATE_PROCESSING:
            case Order::STATE_COMPLETE:
                $this->messageManager->addSuccessMessage(__("Atome Payment Completed"));
                $redirect = 'checkout/onepage/success?type=atome';
                break;
            case Order::STATE_CLOSED:
            case Order::STATE_CANCELED:
                $this->messageManager->addErrorMessage(__('Atome payment failed. Please try again or use an alternative payment method.'));
                $redirect = 'checkout/onepage/failure';
                break;
            default:
                Logger::instance()->error("Unknown Magento order state: {$orderState}");
                break;
        }

        return $redirect;
    }

}
