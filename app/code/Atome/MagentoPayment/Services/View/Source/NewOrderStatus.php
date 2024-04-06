<?php

/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2022 Atome
 */

namespace Atome\MagentoPayment\Services\View\Source;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;

class NewOrderStatus implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $orderStatus = ObjectManager::getInstance()->create(Collection::class);
        $options = $orderStatus->toOptionArray();

        $newOptions = array_values(
            array_filter($options, function ($option) {
                return !in_array(
                    $option['value'],
                    [
                        'canceled',
                        'closed',
                        'fraud',
                        'paypal_canceled_reversal',
                        'paypal_reversed',
                        'pending_paypal',
                        'processing',
                        'complete',
                        'payment_review',
                    ]
                );
            }));

        // Place the pending_payment status in the first position to prevent incorrect data caused by old version upgrade
        usort($newOptions, function ($a, $b) {
            if ($a['value'] === 'pending') {
                return -1;
            }
            return 1;
        });

        return $newOptions;
    }
}
