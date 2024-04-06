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

namespace Mageplaza\GdprPro\Api\Data;

/**
 * Interface CookieInterface
 * @package Mageplaza\GdprPro\Api\Data
 */
interface CookieInterface
{
    /**
     * @return \Mageplaza\GdprPro\Api\Data\CookieOutput\CookieOutputInterface
     */
    public function getOutput();

    /**
     * @return string
     */
    public function getCheckCookieEnable();

    /**
     * @return string
     */
    public function getCookieName();

    /**
     * @return string
     */
    public function getCookieValue();

    /**
     * @return string
     */
    public function getCookieLifetime();

    /**
     * @return string
     */
    public function getNoCookiesUrl();
}
