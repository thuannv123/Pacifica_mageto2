<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking GA4
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
 * @package 	Anowave_Ec4
 * @copyright 	Copyright (c) 2023 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec4\Observer\Compare;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Attributes implements ObserverInterface
{
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec\Helper\Data $helper
     */
    public function __construct
    (
        \Anowave\Ec\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }
    
    /**
     * Execute 
     * 
     * @param EventObserver $observer
     * @return boolean
     */
	public function execute(EventObserver $observer)
	{
	    $category_ids = $this->helper->getCurrentStoreProductCategories($observer->getTransport()->getProduct());

	    if (!$category_ids)
	    {
	        $category_ids[] = (int) $this->helper->getStoreRootDefaultCategoryId();
	    }
	    
	    $category = $this->helper->getCategoryRepository()->get
	    (
	        end($category_ids)
        );

	    $category_array = explode(chr(47),$this->helper->getCategory($category));
	    
	    $item = 
	    [
	        'item_id'   => $observer->getTransport()->getProduct()->getSku(),
	        'item_name' => $observer->getTransport()->getProduct()->getName(),
	        'price'     => $this->helper->getPrice
	        (
	            $observer->getTransport()->getProduct()
            ),
	        'quantity' => 1
	    ];
	    
	    if ($category_array)
	    {
	        $item['item_category'] = array_shift($category_array);
	        
	        $index = 2;
	        
    	    foreach ($category_array as $category)
    	    {
    	        $item["item_category{$index}"] = $category;
    	        
    	        $index++;
    	    }
	    }
	    
	    $observer->getTransport()->setAttributes(
	    [
	        'items' => [$item]
	    ]);
	    
	    return true;
	}
}
