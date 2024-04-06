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

namespace Anowave\Ec\Model\System\Config\Source\Facebook;

class Event implements \Magento\Framework\Option\ArrayInterface
{	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		return 
		[
		    [
		        'label' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT,
		        'value' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_GRANTED_EVENT
		    ],
		    [
		        'label' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT,
		        'value' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_ANALYTICS_GRANTED_EVENT
		    ],
		    [
		        'label' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT,
		        'value' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_MARKETING_GRANTED_EVENT
		    ],
		    [
		        'label' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT,
		        'value' => \Anowave\Ec\Helper\Constants::COOKIE_CONSENT_PREFERENCES_GRANTED_EVENT
		    ]
		];
	}
}