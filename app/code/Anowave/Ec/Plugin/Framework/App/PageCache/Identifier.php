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
 
namespace Anowave\Ec\Plugin\Framework\App\PageCache;

class Identifier
{	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper
	)
	{
		/**
		 * Set helper
		 *
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
	}
	
	/**
	 * Modify cache identifier based on logged user
	 * 
	 * @param \Magento\Framework\App\PageCache\Identifier $identifier
	 * @param string $value
	 * @return string
	 */
	public function afterGetValue(\Magento\Framework\App\PageCache\Identifier $identifier, $value)
	{
		return $value;
	}
}