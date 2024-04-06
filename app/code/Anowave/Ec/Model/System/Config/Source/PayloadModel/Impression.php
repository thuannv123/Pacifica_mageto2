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

namespace Anowave\Ec\Model\System\Config\Source\PayloadModel;

class Impression implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Build impression payload based on pre-rendered products collection
	 * 
	 * @var integer
	 */
	const MODEL_PRE_RENDER = 0;
	
	/**
	 * Build impression payload based on rendered products (IntersectionObserver) 
	 * @var integer
	 */
	const MODEL_POST_RENDER = 1;
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::MODEL_PRE_RENDER, 
				'label' => __('Set dataLayer[] impressions before Pageview')
			],
			[
				'value' => static::MODEL_POST_RENDER, 
				'label' => __('Set dataLayer[] impressions after Pageview (requires additional tag/trigger)')
			]
		];
	}
}