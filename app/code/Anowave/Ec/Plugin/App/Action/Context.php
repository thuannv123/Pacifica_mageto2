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

namespace Anowave\Ec\Plugin\App\Action;

class Context
{
	const ID = 'affiliate'; 
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;
	
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext;
	
	/**
	 * @var \Anowave\Ec\Model\Cookie
	 */
	protected $cookie;
	
	/**
	 * @var \Anowave\Package\Helper\Package
	 */
	protected $helper; 
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param \Anowave\Package\Helper\Package $helper
	 * @param \Anowave\Ec\Model\Cookie $cookie
	 */
	public function __construct
	(
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\Http\Context $httpContext,
		\Anowave\Package\Helper\Package $helper,
		\Anowave\Ec\Model\Cookie $cookie,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		/**
		 * Set customer session
		 * 
		 * @var \Anowave\Ec\Plugin\App\Action\Context $customerSession
		 */
		$this->customerSession 	= $customerSession;
		
		/**
		 * Set context 
		 * 
		 * @var \Anowave\Ec\Plugin\App\Action\Context $httpContext
		 */
		$this->httpContext = $httpContext;
		
		/**
		 * Set cookie
		 * 
		 * @var \Anowave\Ec\Model\Cookie $cookie
		 */
		$this->cookie = $cookie;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Package\Helper\Package $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set store manager
		 * 
		 * @var \Anowave\Ec\Plugin\App\Action\Context $storeManager
		 */
		$this->storeManager = $storeManager;
	}
	
	/**
	 * Around dispatch
	 *
	 * @param \Magento\Framework\App\ActionInterface $subject
	 * @param \Closure $proceed
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return unknown
	 */
	public function aroundDispatch(\Magento\Framework\App\ActionInterface $subject, \Closure $proceed, \Magento\Framework\App\RequestInterface $request)
	{
		if ($this->isEnabled())
		{
			$affiliate = null;

			if (null !== $parameter = $this->getAffiliationParameter())
			{
				if (array_key_exists($parameter, $_GET))
				{
					$affiliate = $_GET[$parameter];
					
					$this->cookie->set($_GET[$parameter]);
				}
			}
			
			if (!$affiliate)
			{
				$affiliate = $this->cookie->get();
			}
			
			if (!$affiliate)
			{
				/**
				 * Default affiliate
				 *
				 * @var string $affiliate
				 */
				$affiliate = trim($this->storeManager->getStore()->getName());
			}

			$this->httpContext->setValue(self::ID, $affiliate, false);
		}
		
		return $proceed($request);
	}
	
	/**
	 * Get affiliation parameter
	 *
	 * @return string|NULL
	 */
	public function getAffiliationParameter()
	{
		if ('' !== $parameter = (string) $this->helper->getConfig('ec/affiliate/parameter'))
		{
			return $parameter;
		}
		
		return null;
	}
	
	/**
	 * Check if affiliate tracking is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return (1 === (int) $this->helper->getConfig('ec/affiliate/enable'));
	}
}
