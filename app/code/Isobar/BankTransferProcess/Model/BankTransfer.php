<?php

namespace Isobar\BankTransferProcess\Model;

use Magento\Sales\Model\Order;
use Magento\Cms\Model\Template\FilterProvider;

class BankTransfer extends \Magento\OfflinePayments\Model\Banktransfer
{
	const BANK_TRANSFER_PENDING_PAYMENT_STATUS = 'pending_payment';

	protected $_isInitializeNeeded = true;
	protected $_filterProvider;
	protected $filterManager;

	public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
		FilterProvider $filterProvider
	) {
		parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger);
		$this->_filterProvider = $filterProvider;
	}
	public function getInstructions()
	{
		return '';
	}

	public function getDescriptions()
	{
		$instructions = $this->getConfigData('descriptions');
        if ($instructions == null) {
            return $instructions !== null ? trim($instructions) : '';
        } else {
            $html = $this->_filterProvider->getPageFilter()->filter($this->getConfigData('descriptions'));
            return $html;
        }
	}
	/**
	 * @return string|true
	 */
	public function getConfigPaymentAction()
	{
		return $this->getConfigData('order_status') == self::BANK_TRANSFER_PENDING_PAYMENT_STATUS ?
			\Magento\Payment\Model\MethodInterface::ACTION_ORDER : parent::getConfigPaymentAction();
	}

	/**
	 * @param $paymentAction
	 * @param $stateObject
	 * @return $this|BankTransfer
	 */
	public function initialize($paymentAction, $stateObject)
	{
		if ($this->getConfigData('order_status') == self::BANK_TRANSFER_PENDING_PAYMENT_STATUS) {
			$stateObject->setData('state', Order::STATE_PENDING_PAYMENT);
			$stateObject->setData('status', Order::STATE_PENDING_PAYMENT);
		}

		return $this;
	}
}
