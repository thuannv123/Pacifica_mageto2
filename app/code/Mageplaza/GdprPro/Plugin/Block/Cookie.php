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

namespace Mageplaza\GdprPro\Plugin\Block;

use Closure;
use Magento\Cookie\Block\Html\Notices;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class Cookie
 *
 * @package Mageplaza\GdprPro\Plugin\Block
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
     * @param Notices $object
     * @param Closure $proceed
     *
     * @return mixed|string
     */
    public function aroundToHtml(Notices $object, Closure $proceed)
    {
        if ($this->helper->isEnableCookieRestrictrion()) {
            return '';
        }

        return $proceed();
    }
}
