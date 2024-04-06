<?php

namespace Isobar\P2c2p\Model\CronJob;

use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoresConfig;
use Magento\Sales\Model\Order;

/**
 * Class that provides functionality of cleaning expired quotes by cron
 */
class CleanExpiredOrders
{
	protected const STATUS_PENDING_2C2P = 'Pending_2C2P';

	/**
	 * @var StoresConfig
	 */
	protected $storesConfig;

	/**
	 * @var CollectionFactory
	 */
	protected $orderCollectionFactory;

	/**
	 * @var OrderManagementInterface
	 */
	private $orderManagement;

	/**
	 * @param StoresConfig $storesConfig
	 * @param CollectionFactory $collectionFactory
	 * @param OrderManagementInterface|null $orderManagement
	 */
	public function __construct(
		StoresConfig $storesConfig,
		CollectionFactory $collectionFactory,
		OrderManagementInterface $orderManagement = null
	) {
		$this->storesConfig = $storesConfig;
		$this->orderCollectionFactory = $collectionFactory;
		$this->orderManagement = $orderManagement ?: ObjectManager::getInstance()->get(OrderManagementInterface::class);
	}

	/**
	 * Clean expired quotes (cron process)
	 *
	 * @return void
	 */
	public function execute()
	{
		$lifetimes = $this->storesConfig->getStoresConfigByPath('sales/orders/delete_pending_after');
		foreach ($lifetimes as $storeId => $lifetime) {
			/** @var $orders \Magento\Sales\Model\ResourceModel\Order\Collection */
			$orders = $this->orderCollectionFactory->create();
			$orders->addFieldToFilter('store_id', $storeId);
			$orders->addFieldToFilter('status', [Order::STATE_PENDING_PAYMENT, self::STATUS_PENDING_2C2P]);
			$orders->getSelect()->where(
				new \Zend_Db_Expr('TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) >= ' . $lifetime * 60)
			);

			foreach ($orders->getAllIds() as $entityId) {
				$this->orderManagement->cancel((int) $entityId);
			}
		}
	}
}
