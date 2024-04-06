<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GdprPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Plugin\Adminhtml;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\GdprPro\Helper\Data;
use Mageplaza\GdprPro\Model\DeleteAccountLogsFactory;

/**
 * Class DeleteCustomer
 * @package Mageplaza\GdprPro\Plugin\Adminhtml
 */
class DeleteCustomer
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var DeleteAccountLogsFactory
     */
    protected $deleteAccountLogsFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * DeleteCustomer constructor.
     *
     * @param Data $helperData
     * @param DeleteAccountLogsFactory $deleteAccountLogsFactory
     * @param DateTime $date
     */
    public function __construct(
        Data $helperData,
        DeleteAccountLogsFactory $deleteAccountLogsFactory,
        DateTime $date
    ) {
        $this->helperData               = $helperData;
        $this->deleteAccountLogsFactory = $deleteAccountLogsFactory;
        $this->date                     = $date;
    }

    /**
     * @param CustomerRepositoryInterface $subject
     * @param callable $proceed
     * @param int $customerId
     *
     * @return mixed
     */
    public function aroundDeleteById(CustomerRepositoryInterface $subject, callable $proceed, $customerId)
    {
        $result = $proceed($customerId);

        if ($this->helperData->isEnabled() && $result) {
            $deleteLog = $this->deleteAccountLogsFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)->getFirstItem();

            if ($deleteLog && $deleteLog->getId()) {
                $deleteLog->setData('status', 0);
                $deleteLog->setData('updated_at', $this->date->date());
                $deleteLog->save();
            }
        }

        return $result;
    }
}
