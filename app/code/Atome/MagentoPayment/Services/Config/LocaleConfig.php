<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\Config;

use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Payment\API\GetVariablesRequest;
use Atome\MagentoPayment\Services\Payment\API\GetVariablesResponse;
use Exception;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use RuntimeException;

/**
 * @method getIntFactor($default = '')
 */
class LocaleConfig
{
    protected $localeDefaultInfos = Atome::VALUES;

    protected $cacheManager;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    protected $currentLocaleInfo;

    public function __construct(
        Manager              $cacheManager,
        ScopeConfigInterface $scopeConfig,
        WriterInterface      $configWriter,
        PaymentGatewayConfig $paymentGatewayConfig
    )
    {
        $this->cacheManager = $cacheManager;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }

    protected function setScopeConfigValue($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $this->configWriter->save(
            Atome::getScopePath($path),
            $value,
            $scope,
            $scopeId
        );
    }

    protected function getLocaleInfo()
    {
        $localeInfoStr = $this->scopeConfig->getValue(
            Atome::getScopePath('locale_info')
        );

        if (empty($localeInfoStr)) {
            Logger::instance()->debug('get locale_info_str from db empty: ' . $localeInfoStr);
            return [];
        }

        $localeInfoArr = json_decode($localeInfoStr, true);

        if (empty($localeInfoArr)) {
            Logger::instance()->debug('locale_info_arr : ' . json_encode($localeInfoArr));
            return [];
        }

        return $localeInfoArr;
    }


    protected function initLocaleInfo()
    {
        $localeInfoArr = $this->getLocaleInfo();
        $country = $this->paymentGatewayConfig->getCountry();
        if ($this->isConfigExpired()) {
            $localeInfoArr = $this->mergeRemoteConfig($localeInfoArr, $country);
        }

        $this->currentLocaleInfo = array_merge($this->localeDefaultInfos[$country] ?? [], $localeInfoArr[$country] ?? []);
    }

    protected function mergeRemoteConfig($localeInfoArr, $country)
    {
        try {
            $remoteConfig = $this->getConfigFromAtome($country);
            $isRemoteConfigChanged = $this->isRemoteConfigChanged($localeInfoArr[$country] ?? [], $remoteConfig);
            if ($isRemoteConfigChanged) {
                $localeInfoArr[$country] = array_merge($localeInfoArr[$country] ?? [], $remoteConfig);
                $configJson = json_encode($localeInfoArr);
                $this->setScopeConfigValue('locale_info', $configJson);
            }

            $this->setScopeConfigValue('last_updated_time', time());

            if ($isRemoteConfigChanged) {
                $this->cacheManager->flush(['config']);
            }

        } catch (Exception $e) {
            Logger::instance()->error(__METHOD__ . " get local info from atome failed:{$e->getMessage()}");
        }

        return $localeInfoArr;
    }

    protected function isRemoteConfigChanged($localeCountryInfoArr, $remoteConfig)
    {
        foreach ($remoteConfig as $remoteKey => $remoteValue) {
            if (!isset($localeCountryInfoArr[$remoteKey]) || $localeCountryInfoArr[$remoteKey] !== $remoteValue) {
                return true;
            }
        }

        return false;
    }

    protected function isConfigExpired()
    {
        $lastUpdatedTime = $this->scopeConfig->getValue(
            Atome::getScopePath('last_updated_time')
        );

        return !$lastUpdatedTime || (time() - ATOME::REMOTE_CONFIG_EXPIRE_SECONDS) > $lastUpdatedTime;

    }

    protected function getConfigFromAtome($country)
    {
        $getVariablesRequest = new GetVariablesRequest();
        $getVariablesRequest->setCountry($country)->request();
        /** @var GetVariablesResponse $getVariablesResponse */
        $getVariablesResponse = $getVariablesRequest->getWrappedResponse();
        $intFactor = $this->localeDefaultInfos[$country]['int_factor'] ?? ATOME::DEFAULT_INT_FACTOR;

        return [
            'minimum_spend' => intval($getVariablesResponse->getMinSpend() / $intFactor)
        ];
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 3) !== 'get') {
            throw new RuntimeException('unknown method call: ' . $method);
        }

        $k = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, lcfirst(substr($method, 3)));

        if (empty($this->currentLocaleInfo)) {
            $this->initLocaleInfo();
        }

        return $this->currentLocaleInfo[$k] ?? ($args[0] ?? '');
    }

    public function getCountryConfig()
    {
        return $this->currentLocaleInfo;
    }

    public function getSupportedCurrencyCodes()
    {
        return array_unique(array_column($this->localeDefaultInfos, 'currency_code'));
    }
}
