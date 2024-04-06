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

class OrderService
{
	/**
	 * @var \Magento\Framework\App\State
	 */
	protected $state = null;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	
	/**
	 * @var \Anowave\Ec\Model\TransactionFactory
	 */
	protected $transactionFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\State $state
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Anowave\Ec\Model\TransactionFactory $transactionFactory
	 */
	public function __construct
	(
		\Magento\Framework\App\State $state,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Framework\Message\ManagerInterface $messageManager,
	    \Anowave\Ec\Model\TransactionFactory $transactionFactory
	)
	{
		/**
		 * Set state
		 *
		 * @var \Magento\Framework\App\State $state
		 */
		$this->state = $state;
		
		/**
		 * Set protocol
		 *
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set helper
		 *
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Magento\Framework\Message\ManagerInterface $messageManager
		 */
		$this->messageManager = $messageManager;
		
		/**
		 * @var \Anowave\Ec\Model\TransactionFactory $transactionFactory
		 */
		$this->transactionFactory = $transactionFactory;
	}
	
	/**
	 * After place plugin 
	 * 
	 * @param \Magento\Sales\Model\Service\OrderService $context
	 * @param \Magento\Sales\Model\Order $order
	 * @return \Magento\Sales\Model\Order
	 */
	public function afterPlace(\Magento\Sales\Model\Service\OrderService $context, \Magento\Sales\Model\Order $order)
	{
	    if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML)
	    {
	        $transaction = $this->transactionFactory->create();
	        
	        $transaction->setEcOrderId($order->getId());
	        $transaction->setEcOrderType(\Anowave\Ec\Helper\Constants::ORDER_TYPE_BACKEND);
	        $transaction->setEcTrack(\Anowave\Ec\Helper\Constants::FLAG_PLACED);
	        
	        if (!(php_sapi_name() == 'cli'))
	        {
	            $transaction->setEcUserAgent($_SERVER['HTTP_USER_AGENT']);
	        }
	
    		if (1 === (int) $this->helper->getConfig('ec/gmp/use_measurement_protocol'))
    		{
    			if (false !== $data = $this->protocol->purchaseById
    			(
    				$order->getId()
    			))
    			{
    				if (!$data->getErrors())
    				{
    				    $transaction->setEcTrack(\Anowave\Ec\Helper\Constants::FLAG_TRACKED);
    				    
    					$this->messageManager->addNoticeMessage("Transaction data {$order->getIncrementId()} sent successfully to Google Analytics {$data->getUA($order)}");
    				}
    				else 
    				{
    					foreach ($data->getErrors() as $error)
    					{
    						$this->messageManager->addErrorMessage($error);
    					}
    				}
    			}
    		}
    		
    		try 
    		{
    		    $transaction->save();
    		}
    		catch (\Exception $e)
    		{
    		    $this->messageManager->addErrorMessage($e->getMessage());
    		}
	    }
		
		return $order;
	}
}