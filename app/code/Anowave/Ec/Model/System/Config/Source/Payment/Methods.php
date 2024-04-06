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

namespace Anowave\Ec\Model\System\Config\Source\Payment;

class Methods implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magento\Payment\Helper\Data
	 */
	protected $paymentHelper;
	
	/**
	 * Constructor
	 * 
	 * @param \Magento\Payment\Helper\Data $paymentHelper
	 */
	public function __construct
	(
		\Magento\Payment\Helper\Data $paymentHelper
	) 
	{
		/**
		 * Set payment helpe
		 * 
		 * @var \Magento\Payment\Helper\Data $paymentHelper
		 */
		$this->paymentHelper = $paymentHelper;
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$methods = [];
		
		foreach ($this->paymentHelper->getPaymentMethodList() as $method => $label)
		{
			if (!is_null($label) && '' !== $label)
			{
				$methods[] = 
				[
					'value' => $method,
					'label' => $label
				];
			}
		}
		
		return $methods;
	}
}