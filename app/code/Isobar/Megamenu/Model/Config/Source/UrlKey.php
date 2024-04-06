<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class UrlKey
 * @package Isobar\Megamenu\Model\Config\Source
 */
class UrlKey implements OptionSourceInterface
{
    /**#@+
     * Define value
     */
    const NO = 0;
    const LINK = 1;
    const EXTERNAL_URL = 4;
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NO, 'label' => __('Choose an option')],
            ['value' => self::LINK, 'label' => __('Internal URL')],
            ['value' => self::EXTERNAL_URL, 'label' => __('External URL')]
        ];
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return array_column($this->toOptionArray(), 'value');
    }
}
