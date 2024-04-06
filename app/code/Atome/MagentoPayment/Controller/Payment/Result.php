<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Controller\Payment;

use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\Page;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;

class Result extends AtomeAction
{
    /**
     * @var Session
     */
    protected $session;
    protected $paymentGatewayConfig;
    protected $controllerContext;

    public function __construct(
        Context              $context,
        Session              $session,
        PaymentGatewayConfig $paymentGatewayConfig

    )
    {
        parent::__construct($context);
        $this->controllerContext = $context;
        $this->session = $session;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }

    public function execute()
    {
        $orderId = $this->_request->getParam('orderId');
        $type = strtolower($this->_request->getParam('type'));

        Logger::instance()->info('action Result: begin' . json_encode(compact('orderId', 'type')));

        if (!$orderId) {
            return $this->whenParameterMissing();
        }


        if ($order = $this->getOrder($orderId)) {
            if ($type === 'cancel') {
                return $this->whenCancelOrder($order);
            }

            $this->restoreSession($order);

            if ($redirect = $this->getRedirectUrl(
                $order->getState(),
                $order->getStatus()
            )) {
                return $this->_redirect($redirect);
            }
        }

        return $this->renderPage($orderId);
    }


    protected function renderPage($orderId, $paymentFailed = false)
    {
        $order = $this->getOrder($orderId);
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        /** @var Template $block */
        $block = $page->getLayout()->getBlock('atome.payment.result');
        $block->setData('orderId', $orderId);
        if ($paymentFailed) {
            $block->setData('payment_failed', 1);
            $block->setData('checkout_url', rtrim($order->getStore()->getBaseUrl() ?? '', '/') . '/checkout/cart');
        } else {
            $this->messageManager->addWarningMessage(__("Atome payment is processing. Please wait a while."));
        }

        return $page;
    }

    protected function whenParameterMissing()
    {
        $this->messageManager->addErrorMessage(__("Atome payment unexpected problem occurs."));

        return $this->_redirect('checkout/cart');
    }

    /**
     * @param Order $order
     */
    protected function whenCancelOrder($order)
    {
        if (!$this->paymentGatewayConfig->getClearCartWithoutPaying()) {
            $this->session->restoreQuote();
        }

        return $this->renderPage(
            $order->getEntityId(),
            true
        );
    }

    /**
     * @param OrderInterface $order
     * @param $quoteId
     * @return void
     */
    protected function restoreSession($order)
    {
        $this->session
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());
    }

    protected function getRedirectUrl($orderState, $orderStatus)
    {
        $redirect = null;
        if ($orderStatus === $this->paymentGatewayConfig->getOrderStatus()) {
            /*
             * If the status has been changed to the status set by the merchant,
             * it means the payment is successful and the callback has been completed,
             * and the success page can be displayed.
             *
             * This also prevents merchants from using unconventional order processes,
             * such as setting the state to remain as PENDING_PAYMENT after payment is complete.
             */
            $this->messageManager->addSuccessMessage(__("Atome Payment Completed"));
            return 'checkout/onepage/success';
        }

        /*
         * Otherwise we use state to determine what result should be displayed
         */
        switch ($orderState) {
            case Order::STATE_NEW:
            case Order::STATE_PENDING_PAYMENT:
            case Order::STATE_HOLDED:
            case Order::STATE_PAYMENT_REVIEW:
                break;
            case Order::STATE_PROCESSING:
            case Order::STATE_COMPLETE:
                $this->messageManager->addSuccessMessage(__("Atome Payment Completed"));
                $redirect = 'checkout/onepage/success';
                break;
            case Order::STATE_CLOSED:
            case Order::STATE_CANCELED:
                $this->messageManager->addErrorMessage(__('Atome payment failed. Please try again or use an alternative payment method.'));
                $redirect = 'checkout/onepage/failure';
                break;
            default:
                Logger::instance()->error("Unknown Magento order state: {$orderState}");
                break;
        }

        return $redirect;
    }


    /**
     * @param $orderId
     * @param $quoteId
     * @return OrderInterface|Order|null
     * @throws InputException
     */
    protected function getOrder($orderId)
    {
        $order = null;
        try {
            $objectManager = ObjectManager::getInstance();
            $order = $objectManager->get(OrderRepository::class)->get($orderId);
        } catch (NoSuchEntityException $exception) {
            // PASS
        }

        return $order;
    }

    protected function _redirect($path, $arguments = [])
    {
        Logger::instance()->info('action Result: redirectUrl => ' . $path);

        return parent::_redirect($path, $arguments);
    }

}
