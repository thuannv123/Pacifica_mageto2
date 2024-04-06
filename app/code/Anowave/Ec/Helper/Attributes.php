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

class Attributes extends \Anowave\Package\Helper\Package
{
	/**
	 * @var \Anowave\Ec\Helper\Affiliation
	 */
	protected $affiliation;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Anowave\Ec\Helper\Affiliation $affiliation
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Anowave\Ec\Helper\Affiliation $affiliation,
		array $data = []
	)
	{
		parent::__construct($context);
		
		/**
		 * Set affiliation 
		 * 
		 * @var \Anowave\Ec\Helper\Affiliation $affiliation
		 */
		$this->affiliation = $affiliation;
	}
	
	/**
	 * Get affiliation
	 *
	 * @return string
	 */
	public function getAttributes()
	{
		return array_merge([],$this->affiliation->getAffiliationArray());
	}
}