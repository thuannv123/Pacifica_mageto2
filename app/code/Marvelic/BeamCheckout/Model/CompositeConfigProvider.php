<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Marvelic\BeamCheckout\Model\BeamCheckoutCreditCard;
use Marvelic\BeamCheckout\Model\BeamCheckoutEwallet;
use Marvelic\BeamCheckout\Model\BeamCheckoutQrcode;

class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var null
     */
    protected $cards = null;

    /**
     * @var string[]
     */
    protected $methodCodes = [
        BeamCheckoutCreditCard::CODE,
        BeamCheckoutEwallet::CODE,
        BeamCheckoutQrcode::CODE,
    ];

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $configSettings;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $configSettings
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\App\Config\ScopeConfigInterface $configSettings,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->configSettings = $configSettings;
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['descriptions'][$code] = $this->getDescriptions($code);
            }
        }
        return $config;
    }

    /**
     * @param string $code
     * @return string
     */
    protected function getDescriptions($code)
    {
        return $this->methods[$code]->getDescriptions();
    }
}
