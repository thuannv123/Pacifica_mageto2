<?php

namespace Marvelic\AnowaveEc4\Plugin;

use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface;

class Refund extends \Anowave\Ec4\Plugin\Refund
{
    /**
     * No category fallback 
     * 
     * @var string
     */
    const FALLBACK_No_CATEGORY = 'Not set';

    /**
     * @var \Anowave\Ec4\Helper\Data
     */
    protected $helper = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attribute;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager = null;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    protected $helperData;

    private $logger;

    public $anowaveEc4logger;

    /**
     * Constructor 
     * 
     * @param \Anowave\Ec4\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Anowave\Ec4\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $state,
        \Marvelic\AnowaveEc4\Helper\Data $helperData,
        \Marvelic\AnowaveEc4\Helper\Logger $anowaveEc4logger,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->attribute = $attribute;
        $this->categoryRepository = $categoryRepository;
        $this->scopeConfig = $scopeConfig;
        $this->state = $state;
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->anowaveEc4logger = $anowaveEc4logger;
    }

    /**
     * Modify exeucte method 
     * 
     * @param \Anowave\Ec\Observer\Refund $context
     * @param callable $proceed
     * @param EventObserver $observer
     * @return unknown|boolean
     */
    public function aroundExecute(\Anowave\Ec\Observer\Refund $context, callable $proceed, EventObserver $observer)
    {
        if (!$this->trackRefund()) {
            return $proceed($observer);
        }

        if ($this->refund($observer->getPayment()->getOrder())) {
            $this->messageManager->addSuccessMessage("Refund for order {$observer->getPayment()->getOrder()->getIncrementId()} tracked to Google Analytics 4 {$this->helperData->getMeasurementIdStore($observer->getPayment()->getOrder()->getStore()->getId())} successfully.");

            return true;
        }

        return $proceed($observer);
    }

    /**
     * Refund order 
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function refund(\Magento\Sales\Model\Order $order)
    {
        $measurement_id         = $this->helperData->getMeasurementIdStore($order->getStore()->getId());
        $measurement_api_secret = $this->helperData->getMeasurementApiSecretKey($order->getStore()->getId());

        $payload = function (array $events = []) use ($measurement_id, $measurement_api_secret) {
            return
                [
                    'client_id' => bin2hex(random_bytes(32)),
                    'events'    => $events
                ];
        };

        if ($measurement_id && $measurement_api_secret) {
            $items = [];

            /**
             * Default start position
             *
             * @var int
             */
            $index = 1;

            if ($order->getTotalRefunded() > 0) {
                if ($order->getIsVirtual()) {
                    $address = $order->getBillingAddress();
                } else {
                    $address = $order->getShippingAddress();
                }


                foreach ($this->getProducts($order) as $product) {
                    $item =
                        [
                            'index'         =>          $index,
                            'item_id'       =>          @$product['id'],
                            'item_name'     =>          @$product['name'],
                            'item_brand'    => (string) @$product['brand'],
                            'price'         => (float)  @$product['price'],
                            'quantity'      => (int)    @$product['quantity']
                        ];

                    $categories = explode(chr(47), (string) @$product['category']);

                    if ($categories) {
                        $category = array_shift($categories);

                        if ($category) {
                            $item['item_category'] = $category;
                        }

                        foreach ($categories as $index => $category) {
                            $key = $index + 2;

                            $item["item_category{$index}"] = $category;
                        }
                    } else {
                        $item['item_category'] = __(static::FALLBACK_No_CATEGORY);
                    }


                    $items[] = $item;

                    $index++;
                }

                $data = $payload(
                    [
                        [
                            'name' => 'refund',
                            'params' =>
                            [
                                'currency'       => $this->helper->getBaseHelper()->getCurrency(),
                                'transaction_id' => $order->getIncrementId(),
                                'value'          => (float) $this->helper->getBaseHelper()->getRevenue($order),
                                'shipping'         => (float) $order->getShippingInclTax(),
                                'tax'             => (float) $order->getTaxAmount(),
                                'affiliation'    => $this->helper->getBaseHelper()->escape(
                                    $this->helper->getBaseHelper()->getStoreName()
                                ),
                                'items' => $items,
                                'traffic_type' => $this->state->getAreaCode()
                            ]
                        ]
                    ]
                );

                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GA4_refund.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info('Param request to GA4: ' . json_encode($data));

                $this->writeLogRefund('Param request refund to GA4: ' . json_encode($data));

                $this->anowaveEc4logger->writelog('Param request refund to GA4: ' . json_encode($data));

                // write logger to system log
                $this->logger->info('Param request to GA4: ' . json_encode($data));

                $analytics = curl_init("https://www.google-analytics.com/mp/collect?measurement_id={$measurement_id}&api_secret={$measurement_api_secret}");

                curl_setopt($analytics, CURLOPT_HEADER,         0);
                curl_setopt($analytics, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($analytics, CURLOPT_POST,             1);
                curl_setopt($analytics, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($analytics, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($analytics, CURLOPT_USERAGENT,        'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                curl_setopt($analytics, CURLOPT_POSTFIELDS,     utf8_encode(
                    json_encode($data)
                ));

                try {
                    $response = curl_exec($analytics);

                    if ($this->helper->getBaseHelper()->useDebugMode()) {
                        $this->messageManager->addNoticeMessage(json_encode($data, JSON_PRETTY_PRINT));
                    }

                    if (!curl_error($analytics)) {
                        return true;
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        return false;
    }
    public function getProducts(\Magento\Sales\Model\Order $order) : array 
    {
        $products = [];
        foreach ($order->getAllVisibleItems() as $item)
        {
            $price = 0;
            $collection = [];
            
            if ($item->getProduct())
            {
                $entity = $this->productFactory->create()->load
                (
                    $item->getProduct()->getId()
                );
                
                $collection = $entity->getCategoryIds();
            }
            
            if ($collection)
            {
                $category = $this->categoryRepository->get(end($collection));
            }
            else
            {
                $category = null;
            }
            
            /**
             * Get product name
             */
            $args = new \stdClass();
            
            $args->id 	= $this->helper->getBaseHelper()->getIdentifierItem($item);
            $args->name = $item->getName();
            
            /**
             * Product variant(s)
             *
             * @var []
             */
            $variant = [];
            
            if ('configurable' === $item->getProductType())
            {
                $options = (array) $item->getProductOptions();
                
                if (isset($options['info_buyRequest']))
                {
                    $info = new \Magento\Framework\DataObject($options['info_buyRequest']);
                    
                    /**
                     * Construct variant
                     */
                    foreach ((array) $info->getSuperAttribute() as $id => $option)
                    {
                        /**
                         * Load attribute model
                         *
                         * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
                         */
                        $attribute = $this->attribute->create()->load($id);
                        
                        if ($attribute->usesSource())
                        {
                            $variant[] = join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER_ATT,
                            [
                                $this->escape($attribute->getFrontendLabel()),
                                $this->escape($attribute->getSource()->getOptionText($option))
                            ]);
                        }
                    }
                }
            }
            $price = $item->getRowTotalInclTax();
           
            $data =
            [
                'name' 		=> $this->escape($args->name),
                'id'		=> $this->escape($args->id),
                'price' 	=> $price,
                'quantity' 	=> $item->getQtyOrdered(),
                'variant'	=> join(\Anowave\Ec\Helper\Data::VARIANT_DELIMITER, $variant)
            ];
            
            if ($category)
            {
                $data['category'] = $this->escape($category->getName());
            }
            
            $products[] = $data;
        }

        return $products;
    }

    public function writeLogRefund($data)
	{
		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GA4_refund.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info($data);
	}
}
