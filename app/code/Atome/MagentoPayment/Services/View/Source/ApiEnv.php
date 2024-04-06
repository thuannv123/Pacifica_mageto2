<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\View\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ApiEnv implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $result[] = ['value' => 'test', 'label' => 'Test'];
        $result[] = ['value' => 'production', 'label' => 'Production'];
        return $result;
    }
}

