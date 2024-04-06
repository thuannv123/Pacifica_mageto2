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

class Position implements \Magento\Framework\Option\ArrayInterface
{
    const BOTTOM_RIGHT          = 'BOTTOM_RIGHT';
    const BOTTOM_LEFT           = 'BOTTOM_LEFT';
    const INLINE                = 'INLINE';
    const CENTER_DIALOG         = 'CENTER_DIALOG';
    const BOTTOM_RIGHT_DIALOG   = 'BOTTOM_RIGHT_DIALOG';
    const BOTTOM_LEFT_DIALOG    = 'BOTTOM_LEFT_DIALOG';
    const TOP_RIGHT_DIALOG      = 'TOP_RIGHT_DIALOG';
    const TOP_LEFT_DIALOG       = 'TOP_LEFT_DIALOG';
    const BOTTOM_TRAY           = 'BOTTOM_TRAY';
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
		    [
		        'value' => static::CENTER_DIALOG,
		        'label' => __('Center Dialog')
		    ],
		    [
		        'value' => static::BOTTOM_RIGHT_DIALOG,
		        'label' => __('Bottom Right Dialog')
		    ],
		    [
		        'value' => static::BOTTOM_LEFT_DIALOG,
		        'label' => __('Bottom Left Dialog')
		    ],
		    [
		        'value' => static::TOP_RIGHT_DIALOG,
		        'label' => __('Top Right Dialog')
		    ],
		    [
		        'value' => static::TOP_LEFT_DIALOG,
		        'label' => __('Top Left Dialog')
		    ],
		    [
		        'value' => static::BOTTOM_TRAY,
		        'label' => __('Bottom Tray')
		    ]
		];
	}
}