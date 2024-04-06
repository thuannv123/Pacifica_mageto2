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
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Model\Api;

use Magento\Cookie\Helper\Cookie as HelperCookie;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Rest\Request;
use Mageplaza\GdprPro\Helper\Data;

/**
 * Class Cookie
 * @package Mageplaza\GdprPro\Model\Api\Cookie
 */
class Cookie
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
     * @var Cookie
     */
    protected $_cookieHelper;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $storeId;

    /**
     * Cookie constructor.
     *
     * @param Data $helperData
     * @param HelperCookie $cookieHelper
     * @param UrlInterface $urlBuilder
     * @param Request $request
     */
    public function __construct(
        Data $helperData,
        HelperCookie $cookieHelper,
        UrlInterface $urlBuilder,
        Request $request
    ) {
        $this->_helperData   = $helperData;
        $this->_cookieHelper = $cookieHelper;
        $this->urlBuilder    = $urlBuilder;
        $this->request       = $request;
        if (isset($this->request->getBodyParams()['storeId'])) {
            $this->storeId = $this->request->getBodyParams()['storeId'];

        }
    }

    /**
     * @return DataObject
     */
    public function cookie()
    {
        return new DataObject([
            'output'              => $this->getOutput(),
            'check_cookie_enable' => $this->_helperData->getCookieConfig('enable', $this->storeId),
            'cookie_name'         => HelperCookie::IS_USER_ALLOWED_SAVE_COOKIE,
            'cookie_value'        => $this->_cookieHelper->getAcceptedSaveCookiesWebsiteIds(),
            'cookie_lifetime'     => $this->_cookieHelper->getCookieRestrictionLifetime(),
            'no_cookies_url'      => $this->removeDomain($this->urlBuilder->getUrl('cookie/index/noCookies'))
        ]);
    }

    /**
     * Get Notices
     * @return DataObject
     */
    protected function getOutput()
    {
        return new  DataObject([
            "html_class"         => $this->_helperData->getHtmlClass($this->storeId),
            "cookie_message"     => $this->_helperData->getCookieConfig('message',$this->storeId),
            "cookie_button_text" => $this->_helperData->getCookieButtonText($this->storeId),
            "cookie_policy_url"  => $this->removeDomain($this->_helperData->getCookiePolicyUrl($this->storeId)),
            "custom_css"         => $this->_helperData->getCustomCss($this->storeId),
            "is_block_access"    => $this->_helperData->isBlockAccess($this->storeId)
        ]);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function removeDomain($url)
    {
        return str_replace($this->urlBuilder->getBaseUrl(), '', $url);
    }
}
