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

class Datalayer extends \Anowave\Package\Helper\Package
{
	/**
	 * dataLayer reference
	 * 
	 * @var []
	 */
	public $dataLayer = [];
	
	/**
	 * Merge dataLayer[]
	 */
	public function merge($data = [])
	{
		$this->dataLayer = array_merge_recursive($this->dataLayer, $data);
		
		return $this;
	}
	
	public function get()
	{
		return $this->dataLayer;
	}
}