<?php

namespace Isobar\BirkenstockCustomize\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const DISABLE_SWATCH_WEBSITES = 'isobar_birkenstock_customize/general/disable_product_swatch';

    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $websiteId
     * @return mixed
     */
    public function getDisableSwatchWebsites($websiteId = null)
    {
        return $this->scopeConfig->getValue(self::DISABLE_SWATCH_WEBSITES, ScopeInterface::SCOPE_WEBSITE,$websiteId);
    }

}