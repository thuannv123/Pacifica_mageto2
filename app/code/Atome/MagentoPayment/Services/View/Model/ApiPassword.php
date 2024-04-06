<?php

namespace Atome\MagentoPayment\Services\View\Model;


use Atome\MagentoPayment\Services\Payment\API\AuthenticationRequest;
use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Throwable;

class ApiPassword extends Encrypted
{

    public function afterSave()
    {
        try {
            $store = ObjectManager::getInstance()->create(StoreManagerInterface::class);
            $request = new AuthenticationRequest();
            $request->setCountry($this->getData('fieldset_data/country'))
                ->setCallbackUrl("{$store->getStore()->getBaseUrl()}atome/payment/ping")
                ->request();
        } catch (Throwable $t) {
            $message = ObjectManager::getInstance()->create(ManagerInterface::class);
            $message->addErrorMessage("Api Key or Api Password is entered incorrectly or an error occurs during the Atome callback.");
            $message->addErrorMessage($t->getMessage());
        }

        return parent::afterSave();
    }

}
