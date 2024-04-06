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

namespace Anowave\Ec\Model\System\Config\Source\Consent;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Generic mode. If enabled, the cookie consent will collect a generic consent
	 * 
	 * @var integer
	 */
	const GENERIC = 0;
	
	/**
	 * Segment mode. If enabled, the cookie consent will collect a differentatiated consent
	 * 
	 * @var integer
	 */
	const SEGMENT = 1;
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::GENERIC, 
				'label' => __('Generic consent')
			],
			[
				'value' => static::SEGMENT, 
				'label' => __('Segmented consent')
			]
		];
	}
}