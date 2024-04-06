<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Request extends \Marvelic\BeamCheckout\Controller\AbstractCheckoutRedirectAction
{
	/**
	 * Payment expiry
	 *
	 * @var string
	 */
	const BEAM_CHECKOUT_PAYMENT_EXPIRY = 'payment/beamcheckout/account_setting/expiry_setting/paymentExpiry';

	/**
	 * Payment credit card
	 *
	 * @var string
	 */
	const BEAMCHECKOUT_CREDIT_CARD = 'payment/beamcheckout/beamcheckout_creditcard/supported_payment_methods';

	/**
	 * Payment ewallet
	 *
	 * @var string
	 */
	const BEAMCHECKOUT_EWALLET = 'payment/beamcheckout/beamcheckout_ewallet/supported_payment_methods';

	/**
	 * Payment qrcode
	 *
	 * @var string
	 */
	const BEAMCHECKOUT_QRCODE = 'payment/beamcheckout/beamcheckout_qrcode/supported_payment_methods';

	/**
	 * @var ScopeConfigInterface
	 */
	protected $configSettings;

	/**
	 * @var \Magento\Checkout\Model\Cart
	 */
	protected $cart;

	/**
	 * @var OrderRepositoryInterface
	 */
	protected $orderRepository;

	/**
	 * @var SearchCriteriaBuilder
	 */
	protected $searchCriteriaBuilder;

	/**
	 * @var ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
	 * Request constructor.
	 * @param Context $context
	 * @param OrderFactory $orderFactory
	 * @param Session $checkoutSession
	 * @param Customer $customer
	 * @param BeamCheckoutRequest $beamCheckoutRequest
	 * @param ScopeConfigInterface $configSettings
	 * @param StoreManagerInterface $storeManager
	 * @param \Magento\Checkout\Model\Cart $cart
	 * @param OrderRepositoryInterface $orderRepository
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param ProductRepositoryInterface $productRepository
	 */
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
			$checkoutSession,
			$orderFactory,
			$customer,
			$beamCheckoutRequest,
			$configSettings,
			$storeManager
		);
		$this->configSettings = $configSettings;
		$this->cart = $cart;
		$this->orderRepository = $orderRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->productRepository = $productRepository;
	}

	/* Write log */
	public function log($data)
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_request.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info($data);
	}

	/* Execute Beam Checkout request */
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
				'redirectUrl' => $baseurl . 'beamcheckout/payment/success?id=' . $orderId,
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
				'redirectUrl' => $baseurl . 'beamcheckout/payment/success?id=' . $orderId,
				'supportedPaymentMethods' => $paymentMethodSupport,
				'customer_email' => $cust_email
			);
		}

		$this->log('----------Data send to Beam Checkout Api----------');
		$this->log(json_encode($dataSent));
		echo $this->getBeamCheckoutRequest(json_encode($dataSent), $customerSession->isLoggedIn(), $order);
	}

	/* Get Product Items */
	public function getProductItems($order)
	{
		$items = $order->getAllVisibleItems();

		$array = [];
		foreach ($items as $item) {
			$productId = $item->getProductId();
			$product = $this->productRepository->getById($productId);
			$finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

			$proArray = [
				'product' => [
					'description' => $item->getDescription(),
					'sku' => $item->getSku(),
					'price' => $finalPrice,
					'name' => $item->getName()
				],
				'quantity' => (int)$item->getQtyOrdered(),
			];
			$array[] = $proArray;
		}

		//order shipping
		$shippingAmount = $order->getShippingInclTax();
		$shippingMethod = $order->getShippingMethod();
		if ($shippingMethod && $shippingAmount != 0) {
			$proArray = [
				'product' => [
					'price' => round($shippingAmount, 2),
					'name' => $order->getShippingDescription()
				],
				'quantity' => 1,
			];
			$array[] = $proArray;
		}

		//order discount
		$discountDescription = $order->getDiscountDescription();
		$discountAmount = $order->getDiscountAmount();
		if ($discountAmount != 0) {
			if ($discountDescription == null) {
				$proArray = [
					'product' => [
						'price' => round($discountAmount, 2),
						'name' => 'Discount'
					],
					'quantity' => 1,
				];
			} else {
				$proArray = [
					'product' => [
						'price' => round($discountAmount, 2),
						'name' => "Discount (" . $order->getDiscountDescription() . ")"
					],
					'quantity' => 1,
				];
			}

			$array[] = $proArray;
		}
		return $array;
	}

	/* Get Order By Increment Id */
	public function getOrderByIncrementId($orderId)
	{
		$searchCriteria = $this->searchCriteriaBuilder->addFilter(
			OrderInterface::INCREMENT_ID,
			$orderId
		)->create();

		$result = $this->orderRepository->getList($searchCriteria);

		if (empty($result->getItems())) {
			throw new NoSuchEntityException(__('No such order.'));
		}

		$orders = $result->getItems();

		return reset($orders);
	}

	/* Get Payment Expiry */
	public function getPaymentExpiry()
	{
		$expiryConfig = explode(',', $this->configSettings->getValue(self::BEAM_CHECKOUT_PAYMENT_EXPIRY, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE));
		$hourstomins = (int)$expiryConfig[0] * 60;
		$minutes = (int)$expiryConfig[1];
		$time = $hourstomins + $minutes;
		return $time;
	}
}
