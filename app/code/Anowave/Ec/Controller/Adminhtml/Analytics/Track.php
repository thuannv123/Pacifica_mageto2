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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class Track extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
	/**
	 * @var OrderManagementInterface
	 */
	protected $orderManagement;
	
	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $orderRepository;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;
	
	/**
	 * @var \Anowave\Ec\Model\TransactionFactory
	 */
	protected $transactionFactory;
	
	/**
	 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory
	 */
	protected $transactionCollectionFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param Context $context
	 * @param Filter $filter
	 * @param CollectionFactory $collectionFactory
	 * @param OrderManagementInterface $orderManagement
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Anowave\Ec\Model\TransactionFactory $transactionFactory
	 * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
	 */
	public function __construct
	(
		Context $context,
		Filter $filter,
		CollectionFactory $collectionFactory,
		OrderManagementInterface $orderManagement,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
	    \Anowave\Ec\Model\TransactionFactory $transactionFactory,
	    \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
	    
	) 
	{
		parent::__construct($context, $filter);
		
		/**
		 * Set collection factory 
		 * 
		 * @var CollectionFactory
		 */
		$this->collectionFactory = $collectionFactory;
		
		/**
		 * Set order management 
		 * 
		 * @var OrderManagementInterface
		 */
		$this->orderManagement = $orderManagement;
		
		/**
		 * Set Measurement Protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set order repository 
		 * 
		 * @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
		 */
		$this->orderRepository = $orderRepository;
		
		/**
		 * Set transaction factory 
		 * 
		 * @var \Anowave\Ec\Model\TransactionFactory $transactionFactory
		 */
		$this->transactionFactory = $transactionFactory;
		
		/**
		 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
		 */
		$this->transactionCollectionFactory = $transactionCollectionFactory;
	}
	
	/**
	 * Mass action
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction::massAction()
	 */
	protected function massAction(AbstractCollection $collection) 
	{
		/**
		 * Success log
		 * 
		 * @var array $success
		 */
		$success = [];
		
		/**
		 * Failure log 
		 * 
		 * @var array $failure
		 */
		$failure = [];

        foreach ($collection->getAllIds() as $id)
        {
        	if (false !== $this->protocol->purchaseById($id))
        	{
        	    $order = $this->orderRepository->get($id);
        	    
        		$success[] = __("Transaction {$order->getIncrementId()} successfully sent to Google Analytics");

        		/**
        		 * Save track information
        		 */
        		$this->track($order);
        	}
        	else 
        	{
        	    $failure[] = __("Failed to send transaction {$this->orderRepository->get($id)->getIncrementId()}  to Google Analytics");
        	    
        	    foreach ($this->protocol->getErrors() as $error)
        	    {
        	        $failure[] = $error;
        	    }
        	}
        }
        
        foreach ($success as $message)
        {
        	$this->messageManager->addSuccessMessage($message);
        }
        
        foreach ($failure as $message)
        {
        	$this->messageManager->addErrorMessage($message);
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $resultRedirect->setPath('sales/order/index');
        
        return $resultRedirect;
    }
    
    /**
     * Track order 
     * 
     * @param \Magento\Sales\Model\Order $order
     */
    protected function track(\Magento\Sales\Model\Order $order)
    {
        $collection = $this->transactionCollectionFactory->create()->addFieldToFilter('ec_order_id', $order->getId());
        
        
        if (!$collection->getSize())
        {
            $transaction = $this->transactionFactory->create();
            
            /**
             * Set data
             */
            $transaction->setEcOrderId($order->getId());
            
            if (isset($_COOKIE['_ga']))
            {
                $transaction->setEcCookieGa($_COOKIE['_ga']);
            }
            
            if (!(php_sapi_name() == 'cli'))
            {
                if (isset($_SERVER['HTTP_USER_AGENT']))
                {
                    $transaction->setEcUserAgent($_SERVER['HTTP_USER_AGENT']);
                }
            }
        }
        else 
        {
            $transaction = $collection->getFirstitem();
        }

        $transaction->setEcTrack(\Anowave\Ec\Helper\Constants::FLAG_TRACKED);
        $transaction->setEcOrderType(\Anowave\Ec\Helper\Constants::ORDER_TYPE_BACKEND);

        $transaction->save();
    }

    protected function _isAllowed() 
    {
        return true;
    }
}