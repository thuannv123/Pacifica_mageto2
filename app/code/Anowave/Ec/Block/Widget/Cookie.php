<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Block\Widget;

use Magento\Widget\Block\BlockInterface; 

class Cookie extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * Set template
     * 
     * @var string
     */
    protected $_template = 'Anowave_Ec::cookiewidget.phtml';
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Anowave\Ec\Helper\Cookie
     */
    protected $cookie;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Anowave\Ec\Helper\Data $helper
     * @param \Anowave\Ec\Helper\Cookie $cookie
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Anowave\Ec\Helper\Data $helper,
        \Anowave\Ec\Helper\Cookie $cookie,
        array $data = []
    )
    {
        /**
         * Set Helper
         * @var \Anowave\Ec\Helper\Data
         */
        $this->helper = $helper;
        
        /**
         * Set cookie helper 
         * 
         * @var \Anowave\Ec\Helper\Cookie $cookie
         */
        $this->cookie = $cookie;
        
        /**
         * Parent constructor
         */
        parent::__construct($context, $data);
    }
    
    /**
     * Get segments
     * 
     * @return array
     */
    public function getSegments()
    {
        return $this->cookie->getSegments();
    }
    
    /**
     * Get helper
     *
     * @return \Anowave\Ec\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
