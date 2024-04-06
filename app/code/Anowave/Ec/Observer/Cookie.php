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

namespace Anowave\Ec\Observer;

use Magento\Framework\Event\ObserverInterface;

abstract class Cookie implements ObserverInterface
{
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;
	
	/** 
	 * @var \Anowave\Ec\Model\Cookie\PrivateData
	 */
	protected $privateData;

	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * Cosntructor 
	 * 
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Model\Cookie\PrivateData $privateData
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 */
	public function __construct
	(
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Model\Cookie\PrivateData $privateData,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Framework\App\Request\Http $request
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set private data 
		 * 
		 * @var \Anowave\Ec\Model\Cookie\PrivateData $privateData
		 */
		$this->privateData = $privateData;
		
		/**
		 * JSON helper 
		 * 
		 * @var \Magento\Framework\Json\Helper\Data $jsonHelper
		 */
		$this->jsonHelper = $jsonHelper;
		
		/**
		 * Set request
		 * 
		 * @var\Magento\Framework\App\Request\Http $request
		 */
		$this->request = $request;
	}
}
