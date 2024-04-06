<?php

namespace Isobar\ZipcodeSuggestion\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Directory\Helper\Data;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

class AdditionalConfigVars implements ConfigProviderInterface
{
    /** @var Data  */
    protected $helperData;

    /** @var CustomerRepository */
    protected $customerRepository;

    /** @var Session  */
    protected $customerSession;

    /**
     * AdditionalConfigVars constructor.
     * @param Data $helperData
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        Data $helperData,
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $customerId = $this->customerSession->getId();
        $additionalVariables['isRequireRegionIds'] = [];
        if (!empty($customerId)) {
            try {
                $customerData = $this->customerRepository->getById($customerId);
                $shippingAddresses = $customerData->getAddresses();
                $countryIds = $this->getCountryIdAddress($shippingAddresses);
                $isRequireRegion = [];
                foreach ($countryIds as $countryId) {
                    $isRequireRegion[$countryId] = $this->helperData->isRegionRequired($countryId);
                }
                $additionalVariables['isRequireRegionIds'] = $isRequireRegion;
            } catch (NoSuchEntityException $e) {
                throw new \Exception(__('Something went wrong.'));
            }
        }
        return $additionalVariables;
    }

    /**
     * @param $shippingAddresses
     * @return array
     */
    public function getCountryIdAddress($shippingAddresses)
    {
        $countryIdAddress = [];
        foreach ($shippingAddresses as $address) {
            $countryIdAddress[] = $address->getCountryId();
        }
        return $countryIdAddress;
    }
}
