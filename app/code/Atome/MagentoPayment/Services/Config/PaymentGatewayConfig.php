<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\Config;

use Atome\MagentoPayment\Enum\ConfigFormName;
use Atome\MagentoPayment\Services\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;
use Magento\Store\Model\ScopeInterface;

class PaymentGatewayConfig
{
    const API_URL = 'api_url';
    const WEB_URL = 'web_url';

    protected $scopeConfig;
    protected $magentoState;
    protected $storeId;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        State                $magentoState
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->magentoState = $magentoState;
        $this->storeId = null;
    }

    public function setStoreId($storeId = null)
    {
        $this->storeId = $storeId;
    }

    public function getApiUrl($path, $query = [])
    {
        return $this->buildUrl(self::API_URL, $path, $query);
    }

    protected function buildUrl($type, $path, $query)
    {
        $apiEnv = $this->getApiEnv();
        $url = $this->getAtomeUrl($apiEnv, $type);
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        if ($path && $path[0] === '/') {
            $url .= substr($path, 1);
        } else {
            $url .= $path;
        }
        if ($query) {
            $url = $url . '?' . http_build_query($query, '', '&');
        }
        return $url;
    }

    public function getAtomeUrl($env, $type)
    {
        if ($env !== 'test' && $env !== 'production') {
            Logger::instance()->info("Since the \$env variable is `{$env}`, it is automatically set to production");
            $env = 'production';
        }

        if ($type == self::WEB_URL) {
            $urls = [
                'test' => Atome::TEST_WEB_URL,
                'production' => Atome::PRODUCTION_WEB_URL
            ];
        } else if ($type == self::API_URL) {
            $urls = [
                'test' => Atome::TEST_API_URL,
                'production' => Atome::PRODUCTION_API_URL,
            ];
        } else {
            throw new \RuntimeException("unknown url type $type");
        }

        $url = $urls[$env];
        if (!isset($url)) {
            throw new \RuntimeException("unknown url type $type/$env");
        }
        return $url;
    }

    protected function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue(
            Atome::getScopePath($path),
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    public function getLanguage()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $getLocale = $objectManager->get('Magento\Framework\Locale\Resolver');
        $haystack = $getLocale->getLocale();
        return strstr($haystack, '_', true);
    }

    public function getCountry()
    {
        return $this->getScopeConfigValue(ConfigFormName::COUNTRY) ?: 'sg';
    }

    public function getApiEnv()
    {
        return $this->getScopeConfigValue(ConfigFormName::API_ENV);
    }

    public function getMerchantApiKey()
    {
        return $this->getScopeConfigValue(ConfigFormName::API_KEY);
    }

    public function getMerchantApiSecret()
    {
        return $this->getScopeConfigValue(ConfigFormName::API_SECRET);
    }

    public function isDebugEnabled()
    {
        return (bool)(int)$this->getScopeConfigValue(ConfigFormName::DEBUG_MODE);
    }

    public function isActive()
    {
        return (bool)(int)$this->getScopeConfigValue(ConfigFormName::ACTIVE);
    }

    public function isPriceDividerProductListVisible()
    {
        return (boolean)(int)$this->getScopeConfigValue(ConfigFormName::PRICE_DIVIDER_PRODUCT_LIST);
    }

    public function isPriceDividerProductDetailVisible()
    {
        return (boolean)(int)$this->getScopeConfigValue(ConfigFormName::PRICE_DIVIDER_PRODUCT_DETAIL);
    }

    public function getOrderEmailSendBy()
    {
        return $this->getScopeConfigValue(ConfigFormName::ORDER_EMAIL_SEND_BY);
    }

    public function getOrderStatus()
    {
        return $this->getScopeConfigValue(ConfigFormName::ORDER_STATUS);
    }

    public function getSortOrder()
    {
        return $this->getScopeConfigValue(ConfigFormName::SORT_ORDER);
    }

    public function getOrderState()
    {
        $collection = ObjectManager::getInstance()->create(Collection::class);
        $stateRow = $collection->getConnection()->fetchRow(
            $collection->joinStates()->getSelect()
                ->where('state_table.status=?', $this->getOrderStatus())
        );

        return $stateRow['state'] ?? null;
    }

    public function getNewOrderStatus()
    {
        return $this->getScopeConfigValue(ConfigFormName::NEW_ORDER_STATUS);
    }

    public function getNewOrderState()
    {
        $collection = ObjectManager::getInstance()->create(Collection::class);
        $stateRow = $collection->getConnection()->fetchRow(
            $collection->joinStates()->getSelect()
                ->where('state_table.status=?', $this->getNewOrderStatus())
        );

        return $stateRow['state'] ?? null;
    }

    public function getMaxSpend()
    {
        return $this->getScopeConfigValue(ConfigFormName::MAX_SPEND);
    }

    public function getMinSpend()
    {
        return $this->getScopeConfigValue(ConfigFormName::MIN_SPEND);
    }

    public function getClearCartWithoutPaying()
    {
        return (bool)(int)$this->getScopeConfigValue(ConfigFormName::CLEAR_CART_WITHOUT_PAYING);
    }

    public function getExcludedCategories()
    {
        return $this->getScopeConfigValue(ConfigFormName::EXCLUDE_CATEGORY);
    }

    public function getCancelTimeout($inSeconds = false)
    {
        $timeout = $this->getScopeConfigValue(ConfigFormName::CANCEL_TIMEOUT);
        return $inSeconds ? $timeout * 60 : $timeout;
    }
}
