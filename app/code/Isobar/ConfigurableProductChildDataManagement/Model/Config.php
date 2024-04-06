<?php

namespace Isobar\ConfigurableProductChildDataManagement\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_PATH_IS_ENABLE = 'isobar_configurable_product_child_data/general/enable';

    const XML_PATH_DISABLE_ATTRIBUTES = 'isobar_configurable_product_child_data/general/attributes';

    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnable($storeId = null)
    {
       return $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getDisabledAttributes($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DISABLE_ATTRIBUTES, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
