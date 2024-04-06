<?php

namespace Atome\MagentoPayment\Services\Price;

use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\LocaleConfig;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;

class PriceService
{

    /**
     * @var LocaleConfig
     */
    protected $localeConfig;

    /**
     * @var PaymentGatewayConfig
     */
    protected $paymentGatewayConfig;

    public function __construct(LocaleConfig $localeConfig, PaymentGatewayConfig $paymentGatewayConfig)
    {
        $this->localeConfig = $localeConfig;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }

    public function format($amount)
    {
        $intFactor = $this->localeConfig->getIntFactor(ATOME::DEFAULT_INT_FACTOR);

        Logger::instance()->debug("[formatAmount] get int_factor form local config: " . $intFactor);

        $amount *= $intFactor;
        $amount = in_array($this->paymentGatewayConfig->getCountry(), ['id', 'vn']) ? ceil($amount) : round($amount);

        return intval($amount);
    }

    public function reverseFormat($amount)
    {
        $intFactor = $this->localeConfig->getIntFactor(ATOME::DEFAULT_INT_FACTOR);

        Logger::instance()->debug("[reverseFormatAmount] get int_factor form local config: " . $intFactor);

        $amount /= $intFactor;

        return in_array($this->paymentGatewayConfig->getCountry(), ['id', 'vn']) ? ceil($amount) : round($amount, 2);
    }

}
