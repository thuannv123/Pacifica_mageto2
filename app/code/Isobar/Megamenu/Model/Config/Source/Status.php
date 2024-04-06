<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Isobar\Megamenu\Model\Config\Source
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Define value
     */
    const DISABLED = 0;
    const ENABLED = 1;

    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Enable')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disable')
            ]
        ];
    }
}
