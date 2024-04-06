<?php

namespace Isobar\SalesRule\Model\Coupon\Quote;

use Magento\Quote\Api\Data\CartInterface;
use Magento\SalesRule\Model\Coupon\Usage\Processor as CouponUsageProcessor;
use Magento\SalesRule\Model\Coupon\Usage\UpdateInfo;
use Magento\SalesRule\Model\Coupon\Usage\UpdateInfoFactory;
use Magento\SalesRule\Model\Service\CouponUsagePublisher;

class UpdateCouponUsages extends \Magento\SalesRule\Model\Coupon\Quote\UpdateCouponUsages
{
    /**
     * @var CouponUsageProcessor
     */
    private $couponUsageProcessor;

    /**
     * @var UpdateInfoFactory
     */
    private $updateInfoFactory;

    /**
     * @var CouponUsagePublisher
     */
    private $couponUsagePublisher;


    /**
     * @param CouponUsageProcessor $couponUsageProcessor
     * @param CouponUsagePublisher $couponUsagePublisher
     * @param UpdateInfoFactory $updateInfoFactory
     */
    public function __construct(
        CouponUsageProcessor $couponUsageProcessor,
        CouponUsagePublisher $couponUsagePublisher,
        UpdateInfoFactory $updateInfoFactory
    ) {
        $this->couponUsageProcessor = $couponUsageProcessor;
        $this->couponUsagePublisher = $couponUsagePublisher;
        $this->updateInfoFactory = $updateInfoFactory;
        parent::__construct($couponUsagePublisher, $updateInfoFactory);
    }

    /**
     * Executes the current command
     *
     * @param CartInterface $quote
     * @param bool $increment
     * @return void
     */
    public function execute(CartInterface $quote, bool $increment): void
    {
        if (!$quote->getAppliedRuleIds()) {
            return;
        }

        /** @var UpdateInfo $updateInfo */
        $updateInfo = $this->updateInfoFactory->create();
        $updateInfo->setAppliedRuleIds(explode(',', $quote->getAppliedRuleIds()));
        $updateInfo->setCouponCode((string)$quote->getCouponCode());
        $updateInfo->setCustomerId((int)$quote->getCustomerId());
        $updateInfo->setIsIncrement($increment);

        $this->couponUsageProcessor->process($updateInfo);
    }
}
