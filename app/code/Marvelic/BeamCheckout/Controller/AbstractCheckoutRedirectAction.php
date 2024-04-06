<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as Customer;
use Marvelic\BeamCheckout\Controller\AbstractCheckoutAction;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractCheckoutRedirectAction extends AbstractCheckoutAction
{
    /**
     * @var Customer
     */
    protected $objCustomer;

    /**
     * @var BeamCheckoutRequest
     */
    protected $objBeamCheckoutRequestHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $configSettings;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AbstractCheckoutRedirectAction constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Customer $customer
     * @param BeamCheckoutRequest $beamCheckoutRequest
     * @param ScopeConfigInterface $configSettings
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Customer $customer,
        BeamCheckoutRequest $beamCheckoutRequest,
        ScopeConfigInterface $configSettings,
        StoreManagerInterface $storeManager
    ) {

        parent::__construct($context, $checkoutSession, $orderFactory);
        $this->objCustomer = $customer;
        $this->objBeamCheckoutRequestHelper = $beamCheckoutRequest;
        $this->configSettings = $configSettings;
        $this->storeManager = $storeManager;
    }

    /* Get the BeamCheckout request helper class. It is responsible for construct the current user request for beamcheckout Payment Gateway. */
    protected function getBeamCheckoutRequest($paramter, $isloggedIn, $order)
    {
        return $this->objBeamCheckoutRequestHelper->beamcheckout_construct_request($paramter, $isloggedIn, $order);
    }

    /* This is magento object to get the customer object. */
    protected function getCustomerSession()
    {
        return $this->objCustomer;
    }
    
    /* Get the Beam Checkout End point */
    protected function getBeamCheckoutEndpoint($storeId)
    {
        return $this->objBeamCheckoutRequestHelper->getBeamCheckoutEndpoint($storeId);
    }
}
