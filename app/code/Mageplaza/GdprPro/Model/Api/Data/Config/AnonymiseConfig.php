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

namespace Mageplaza\GdprPro\Model\Api\Data\Config;

use Magento\Framework\DataObject;
use Magento\Framework\Webapi\Rest\Request;
use Mageplaza\GdprPro\Api\Data\Config\AnonymiseConfigInterface;
use Mageplaza\GdprPro\Helper\Anonymise;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class AnonymiseConfig
 * @package Mageplaza\GdprPro\Model\Api\Data\Config
 */
class AnonymiseConfig implements AnonymiseConfigInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var Anonymise
     */
    private $helperAnonymise;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $storeId;

    /**
     * AnonymiseConfig constructor.
     *
     * @param Data $helperData
     * @param Anonymise $helperAnonymise
     * @param Request $request
     */
    public function __construct(
        Data $helperData,
        Anonymise $helperAnonymise,
        Request $request
    ) {
        $this->helperData      = $helperData;
        $this->helperAnonymise = $helperAnonymise;
        $this->request         = $request;
        if (isset($this->request->getBodyParams()['storeId'])) {
            $this->storeId = $this->request->getBodyParams()['storeId'];
        }
    }

    /**
     * @return DataObject
     */
    public function getConfig()
    {
        return new DataObject([
            "allow_delete_abandonedcart" => $this->getAllowDeleteAbandonedcart(),
            "order_processing_enable"    => $this->getOrderProcessingEnable(),
            "first_name"                 => $this->getFirstName(),
            "last_name"                  => $this->getLastName(),
            "email"                      => $this->getEmail(),
            "order_address_enable"       => $this->getOrderAddressEnable(),
            "order_address_fields"       => $this->getOrderAddressFields()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getAllowDeleteAbandonedcart()
    {
        return $this->helperData->getModuleConfig('anonymise_account/allow_delete_abandonedcart', 0);
    }

    /**
     * @inheritDoc
     */
    public function getOrderProcessingEnable()
    {
        return $this->helperData->getAnonymiseAccountPurchase($this->request->getParam('storeId'));
    }

    /**
     * @inheritDoc
     */
    public function getFirstName()
    {
        return $this->helperAnonymise->getAnonymiseFirstname($this->request->getParam('storeId'));
    }

    /**
     * @inheritDoc
     */
    public function getLastName()
    {
        return $this->helperAnonymise->getAnonymiseLastName($this->request->getParam('storeId'));
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->helperAnonymise->getAnonymiseEmail($this->request->getParam('storeId'));
    }

    /**
     * @inheritDoc
     */
    public function getOrderAddressEnable()
    {
        return $this->helperData->getAllowAnonymiseAddress($this->request->getParam('storeId'));
    }

    /**
     * @inheritDoc
     */
    public function getOrderAddressFields()
    {
        return $this->helperData->getAnonymiseAddressOption($this->request->getParam('storeId'));
    }
}
