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

namespace Anowave\Ec\Block;

class Result
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $_helper = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Dom
	 */
	protected $_helperDom = null;
	
	/**
	 * @var \Anowave\Ec\Helper\Attributes
	 */
	protected $attributes;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryRepository
	 */
	protected $categoryRepository;
	
	/**
	 * Category map 
	 * 
	 * @var array
	 */
	protected $categories = [];
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Registry $registry
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Anowave\Ec\Helper\Dom $helperDom
	 * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
	 */
	public function __construct
	(
		\Magento\Framework\Registry $registry,
		\Anowave\Ec\Helper\Data $helper,
		\Anowave\Ec\Helper\Dom $helperDom,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository
	)
	{
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $_helper
		 */
		$this->_helper = $helper;
		
		/**
		 * Set DOM helper 
		 * 
		 * @var \Anowave\Ec\Helper\Dom $_helperDom
		 */
		$this->_helperDom = $helperDom;
		
		/**
		 * Set core registry 
		 * 
		 * @var \Magento\Framework\Registry $_coreRegistry
		 */
		$this->_coreRegistry = $registry;
		
		/**
		 * Set category repository 
		 * 
		 * @var \Anowave\Ec\Block\Result $categoryRepository
		 */
		$this->categoryRepository = $categoryRepository;
	}
	
	/**
	 * Augment search results block 
	 * 
	 * @param \Magento\CatalogSearch\Block\Result $block
	 * @param string $content
	 * @return string
	 */
	public function afterGetProductListHtml(\Magento\CatalogSearch\Block\Result $block, $content)
	{
		/**
		 * Retrieve list of impression product(s)
		 * 
		 * @var array
		 */
		$products = [];
		
		foreach ($block->getListBlock()->getLoadedProductCollection() as $product)
		{
			$products[] = $product;
		}
		
		/**
		 * Append tracking
		 */
		$doc = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		$dom = new \Anowave\Ec\Model\Dom('1.0','utf-8');
		
		@$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

		$query = new \DOMXPath($dom);
		
		/**
		 * Default starting position
		 *
		 * @var integer $position
		 */
		$position = 1;
		
		/**
		 * Adjust position depending on search pagination
		 */
		try 
		{
		    $current = (int) $block->getListBlock()->getToolbarBlock()->getCollection()->getCurPage();
		    
		    if (1 === $current)
		    {
		        $position = 0;
		    }
		    else 
		    {
		        $position = ($block->getListBlock()->getToolbarBlock()->getLimit()  * $current) - $block->getListBlock()->getToolbarBlock()->getLimit();
		    }
		}
		catch (\Exception $e){}

		/**
		 * Load root category id 
		 * 
		 * @var int $root_category_id
		 */
		$root_category_id = $this->_helper->getStoreRootDefaultCategoryId();
		
		/**
		 * Loop products and bind data-attributes
		 */
		foreach ($query->query($this->_helper->getListSelector()) as $key => $element)
		{
			if (isset($products[$key]))
			{				
				$categories = $this->_helper->getCurrentStoreProductCategories($products[$key]);

				/**
				 * Cases when product does not exist in any category
				 */
				if (!$categories)
				{
					$categories[] = $root_category_id;
				}
				
				try 
				{
    				/**
    				 * Load last category
    				 */
    				$category = $this->getCategory
    				(
    				    end($categories)
    			    );
				}
				catch (\Exception $e)
				{
				    $category = $this->getCategory($root_category_id);
				}
				
				/**
				 * Add data-* attributes used for tracking dynamic values
				 */
				foreach ($query->query($this->_helper->getListClickSelector(), $element) as $a)
				{
					$click = $a->getAttribute('onclick');
						
					$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
					$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
					$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
					$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($this->_helper->getCategory($category)));
					$a->setAttribute('data-list',		__('Search results'));
					$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
					$a->setAttribute('data-quantity', 	1);
					$a->setAttribute('data-click',		$click);
					$a->setAttribute('data-store',		$this->_helper->getStoreName());
					$a->setAttribute('data-event',		'productClick');
					$a->setAttribute('data-position',   ++$position);
					
					$a->setAttribute("data-{$this->_helper->getStockDimensionIndex(true)}", $this->_helper->getStock($products[$key]));
					
					/**
					 * Create transport object
					 *
					 * @var \Magento\Framework\DataObject $transport
					 */
					$transport = new \Magento\Framework\DataObject
					(
						[
							'attributes' => $this->_helper->getSearchAttributes()
						]
					);
					
					/**
					 * Notify others
					 */
					$this->_helper->getEventManager()->dispatch('ec_get_search_click_attributes', ['transport' => $transport]);
					
					/**
					 * Get response
					 */
					$attributes = $transport->getAttributes();
					
					$a->setAttribute('data-attributes', $this->_helper->getJsonHelper()->encode($attributes));
				}
				
				/**
				 * Direct "Add to cart" from search results
				 */
				if ('' !== $selector = $this->_helper->getCartCategorySelector())
				{
					foreach (@$query->query($selector, $element) as $a)
					{
						$click = $a->getAttribute('onclick');
					
						$a->setAttribute('data-id', 		$this->_helper->escapeDataArgument($products[$key]->getSku()));
						$a->setAttribute('data-name', 		$this->_helper->escapeDataArgument($products[$key]->getName()));
						$a->setAttribute('data-price', 		$this->_helper->escapeDataArgument($this->_helper->getPrice($products[$key])));
						$a->setAttribute('data-category',   $this->_helper->escapeDataArgument($this->_helper->getCategory($category)));
						$a->setAttribute('data-list',		__('Search results'));
						$a->setAttribute('data-brand',		$this->_helper->escapeDataArgument($this->_helper->getBrand($products[$key])));
						$a->setAttribute('data-quantity', 	1);
						$a->setAttribute('data-click',		$click);
						$a->setAttribute('data-store',		$this->_helper->getStoreName());
						$a->setAttribute('data-event',		'addToCart');
						$a->setAttribute('data-position',   $position);
					}
				}
			}
		}
		
		return $this->_helperDom->getDOMContent($dom, $doc);
	}
	
	/**
	 * Get category 
	 * 
	 * @param int $id
	 */
	protected function getCategory(int $id = 0)
	{
	    if (isset($this->categories[$id]))
	    {
	        return $this->categories[$id];
	    }
	    
	    /**
	     * Load category 
	     * 
	     * @var \Magento\Catalog\Model\Category $category
	     */
	    $category = $this->categoryRepository->get($id);
	    
	    /**
	     * Push category to category map
	     */
	    $this->categories[$id] = $category;
	    
	    return $category;
	}
}