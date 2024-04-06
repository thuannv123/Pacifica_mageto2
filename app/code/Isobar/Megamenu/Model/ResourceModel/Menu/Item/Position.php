<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu\Item;

use Isobar\Megamenu\Api\Data\Menu\Item\PositionInterface;
use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Model\ResourceModel\CategoryCollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Position
 * @package Isobar\Megamenu\Model\ResourceModel\Menu\Item
 */
class Position extends AbstractDb
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Position constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PositionInterface::TABLE, PositionInterface::ID);
    }

    /**
     * @param \Isobar\Megamenu\Model\Menu\Item\Position $item
     * @param int $afterItemId
     * @return int|string
     */
    public function changePosition($item, $afterItemId)
    {
        $table = $this->getTable(PositionInterface::TABLE);
        $connection = $this->getConnection();
        $positionField = $connection->quoteIdentifier(PositionInterface::POSITION);

        $bind = [PositionInterface::POSITION => new \Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            PositionInterface::STORE_VIEW . ' = ?' => $item->getStoreView(),
            $positionField . ' > ?' => $item->getSortOrder(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterItemId) {
            $select = $connection->select()->from($table, PositionInterface::POSITION)->where(PositionInterface::ID . ' = :id');
            $position = $connection->fetchOne($select, [PositionInterface::ID => $afterItemId]);
            $position++;
        } else {
            $position = 1;
        }

        $bind = [PositionInterface::POSITION => new \Zend_Db_Expr($positionField . ' + 1')];
        $where = [
            PositionInterface::STORE_VIEW . ' = ?' => $item->getStoreView(),
            $positionField . ' >= ?' => $position
        ];
        $connection->update($table, $bind, $where);

        $data = [
            PositionInterface::POSITION => $position
        ];
        $connection->update($table, $data, [PositionInterface::ID . ' = ?' => $item->getId()]);

        return $position;
    }

    /**
     * @param int|null $store
     * @throws LocalizedException
     */
    public function importCategoryPositions($store = null)
    {
        $categories = $this->categoryCollectionFactory->create()->getIncludedInMenuCategories($store);
        $data = [];
        $entityIds = [];
        $rootCategoryId = $this->storeManager->getStore($store)->getRootCategoryId();

        /** @var Category $category */
        foreach ($categories as $category) {
            if ($category->getData('parent_id') == $rootCategoryId) {
                $entityIds[] = $category->getEntityId();
                $data = $this->generateData($category, $data, $store);
            }
        }

        $this->deleteCategories($entityIds, $store);
        $this->getConnection()->insertOnDuplicate(
            $this->getTable(PositionInterface::TABLE),
            $data,
            [PositionInterface::ENTITY_ID]
        );
    }

    /**
     * @param Category $category
     * @param array $data
     * @param int|null $store
     * @return array
     */
    private function generateData(Category $category, $data = [], $store = null)
    {
        $data[] = [
            PositionInterface::ENTITY_ID => $category->getEntityId(),
            PositionInterface::TYPE => ItemInterface::CATEGORY_TYPE,
            PositionInterface::POSITION => $category->getPosition(),
            PositionInterface::STORE_VIEW => $store
        ];

        return $data ?? [];
    }

    /**
     * @param array $entityIds
     * @param int|null $store
     */
    private function deleteCategories($entityIds = [], $store = null)
    {
        $where = [
            'type = ?' => ItemInterface::CATEGORY_TYPE,
            'entity_id NOT IN (?)' => $entityIds
        ];
        if ($store !== null) {
            $where['store_view = ?'] = $store;
        }

        $this->getConnection()->delete(
            $this->getTable(PositionInterface::TABLE),
            $where
        );
    }

    /**
     * @param string $type
     * @param int $entityId
     * @throws LocalizedException
     */
    public function deleteItem($type, $entityId)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [PositionInterface::TYPE . ' = ?' => $type, PositionInterface::ENTITY_ID . ' = (?)' => $entityId]
        );
    }
}
