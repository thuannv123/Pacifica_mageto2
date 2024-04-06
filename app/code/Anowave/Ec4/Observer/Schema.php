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

abstract class Schema implements ObserverInterface
{
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper; 
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager = null;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     */
    public function __construct
    (
        \Anowave\Ec4\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        /**
         * Set helper 
         * 
         * @var \Anowave\Ec4\Helper\Data $helper
         */
        $this->helper = $helper;
        
        /**
         * Set event manager 
         * 
         * @var \Magento\Framework\Message\ManagerInterface $eventManager
         */
        $this->messageManager = $messageManager;
    }
	/**
	 * Add order information into GA block to render on checkout success pages
	 *
	 * @param EventObserver $observer
	 * @return void
	 */
	public function execute(EventObserver $observer)
	{
	    return true;
	}
}
