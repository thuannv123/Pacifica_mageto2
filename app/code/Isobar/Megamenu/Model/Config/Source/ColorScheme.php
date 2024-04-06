<?php

namespace Isobar\Megamenu\Model\Config\Source;

class ColorScheme implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */

    public function toOptionArray()
    {
        return [
            ['value' => 'custom', 'label' => __('Custom')],
            ['value' => 'orange_peel', 'label' => __('Orange Peel')],
            ['value' => 'ecru', 'label' => __('Ecru')],
            ['value' => 'feijoa', 'label' => __('Feijoa')],
            ['value' => 'jambalaya', 'label' => __('White Smoke - Jambalaya')],
            ['value' => 'prussian_blue', 'label' => __('Prussian Blue')],
            ['value' => 'night_rider', 'label' => __('Night Rider')],
            ['value' => 'eclipse', 'label' => __('Black - Eclipse')],
            ['value' => 'pacific_blue', 'label' => __('Pacific Blue')],
            ['value' => 'radical_red', 'label' => __('Radical Red')],
            ['value' => 'iris_blue', 'label' => __('Iris Blue')],
            ['value' => 'cinnabar', 'label' => __('Black - Cinnabar')],
            ['value' => 'atlantis', 'label' => __('White Smoke - Atlantis')],
            ['value' => 'dark_red', 'label' => __('Dark Red')],
            ['value' => 'lima', 'label' => __('Lima')],
            ['value' => 'radical_red_2', 'label' => __('Radical Red 2')],
            ['value' => 'paris_m', 'label' => __('Paris M')],
            ['value' => 'clay_creek', 'label' => __('Clay Creek')],
            ['value' => 'whisper', 'label' => __('Whisper')],
            ['value' => 'pumpkin', 'label' => __('Pumpkin')],
            ['value' => 'surfie_green', 'label' => __('Surfie Green')],
            ['value' => 'scarlet', 'label' => __('Scarlet')]
        ];
    }
}
