<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Mageplaza\GdprPro\Model\Config\Source
 */
class Status implements ArrayInterface
{
    const CANCEL      = 0;
    const ACTIVE      = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ACTIVE, 'label' => __('Active')],
            ['value' => self::CANCEL, 'label' => __('Cancel')]
        ];
    }
}
