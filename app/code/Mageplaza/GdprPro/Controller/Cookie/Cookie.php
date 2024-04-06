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

namespace Mageplaza\GdprPro\Controller\Cookie;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GdprPro\Helper\Data;
use Magento\Cookie\Helper\Cookie as HelperCookie;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Cookie
 * @package Mageplaza\GdprPro\Controller\Cookie
 */
class Cookie extends Action
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
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Data $helperData
     * @param HelperCookie $cookieHelper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Data $helperData,
        HelperCookie $cookieHelper,
        UrlInterface $urlBuilder
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helperData        = $helperData;
        $this->_cookieHelper      = $cookieHelper;
        $this->urlBuilder         = $urlBuilder;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result     = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();

        $block = $resultPage->getLayout()
            ->createBlock(Template::class)
            ->setTemplate('Mageplaza_GdprPro::cookie/notices.phtml')
            ->toHtml();

        $result->setData([
            'output'            => $block,
            'checkCookieEnable' => $this->_helperData->isEnableCookieRestrictrion(),
            'cookieName'        => HelperCookie::IS_USER_ALLOWED_SAVE_COOKIE,
            'cookieValue'       => $this->_cookieHelper->getAcceptedSaveCookiesWebsiteIds(),
            'cookieLifetime'    => $this->_cookieHelper->getCookieRestrictionLifetime(),
            'noCookiesUrl'      => $this->urlBuilder->getUrl('cookie/index/noCookies')
        ]);

        return $result;
    }
}
