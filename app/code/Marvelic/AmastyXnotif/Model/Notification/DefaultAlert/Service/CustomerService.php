<?php
namespace Marvelic\AmastyXnotif\Model\Notification\DefaultAlert\Service;

use Amasty\Xnotif\Model\ConfigProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Model\AbstractModel;
use Magento\ProductAlert\Model\Price as PriceAlert;
use Magento\ProductAlert\Model\ResourceModel\Price\Collection as PriceAlertCollection;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockAlertCollection;
use Magento\ProductAlert\Model\Stock as StockAlert;
use Magento\Store\Model\StoreManagerInterface;

class CustomerService extends \Amasty\Xnotif\Model\Notification\DefaultAlert\Service\CustomerService
{
  
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerInterface[]|null
     */
    private $loadedCustomersByIds;

    /**
     * @var CustomerInterface[]|null
     */
    private $loadedCustomersByEmails;

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerFactory $customerFactory,
        ConfigProvider $configProvider
    ) {
        parent::__construct($storeManager,$customerCollectionFactory,$customerFactory,$configProvider);
        $this->storeManager = $storeManager;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * @param PriceAlertCollection|StockAlertCollection|Collection $alertCollection
     * @return CustomerModel[]
     */
    public function loadCustomersByEmails(Collection $alertCollection): array
    {
        /** @var StockAlert|PriceAlert $alert */
        $alert = $alertCollection->getFirstItem();
        if (!$alert->getId()) {
            return [];
        }

        $websiteId = (int)$alert->getWebsiteId();
        $customerEmails = array_map(
        /** @var StockAlert|PriceAlert $alert */
            static function (AbstractModel $alert) {
                return $alert->getCustomerId() ? '' : $alert->getEmail();
            },
            $alertCollection->getItems()
        );
        $customerEmails = array_unique(array_filter($customerEmails));

        $customers = $this->loadCustomers([
            ['email', ['in' => $customerEmails]],
            ['website_id', ['eq' => $websiteId]]
        ]);
        $arr_customer = [];
        $arr_customer_key = [];
        foreach ($customers as $key => $customer) {
            $arr_customer[$customer->getEmail()] = $customer;
            $arr_customer_key[$customer->getEmail()] = $customer->getData();
        }


        return array_combine(array_column($arr_customer_key, 'email'), $arr_customer);
    }

    /**
     * @param array[] $conditions [['fieldName', ['operator' => 'value']], ...] Applies conditions with AND operator.
     * @return CustomerModel[]
     */
    private function loadCustomers(array $conditions): array
    {
        $customerCollection = $this->customerCollectionFactory->create();
        foreach ($conditions as $condition) {
            $customerCollection->addFieldToFilter(...$condition);
        }
        return $customerCollection->getItems();
    }
}
