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

namespace Anowave\Ec\Model\System\Config\Source\CustomerReviews;

class Delivery implements \Magento\Framework\Option\ArrayInterface
{
    const DELIVERY_NOW          = 0;
    const DELIVERY_1_DAYS     = '+ 1 days';
    const DELIVERY_2_DAYS     = '+ 2 days';
    const DELOVERY_3_DAYS     = '+ 3 days';
    const DELOVERY_4_DAYS     = '+ 4 days';
    const DELOVERY_5_DAYS     = '+ 5 days';
    const DELOVERY_1_WEEK     = '+ 7 days';
    const DELOVERY_2_WEEK     = '+ 14 days';
    const DELOVERY_1_MONTH    = '+ 1 month';
    
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::DELIVERY_NOW, 
				'label' => __('Dispatched same day')
			],
			[
				'value' => static::DELIVERY_1_DAYS, 
				'label' => __('1 business day')
			],
		    [
		        'value' => static::DELIVERY_2_DAYS,
		        'label' => __('2 business days')
		    ],
		    [
		        'value' => static::DELOVERY_3_DAYS,
		        'label' => __('3 business dayss')
		    ],
		    [
		        'value' => static::DELOVERY_4_DAYS,
		        'label' => __('4 business days')
		    ],
		    [
		        'value' => static::DELOVERY_5_DAYS,
		        'label' => __('5 business days')
		    ],
		    [
		        'value' => static::DELOVERY_1_WEEK,
		        'label' => __('1 week')
		    ],
		    [
		        'value' => static::DELOVERY_2_WEEK,
		        'label' => __('2 weeks')
		    ],
		    [
		        'value' => static::DELOVERY_1_MONTH,
		        'label' => __('1 month')
		    ]
		];
	}
}