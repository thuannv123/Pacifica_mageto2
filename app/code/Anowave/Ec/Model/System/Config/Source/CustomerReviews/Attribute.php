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

namespace Anowave\Ec\Model\System\Config\Source\CustomerReviews;

class Attribute implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magento\Framework\Api\SearchCriteriaBuilder
	 */
	protected $searchCriteriaBuilder;
	
	/**
	 * @var \Magento\Eav\Api\AttributeRepositoryInterface
	 */
	protected $attributeRepository;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
	 */
	public function __construct
	(
		\Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
		\Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
	)
	{
	    /**
	     * Set search builder 
	     * 
	     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
	     */
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		
		/**
		 * Set attribute repsitory 
		 * 
		 * @var \Anowave\Ec\Model\System\Config\Source\CustomerReviews\Attribute $attributeRepository
		 */
		$this->attributeRepository = $attributeRepository;
	}
	
	/**
	 * @return []
	 */
	public function toOptionArray()
	{
		$searchCriteria = $this->searchCriteriaBuilder->addFilter('is_user_defined', 1)->create();
		
		$attributeRepository = $this->attributeRepository->getList(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,$searchCriteria);

		foreach ($attributeRepository->getItems() as $item) 
		{
			$options[] = 
			[
				'value' => $item->getAttributeCode(),
				'label' => $item->getFrontendLabel()
			];
		}
		
		return $options;
	}
}