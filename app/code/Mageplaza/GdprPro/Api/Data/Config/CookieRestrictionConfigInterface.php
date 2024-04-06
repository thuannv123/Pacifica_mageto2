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
 * Interface AnonymiseConfigInterface
 * @api
 */
interface CookieRestrictionConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE           = "enable";
    const BLOCK_ACCESS     = "block_access";
    const MESSAGE          = "message";
    const POLICY_PAGE      = "policy_page";
    const BUTTON_TEXT      = "button_text";
    const APPLY_FOR        = "apply_for";
    const LOCATION         = "location";
    const SPECIFIC_COUNTRY = "specific_country";
    const CUSTOM_CSS       = "custom_css";

    /**
     * @return string
     */
    public function getEnable();

    /**
     * @return string
     */
    public function getBlockAccess();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getPolicyPage();

    /**
     * @return string
     */
    public function getButtonText();

    /**
     * @return string
     */
    public function getApplyFor();

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @return string
     */
    public function getSpecificCountry();

    /**
     * @return string
     */
    public function getCustomCss();
}
