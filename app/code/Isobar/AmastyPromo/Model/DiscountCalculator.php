<?php

namespace Isobar\AmastyPromo\Model;

use Amasty\Promo\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Amasty\Promo\Model\Rule;
use Magento\Store\Model\Store;

class DiscountCalculator extends \Amasty\Promo\Model\DiscountCalculator
{
    /**
     * @param Store $store
     * @param Config $config
     * @param \Magento\Tax\Model\Config $taxConfig
     */
    public function __construct(Store $store, Config $config, \Magento\Tax\Model\Config $taxConfig)
    {
        $this->taxConfig = $taxConfig;
        parent::__construct($store, $config, $taxConfig);
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param Item $item
     *
     * @return float|int|mixed|null
     * @throws LocalizedException
     */
    public function getBaseDiscountAmount(\Magento\SalesRule\Model\Rule $rule, Item $item)
    {
        /** @var Rule $promoRule */
        $promoRule = $rule->getAmpromoRule();
        $promoDiscount = trim($promoRule->getItemsDiscount() ?? 0);

        /** Apply Discount On Prices Including Tax */
        if ($this->taxConfig->discountTax()) {
            $itemPrice = $item->getBasePriceInclTax();
        } else {
            $itemPrice = $item->getBasePrice();
        }

        switch (true) {
            case $promoDiscount === "100%":
            case $promoDiscount == "":
                $baseDiscount = $itemPrice;
                break;

            case strpos($promoDiscount, "%") !== false:
                $baseDiscount = $this->getPercentDiscount($itemPrice, $promoDiscount);
                break;

            case strpos($promoDiscount, "-") !== false:
                $baseDiscount = $this->getFixedDiscount($itemPrice, $promoDiscount);
                break;

            default:
                $baseDiscount = $this->getFixedPrice($itemPrice, $promoDiscount);
                break;
        }

        $baseDiscount = $this->getDiscountAfterMinimalPrice($promoRule, $itemPrice, $baseDiscount) * $item->getQty();

        return $baseDiscount;
    }

    /**
     * @param $itemPrice
     * @param $promoDiscount
     * @return mixed
     */
    private function getPercentDiscount($itemPrice, $promoDiscount)
    {
        $percent = (float)str_replace("%", "", $promoDiscount);
        $discount = $itemPrice * $percent / 100;

        return $discount;
    }

    /**
     * @param $itemPrice
     * @param $promoDiscount
     * @return mixed
     */
    private function getFixedDiscount($itemPrice, $promoDiscount)
    {
        $discount = abs($promoDiscount);
        if ($discount > $itemPrice) {
            $discount = $itemPrice;
        }

        return $discount;
    }

    /**
     * @param $itemPrice
     * @param $promoDiscount
     * @return mixed
     */
    private function getFixedPrice($itemPrice, $promoDiscount)
    {
        $discount = $itemPrice - (float)$promoDiscount;
        if ($discount < 0) {
            $discount = 0;
        }

        return $discount;
    }

    /**
     * @param Rule $promoRule
     * @param float $itemPrice
     * @param float $discount
     *
     * @return mixed
     * @throws LocalizedException
     */
    private function getDiscountAfterMinimalPrice(Rule $promoRule, $itemPrice, $discount)
    {
        $minimalPrice = (float)$promoRule->getMinimalItemsPrice();

        if ($itemPrice > $minimalPrice && $itemPrice - $discount < $minimalPrice) {
            $discount = $itemPrice - $minimalPrice;
        }

        return $discount;
    }
}
