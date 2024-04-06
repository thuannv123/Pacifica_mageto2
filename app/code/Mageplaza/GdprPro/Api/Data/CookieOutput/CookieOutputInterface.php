<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Gdpr
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Api\Data\CookieOutput;

/**
 * Interface CookieOutputInterface
 * @package Mageplaza\GdprPro\Api\Data\CookieOutput
 */
interface CookieOutputInterface
{
    /**
     * @return string
     */
    public function getHtmlClass();

    /**
     * @return string
     */
    public function getCookieMessage();

    /**
     * @return string
     */
    public function getCookieButtonText();

    /**
     * @return string
     */
    public function getCookiePolicyUrl();

    /**
     * @return string
     */
    public function getCustomCss();

    /**
     * @return string
     */
    public function getIsBlockAccess();
}
