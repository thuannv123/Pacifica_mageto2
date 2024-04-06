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

namespace Anowave\Ec\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Success implements ObserverInterface
{
	/**
	 * @var \Magento\Framework\View\LayoutInterface
	 */
	protected $_layout;
	
	/**
	 * Set affiliation
	 * 
	 * @var \Anowave\Ec\Helper\Affiliation
	 */
	protected $affiliation;
	
	/**
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $eventManager;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Anowave\Ec\Model\TransactionFactory
	 */
	protected $transactionFactory;
	
	/**
	 * Set transaction collection factory
	 * 
	 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory
	 */
	protected $transactionCollectionFactory;
	
	/**
	 * @var \Magento\Framework\Stdlib\CookieManagerInterface
	 */
	protected $cookieManager;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Anowave\Ec\Helper\Affiliation $affiliation
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Anowave\Ec\Helper\Data $helper
     * @param \Anowave\Ec\Model\TransactionFactory $transactionFactory
     * @param \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
     */
	public function __construct
	(
		\Magento\Framework\View\LayoutInterface $layout,
		\Anowave\Ec\Helper\Affiliation $affiliation,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Anowave\Ec\Helper\Data $helper,
	    \Anowave\Ec\Model\TransactionFactory $transactionFactory,
	    \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
	    \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
	    \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	) 
	{
		/**
		 * Set layout 
		 * 
		 * @var \Anowave\Ec\Observer\Success $_layout
		 */
		$this->_layout = $layout;
		
		/**
		 * Set affiliation
		 * 
		 * @var \Anowave\Ec\Helper\Affiliation $affiliation
		 */
		$this->affiliation = $affiliation;
		
		/**
		 * Set event manager 
		 * 
		 * @var \Anowave\Ec\Observer\Success $eventManager
		 */
		$this->eventManager = $eventManager;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set transaction factory 
		 * 
		 * @var \Anowave\Ec\Model\TransactionFactory $factory
		 */
		$this->transactionFactory = $transactionFactory;
		
		/**
		 * @var \Anowave\Ec\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
		 */
		$this->transactionCollectionFactory = $transactionCollectionFactory;
		
		/**
		 * Set cookie manager 
		 * 
		 * @var \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
		 */
		$this->cookieManager = $cookieManager;
		
		/**
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Observer\Success $protocol
		 */
		$this->protocol = $protocol;
	}
	
	/**
	 * Add order information into GA block to render on checkout success pages
	 *
	 * @param EventObserver $observer
	 * @return void
	 */
	public function execute(EventObserver $observer)
	{
		$order_ids = $observer->getEvent()->getOrderIds();
		
		if (empty($order_ids) || !is_array($order_ids)) 
		{
			return;
		}
		
		$block = $this->_layout->getBlock('ec_purchase');
		
		if ($block) 
		{
		    try 
		    {
    		    foreach ($order_ids as $id)
    		    {
    		        $collection = $this->transactionCollectionFactory->create()->addFieldToFilter('ec_order_id', (int) $id);
    		        
    		        if ($collection->getSize())
    		        {
    		            $transaction = $this->transactionFactory->create()->load($collection->getFirstItem()->getEcId());
    	
    		            /**
    		             * Set transaction as tracked
    		             */
    		            $transaction->setEcTrack(\Anowave\Ec\Helper\Constants::FLAG_TRACKED);
    		            
    		            /**
    		             * Get _ga cookie
    		             * 
    		             * @var string $ga
    		             */
    		            $ga = $this->get_gaCookie();
    		            
    		            if ($ga)
    		            {
    		                $transaction->setEcCookieGa($ga);
    		            }
    		            
    		            $transaction->save();
    		        }
    		    }
		    }
		    catch (\Exception $e){}
		    
		    if ($this->helper->useMeasurementProtocolOnly())
		    {
		        /**
		         * Track serve-side
		         */
		        $this->trackServerSide($order_ids);
		    }
		    else 
		    {
    			if (!$this->helper->isBetaMode())
    			{
    				$block->setOrderIds($order_ids);
    			}
    			else 
    			{
    				/**
    				 * Get orders collection
    				 *
    				 * @var array $orders
    				 */
    				$orders = $this->helper->getOrdersCollection($order_ids);
    				
    				if ($orders)
    				{
    					$block->setOrderIds($order_ids);
    				}
    			}
		    }
		}
	}
	
	/**
	 * Track transaction using server-side call (Measurement Protocol)
	 * 
	 * @param array $order_ids
	 */
	public function trackServerSide(array $order_ids = [])
	{
	    /**
	     * Get _ga cookie 
	     * 
	     * @var string $ga
	     */
	    $ga = $this->get_gaCookie();
	    
	    if ($ga)
	    {
	        $cid = substr($ga, 6);
	        
	        if ($cid)
	        {
	            /**
	             * Set client ID
	             */
	            $this->protocol->setCID($cid);
	            
	            /**
	             * Track each order separately
	             */
	            foreach ($order_ids as $id)
	            {
	                $this->protocol->purchaseById($id);
	            }
	        }
	    }
	    
	    return true;
	}
	
	/**
	 * Get Google Analytics (_ga) cookie 
	 * 
	 * @return string
	 */
	private function get_gaCookie() : string
	{
	    return (string) $this->cookieManager->getCookie('_ga');
	}
}
