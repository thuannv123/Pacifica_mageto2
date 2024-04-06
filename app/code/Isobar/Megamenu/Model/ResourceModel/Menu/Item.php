<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu;

use Isobar\Megamenu\Api\Data\Menu\Item\PositionInterface;
use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Model\ResourceModel\CategoryCollection;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Item
 * @package Isobar\Megamenu\Model\ResourceModel\Menu
 */
class Item extends AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Item constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param string $connectionName
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ItemInterface::TABLE_NAME, ItemInterface::ID);
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
            [ItemInterface::TYPE . '=?' => $type, ItemInterface::ENTITY_ID . '=?' => $entityId]
        );
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $data = [];
        $storeIds = [];

        switch ($object->getType()) {
            case ItemInterface::CUSTOM_TYPE:
                foreach ($this->storeManager->getStores() as $store) {
                    $storeIds[] = $store->getId();
                }
                break;
            case ItemInterface::CATEGORY_TYPE:
                $category = $this->categoryRepository->get($object->getEntityId());
                if ($category->getLevel() == CategoryCollection::MENU_LEVEL) {
                    $storeIds = $category->getStoreIds();
                }
                break;
        }

        foreach ($storeIds as $storeId) {
            $data[] = [
                PositionInterface::STORE_VIEW => $storeId,
                PositionInterface::TYPE => $object->getType(),
                PositionInterface::POSITION => $object->getSortOrder() ?: $category->getPosition(),
                PositionInterface::ENTITY_ID => $object->getEntityId()
            ];
        }

        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(PositionInterface::TABLE),
                $data,
                [ItemInterface::ENTITY_ID]
            );
        }

        return parent::_afterSave($object);
    }
}
