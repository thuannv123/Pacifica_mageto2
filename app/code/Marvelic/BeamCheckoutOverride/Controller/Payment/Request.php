<?php

namespace Marvelic\BeamCheckoutOverride\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Request extends \Marvelic\BeamCheckout\Controller\Payment\Request
{

    protected $configSettings;
    protected $cart;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $productRepository;

    public function __construct(
        Context $context,
		OrderFactory $orderFactory,
		Session $checkoutSession,
		Customer $customer,
		BeamCheckoutRequest $beamCheckoutRequest,
		ScopeConfigInterface $configSettings,
		StoreManagerInterface $storeManager,
		\Magento\Checkout\Model\Cart $cart,
		OrderRepositoryInterface $orderRepository,
		SearchCriteriaBuilder $searchCriteriaBuilder,
		ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $context,
            $orderFactory,
            $checkoutSession,
            $customer,
            $beamCheckoutRequest,
            $configSettings,
            $storeManager,
            $cart,
            $orderRepository,
            $searchCriteriaBuilder,
            $productRepository
        );
    }

    public function execute()
    {
        //Get current order detail from OrderFactory object.
        $orderId = $this->getCheckoutSession()->getLastRealOrderId();

        if (empty($orderId)) {
            die("Aunthentication Error: Order is is empty.");
        }

        $order = $this->getOrderByIncrementId($orderId);

        //Redirect to home page with error
        if (!isset($order)) {
            $this->_redirect('');
            return;
        }
        $customerSession = $this->getCustomerSession();
        //Check whether customer is logged in or not into current merchant website.
        if ($customerSession->isLoggedIn()) {
            $cust_email = $customerSession->getCustomer()->getEmail();
        } else {
            $billingAddress = $order->getBillingAddress();
            $cust_email = $billingAddress->getEmail();
        }
        //Get baseurl 
        $baseurl = $this->storeManager->getStore()->getBaseUrl();

        //Get payment method support
		$paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
		if ($paymentMethod == 'beamcheckout_creditcard') {
			$paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_CREDIT_CARD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStoreId());
		} else if ($paymentMethod == 'beamcheckout_ewallet') {
			$paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_EWALLET, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStoreId());
		} else {
			$paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_QRCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStoreId());
		}
        $paymentMethodSupport = explode(",", $paymentMethodsSupportConfig);

        //Get product items
        $productItems = $this->getProductItems($order);

        //Get payment expiry 
		$time = $this->getPaymentExpiry();
		if ($time > 0) {
			$date = str_replace('+00:00', 'Z', date('c'));
			$dateTime = new \DateTime($date);
			$dateTime->modify("+$time minutes");
			$paymentExpiry = $dateTime->format('c');

			//Create basic form array.
			$dataSent = array(
				'expiry' => $paymentExpiry,
				'order' => [
					'currencyCode' => "THB",
					'merchantReferenceId' => $this->getCheckoutSession()->getLastRealOrderId(),
					'netAmount' => round($order->getBaseGrandTotal(), 2),
					'orderItems' => $productItems,
					'totalAmount' => round($order->getBaseGrandTotal(), 2),
					'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
				],
				'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
				'redirectUrl' => $baseurl . 'checkout/onepage/success?id=' . $orderId,
				'supportedPaymentMethods' => $paymentMethodSupport,
				'customer_email' => $cust_email
			);
		} else {
			//Create basic form array.
			$dataSent = array(
				'order' => [
					'currencyCode' => "THB",
					'merchantReferenceId' => $this->getCheckoutSession()->getLastRealOrderId(),
					'netAmount' => round($order->getBaseGrandTotal(), 2),
					'orderItems' => $productItems,
					'totalAmount' => round($order->getBaseGrandTotal(), 2),
					'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
				],
				'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
				'redirectUrl' => $baseurl . 'checkout/onepage/success?id=' . $orderId,
				'supportedPaymentMethods' => $paymentMethodSupport,
				'customer_email' => $cust_email
			);
		}
        
        $this->log('----------Data send to Beam Checkout Api----------');
        $this->log(json_encode($dataSent));
		echo $this->getBeamCheckoutRequest(json_encode($dataSent), $customerSession->isLoggedIn(), $order);
    }
}
