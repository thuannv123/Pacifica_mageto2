<?php

namespace Isobar\Shipping\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const CARRIER_CODE = 'custom_carrier';

    const ISOBAR_SHIPPING_GENERAL_PATH = 'carriers/custom_carrier/';

    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $scope
     * @param $scopeCode
     * @return mixed
     */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::ISOBAR_SHIPPING_GENERAL_PATH . 'active', $scope, $scopeCode);
    }

    /**
     * @param $scope
     * @param $scopeCode
     * @return mixed
     */
    public function getTitle($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::ISOBAR_SHIPPING_GENERAL_PATH . 'title', $scope, $scopeCode);
    }

    /**
     * @param $scope
     * @param $scopeCode
     * @return mixed
     */
    public function getUrl($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::ISOBAR_SHIPPING_GENERAL_PATH . 'url', $scope, $scopeCode);
    }
}
