<?php

namespace Isobar\Shipping\Model\Carrier;

use Magento\Framework\App\Helper\Context;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Sales\Model\Order\Shipment\TrackRepository;
use Magento\Sales\Model\Order\ShipmentRepository;
use \Isobar\Shipping\Model\ShipmentDataByOrder;

class CustomCarrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier
{
    const CODE = 'custom_carrier';

    protected $_code = self::CODE;

    /**
     * Allowed hash keys for shipment tracking
     *
     * @var string[]
     */
    protected $_allowedHashKeys = ['order_id', 'ship_id', 'track_id'];

    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected $trackFactory;

    /**
     * @var TrackRepository
     */
    protected $trackRepository;

    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var ShipmentDataByOrder
     */
    protected $shipmentDataByOrder;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    protected $trackStatusFactory;

    /**
     * @var \Magento\Shipping\Model\InfoFactory
     */
    protected $_shippingInfoFactory;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     * @param TrackRepository $trackRepository
     * @param ShipmentRepository $shipmentRepository
     * @param ShipmentDataByOrder $shipmentDataByOrder
     * @param \Magento\Shipping\Model\InfoFactory $shippingInfoFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Psr\Log\LoggerInterface $logger,
        TrackRepository $trackRepository,
        ShipmentRepository $shipmentRepository,
        ShipmentDataByOrder $shipmentDataByOrder,
        \Magento\Shipping\Model\InfoFactory $shippingInfoFactory,
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->trackFactory = $trackFactory;
        $this->trackStatusFactory = $trackStatusFactory;
        $this->trackRepository = $trackRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentDataByOrder = $shipmentDataByOrder;
        $this->_shippingInfoFactory = $shippingInfoFactory;
        $this->urlDecoder = $context->getUrlDecoder();
        $this->request = $request;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function collectRates(RateRequest $request)
    {
        return false;
    }

    /**
     * Determine if tracking is set in the admin panel
     **/
    public function isTrackingAvailable()
    {
        if ($this->getConfigFlag('active')) {
            return true;
        }
        return false;
    }

    /**
     * @param $trackingNumber
     *
     * @return bool|\Magento\Shipping\Model\Tracking\Result
     */
    public function getTrackingInfo($trackingNumber)
    {
        $result = $this->getTracking($trackingNumber);

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($result) && ! empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Get tracking information
     * @param $trackingNumber
     *
     * @return \Magento\Shipping\Model\Tracking\Result
     */
    public function getTracking($trackingNumber)
    {
        $result = $this->trackFactory->create();
        $tracking = $this->trackStatusFactory->create();
        $shippingInfoModel = $this->decodeTrackingHash($this->request->getParams()['hash']);
        if (isset($shippingInfoModel['key']) && $shippingInfoModel['key'] == 'track_id') {
            $trackRepository =  $this->trackRepository->get($shippingInfoModel['id']);
            if ($trackRepository->getData('track_url') != null) {
                $tracking->setUrl($trackRepository->getData('track_url'));
            }
        } elseif (isset($shippingInfoModel['key']) && $shippingInfoModel['key'] == 'ship_id') {
            $shipmentRepository = $this->shipmentRepository->get($shippingInfoModel['id']);
            $tracks = $shipmentRepository->getAllTracks();
            foreach ($tracks as $track) {
                if ($track->getData('track_url') != null && $track->getData('track_number') == $trackingNumber) {
                    $tracking->setUrl($track->getData('track_url'));
                }
            }
        } elseif (isset($shippingInfoModel['key']) && $shippingInfoModel['key'] == 'order_id') {
            $shipmentItems = $this->shipmentDataByOrder->getShipmentDataByOrderId($shippingInfoModel['id']);
            foreach ($shipmentItems as $shipmentItem) {
                $shipmentRepository = $this->shipmentRepository->get($shipmentItem->getEntityId());
                $tracks = $shipmentRepository->getAllTracks();
                foreach ($tracks as $track) {
                    if ($track->getData('track_url') != null && $track->getData('track_number') == $trackingNumber) {
                        $tracking->setUrl($track->getData('track_url'));
                    }
                }
            }
        }

        $tracking->setCarrier($this->_code);
        $tracking->setTracking($trackingNumber);
        $tracking->setCarrierTitle($this->getConfigData('title'));
        $result->append($tracking);

        return $result;
    }

    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', $this->urlDecoder->decode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return ['key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]];
        }
        return [];
    }
}
