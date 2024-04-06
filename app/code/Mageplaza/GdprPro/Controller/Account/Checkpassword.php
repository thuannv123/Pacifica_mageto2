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

namespace Mageplaza\GdprPro\Controller\Account;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Checkpassword
 *
 * @package Mageplaza\GdprPro\Controller\Account\Checkpassword
 */
class Checkpassword extends AbstractAccount
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var CustomerRegistry
     */
    protected $_customerRegistry;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var $request
     */
    protected $request;

    /**
     * Checkpassword constructor.
     *
     * @param Context $context
     * @param Encryptor $encryptor
     * @param Session $customerSession
     * @param CustomerRegistry $customerRegistry
     */
    public function __construct(
        Context $context,
        Encryptor $encryptor,
        Session $customerSession,
        CustomerRegistry $customerRegistry
    ) {
        $this->_customerSession  = $customerSession;
        $this->encryptor         = $encryptor;
        $this->_customerRegistry = $customerRegistry;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function execute()
    {
        /**
         * get customer passwordhash
         */
        $customerId           = $this->_customerSession->getCustomerId();
        $customerSecure       = $this->_customerRegistry->retrieveSecureData($customerId);
        $customerPasswordHash = $customerSecure->getPasswordHash();
        /**
         * get passwordPost
         */
        $passwordPost = $this->getRequest()->getPost('password');

        $result = $this->encryptor->validateHash($passwordPost, $customerPasswordHash);

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['status' => $result]);

        return $resultJson;
    }
}
