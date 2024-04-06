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


namespace Mageplaza\GdprPro\GraphQl\Model\Resolver;

use Magento\Cookie\Helper\Cookie as HelperCookie;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class Cookie
 * @package Mageplaza\GdprPro\GraphQl\Model\Resolver
 */
class Cookie implements ResolverInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var \Mageplaza\GdprPro\Model\Api\Cookie
     */
    protected $_cookieHelper;

    /**
     * @var string
     */
    protected $_baseUrl;

    /**
     * @param Data $helperData
     * @param HelperCookie $cookieHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Data $helperData,
        HelperCookie $cookieHelper,
        UrlInterface $urlBuilder
    ) {
        $this->_helperData   = $helperData;
        $this->_cookieHelper = $cookieHelper;
        $this->urlBuilder    = $urlBuilder;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array
     * @throws GraphQlAuthorizationException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->_helperData->isEnabled()) {
            throw new GraphQlAuthorizationException(__('The Gdpr is disabled.'));
        }
        $store          = $context->getExtensionAttributes()->getStore();
        $storeId        = $store->getId();
        $this->_baseUrl = $store->getBaseUrl();

        return [
            'output'              => $this->getOutput($storeId),
            'check_cookie_enable' => $this->_helperData->getCookieConfig('enable', $storeId),
            'cookie_name'         => HelperCookie::IS_USER_ALLOWED_SAVE_COOKIE,
            'cookie_value'        => $this->_cookieHelper->getAcceptedSaveCookiesWebsiteIds(),
            'cookie_lifetime'     => $this->_cookieHelper->getCookieRestrictionLifetime(),
            'no_cookies_url'      => $this->removeDomain($this->urlBuilder->getUrl('cookie/index/noCookies'))
        ];
    }

    /**
     * @param $storeId
     *
     * @return array
     */
    protected function getOutput($storeId)
    {
        return [
            "html_class"         => $this->_helperData->getHtmlClass($storeId),
            "cookie_message"     => $this->_helperData->getCookieConfig('message', $storeId),
            "cookie_button_text" => $this->_helperData->getCookieButtonText($storeId),
            "cookie_policy_url"  => $this->removeDomain($this->_helperData->getCookiePolicyUrl($storeId)),
            "custom_css"         => $this->_helperData->getCustomCss($storeId),
            "is_block_access"    => $this->_helperData->isBlockAccess($storeId)
        ];
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function removeDomain($url)
    {
        return str_replace($this->_baseUrl, '', $url);
    }
}
