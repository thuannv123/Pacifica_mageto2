<?php

namespace Marvelic\GBPrimePay\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\ConfigInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use GBPrimePay\Payments\Helper\Constant;

class GBPrimePayDirect extends \GBPrimePay\Payments\Model\GBPrimePayDirect
{
    protected $gbprimePayPurchaseCollection;

    const XML_PATH_DEFAULT_LOCALE = 'general/locale/code';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $checkoutRegistry,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \GBPrimePay\Payments\Helper\ConfigHelper $configHelper,
        \GBPrimePay\Payments\Model\CustomerFactory $customerFactory,
        \GBPrimePay\Payments\Model\CardFactory $cardFactory,
        \GBPrimePay\Payments\Model\PurchaseFactory $purchaseFactory,
        \GBPrimePay\Payments\Logger\Logger $gbprimepayLogger,
        \Marvelic\GBPrimePay\Model\GBPrimePayPurchaseFactory $gbprimePayPurchaseCollection,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $scopeConfig,
            $moduleList,
            $localeDate,
            $messageManager,
            $checkoutRegistry,
            $backendAuthSession,
            $sessionQuote,
            $customerSession,
            $logger,
            $paymentData,
            $quoteRepository,
            $quoteManagement,
            $checkoutData,
            $checkoutSession,
            $storeManager,
            $configHelper,
            $customerFactory,
            $cardFactory,
            $purchaseFactory,
            $gbprimepayLogger
        );
        $this->gbprimePayPurchaseCollection = $gbprimePayPurchaseCollection;
    }

    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/gbprimepay/credit_request.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     */
    public function _secured($payment, $amount)
    {
        try {
            $gbprimepayCardId = $payment->getAdditionalInformation('gbprimepayCardId');
            $transaction_form = $payment->getAdditionalInformation('transaction_form');
            $tokenid = $payment->getAdditionalInformation('tokenid');
            $order = $payment->getOrder();
            $customer_full_name = $order->getBillingAddress()->getFirstname() . ' ' . $order->getBillingAddress()->getLastname();
            $callgetMerchantId = $this->_config->getMerchantId();
            $callgenerateID = $this->_config->generateID();
            $generateitem = $this->_config->getGBPTransactionITEM();

            $_orderId = $order->getEntityId();
            $_incrementId = $order->getIncrementId();
            $itemamount = number_format((($amount * 100) / 100), 2, '.', '');
            $itemdetail = 'Charge for order ' . $_incrementId;
            $itemReferenceId = '' . substr(time(), 4, 5) . '00' . $_orderId;
            $itemformkey = isset($generateitem['merchantDefined4']) ? $generateitem['merchantDefined4'] : '';
            $itemcustomerEmail = $order->getCustomerEmail();
            $itemcustomerAddress = '';
            $itemcustomerAddress .= '' . $customer_full_name . ' ';
            $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('company') . ' ';
            $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet()) > 0) ? $order->getBillingAddress()->getStreet()[0] : '' . ' ';
            $itemcustomerAddress .= '' . (count($order->getBillingAddress()->getStreet()) > 1) ? $order->getBillingAddress()->getStreet()[1] : '' . ' ';
            $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('city') . ' ';
            $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('region') . ' ';
            $itemcustomerAddress .= '' . $order->getBillingAddress()->getData('postcode') . '';
            $itemcustomerTelephone = '' . $order->getBillingAddress()->getTelephone();
            $itemmagento_customer_id = $payment->getOrder()->getCustomerId();
            $otpCode = 'Y';
            $otpResponseUrl = $this->_config->getresponseUrl('response_direct') . 'form_key/' . $transaction_form;
            $otpBackgroundUrl = $this->_config->getresponseUrl('background_direct');

            if ($this->_config->getEnvironment() === 'prelive') {
                $url = Constant::URL_CHARGE_TEST;
            } else {
                $url = Constant::URL_CHARGE_LIVE;
            }

            $field = "{\r\n\"amount\": $itemamount,\r\n\"referenceNo\": \"$itemReferenceId\",\r\n\"detail\": \"$itemdetail\",\r\n\"customerName\": \"$customer_full_name\",\r\n\"customerEmail\": \"$itemcustomerEmail\",\r\n\"customerAddress\": \"$itemcustomerAddress\",\r\n\"customerTelephone\": \"$itemcustomerTelephone\",\r\n\"merchantDefined1\": \"$callgenerateID\",\r\n\"merchantDefined2\": null,\r\n\"merchantDefined3\": \"$itemReferenceId\",\r\n\"merchantDefined4\": \"$itemformkey\",\r\n\"merchantDefined5\": null,\r\n\"card\": {\r\n\"token\": \"$gbprimepayCardId\"\r\n},\r\n\"otp\": \"$otpCode\",\r\n\"responseUrl\": \"$otpResponseUrl\",\r\n\"backgroundUrl\": \"$otpBackgroundUrl\"\r\n}\r\n";

            if ($this->_config->getCanDebug()) {
                $this->log("----------Data sent to API for CreditCard----------");
                $this->log($field);
            }

            $callback = $this->_config->sendCHARGECurl("$url", $field, 'POST');

            if ($callback == false) {
                $currentStore = $this->storeManager->getStore()->getConfig(self::XML_PATH_DEFAULT_LOCALE);
                if ($currentStore == 'en_US') {
                    return throw new CouldNotSaveException(
                        __('There is a temporary connection problem with GBPrime. Please wait a moment and try again.')
                    );
                } else if($currentStore == 'th_TH') {
                    return throw new CouldNotSaveException(
                        __('ขณะนี้มีปัญหาการเชื่อมต่อ GBPrime ชั่วคราว รอสักครู่แล้วลองใหม่อีกครั้ง')
                    );
                }
            }

            if (isset($callback['referenceNo'])) {
                $purchaseDataCollection = $this->gbprimePayPurchaseCollection->create();
                $purchaseData = [
                    'order_id' => $_orderId,
                    'referenceNo' => $callback['referenceNo'],
                ];
                $purchaseDataCollection->setData($purchaseData);
                $purchaseDataCollection->save();
            }

            if ($this->_config->getCanDebug()) {
                $this->log("----------Data reponse to API for CreditCard----------");
                $this->log(json_encode($callback));
            }

            $getgbprimepay_customer_id = $payment->getAdditionalInformation('gbprimepay_customer_id');

            $gbpReferenceNo_action = isset($callback['gbpReferenceNo']) ? $callback['gbpReferenceNo'] : '';
            if ($gbpReferenceNo_action == true) {
                $callbackgbpReferenceNo = $callback['gbpReferenceNo'];
            } else {
                $callbackgbpReferenceNo = '';
            }

            $item = array(
                "id" => $callgenerateID,
                "tokenreference" => $gbprimepayCardId,
                "resultCode" => $callback['resultCode'],
                "amount" => $itemamount,
                "referenceNo" => $itemReferenceId,
                "gbpReferenceNo" => $callbackgbpReferenceNo,
                "detail" => $itemdetail,
                "customerName" => $customer_full_name,
                "customerEmail" => $itemcustomerEmail,
                "customerAddress" => $itemcustomerAddress,
                "customerTelephone" => $itemcustomerTelephone,
                "merchantDefined1" => $callgenerateID,
                "merchantDefined2" => null,
                "merchantDefined3" => $itemReferenceId,
                "merchantDefined4" => null,
                "merchantDefined5" => null,
                "related" => array(
                    "self" => "$getgbprimepay_customer_id",
                    "buyers" => "$callgetMerchantId",
                ),
                "links" => array(
                    "self" => "/charges/$callgenerateID",
                    "buyers" => "/charges/$callgenerateID/buyers",
                    "sellers" => "/charges/$callgenerateID/sellers",
                    "status" => "/charges/$callgenerateID/status",
                    "fees" => "/charges/$callgenerateID/fees",
                    "transactions" => "/charges/$callgenerateID/transactions",
                    "batch_transactions" => "/charges/$callgenerateID/batch_transactions",
                ),
            );

            if ($item['tokenreference']) {
                if ($callback['resultCode'] === '00') {
                    return $item;
                } else {
                    throw new CouldNotSaveException(
                        __('Something went wrong. Please try again!')
                    );
                }
            } else {
                throw new CouldNotSaveException(
                    __('Something went wrong. Please try again!')
                );
            }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("_secured auth //" . $exception->getMessage());
            }

            throw new \Exception(
                $exception->getMessage()
            );
        }
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     */
    public function _capture($payment, $amount)
    {
        try {
            $gbprimepayCardId = $payment->getAdditionalInformation('gbprimepayCardId');
            $order = $payment->getOrder();

            $customer_full_name = $order->getBillingAddress()->getFirstname() . ' ' . $order->getBillingAddress()->getLastname();
            $callgetMerchantId = $this->_config->getMerchantId();
            $callgenerateID = $this->_config->generateID();

            $itemamount = number_format((($amount * 100) / 100), 2, '.', '');
            $itemdetail = 'Charge for order ' . $order->getEntityId();
            $itemReferenceId = '' . substr(time(), 4, 5) . '00' . $order->getIncrementId();
            $itemcustomerEmail = $order->getCustomerEmail();
            $itemcustomerAddress = '' . str_replace("<br/>", " ", $order->getBillingAddress());
            $itemcustomerTelephone = '' . $order->getBillingAddress()->getTelephone();
            $itemmagento_customer_id = $payment->getOrder()->getCustomerId();
            $otpCode = 'Y';
            $otpResponseUrl = $this->_config->getresponseUrl('response_direct');
            $otpBackgroundUrl = $this->_config->getresponseUrl('background_direct');

            if ($this->_config->getEnvironment() === 'prelive') {
                $url = Constant::URL_CHARGE_TEST;
            } else {
                $url = Constant::URL_CHARGE_LIVE;
            }

            $field = "{\r\n\"amount\": $itemamount,\r\n\"referenceNo\": \"$itemReferenceId\",\r\n\"detail\": \"$itemdetail\",\r\n\"customerName\": \"$customer_full_name\",\r\n\"customerEmail\": \"$itemcustomerEmail\",\r\n\"customerAddress\": \"$itemcustomerAddress\",\r\n\"customerTelephone\": \"$itemcustomerTelephone\",\r\n\"merchantDefined1\": \"$callgenerateID\",\r\n\"merchantDefined2\": null,\r\n\"merchantDefined3\": \"$itemReferenceId\",\r\n\"merchantDefined4\": null,\r\n\"merchantDefined5\": null,\r\n\"card\": {\r\n\"token\": \"$gbprimepayCardId\"\r\n},\r\n\"otp\": \"$otpCode\",\r\n\"responseUrl\": \"$otpResponseUrl\",\r\n\"backgroundUrl\": \"$otpBackgroundUrl\"\r\n}\r\n";

            if ($this->_config->getCanDebug()) {
                $this->log("----------Data sent to API for CreditCard----------");
                $this->log($field);
            }

            $callback = $this->_config->sendCHARGECurl("$url", $field, 'POST');

            if ($callback == false) {
                $currentStore = $this->storeManager->getStore()->getConfig(self::XML_PATH_DEFAULT_LOCALE);
                if ($currentStore == 'en_US') {
                    return throw new CouldNotSaveException(
                        __('There is a temporary connection problem with GBPrime. Please wait a moment and try again.')
                    );
                } else if($currentStore == 'th_TH') {
                    return throw new CouldNotSaveException(
                        __('ขณะนี้มีปัญหาการเชื่อมต่อ GBPrime ชั่วคราว รอสักครู่แล้วลองใหม่อีกครั้ง')
                    );
                }
            }

            if (isset($callback['referenceNo'])) {
                $purchaseDataCollection = $this->gbprimePayPurchaseCollection->create();
                $purchaseData = [
                    'order_id' => $order->getEntityId(),
                    'referenceNo' => $callback['referenceNo'],
                ];
                $purchaseDataCollection->setData($purchaseData);
                $purchaseDataCollection->save();
            }

            if ($this->_config->getCanDebug()) {
                $this->log("----------Data reponse to API for CreditCard----------");
                $this->log(json_encode($callback));
            }

            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("Debug _capture sendCHARGECurl callback //" . print_r($callback, true));
            }

            if ($callback['resultCode'] == "00") {
                $isLogin = $this->customerSession->isLoggedIn();
                if ($isLogin) {
                    $cardModel = $this->cardFactory->create();
                    $getcardDataSave = $payment->getAdditionalInformation('cardDataSave');
                    if ($getcardDataSave) {
                        $cardModel->setData($getcardDataSave);
                        $cardModel->save();
                    }
                }
            }
            $getgbprimepay_customer_id = $payment->getAdditionalInformation('gbprimepay_customer_id');
            $item = array(
                "id" => $callgenerateID,
                "tokenreference" => $gbprimepayCardId,
                "resultCode" => $callback['resultCode'],
                "amount" => $itemamount,
                "referenceNo" => $itemReferenceId,
                "detail" => $itemdetail,
                "customerName" => $customer_full_name,
                "customerEmail" => $itemcustomerEmail,
                "customerAddress" => $itemcustomerAddress,
                "customerTelephone" => $itemcustomerTelephone,
                "merchantDefined1" => $callgenerateID,
                "merchantDefined2" => null,
                "merchantDefined3" => $itemReferenceId,
                "merchantDefined4" => null,
                "merchantDefined5" => null,
                "related" => array(
                    "self" => "$getgbprimepay_customer_id",
                    "buyers" => "$callgetMerchantId",
                ),
                "links" => array(
                    "self" => "/charges/$callgenerateID",
                    "buyers" => "/charges/$callgenerateID/buyers",
                    "sellers" => "/charges/$callgenerateID/sellers",
                    "status" => "/charges/$callgenerateID/status",
                    "fees" => "/charges/$callgenerateID/fees",
                    "transactions" => "/charges/$callgenerateID/transactions",
                    "batch_transactions" => "/charges/$callgenerateID/batch_transactions",
                ),
            );

            if ($item['tokenreference']) {
                if ($callback['resultCode'] === '00') {
                    return $item;
                } else {
                    throw new CouldNotSaveException(
                        __('Something went wrong. Please try again!')
                    );
                }
            } else {
                throw new CouldNotSaveException(
                    __('Something went wrong. Please try again!')
                );
            }
        } catch (\Exception $exception) {
            if ($this->_config->getCanDebug()) {
                $this->gbprimepayLogger->debug("_capture auth //" . $exception->getMessage());
            }

            throw new \Exception(
                $exception->getMessage()
            );
        }
    }
}
