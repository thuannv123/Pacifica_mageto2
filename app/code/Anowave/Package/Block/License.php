<?php
/**
 * Anowave Package
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
 * @package 	Anowave_Package
 * @copyright 	Copyright (c) 2021 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 

namespace Anowave\Package\Block;

class License extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var Magento\Framework\App\Request\Http
	 */
	protected $request = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context, 
		array $data = []
	)
	{
		parent::__construct($context, $data);
		
		/**
		 * Get request 
		 * 
		 * @var Magento\Framework\App\Request\Http $request
		 */
		$this->request = $context->getRequest();
	}
	
	/**
	 * Get domain 
	 * 
	 * @return string
	 */
	public function getHost()
	{
		if (null !== $store = $this->request->getParam('store'))
		{
			return parse_url($this->_storeManager->getStore($store)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB), PHP_URL_HOST);
		}
		
		return $_SERVER['HTTP_HOST'];
	}
}