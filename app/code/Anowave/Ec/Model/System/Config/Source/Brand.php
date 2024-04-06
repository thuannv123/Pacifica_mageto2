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

namespace Anowave\Ec\Model\System\Config\Source;

class Brand implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
	 */
	protected $attributeCollectionFactory;
	
	/**
	 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
	 */
	protected $attributeFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
	 * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
	 */
	public function __construct
	(
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
	)
	{
		/**
		 * Set collection factory 
		 * 
		 * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
		 */
		$this->attributeCollectionFactory = $attributeCollectionFactory;
		
		/**
		 * Set attribute factory
		 * 
		 * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory
		 */
		$this->attributeFactory = $attributeFactory;
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$collection = $this->attributeCollectionFactory->create();
		
		$options = 
		[
			[
				'value' => null,
				'label' => __('None')
			]
		];
		
		foreach ($collection as $entity)
		{
			$attribute = $this->attributeFactory->create()->load
			(
				$entity->getId()
			);
			
			if ($attribute->usesSource())
			{
				if ($attribute->getIsUserDefined() || in_array($attribute->getAttributeCode(), ['manufacturer','brand']))
				{
					$options[] = 
					[
						'value' => $attribute->getAttributeCode(),
						'label' => $attribute->getStoreLabel()
					];
				}
			}
			else 
			{
				if ($attribute->getIsUserDefined())
				{
					$options[] =
					[
						'value' => $attribute->getAttributeCode(),
						'label' => $attribute->getStoreLabel()
					];
				}
			}
		}

		return $options;
	}
}