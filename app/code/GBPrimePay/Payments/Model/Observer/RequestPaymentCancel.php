<?php
/**
 * GBPrimePay_Payments extension
 * @package GBPrimePay_Payments
 * @copyright Copyright (c) 2020 GBPrimePay Payments (https://gbprimepay.com/)
 */

namespace GBPrimePay\Payments\Model\Observer;


use Magento\Framework\Event\ObserverInterface;
use GBPrimePay\Payments\Helper\Constant;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Model\Rule\CustomerFactory;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\ResourceModel\Coupon\Usage;


class RequestPaymentCancel implements ObserverInterface
{

protected $_config;
protected $orderFactory;
protected $checkoutSession;
protected $checkoutRegistry;
protected $customerFactory;
protected $customerSession;
protected $orderPayment;
protected $couponModel;
protected $rule;
protected $gbprimepayLogger;

public function __construct(
    \Magento\Framework\Model\Context $context,
    \Magento\Payment\Helper\Data $paymentData,
    \Magento\Payment\Model\Method\Logger $logger,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Backend\Model\Auth\Session $backendAuthSession,
    \Magento\Backend\Model\Session\Quote $sessionQuote,
    \Magento\Checkout\Model\Session $checkoutSession,
    \Magento\Framework\Registry $checkoutRegistry,
    \Magento\Sales\Model\OrderFactory $orderFactory,
    \Magento\Sales\Model\Order $orderPayment,
    \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
    \Magento\Quote\Api\CartManagementInterface $quoteManagement,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Checkout\Helper\Data $checkoutData,
    \Magento\SalesRule\Model\RuleFactory $ruleFactory,
    \Magento\SalesRule\Model\Rule\CustomerFactory $ruleCustomerFactory,
    \Magento\SalesRule\Model\Coupon $coupon,
    \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage,
    \GBPrimePay\Payments\Helper\ConfigHelper $configHelper,
    \GBPrimePay\Payments\Model\CustomerFactory $customerFactory,
    \GBPrimePay\Payments\Logger\Logger $gbprimepayLogger,
    $data = []
) {

    $this->gbprimepayLogger = $gbprimepayLogger;
    $this->_config = $configHelper;
    $this->customerFactory = $customerFactory;
    $this->customerSession = $customerSession;
    $this->backendAuthSession = $backendAuthSession;
    $this->orderFactory = $orderFactory;
    $this->sessionQuote = $sessionQuote;
    $this->checkoutSession = $checkoutSession;
    $this->_ruleFactory = $ruleFactory;
    $this->_ruleCustomerFactory = $ruleCustomerFactory;
    $this->_coupon = $coupon;
    $this->_couponUsage = $couponUsage;
    $this->checkoutRegistry = $checkoutRegistry;
    $this->orderPayment = $orderPayment;
    $this->quoteRepository = $quoteRepository;
    $this->quoteManagement = $quoteManagement;
    $this->checkoutData = $checkoutData;

}
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
    $_orderId = $this->checkoutSession->getLastRealOrder()->getId();
    $_last_order_status = $this->checkoutSession->getLastOrderStatus();

    $orderId = $this->getIncrementIdByOrderId($_orderId);    
    $order = $this->getQuoteByOrderId($orderId);
    $payment = $order->getPayment();      
    $code = $order->getCouponCode();    
    $_customerid = $order->getCustomerId();    
    $_discountamount = $order->getDiscountAmount();  
    $orderId = $this->getIncrementIdByOrderId($_orderId);    
    $order = $this->getQuoteByOrderId($orderId);
    $payment = $order->getPayment();
    
    if ($_last_order_status = "canceled") {
        if ($code = $order->getCouponCode()) {
    
          if (!$order || $order->getDiscountAmount() == 0) {
            return $this;
          }
          $ruleIds = explode(',', $order->getAppliedRuleIds());
          $ruleIds = array_unique($ruleIds);

          $ruleCustomer = null;
          $customerId = $order->getCustomerId();
          foreach ($ruleIds as $ruleId) {
              if (!$ruleId) {
                  continue;
              }
              $rule = $this->_ruleFactory->create();
              $rule->load($ruleId);
              if ($rule->getId()) {
                  $rule->loadCouponCode();
                  $rule->setTimesUsed($rule->getTimesUsed() - 1);
                  $rule->save();

                  if ($customerId) {
                      $ruleCustomer = $this->_ruleCustomerFactory->create();
                      $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
                      if ($ruleCustomer->getId()) {
                          $ruleCustomer->setTimesUsed($ruleCustomer->getTimesUsed() - 1);
                      } else {
                          $ruleCustomer->setCustomerId($customerId)->setRuleId($ruleId)->setTimesUsed(0);
                      }
                      $ruleCustomer->save();
                  }
              }
          }

          $this->_coupon->load($order->getCouponCode(), 'code');
          if ($this->_coupon->getId()) {
              $this->_coupon->setTimesUsed($this->_coupon->getTimesUsed() - 1);
              $this->_coupon->save();
              if ($customerId) {
                  $this->_couponUsage->updateCustomerCouponTimesUsed($customerId, $this->_coupon->getId(),false);
              }
          }

        }
    }
    return $this;
	}
  function getIncrementIdByOrderId($entityId)
  {
      try {
          $orderModel = $this->orderFactory->create();
          $order = $orderModel->loadByAttribute('entity_id',$entityId);
          $orderId = $order->getIncrementId();
          return $orderId;
      } catch (\Exception $e) {
          $this->gbprimepayLogger->addCritical($e->getMessage());
      }
  }
  function getQuoteByOrderId($orderId) {        
      return $this->orderPayment->loadByIncrementId($orderId);
  }
}

