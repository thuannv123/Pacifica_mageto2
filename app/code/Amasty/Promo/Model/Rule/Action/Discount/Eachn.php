<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Free Gift Base for Magento 2
 */

namespace Amasty\Promo\Model\Rule\Action\Discount;

/**
 * Action name: Auto add promo items with products
 */
class Eachn extends AbstractDiscount
{
    /**
     * {@inheritdoc}
     */
    protected function _getFreeItemsQty(
        \Magento\SalesRule\Model\Rule $rule,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item
    ) {
        return $this->getPromoQtyByStep($rule, $item);
    }
}
