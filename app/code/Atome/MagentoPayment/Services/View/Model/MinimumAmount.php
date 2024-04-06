<?php

namespace Atome\MagentoPayment\Services\View\Model;

use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Payment\API\GetVariablesRequest;
use Atome\MagentoPayment\Services\Payment\API\GetVariablesResponse;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;

class MinimumAmount extends Value
{
    public function beforeSave()
    {
        $country = strtoupper($this->getData('fieldset_data/country'));
        if (!$country) {
            $country = ObjectManager::getInstance()->create(PaymentGatewayConfig::class)->getCountry();
        }

        $request = new GetVariablesRequest();
        if ($request->setCountry($country)->request()->getStatusCode() === 200) {

            /** @var GetVariablesResponse $response */
            $response = $request->getWrappedResponse();

            $localConfig = Atome::getByCountry($country);
            $intFactor = isset($localConfig['int_factor']) && $localConfig['int_factor'] > 0
                ? $localConfig['int_factor']
                : Atome::DEFAULT_INT_FACTOR;

            $minSpend = $response->getMinSpend() / $intFactor;
            if ($minSpend > $this->getValue()) {
                $this->setValue($minSpend);
                $message = ObjectManager::getInstance()->create(ManagerInterface::class);

                $currency = $localConfig['currency_code'];
                $message->addNoticeMessage("Since the value you filled in is lower than the minimum amount {$currency} {$minSpend} for {$country}, we have adjusted the `{$this->getData('field_config/label')}` accordingly.");
            }

        }
        parent::beforeSave();
    }


}
