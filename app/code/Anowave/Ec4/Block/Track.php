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

namespace Anowave\Ec4\Block;

class Track extends \Anowave\Ec\Block\Track
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @var \Anowave\Ec\Helper\Affiliation $affiliation
     */
    protected $affiliation;

    /**
	 * @var \Magento\Framework\Filesystem\Driver\File
	 */
	protected $driverFile;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Anowave\Ec\Helper\Data $helper
     * @param \Anowave\Ec\Helper\Datalayer $dataLayer
     * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Anowave\Ec\Helper\Affiliation $affiliation
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Anowave\Ec\Helper\Data $helper,
        \Anowave\Ec\Helper\Datalayer $dataLayer,
        \Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Registry $registry,
        \Anowave\Ec\Helper\Affiliation $affiliation,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
	    \Psr\Log\LoggerInterface $logger,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->affiliation = $affiliation;
        
        parent::__construct($context, $helper, $dataLayer, $protocol, $currencyFactory, $driverFile, $logger);
    }
    
    public function getViewCart() : string
    {
        $checkout = $this->getHelper()->getCheckoutProducts($this, $this->registry);
        
        $payload = 
        [
            'event' => 'view_cart',
            'ecommerce' => 
            [
                'currency' => $this->getHelper()->getCurrency(),
                'value' => 0,
                'items' => []
            ]
        ];
        
        /**
         * Cart value
         * 
         * @var integer $value
         */
        $value = 0;
        
        /**
         * Item array 
         * 
         * @var array $items
         */
        $items = [];
        
        /**
         * Default starting position
         * 
         * @var integer $index
         */
        $index = 1;
        
        foreach ($checkout->products as $product)
        {
            $item = 
            [
                'item_id'           => $product['id'],
                'item_name'         => $product['name'],
                'item_list_id'      => $product['list'],
                'item_list_name'    => $product['list'],
                'price'             => $product['price'],
                'quantity'          => $product['quantity'],
                'currency'          => $this->getHelper()->getCurrency(),
                'item_brand'        => $product['brand'],
                'index'             => $index++
            ];
            
            if ($this->affiliation->isEnabled())
            {
                $item['affiliation'] = $this->affiliation->getAffiliation();
            }
            
            if (isset($product['variant']))
            {
                $item['item_variant'] = $product['variant'];
            }
            
            foreach ($this->getItemCategories($product['category']) as $key => $category)
            {
                $item[$key] = $category;
            }
            
            $items[] = $item;
            
            $value += ((float) $item['price'] * (float) $product['quantity']);
        }
        
        /**
         * Set value
         */
        $payload['ecommerce']['value'] = $value;
        
        /**
         * Set items
         */
        $payload['ecommerce']['items'] = $items;
        
        /**
         * Create transport object
         *
         * @var \Magento\Framework\DataObject $transport
         */
        $transport = new \Magento\Framework\DataObject
        (
            [
                'payload' => $payload
            ]
        );
        
        /**
         * Notify others for schema
         */
        $this->getHelper()->getEventManager()->dispatch('ec_view_cart', ['transport' => $transport]);
        
        /**
         * Update payload
         */
        $payload = $transport->getPayload();
        
        return $this->getHelper()->getJsonHelper()->encode($payload);
    }
    
    /**
     * Get cateiory map 
     * 
     * @param string $category
     * @return array
     */
    protected function getItemCategories($category) : array
    {
        $map = [];
        
        $categories = explode(chr(47), $category);
        
        $map['item_category'] = array_shift($categories);
        
        if ($categories)
        {
            $index = 1;
            
            foreach ($categories as $category)
            {
                $map['item_category' . (++$index)] = $category;
            }
        }
        
        return $map;
    }
    
}