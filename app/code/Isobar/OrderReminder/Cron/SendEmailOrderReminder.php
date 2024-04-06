<?php

namespace Isobar\OrderReminder\Cron;

use Isobar\OrderReminder\Model\Config;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendEmailOrderReminder
{
    const LIMIT_ORDER = 10;

    private Config $config;

    private OrderCollectionFactory $orderCollectionFactory;

    private TransportBuilder $transportBuilder;

    private StateInterface $inlineTranslation;

    private StoreManagerInterface $storeManager;

    private OrderRepositoryInterface $orderRepository;

    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $state
     * @param StoreManagerInterface $storeManager
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config                   $config,
        OrderCollectionFactory   $orderCollectionFactory,
        TransportBuilder         $transportBuilder,
        StateInterface           $state,
        StoreManagerInterface    $storeManager,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface          $logger
    )
    {
        $this->config = $config;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $state;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $globalIsEnabled = $this->config->isEnabled(ScopeInterface::SCOPE_WEBSITES, 0);
        if (!$globalIsEnabled) {
            $websiteIds = $this->config->getWebistesEnabled();
        }

        if (!$globalIsEnabled && !$websiteIds) {
            return;
        }

        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('sent_email_reminder', 0)
            ->addFieldToFilter('status', ['in' => ['pending', 'pending_payment']])
            ->setOrder('created_at', 'ASC')
            ->setCurPage(1)
            ->setPageSize(self::LIMIT_ORDER);

        if (!$globalIsEnabled) {
            $orders->join(
                ['store_table' => 'store'],
                'main_table.store_id = store_table.store_id
                AND store_table.website_id IN (' . implode(',', $websiteIds) . ')',
                []
            );
        }

        $orders->load();

        if ($orders->getSize()) {
            foreach ($orders->getItems() as $order) {
                /** @var Order $order */
                $now = time();
                $orderRemindTime = $this->config->getExpirationTime($order->getStoreId());
                $createdDate = new \DateTime($order->getCreatedAt());
                $createdDate->modify("+" . $orderRemindTime . "hours");

                if ($createdDate->getTimestamp() <= $now && $this->sendOrderRemindToEmail($order)) {
                    $this->saveOrderReminder($order);
                }
            }
        }
    }

    /**
     * @param Order $order
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRestoreOrderLink(Order $order)
    {
        $storeId = $order->getStoreId();
        $linkExpirationTime = time() + ($this->config->getLinkExpirationTime($storeId) * 3600);
        return $this->storeManager->getStore($storeId)->getUrl(
            'order_reminder/restore/index',
            ['_query' => ['protected_code' => $order->getData('protect_code'), 's' => $linkExpirationTime]]
        );
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function sendOrderRemindToEmail(Order $order)
    {
        $storeId = $order->getStoreId();
        $email = $order->getCustomerEmail();
        $templateId = $this->config->getEmailTemplate($storeId);

        try {
            $templateVars = [
                'customer_name' => $order->getCustomerName(),
                'reminder_link' => $this->getRestoreOrderLink($order)
            ];

            $this->inlineTranslation->suspend();

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope('sales', $storeId)
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return false;
    }

    /**
     * @param Order $order
     * @return void
     */
    private function saveOrderReminder(Order $order)
    {
        $order->setData('sent_email_reminder', 1);
        $this->orderRepository->save($order);
    }
}
