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

namespace Anowave\Ec\Observer\Facebook;

use Magento\Framework\Event\Observer as EventObserver;
use Anowave\Ec\Observer\Facebook;

class Purchase extends Facebook
{
    public function execute(EventObserver $observer)
    {
        $response = $observer->getTransport()->getResponse();

        /**
         * Get order
         * 
         * @var \Magento\Sales\Model\Order $order
         */
        $order = $observer->getTransport()->getOrder();
        
        /**
         * Content ids map
         * 
         * @var array $content_ids
         */
        $content_ids = [];
        
        foreach ($response['ecommerce']['purchase']['products'] as $product)
        {
            $content_ids[] = $product['id'];
        }
        
        $this->helper->getFacebookConversionsApi()->trackPurchase($order, $content_ids, $response['ecommerce']['currencyCode'], $response['ecommerce']['purchase']['actionField']['revenue']);
    }
}