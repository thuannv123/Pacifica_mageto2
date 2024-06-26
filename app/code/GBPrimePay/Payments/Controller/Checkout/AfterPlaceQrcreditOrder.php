<?php
/**
 * GBPrimePay_Payments extension
 * @package GBPrimePay_Payments
 * @copyright Copyright (c) 2020 GBPrimePay Payments (https://gbprimepay.com/)
 */

namespace GBPrimePay\Payments\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;

class AfterPlaceQrcreditOrder extends \GBPrimePay\Payments\Controller\Checkout
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
                $raw_post = @file_get_contents( 'php://input' );
                $payload  = json_decode( $raw_post );
                $referenceNo = $payload->{'referenceNo'};
                $_orderId = substr($payload->{'referenceNo'}, 7);
                $_transaction_id = $payload->{'merchantDefined1'};
                $_gbpReferenceNo = $payload->{'gbpReferenceNo'};
                $_gbpReferenceNum = substr($payload->{'gbpReferenceNo'}, 3);
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->debug("QR Visa Callback Handler //" . print_r($payload, true));
                }
                $orderId = $this->getIncrementIdByOrderId($_orderId);    
                $order = $this->getQuoteByOrderId($orderId);
                $_amount = $order->getBaseCurrency()->formatTxt($payload->{'amount'});
                $payment_type = "gbprimepay_qrcredit";
                $order_note = "Payment Authorized, Pay with QR Visa amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($payload->{'resultCode'} === '00') {
                    if ($orderId) {
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            $this->checkoutSession->clearQuote();
                        }
                    }
                }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("AfterPlaceQrcreditOrder error//" . $exception->getMessage());
            }
            $this->cancelOrder();
            $this->checkoutSession->restoreQuote();

            return $this->jsonFactory->create()->setData([
                'success' => false,
                'error' => true,
                'message' => $exception->getMessage()
            ]);
        }
    }
}