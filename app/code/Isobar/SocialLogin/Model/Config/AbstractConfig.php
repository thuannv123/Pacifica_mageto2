<?php

namespace Isobar\SocialLogin\Model\Config;

use Isobar\SocialLogin\Model\Serialize\Serializer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

abstract class AbstractConfig
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    protected $pathPrefix;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Serializer $serializer
     * @param string $pathPrefix
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer,
        $pathPrefix = ''
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * Get config value
     *
     * @param string $path
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    protected function getValue(
        $path,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->preparePath($path), $scopeType, $scopeCode);
    }

    /**
     * Get serialized config value
     *
     * @param string $path
     * @param string $scopeType
     * @param null|string $scopeCode
     * @param mixed $default
     * @return array
     */
    protected function getSerializedValue(
        $path,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null,
        $default = null
    ) {
        $serializedValue = $this->getValue($path, $scopeType, $scopeCode);

        try {
            $value = $this->serializer->unserialize($serializedValue);
        } catch (\Exception $e) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Is set flag
     *
     * @param string $path
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return bool
     */
    protected function isSetFlag(
        $path,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->isSetFlag($this->preparePath($path), $scopeType, $scopeCode);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function preparePath($path)
    {
        return $this->pathPrefix . $path;
    }
}
