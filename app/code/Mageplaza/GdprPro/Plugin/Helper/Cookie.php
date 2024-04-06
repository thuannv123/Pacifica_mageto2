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

namespace Mageplaza\GdprPro\Plugin\Helper;

use Mageplaza\GdprPro\Helper\Data;

/**
 * Class Cookie
 *
 * @package Mageplaza\GdprPro\Plugin\Helper
 */
class Cookie
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Cookie constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Cookie\Helper\Cookie $object
     * @param $result
     *
     * @return bool
     */
    public function afterIsCookieRestrictionModeEnabled(\Magento\Cookie\Helper\Cookie $object, $result)
    {
        if ($this->helper->isEnableCookieRestrictrion()) {
            return true;
        }

        return $result;
    }
}
