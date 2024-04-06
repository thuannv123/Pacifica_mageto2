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

namespace Mageplaza\GdprPro\Helper;

use Exception;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Integration\Model\CustomUserContext;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Gdpr\Helper\Data as GdprData;
use Mageplaza\GdprPro\Model\Config\Source\CountryType;

/**
 * Class Data
 * @package Mageplaza\GdprPro\Helper
 */
class Data extends GdprData
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TokenFactory
     */
    protected $_tokenModelFactory;

    /**
     * @var Serialize
     */
    protected $_serialize;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     * @param Serialize $serialize
     * @param TokenFactory $tokenFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Request $request,
        Serialize $serialize,
        TokenFactory $tokenFactory
    ) {
        $this->request            = $request;
        $this->_tokenModelFactory = $tokenFactory;
        $this->_serialize         = $serialize;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAnonymiseAccountPurchase(
        $storeId
    ) {
        return $this->getModuleConfig('anonymise_account/order_processing_enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAllowAnonymiseAddress(
        $storeId
    ) {
        return $this->getModuleConfig('anonymise_account/order_address_enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAnonymiseAddressOption(
        $storeId
    ) {
        return $this->getModuleConfig('anonymise_account/order_address_fields', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getAllowDeleteAbandonedcart(
        $storeId
    ) {
        return $this->isEnabled($storeId)
            && $this->getModuleConfig('anonymise_account/allow_delete_abandonedcart', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEnableTermAndCondition(
        $storeId = null
    ) {
        return $this->getConfigGeneral('allow_tac_register_customer', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTermAndConditionContent(
        $storeId = null
    ) {
        return $this->getConfigGeneral('tac_content', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTitleCheckboxTAC(
        $storeId = null
    ) {
        return $this->getConfigGeneral('tac_title_checkbox', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function allowVerifyPassword(
        $storeId = null
    ) {
        return $this->getConfigGeneral('allow_verify_password', $storeId);
    }

    /**
     * @param $customerId
     * @return Token
     */
    public function createTokencustomer(
        $customerId
    ) {
        $customerToken = $this->_tokenModelFactory->create();
        $objectManager = $this->objectManager;

        if ($this->versionCompare('2.4.4')) {
            $issuer             = $objectManager->get(\Magento\Integration\Model\OpaqueToken\Issuer::class);
            $tokenParams        = $objectManager->get(\Magento\Integration\Api\Data\UserTokenParametersInterface::class);

            $customerToken->loadByToken($issuer->create(
                new CustomUserContext((int) $customerId, UserContextInterface::USER_TYPE_CUSTOMER),
                $tokenParams
            ));

            return $customerToken;
        } else {
            return $customerToken->createCustomerToken($customerId);
        }
    }

    /**
     * @param int $customerId
     *
     * @return string
     */
    public function getTokencustomer(
        $customerId
    ) {
        $customerToken = $this->_tokenModelFactory->create()->getCollection()->addFieldToFilter(
            'customer_id',
            $customerId
        )->getLastItem();
        if (!$customerToken->getId()) {
            $customerToken = $this->createTokencustomer($customerId);
        }

        return $customerToken->getToken();
    }

    /********************************************** Cookie Configuration ***********************************************
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCookieConfig(
        $code,
        $storeId = null
    ) {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('cookie' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnableCookieRestrictrion(
        $storeId = null
    ) {
        if (!$this->isEnabled($storeId) || !$this->getCookieConfig('enable', $storeId)) {
            return false;
        }

        $applyFor = $this->getCookieConfig('apply_for', $storeId);
        if ($applyFor == CountryType::ALL) {
            return true;
        }

        try {
            $countryArray = $this->getGeoIpCountry();
            if (!isset($countryArray['geoplugin_countryCode'])) {
                throw new LocalizedException(__('Cannot get geoIp data.'));
            }

            if ($applyFor == CountryType::EU) {
                return isset($countryArray['geoplugin_inEU']) && $countryArray['geoplugin_inEU'];
            }

            $specificCountries = $this->getCookieConfig('specific_country', $storeId);

            return $specificCountries
                && in_array($countryArray['geoplugin_countryCode'], explode(',', $specificCountries));
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());

            return true;
        }
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    protected function getGeoIpCountry()
    {
        $geoUrl   = 'http://www.geoplugin.net/php.gp';
        $clientIp = $this->request->getClientIp();

        if ($clientIp != '127.0.0.1') {
            $geoUrl .= '?ip=' . $clientIp;
        }
        $geoData = file_get_contents($geoUrl);

        return $this->_serialize->unserialize($geoData);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCookieMessage(
        $storeId = null
    ) {
        $message   = $this->getCookieConfig('message', $storeId);
        $policyUrl = $this->getCookiePolicyUrl($storeId);

        return str_replace('%policyUrl', $policyUrl, $message);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getCookiePolicyUrl(
        $storeId = null
    ) {
        return $this->_getUrl($this->getCookieConfig('policy_page', $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCookieButtonText(
        $storeId = null
    ) {
        return $this->getCookieConfig('button_text', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isBlockAccess(
        $storeId = null
    ) {
        return $this->getCookieConfig('block_access', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getHtmlClass(
        $storeId = null
    ) {
        return 'gdpr-cookie ' . $this->getCookieConfig('location', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomCss(
        $storeId = null
    ) {
        return $this->getCookieConfig('custom_css', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function allowDownload(
        $storeId = null
    ) {
        return $this->isEnabled($storeId) && $this->getConfigGeneral('allow_download', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDownloadMessage(
        $storeId = null
    ) {
        return $this->getConfigGeneral('download_customer_message', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isAutoDeleteAccount(
        $storeId = null
    ) {
        return $this->isEnabled($storeId) && $this->getConfigGeneral('auto_delete_customer_account', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTimeAutoDelete(
        $storeId = null
    ) {
        return $this->getConfigGeneral('time_auto_delete', $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailConfig(
        $code,
        $storeId = null
    ) {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('email' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSenderEmail(
        $storeId = null
    ) {
        return $this->getEmailConfig('sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEmailEnable(
        $storeId = null
    ) {
        return $this->getEmailConfig('enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDayBeforeAutoDelete(
        $storeId = null
    ) {
        return $this->getEmailConfig('before_delete_account/time_before_auto_delete', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailTemplateBeforeDelete(
        $storeId = null
    ) {
        return $this->getEmailConfig('before_delete_account/template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailTemplateAfterDelete(
        $storeId = null
    ) {
        return $this->getEmailConfig('before_delete_account/template_after', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnabledEmailBeforeDelete(
        $storeId = null
    ) {
        return $this->isEmailEnable($storeId) && $this->getEmailConfig('before_delete_account/enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailConfirmTemplate(
        $storeId = null
    ) {
        return $this->getEmailConfig('confirmation/template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function getEnableEmailConfirm(
        $storeId = null
    ) {
        return $this->isEmailEnable($storeId) && $this->getEmailConfig('confirmation/enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function getEnableEmailAdminNof($storeId = null)
    {
        return $this->isEmailEnable($storeId) && $this->getEmailConfig('admin_notification_email/enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getEmailAdminNofTemplate($storeId = null)
    {
        return $this->getEmailConfig('admin_notification_email/template', $storeId);
    }

    /**
     * @param int $storeId
     *
     * @return array
     */
    public function getReceiverInf($storeId)
    {
        $receiver = $this->getEmailConfig('admin_notification_email/receiver', $storeId);

        return [
                'email' => $this->getConfigValue('trans_email/ident_' . $receiver . '/email', $storeId),
                'name' => $this->getConfigValue('trans_email/ident_' . $receiver . '/name', $storeId),
        ];
    }
}
