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
use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GdprPro\Helper\ConfirmUrl as ConfirmUrl;
use Mageplaza\GdprPro\Helper\Data as HelperData;
use Mageplaza\GdprPro\Model\DeleteAccountLogsFactory;
use Zend_Db_Expr;
use Zend_Db_Select;

/**
 * Class EmailConfirmBeforeDelete
 * @package Mageplaza\GdprPro\Observer
 */
class EmailConfirmBeforeDelete implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var ConfirmUrl
     */
    protected $_helperConfirm;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Mail transport builder
     *
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /***
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var DeleteAccountLogsFactory
     */
    protected $deleteAccountLogsFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /***
     * EmailConfirmBeforeDelete constructor.
     *
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param ConfirmUrl $confirmUrl
     * @param ManagerInterface $messageManager
     * @param CustomerFactory $customerFactory
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param RequestInterface $requestInterface
     * @param DeleteAccountLogsFactory $deleteAccountLogsFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderFactory $orderFactory
     * @param DateTime $date
     * @param UrlInterface $backendUrl
     */
    public function __construct(
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ConfirmUrl $confirmUrl,
        ManagerInterface $messageManager,
        CustomerFactory $customerFactory,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        RequestInterface $requestInterface,
        DeleteAccountLogsFactory $deleteAccountLogsFactory,
        OrderCollectionFactory $orderCollectionFactory,
        OrderFactory $orderFactory,
        DateTime $date,
        UrlInterface $backendUrl
    ) {
        $this->_helperData              = $helperData;
        $this->storeManager             = $storeManager;
        $this->_transportBuilder        = $transportBuilder;
        $this->_messageManager          = $messageManager;
        $this->_helperConfirm           = $confirmUrl;
        $this->_customerFactory         = $customerFactory;
        $this->customerSession          = $customerSession;
        $this->_customerRepository      = $customerRepository;
        $this->_request                 = $requestInterface;
        $this->deleteAccountLogsFactory = $deleteAccountLogsFactory;
        $this->orderCollectionFactory   = $orderCollectionFactory;
        $this->date                     = $date;
        $this->backendUrl               = $backendUrl;
        $this->orderFactory             = $orderFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $paramToken    = $this->_request->getParam('tokenEmail');
            $storeId       = $this->storeManager->getStore()->getId();
            $customerId    = $this->customerSession->getCustomerId();
            $customer      = $this->_customerRepository->getById($customerId);
            $customerEmail = $customer->getEmail();
            $customer      = $this->_customerFactory->create()->getCollection()->addFieldToFilter(
                'email',
                $customerEmail
            )->getLastItem();
            $logCollection = $this->deleteAccountLogsFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);

            $deleteData = [
                'customer_name'  => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'customer_id'    => $customer->getId(),
                'customer_email' => $customerEmail,
                'status'         => 1,
                'store_id'       => $storeId,
                'order_count'    => $this->getOrderCount($customerId, $storeId),
                'grand_total'    => $this->getOrderTotals($customerId, $storeId)->getData('totals') ?: 0,
                'refunded'       => $this->getOrderTotals($customerId, $storeId)->getData('refunded') ?: 0,
                'updated_at'     => $this->date->date()
            ];

            if (!$logCollection->getSize()) {
                $this->deleteAccountLogsFactory->create()->setData($deleteData)->save();
            } else {
                $log = $logCollection->getFirstItem();
                $log->addData($deleteData)->save();
            }

            if ($this->_helperData->getEnableEmailAdminNof() && !$paramToken && $customer->getId()) {
                $receiverInf = $this->_helperData->getReceiverInf($storeId);
                $transport   = $this->_transportBuilder
                    ->setTemplateIdentifier($this->_helperData->getEmailAdminNofTemplate($storeId))
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                    ->setTemplateVars(
                        [
                            'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                            'emailSubject'  => __('Notification When Customer Delete Your Account')
                        ]
                    )
                    ->setFrom($this->_helperData->getSenderEmail($storeId))
                    ->addTo($receiverInf['email'], $receiverInf['name'])
                    ->getTransport();

                $transport->sendMessage();
            }

            if ($this->_helperData->getEnableEmailConfirm() && !$paramToken && $customer->getId()) {
                /** Send email confirm deletion to customer */
                $store     = $this->storeManager->getStore();
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->_helperData->getEmailConfirmTemplate())
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
                    ->setTemplateVars(
                        [
                            'customer_firstname' => $customer->getFirstname(),
                            'confirm_delete_url' => $this->_helperConfirm->getConfirmUrl($customer->getId())
                        ]
                    )
                    ->setFrom($this->_helperData->getSenderEmail($store->getId()))
                    ->addTo($customer->getEmail(), $customer->getName())
                    ->getTransport();

                $transport->sendMessage();
                $this->_messageManager->addSuccessMessage(__('A confirmation email has been sent to you. Please check it to confirm your deletion'));
                $this->_helperConfirm->redirectCustomerEdit($observer->getEvent()->getControllerAction());
            }
        } catch (Exception $e) {
            $this->_messageManager->addErrorMessage(__('Somethings when wrong when sending email.'));
            $this->_helperConfirm->redirectCustomerEdit($observer->getEvent()->getControllerAction());
        }
    }

    /**
     * @param int $customerId
     * @param int $storeId
     *
     * @return int
     */
    public function getOrderCount($customerId, $storeId)
    {
        return $this->orderFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('store_id', $storeId)->getSize();
    }

    /**
     * @param int $customerId
     * @param int $storeId
     *
     * @return DataObject
     */
    public function getOrderTotals($customerId, $storeId)
    {
        $resource = $this->orderCollectionFactory->create();

        $resource->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns([
            'totals'   => new Zend_Db_Expr('SUM(base_grand_total)'),
            'refunded' => new Zend_Db_Expr('SUM(base_total_refunded)')
        ])->where(
            'main_table.customer_id = ?',
            $customerId
        )->where(
            'main_table.store_id = ?',
            $storeId
        );

        return $resource->getFirstItem();
    }
}
