<?php

namespace Isobar\CustomerDataMigration\Model\Import;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\CustomerImportExport\Model\Import\Customer;


//class CustomerJob extends \Magento\CustomerImportExport\Model\Import\Customer
class CustomerJob extends \Firebear\ImportExport\Model\Import\Customer
{
    /**
     * Customer fields in file
     */
    protected $customerFields = [
        CustomerInterface::GROUP_ID,
        CustomerInterface::STORE_ID,
        CustomerInterface::UPDATED_AT,
        CustomerInterface::CREATED_AT,
        CustomerInterface::CREATED_IN,
        CustomerInterface::PREFIX,
        CustomerInterface::FIRSTNAME,
        CustomerInterface::MIDDLENAME,
        CustomerInterface::LASTNAME,
        CustomerInterface::SUFFIX,
        CustomerInterface::DOB,
        'password_hash',
        CustomerInterface::TAXVAT,
        CustomerInterface::CONFIRMATION,
        CustomerInterface::GENDER,
        'rp_token',
        'rp_token_created_at',
        'failures_num',
        'first_failure',
        'lock_expires',
        'is_password_reset_require'
    ];


    /**
     * @param array $rowData
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareDataForUpdate(array $rowData)
    {
        $multiSeparator = $this->getMultipleValueSeparator();
        $entitiesToCreate = [];
        $entitiesToUpdate = [];
        $attributesToSave = [];

        // entity table data
        $now = new \DateTime();
        if (empty($rowData['created_at'])) {
            $createdAt = $now;
        } else {
            $createdAt = (new \DateTime())->setTimestamp(strtotime($rowData['created_at']));
        }

        $emailInLowercase = strtolower(trim($rowData[self::COLUMN_EMAIL]));
        $newCustomer = false;
        $entityId = $this->_getCustomerId($emailInLowercase, $rowData[self::COLUMN_WEBSITE]);
        if (!$entityId) {
            // create
            $newCustomer = true;
            $entityId = $this->_getNextEntityId();
            $this->_newCustomers[$emailInLowercase][$rowData[self::COLUMN_WEBSITE]] = $entityId;
        }

        // password change/set
        if (isset($rowData['password']) && strlen($rowData['password'])) {
            $rowData['password_hash'] = $this->_customerModel->hashPassword($rowData['password']);
        }
        $entityRow = ['entity_id' => $entityId];
        // attribute values
        foreach (array_intersect_key($rowData, $this->_attributes) as $attributeCode => $value) {
            $attributeParameters = $this->_attributes[$attributeCode];
            if (in_array($attributeParameters['type'], ['select', 'boolean'])) {
                $value = $this->getSelectAttrIdByValue($attributeParameters, $value);
                if ($attributeCode === CustomerInterface::GENDER && $value === 0) {
                    $value = null;
                }
            } elseif ('multiselect' == $attributeParameters['type']) {
                $ids = [];
                foreach (explode($multiSeparator, mb_strtolower($value)) as $subValue) {
                    $ids[] = $this->getSelectAttrIdByValue($attributeParameters, $subValue);
                }
                $value = implode(',', $ids);
            } elseif ('datetime' == $attributeParameters['type'] && !empty($value)) {
                $value = (new \DateTime())->setTimestamp(strtotime($value));
                $value = $value->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            }

            if (!$this->_attributes[$attributeCode]['is_static']) {
                /** @var $attribute \Magento\Customer\Model\Attribute */
                $attribute = $this->_customerModel->getAttribute($attributeCode);
                $backendModel = $attribute->getBackendModel();
                if ($backendModel
                    && $attribute->getFrontendInput() != 'select'
                    && $attribute->getFrontendInput() != 'datetime') {
                    $attribute->getBackend()->beforeSave($this->_customerModel->setData($attributeCode, $value));
                    $value = $this->_customerModel->getData($attributeCode);
                }
                $attributesToSave[$attribute->getBackend()
                    ->getTable()][$entityId][$attributeParameters['id']] = $value;

                // restore 'backend_model' to avoid default setting
                $attribute->setBackendModel($backendModel);
            } else {
                $entityRow[$attributeCode] = $value;
            }
        }
        if ((!empty($this->_parameters['send_reset_password_after_import']) && $this->_parameters['send_reset_password_after_import'] == 1)) {
            $entityRow['is_password_reset_require'] = 1;
        }

        if ($newCustomer) {
            // create
            $entityRow['group_id'] = empty($rowData['group_id']) ? self::DEFAULT_GROUP_ID : $rowData['group_id'];
            $entityRow['store_id'] = empty($rowData[self::COLUMN_STORE])
                ? \Magento\Store\Model\Store::DEFAULT_STORE_ID : $this->_storeCodeToId[$rowData[self::COLUMN_STORE]];
            $entityRow['created_at'] = $createdAt->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $entityRow['updated_at'] = $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $entityRow['website_id'] = $this->_websiteCodeToId[$rowData[self::COLUMN_WEBSITE]];
            $entityRow['email'] = $emailInLowercase;
            $entityRow['is_active'] = 1;
            $entitiesToCreate[] = $entityRow;
        } else {
            // edit
            $entityRow['updated_at'] = $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            if (!empty($rowData[self::COLUMN_STORE])) {
                $entityRow['store_id'] = $this->_storeCodeToId[$rowData[self::COLUMN_STORE]];
            } else {
                $entityRow['store_id'] = $this->getCustomerStoreId($emailInLowercase, $rowData[self::COLUMN_WEBSITE]);
            }
            $entitiesToUpdate[] = $entityRow;
        }

        return [
            self::ENTITIES_TO_CREATE_KEY => $entitiesToCreate,
            self::ENTITIES_TO_UPDATE_KEY => $entitiesToUpdate,
            self::ATTRIBUTES_TO_SAVE_KEY => $attributesToSave
        ];
    }

    /**
     * @param string $email
     * @param string $websiteCode
     * @return bool|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerStoreId(string $email, string $websiteCode)
    {
        $websiteId = (int) $this->getWebsiteId($websiteCode);
        $storeId = $this->getCustomerStorage()->getCustomerStoreId($email, $websiteId);
        if ($storeId === null || $storeId === false) {
            $defaultStore = $this->_storeManager->getWebsite($websiteId)->getDefaultStore();
            $storeId = $defaultStore ? $defaultStore->getId() : \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }
        return $storeId;
    }

    /**
     * Update and insert data in entity table
     *
     * @param array $entitiesToCreate Rows for insert
     * @param array $entitiesToUpdate Rows for update
     * @return $this
     */
    protected function _saveCustomerEntities(array $entitiesToCreate, array $entitiesToUpdate)
    {
        if ($entitiesToCreate) {
            $this->_connection->insertMultiple($this->_entityTable, $entitiesToCreate);
        }

        if ($entitiesToUpdate) {
            $this->_connection->insertOnDuplicate(
                $this->_entityTable,
                $entitiesToUpdate,
                $this->getCustomerEntityFieldsToUpdate($entitiesToUpdate)
            );
        }

        return $this;
    }

    /**
     * Filter the entity that are being updated so we only change fields found in the importer file
     *
     * @param array $entitiesToUpdate
     * @return array
     */
    protected function getCustomerEntityFieldsToUpdate(array $entitiesToUpdate): array
    {
        $firstCustomer = reset($entitiesToUpdate);
        $columnsToUpdate = array_keys($firstCustomer);
        $customerFieldsToUpdate = array_filter(
            $this->customerFields,
            function ($field) use ($columnsToUpdate) {
                return in_array($field, $columnsToUpdate);
            }
        );
        return $customerFieldsToUpdate;
    }
}