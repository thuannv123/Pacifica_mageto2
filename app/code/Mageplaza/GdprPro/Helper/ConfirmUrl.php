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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfirmUrl
 *
 * @package Mageplaza\GdprPro\Helper
 */
class ConfirmUrl extends Data
{
    /**
     * @var RedirectInterface
     */
    protected $_redirectInterface;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;

    /**
     * ConfirmUrl constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     * @param Serialize $serialize
     * @param TokenFactory $tokenFactory
     * @param ActionFlag $actionFlag
     * @param RedirectInterface $redirectInterface
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Request $request,
        Serialize $serialize,
        TokenFactory $tokenFactory,
        ActionFlag $actionFlag,
        RedirectInterface $redirectInterface
    ) {
        $this->_actionFlag        = $actionFlag;
        $this->_redirectInterface = $redirectInterface;
        parent::__construct($context, $objectManager, $storeManager, $request, $serialize, $tokenFactory);
    }

    /**
     * @param $customerId
     *
     * @return string
     */
    public function getConfirmUrl($customerId)
    {
        $tockenCustomer = $this->createTokencustomer($customerId)->getToken();

        return $this->_getUrl(
            'customer/account/delete',
            ['id' => $customerId, 'tokenEmail' => $tockenCustomer, '_nosid' => true]
        );
    }

    /**
     * @param $customerId
     *
     * @return string
     */
    public function getTokenByCustomerId($customerId)
    {
        return $this->getTokencustomer($customerId);
    }

    /**
     * @param $response
     */
    public function redirectCustomerEdit($response)
    {
        $this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        return $this->_redirectInterface->redirect($response->getResponse(), 'customer/account/edit');
    }
}
