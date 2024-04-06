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

namespace Mageplaza\GdprPro\Model\Api;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GdprPro\Api\RequestsManagementInterface;

/**
 * Class RequestsManagement
 * @package Mageplaza\GdprPro\Model\Api
 */
class RequestsManagement implements RequestsManagementInterface
{
    /**
     * @var CheckPassword
     */
    protected $_checkPassword;

    /**
     * @var DownLoad
     */
    protected $_download;

    /**
     * @var Cookie
     */
    protected $_cookie;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var UserContextInterface
     */
    protected $_userContext;

    /**
     * RequestsManagement constructor.
     *
     * @param DownLoad $download
     * @param CheckPassword $checkPassword
     * @param Cookie $cookie
     * @param Config $config
     * @param UserContextInterface $userContext
     */
    public function __construct(
        DownLoad $download,
        CheckPassword $checkPassword,
        Cookie $cookie,
        Config $config,
        UserContextInterface $userContext
    ) {
        $this->_download      = $download;
        $this->_checkPassword = $checkPassword;
        $this->_cookie        = $cookie;
        $this->_config        = $config;
        $this->_userContext   = $userContext;
    }

    /**
     * @inheritDoc
     *
     * @param $type
     *
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function downLoadCustomerData($type)
    {
        $this->_download->downLoadCustomerData($this->getCurrentUserId(), $type);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function checkPassword($email, $password)
    {
        return $this->_checkPassword->checkPassword($this->getCurrentUserId(), $email, $password);
    }

    /**
     * @inheritDoc
     */
    public function cookie()
    {
        return $this->_cookie->cookie();
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->_config->getConfig();
    }

    /**
     * @return int
     */
    public function getCurrentUserId()
    {
        return $this->_userContext->getUserId();
    }
}
