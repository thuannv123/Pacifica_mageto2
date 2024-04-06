<?php

namespace Isobar\InventoryInStorePickupShippingApi\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class GetCarrierTitle extends \Magento\InventoryInStorePickupShippingApi\Model\Carrier\GetCarrierTitle
{
    private const CONFIG_PATH = 'carriers/instore/title';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($scopeConfig);
    }

    /**
     * Get In-Store Pickup carrier title
     *
     * @param int|null $storeId
     * @return string
     */
    public function execute(?int $storeId = null): string
    {
        $config = $storeId ?
            $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE, $storeId)
            : $this->scopeConfig->getValue(self::CONFIG_PATH);

        return $config ? $config : 'In-store Pickup';
    }
}
