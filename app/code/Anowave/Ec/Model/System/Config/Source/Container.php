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

class Container implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * 
	 * @var \Magento\Framework\View\Element\BlockFactory
	 */
	protected $blockFactory;
	
	/**
	 * Cosntructor 
	 * 
	 * @param \Anowave\Ec\Model\Api $api
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\BlockFactory $blockFactory
	)
	{
		/**
		 * Set block factory 
		 * 
		 * @var \Magento\Framework\View\Element\BlockFactory $blockFactory
		 */
		$this->blockFactory = $blockFactory;
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$options = [];
		
		$block = $this->blockFactory->createBlock('Anowave\Ec\Block\Field\Comment');
		
		if ($block)
		{
			$errors = [];
			
			try
			{
				foreach($block->getContainers() as $container)
				{
					$options[] =
					[
						'value' => $container->containerId,
						'label' => $container->publicId
						
					];
				}
			}
			catch (\Exception $e)
			{
				$errors[] = $e->getMessage();
			}
			
			if (!$errors)
			{
				if (!$block->getApi()->getClient()->isAccessTokenExpired())
				{
					if (!$options)
					{
						$options =
						[
							[
								'value' => null,
								'label' => __('No container(s) detected (Check your Account ID)')
							]
						];
					}
				}
				else 
				{
					$options =
					[
						[
							'value' => null,
							'label' => __('Not set (Sign in with Google first)')
						]
					];
				}
			}
			else 
			{
				$options = 
				[
					[
						'value' => null,
						'label' => join(PHP_EOL, $errors)
					]	
				];
			}
		}

		return $options;
	}
}