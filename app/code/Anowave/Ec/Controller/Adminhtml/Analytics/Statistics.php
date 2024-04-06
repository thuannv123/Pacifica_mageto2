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

namespace Anowave\Ec\Controller\Adminhtml\Analytics;

use Magento\Backend\App\Action;

class Statistics extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * Transaction collection factory
     * @var unknown
     */
    protected $transactionCollectionFactory;
    
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     */
    public function __construct
    (
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
    )
    {
        /**
         * Set result JSON factory 
         * 
         * @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
         */
        $this->resultJsonFactory = $resultJsonFactory;
        
        /**
         * Set transaction collection factory 
         * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
         */
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        
        parent::__construct($context);
    }
    
    /**
     * Hello test controller page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        
        $data =
        [
            'placed'            => $this->getPlaced(),
            'placed_admin'      => $this->getPlacedAdmin(),
            'tracked'           => $this->getTracked(),
            'tracked_admin'     => $this->getTrackedAdmin(),
            'total'             => $this->getTotal()
        ];
        
        $result->setData($data);
        
        return $result;
    }
    
    /**
     * Get number of placed but not tracked orders
     * 
     * @return int
     */
    public function getPlaced() : int
    {
        return $this->transactionCollectionFactory->create()->addFieldToFilter('ec_track',  \Anowave\Ec\Helper\Constants::FLAG_PLACED)->addFieldToFilter('ec_order_type',\Anowave\Ec\Helper\Constants::ORDER_TYPE_FRONTEND)->getSize();
    }
    
    /**
     * Get number of placed but not tracked admin orders
     * 
     * @return int
     */
    public function getPlacedAdmin() : int
    {
        return $this->transactionCollectionFactory->create()->addFieldToFilter('ec_track',  \Anowave\Ec\Helper\Constants::FLAG_PLACED)->addFieldToFilter('ec_order_type',\Anowave\Ec\Helper\Constants::ORDER_TYPE_BACKEND)->getSize();
    }
    
    /**
     * Get number of placed and tracked orders 
     * 
     * @return int
     */
    public function getTracked() : int
    {
        return $this->transactionCollectionFactory->create()->addFieldToFilter('ec_track', \Anowave\Ec\Helper\Constants::FLAG_TRACKED)->addFieldToFilter('ec_order_type',\Anowave\Ec\Helper\Constants::ORDER_TYPE_FRONTEND)->getSize();
    }
    
    /**
     * Get number of placed and tracked admin orders
     * 
     * @return int
     */
    public function getTrackedAdmin() : int
    {
        return $this->transactionCollectionFactory->create()->addFieldToFilter('ec_track', \Anowave\Ec\Helper\Constants::FLAG_TRACKED)->addFieldToFilter('ec_order_type',\Anowave\Ec\Helper\Constants::ORDER_TYPE_BACKEND)->getSize();
    }
    
    /**
     * Get total number of transactions placed
     * 
     * @return int
     */
    public function getTotal() : int
    {
        return $this->transactionCollectionFactory->create()->getSize();
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}