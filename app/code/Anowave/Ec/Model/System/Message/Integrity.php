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

namespace Anowave\Ec\Model\System\Message;

class Integrity implements \Magento\Framework\Notification\MessageInterface
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * @var \Magento\Backend\Model\UrlInterface
	 */
	protected $backendUrl;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Backend\Model\UrlInterface $backendUrl
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Backend\Model\UrlInterface $backendUrl
	)
	{
		$this->helper = $helper;
		$this->backendUrl = $backendUrl;
	}
	
	/**
	 * Get message identity 
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Framework\Notification\MessageInterface::getIdentity()
	 */
	public function getIdentity()
	{
		return 'ec_integrity';
	}
	
	/**
	 * Check whether message should be displayed
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Framework\Notification\MessageInterface::isDisplayed()
	 */
	public function isDisplayed()
	{
		if (!$this->helper->isAdwordsConversionTrackingActive())
		{
			if ('' !== (string) $this->helper->getConfig('ec/adwords/conversion_id'))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Get message text 
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Framework\Notification\MessageInterface::getText()
	 */
	public function getText()
	{
		$message = 
		[
			"AdWords Conversion Tracking is disabled but Conversion ID exists. Please check AdWords Conversion settings <a href='{$this->backendUrl->getUrl("adminhtml/system_config/edit/section/ec", [])}'>HERE</a>",
			"If you use AdWords Conversion Tracking you may need to enable it explicitly. Otherwise leave fields empty and disable AdWords Conversion Tracking."
		];
		
		return nl2br(join(PHP_EOL, $message));
	}
	
	public function getSeverity()
	{
		return self::SEVERITY_MAJOR;
	}
}