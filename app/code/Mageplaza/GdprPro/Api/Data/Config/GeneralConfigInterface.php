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

namespace Mageplaza\GdprPro\Api\Data\Config;

/**
 * Interface GeneralConfigInterface
 * @api
 */
interface GeneralConfigInterface extends \Mageplaza\Gdpr\Api\Data\Config\GeneralConfigInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ALLOW_VERIFY_PASSWORD        = "allow_verify_password";
    const ALLOW_DOWNLOAD               = "allow_download";
    const DOWNLOAD_CUSTOMER_MESSAGE    = "download_customer_message";
    const ALLOW_TAC_REGISTER_CUSTOMER  = "allow_tac_register_customer";
    const TAC_TITLE_CHECKBOX           = "tac_title_checkbox";
    const TAC_CONTENT                  = "tac_content";
    const AUTO_DELETE_CUSTOMER_ACCOUNT = "auto_delete_customer_account";
    const TIME_AUTO_DELETE             = "time_auto_delete";

    /**
     * @return string
     */
    public function getAllowVerifyPassword();

    /**
     * @return string
     */
    public function getAllowDownload();

    /**
     * @return string
     */
    public function getDownloadCustomerMessage();

    /**
     * @return string
     */
    public function getAllowTacRegisterCustomer();

    /**
     * @return string
     */
    public function getTacTitleCheckbox();

    /**
     * @return string
     */
    public function getTacContent();

    /**
     * @return string
     */
    public function getAutoDeleteCustomerAccount();

    /**
     * @return string
     */
    public function getTimeAutoDelete();
}
