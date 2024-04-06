<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Config\Source\Status;
use Isobar\Megamenu\Model\Config\Source\UrlKey;
use Isobar\Megamenu\Model\Provider\FieldsByStore;
use Isobar\Megamenu\Model\ResourceModel\CategoryCollection as CategoryCollectionResource;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position\Collection as PositionCollection;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position\CollectionFactory as PositionCollectionFactory;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TreeResolver
 * @package Isobar\Megamenu\Model\Menu
 */
class TreeResolver
{
    const ITEM_POSITION_CLASS_PREFIX = 'nav-';

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var array
     */
    private $noIncludeInMenu = [];

    /**
     * @var PositionCollectionFactory
     */
    private $positionCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Node[]
     */
    private $menu;

    /**
     * @var int
     */
    private $positionCounter = 1;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    /**
     * @var GetItemsCollection
     */
    private $getItemsCollection;

    /**
     * @var UrlKey
     */
    private $urlKey;

    /**
     * TreeResolver constructor.
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryHelper $categoryHelper
     * @param StoreManagerInterface $storeManager
     * @param DataObjectFactory $dataObjectFactory
     * @param PositionCollectionFactory $positionCollectionFactory
     * @param UrlInterface $urlBuilder
     * @param LayerResolver $layerResolver
     * @param FieldsByStore $fieldsByStore
     * @param GetItemsCollection $getItemsCollection
     * @param UrlKey $urlKey
     */
    public function __construct(
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategoryHelper $categoryHelper,
        StoreManagerInterface $storeManager,
        DataObjectFactory $dataObjectFactory,
        PositionCollectionFactory $positionCollectionFactory,
        UrlInterface $urlBuilder,
        LayerResolver $layerResolver,
        FieldsByStore $fieldsByStore,
        GetItemsCollection $getItemsCollection,
        UrlKey $urlKey
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->categoryHelper = $categoryHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->positionCollectionFactory = $positionCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->layerResolver = $layerResolver;
        $this->fieldsByStore = $fieldsByStore;
        $this->getItemsCollection = $getItemsCollection;
        $this->urlKey = $urlKey;
    }

    /**
     * @param int $storeId
     * @return Node
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function get($storeId): Node
    {
        if (!isset($this->menu[$storeId])) {
            $this->menu[$storeId] = $this->getMenu($storeId);
            $this->positionCounter = 1;
        }

        return $this->menu[$storeId];
    }

    /**
     * @param int $storeId
     * @return Node
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMenu($storeId): Node
    {
        $rootCategoryId = $this->getCategoryRootId($storeId);
        $parentCategoryNode = $this->getRootMenuNode();
        $mapping = [
            $rootCategoryId => $parentCategoryNode
        ];

        $this->addAdditionalLinks($mapping, $parentCategoryNode, $this->getBeforeAdditionalLinks());
        $this->addMainNodes($mapping, $parentCategoryNode, $storeId);
        $this->addChildNodes($mapping, $storeId);
        $this->overrideByCustomItem($mapping, $storeId, $rootCategoryId);
        $this->addAdditionalLinks($mapping, $parentCategoryNode, $this->getAdditionalLinks());

        return $mapping[$rootCategoryId];
    }

    /**
     * @param array $mapping
     * @param Node $parentCategoryNode
     * @param array $links
     */
    private function addAdditionalLinks(array &$mapping, Node $parentCategoryNode, array $links): void
    {
        /** @var Item $item */
        foreach ($links as $item) {
            $item = is_array($item) ? $this->dataObjectFactory->create(['data' => $item]) : $item;

            if ($item->getName() && $item->getId()) {
                $childNode = $this->nodeFactory->create(
                    [
                        'data' => [
                            ItemInterface::NAME => $item->getName(),
                            'id' => 'additional-node-' . $item->getId(),
                            'url' => $item->getUrl(),
                            'width' => (int)$item->getWidth(),
                            'content' => $item->getContent(),
                            'has_active' => false,
                            'is_active' => $this->isItemActive($item->getUrl()),
                            'is_category' => false,
                            'is_parent_active' => true
                        ],
                        'idField' => 'id',
                        'tree' => $parentCategoryNode->getTree(),
                        'parent' => $parentCategoryNode
                    ]
                );
                $parentCategoryNode->addChild($childNode);

                $mapping['additional' . $item->getId()] = $childNode;
            }
        }
    }

