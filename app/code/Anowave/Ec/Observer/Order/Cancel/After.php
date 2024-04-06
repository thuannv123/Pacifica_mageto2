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

namespace Anowave\Ec\Observer\Order\Cancel;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class After implements ObserverInterface
{
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Anowave\Ec\Helper\Data $helper
	 */
	public function __construct
	(
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Anowave\Ec\Helper\Data $helper
	)
	{
		/**
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Magento\Framework\Message\ManagerInterface $messageManager
		 */
		$this->messageManager = $messageManager;
		
		/**
		 * Set helper
		 * 
		 * @var \Anowave\Ec\Observer\Order\Cancel\After $helper
		 */
		$this->helper = $helper;
	}
	
	/**
	 * Execute (non-PHPdoc)
	 * 
	 * @see \Magento\Framework\Event\ObserverInterface::execute()
	 */
	public function execute(EventObserver $observer)
	{
		if ($this->trackCancel())
		{
			if (null !== $order = $observer->getOrder())
			{
				if ($this->protocol->cancel($order))
				{
					$this->messageManager->addWarningMessage("Transaction {$order->getIncrementId()} reversed in Google Analytics {$this->protocol->getUA($order)} successfully");
				}
				else 
				{
				    foreach ($this->protocol->getErrors() as $error)
				    {
				        $this->messageManager->addNoticeMessage($error);
				    }
				}
			}
		}
		else 
		{
			if (null !== $order = $observer->getOrder())
			{
				$this->messageManager->addNoticeMessage("Google Analytics transaction cancel is currently not enabled. Transaction {$order->getIncrementId()} not canceled in Google Analytics.");
			}
		}
		
		return true;
	}
	
	/**
	 * Check if cancel tracking is enabled
	 * 
	 * @return boolean
	 */
	public function trackCancel()
	{
		return 1 === (int) $this->helper->getConfig('ec/gmp/use_measurement_protocol_cancel');
	}
}