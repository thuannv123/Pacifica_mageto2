<?php

namespace Marvelic\BeamCheckoutOverride\Observer;

class OrderPlaceAfter extends \Marvelic\BeamCheckout\Observer\OrderPlaceAfter
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $incrementId = $order->getIncrementId();
            $methodCode = $order->getPayment()->getMethodInstance()->getCode();
            if ($methodCode == 'beamcheckout_creditcard' || $methodCode == 'beamcheckout_ewallet' || $methodCode == 'beamcheckout_qrcode') {
                $storeId = $order->getStoreId();
                $endpoint = $this->_beamCheckoutRequest->getBeamCheckoutEndpoint($storeId);
                $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
                //Get payment method support
                $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
                if ($paymentMethod == 'beamcheckout_creditcard') {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_CREDIT_CARD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                } else if ($paymentMethod == 'beamcheckout_ewallet') {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_EWALLET, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                } else {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_QRCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                }
                $paymentMethodSupport = explode(",", $paymentMethodsSupportConfig);

                //Get product items
                $proArray = $this->getProductItems($order);

                //Get payment expiry 
                $time = $this->getPaymentExpiry();
                if ($time > 0) {
                    $date = str_replace('+00:00', 'Z', date('c'));
                    $dateTime = new \DateTime($date);
                    $dateTime->modify("+$time minutes");
                    $paymentExpiry = $dateTime->format('c');

                    //Create basic form array.
                    $send_product = array(
                        'expiry' => $paymentExpiry,
                        'order' => [
                            'currencyCode' => "THB",
                            'merchantReferenceId' => $incrementId,
                            'netAmount' => round($order->getBaseGrandTotal(), 2),
                            'orderItems' => $proArray,
                            'totalAmount' => round($order->getBaseGrandTotal(), 2),
                            'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
                        ],
                        'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
                        'redirectUrl' => $baseUrl . 'checkout/onepage/success?id=' . $incrementId,
                        'supportedPaymentMethods' => $paymentMethodSupport,
                    );
                } else {
                    //Create basic form array.
                    $send_product = array(
                        'order' => [
                            'currencyCode' => "THB",
                            'merchantReferenceId' => $incrementId,
                            'netAmount' => round($order->getBaseGrandTotal(), 2),
                            'orderItems' => $proArray,
                            'totalAmount' => round($order->getBaseGrandTotal(), 2),
                            'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
                        ],
                        'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
                        'redirectUrl' => $baseUrl . 'checkout/onepage/success?id=' . $incrementId,
                        'supportedPaymentMethods' => $paymentMethodSupport,
                    );
                }

                $this->log('----------Beam Checkout data send to Api----------');
                $this->log(json_encode($send_product));
                $response = $this->_beamHelperCurl->sendCurlRequest($endpoint, json_encode($send_product), $storeId);
                $jsonResult = json_decode($response, true);
                $this->log('----------Data return from Beam Checkout----------');
                $this->log(json_encode($jsonResult));
                //Save data json when send to api
                $jsonData = json_decode(json_encode($send_product), true);

                $beamCollection = $this->_beamcheckoutPurchaseFactory->create();
                $beamCollection->setBeamOrderId($order->getId())
                    ->setBeamOrderIncrementId($jsonData['order']['merchantReferenceId'])
                    ->setBeamPurchaseId($jsonResult['purchaseId'])
                    ->setBeamPaymentLink($jsonResult['paymentLink'])
                    ->save();

                return $this;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }
}
