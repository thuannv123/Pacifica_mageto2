<?php

namespace Isobar\LineBot\Service;

use Isobar\Base\Model\ConfigProvider;
use Isobar\LineBot\Constant\Config as BaseConfig;
use Magento\Store\Model\ScopeInterface;

class Config extends ConfigProvider
{
    /**
     * Check active module on scope website
     * @param null $websiteCode
     * @return bool
     */
    public function isEnable($websiteCode = null)
    {
        if ($websiteCode) {
            $enabled = $this->getValue(BaseConfig::ENABLE_MODULE, $websiteCode, ScopeInterface::SCOPE_WEBSITE);
        } else {
            $enabled =  $this->getValue(BaseConfig::ENABLE_MODULE);
        }

        return $enabled ? true : false;
    }

    public function getChanelAccessToken($websiteCode = null)
    {
        if ($websiteCode) {
            $level = $this->getValue(BaseConfig::CHANEL_ACCESS_TOKEN, $websiteCode, ScopeInterface::SCOPE_WEBSITE);
        } else {
            $level =  $this->getValue(BaseConfig::CHANEL_ACCESS_TOKEN);
        }

        return $level;
    }

    public function getChanelSecret($websiteCode = null)
    {
        if ($websiteCode) {
            $disable = $this->getValue(BaseConfig::CHANEL_SECRET, $websiteCode, ScopeInterface::SCOPE_WEBSITE);
        } else {
            $disable =  $this->getValue(BaseConfig::CHANEL_SECRET);
        }

        return $disable;
    }
}
