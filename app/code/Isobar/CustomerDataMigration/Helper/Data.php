<?php

namespace Isobar\CustomerDataMigration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Isobar\CustomerDataMigration\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_MESSAGE = 'customerdatamigration/general/message';
    const XML_PATH_SCHEDULE = 'customerdatamigration/general/schedule';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        EncryptorInterface $encryptor
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
    }

    /**
     * @param string $scope
     * @return mixed
     */
    public function getConfigSchedule($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SCHEDULE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $scope
     * @return mixed
     */
    public function getConfigMessage($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
