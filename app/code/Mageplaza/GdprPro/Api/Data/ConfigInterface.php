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
 * Interface ConfigInterface
 * @package Mageplaza\GdprPro\Api\Data
 */
interface ConfigInterface
{
    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\AnonymiseConfigInterface
     */
    public function getAnonymiseConfig();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\CookieRestrictionConfigInterface
     */
    public function getCookieRestrictionConfig();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\EmailConfigInterface
     */
    public function getEmailConfig();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\GeneralConfigInterface
     */
    public function getGeneralConfig();
}