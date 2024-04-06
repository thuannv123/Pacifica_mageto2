<?php

namespace Isobar\ZipcodeSuggestion\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\Order;

class SubmitCustomData implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    protected QuoteRepository $quoteRepository;

    /**
     * SubmitCustomData constructor.
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        /** @var Order $order */
        $order = $observer->getOrder();
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $billingAddress = $order->getBillingAddress();
        $billingAddress = $this->transformCustomData($quote->getBillingAddress(), $billingAddress);
        $order->setBillingAddress($billingAddress);
        if ($order->getShippingAddress()) {
            $shippingAddress = $order->getShippingAddress();
            $shippingAddress = $this->transformCustomData($quote->getShippingAddress(), $shippingAddress);
            $order->setShippingAddress($shippingAddress);
        }
    }

    /**
     * @param $quoteAddress
     * @param $saleAddress
     * @return mixed
     */
    public function transformCustomData($quoteAddress, $saleAddress)
    {
        $saleAddress->setSubdistrict($quoteAddress->getSubdistrict());
        return $saleAddress;
    }
}
