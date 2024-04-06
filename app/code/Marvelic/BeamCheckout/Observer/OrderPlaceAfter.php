<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Marvelic\BeamCheckout\Helper\BeamCheckoutRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Marvelic\BeamCheckout\Model\BeamCheckoutPurchaseFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\BeamCheckout\Helper\Curl;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * Payment expiry
     *
     * @var string
     */
    const BEAM_CHECKOUT_PAYMENT_EXPIRY = 'payment/beamcheckout/account_setting/expiry_setting/paymentExpiry';

    /**
     * Payment credit card
     *
     * @var string
     */
    const BEAMCHECKOUT_CREDIT_CARD = 'payment/beamcheckout/beamcheckout_creditcard/supported_payment_methods';

    /**
     * Payment ewallet
     *
     * @var string
     */
    const BEAMCHECKOUT_EWALLET = 'payment/beamcheckout/beamcheckout_ewallet/supported_payment_methods';

    /**
     * Payment qrcode
     *
     * @var string
     */
    const BEAMCHECKOUT_QRCODE = 'payment/beamcheckout/beamcheckout_qrcode/supported_payment_methods';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $_paymentMethodManagement;

    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * @var BeamCheckoutRequest
     */
    protected $_beamCheckoutRequest;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Redirect
     */
    protected $resultRedirect;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * @var BeamCheckoutPurchaseFactory
     */
    protected $_beamcheckoutPurchaseFactory;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $configSettings;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Curl
     */
    protected $_beamHelperCurl;

    /**
     * OrderPlaceAfter constructor.
     * @param LoggerInterface $logger
     * @param CartRepositoryInterface $quoteRepository
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param BeamCheckoutRequest $beamCheckoutRequest
     * @param ResponseInterface $response
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param BeamCheckoutPurchaseFactory $beamcheckoutPurchaseFactory
     * @param ObjectManager $objectManager
     * @param ScopeConfigInterface $configSettings
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Curl $beamHelperCurl
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        PaymentMethodManagementInterface $paymentMethodManagement,
        Quote $quote,
        ScopeConfigInterface $configSettings,
        BeamCheckoutRequest $beamCheckoutRequest,
        ResponseInterface $response,
        Redirect $resultRedirect,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        BeamCheckoutPurchaseFactory $beamcheckoutPurchaseFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        Curl $beamHelperCurl
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_paymentMethodManagement = $paymentMethodManagement;
        $this->_quote = $quote;
        $this->_beamCheckoutRequest = $beamCheckoutRequest;
        $this->configSettings = $configSettings;
        $this->response = $response;
        $this->resultRedirect = $resultRedirect;
        $this->messageManager = $messageManager;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        $this->_beamcheckoutPurchaseFactory = $beamcheckoutPurchaseFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->_beamHelperCurl = $beamHelperCurl;
    }

    /* Write log */
    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/beamcheckout_order_backend.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }

    /* Execute for after place order backend */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $incrementId = $order->getIncrementId();
            $methodCode = $order->getPayment()->getMethodInstance()->getCode();
            if ($methodCode == 'beamcheckout_creditcard' || $methodCode == 'beamcheckout_ewallet' || $methodCode == 'beamcheckout_qrcode') {
                $storeId = $order->getStoreId();
                $endpoint = $this->_beamCheckoutRequest->getBeamCheckoutEndpoint($storeId);
                $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
                //Get payment method support
                $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
                if ($paymentMethod == 'beamcheckout_creditcard') {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_CREDIT_CARD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                } else if ($paymentMethod == 'beamcheckout_ewallet') {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_EWALLET, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                } else {
                    $paymentMethodsSupportConfig = $this->configSettings->getValue(self::BEAMCHECKOUT_QRCODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
                }
                $paymentMethodSupport = explode(",", $paymentMethodsSupportConfig);

                //Get product items
                $proArray = $this->getProductItems($order);
                //Get payment expiry 
                $time = $this->getPaymentExpiry();
                if ($time > 0) {
                    $date = str_replace('+00:00', 'Z', date('c'));
                    $dateTime = new \DateTime($date);
                    $dateTime->modify("+$time minutes");
                    $paymentExpiry = $dateTime->format('c');

                    //Create basic form array.
                    $send_product = array(
                        'expiry' => $paymentExpiry,
                        'order' => [
                            'currencyCode' => "THB",
                            'merchantReferenceId' => $incrementId,
                            'netAmount' => round($order->getBaseGrandTotal(), 2),
                            'orderItems' => $proArray,
                            'totalAmount' => round($order->getBaseGrandTotal(), 2),
                            'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
                        ],
                        'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
                        'redirectUrl' => $baseUrl . 'beamcheckout/payment/success?id=' . $incrementId,
                        'supportedPaymentMethods' => $paymentMethodSupport,
                    );
                } else {
                    //Create basic form array.
                    $send_product = array(
                        'order' => [
                            'currencyCode' => "THB",
                            'merchantReferenceId' => $incrementId,
                            'netAmount' => round($order->getBaseGrandTotal(), 2),
                            'orderItems' => $proArray,
                            'totalAmount' => round($order->getBaseGrandTotal(), 2),
                            'totalDiscount' => round($order->getDiscountAmount(), 2) * (-1),
                        ],
                        'requiredFieldsFormId' => 'beamdatacompany-checkout-phoneonly',
                        'redirectUrl' => $baseUrl . 'beamcheckout/payment/success?id=' . $incrementId,
                        'supportedPaymentMethods' => $paymentMethodSupport,
                    );
                }

                $this->log('----------Beam Checkout data send to Api----------');
                $this->log(json_encode($send_product));
                $response = $this->_beamHelperCurl->sendCurlRequest($endpoint, json_encode($send_product), $storeId);
                $jsonResult = json_decode($response, true);
                $this->log('----------Data return from Beam Checkout----------');
                $this->log(json_encode($jsonResult));
                //Save data json when send to api
                $jsonData = json_decode(json_encode($send_product), true);

                $beamCollection = $this->_beamcheckoutPurchaseFactory->create();
                $beamCollection->setBeamOrderId($order->getId())
                    ->setBeamOrderIncrementId($jsonData['order']['merchantReferenceId'])
                    ->setBeamPurchaseId($jsonResult['purchaseId'])
                    ->setBeamPaymentLink($jsonResult['paymentLink'])
                    ->save();

                return $this;
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }

    /* Get product items from order */
    public function getProductItems($order)
    {
        $items = $order->getAllVisibleItems();

        $array = [];
        foreach ($items as $item) {
            $productId = $item->getProductId();
            $product = $this->productRepository->getById($productId);
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();

            $proArray = [
                'product' => [
                    'description' => $item->getDescription(),
                    'sku' => $item->getSku(),
                    'price' => $finalPrice,
                    'name' => $item->getName()
                ],
                'quantity' => (int)$item->getQtyOrdered(),
            ];
            $array[] = $proArray;
        }

        //order shipping
        $shippingAmount = $order->getShippingInclTax();
        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod && $shippingAmount != 0) {
            $proArray = [
                'product' => [
                    'price' => round($shippingAmount, 2),
                    'name' => $order->getShippingDescription()
                ],
                'quantity' => 1,
            ];
            $array[] = $proArray;
        }

        //order discount
        $discountDescription = $order->getDiscountDescription();
        $discountAmount = $order->getDiscountAmount();
        if ($discountAmount != 0) {
            if ($discountDescription == null) {
                $proArray = [
                    'product' => [
                        'price' => round($discountAmount, 2),
                        'name' => 'Discount'
                    ],
                    'quantity' => 1,
                ];
            } else {
                $proArray = [
                    'product' => [
                        'price' => round($discountAmount, 2),
                        'name' => "Discount (" . $order->getDiscountDescription() . ")"
                    ],
                    'quantity' => 1,
                ];
            }

            $array[] = $proArray;
        }
        return $array;
    }

    /* Get payment expiry from configuration */
    public function getPaymentExpiry()
    {
        $expiryConfig = explode(',', $this->configSettings->getValue(self::BEAM_CHECKOUT_PAYMENT_EXPIRY, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE));
        $hourstomins = (int)$expiryConfig[0] * 60;
        $minutes = (int)$expiryConfig[1];
        $time = $hourstomins + $minutes;
        return $time;
    }
}
