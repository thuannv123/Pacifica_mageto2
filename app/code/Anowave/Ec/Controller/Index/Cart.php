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

class Cart extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var \Magento\Checkout\Model\Session\Proxy 
	 */
	protected $proxy;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	private $request;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Magento\Checkout\Model\Session\Proxy $proxy
	 * @param \Anowave\Ec\Helper\Data $helper
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
	    \Magento\Checkout\Model\Session\Proxy $proxy,
	    \Anowave\Ec\Helper\Data $helper
	)
	{
		parent::__construct($context);
		
		$this->request = $context->getRequest();
		
		/**
		 * Set response type factory 
		 * 
		 * @var \Magento\Framework\Controller\Result\JsonFactory
		 */
		$this->resultJsonFactory = $resultJsonFactory;
		
		/**
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set cart proxy 
		 * 
		 * @var \Magento\Checkout\Model\Session\Proxy  $proxy
		 */
		$this->proxy = $proxy;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Ec\Helper\Data $helper
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
		$result = $this->resultJsonFactory->create();
		
		/**
		 * Default response 
		 * 
		 * @var array $response
		 */
		$response = 
		[
			'event'           => 'summary',
		    'eventTrigger'    => $this->request->getParam('event'),
		    'items'           => []
		];
		
		/**
		 * Get parameters
		 * 
		 * @var array $params
		 */
		if ($this->getRequest()->isXmlHttpRequest())
		{
		    $items = [];
		    
		    $current = $this->proxy->getQuote()->getItems();
		    
		    if ($current)
		    {
    			foreach ($current as $item)
    			{
    			    $items[] = 
    			    [
    			        'id'         => $this->helper->getIdentifier($item->getProduct()),
    			        'price'      => $this->helper->getPrice($item->getProduct()),
    			        'name'       => $item->getProduct()->getName(),
    			        'quantity'   => $item->getQty()
    			    ];
    			}
    			
    			$response['items'] = $items;
		    }
		}
		else 
		{
			$response['error'] = __('CSRF');
		}

		return $result->setData($response);
	}
}