<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Model\System\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Shared type. Use the same measurement ID for frontend and backend orders
     * @var integer
     */
    const TYPE_SHARED = 0;
    
    /**
     * Dedicated type. Use separate measurement ID for frontend and backend orders
     * 
     * @var integer
     */
    const TYPE_DEDICATED = 1;
    
    
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
			[
				'value' => static::TYPE_SHARED,
				'label' => __('Shared')
			],
			[
				'value' => static::TYPE_DEDICATED,
				'label' => __('Dedicated')
			]
		];
	}
}