<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model;

class BeamCheckoutPurchase extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'beamcheckout_purchase';

    protected $_cacheTag = 'beamcheckout_purchase';

    protected $_eventPrefix = 'beamcheckout_purchase';

    protected function _construct()
    {
        $this->_init('Marvelic\BeamCheckout\Model\ResourceModel\BeamCheckoutPurchase');
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

    /**
     * Get Beam Order ID
     *
     * @return int|null
     */
    public function getBeamOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set Beam Order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setBeamOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * Get Beam Order Increment ID
     *
     * @return string|null
     */
    public function getBeamOrderIncrementId()
    {
        return $this->getData('increment_id');
    }

    /**
     * Set Beam Order Increment ID
     *
     * @param string $orderIncrementId
     * @return $this
     */
    public function setBeamOrderIncrementId($orderIncrementId)
    {
        return $this->setData('increment_id', $orderIncrementId);
    }

    /**
     * Get Beam Purchase ID
     *
     * @return string|null
     */
    public function getBeamPurchaseId()
    {
        return $this->getData('purchaseId');
    }

    /**
     * Set Beam Purchase ID
     *
     * @param string $purchaseId
     * @return $this
     */
    public function setBeamPurchaseId($purchaseId)
    {
        return $this->setData('purchaseId', $purchaseId);
    }

    /**
     * Get Beam Payment Link
     *
     * @return string|null
     */
    public function getBeamPaymentLink()
    {
        return $this->getData('paymentLink');
    }

    /**
     * Set Beam Payment Link
     *
     * @param string $paymentLink
     * @return $this
     */
    public function setBeamPaymentLink($paymentLink)
    {
        return $this->setData('paymentLink', $paymentLink);
    }
}
