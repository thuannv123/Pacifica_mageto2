<?php

namespace Isobar\ZipcodeSuggestion\Model\Config\Source;

class SuggestionCountry implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'TH', 'label' => __('Thailand')]
        ];
    }
}
