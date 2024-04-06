<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller\Payment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as Customer;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Marvelic\BeamCheckout\Helper\Curl;
use Marvelic\BeamCheckout\Helper\BeamCheckoutSalesOrder;

class Webhook extends \Marvelic\BeamCheckout\Controller\AbstractCheckoutRedirectAction implements HttpPostActionInterface, HttpGetActionInterface, CsrfAwareActionInterface
{
	/**
	 * @var RequestInterface
	 */
	protected $request;

	/**
	 * @var OrderFactory
	 */
	protected $_orderFactory;

	/**
	 * @var ResponseHttp
	 */
	protected $_responseHttp;

	/**
	 * @var JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var OrderRepositoryInterface
	 */
	protected $_orderRepository;

	/**
	 * @var Curl
	 */
	protected $_beamHelperCurl;

	/**
	 * @var BeamCheckoutSalesOrder
	 */
	protected $beamHelperSalesOrder;

	/**
	 * Webhook constructor.
	 * @param Context $context
	 * @param RequestInterface $request
	 * @param OrderFactory $orderFactory
	 * @param ResponseHttp $responseHttp
	 * @param JsonFactory $resultJsonFactory
	 * @param Session $checkoutSession
	 * @param Customer $customer
	 * @param BeamCheckoutRequest $beamCheckoutRequest
	 * @param ScopeConfigInterface $configSettings
	 * @param StoreManagerInterface $storeManager
	 * @param OrderRepositoryInterface $orderRepository
	 * @param Curl $beamHelperCurl
	 * @param BeamCheckoutSalesOrder $beamHelperSalesOrder
	 */
	public function __construct(
		Context $context,
		RequestInterface $request,
		OrderFactory $orderFactory,
		ResponseHttp $responseHttp,
		JsonFactory $resultJsonFactory,
		Session $checkoutSession,
		Customer $customer,
		BeamCheckoutRequest $beamCheckoutRequest,
		ScopeConfigInterface $configSettings,
		StoreManagerInterface $storeManager,
		OrderRepositoryInterface $orderRepository,
		Curl $beamHelperCurl,
		BeamCheckoutSalesOrder $beamHelperSalesOrder
	) {
		parent::__construct(
			$context,
			$checkoutSession,
			$orderFactory,
			$customer,
			$beamCheckoutRequest,
			$configSettings,
			$storeManager
		);
		$this->request = $request;
		$this->_orderFactory = $orderFactory;
		$this->_responseHttp = $responseHttp;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_orderRepository = $orderRepository;
		$this->_beamHelperCurl = $beamHelperCurl;
		$this->beamHelperSalesOrder = $beamHelperSalesOrder;
	}

	public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
	{
		return null;
	}

	public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
	{
		return true;
	}

	/* Write Log */
	public function log($data)
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_webhook.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info($data);
	}

	/* Execute webhook request */
	public function execute()
	{
		//Get request data•
		$params = $this->getRequest()->getContent();
		$data = json_decode($params, true);
		$this->log('----------Data from Webhook Beam Checkout return----------');
		$this->log(json_encode($data));
		$purchaseId = $data['purchaseId'];
		$this->log('----------Purchase Id for webhook return----------');
		$this->log($purchaseId);

		$orderId = $this->beamHelperSalesOrder->getOrderIdByPurchaseId($purchaseId);

		if ($data['state'] == 'complete') {
			$order = $this->_orderFactory->create()->load($orderId);
			// Set the API endpoint
			$storeId = $order->getStoreId();
			$endpoint = $this->getBeamCheckoutEndpoint($storeId);
			// Data reponse after calling to api
			$response = $this->_beamHelperCurl->sendCurlPurchaseId($endpoint, $purchaseId, $storeId);
			$jsonResult = json_decode($response, true);
			$this->log('----------Beam Checkout result get by Purchase Id----------');
			$this->log(json_encode($jsonResult));
			//Check state result from webhook
			if (isset($jsonResult['state']) && $jsonResult['state'] == 'complete') {
				// create invoice
				if ($order->canInvoice()) {
					$this->beamHelperSalesOrder->prepareInvoice($order);
					$paymentData = [
						'id' => $order->getId()
					];
					$this->beamHelperSalesOrder->createTransaction($order, $paymentData);
					//Set the complete status when payment is completed.
					$order->setState(Order::STATE_PROCESSING)
						->setStatus(Order::STATE_PROCESSING)
						->addStatusHistoryComment('Payment successfully processed by BeamCheckout.')
						->save();
					exit;
				}
			} else {
				exit;
				//this to check other payment status(failed, cancel, pending...)
			}
		} else {
			exit;
			//this to check other payment status(failed, cancel, pending...)
		}
	}
}
