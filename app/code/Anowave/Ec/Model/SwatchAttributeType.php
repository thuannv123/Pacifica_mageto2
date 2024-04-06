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

use Magento\Eav\Api\Data\AttributeInterface;
use Anowave\Ec\Model\Serializer\Json;
use Magento\Swatches\Model\Swatch;

class SwatchAttributeType
{
	/**
	 * @var Json
	 */
	private $serializer;
	
	/**
	 * Data key which should populated to Attribute entity from "additional_data" field
	 *
	 * @var array
	 */
	private $eavAttributeAdditionalDataKeys = 
	[
		Swatch::SWATCH_INPUT_TYPE_KEY,'update_product_preview_image','use_product_image_for_swatch'
	];
	
	/**
	 * SwatchAttributeType constructor.
	 * @param Json $serializer
	 */
	public function __construct(Json $serializer)
	{
		$this->serializer = $serializer;
	}
	
	/**
	 * @param AttributeInterface $productAttribute
	 * @return bool
	 */
	public function isTextSwatch(AttributeInterface $productAttribute)
	{
		$this->populateAdditionalDataEavAttribute($productAttribute);
		
		return $productAttribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY) === Swatch::SWATCH_INPUT_TYPE_TEXT;
	}
	
	/**
	 * @param AttributeInterface $productAttribute
	 * @return bool
	 */
	public function isVisualSwatch(AttributeInterface $productAttribute)
	{
		$this->populateAdditionalDataEavAttribute($productAttribute);
		
		return $productAttribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY) === Swatch::SWATCH_INPUT_TYPE_VISUAL;
	}
	
	/**
	 * @param AttributeInterface $productAttribute
	 * @return bool
	 */
	public function isSwatchAttribute(AttributeInterface $productAttribute)
	{
		return $this->isTextSwatch($productAttribute) || $this->isVisualSwatch($productAttribute);
	}
	
	/**
	 * @param AttributeInterface $attribute
	 * @return void
	 */
	private function populateAdditionalDataEavAttribute(AttributeInterface $attribute)
	{
		if (!$attribute->hasData(Swatch::SWATCH_INPUT_TYPE_KEY)) 
		{
			$serializedAdditionalData = $attribute->getData('additional_data');
			
			if ($serializedAdditionalData) 
			{
				$additionalData = $this->serializer->unserialize($serializedAdditionalData);
				if ($additionalData !== null && is_array($additionalData)) 
				{
					foreach ($this->eavAttributeAdditionalDataKeys as $key) 
					{
						if (isset($additionalData[$key])) 
						{
							$attribute->setData($key, $additionalData[$key]);
						}
					}
				}
			}
		}
	}
}