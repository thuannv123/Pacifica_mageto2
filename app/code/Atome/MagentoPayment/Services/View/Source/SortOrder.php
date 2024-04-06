<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\View\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SortOrder implements OptionSourceInterface
{
    public function toOptionArray()
    {

        $options = [];
        for ($i = 0; $i <= 10; $i++) {
            $options[] = [
                'value' => $i,
                'label' => $i === 0 ? '0 (top of the list)' : $i
            ];
        }

        return $options;
    }
}

