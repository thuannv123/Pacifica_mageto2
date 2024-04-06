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

class Override implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Anowave\Ec\Helper\Scope
	 */
	protected $scope;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Helper\Scope $scope
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Scope $scope
	)
	{
		/**
		 * Set scope 
		 * 
		 * @var \Anowave\Ec\Helper\Scope $scope
		 */
		$this->scope = $scope;
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$options = 
		[
			[
				'value' => 1,
				'label' => __('Yes')
			]
		];
		
		
		if (1 === (int) $this->scope->getConfig('ec/beta/mode'))
		{
			$options[] = 
			[
				'value' => 0,
				'label' => __('No')
			];
		}

		return $options;
	}
}