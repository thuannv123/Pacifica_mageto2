<?php
namespace Marvelic\VisualMerchandiser\Model\Sorting;

use \Magento\Framework\DB\Select;

class OutStockBottom extends \Magento\VisualMerchandiser\Model\Sorting\OutStockBottom
{
    const XML_PATH_MIN_STOCK_THRESHOLD = 'visualmerchandiser/options/minimum_stock_threshold';
    
    public function sort(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        if (!$this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            return $collection;
        }

        $minStockThreshold = (int)$this->scopeConfig->getValue(self::XML_PATH_MIN_STOCK_THRESHOLD);

        $baseSet = clone $collection;
        $finalSet = clone $collection;

        $collection->getSelect()
            ->having('stock <= ?', $minStockThreshold)
            ->reset(Select::ORDER)
            ->order('stock ' . $collection::SORT_ORDER_DESC);

        $resultIds = [];

        $collection->load();

        foreach ($collection as $item) {
            $resultIds[] = $item->getId();
        }

        $ids = array_unique(array_merge($resultIds, $baseSet->getAllIds()));

        $finalSet->getSelect()
            ->reset(Select::ORDER)
            ->reset(Select::WHERE);

        $finalSet->addAttributeToFilter('entity_id', ['in' => $ids]);
        if (count($ids)) {
            $finalSet->getSelect()->order(new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $ids) . ')'));
        }
        $finalSet->getSelect()
            ->reset(Select::ORDER)
            ->order('stock ' . $finalSet::SORT_ORDER_DESC);
        return $finalSet;
    }
}
