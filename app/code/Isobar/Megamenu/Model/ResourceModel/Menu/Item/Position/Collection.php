<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position;

use Isobar\Megamenu\Api\Data\Menu\Item\PositionInterface;
use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Menu\Item\Position;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(PositionInterface::ID);
        $this->_init(
            Position::class,
            PositionResource::class
        );
    }

    /**
     * @param int $storeId
     * @return Collection
     */
    public function getSortedCollection($storeId)
    {
        return $this->addFieldToFilter(PositionInterface::STORE_VIEW, $storeId)
            ->addOrder(PositionInterface::POSITION, 'asc');
    }

    /**
     * @return void
     */
    public function joinLinkTable()
    {
        $this->getSelect()->joinLeft(
            ['links' => $this->getTable(LinkInterface::TABLE_NAME)],
            sprintf(
                'main_table.%s = "%s" AND main_table.%s = links.%s',
                ItemInterface::TYPE,
                ItemInterface::CUSTOM_TYPE,
                ItemInterface::ENTITY_ID,
                LinkInterface::ENTITY_ID
            ),
            [LinkInterface::TYPE]
        );
    }

    /**
     * @param array $values
     * @return void
     */
    public function addLinkTypeFilter(array $values)
    {
        $this->getSelect()->where(
            sprintf(
                'coalesce(%s, 0) IN (%s)',
                LinkInterface::TYPE,
                implode(', ', $values)
            )
        );
    }
}
