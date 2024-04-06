<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
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
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Plugin;

class Comment
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
     * After get data 
     * 
     * @param \Anowave\Ec\Block\Comment $block
     * @param mixed $result
     * @param string $key
     * @param string $index
     * @return array|unknown
     */
    public function afterGetData(\Anowave\Ec\Block\Comment $block, $result, $key, $index)
    {
        if (!$this->helper->isGA4Enabled())
        {
            return $result;
        }
        
        if ('options' === $key)
        {
            return array_filter($result, function($key)
            {
                return in_array($key, ['ec_api_variables_ga4','ec_api_triggers_ga4','ec_api_tags_ga4']);
                
            }, ARRAY_FILTER_USE_KEY);
        }
        
        return $result;
    }
    
    /**
     * After Get Property Title
     * 
     * @param \Anowave\Ec\Block\Comment $block
     * @param string $title
     * @return string
     */
    public function afterRenderPropertyTitle(\Anowave\Ec\Block\Comment $block, $title)
    {
        if (!$this->helper->isGA4Enabled())
        {
            return $title;
        }
        
        return __('GA4 Configuration tag');
    }
}