<?php
namespace Isobar\BankTransferProcess\Model\Config\Source\Order\Status;

class BankTransferStatuses extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = [
		\Magento\Sales\Model\Order::STATE_NEW,
		\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
	];
}