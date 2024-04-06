<?php

namespace Isobar\OrderReminder\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class HandleReloadCartCookie
{
    const COOKIE_NAME = 'must_reload_cart';
    const COOKIE_EXPIRE_TIME = 86400;

    private CookieManagerInterface $cookieManager;

    private CookieMetadataFactory $cookieMetadataFactory;

    private SessionManagerInterface $sessionManager;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(CookieManagerInterface $cookieManager, CookieMetadataFactory $cookieMetadataFactory, SessionManagerInterface $sessionManager)
    {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return string|null
     */
    public function readReloadCartCookie()
    {
       return $this->cookieManager->getCookie(self::COOKIE_NAME);
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function buildReloadCartCookie()
    {
        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            1,
            $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_EXPIRE_TIME)
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function removeReloadCartCookie()
    {
       $this->cookieManager->deleteCookie(
           self::COOKIE_NAME,
           $this->cookieMetadataFactory->createPublicCookieMetadata()
               ->setPath($this->sessionManager->getCookiePath())
               ->setDomain($this->sessionManager->getCookieDomain())
       );
    }

}
