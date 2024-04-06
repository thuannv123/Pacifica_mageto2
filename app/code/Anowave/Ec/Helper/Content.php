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

class Content extends \Anowave\Package\Helper\Package
{
	/**
	 * Regular expression
	 * 
	 * @var string
	 */
	private $regex = '/<script type="text\/javascript">.*?<\/script>/ims';
	
	/**
	 * Get placeholders
	 *
	 * @param string $content
	 *
	 * @return string[]|NULL
	 */
	protected function getPlaceholders($content)
	{
		preg_match_all($this->regex, $content, $matches);
		
		if ($matches)
		{
			$placeholders = [];
			
			foreach ($matches[0] as $key => $match)
			{
				$placeholders["%{$key}%"] = $match;
			}
			
			return $placeholders;
		}
		
		return null;
	}
	
	/**
	 * Apply placeholders
	 *
	 * @param string $content
	 *
	 * @return string[]|NULL
	 */
	protected function applyPlaceholders(&$content)
	{
		if (null !== $placeholders = $this->getPlaceholders($content));
		{
			foreach ($placeholders as $placeholder => $value)
			{
				$content = str_replace($value,$placeholder, $content);
			}
		}
		
		return $placeholders;
	}
	
	/**
	 * Restore placeholders
	 *
	 * @param string $content
	 * @param string $placeholders
	 *
	 * @return mixed
	 */
	protected function restorePlaceholders(&$content, $placeholders)
	{
		if ($placeholders)
		{
			if ($placeholders)
			{
				foreach ($placeholders as $placeholder => $value)
				{
					$content = str_replace($placeholder,$value, $content);
				}
			}
		}
		
		return $content;
	}
}