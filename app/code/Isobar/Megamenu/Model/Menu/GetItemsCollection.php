<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Collection as ItemCollection;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Store\Model\Store;

/**
 * Class GetItemsCollection
 * @package Isobar\Megamenu\Model\Menu
 */
class GetItemsCollection
{
    /**
     * @var ItemCollectionFactory
     */
    protected $collectionFactory;

    /**
     * GetItemsCollection constructor.
     * @param ItemCollectionFactory $collectionFactory
     */
    public function __construct(
        ItemCollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $storeId
     * @return ItemCollection
     */
    public function execute(int $storeId): ItemCollection
    {
        /** @var ItemCollection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('store_id', [$storeId, Store::DEFAULT_STORE_ID]);
        $collection->addOrder('store_id', Collection::SORT_ORDER_ASC);
        $collection->getSelect()->joinLeft(
            ['links' => $collection->getTable(LinkInterface::TABLE_NAME)],
            'main_table.entity_id = links.entity_id AND main_table.type = \'custom\'',
            ['url' => LinkInterface::LINK, LinkInterface::TYPE]
        );

        return $collection;
    }
}
