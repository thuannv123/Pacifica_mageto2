<?php
/**
 * Anowave Magento 2 Onestepcheckout Add-on for GTM (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ecoam
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ecoam\Helper;

use Anowave\Package\Helper\Package;

class Data extends \Anowave\Package\Helper\Package
{
    const CHECKOUT_STEP_CART 	 = 1;
    const CHECKOUT_STEP_CHECKOUT = 2;
    const CHECKOUT_STEP_SHIPPING = 3;
    const CHECKOUT_STEP_PAYMENT  = 4;
    const CHECKOUT_STEP_REVIEW   = 5;
    const CHECKOUT_STEP_ORDER	 = 6;
    
	/**
	 * Package name
	 * 
	 * @var string
	 */
	protected $package = 'MAGE2-ECOAM';
	
	/**
	 * Config path 
	 * 
	 * @var string
	 */
	protected $config = 'ecoam/general/license';
	
	/**
	 * @var \Magento\Payment\Model\MethodList
	 */
	protected $methodList;
	
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;
	
	/**
	 * @var \Magento\Shipping\Model\Config
	 */
	protected $shippingConfig;
	
	/**
	 * @var \Magento\Quote\Api\ShippingMethodManagementInterface
	 */
	protected $shippingMethodManager;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Payment\Model\MethodList $methodList
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Shipping\Model\Config $shippingConfig
	 * @param \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager
	 */
	public function __construct
	(
	    \Magento\Framework\App\Helper\Context $context,
	    \Magento\Payment\Model\MethodList $methodList, 
	    \Magento\Checkout\Model\Session $checkoutSession,
	    \Magento\Shipping\Model\Config $shippingConfig,
	    \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager
	)
	{
	    parent::__construct($context);
	    
	    /**
	     * Set payment method list model 
	     * 
	     * @var \Magento\Payment\Model\MethodList $methodList
	     */
	    $this->methodList = $methodList;
	    
	    /**
	     * Set checkout session
	     * 
	     * @var \Magento\Checkout\Model\Session $checkoutSession
	     */
	    $this->checkoutSession = $checkoutSession;
	    
	    /**
	     * Set shipping config
	     * 
	     * @var \Magento\Shipping\Model\Config $shippingConfig
	     */
	    $this->shippingConfig = $shippingConfig; 
	    
	    /**
	     * @var \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager
	     */
	    $this->shippingMethodManager = $shippingMethodManager;
	}
	
	/**
	 * Get method list 
	 * 
	 * @return \Magento\Payment\Model\MethodInterface[]
	 */
	public function getAvailableMethods()
	{
	    return $this->methodList->getAvailableMethods
	    (
	        $this->checkoutSession->getQuote()
	    );
	}
	
	/**
	 * Get available shipping methods
	 * 
	 * @return array
	 */
	public function getAvailableShippingMethods() : array
	{
	    $methods = [];
	    
	    $quote = $this->checkoutSession->getQuote();

	    foreach ($this->shippingMethodManager->getList($quote->getId()) as $method)
	    {
	        $methods[] = $method->getMethodTitle();
	    }
	    
	    return $methods;      
	}
	
	public function getActiveShippingMethods() : array  
	{
	    $activeCarriers = $this->shippingConfig->getActiveCarriers();
	    
	    foreach($activeCarriers as $carrierCode => $carrierModel) 
	    {
	        $methods[] = $carrierCode;
	    }
	    
	    return $methods;
	}
	
	/**
	 * Check if only one payment method is available
	 * 
	 * @return bool
	 */
	public function isOnePaymentMethod() : bool
	{
	    return 1 == count($this->getAvailableMethods());
	}
	
	/**
	 * Check if only one shipping method is available
	 * 
	 * @return bool
	 */
	public function isOneShippingMethod() : bool
	{
	    return 1 == count($this->getActiveShippingMethods());
	}
}

