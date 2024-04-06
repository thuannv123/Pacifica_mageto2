<?php

namespace Isobar\OrderAttachment\Helper;

use Magento\Store\Model\ScopeInterface;
use Meetanshi\OrderUpload\Helper\Data as HelperData;

class Data extends HelperData
{
    const XML_PATH_ALLOW_SUCCESS_PAGE = 'orderupload/customer/allow_success_page';
    const XML_PATH_ALLOWED_PAYMENT_METHODS = 'orderupload/general/allow_payment_methods';

    /**
     * @return mixed
     */
    public function allowOnSuccessPage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLOW_SUCCESS_PAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function allowPaymentMethods()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLOWED_PAYMENT_METHODS, ScopeInterface::SCOPE_STORE);
    }
}
