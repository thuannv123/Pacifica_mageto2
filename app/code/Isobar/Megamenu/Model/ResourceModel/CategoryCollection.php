<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CatalogCategoryCollection;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CategoryCollection
 * @package Isobar\Megamenu\Model\ResourceModel
 */
class CategoryCollection extends CatalogCategoryCollection
{
    /**
     * @var int
     */
    const MENU_LEVEL = 2;

    /**
     * @param int|null $storeId
     * @return CatalogCategoryCollection
     * @throws LocalizedException
     */
    public function getIncludedInMenuCategories($storeId = null)
    {
        if ($storeId !== null) {
            $this->setStoreId($storeId);
        }

        $this->addLevelFilter(self::MENU_LEVEL);
        $this->addAttributeToFilter('include_in_menu', 1);
        $this->addAttributeToSelect('name', true);
        $this->addIsActiveFilter();
        $this->addOrder('position', Collection::SORT_ORDER_ASC);

        return $this;
    }
}
