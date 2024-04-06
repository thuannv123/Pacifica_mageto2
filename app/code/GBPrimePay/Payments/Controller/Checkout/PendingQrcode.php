<?php
/**
 * GBPrimePay_Payments extension
 * @package GBPrimePay_Payments
 * @copyright Copyright (c) 2020 GBPrimePay Payments (https://gbprimepay.com/)
 */

namespace GBPrimePay\Payments\Controller\Checkout;


use Magento\Framework\Registry;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Api\Data\OrderInterface;
use GBPrimePay\Payments\Helper\Constant;

class PendingQrcode extends \GBPrimePay\Payments\Controller\Checkout
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
          $transactionId = $this->getRequest()->getParam('key');
          $_orderId = $this->getRequest()->getParam('id');
          $orderId = $this->getIncrementIdByOrderId($_orderId);
          $order = $this->getQuoteByOrderId($orderId);
          $payment = $order->getPayment();
          $_getEntityId = $order->getEntityId();
          $_getIncrementId = $order->getIncrementId();
          $_getOrderByIncrementId = $this->getOrderIdByIncrementId($_getIncrementId);
          $_getOrderByEntityId = $this->getIncrementIdByOrderId($_getEntityId);
          if (($_orderId == $_getEntityId ) && ($_getIncrementId == $_getOrderByEntityId )) {
                
                $transaction_getid = $order->getId();
                $_transaction_id = $this->transactiondigit($transaction_getid);
                //$_transaction_id_old = $this->_config->getGBPTransactionID();
                //$_transaction_key_old = $this->_config->getGBPTransactionKEY();
                //$generateitem = $this->_config->getGBPTransactionITEM();
                $_transaction_key = $this->_config->generateID();
                $transaction_form = $payment->getAdditionalInformation("transaction_form");
                $ordercompletestatus = $this->getOrderCompleteStatus($_getOrderByEntityId);

                if($ordercompletestatus != 0){
                    $this->checkoutRegistry->register('order_generate_qrcode', 0, false);
                    $this->checkoutRegistry->register('order_complete_qrcode', $ordercompletestatus, false);
                    $this->checkoutRegistry->register('order_id_qrcode', $orderId, false);       
                    $this->checkoutRegistry->register('key_id_qrcode', $transaction_form, false);
                }else{
                    if ($this->_config->getEnvironment() === 'prelive') {
                        $url = Constant::URL_QRCODE_TEST;
                        $itemtoken = $this->_config->getTestTokenKey();
                    } else {
                        $url = Constant::URL_QRCODE_LIVE;
                        $itemtoken = $this->_config->getLiveTokenKey();
                    }
                    $customer_full_name = $order->getBillingAddress()->getData('firstname') . ' ' . $order->getBillingAddress()->getData('lastname');
                    $itemquoteno = $_transaction_id;
                    $itemcustomerAddress = '';
                    $itemcustomerAddress .= '' . $customer_full_name .' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('company') .' ';
                    $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet())>0) ? $order->getBillingAddress()->getStreet()[0] : '' .' ';
                    $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet())>1) ? $order->getBillingAddress()->getStreet()[1] : '' .' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('city') .' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('region') .' ';
                    $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('postcode') .'';
                    $itemcustomerTelephone = '' . $order->getBillingAddress()->getTelephone();
                    $callgetMerchantId = $this->_config->getMerchantId();
                    $_transaction_key = $this->_config->generateID();

                    $itemdetail = 'Charge for order ' . $_getIncrementId;
                    $itemreferenceno = ''.substr(time(), 4, 5).'00'.$_orderId;
                    $itemresponseurl = $this->_config->getresponseUrl('response_qrcode');
                    $itembackgroundurl = $this->_config->getresponseUrl('background_qrcode');
                    $amount = $order->getBaseGrandTotal();
                    $itemamount = number_format((($amount * 100)/100), 2, '.', '');
                    $itemcustomerEmail = $order->getCustomerEmail();
                    $itemcustomerAddress = $itemcustomerAddress;
                    $itemcustomerTelephone = $itemcustomerTelephone;
                    $itemmerchantDefined1 = $this->_config->generateID();
                    $itemmerchantDefined2 = $order->getCustomerId();
                    $itemmerchantDefined3 = $itemquoteno;
                    $itemmerchantDefined4 = $itemreferenceno;
                    $itemmerchantDefined5 = $_getIncrementId;
                    $field = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n$itemtoken\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$itemamount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"referenceNo\"\r\n\r\n$itemreferenceno\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"backgroundUrl\"\r\n\r\n$itembackgroundurl\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"detail\"\r\n\r\n$itemdetail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerName\"\r\n\r\n$customer_full_name\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerEmail\"\r\n\r\n$itemcustomerEmail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerAddress\"\r\n\r\n$itemcustomerAddress\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"customerTelephone\"\r\n\r\n$itemcustomerTelephone\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data;  name=\"merchantDefined1\"\r\n\r\n$itemmerchantDefined1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined2\"\r\n\r\n$itemmerchantDefined2\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined3\"\r\n\r\n$itemmerchantDefined3\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined4\"\r\n\r\n$itemmerchantDefined4\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"merchantDefined5\"\r\n\r\n$itemmerchantDefined5\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--";
                    
                    if($amount){
                        $callback = $this->_config->sendQRCurl("$url", $field, 'POST');
                            if ($callback=="Incomplete information") {
                            }else{
                                $this->checkoutRegistry->register('order_generate_qrcode', $callback, false);
                                $this->checkoutRegistry->register('order_complete_qrcode', 0, false);
                                $this->checkoutRegistry->register('order_id_qrcode', $orderId, false);          
                                $this->checkoutRegistry->register('key_id_qrcode', $transaction_form, false);
                                $this->sendEmailCustomer($order);
                            }
                    }else {
                        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                    }
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
        $result = $this->PageFactory->create();
        return $result;
      } catch (\Exception $exception) {
          if ($this->_config->getCanDebug()) {
              $this->gbprimepayLogger->debug("PendingQrcode error //" . $exception->getMessage());
          }
          $this->cancelOrder();
          $this->checkoutSession->restoreQuote();
      }
    }
    function transactiondigit($string) {
        $strInt = intval($string);
        $strLen = 9;
    	  $strPad = str_pad(($strInt), $strLen, "0", STR_PAD_LEFT);
        return $strPad;
    }
}