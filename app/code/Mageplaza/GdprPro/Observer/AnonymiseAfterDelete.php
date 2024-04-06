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
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GdprPro\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\GdprPro\Helper\Anonymise as HelperAnonymise;
use Mageplaza\GdprPro\Model\DeleteAccountLogsFactory;
use Psr\Log\LoggerInterface;

/**
 * Class AnonymiseAfterDelete
 *
 * @package Mageplaza\GdprPro\Observer
 */
class AnonymiseAfterDelete implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HelperAnonymise
     */
    protected $_helperAnonymise;

    /**
     * @var DeleteAccountLogsFactory
     */
    protected $deleteAccountLogsFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * AnonymiseAfterDelete constructor.
     *
     * @param HelperAnonymise $helperAnonymise
     * @param LoggerInterface $logger
     * @param DeleteAccountLogsFactory $deleteAccountLogsFactory
     * @param DateTime $date
     */
    public function __construct(
        HelperAnonymise $helperAnonymise,
        LoggerInterface $logger,
        DeleteAccountLogsFactory $deleteAccountLogsFactory,
        DateTime $date
    ) {
        $this->_helperAnonymise         = $helperAnonymise;
        $this->logger                   = $logger;
        $this->deleteAccountLogsFactory = $deleteAccountLogsFactory;
        $this->date                     = $date;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        $deleteLog = $this->deleteAccountLogsFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();

        if ($deleteLog && $deleteLog->getId()) {
            $deleteLog->setData('status', 0);
            $deleteLog->setData('updated_at', $this->date->date());
            $deleteLog->save();
        }

        /**
         * Delete Abandoned Cart
         */
        try {
            $quoteModel = $this->_helperAnonymise->getAbandonedcartByEmail($customer->getEmail());
            foreach ($quoteModel as $quote) {
                $quote_store_id = $quote->getStoreId();
                if ($this->_helperAnonymise->getAllowDeleteAbandonedcart($quote_store_id)) {
                    $this->_helperAnonymise->deleAbandonedcart($quote);
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        try {
            $orderModel = $this->_helperAnonymise->getOrderByEmail($customer->getEmail());
            foreach ($orderModel as $order) {
                if ($this->_helperAnonymise->getAnonymiseAccountPurchase($order->getStoreId())) {
                    /**
                     * Anonymise info order processing
                     */
                    if ($this->_helperAnonymise->getAllowAnonymiseAddress($order->getStoreId())) {
                        $this->_helperAnonymise->anonymiseAddressInOrder($order);
                    }

                    $this->_helperAnonymise->anonymiseAccountInOrder($order);
                }
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
