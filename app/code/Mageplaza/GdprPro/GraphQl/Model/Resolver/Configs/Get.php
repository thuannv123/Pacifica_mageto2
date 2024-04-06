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
 * @package     Mageplaza_Gdpr
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\GdprPro\GraphQl\Model\Resolver\Configs;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\Gdpr\GraphQl\Model\Resolver\Configs\Get as AbstractGet;
use Mageplaza\Gdpr\Model\Api\Data\Config\GeneralConfig;
use Mageplaza\GdprPro\Helper\Anonymise;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class Get
 * @package Mageplaza\GdprPro\GraphQl\Model\Resolver\Configs
 */
class Get extends AbstractGet
{
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var Anonymise
     */
    protected $helperAnonymise;

    /**
     * Get constructor.
     *
     * @param Data $helperData
     * @param Anonymise $helperAnonymise
     * @param GeneralConfig $generalConfig
     */
    public function __construct(
        Data $helperData,
        Anonymise $helperAnonymise,
        GeneralConfig $generalConfig
    ) {
        $this->helperData      = $helperData;
        $this->helperAnonymise = $helperAnonymise;
        $this->generalConfig   = $generalConfig;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array|Value|GeneralConfig|mixed
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $store   = $context->getExtensionAttributes()->getStore();
        $storeId = $store->getId();

        if (!$this->helperData->isEnabled($storeId)) {
            throw new GraphQlInputException(__('Gdpr is disabled.'));
        }

        return $this->getConfig($storeId);
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getGeneralConfig($storeId)
    {
        return [
            'enabled'                      =>
                $this->helperData->getConfigGeneral('enabled', $storeId),
            'allow_delete_customer'        =>
                $this->helperData->getConfigGeneral('allow_delete_customer', $storeId),
            'delete_customer_message'      =>
                $this->helperData->getConfigGeneral('delete_customer_message', $storeId),
            'allow_delete_default_address' =>
                $this->helperData->getConfigGeneral('allow_delete_default_address', $storeId),
            'allow_verify_password'        =>
                $this->helperData->getConfigGeneral('allow_verify_password', $storeId),
            'allow_download'               =>
                $this->helperData->getConfigGeneral('allow_download', $storeId),
            'download_customer_message'    =>
                $this->helperData->getConfigGeneral('download_customer_message', $storeId),
            'allow_tac_register_customer'  =>
                $this->helperData->getConfigGeneral('allow_tac_register_customer', $storeId),
            'tac_title_checkbox'           =>
                $this->helperData->getConfigGeneral('tac_title_checkbox', $storeId),
            'tac_content'                  =>
                $this->helperData->getConfigGeneral('tac_content', $storeId),
            'auto_delete_customer_account' =>
                $this->helperData->getConfigGeneral('auto_delete_customer_account', $storeId),
            'time_auto_delete'             =>
                $this->helperData->getConfigGeneral('time_auto_delete', $storeId)
        ];
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getCookieConfig($storeId)
    {
        return [
            'enable'           => $this->helperData->getCookieConfig('enable',$storeId),
            'block_access'     => $this->helperData->isBlockAccess($storeId),
            'message'          => $this->helperData->getCookieMessage($storeId),
            'policy_page'      => $this->helperData->getCookiePolicyUrl($storeId),
            'button_text'      => $this->helperData->getCookieButtonText($storeId),
            'apply_for'        => $this->helperData->getCookieConfig('apply_for', $storeId),
            'location'         => $this->helperData->getCookieConfig('location', $storeId),
            'specific_country' => $this->helperData->getCookieConfig('specific_country', $storeId),
            'custom_css'       => $this->helperData->getCustomCss($storeId),
        ];
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getAnonymiseAccountConfig($storeId)
    {
        return [
            'allow_delete_abandonedcart' => $this->helperData->getModuleConfig('anonymise_account/allow_delete_abandonedcart',$storeId),
            'order_processing_enable'    => $this->helperData->getAnonymiseAccountPurchase($storeId),
            'firstname'                  => $this->helperAnonymise->getAnonymiseFirstname($storeId),
            'lastname'                   => $this->helperAnonymise->getAnonymiseLastName($storeId),
            'email'                      => $this->helperAnonymise->getAnonymiseEmail($storeId),
            'order_address_enable'       => $this->helperData->getAllowAnonymiseAddress($storeId),
            'order_address_fields'       => $this->helperData->getAnonymiseAddressOption($storeId),
        ];
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getEmailConfig($storeId)
    {
        return [
            'enable'                   => $this->helperData->isEmailEnable($storeId),
            'sender'                   => $this->helperData->getSenderEmail($storeId),
            'confirmation'             => [
                'enable'   => $this->helperData->getEmailConfig('confirmation/enable',$storeId),
                'template' => $this->helperData->getEmailConfirmTemplate($storeId),
            ],
            'before_delete_account'    => [
                'enable'                  => $this->helperData->getEmailConfig('before_delete_account/enable',$storeId),
                'time_before_auto_delete' => $this->helperData->getDayBeforeAutoDelete($storeId),
                'template'                => $this->helperData->getEmailTemplateBeforeDelete($storeId),
                'template_after'          => $this->helperData->getEmailTemplateAfterDelete($storeId),
            ],
            'admin_notification_email' => [
                'enable'   => $this->helperData->getEmailConfig('admin_notification_email/enable',$storeId),
                'receiver' => $this->helperData->getReceiverInf($storeId),
                'template' => $this->helperData->getEmailAdminNofTemplate($storeId),

            ]
        ];
    }

    /**
     * get All Config
     *
     * @param int $storeId
     *
     * @return array
     */
    private function getConfig($storeId)
    {
        return [
            'email'             => $this->getEmailConfig($storeId),
            'cookieRestriction' => $this->getCookieConfig($storeId),
            'anonymiseAccount'  => $this->getAnonymiseAccountConfig($storeId),
            'general'           => $this->getGeneralConfig($storeId),
        ];
    }
}
