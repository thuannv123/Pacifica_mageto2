<?php
/**
 * GBPrimePay_Payments extension
 * @package GBPrimePay_Payments
 * @copyright Copyright (c) 2020 GBPrimePay Payments (https://gbprimepay.com/)
 */

namespace GBPrimePay\Payments\Controller\Checkout;

use Magento\Framework\App\ResponseInterface;

class AfterPlaceDirectOrder extends \GBPrimePay\Payments\Controller\Checkout
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
                $postData = $_POST;
                $referenceNo = $postData['referenceNo'];
                $_orderId = substr($postData['referenceNo'], 7);
                $_transaction_id = $postData['merchantDefined1'];
                $_gbpReferenceNo = $postData['gbpReferenceNo'];
                $_gbpReferenceNum = substr($postData['gbpReferenceNo'], 3);
                if ($this->_config->getCanDebug()) {
                    $this->gbprimepayLogger->debug("Credit Card Callback Handler //" . print_r($postData, true));
                }
                $orderId = $this->getIncrementIdByOrderId($_orderId);    
                $order = $this->getQuoteByOrderId($orderId);
                $payment = $order->getPayment();
                $transaction_form = $payment->getAdditionalInformation("transaction_form");
                $isSave = $payment->getAdditionalInformation("isSave");
                $_amount = $order->getBaseCurrency()->formatTxt($postData['amount']);
                $payment_type = "gbprimepay_direct";
                $order_note = "Payment Authorized, Pay with Credit Card amount: ".$_amount.". Reference ID: "."\"$_gbpReferenceNum\"";    
                if ($postData['resultCode'] === '00') {
                    if ($orderId) {
                        if ($isSave) {
                            $storedcard = $this->gbprimepayDirect->_storedcard($order->getPayment(), $order->getBaseGrandTotal());
                        }
                        if ($order->canInvoice() && !$order->hasInvoices()) {
                            $this->generateInvoice($orderId, $payment_type);
                            $this->generateTransaction($orderId, $_transaction_id, $_gbpReferenceNum);
                            $this->setOrderStateAndStatus($orderId, \Magento\Sales\Model\Order::STATE_PROCESSING, $order_note);
                            
                            $this->checkoutSession->setLastQuoteId($order->getQuoteId());
                            $this->checkoutSession->setLastOrderId($order->getId());
                            $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                            $this->checkoutSession->setLastOrderStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
                            $this->checkoutSession->setLastSuccessQuoteId($order->getQuoteId());
                            

                        }
                    }
                }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("AfterPlaceDirectOrder error//" . $exception->getMessage());
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