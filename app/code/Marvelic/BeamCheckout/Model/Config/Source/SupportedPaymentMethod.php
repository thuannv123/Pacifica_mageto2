<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SupportedPaymentMethod implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'creditCard', 'label' => __('Credit Card')],
            ['value' => 'eWallet', 'label' => __('eWallet')],
            ['value' => 'internetBanking', 'label' => __('Internet Banking')],
            ['value' => 'installmentsCc', 'label' => __('Installment (via credit card)')],
            ['value' => 'qrThb', 'label' => __('PromptPay QR')]
        ];
    }
}
