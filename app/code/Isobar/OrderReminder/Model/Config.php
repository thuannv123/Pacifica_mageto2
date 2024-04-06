<?php

namespace Isobar\OrderReminder\Model;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const PATH_GENERAL = "isobar_order_reminder/general/";
    const ENABLED_CONFIG = "enabled";
    const EXPIRATION_TIME_CONFIG = "expiration_time";
    const LINK_TIME_EXPIRATION_CONFIG = "link_expiration_time";
    const EMAIL_TEMPLATE_CONFIG = "pickup_template";
    const SALES_EMAIL = 'trans_email/ident_sales/name';
    const SALES_NAME = 'trans_email/ident_sales/email';

    /**
     * @var ConfigCollectionFactory
     */
    private ConfigCollectionFactory $configCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    private $expirationTime = [];
    private $linkExpirationTime = [];
    private $emailTemplate = [];

    /**
     * @param ConfigCollectionFactory $configCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigCollectionFactory $configCollectionFactory,
        ScopeConfigInterface    $scopeConfig
    )
    {
        $this->configCollectionFactory = $configCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getWebistesEnabled()
    {
        $websiteIds = [];
        $websites = $this->configCollectionFactory->create()
            ->addFieldToSelect('scope_id')
            ->addFieldToFilter('path', self::PATH_GENERAL . self::ENABLED_CONFIG)
            ->addFieldToFilter('scope', 'websites')
            ->addFieldToFilter('value', 1)
            ->load();

        if ($websites->getSize()) {
            foreach ($websites->getData() as $data) {
                $websiteIds[] = $data['scope_id'];
            }
        }

        return $websiteIds;
    }

    /**
     * @param string $scope
     * @param null $storeId
     * @return mixed
     */
    public function isEnabled($scope = ScopeInterface::SCOPE_WEBSITE, $storeId = null)
    {
        return $this->scopeConfig->getValue(self::PATH_GENERAL . self::ENABLED_CONFIG, $scope, $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getExpirationTime($storeId = null)
    {
        $storeId = $storeId ?: "default";
        if (!isset($this->expirationTime[$storeId])) {
            $this->expirationTime[$storeId] = $this->scopeConfig->getValue(
                self::PATH_GENERAL . self::EXPIRATION_TIME_CONFIG,
                ScopeInterface::SCOPE_STORE,
                $storeId == 'default' ? null : $storeId
            );
        }

        return $this->expirationTime[$storeId];
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getLinkExpirationTime($storeId = null)
    {
        $storeId = $storeId ?: "default";
        if (!isset($this->linkExpirationTime[$storeId])) {
            $this->linkExpirationTime[$storeId] = $this->scopeConfig->getValue(
                self::PATH_GENERAL . self::LINK_TIME_EXPIRATION_CONFIG,
                ScopeInterface::SCOPE_STORE,
                $storeId == 'default' ? null : $storeId
            );
        }

        return $this->linkExpirationTime[$storeId];
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getEmailTemplate($storeId = null)
    {
        $storeId = $storeId ?: "default";
        if (!isset($this->emailTemplate[$storeId])) {
            $this->emailTemplate[$storeId] = $this->scopeConfig->getValue(
                self::PATH_GENERAL . self::EMAIL_TEMPLATE_CONFIG,
                ScopeInterface::SCOPE_STORE,
                $storeId == 'default' ? null : $storeId
            );
        }

        return $this->emailTemplate[$storeId];
    }
}
