<?php
namespace Marvelic\GBPrimePay\Model\ResourceModel\GBPrimePayPurchase;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'gbprimepay_purchase_collection';
	protected $_eventObject = 'purchase_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Marvelic\GBPrimePay\Model\GBPrimePayPurchase', 'Marvelic\GBPrimePay\Model\ResourceModel\GBPrimePayPurchase');
	}

}