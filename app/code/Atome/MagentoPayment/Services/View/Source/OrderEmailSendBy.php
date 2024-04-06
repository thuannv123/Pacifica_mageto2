<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\View\Source;

use Magento\Framework\Data\OptionSourceInterface;

class OrderEmailSendBy implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $result[] = ['value' => 'default', 'label' => 'Default'];
        $result[] = ['value' => 'atome', 'label' => 'Atome'];
        return $result;
    }
}

