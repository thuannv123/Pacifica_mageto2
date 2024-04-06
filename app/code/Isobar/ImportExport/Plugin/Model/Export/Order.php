<?php
declare(strict_types=1);

namespace Isobar\ImportExport\Plugin\Model\Export;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Order
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /**
     * @var DataObject
     */
    public $list;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Customer constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param DataObject $list DataObject
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        DataObject $list = null,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->list = $list;
        $this->logger = $logger;
    }

    /**
     * @param \Firebear\ImportExport\Model\Export\Order $subject
     * @param $result
     * @param $item
     * @return mixed
     */
    public function afterExportItem(
        \Firebear\ImportExport\Model\Export\Order $subject,
        $result,
        $item
    ) {
        try {
            if (!empty($item->getExported())) {
                return $result;
            }
            if ($this->list == null) {
                $this->list = new DataObject();
            }
            if ($this->list->hasData('orders')) {
                $data = $this->list->getData('orders');
                $data[] = $item->getId();
                $this->list->setData('orders', $data);
            } else {
                $this->list->setData('orders', [$item->getId()]);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }

    public function afterExport(
        \Firebear\ImportExport\Model\Export\Order $subject,
        $result
    ) {
        if ($this->list && $this->list->hasData('orders')) {
            $orderExported = $this->list->getData('orders');
            foreach ($orderExported as $orderId) {
                $order = $this->orderRepository->get($orderId);
                $order->setData('exported', 1);
                $this->orderRepository->save($order);
            }
        }
        return $result;
    }
}
