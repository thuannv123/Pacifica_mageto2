<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model;

class PaymentModeType extends \Magento\Payment\Model\Method\AbstractMethod
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => 'Test Mode'),
            array('value' => 0, 'label' => 'Live Mode'),
        );
    }
}
