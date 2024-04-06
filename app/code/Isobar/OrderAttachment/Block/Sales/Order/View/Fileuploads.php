<?php

namespace Isobar\OrderAttachment\Block\Sales\Order\View;

use Isobar\OrderAttachment\Helper\Data;
use Meetanshi\OrderUpload\Model\OrderUploadFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\ProductAlert\Model\StockFactory;
use Meetanshi\OrderUpload\Block\Sales\Order\View\Fileuploads as FileuploadsExtend;

class Fileuploads extends FileuploadsExtend
{
    private Data $helper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Data $helper
     * @param StockFactory $stockFactory
     * @param Registry $registry
     * @param CurrentCustomer $currentCustomer
     * @param OrderUploadFactory $orderUploadFactory
     * @param array $data
     */
    public function __construct(
        Context            $context,
        Session            $customerSession,
        Data               $helper,
        StockFactory       $stockFactory,
        Registry           $registry,
        CurrentCustomer    $currentCustomer,
        OrderUploadFactory $orderUploadFactory,
        array              $data = []
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $customerSession, $helper, $stockFactory, $registry, $currentCustomer, $orderUploadFactory, $data);
    }

    /**
     * @return bool
     */
    public function canShowOrderAttachment()
    {
        $order = $this->getOrder();
        $paymentMethodsAllowed = $this->getPaymentMethodsAllowed();

        if (!in_array($order->getPayment()->getMethod(), $paymentMethodsAllowed)) {
            return false;
        }

        return true;
    }

    /**
     * @return false|string[]
     */
    public function getPaymentMethodsAllowed()
    {
        $allowedPaymentMethods = $this->helper->allowPaymentMethods();
        return explode(',', $allowedPaymentMethods);
    }

}