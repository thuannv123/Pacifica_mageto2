<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Plugin;

class Helper
{
    /**
     * After get cart update event 
     * 
     * @param \Anowave\Ec\Helper\Data $helper
     * @param string $result
     * @return string
     */
    public function afterGetCartUpdateEvent(\Anowave\Ec\Helper\Data $helper, $result)
    {
        if ($result)
        {
            $result = json_decode($result, true);
            
            if (array_key_exists('event', $result))
            {
                switch($result['event'])
                {
                    case 'addToCart':
                        
                        $result['event'] = 'add_to_cart';
                        
                        $result['ecommerce']['items'] = $this->remap($result['ecommerce']['add']['products']);
                        
                        break;
                        
                    case 'removeFromCart':
                        
                        $result['event'] = 'remove_from_cart';
                        
                        $result['ecommerce']['items'] = $this->remap($result['ecommerce']['remove']['products']);
                        
                        break;
                }
            }
            
            $result = json_encode($result);
        }
        
        
        return $result;
    }
    
    /**
     * Remap products array to GA4 array
     * 
     * @param array $products
     * @return array
     */
    protected function remap(array $products = []) : array
    {
        /**
         * Items array 
         * 
         * @var array $items
         */
        $items = [];
        
        /**
         * Starting position 
         * 
         * @var integer $index
         */
        $index = 1;
        
        foreach ($products as $product)
        {
            $item =
            [
                'index'         =>          $index++,
                'item_id'       =>          @$product['id'],
                'item_name'     =>          @$product['name'],
                'item_brand'    => (string) @$product['brand'],
                'price'         => (float)  @$product['price'],
                'quantity'      => (int)    @$product['quantity']
            ];
            
            $items[] = $item;
        }
        
        return $items;
        
    }
}