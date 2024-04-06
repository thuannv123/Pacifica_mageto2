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

namespace Anowave\Ec\Controller\Index;

class Events extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $helper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Anowave\Ec\Helper\Data $helper
     */
    public function __construct
    (
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Anowave\Ec\Helper\Data $helper
    )
    {
        parent::__construct($context);
        
        /**
         * Set response type factory
         *
         * @var \Magento\Framework\Controller\Result\JsonFactory
         */
        $this->resultJsonFactory = $resultJsonFactory;
        
        /**
         * Set helper
         *
         * @var \Anowave\Ec\Helper\Data
         */
        $this->helper = $helper;
    }
    
    /**
     * Execute controller
     *
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $events = [];
        
        foreach(
        [
            $this->helper->getContactEvent(), 
            $this->helper->getNewsletterEvent(), 
            $this->helper->getCartUpdateEvent(), 
            $this->helper->getCustomerRegisterEvent(),
            $this->helper->getCustomerLoginEvent()
        ] 
            as $event)
        {
            if (false !== $event)
            {
                $events[] = json_decode($event);
            }
        }

        return $this->resultJsonFactory->create()->setData
        (
            [
                'events' => $events
            ]
        );
    }
}