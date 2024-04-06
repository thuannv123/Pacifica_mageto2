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

namespace Anowave\Ec\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Cookie
{
	/**
	 * Name of cookie that holds private content version
	 */
	protected $name = 'affiliate';
	
	/**
	 * CookieManager
	 *
	 * @var CookieManagerInterface
	 */
	protected $cookieManager;
	
	/**
	 * @var CookieMetadataFactory
	 */
	protected $cookieMetadataFactory;
	
	/**
	 * @var SessionManagerInterface
	 */
	protected $sessionManager;
	
	/**
	 * Constructor 
	 * 
	 * @param CookieManagerInterface $cookieManager
	 * @param CookieMetadataFactory $cookieMetadataFactory
	 * @param SessionManagerInterface $sessionManager
	 */
	public function __construct
	(
		CookieManagerInterface $cookieManager,
		CookieMetadataFactory $cookieMetadataFactory,
		SessionManagerInterface $sessionManager
	)
	{
		/**
		 * Set Cookie Manager 
		 * 
		 * @var CookieManagerInterface $cookieManager
		 */
		$this->cookieManager = $cookieManager;
		
		/**
		 * Set Cookie Metafactory
		 * 
		 * @var CookieMetadataFactory $cookieMetadataFactory
		 */
		$this->cookieMetadataFactory = $cookieMetadataFactory;
		
		/**
		 * Set session manager 
		 * 
		 * @var SessionManagerInterface $sessionManager
		 */
		$this->sessionManager = $sessionManager;
	}

	/**
	 * Get form key cookie
	 *
	 * @return string
	 */
	public function get()
	{
		return $this->cookieManager->getCookie($this->name);
	}
	
	/**
	 * Set cookie
	 * 
	 * @param string  $value
	 * @param number $duration. Defaults to 1 hour (3600 sec.)
	 */
	public function set($value = '', $duration = 3600)
	{
		$this->cookieManager->setPublicCookie
		(
			$this->name, $value, $this->cookieMetadataFactory->createPublicCookieMetadata()->setDuration($duration)->setPath($this->sessionManager->getCookiePath())->setDomain($this->sessionManager->getCookieDomain())
		);
	}
	
	/**
	 * Delete cookie
	 * 
	 * @return void
	 */
	public function delete()
	{
		$this->cookieManager->deleteCookie
		(
			$this->name, $this->cookieMetadataFactory->createCookieMetadata()->setPath($this->sessionManager->getCookiePath())->setDomain($this->sessionManager->getCookieDomain())
		);
	}
}