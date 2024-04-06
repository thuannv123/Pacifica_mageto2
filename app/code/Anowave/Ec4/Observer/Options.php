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
use Anowave\Ec4\Observer\Schema as Schema;

class Options extends Schema implements ObserverInterface
{
    /**
     * Execute observer 
     * 
     * {@inheritDoc}
     * @see \Anowave\Ec4\Observer\Schema::execute()
     */
	public function execute(EventObserver $observer)
	{
	    if (!$this->helper->isGA4Enabled())
	    {
	        return true;
	    }
	    
	    $options = $observer->getTransport()->getOptions();
	    
	    $options['ec_api_variables_ga4']   = __('Create variables (Google Analytics 4)');
	    $options['ec_api_triggers_ga4']    = __('Create triggers (Google Analytics 4)');
	    $options['ec_api_tags_ga4']        = __('Create tags (Google Analytics 4)');
	    
	    $observer->getTransport()->setOptions($options);
	    
	    return true;
	}
}