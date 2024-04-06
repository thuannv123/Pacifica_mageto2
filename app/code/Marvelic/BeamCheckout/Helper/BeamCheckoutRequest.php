<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Marvelic\BeamCheckout\Helper\Curl;
use Magento\Framework\App\Helper\Context;
use Marvelic\BeamCheckout\Model\BeamCheckoutPurchaseFactory;

class BeamCheckoutRequest extends AbstractHelper
{
	/**
	 * Payment account setting
	 *
	 * @var string
	 */
	const XML_PATH_BEAMCHECKOUT_SETTING = 'payment/beamcheckout/account_setting';

	/** 
	 * @var \Magento\Store\Model\StoreManagerInterface 
	 */
	protected $objStoreManagerInterface;

	/** 
	 * @var \Marvelic\BeamCheckout\Helper\Curl
	 */
	protected $_beamHelperCurl;

	/** 
	 * @var string 
	 */
	protected $_purchaseId;

	/** 
	 * @var \Magento\Framework\HTTP\PhpEnvironment\Response 
	 */
	protected $_responseHttp;

	/** 
	 * @var \Marvelic\BeamCheckout\Model\ResourceModel\Purchase\Collection 
	 */
	protected $_beamcheckoutPurchaseCollection;

	/**
	 * BeamCheckoutRequest constructor.
	 * @param Context $context
	 * @param ScopeConfigInterface $scopeConfig
	 * @param StoreManagerInterface $storeManagerInterface
	 * @param Curl $curlClient
	 * @param ResponseHttp $responseHttp
	 * @param BeamCheckoutPurchaseFactory $beamcheckoutPurchaseCollection
	 */
	function __construct(
		Context $context,
		ScopeConfigInterface $scopeConfig,
		StoreManagerInterface $storeManagerInterface,
		Curl $beamHelperCurl,
		ResponseHttp $responseHttp,
		BeamCheckoutPurchaseFactory $beamcheckoutPurchaseCollection
	) {
		parent::__construct($context);
		$this->_responseHttp = $responseHttp;
		$this->scopeConfig = $scopeConfig;
		$this->objStoreManagerInterface = $storeManagerInterface;
		$this->_beamHelperCurl = $beamHelperCurl;
		$this->_beamcheckoutPurchaseCollection = $beamcheckoutPurchaseCollection;
	}

	/* Write log */
	public function log($data)
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_request.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info($data);
	}

	/* Beam Checkout payment request. */
	public function beamcheckout_construct_request($parameter, $isLoggedIn, $order)
	{
		$storeId = $order->getStoreId();

		// Set the API endpoint
		$endpoint = $this->getBeamCheckoutEndpoint($storeId);
		// Data reponse after calling to api
		$response = $this->_beamHelperCurl->sendCurlRequest($endpoint, $parameter, $storeId);
		$jsonResult = json_decode($response, true);
		$jsonData = json_decode($parameter, true);

		//Log for json result from api beamcheckout return
		$this->log('----------Beam Checkout Return----------');
		$this->log(json_encode($jsonResult));

		// Redirect payment for beam checkout
		if (isset($jsonResult['paymentLink'])) {
			//Save data result json when api return data

			$orderIncrementId = $jsonData['order']['merchantReferenceId'];
			$beamCollection = $this->_beamcheckoutPurchaseCollection->create();

			$beamCollection->setBeamOrderId($order->getId())
				->setBeamOrderIncrementId($orderIncrementId)
				->setBeamPurchaseId($jsonResult['purchaseId'])
				->setBeamPaymentLink($jsonResult['paymentLink'])
				->save();

			//Check results has response from webhook api beamcheckout
			$this->_responseHttp->setRedirect($jsonResult['paymentLink'])->sendResponse();
		} else {
			//Check results no response from webhook api beamcheckout
			$baseurl = $this->objStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
			$this->_responseHttp->setRedirect($baseurl)->sendResponse();
		}
	}

	/* Get Beam checkout Endpoint. */
	public function getBeamCheckoutEndpoint($storeId)
	{
		$configSettings = $this->scopeConfig->getValue(self::XML_PATH_BEAMCHECKOUT_SETTING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		$merchantId = $configSettings['merchantId'];
		if ($configSettings['mode'] == 1) {
			return "https://stg-partner-api.beamdata.co/purchases/$merchantId";
		} else {
			return "https://partner-api.beamdata.co/purchases/$merchantId";
		}
	}
}
