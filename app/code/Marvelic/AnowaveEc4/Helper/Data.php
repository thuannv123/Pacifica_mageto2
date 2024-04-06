<?php

namespace Marvelic\AnowaveEc4\Helper;

class Data extends \Anowave\Ec4\Helper\Data
{
    /**
     * Shared measurement ID config path
     *
     * @var string
     */
    const CONFIG_SHARED = 'ec/api/google_gtm_ua4_measurement_id';

    /**
     * Dedicated measurement ID config path
     *
     * @var string
     */
    const CONFIG_DEDICATED = 'ec/api/google_gtm_ua4_measurement_id_backend';

    /**
     * Package name
     * @var string
     */
    protected $package = 'MAGE2-GTMGA4';

    /**
     * Config path
     * @var string
     */
    protected $config = 'ec4/general/license';

    /**
     * @var \Anowave\Ec\Helper\Data
     */
    protected $baseHelper;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    protected $proxy;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    protected $ruleRepositoryInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor 
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Anowave\Ec\Helper\Data $baseHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Anowave\Ec\Helper\Data $baseHelper,
        \Magento\Checkout\Model\Session\Proxy $proxy,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepositoryInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->baseHelper = $baseHelper;

        /**
         * Set proxy 
         * 
         * @var \Magento\Checkout\Model\Session\Proxy $proxy
         */
        $this->proxy = $proxy;

        /**
         * Set rule repository interface 
         * 
         * @var \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepositoryInterface
         */
        $this->ruleRepositoryInterface = $ruleRepositoryInterface;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context, $baseHelper, $proxy, $ruleRepositoryInterface);
    }

    /**
     * Get Measurement ID
     *
     * @param int $store
     * @return string
     */
    public function getMeasurementIdStore($store): string
    {
        return (string) $this->scopeConfig->getValue($this->getMeasurementIdConfig(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get measurement api secret
     *
     * @return string
     */
    public function getMeasurementApiSecretKey($store): string
    {
        return (string) $this->scopeConfig->getValue('ec/api/google_gtm_ua4_measurement_api_secret', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}
