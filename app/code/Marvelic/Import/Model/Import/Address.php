<?php

namespace Marvelic\Import\Model\Import;

use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\CountryWithWebsites as CountryWithWebsitesSource;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\CustomerImportExport\Model\ResourceModel\Import\Address\Storage as AddressStorage;
use Magento\Customer\Model\Indexer\Processor;

class Address extends \Firebear\ImportExport\Model\Import\Address
{
    protected $customerImportAddress;

    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionColFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory $attributesFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Model\Address\Validator\Postcode $postcodeValidator,
        \Magento\CustomerImportExport\Model\Import\Address $customerImportAddress,
        array $data = [],
        ?CountryWithWebsitesSource $countryWithWebsites = null,
        ?AddressStorage $addressStorage = null,
        ?Processor $indexerProcessor = null
    ) {
        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $storeManager,
            $collectionFactory,
            $eavConfig,
            $storageFactory,
            $addressFactory,
            $regionColFactory,
            $customerFactory,
            $attributesFactory,
            $dateTime,
            $postcodeValidator,
            $data,
            $countryWithWebsites,
            $addressStorage,
            $indexerProcessor
        );
        $this->customerImportAddress = $customerImportAddress;
    }

    protected function _importData()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $newRows = [];
            $updateRows = [];
            $attributes = [];
            $defaults = [];
            $deleteRowIds = [];
            if (\method_exists($this, 'prepareCustomerData')) {
                $this->prepareCustomerData($bunch);
            }
            foreach ($bunch as $rowNumber => $rowData) {
                $time = explode(" ", microtime());
                $startTime = $time[0] + $time[1];
                $email = $rowData['_email'];
                $rowData = $this->joinIdenticalyData($rowData);
                $rowData = $this->customChangeData($rowData);
                if (!empty($this->_parameters['use_only_fields_from_mapping'])) {
                    $rowData = $this->useOnlyFieldsFromMapping($rowData);
                    $bunch[$rowNumber] = $rowData;
                }
                if ($this->_isOptionalAddressEmpty($rowData) || !$this->validateRow($rowData, $rowNumber)) {
                    $this->addLogWriteln(__('address with email: %1 is not valided', $email), $this->output, 'info');
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNumber);
                    continue;
                }
                if (
                    isset($this->_parameters['remove_all_customer_address'])
                    && $this->_parameters['remove_all_customer_address'] == 1
                ) {
                    $this->removeCustomerAddress($rowData);
                }
                if (\Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE == $this->getBehavior($rowData)) {

                    $updateResult = $this->customerImportAddress->_prepareDataForUpdate($rowData);

                    if ($updateResult['entity_row_new']) {
                        $newRows[] = $updateResult['entity_row_new'];
                    }
                    if ($updateResult['entity_row_update']) {
                        $updateRows[] = $updateResult['entity_row_update'];
                    }

                    $attributes = $this->customerImportAddress->_mergeEntityAttributes($updateResult['attributes'], $attributes);
                    $defaults = $this->customerImportAddress->_mergeEntityAttributes($updateResult['defaults'], $defaults);
                } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
                    $deleteRowIds[] = $rowData[self::COLUMN_ADDRESS_ID];
                }
                $endTime = $time[0] + $time[1];
                $totalTime = $endTime - $startTime;
                $totalTime = round($totalTime, 5);
                $this->addLogWriteln(
                    __('address with email: %1 .... %2s', $email, $totalTime),
                    $this->output,
                    'info'
                );
            }

            $this->updateItemsCounterStats(
                $newRows,
                $updateRows,
                $deleteRowIds
            );

            $this->customerImportAddress->_saveAddressEntities(
                $newRows,
                $updateRows
            );

            $this->customerImportAddress->_saveAddressAttributes(
                $attributes
            );
            
            $this->customerImportAddress->_saveCustomerDefaults(
                $defaults
            );

            $this->_deleteAddressEntities($deleteRowIds);
        }
        return true;
    }
}
