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
 * Class CookieLocation
 *
 * @package Mageplaza\GdprPro\Model\Config\Source
 */
class CookieLocation implements ArrayInterface
{
    const LOCATION_TOP    = 'top';
    const LOCATION_BOTTOM = 'bottom';

    /**
     * get available locations.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::LOCATION_TOP    => __('Top of the page'),
            self::LOCATION_BOTTOM => __('Bottom of the page'),
        ];
    }
}
