<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\View;

use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\ConfigService;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\ObjectManager;

class FrontendConfigProvider implements ConfigProviderInterface
{
    protected $paymentGatewayConfig;

    public function __construct(PaymentGatewayConfig $config)
    {
        $this->paymentGatewayConfig = $config;
    }

    /**
     * config for `window.checkoutConfig` in JS
     *
     * @return array
     */
    public function getConfig()
    {
        // will be rendered to `window.checkoutConfig.payment.atome` in js
        return [
            'payment' => [
                'atome' => [
                    'isActive' => $this->paymentGatewayConfig->isActive(),
                    'logo' => Atome::LOGO,
                    'new_user_off_image' => ObjectManager::getInstance()->create(ConfigService::class)->getNewUserOffImage(),
                    'min_spend' => $this->paymentGatewayConfig->getMinSpend(),
                    'atome_terms_conditions_url' => Atome::termsAndConditionsUrl(),
                ],
            ],
        ];
    }

}
