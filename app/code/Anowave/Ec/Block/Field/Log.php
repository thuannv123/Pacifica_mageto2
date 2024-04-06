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
 
namespace Anowave\Ec\Block\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Log extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor
	 *
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Anowave\Ec\Model\Api $api
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Backend\Block\Template\Context $context,
		\Anowave\Ec\Helper\Data $helper,
		array $data = []
	)
	{
		parent::__construct($context, $data);
		 
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data
		 */
		$this->helper = $helper;
	}
	
	/**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	$log = (string) $this->helper->getConfig('ec/logs/log');

    	/**
    	 * Check if log is not empty
    	 */
    	if ('' !== $log)
    	{
    		$log = array_filter((array) @unserialize($log));
    		
    		return $this->getLayout()->createBlock('Anowave\Ec\Block\Comment')->setTemplate('log.phtml')->setData
    		(
    			[
    				'log' => $log
    			]
    		)->toHtml();
    	}
    	
    	return __('Log is empty');
    }
}