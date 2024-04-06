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

namespace Anowave\Ec\Observer\Customer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Register implements ObserverInterface
{
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $session;

	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Customer\Model\SessionFactory $sessionFactory
	 * @param \Anowave\Ec\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Customer\Model\SessionFactory $sessionFactory,
		\Anowave\Ec\Helper\Data $helper
	)
	{
		/**
		 * Set session 
		 * 
		 * @var \Magento\Customer\Model\Session $session
		 */
		$this->session = $sessionFactory->create();
		
		/**
		 * Set JSON helper 
		 * 
		 * @var \Anowave\Ec\Helper\Json $jsonHelper
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
		if ($observer->getCustomer())
		{
			$this->session->setFacebookCompleteRegistrationEvent($this->helper->getJsonHelper()->encode(
			[
			    'status'       => true,
			    'currency'     => $this->helper->getCurrency(),
				'content_name' => $this->helper->getStoreName()
			]));
			
			$this->session->setCustomerRegisterEvent($this->helper->getJsonHelper()->encode(
		    [
		        'event'       => 'customerRegister',
		        'eventAction' => 'createAccount'		        
		    ]));
		}
	}
}