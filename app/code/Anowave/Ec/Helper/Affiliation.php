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

class Affiliation extends \Anowave\Package\Helper\Package
{
	/**
	 * Affiliate placeholder
	 * 
	 * @var string
	 */
	const PLACEHOLDER = '%%affiliate%%'; 

	/**
	 * Default dimension index (as in Google Analytics)
	 * 
	 * @var integer
	 */
	const DEFAULT_DIMENSION_INDEX = 20; 
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager = null;
	
	/**
	 * @var \Magento\Framework\App\Http\Context
	 */
	protected $httpContext;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\App\Http\Context $httpContext
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Http\Context $httpContext,
		array $data = []
	)
	{
		parent::__construct($context);
		/**
		 * Set store manager 
		 * 
		 * @var \Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set HTTP context 
		 * 
		 * @var \Magento\Framework\App\Http\Context $httpContext
		 */
		$this->httpContext = $httpContext;
	}
	
	/**
	 * Get affiliation 
	 * 
	 * @return string
	 */
	public function getAffiliation()
	{	
		if ($this->isEnabled())
		{
			$affiliate = $this->httpContext->getValue(\Anowave\Ec\Plugin\App\Action\Context::ID);

			if ($affiliate)
			{
				return $affiliate;
			}
		}

		return trim
		(
			$this->storeManager->getStore()->getName()
		);
	}
	
	/**
	 * Get affiliation placeholder
	 * 
	 * @return string
	 */
	public function getAffiliationPlaceholder()
	{
		return self::PLACEHOLDER;
	}
	
	/**
	 * Get affiliation index
	 * 
	 * @return number|string
	 */
	public function getAffiliationIndex()
	{
		$index = (int) $this->getConfig('ec/affiliate/dimension');
		
		if (!$index)
		{
			$index = self::DEFAULT_DIMENSION_INDEX;
		}
		
		return $index;
	}
	
	/**
	 * Get affiliation array 
	 * 
	 * @return string[]
	 */
	public function getAffiliationArray()
	{
		if (!$this->isEnabled())
		{
			return [];
		}
		
		return 
		[
			"dimension{$this->getAffiliationIndex()}" => $this->getAffiliationPlaceholder()
		];
	}
	
	/**
	 * Check if affiliate tracking is enabled
	 * 
	 * @return boolean
	 */
	public function isEnabled()
	{
		return (1 === (int) $this->getConfig('ec/affiliate/enable'));
	}
}