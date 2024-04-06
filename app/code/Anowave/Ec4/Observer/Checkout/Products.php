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

namespace Anowave\Ec4\Observer\Checkout;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Products implements ObserverInterface
{
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Anowave\Ec\Helper\Affiliation
     */
    protected $affiliation;

    /**
     * Constructor 
     * 
     * @param \Anowave\Ec\Helper\Affiliation $affiliation
     * @param \Anowave\Ec4\Helper\Data $helper
     */
    public function __construct
    (
        \Anowave\Ec\Helper\Affiliation $affiliation,
        \Anowave\Ec4\Helper\Data $helper
    )
    {
        $this->affiliation = $affiliation;
        
        /**
         * Set helper 
         * 
         * @var \Anowave\Ec4\Helper\Data $helper
         */
        $this->helper = $helper;
    }
    
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->helper->isGA4Enabled())
        {
            return true;
        }
        
        if (!$this->affiliation->isEnabled())
        {
            return true;
        }
          
        $products = $observer->getTransport()->getProducts();
        
        $affiliation = $this->affiliation->getAffiliation();
        
        foreach ($products as &$product)
        {
            $product['affiliation'] = $affiliation;
        }
        
        unset($product);
        
        
        $observer->getTransport()->setProducts($products);
    }
}