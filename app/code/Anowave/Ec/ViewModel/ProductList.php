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

use Magento\Catalog\Model\Product;

class ProductList implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const EVENT = 'impression';
    
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
    public function getImpressionPayload(\Magento\Catalog\Block\Product\ListProduct $block) : string
    {
        try 
        {
            $category = $this->getCurrentCategory(); 
            
            $payload = 
            [
                'ecommerce' => 
                [
                    'currencyCode' => $this->helper->getCurrency(),
                    'actionField' => 
                    [
                        'list' => $this->helper->getCategoryList($category)
                    ]
                ],
                'event' => static::EVENT
            ];
        }
        catch (\Exception $e)
        {
            $payload = [];
        }
        
        return $this->helper->getJsonHelper()->encode($payload);
    }
    
    /**
     * Get Search impression payload 
     * 
     * @param \Magento\Catalog\Block\Product\ListProduct $block
     * @return string
     */
    public function getSearchImpressionPayload(\Magento\Catalog\Block\Product\ListProduct $block) : string
    {
        try
        {
            $payload =
            [
                'ecommerce' =>
                [
                    'currencyCode' => $this->helper->getCurrency(),
                    'actionField' =>
                    [
                        'list' => __('Search results')
                    ]
                ],
                'event' => static::EVENT
            ];
        }
        catch (\Exception $e)
        {
            $payload = [];
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
