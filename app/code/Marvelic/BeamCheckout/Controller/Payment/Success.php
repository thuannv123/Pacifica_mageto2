<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderFactory;

class Success extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var Session
	 */
	protected $_checkoutSession;

	/**
	 * @var OrderFactory
	 */
	protected $_orderFactory;

	/**
	 * Success constructor.
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 * @param Session $checkoutSession
	 * @param OrderFactory $orderFactory
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		Session $checkoutSession,
		OrderFactory $orderFactory
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_checkoutSession = $checkoutSession;
		$this->_orderFactory = $orderFactory;
		parent::__construct($context);
	}

	/* Execute Beam Checkout controller success */
	public function execute()
	{
		$resultRedirect = $this->resultPageFactory->create();
		return $resultRedirect;
	}
}
