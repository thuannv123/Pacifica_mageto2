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

namespace Anowave\Ec4\Plugin\Api\Measurement;

use Magento\Framework\App\Response\Http;

class Protocol
{
    
    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper = null;
    
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager = null;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $state
     */
    public function __construct
    (
        \Anowave\Ec4\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $state
    )
    {
        /**
         * Set helper
         *
         * @var \Anowave\Ec4\Helper\Data $_helper
         */
        $this->helper = $helper;
        
        /**
         * Set message manager
         *
         * @var \Magento\Framework\Message\ManagerInterface $_messageManager
         */
        $this->messageManager = $messageManager;
        
        /**
         * Set scope config 
         * 
         * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
         */
        $this->scopeConfig = $scopeConfig;
        
        /**
         * Set state
         * 
         * @var \Magento\Framework\App\State $state
         */
        $this->state = $state;
    }
    
    /**
     * After purchase 
     * 
     * @param \Anowave\Ec\Model\Api\Measurement\Protocol $interceptor
     * @param callable $proceed
     * @param \Magento\Sales\Model\Order $order
     * @param string $reverse
     * @return array
     */
    public function aroundPurchase(\Anowave\Ec\Model\Api\Measurement\Protocol $interceptor, callable $proceed, \Magento\Sales\Model\Order $order, $reverse = false)
    {
        $measurement_id         = $this->getOrderMeasurementId($order);
        $measurement_api_secret = $this->getOrderMeasurementApiSecret($order);
        
        /**
         * Get client id 
         * 
         * @var Ambiguous $cid
         */
        $cid = $interceptor->getCID();
        
        $payload = function(array $events = []) use ($measurement_id, $measurement_api_secret, $reverse, $cid)
        {
            return
            [
                'client_id' => $cid,
                'events'    => $events
            ];
        };

        if ($measurement_id && $measurement_api_secret)
        {
            $items = [];
            
            /**
             * Default start position
             *
             * @var int
             */
            $index = 1;
            
            /**
             * Loop products
             */
            foreach ($interceptor->getProducts($order) as $product)
            {
                $item = 
                [
                    'index'         =>          $index,
                    'item_id'       =>          @$product['id'],
                    'item_name'     =>          @$product['name'],
                    'item_brand'    => (string) @$product['brand'],
                    'price'         => (float)  @$product['price'],
                    'quantity'      => (int)    @$product['quantity']
                ];
                
                /**
                 * Check if reverse and reverse quantity
                 */
                
                if ($reverse)
                {
                    $item['quantity'] *= -1;
                    $item['price'] *= -1;
                }
                
                $categories = explode(chr(47), @$product['category']);
                
                if ($categories)
                {
                    $category = array_shift($categories);
                    
                    if ($category)
                    {
                        $item['item_category'] = $category;
                    }
                    
                    foreach ($categories as $index => $category)
                    {
                        $key = $index + 2;
                        
                        $item["item_category{$index}"] = $category;
                    }
                }
                
                
                $items[] = $item;
                
                $index++;
            }

            $data = $payload
            (
                [
                    [
                        'name' => 'purchase',
                        'params' => 
                        [
                            'currency'       => $this->helper->getBaseHelper()->getCurrency(),
                            'transaction_id' => $order->getIncrementId(),
                            'value'          => $this->helper->getBaseHelper()->getRevenue($order),
                            'shipping'	     => (float) $order->getShippingInclTax(),
                            'tax'	         => (float) $order->getTaxAmount(),
                            'affiliation'    => $this->helper->getBaseHelper()->escape
                            (
                                $order->getStore()->getName()
                            ),
                            'items' => $items,
                            'traffic_type' => $this->state->getAreaCode()
                        ]
                    ]
                ]
            );

            if ($reverse)
            {
                $data['events'][0]['params']['value']    *= -1;
                $data['events'][0]['params']['shipping'] *= -1;
                $data['events'][0]['params']['tax']      *= -1;
            }

            $analytics = curl_init("https://www.google-analytics.com/mp/collect?measurement_id={$measurement_id}&api_secret={$measurement_api_secret}");
            
            curl_setopt($analytics, CURLOPT_HEADER, 		0);
            curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($analytics, CURLOPT_POST, 			1);
            curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($analytics, CURLOPT_USERAGENT,		'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($analytics, CURLOPT_POSTFIELDS,     utf8_encode
            (
                json_encode($data)
            ));
            
            try
            {
                $response = curl_exec($analytics);
                
                if ($this->helper->getBaseHelper()->useDebugMode())
                {
                    $this->messageManager->addNoticeMessage(json_encode($data, JSON_PRETTY_PRINT));
                }

                if (!curl_error($analytics) && $response)
                {
                    return $interceptor;
                }
            }
            catch (\Exception $e) {}
        }

        return $proceed($order, $reverse);
    }
    
    /**
     * Get UA-ID
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function afterGetUA(\Anowave\Ec\Model\Api\Measurement\Protocol $interceptor, $result, \Magento\Sales\Model\Order $order = null)
    {
        if ($order && $order->getId())
        {
            return trim
            (
                (string) $this->scopeConfig->getValue($this->helper->getMeasurementIdConfig(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStore())
            );
        }
        
        return trim
        (
            $this->helper->getConfig
            (
                $this->helper->getMeasurementIdConfig()
            )
        );
    }
    
    /**
     * Get measurement ID from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    protected function getOrderMeasurementId(\Magento\Sales\Model\Order $order = null) : string
    {
        return trim
        (
            (string) $this->scopeConfig->getValue($this->helper->getMeasurementIdConfig(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStore())
        );
    }
    
    /**
     * Get measurement secret from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    protected function getOrderMeasurementApiSecret(\Magento\Sales\Model\Order $order = null) : string
    {
        return trim
        (
            (string) $this->scopeConfig->getValue('ec/api/google_gtm_ua4_measurement_api_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $order->getStore())
        );
    }
}