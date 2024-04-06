<?php
/**
 * Anowave Magento 2 Onestepcheckout Add-on for GTM (UA) Tracking
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
 * @package 	Anowave_Ecoam
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ecoam\Block;

class Plugin extends \Anowave\Ec\Block\Plugin
{
	/**
	 * Block output modifier
	 *
	 * @param \Magento\Framework\View\Element\Template $block
	 * @param string $content
	 *
	 * @return string
	 */
	public function afterToHtml($block, $content)
	{
		/**
		 * Do not employ code for base module
		 */
		if ('checkout.root' == $block->getNameInLayout())
		{
			return $content;
		}
		
		/**
		 * Call parent
		 */
		$content = parent::afterToHtml($block, $content);
	
		/**
		 * Amasty support
		 */
		if ('amasty.checkout.head' == $block->getNameInLayout())
		{
			return $this->augmentCheckoutOscBlock($block, $content);
		}
		
		if ('checkout.cart' == $block->getNameInLayout())
		{
		    return $this->augmentCartOscBlock($block, $content);
		}
		
		return $content;
	}
	
	/**
	 * Modify cart output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 *
	 * @return string
	 */
	protected function augmentCartOscBlock($block, $content)
	{
	    return $content .= $block->getLayout()->createBlock('Anowave\Ec\Block\Track')->setTemplate('Anowave_Ecoam::cart.phtml')->setData
	    (
	        [
	            'cart_push' => $this->_helper->getCheckoutPush($block, $this->_coreRegistry)
	        ]
	        )->toHtml();
	}
	
	/**
	 * Modify checkout output
	 *
	 * @param AbstractBlock $block
	 * @param string $content
	 *
	 * @return string
	 */
	protected function augmentCheckoutOscBlock($block, $content)
	{
		return $content .= $block->getLayout()->createBlock('Anowave\Ecoam\Block\Checkout')->setTemplate('Anowave_Ecoam::checkout.phtml')->setData
		(
			array
			(
				'checkout_push' => $this->_helper->getCheckoutPush($block, $this->_coreRegistry)
			)
		)
		->toHtml();
	}
}