<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Cron;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Marvelic\BeamCheckout\Helper\Curl;
use Marvelic\BeamCheckout\Helper\BeamCheckoutSalesOrder;

class CheckOrderPending
{
    /**
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    public $filterBuilder;

    /**
     * @var FilterGroup
     */
    public $filterGroup;

    /**
     * @var OrderManagementInterface
     */
    public $orderManagement;

    /**
     * @var BeamCheckoutRequest
     */
    public $beamHelperRequest;

    /**
     * @var Curl
     */
    public $beamHelperCurl;

    /**
     * @var BeamCheckoutSalesOrder
     */
    public $beamHelperSalesOrder;

    /**
     * CancelOrderPending constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroup $filterGroup
     * @param OrderManagementInterface $orderManagement
     * @param BeamCheckoutRequest $orderManagement
     * @param Curl $orderManagement
     * @param BeamCheckoutSalesOrder $orderManagement
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup,
        OrderManagementInterface $orderManagement,
        BeamCheckoutRequest $beamHelperRequest,
        Curl $beamHelperCurl,
        BeamCheckoutSalesOrder $beamHelperSalesOrder
    ) {
        $this->orderRepository       = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->filterGroup           = $filterGroup;
        $this->orderManagement       = $orderManagement;
        $this->beamHelperRequest     = $beamHelperRequest;
        $this->beamHelperCurl        = $beamHelperCurl;
        $this->beamHelperSalesOrder  = $beamHelperSalesOrder;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('status', 'Pending_BeamCheckout')
                ->create();

            $orders = $this->orderRepository->getList($searchCriteria)->getItems();

            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $orderId = $order->getId();

                    //get BeamCheckout Endpoint
                    $storeId = $order->getStoreId();
                    $endpoint = $this->beamHelperRequest->getBeamCheckoutEndpoint($storeId);

                    //get PurchaseId
                    $purchaseId = $this->beamHelperSalesOrder->getPurchaseIdByOrderId($orderId);

                    if (isset($purchaseId)) {
                        //Calling to api BeamCheckout
                        $response = $this->beamHelperCurl->sendCurlPurchaseId($endpoint, $purchaseId, $storeId);
                        //Data reponse after calling to api
                        $jsonResult = json_decode($response, true);
                        $this->log('----------Beam Checkout result get by Purchase Id----------');
                        $this->log(json_encode($jsonResult));

                        //Check reponse state order from state data reponse
                        $date = str_replace('+00:00', 'Z', date('c'));
                        if (isset($jsonResult['state']) && ($jsonResult['state'] != 'complete') && isset($jsonResult['expiry']) && ($date > $jsonResult['expiry']) || (isset($jsonResult['isDisabled']) && $jsonResult['isDisabled'] == true)) {
                            //cancel order
                            if ($order->canCancel()) {
                                $order->cancel();
                                $order->addStatusHistoryComment('Cancel Order because order still pending and did not paid.');
                                $this->orderRepository->save($order);
                            }
                        } elseif (isset($jsonResult['state']) && ($jsonResult['state'] == 'complete')) {
                            if (!$order->hasInvoices()) {
                                $this->beamHelperSalesOrder->prepareInvoice($order);
                                $paymentData = [
                                    'id' => $order->getId()
                                ];
                                $this->beamHelperSalesOrder->createTransaction($order, $paymentData);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log the error message or perform any necessary error handling
            $errorMessage = $e->getMessage();
            $this->log('An error occurred: ' . $errorMessage);
        }

        return $this;
    }

    /* Write log */
    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/crontab_beamcheckout.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }
}
