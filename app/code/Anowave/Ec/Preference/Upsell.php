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

namespace Anowave\Ec\Preference;

class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell
{
	/**
	 * Get loaded items
	 * 
	 * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
	 */
	public function getLoadedItems()
	{
		$this->_prepareData();
		
		return $this->getItems();
	}
	
	/**
	 * Prevent template issues by overriding _toHtml() method
	 *
	 * {@inheritDoc}
	 * @see \Magento\Framework\View\Element\Template::_toHtml()
	 */
	protected function _toHtml()
	{
		$this->setModuleName($this->extractModuleName('Magento\Catalog\Block\Product\ProductList\Upsell'));
		
		return parent::_toHtml();
	}
}