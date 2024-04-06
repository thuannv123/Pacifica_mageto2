<?php

namespace Isobar\ImportExport\Plugin;

use Firebear\ImportExport\Ui\Component\Listing\Column\Import\Source\Configurable\Type\Options;
use Isobar\ImportExport\Api\ConfigurableTypeOptionsInterface;

class ConfigurableTypeOptions
{
    /**
     * @param Options $subject
     * @param array $result
     * @return array
     */
    public function afterToOptionArray(Options $subject, array $result): array
    {
        $result[] = [
            'label' => __('Сreate configurable product by the same attribute of simple products, split variation by color'),
            'value' => ConfigurableTypeOptionsInterface::SPLIT_COLOR_ATTRIBUTE
        ];

        $result[] = [
            'label' => __('Сreate config product by same attribute of simple products (Keds only)'),
            'value' => ConfigurableTypeOptionsInterface::SPLIT_WITHOUT_COLOR_ATTRIBUTE
        ];

        return $result;
    }
}
