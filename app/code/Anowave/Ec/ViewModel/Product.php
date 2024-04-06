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

declare(strict_types=1);

namespace Anowave\Ec\ViewModel;

class Product implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const EVENT = 'detail';
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor 
     * 
     * @param \Anowave\Ec\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct
    (
        \Anowave\Ec\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    )
    {
        /**
         * Set helper
         * 
         * @var \Anowave\Ec\Helper\Data $helper
         */
        $this->helper = $helper;
        
        /**
         * Set registry 
         * 
         * @var \Magento\Framework\Registry $registry
         */
        $this->registry = $registry;
    }
    
    /**
     * Get impression payload default parameters 
     * 
     * @param \Magento\Catalog\Block\Product\ListProduct $block
     * @return string
     */
    public function getDetailPayload(\Magento\Catalog\Block\Product\View $block) : string
    {
        if (true)
        {
            return $this->helper->getJsonHelper()->encode(['payload' => 'detail']);
        }
        
        /**
         * @todo Implementation of async product detail view. Upcoming in future releases.
         */
        if (!$this->helper->isDeferEnabled())
        {
            return $this->helper->getJsonHelper()->encode([]);
        }
        
        try 
        {
            /**
             * Get product 
             * 
             * @var \Magento\Catalog\Model\Product $product
             */
            $product = $block->getProduct();
            
            /**
             * Get category 
             * 
             * @var \Magento\Catalog\Model\Category $category
             */
            $category = $this->getCurrentCategory(); 
            
            /**
             * Pick category from layer resolver
             */
            if (!$category)
            {
                $category = $this->helper->getLayerResolver()->get()->getCurrentCategory();
            }
            
            
            /**
             * Check if category isn't root category
             */
            if ($category)
            {
                if ((int) $category->getId() === (int) $this->helper->getStoreRootDefaultCategoryId())
                {
                    $category = null;
                }
            }
            
            /**
             * Pick category from list of categories product is assigned to
             */
            if (!$category)
            {
                $categories = $this->helper->getCurrentStoreProductCategories($product);
                
                if (!$categories && $product->getCategoryIds())
                {
                    $categories = $product->getCategoryIds();
                }
                
                /**
                 * Cases when product does not exist in any category
                 */
                if (!$categories)
                {
                    $categories[] = $this->helper->getStoreRootDefaultCategoryId();
                }
                
                /**
                 * Load last category
                 */
                $category = $this->helper->getCategoryRepository()->get
                (
                    end($categories)
                );
            }
            
            /**
             * Create transport object
             *
             * @var \Magento\Framework\DataObject $transport
             */
            $transport = new \Magento\Framework\DataObject
            (
                [
                    'attributes' => $this->helper->getAttributes()->getAttributes(),
                    'product'    => $product
                ]
            );
            
            /**
             * Notify others
             */
            $this->helper->getEventManager()->dispatch('ec_get_viewmodel_detail_attributes', ['transport' => $transport]);
            
            /**
             * Get response
             */
            $attributes = $transport->getAttributes();

            $payload = 
            [
                'ecommerce' => 
                [
                    'currencyCode' => $this->helper->getCurrency(),
                    'detail' => 
                    [
                        'products' => 
                        [
                            array_merge
                            (
                                [
                                    
                                    'id' 								         => $this->helper->getIdentifier($product),
                                    'name' 								         => $product->getName(),
                                    'price' 							         => $this->helper->getPrice($product),
                                    'brand'								         => $this->helper->getBrand($product),
                                    'category'							         => $this->helper->getCategory($category),
                                    $this->helper->getStockDimensionIndex(true)  => $this->helper->getStock($product),
                                    'quantity' 							         => 1
                                ], 
                                $attributes
                            )
                        ]
                    ]
                ],
                'event' => static::EVENT
            ];
        }
        catch (\Exception $e)
        {
            $payload = 
            [
                'error' => $e->getMessage() 
            ];   
        }
        
        return $this->helper->getJsonHelper()->encode($payload);
    }

   
    /**
     * Get current category 
     * 
     * @return mixed|NULL
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
    
    /**
     * Get current category
     *
     * @return mixed|NULL
     */
    public function getCurrentCategoryName() : string
    {
        return $this->helper->getJsonHelper()->encode
        (
            $this->getCurrentCategory()->getName()
        );
    }
}
