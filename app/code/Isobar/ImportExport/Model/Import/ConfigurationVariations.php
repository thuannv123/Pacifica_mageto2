<?php

namespace Isobar\ImportExport\Model\Import;

use Firebear\ImportExport\Model\Import\Product\ConfigurationVariations as ParentConfigurationVariations;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\Store;

class ConfigurationVariations extends ParentConfigurationVariations
{
    /**
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function saveAttributes(array $data): self
    {
        if (empty($data['eav_attributes'])) {
            return $this;
        }

        $storeCode = Store::DEFAULT_STORE_ID;

        if (isset($data['eav_attributes']['store_view_code']) && $data['eav_attributes']['store_view_code']) {
            $storeCode = $data['eav_attributes']['store_view_code'];
            unset($data['eav_attributes']['store_view_code']);
        }

        $this->productAction->updateAttributes(
            [$data[$this->getProductIdentifierField()]],
            $data['eav_attributes'],
            $storeCode
        );
        return $this;
    }

    private function getProductIdentifierField()
    {
        if (!$this->productEntityIdentifierField) {
            $this->productEntityIdentifierField = $this->metadataPool->getMetadata(ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }
}
