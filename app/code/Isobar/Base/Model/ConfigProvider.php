<?php
/**
 * @author Isobar Team
 * @copyright Copyright (c) 2020 Isobar (https://www.isobar.com)
 * @package Isobar_Base
 */

namespace Isobar\Base\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @since 1.4.4
 */
class ConfigProvider
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Stored values by scopes
     *
     * @var array
     */
    protected $data = [];

    /**
     * ConfigProvider constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws \LogicException
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $path '{group}/{field}'
     * @param int|ScopeInterface|null $storeId Scope code
     * @param string $scope
     *
     * @return mixed
     */
    public function getValue(
        $path,
        $storeId = null,
        $scope = ScopeInterface::SCOPE_STORE
    ) {
        if ($storeId instanceof \Magento\Framework\App\ScopeInterface) {
            $storeId = $storeId->getId();
        }
        $scopeKey = $storeId;
        if ($scopeKey === null) {
            $scopeKey = 'current_';
        }
        $scopeKey .= $scope;
        if (empty($this->data[$path][$scopeKey])) {
            $this->data[$path][$scopeKey] = $this->scopeConfig->getValue($path, $scope, $storeId);
        }

        return $this->data[$path][$scopeKey];
    }

    /**
     * An alias for scope config with scope type Default
     *
     * @param string $path '{group}/{field}'
     *
     * @return mixed
     */
    public function getGlobalValue($path)
    {
        return $this->getValue($path, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function getModuleInfo($installedModule)
    {
        return [
            'guide' => $this->getGlobalValue($installedModule . '/guide')
        ];
    }
}
