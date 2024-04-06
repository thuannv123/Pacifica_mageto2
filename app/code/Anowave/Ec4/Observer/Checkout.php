<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking GA4
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
 * @package 	Anowave_Ec4
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Checkout implements ObserverInterface
{
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     */
    public function __construct
    (
        \Anowave\Ec4\Helper\Data $helper
        
    )
    {
        $this->helper = $helper;
    }
    
    /**
     * Modify checkout payload 
     * 
     * {@inheritDoc}
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(EventObserver $observer)
    {
        $checkout = $observer->getTransport()->getCheckout();
        
        $quote = $this->helper->getQuoteDetails();
        
        if ($quote)
        {
            if (isset($quote['coupon']))
            {
                $checkout['push']['ecommerce']['coupon'] = $quote['coupon'];
            }
        }
        
        
        $checkout['push']['ecommerce']['currency'] = $this->helper->getBaseHelper()->getCurrency();
        
        /**
         * Update observer
         */
        $observer->getTransport()->setCheckout($checkout);
    }
}