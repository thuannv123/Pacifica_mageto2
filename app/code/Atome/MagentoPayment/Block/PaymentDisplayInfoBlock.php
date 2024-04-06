<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Block;

use Atome\MagentoPayment\Enum\AdditionalInformationKey;
use Atome\MagentoPayment\Services\Price\PriceService;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Block\Info;

class PaymentDisplayInfoBlock extends Info
{

    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];

        if ($additionalInformation = $this->getInfo()->getAdditionalInformation()) {

            $priceService = ObjectManager::getInstance()->get(PriceService::class);

            foreach ($additionalInformation as $field => $value) {
                if (in_array($field, [AdditionalInformationKey::PAYMENT_DEBUG_SECRET, AdditionalInformationKey::MERCHANT_REFERENCE_ID])) {
                    continue;
                } else if ($field === AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED) {
                    $data['Atome payment amount'] = number_format($priceService->reverseFormat($value), 2);
                } else {
                    $beautifiedFieldName = str_replace('_', ' ', ucwords(trim(preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $field) ?? '')));
                    $data[__($beautifiedFieldName)->getText()] = $value;
                }
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
