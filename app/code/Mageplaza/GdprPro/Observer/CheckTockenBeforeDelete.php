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

namespace Mageplaza\GdprPro\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\GdprPro\Helper\Data as HelperData;

/**
 * Class CheckTockenBeforeDelete
 *
 * @package Mageplaza\GdprPro\Observer
 */
class CheckTockenBeforeDelete implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * CheckTockenBeforeDelete constructor.
     *
     * @param RequestInterface $requestInterface
     * @param HelperData $helperData
     */
    public function __construct(
        RequestInterface $requestInterface,
        HelperData $helperData
    ) {
        $this->_request    = $requestInterface;
        $this->_helperData = $helperData;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $paramTokenEmail = $this->_request->getParam('tokenEmail');
        $paramToken      = $this->_request->getParam('token');
        $checkToken      = $observer->getEvent()->getChecktoken();
        $customer        = $observer->getEvent()->getCustomer();
        $customerId      = $customer->getId();
        $checkToken->setFlag(false);

        if ($paramToken) {
            /**
             * get and compare token customer case verify password
             */
            $tokenCustomer = $this->_helperData->getTokencustomer($customerId);
            if ($paramToken === $tokenCustomer) {
                $checkToken->setFlag(true);
            }
        }

        if ($this->_helperData->getEnableEmailConfirm() && $paramTokenEmail) {
            /**
             * get and compare token customer case confirm email
             */
            $tokenCustomer = $this->_helperData->getTokencustomer($customerId);
            if ($paramTokenEmail === $tokenCustomer) {
                $checkToken->setFlag(true);
            }
        }
    }
}
