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

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;


class Datalayer extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface, HttpPostActionInterface, HttpGetActionInterface
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
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 */
	public function __construct
	(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol
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
		 * Set protocol 
		 * 
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
		 */
		$this->protocol = $protocol;
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
			'success' => false
		];
		
		/**
		 * Get parameters
		 * 
		 * @var array $params
		 */
		$params = $this->getRequest()->getParams();
		
		if ($this->getRequest()->isXmlHttpRequest())
		{
			if ($params)
			{
				if($this->protocol->fallback($params))
				{
					return $result->setData(['success' => true]);
				}
			}
			else 
			{
				$response['error'] = __('Missing ecommerce parameters');
			}
		}
		else 
		{
			$response['error'] = __('CSRF');
		}

		return $result->setData($response);
	}
	
	/**
	 * Create CSRF Exception
	 *
	 * {@inheritDoc}
	 * @see \Magento\Framework\App\CsrfAwareActionInterface::createCsrfValidationException()
	 */
	public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
	{
	    return null;
	}
	
	/**
	 * Validate for CSRF
	 *
	 * {@inheritDoc}
	 * @see \Magento\Framework\App\CsrfAwareActionInterface::validateForCsrf()
	 */
	public function validateForCsrf(RequestInterface $request): ? bool
	{
	    return true;
	}
}