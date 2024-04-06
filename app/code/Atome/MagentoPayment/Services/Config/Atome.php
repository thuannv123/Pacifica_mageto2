<?php

namespace Atome\MagentoPayment\Services\Config;

use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 *
 * @method static getCurrencyCode($default = null)
 * @method static getMinimumSpend($default = null)
 * @method static getAtomeUrl($default = null)
 * @method static getAtomeLogo($default = null)
 * @method static getNewUserOffImage($default = null)
 * @method static getIntFactor($default = null)
 * @method static getCustomCountry($default = null)
 * @method static getCustomLang($default = null)
 *
 */
class Atome
{
    const MODULE_NAME = 'Atome_MagentoPayment';
    const METHOD_CODE = 'atome_payment_gateway';

    const PLUGIN_HOST = 'https://gateway.apaylater.com';
    const DEFAULT_OFFICIAL_SITE = 'https://www.atome.sg';
    const PRICE_DIVIDER_URL = 'https://gateway.apaylater.com/plugins/price_divider/main.js';
    const LOADING_IMAGE = 'https://gateway.apaylater.com/plugins/common/assets/images/loading.gif';
    const LOGO = 'https://gateway.apaylater.com/plugins/common/assets/svg/logo.svg';

    const TEST_API_URL = 'https://api.apaylater.net/v1/';
    const PRODUCTION_API_URL = 'https://api.apaylater.com/v1/';

    const TEST_WEB_URL = 'https://www.apaylater.net/';
    const PRODUCTION_WEB_URL = 'https://www.atome.sg/';

    const SIMULATE_FILE_NAME = '.atome_simulation';

    const REMOTE_CONFIG_EXPIRE_SECONDS = 3600;
    const CANCEL_TIMEOUT_MINIMUM_MINUTES = 10;
    const CANCEL_TIMEOUT_MAXIMUM_MINUTES = 720;
    const DEFAULT_INT_FACTOR = 100;

    const VALUES = [
        'sg' => [
            'currency_code' => 'SGD',
            'minimum_spend' => 10,
            'atome_url' => 'https://www.atome.sg',
            'new_user_off_image' => 'ic-new-user-off-sg.svg',
            'int_factor' => 100,
            'custom_country' => 'sg',
            'custom_lang' => 'en',
            'terms_and_conditions_url' => 'https://www.atome.sg/en-sg/terms-of-service'
        ],
        'hk' => [
            'currency_code' => 'HKD',
            'minimum_spend' => 100,
            'atome_url' => 'https://www.atome.hk',
            'new_user_off_image' => 'ic-new-user-off-hk.svg',
            'int_factor' => 100,
            'custom_country' => 'hk',
            'custom_lang' => 'zh',
            'terms_and_conditions_url' => 'https://www.atome.hk/zh-hk/terms-of-service'
        ],
        'my' => [
            'currency_code' => 'MYR',
            'minimum_spend' => 50,
            'atome_url' => 'https://www.atome.my',
            'new_user_off_image' => 'ic-new-user-off-my.svg',
            'int_factor' => 100,
            'custom_country' => 'my',
            'custom_lang' => 'en',
            'terms_and_conditions_url' => 'https://www.atome.my/en-my/terms-of-service'
        ],
        'id' => [
            'currency_code' => 'IDR',
            'minimum_spend' => 100000,
            'atome_url' => 'https://www.atome.id',
            'new_user_off_image' => '',
            'int_factor' => 1,
            'custom_country' => 'id',
            'custom_lang' => 'id',
            'terms_and_conditions_url' => 'https://www.atome.id/id-id/terms-of-service'
        ],
        'th' => [
            'currency_code' => 'THB',
            'minimum_spend' => 100,
            'atome_url' => 'https://www.atometh.com',
            'new_user_off_image' => 'ic-new-user-off-th.svg',
            'int_factor' => 100,
            'custom_country' => 'th',
            'custom_lang' => 'en',
            'terms_and_conditions_url' => 'https://www.atometh.com/en-th/terms-of-service'
        ],
        'vn' => [
            'currency_code' => 'VND',
            'minimum_spend' => 200000,
            'atome_url' => 'https://www.atome.vn',
            'new_user_off_image' => '',
            'int_factor' => 1,
            'custom_country' => 'vn',
            'custom_lang' => 'vi',
            'terms_and_conditions_url' => 'https://www.atome.vn/terms-of-service'
        ],
        'ph' => [
            'currency_code' => 'PHP',
            'minimum_spend' => 80,
            'atome_url' => 'https://www.atome.ph',
            'new_user_off_image' => '',
            'int_factor' => 100,
            'custom_country' => 'ph',
            'custom_lang' => 'en',
            'terms_and_conditions_url' => 'https://www.atome.ph/terms-of-service'
        ],
        'tw' => [
            'currency_code' => 'TWD',
            'minimum_spend' => 50,
            'atome_url' => 'https://www.atome.tw',
            'new_user_off_image' => '',
            'int_factor' => 1,
            'custom_country' => 'tw',
            'custom_lang' => 'zh',
            'terms_and_conditions_url' => 'https://www.atome.tw/terms-of-service'
        ],
        'jp' => [
            'currency_code' => 'JPY',
            'minimum_spend' => 200,
            'atome_url' => 'https://www.atome.jp',
            'new_user_off_image' => '',
            'int_factor' => 1,
            'custom_country' => 'jp',
            'custom_lang' => 'ja',
            'terms_and_conditions_url' => 'https://www.atome.jp/terms-of-service'
        ],

    ];

    public static function getByCountry($country)
    {
        return self::VALUES[strtolower($country)] ?? [];
    }

    public static function getScopePath($key)
    {
        return 'payment/' . Atome::METHOD_CODE . '/' . $key;
    }


    public static function __callStatic(string $name, array $arguments)
    {
        if (substr($name, 0, 3) !== 'get') {
            throw new \RuntimeException('unknown method call: ' . $name);
        }

        $k = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, lcfirst(substr($name, 3)));


        $country = ObjectManager::getInstance()
            ->create(PaymentGatewayConfig::class)
            ->getCountry();

        $config = static::getByCountry($country);
        Logger::instance()->info(__METHOD__ . json_encode([
                'config' => $config,
                'key' => $k,
                'country' => $country
            ]));

        return $config[$k] ?? ($arguments[0] ?? null);
    }

    public static function termsAndConditionsUrl($country = null)
    {
        if (!$country) {
            $country = self::currentCountry();
        }

        $country = strtolower($country);

        if ($country) {
            return self::VALUES[$country]['terms_and_conditions_url'] ?? self::VALUES['sg']['terms_and_conditions_url'];
        }

        return Atome::DEFAULT_OFFICIAL_SITE;
    }

    public static function officialSiteUrl($country = null)
    {
        if (!$country) {
            $country = self::currentCountry();
        }

        if ($country) {
            return self::VALUES[$country]['atome_url'] ?? Atome::DEFAULT_OFFICIAL_SITE;
        }

        return Atome::DEFAULT_OFFICIAL_SITE;
    }


    protected static function currentCountry()
    {
        return ObjectManager::getInstance()->get(ScopeConfigInterface::class)
            ->getValue(
                'general/country/default',
                ScopeInterface::SCOPE_STORE,
                ObjectManager::getInstance()->get(StoreManagerInterface::class)
                    ->getStore()
                    ->getId()
            ) ?: 'SG';
    }


    public static function version()
    {
        $module = ObjectManager::getInstance()
            ->create(ModuleListInterface::class)
            ->getOne(Atome::MODULE_NAME);

        return $module['setup_version'] ?? '0.0.0';
    }


}
