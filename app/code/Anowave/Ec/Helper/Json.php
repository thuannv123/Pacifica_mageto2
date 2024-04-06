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

class Json extends \Anowave\Package\Helper\Package
{
	/**
	 * Encode JSON 
	 * 
	 * @param mixed $content
	 * @return string
	 */
	public function encode($value, $options = null, $depth = null)
	{
		if (is_null($options))
		{
			$options = JSON_UNESCAPED_UNICODE;
		}
		
		return json_encode($value, $options);
	}

	/**
	 * Decode JSON 
	 * 
	 * @param string $json
	 * @param boolean $assoc
	 * @param integer $depth
	 * @param array $options
	 * @return mixed
	 */
	public function decode($json, $assoc = null, $depth = null, $options = null)
	{
		return json_decode($json, $assoc, $depth, $options);
	}
	
	/**
	 * Get payload size in bytes
	 * 
	 * @param string $str
	 * @return number
	 */
	public function getSize($str)
	{
		// Number of characters in string
		$strlen_var = strlen($str);
		
		// string bytes counter
		$d = 0;
		
		for($c = 0; $c < $strlen_var; ++$c)
		{
			$ord_var_c = ord($str[$c]);
			
			switch(true)
			{
				case(($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
					// characters U-00000000 - U-0000007F (same as ASCII)
					$d++;
					break;
				case(($ord_var_c & 0xE0) == 0xC0):
					// characters U-00000080 - U-000007FF, mask 110XXXXX
					$d+=2;
					break;
				case(($ord_var_c & 0xF0) == 0xE0):
					// characters U-00000800 - U-0000FFFF, mask 1110XXXX
					$d+=3;
					break;
				case(($ord_var_c & 0xF8) == 0xF0):
					// characters U-00010000 - U-001FFFFF, mask 11110XXX
					$d+=4;
					break;
				case(($ord_var_c & 0xFC) == 0xF8):
					// characters U-00200000 - U-03FFFFFF, mask 111110XX
					$d+=5;
					break;
				case(($ord_var_c & 0xFE) == 0xFC):
					// characters U-04000000 - U-7FFFFFFF, mask 1111110X
					$d+=6;
					break;
				default:
					$d++;
			};
		};
		
		return $d;
	}
}