    /**
     * @param array $mapping
     * @param Node $parentCategoryNode
     * @param int $storeId
     * @throws LocalizedException
     */
    private function addMainNodes(array &$mapping, Node $parentCategoryNode, int $storeId)
    {
        $categoryCollection = $this->getCategoryCollection($storeId);
        $currentCategory = $this->getCurrentCategory();
        $items = $this->getItemsCollection->execute($storeId);
        $items->addFieldToFilter(LinkInterface::TYPE, $this->urlKey->getValues());

        foreach ($this->getSortedItems($storeId) as $sortedItem) {
            switch ($sortedItem->getType()) {
                case ItemInterface::CATEGORY_TYPE:
                    /** @var Category $category */
                    $category = $categoryCollection->getItemById($sortedItem->getEntityId());

                    if ($category === null
                        || $category->getLevel() != CategoryCollectionResource::MENU_LEVEL
                    ) {
                        continue 2;
                    }

                    $mapping[$category->getId()] = $this->createCategoryNode(
                        $parentCategoryNode,
                        $category,
                        $currentCategory,
                        true
                    )->setPositionClass($this->getPositionClass()); //add node in stack

                    break;
                case ItemInterface::CUSTOM_TYPE:
                    $mapKey = 'custom-' . $sortedItem->getEntityId();
                    $item = $items->getCustomItemByEntityId($sortedItem->getEntityId());

                    if (!isset($mapping[$mapKey]) && $item) {
                        $mapping[$mapKey] = $this->createNewNode(
                            $parentCategoryNode,
                            $item,
                            $storeId
                        )->setPositionClass($this->getPositionClass());
                    }

                    break;
            }
        }
    }

