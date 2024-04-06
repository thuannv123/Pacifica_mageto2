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
interface AnonymiseConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ALLOW_DELETE_ABANDONEDCART = "allow_delete_abandonedcart";
    const ORDER_PROCESSING_ENABLE    = "order_processing_enable";
    const FIRSTNAME                  = "firstname";
    const EMAIL                      = "email";
    const LASTNAME                   = "lastname";
    const ORDER_ADDRESS_ENABLE       = "order_address_enable";
    const ORDER_ADDRESS_FIELDS       = "order_address_fields";

    /**
     * @return string
     */
    public function getAllowDeleteAbandonedcart();

    /**
     * @return string
     */
    public function getOrderProcessingEnable();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getOrderAddressEnable();

    /**
     * @return mixed
     */
    public function getOrderAddressFields();
}
