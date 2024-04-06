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

namespace Anowave\Ec\Helper;

use Anowave\Package\Helper\Package;

class Bridge extends \Anowave\Package\Helper\Package
{
	/**
	 * @var \Magento\Framework\App\ProductMetadataInterface
	 */
	protected $productMetadata;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ProductMetadataInterface $productMetadata,
		array $data = []
	)
	{
		parent::__construct($context);
		
		$this->productMetadata = $productMetadata;
	}
	/**
	 * Bridge between CE and EE (2.x)
	 * 
	 * @param \Magento\Catalog\Block\Product\AbstractProduct $block
	 */
	public function getLoadedItems($block)
	{	
		/**
		 * Try to get loaded items
		 * 
		 * @var [] $items
		 */
		$items = $block->getLoadedItems();
		
		/**
		 * Check items. They could be null in Magento EE
		 */
		if (!$items)
		{
			$items = $block->getAllItems();	
		}
		
		if ($items)
		{
			return $items;
		}
		
		return [];
	}
	
	public function getVersion()
	{
		return $this->productMetadata->getVersion();
	}
}