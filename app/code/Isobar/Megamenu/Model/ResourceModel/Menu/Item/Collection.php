<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu\Item;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Model\Menu\Item;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item as MenuItemResource;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Isobar\Megamenu\Model\ResourceModel\Menu\Item
 */
class Collection extends AbstractCollection
{
    /**
     * @var array
     */
    private $customItems = [];

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(ItemInterface::ID);
        $this->_init(
            Item::class,
            MenuItemResource::class
        );
    }

    /**
     * @inheritdoc
     */
    public function addItem(DataObject $item)
    {
        if ($item->getData(ItemInterface::TYPE) == ItemInterface::CUSTOM_TYPE) {
            $this->customItems[$item->getData(ItemInterface::ENTITY_ID)] = $item;
        }

        return parent::addItem($item);
    }

    /**
     * @param int $entityId
     * @return ItemInterface|null
     */
    public function getCustomItemByEntityId($entityId)
    {
        $this->load();
        return $this->customItems[$entityId] ?? null;
    }
}
