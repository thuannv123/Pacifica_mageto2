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

class Scope extends \Anowave\Package\Helper\Package
{
	const DEFAULT_SCOPE = 'default';
	
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * Store manager 
	 * 
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http $request
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
	}
	
	/**
	 * Get store config
	 *
	 * @param string $config
	 * 
	 * @return mixed
	 */
	public function getConfig($config)
	{
	    return $this->_context->getScopeConfig()->getValue($config, $this->getCurrentScope(), $this->getCurrentScopeCode());
	}
	
	/**
	 * Get current scope 
	 * 
	 * @return string
	 */
	public function getCurrentScope()
	{
		if ($this->request->getParam('store'))
		{
			return \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		}
		elseif ($this->request->getParam('website'))
		{
			return \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
		}
		
		return static::DEFAULT_SCOPE;
	}
	
	/**
	 * Get current scope code 
	 * 
	 * @return string|NULL
	 */
	public function getCurrentScopeCode()
	{
		if ($this->request->getParam('store'))
		{
			return $this->storeManager->getStore($this->request->getParam('store'))->getCode();
		}
		elseif ($this->request->getParam('website'))
		{
			return $this->storeManager->getWebsite($this->request->getParam('website'))->getCode();
		}
		
		return null;
	}
	
	/**
	 * Get request 
	 * 
	 * @return \Magento\Framework\App\Request\Http
	 */
	public function getRequest()
	{
		return $this->request;
	}
}