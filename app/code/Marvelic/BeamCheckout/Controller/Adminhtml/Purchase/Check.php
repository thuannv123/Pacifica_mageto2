<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller\Adminhtml\Purchase;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Sales\Api\OrderRepositoryInterface;
use Marvelic\BeamCheckout\Helper\Curl;
use Marvelic\BeamCheckout\Helper\BeamCheckoutSalesOrder;

class Check extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var BeamCheckoutRequest
     */
    protected $helperBeamCheckoutRequest;

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
    protected $helperBeamSalesOrder;

    /**
     * Check constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BeamCheckoutRequest $helperBeamCheckoutRequest
     * @param OrderRepositoryInterface $orderRepository
     * @param Curl $beamHelperCurl
     * @param BeamCheckoutSalesOrder $helperBeamSalesOrder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BeamCheckoutRequest $helperBeamCheckoutRequest,
        OrderRepositoryInterface $orderRepository,
        Curl $beamHelperCurl,
        BeamCheckoutSalesOrder $helperBeamSalesOrder
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helperBeamCheckoutRequest = $helperBeamCheckoutRequest;
        $this->_orderRepository = $orderRepository;
        $this->_beamHelperCurl = $beamHelperCurl;
        $this->helperBeamSalesOrder = $helperBeamSalesOrder;
    }

    /* Write log */
    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_check.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    /* Execute check button */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_orderRepository->get($orderId);

        // Set the API endpoint
        $storeId = $order->getStoreId();
        $endpoint = $this->helperBeamCheckoutRequest->getBeamCheckoutEndpoint($storeId);

        // Get Purchase Id
        $purchaseId = $this->helperBeamSalesOrder->getPurchaseIdByOrderId($orderId);
        $this->log('----------Purchase Id for submit Check----------');
        $this->log($purchaseId);

        // Data reponse after calling to api
        $response = $this->_beamHelperCurl->sendCurlPurchaseId($endpoint, $purchaseId, $storeId);
        $jsonResult = json_decode($response, true);
        $this->log('----------Beam Checkout result get by Purchase Id----------');
        $this->log(json_encode($jsonResult));

        //Check state result from webhook
        if (isset($jsonResult['state']) && $jsonResult['state'] == 'complete') {
            // create invoice
            if ($order->canInvoice()) {
                $this->helperBeamSalesOrder->prepareInvoice($order);
                $paymentData = [
                    'id' => $orderId
                ];
                $this->helperBeamSalesOrder->createTransaction($order, $paymentData);
                //Set the complete status when payment is completed.
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus(Order::STATE_PROCESSING)
                    ->addStatusHistoryComment('Payment successfully processed by BeamCheckout.')
                    ->save();
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setRefererOrBaseUrl();
            }
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
            exit;
            //this to check other payment status(failed, cancel, pending...)
        }
    }
}
