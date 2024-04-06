<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Block;

use Atome\MagentoPayment\Enum\ConfigFormName;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\ConfigService;
use Atome\MagentoPayment\Services\Config\LocaleConfig;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class GlobalBlock extends Template
{
    /**
     * @var LocaleConfig
     */
    protected $localeConfig;

    protected $paymentGatewayConfig;

    public function __construct(
        Template\Context     $context,
        LocaleConfig         $localeConfig,
        PaymentGatewayConfig $paymentGatewayConfig,
        array                $data
    )
    {
        parent::__construct($context, $data);

        $this->localeConfig = $localeConfig;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }


    public function getConfig()
    {
        $shopBaseUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);

        return [
            'logo' => Atome::LOGO,
            'shopBaseUrl' => $shopBaseUrl,
            'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
            'country_config' => $this->localeConfig->getCountryConfig(),
            'locale_code' => $this->_scopeConfig->getValue(
                'general/locale/code', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId()
            ),
            'debug_mode' => $this->_scopeConfig->getValue(
                Atome::getScopePath(ConfigFormName::DEBUG_MODE),
                ScopeInterface::SCOPE_STORE
            ),
            'clear_cart_without_paying' => $this->_scopeConfig->getValue(
                Atome::getScopePath(ConfigFormName::CLEAR_CART_WITHOUT_PAYING)
            ),
            'new_user_off_image' => ObjectManager::getInstance()->create(ConfigService::class)->getNewUserOffImage()
        ];
    }

    public function getPriceDividerConfig()
    {
        $n = 0;
        if ($this->paymentGatewayConfig->isPriceDividerProductListVisible()) {
            $n += 1;
        }
        if ($this->paymentGatewayConfig->isPriceDividerProductDetailVisible()) {
            $n += 2;
        }

        $priceDivider = 'no';
        $priceDividerAppliedOn = '';

        switch ($n) {
            case 1:
                $priceDivider = 'yes';
                $priceDividerAppliedOn = 'list';
                break;
            case 2:
                $priceDivider = 'yes';
                $priceDividerAppliedOn = 'detail';
                break;
            case 3:
                $priceDivider = 'yes';
                $priceDividerAppliedOn = 'all';
                break;
        }

        return [
            'language' => $this->paymentGatewayConfig->getLanguage(),
            'price_divider' => $priceDivider,
            'price_divider_applied_on' => $priceDividerAppliedOn,
            'max_spend' => $this->paymentGatewayConfig->getMaxSpend(),
            'min_spend' => $this->paymentGatewayConfig->getMinSpend(),
            'country' => $this->paymentGatewayConfig->getCountry(),
            'platform' => 'MAGENTO',
            'version' => Atome::version(),
        ];
    }


    public function isPaymentActive()
    {
        return ObjectManager::getInstance()->create(PaymentGatewayConfig::class)->isActive();
    }
}
