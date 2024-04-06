<?php

namespace Marvelic\BankTransfer\Model\Order;

class Config extends \Magento\Sales\Model\Order\Config
{
    public function __construct(
        \Magento\Sales\Model\Order\StatusFactory $orderStatusFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Magento\Framework\App\State $state,
        \Magento\Sales\Model\Order\StatusLabel $statusLabel = null
    ) {
        parent::__construct(
            $orderStatusFactory,
            $orderStatusCollectionFactory,
            $state,
            $statusLabel
        );
    }

    public function getOrderStateStatuses($state, $addLabels = true)
    {
        $key = sha1(json_encode([$state, $addLabels]));
        if (isset($this->stateStatuses[$key])) {
            return $this->stateStatuses[$key];
        }
        $statuses = [];

        if (!is_array($state)) {
            $state = [$state];
        }
        foreach ($state as $_state) {
            $stateNode = $this->_getState($_state);
            if ($stateNode) {
                $collection = $this->orderStatusCollectionFactory->create()
                    ->addStateFilter($_state)
                    ->orderByLabel();
                foreach ($collection as $item) {
                    $status = $item->getData('status');
                    if ($addLabels) {
                        $statuses[$status] = $this->getStatusLabel($status);
                    } else {
                        $statuses[] = $status;
                    }
                }
            }

            if (isset($_state) && $_state == 'payment_review') {
                $collection = $this->orderStatusCollectionFactory->create()
                    ->addStateFilter('canceled')
                    ->orderByLabel();
                foreach ($collection as $item) {
                    $status = $item->getData('status');
                    if ($status == 'canceled') {
                        if ($addLabels) {
                            $statuses[$status] = $this->getStatusLabel($status);
                        } else {
                            $statuses[] = $status;
                        }
                    }
                }
            }
        }

        $this->stateStatuses[$key] = $statuses;
        return $statuses;
    }
}
