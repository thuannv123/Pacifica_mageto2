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

class Validate implements ObserverInterface
{
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $_helper = null;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager = null;
    
    /**
     * @var \Anowave\Ec\Helper\Scope
     */
    protected $scope;
    
    /**
     * Constructor
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Anowave\Ec\Helper\Scope $scope
     */
    public function __construct
    (
        \Anowave\Ec4\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Anowave\Ec\Helper\Scope $scope
        )
    {
        /**
         * Set helper
         *
         * @var \Anowave\Ec4\Helper\Data $_helper
         */
        $this->_helper = $helper;
        
        /**
         * Set message manager
         *
         * @var \Magento\Framework\Message\ManagerInterface $_messageManager
         */
        $this->_messageManager = $messageManager;
        
        /**
         * @var \Anowave\Ec\Helper\Scope $scope
         */
        $this->scope = $scope;
    }
    
    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->_helper->isGA4Enabled())
        {
            return true;
        }
        
        $errors = $observer->getTransport()->getErrors();
        $notice = $observer->getTransport()->getNotice();
        
        if (array_key_exists('google_gtm_ua', $errors))
        {
            unset($errors['google_gtm_ua']);
            
            $notice[] = __('Universal Analytics ID is missing (skipping)');
        }
        
        $args = $observer->getTransport()->getArgs();
        
        if (in_array('ec_api_tags', $args))
        {
            $errors[] = __('Mixing Universal Analytics and GA4 tags should be avoided. Check GA4 tags only.');
        }
        
        if (in_array('ec_api_variables', $args))
        {
            $errors[] = __('Mixing Universal Analytics and GA4 variables should be avoided. Check GA4 variables only.');
        }
        
        if (in_array('ec_api_triggers', $args))
        {
            $errors[] = __('Mixing Universal Analytics and GA4 triggers should be avoided. Check GA4 triggers only.');
        }
        
        if ('' === (string) $this->scope->getConfig('ec/api/google_gtm_ua4_measurement_id'))
        {
            $errors['google_gtm_ua4_measurement_id'] = __('Google Analytics 4 Measurement ID is missing.');
        }
        
        $observer->getTransport()->setErrors($errors);
        $observer->getTransport()->setNotice($notice);
    }
}