<?php

namespace Marvelic\Review\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Review\Model\ReviewFactory;
use Magento\Customer\Model\Session;


class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        ReviewFactory $reviewFactory,
        Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $reviewFactory, $data);
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }
}