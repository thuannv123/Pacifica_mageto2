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

namespace Anowave\Ec\Model\System\Config\Source\Optimize;

class Implementation implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Standard implementation 
	 * 
	 * @var integer
	 */
	const I_ASSISTED = 0;
	
	/**
	 * Implementation using GTAG
	 * 
	 * @var integer
	 */
	const I_STANDALONE = 1;
	
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
		$this->helper = $helper;	
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$options = 
		[
			[
				'value' => self::I_ASSISTED,
				'label' => __('Assisted (depends on Optimize implemented via tag in GTM)')
			]
		];
		
		if ($this->helper->isBetaMode())
		{
			$options[] = 
			[
				'value' => self::I_STANDALONE,
				'label' => __('Standalone (installs/configures Optimize without GTM)')
			];
		}
		
		return $options;
	}
}