<?php

namespace Isobar\MageplazaSocialLogin\Controller\Social;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;

class Login extends \Isobar\MageplazaSocialLogin\Controller\Social\AbstractSocial
{
    public function execute()
    {
        if ($this->checkCustomerLogin() && $this->session->isLoggedIn()) {
            $this->_redirect('customer/account');

            return;
        }

        $type = $this->apiHelper->setType($this->getRequest()->getParam('type'));

        if (!$type) {
            $this->_forward('noroute');

            return;
        }

        return $this->login($type);
    }
}