    /**
     * @param array $mapping
     * @param int $storeId
     * @throws LocalizedException
     */
    private function addChildNodes(array &$mapping, int $storeId)
    {
        $categoryCollection = $this->getCategoryCollection($storeId);
        $currentCategory = $this->getCurrentCategory();

        foreach ($categoryCollection as $category) {
            if (!$category->getParentCategory()->getIncludeInMenu()
                || !$category->getParentCategory()->getIsActive()
                || isset($this->noIncludeInMenu[$category->getParentId()])
            ) {
                $this->noIncludeInMenu[$category->getId()] = 0;
                continue;
            }

            $categoryParentId = $category->getParentId();

            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();

                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$categoryParentId];

            if (!isset($mapping[$category->getId()])) {
                $mapping[$category->getId()] = $this->createCategoryNode(
                    $parentCategoryNode,
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                )->setPositionClass($this->getPositionClass());
            }
        }
    }

    /**
     * Get current Category from catalog layer
     *
     * @return Category|null
     */
    public function getCurrentCategory()
    {
        $result = null;
        $catalogLayer = $this->layerResolver->get();

        if ($catalogLayer) {
            $result = $catalogLayer->getCurrentCategory();
        }

        return $result;
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getBeforeAdditionalLinks()
    {
        return [];
    }

    /**
     * public method for creating plugins
     * @return array
     */
    public function getAdditionalLinks()
    {
        return [];
    }

    /**
     * @param array $mapping
     * @param int $storeId
     * @param int $rootCategoryId
     * @throws NoSuchEntityException
     */
    private function overrideByCustomItem(&$mapping, $storeId, $rootCategoryId)
    {
        /** @var Node $parentNode */
        $parentNode = $mapping[$this->getCategoryRootId($storeId)];
        $disabled = [];
        $itemCollection = $this->getItemsCollection->execute($storeId);
        $itemCollection->addFieldToFilter(
            LinkInterface::TYPE,
            [
                ['in' => $this->urlKey->getValues()],
                ['null' => true]
            ]
        );

        /** @var ItemInterface $item */
        foreach ($itemCollection as $item) {
            switch ($item->getType()) {
                case 'category':
                    $mapKey = $item->getEntityId();
                    $dataToImport = $this->fieldsByStore->getCategoryFields();
                    $dataToImport['isobar_mega_menu_fieldset'][] = ItemInterface::STATUS;
                    if (!isset($mapping[$mapKey])) {
                        continue 2;
                    }
                    break;
                case 'custom':
                    $mapKey = 'custom-' . $item->getEntityId();
                    $dataToImport = $this->fieldsByStore->getCustomFields();
                    if (!isset($mapping[$mapKey])) {
                        $mapping[$mapKey] = $this->createNewNode($parentNode, $item, $storeId);
                    }
                    break;
                default:
                    continue 2;
            }

            /** @var Node $node */
            $node = $mapping[$mapKey];
            $status = $item->getStatus();
            if (($status === null && !$node->getData('status'))
                || $status === Status::DISABLED
                && $item->getStoreId() != Store::DEFAULT_STORE_ID
            ) {
                unset($mapping[$mapKey]);
                $parentNode->removeChild($node);
            } else {
                foreach ($dataToImport as $fieldSet) {
                    foreach ($fieldSet as $field) {
                        $data = $item->getData($field);
                        if ($data !== null) {
                            $node->setData($field, $data);
                        }
                    }
                }
            }
            if ((int)$item->getStatus() === Status::DISABLED
                && (int)$item->getStoreId() === Store::DEFAULT_STORE_ID
                && $rootCategoryId != $mapKey
            ) {
                $disabled[] = $mapKey;
            }
        }

        foreach ($disabled as $mapKey) {
            if (isset($mapping[$mapKey])) {
                $node = $mapping[$mapKey];
                if ($node->getStatus() == Status::DISABLED) {
                    unset($mapping[$mapKey]);
                    $parentNode->removeChild($node);
                }
            }
        }
    }

    /**
     * @param Node $parentNode
     * @param ItemInterface $item
     * @param int|null $storeId
     *
     * @return Node
     * @throws NoSuchEntityException
     */
    private function createNewNode($parentNode, ItemInterface $item, $storeId)
    {
        $itemNode = $this->nodeFactory->create(
            [
                'data' => $this->getItemAsArray(
                    $storeId,
                    $item
                ),
                'idField' => 'id',
                'tree' => $parentNode->getTree(),
                'parent' => $parentNode
            ]
        );
        $parentNode->addChild($itemNode);

        return $itemNode;
    }

    /**
     * @param Node $parentNode
     * @param Category $category
     * @param Category $currentCategory
     * @param bool $isParentActive
     * @return Node
     */
    private function createCategoryNode($parentNode, $category, $currentCategory, $isParentActive)
    {
        $categoryNode = $this->nodeFactory->create(
            [
                'data' => $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $isParentActive
                ),
                'idField' => 'id',
                'tree' => $parentNode->getTree(),
                'parent' => $parentNode
            ]
        );
        $parentNode->addChild($categoryNode);

        return $categoryNode;
    }

    /**
     * @return Node
     */
    private function getRootMenuNode(): Node
    {
        return $this->nodeFactory->create(
            [
                'data' => [],
                'idField' => 'root',
                'tree' => $this->treeFactory->create()
            ]
        );
    }

    /**
     * @param Category $category
     * @param Category $currentCategory
     * @param bool $isParentActive
     * @return array
     */
    private function getCategoryAsArray(Category $category, Category $currentCategory, bool $isParentActive): array
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->categoryHelper->getCategoryUrl($category),
            'has_active' => in_array(
                (string)$category->getId(),
                explode('/', $currentCategory->getPath()),
                true
            ),
            'is_active' => $category->getId() == $currentCategory->getId(),
            'is_category' => true,
            'is_parent_active' => $isParentActive,
            'level' => $category->getLevel(),
            'mm_turn_on' => $category->getMmTurnOn(),
            'mm_menu_type' => $category->getMmMenuType(),
            'mm_display_on' => $category->getMmDisplayOn(),
            'mm_image' => $category->getMmImage(),
            'mm_label' => $category->getMmLabel(),
            'mm_label_background' => $category->getMmLabelBackground(),
            'mm_color' => $category->getMmColor(),
            'mm_configurator' => $category->getMmConfigurator(),
            'mm_background_image' => $category->getMmBackgroundImage(),
            'mm_icon_class' => $category->getMmIconClass(),
            'mm_width' => $category->getMmWidth()
        ];
    }

    /**
     * @param int $storeId
     * @param ItemInterface $item
     * @return array
     * @throws NoSuchEntityException
     */
    private function getItemAsArray($storeId, ItemInterface $item): array
    {
        $linkType = $item->getLinkType();
        $url = $item->getUrl() ?? '';
        $url = $linkType == UrlKey::EXTERNAL_URL || $linkType == UrlKey::NO
            ? $url
            : $this->getAbsoluteUrl($storeId, $url);

        return [
            ItemInterface::NAME => $item->getName(),
            'id' => 'custom-node-' . $item->getEntityId(),
            'url' => $url,
            LinkInterface::TYPE => $linkType,
            'content' => $item->getContent(),
            'has_active' => false,
            'is_active' => $this->isItemActive($url),
            'is_category' => false,
            'is_parent_active' => true,
            ItemInterface::STATUS => $item->getStatus(),
            'mm_turn_on' => !empty($item->getStatus()),
            'mm_label' => $item->getLabel(),
            'mm_label_background' => $item->getLabelBackgroundColor(),
            'mm_color' => $item->getLabelTextColor(),
            'mm_width' => $item->getWidth(),
            'mm_menu_type' => 'horizontal'
        ];
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function isItemActive(string $url): bool
    {
        if ($url) {
            $result = strpos($this->urlBuilder->getCurrentUrl(), $url) !== false;
        }

        return $result ?? false;
    }

    /**
     * @param int $storeId
     * @return CategoryCollection
     * @throws LocalizedException
     */
    public function getCategoryCollection($storeId): CategoryCollection
    {
        /** @var CategoryCollection $collection */
        $collection = $this->categoryCollectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect(
            [
                'name',
                'mm_turn_on',
                'mm_display_on',
                'mm_menu_type',
                'mm_image',
                'mm_label',
                'mm_label_background',
                'mm_color',
                'mm_configurator',
                'mm_background_image',
                'mm_icon_class',
                'mm_width'
            ]
        );
        $collection->addFieldToFilter('path', ['like' => '1/' . $this->getCategoryRootId($storeId) . '/%']);
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addNavigationMaxDepthFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * @param int $storeId
     *
     * @return int
     *
     * @throws NoSuchEntityException
     */
    public function getCategoryRootId($storeId)
    {
        return $this->storeManager->getStore($storeId)->getRootCategoryId();
    }

    /**
     * Get store base url
     *
     * @param int $storeId
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getStoreBaseUrl($storeId, $type = UrlInterface::URL_TYPE_LINK)
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();

        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     * Get url
     *
     * @param int $storeId
     * @param string $url
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getAbsoluteUrl($storeId, $url, $type = UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
    }

    /**
     * @param int $storeId
     * @return PositionCollection
     */
    public function getSortedItems($storeId)
    {
        return $this->positionCollectionFactory->create()->getSortedCollection($storeId);
    }

    /**
     * @return string
     */
    private function getPositionClass(): string
    {
        return self::ITEM_POSITION_CLASS_PREFIX . $this->positionCounter++;
    }
}
