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
use Magento\Backend\Block\Template;

class Auth extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
	 * @var \Anowave\Ec\Model\Api
	 */
	protected $api;
	
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
    	\Anowave\Ec\Model\Api $api,
        \Anowave\Ec\Helper\Data $helper,
    	array $data = [])
    {
    	parent::__construct($context, $data);
    	
    	/**
    	 * Set API 
    	 * 
    	 * @var \Anowave\Ec\Model\Api
    	 */
    	$this->api = $api;
    	
    	/**
    	 * Set helper 
    	 * 
    	 * @var \Anowave\Ec\Helper\Data $helper
    	 */
    	$this->helper = $helper;
    }
	
	/**
	 * Get element content
	 * 
	 * @see \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
	    $options = 
	    [
	        'ec_api_variables' => __('Create variables (Universal Analytics)'),
	        'ec_api_triggers'  => __('Create triggers (Universal Analytics)'),
	        'ec_api_tags'      => __('Create tags (Universal Analytics)')
	    ];
	    
	    /**
	     * Create transport object
	     *
	     * @var \Magento\Framework\DataObject $transport
	     */
	    $transport = new \Magento\Framework\DataObject
	    (
	        [
	            'options' => $options
	        ]
        );
	    
	    /**
	     * Notify others for schema
	     */
	    $this->helper->getEventManager()->dispatch('ec_schema_options', ['transport' => $transport]);
	    
		return $this->getLayout()->createBlock('Anowave\Ec\Block\Comment')->setTemplate('auth.phtml')->setData
		(
			array
			(
				'api' => $this->api,
			    'options' => $transport->getOptions()
			)
		)->toHtml();
	}
}