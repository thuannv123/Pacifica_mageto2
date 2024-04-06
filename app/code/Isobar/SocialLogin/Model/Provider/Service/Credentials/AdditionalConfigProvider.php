<?php

namespace Isobar\SocialLogin\Model\Provider\Service\Credentials;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;

class AdditionalConfigProvider extends ConfigProvider implements AdditionalCredentialsInterface
{
    /**
     * @var string
     */
    private $publicKeyPath;

    public function __construct(ScopeConfigInterface $scopeConfig, UrlInterface $urlBuilder, $providerCode, $consumerIdPath, $consumerSecretPath)
    {
        parent ::__construct($scopeConfig, $urlBuilder, $providerCode, $consumerIdPath, $consumerSecretPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKey()
    {
        return $this->scopeConfig->getValue($this->publicKeyPath);
    }
}
