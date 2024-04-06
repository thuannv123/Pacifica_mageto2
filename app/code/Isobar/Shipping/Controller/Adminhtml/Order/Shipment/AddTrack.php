<?php

namespace Isobar\Shipping\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;

class AddTrack extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\AddTrack
{
    const CODE = 'custom_carrier';

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentTrackInterfaceFactory
     */
    private $trackFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $storeManager;

    /**
     * @param Action\Context $context
     * @param ShipmentLoader $shipmentLoader
     * @param ShipmentRepositoryInterface|null $shipmentRepository
     * @param ShipmentTrackInterfaceFactory|null $trackFactory
     * @param SerializerInterface|null $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Action\Context $context,
        ShipmentLoader $shipmentLoader,
        ShipmentRepositoryInterface $shipmentRepository = null,
        ShipmentTrackInterfaceFactory $trackFactory = null,
        SerializerInterface $serializer = null,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $shipmentLoader, $shipmentRepository, $trackFactory, $serializer);
        $this->shipmentRepository = $shipmentRepository ?: ObjectManager::getInstance()
            ->get(ShipmentRepositoryInterface::class);
        $this->trackFactory = $trackFactory ?: ObjectManager::getInstance()
            ->get(ShipmentTrackInterfaceFactory::class);
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(SerializerInterface::class);
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
    /**
     * Add new tracking number action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number = $this->getRequest()->getPost('number');
            $title = $this->getRequest()->getPost('title');

            if (empty($carrier)) {
                throw new LocalizedException(__('Please specify a carrier.'));
            }
            if (empty($number)) {
                throw new LocalizedException(__('Please enter a tracking number.'));
            }

            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            if ($shipment) {
                $track = $this->trackFactory->create()->setNumber(
                    $number
                )->setCarrierCode(
                    $carrier
                )->setTitle(
                    $title
                );
                if ($carrier == self::CODE) {
                    $trackingUrl = $this->getConfigData('url') . $number;
                    $track->setTrackUrl($trackingUrl);
                }
                $shipment->addTrack($track);
                $this->shipmentRepository->save($shipment);

                $this->_view->loadLayout();
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipments'));
                $response = $this->_view->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = [
                    'error' => true,
                    'message' => __('We can\'t initialize shipment for adding tracking number.'),
                ];
            }
        } catch (LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot add tracking number.')];
        }

        if (\is_array($response)) {
            $response = $this->serializer->serialize($response);

            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setJsonData($response);
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents($response);
    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  false|string
     */
    public function getConfigData($field)
    {
        if ($this->getRequest()->getPost('store_id') == null) {
            $storeCode = $this->storeManager->getStore()->getCode();
        } else {
            $storeData = $this->storeManager->getStore($this->getRequest()->getPost('store_id'));
            $storeCode = $storeData->getCode();
        }
        $path = 'carriers/' . self::CODE . '/' . $field;

        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }
}
