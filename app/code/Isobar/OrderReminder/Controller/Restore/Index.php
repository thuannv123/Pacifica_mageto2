<?php

namespace Isobar\OrderReminder\Controller\Restore;

use Isobar\OrderReminder\Model\HandleReloadCartCookie;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Psr\Log\LoggerInterface;

class Index implements HttpGetActionInterface
{
    private RequestInterface $request;

    private RedirectFactory $resultRedirectFactory;

    private ManagerInterface $messageManager;

    private Session $customerSession;

    private OrderCollectionFactory $orderCollectionFactory;

    private OrderRepositoryInterface $orderRepository;

    private CartRepositoryInterface $cartRepository;

    private CheckoutSession $checkoutSession;

    private HandleReloadCartCookie $handleReloadCartCookie;

    private LoggerInterface $logger;


    /**
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Session $customerSession
     * @param CartRepositoryInterface $cartRepository
     * @param CheckoutSession $checkoutSession
     * @param HandleReloadCartCookie $handleReloadCartCookie
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        OrderCollectionFactory $orderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        Session $customerSession,
        CartRepositoryInterface $cartRepository,
        CheckoutSession $checkoutSession,
        HandleReloadCartCookie $handleReloadCartCookie,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->customerSession = $customerSession;
        $this->cartRepository = $cartRepository;
        $this->checkoutSession = $checkoutSession;
        $this->handleReloadCartCookie = $handleReloadCartCookie;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $protectedCode = $this->request->getParam('protected_code');
        $linkExpirationTime = $this->request->getParam('s');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$protectedCode || !$linkExpirationTime) {
            $this->messageManager->addErrorMessage(__('Invalid protected code.'));
            return $resultRedirect->setPath('/');
        }

        try {
            $order = $this->getOrderByProtectCode($protectedCode);

            if (!$order || !$order->getId()) {
                throw new NoSuchEntityException(__('Couldn\'t find order.'));
            }

            $quoteOrder = $this->cartRepository->get($order->getQuoteId());

            if ((int)$linkExpirationTime < time()) {
                throw new AuthorizationException(__('Restore cart was rejected! Because reached time expired.'));
            }

            if (!$order->getCustomerIsGuest()) {
                if ($this->customerSession->isLoggedIn() && $this->customerSession->getCustomerId() !== $order->getCustomerId()) {
                    $this->messageManager->addErrorMessage(__('Current account not match with order\'s account.'));
                    return $resultRedirect->setPath('/');
                }

                $this->customerSession->expireSessionCookie();
                $this->customerSession->loginById($order->getCustomerId());
                $quote = $this->getCustomerQuote($order->getCustomerId());
                $quote->merge($quoteOrder);
                $quote->setIsActive(true);
                $this->cartRepository->save($quote);
            } else {
                $quote = $this->checkoutSession->getQuote();
                $quote->merge($quoteOrder);
                $quote->setIsActive(true);
                $shippingAddress = $quoteOrder->getShippingAddress()->exportCustomerAddress();
                $quote->getShippingAddress()->importCustomerAddressData($shippingAddress);
                $quote->setCustomerEmail($quoteOrder->getCustomerEmail());
                $this->cartRepository->save($quote);
            }

            $this->handleReloadCartCookie->buildReloadCartCookie();

            if ($order->canCancel()) {
                $this->logger->info('Order can cancel order, increment_id' . $order->getIncrementId(), ['order' => $order->getData()]);
                $order->cancel();
                $this->orderRepository->save($order);
                $this->logger->info('Order was canceled, increment_id' . $order->getIncrementId(), ['order' => $order->getData()]);
            } else {
                $allInvoiced = true;
                foreach ($order->getAllItems() as $item) {
                    if ($item->getQtyToInvoice()) {
                        $allInvoiced = false;
                        break;
                    }
                }

                $this->logger->alert(
                    'Order couldn\'t canceled, increment_id' . $order->getIncrementId(),
                    [
                        'is_cancelled' => $order->isCanceled(),
                        'can_unhold' => $order->canUnhold(),
                        'is_payment_review' => $order->isPaymentReview(),
                        'can_ReviewPayment' => $order->canReviewPayment(),
                        'canFetchPaymentReviewUpdate' => $order->canFetchPaymentReviewUpdate(),
                        'allInvoiced' => $allInvoiced,
                        'isCanceled' => $order->isCanceled(),
                        'state' => $order->getState(),
                        'action_flag_cancel' => $order->getActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_CANCEL)
                    ]
                );
            }

            return $resultRedirect->setPath('checkout/cart/index');
        } catch (NoSuchEntityException | AuthorizationException $e) {
            $this->logger->critical('Controller restore order: ' . $e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('/');
        } catch (\Exception $e) {
            $this->logger->critical('Controller restore order: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Has an error while restoring cart, please try later.'));
            return $resultRedirect->setPath('/');
        }
    }

    /**
     * @param $protectCode
     * @return \Magento\Framework\DataObject
     */
    private function getOrderByProtectCode($protectCode)
    {
        $orders = $this->orderCollectionFactory->create();
        $orders->addAttributeToFilter('protect_code', $protectCode);
        $orders->setPageSize(1)->setCurPage(1);

        return $orders->getFirstItem();
    }

    /**
     * @param $customerId
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerQuote($customerId)
    {
        try {
            return $this->cartRepository->getActiveForCustomer($customerId);
        } catch (NoSuchEntityException $e) {
            return $this->checkoutSession->getQuote();
        }
    }
}
