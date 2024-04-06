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
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Api;

/**
 * Interface RequestsManagementInterface
 * @package Mageplaza\GdprPro\Api
 */
interface RequestsManagementInterface
{

    /**
     * @param string $type
     *
     * @return void
     */
    public function downLoadCustomerData($type);

    /**
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($email, $password);

    /**
     * @return \Mageplaza\GdprPro\Api\Data\CookieInterface
     */
    public function cookie();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\ConfigInterface
     */
    public function getConfig();
}
