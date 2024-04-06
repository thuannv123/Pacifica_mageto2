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

namespace Anowave\Ec4\Block;

class Config extends \Magento\Framework\View\Element\Template
{
    const DEFAULT_CONVERSION_EVENT = 'purchase';
    
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Anowave\Ec4\Helper\Data $helper
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Anowave\Ec4\Helper\Data $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        
        $this->helper = $helper;
    }
    /**
     * Check if GA4 is enabled 
     * 
     * @return bool
     */
    public function isGA4Enabled() : bool
    {
        return $this->helper->isGA4Enabled();
    }
    
    /**
     * Get conversion event
     * 
     * @return string
     */
    public function getConversionEvent() : string
    {
        $conversion_event = (string) $this->helper->getConfig('ec4/preferences/conversion_event');
        
        if (!$conversion_event)
        {
            $conversion_event = static::DEFAULT_CONVERSION_EVENT;
        }
        
        return $conversion_event; 
    }
    
    /**
     * Get quote details
     * 
     * @return array
     */
    public function getQuoteDetails() : array
    {
        return $this->helper->getQuoteDetails();
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
}