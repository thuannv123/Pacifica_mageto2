<?php

namespace Isobar\ZipcodeSuggestion\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Directory\Helper\Data;

class ValidateAddressCustomer implements ObserverInterface
{
    /** @var CustomerRepository */
    protected $customerRepository;

    /** @var Session  */
    protected $customerSession;

    /** @var UrlInterface  */
    protected $urlRedirect;

    /** @var AddressFactory  */
    protected $addressFactory;

    /** @var Data  */
    protected $helperData;

    /** @var ManagerInterface */
    private $messageManager;


    /**
     * SalesOrderPlace constructor.
     * @param Data $helperData
     * @param AddressFactory $addressFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $urlInterface
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        Data $helperData,
        AddressFactory $addressFactory,
        ManagerInterface $messageManager,
        UrlInterface $urlInterface,
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->helperData = $helperData;
        $this->addressFactory = $addressFactory;
        $this->messageManager = $messageManager;
        $this->urlRedirect = $urlInterface;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $customerId = $this->customerSession->getId();
        if (!empty($customerId)) {
            $customerData = $this->customerRepository->getById($customerId);
            $shippingAddressesId = $customerData->getDefaultShipping();
            $shippingAddress = $this->addressFactory->create()->load($shippingAddressesId);
            $regionId = $shippingAddress->getRegionId();
            if ($this->helperData->isRegionRequired($shippingAddress->getCountryId())) {
                if (empty($regionId)) {
                    $message = 'Please check the shipping address information. "regionId" is required. Enter and try again..';
                    $this->messageManager->addError(__($message));
                    $redirectionUrl = $this->urlRedirect
                        ->getUrl('customer/address/edit/id/'. $shippingAddressesId);
                    return $action->getResponse()->setRedirect($redirectionUrl);
                }
            }
        }
        return $this;
    }
}
