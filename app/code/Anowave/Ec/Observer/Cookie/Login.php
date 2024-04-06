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

namespace Anowave\Ec\Observer\Cookie;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Anowave\Ec\Observer\Cookie as Cookie;

class Login extends Cookie implements ObserverInterface
{
	/**
	 * Execute (non-PHPdoc)
	 *
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(EventObserver $observer)
	{
		try
		{
			/**
			 * Private data 
			 * 
			 * @var array $private
			 */
			$private = [];
			
			if ($this->privateData->get())
			{
				/**
				 * Get private data
				 *
				 * @var array $privateData
				 */
				$private = (array) $this->jsonHelper->jsonDecode($this->privateData->get());
			}
			
			$private['visitor'] = 
			[
				'visitorId' 		=> (int) $observer->getCustomer()->getId(),
				'visitorLoginState' => __('Logged in')->__toString()
			];
			
			
			$this->privateData->set
			(
				$this->jsonHelper->jsonEncode($private)
			);
		}
		catch (\Exception $e){}
	}
}