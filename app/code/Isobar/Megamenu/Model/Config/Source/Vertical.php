<?php

namespace Isobar\Megamenu\Model\Config\Source;

class Vertical implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Horizontal Menu')],
            ['value' => 1, 'label' => __('Vertical  Menu')],
            ['value' => 2, 'label' => __('Horizontal And Vertical Menu')]
        ];
    }
}
