<?php

declare(strict_types=1);

namespace Isobar\ImportExport\Model\Import\Product;

use Magento\Catalog\Model\Product\Url;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;

class UrlKeyManager extends \Firebear\ImportExport\Model\Import\Product\UrlKeyManager
{
    /**
     * UrlKeyManager constructor.
     * @param Url $productUrl
     * @param ResourceConnection $resource
     */
    public function __construct(
        Url $productUrl,
        ResourceConnection $resource
    ) {
        parent::__construct(
            $productUrl,
            $resource
        );
    }

    /**
     * @param $sku
     * @param $urlKey
     * @param $storeId
     *
     * @return $this
     */
    protected function updateUrlKeys($sku, $urlKey, $storeId = Store::DEFAULT_STORE_ID)
    {
        $parameters = $this->entity->getParameters();
        if (isset($parameters['enable_product_url_pattern']) && $parameters['enable_product_url_pattern'] !== 0) {
            // search old sku for new url
            $oldSku = array_search($urlKey, $this->importSkuUrlKeys[$storeId]);
            if (false !== $oldSku && $oldSku != $sku) {
                return $this;
            }
            // search old url for sku
            $oldUrlKey = array_search($sku, $this->importUrlKeys[$storeId]);
            if (false !== $oldUrlKey && $oldUrlKey != $urlKey) {
                $this->importUrlKeys[$storeId][$urlKey] = $this->importUrlKeys[$storeId][$oldUrlKey];
                unset($this->importUrlKeys[$storeId][$oldUrlKey]);
            }
            $this->importSkuUrlKeys[$storeId][$sku] = $urlKey;
        } else if (isset($parameters['enable_configurable_product_url_pattern']) && $parameters['enable_configurable_product_url_pattern'] !== 0) {
            // search old sku for new url
            $oldSku = array_search($urlKey, $this->importSkuUrlKeys[$storeId]);
            if (false !== $oldSku && $oldSku != $sku) {
                return $this;
            }
            // search old url for sku
            $oldUrlKey = array_search($sku, $this->importUrlKeys[$storeId]);
            if (false !== $oldUrlKey && $oldUrlKey != $urlKey) {
                $this->importUrlKeys[$storeId][$urlKey] = $this->importUrlKeys[$storeId][$oldUrlKey];
                unset($this->importUrlKeys[$storeId][$oldUrlKey]);
            }
            $this->importSkuUrlKeys[$storeId][$sku] = $urlKey;
        }

        return $this;
    }
}
