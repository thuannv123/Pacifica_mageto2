<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Link
 * @package Isobar\Megamenu\Model\ResourceModel\Menu
 */
class Link extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LinkInterface::TABLE_NAME, LinkInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $itemContentTable = $this->getTable(ItemInterface::TABLE_NAME);
        $select->joinInner(
            $itemContentTable,
            sprintf(
                '%s.entity_id = %s.entity_id AND store_id = 0 AND %s.type = "custom"',
                $itemContentTable,
                $this->getMainTable(),
                $itemContentTable
            )
        );

        return $select;
    }
}
