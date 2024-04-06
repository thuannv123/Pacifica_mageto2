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

namespace Anowave\Ec\Model\System\Config\Source;

class Throttle implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => 0,
				'label' => __('No throttle')
			],
			[
				'value' => 250000,
				'label' => __('4.0 queries per second')
			],
			[
				'value' => 500000,
				'label' => __('2.0 queries per second')
			],
			[
				'value' => 1000000,
				'label' => __('1.0 queries per second')
			],
			[
				'value' => 2000000,
				'label' => __('0.5 queries per second')
			],
			[
				'value' => 3000000,
				'label' => __('0.4 queries per second')
			],
			[
				'value' => 4000000,
				'label' => __('0.3 queries per second')
			],
			[
				'value' => 5000000,
				'label' => __('0.2 queries per second (Recommended)')
			]
		];
	}
}