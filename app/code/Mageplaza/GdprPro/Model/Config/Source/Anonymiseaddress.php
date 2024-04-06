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
 * Class Anonymiseaddress
 *
 * @package Mageplaza\GdprPro\Model\Config\Source
 */
class Anonymiseaddress implements ArrayInterface
{
    /**
     * get anonymise address.
     *
     * @return array []
     */
    public function toOptionArray()
    {
        $purchase_address = [
            'street'         => 'Street',
            'city'           => 'City',
            'state_province' => 'State/Province',
            'postcode'       => 'Post Code',
            'telephone'      => 'Telephone',
            'first_name'     => 'First Name',
            'last_name'      => 'Last Name'
        ];
        $array_list       = [];
        foreach ($purchase_address as $key => $value) {
            $array_list[] = ['value' => $key, 'label' => $value];
        }

        return $array_list;
    }
}
