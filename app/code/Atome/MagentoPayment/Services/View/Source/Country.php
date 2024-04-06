<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\View\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Country implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $result[] = ['value' => 'sg', 'label' => 'Singapore'];
        $result[] = ['value' => 'hk', 'label' => 'HongKong'];
        $result[] = ['value' => 'my', 'label' => 'Malaysia'];
        $result[] = ['value' => 'id', 'label' => 'Indonesia'];
        $result[] = ['value' => 'th', 'label' => 'Thailand'];
        $result[] = ['value' => 'vn', 'label' => 'Vietnam'];
        $result[] = ['value' => 'ph', 'label' => 'Philippines'];
        $result[] = ['value' => 'tw', 'label' => 'Taiwan'];
        $result[] = ['value' => 'jp', 'label' => 'Japan'];
        return $result;
    }
}

