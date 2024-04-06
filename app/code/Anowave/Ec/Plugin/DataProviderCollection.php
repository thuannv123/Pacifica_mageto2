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

namespace Anowave\Ec\Plugin;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;

class DataProviderCollection
{
    /**
     * After get report 
     * 
     * @param CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @param strng $requestName
     * @return \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    public function afterGetReport(CollectionFactory $subject, $collection, $requestName) 
    {
        if ($requestName == 'sales_order_grid_data_source') 
        { 
            $collection->getSelect()->joinLeft
            (
                ['ec' => $collection->getTable('ae_ec')], 'main_table.entity_id = ec.ec_order_id',['ec_cookie_ga','ec_track']
                
            );
        }
        
        return $collection;
    } 
}