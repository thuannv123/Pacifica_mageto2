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
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailConfigInterface
 * @api
 */
interface EmailConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE                  = "enable";
    const SENDER                  = "sender";
    const CONFIRMATION            = "confirmation";
    const TIME_BEFORE_AUTO_DELETE = "time_before_auto_delete";
    const TEMPLATE                = "template";
    const TEMPLATE_AFTER          = "template_after";
    const RECEIVER                = "receiver";

    /**
     * @return string
     */
    public function getEnable();

    /**
     * @return string
     */
    public function getSender();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\EmailConfig\ConfirmationInterface
     */
    public function getConfirmation();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\EmailConfig\BeforeDeleteAccountInterface
     */
    public function getBeforeDeleteAccount();

    /**
     * @return \Mageplaza\GdprPro\Api\Data\Config\EmailConfig\AdminNotificationEmailInterface
     */
    public function getAdminNotificationEmail();
}
