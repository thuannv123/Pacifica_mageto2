<?php

namespace Marvelic\GBPrimePay\Model;

class GBPrimePayPurchase extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'gbprimepay_purchase';

    protected $_cacheTag = 'gbprimepay_purchase';

    protected $_eventPrefix = 'gbprimepay_purchase';

    protected function _construct()
    {
        $this->_init('Marvelic\GBPrimePay\Model\ResourceModel\GBPrimePayPurchase');
    }
    
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}
