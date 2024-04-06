<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Marvelic\AmastyLabel\Model\ResourceModel\Label\Grid;

use Amasty\Label\Model\ResourceModel\Label\Grid\Collection as AmastyLabelGridCollection;

class Collection extends AmastyLabelGridCollection
{
    protected function _beforeLoad()
    {
        $this->joinCatalogParts();

        return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult::_beforeLoad();
    }
    private function joinCatalogParts(): void
    {
        $select = $this->getSelect();
        $tableName = $this->getTable(\Amasty\Label\Setup\Uninstall::AMASTY_LABEL_CATALOG_PARTS_TABLE);
        $columns = $this->getCatalogPartColumns();
        $mods = [
            self::CATEGORY_PREFIX => \Amasty\Label\Model\ResourceModel\Label\Collection::MODE_LIST,
            self::PRODUCT_PREFIX => \Amasty\Label\Model\ResourceModel\Label\Collection::MODE_PDP
        ];

        foreach ($mods as $modName => $modCode) {
            $joinColumns = [];

            foreach ($columns as $columnName) {
                $joinColumns["{$modName}_{$columnName}"] = $columnName;
            }

            $select->join(
                [$modName => $tableName],
                sprintf(
                    'main_table.%1$s = %2$s.%1$s and %2$s.%3$s = %4$d',
                    \Amasty\Label\Api\Data\LabelInterface::LABEL_ID,
                    $modName,
                    \Amasty\Label\Model\Label\Parts\FrontendSettings::TYPE,
                    $modCode
                ),
                $joinColumns
            );
        }
    }
}
