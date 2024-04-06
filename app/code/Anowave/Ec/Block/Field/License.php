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

class License extends \Magento\Config\Block\System\Config\Form\Field
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
	 * Get element content
	 * 
	 * @see \Magento\Config\Block\System\Config\Form\Field::_getElementHtml()
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		return $this->getLayout()->createBlock('Anowave\Package\Block\License')->setTemplate('Anowave_Package::license.phtml')->setData([])->toHtml();
    }
}