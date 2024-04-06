<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Repository;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Api\ItemRepositoryInterface;
use Isobar\Megamenu\Api\LinkRepositoryInterface;
use Isobar\Megamenu\Model\Menu\Link;
use Isobar\Megamenu\Model\Menu\LinkFactory;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link as LinkResource;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link\Collection as LinkCollection;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link\CollectionFactory as LinkCollectionFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * Class LinkRepository
 * @package Isobar\Megamenu\Model\Repository
 */
class LinkRepository implements LinkRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var LinkResource
     */
    private $linkResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $links;

    /**
     * @var LinkCollectionFactory
     */
    private $linkCollectionFactory;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var PositionResource
     */
    private $positionResource;

    /**
     * LinkRepository constructor.
     * @param BookmarkSearchResultsInterfaceFactory $searchResultsFactory
     * @param LinkFactory $linkFactory
     * @param LinkResource $linkResource
     * @param PositionResource $positionResource
     * @param LinkCollectionFactory $linkCollectionFactory
     * @param ItemRepositoryInterface $itemRepository
     */
    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        LinkFactory $linkFactory,
        LinkResource $linkResource,
        PositionResource $positionResource,
        LinkCollectionFactory $linkCollectionFactory,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->linkFactory = $linkFactory;
        $this->linkResource = $linkResource;
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->itemRepository = $itemRepository;
        $this->positionResource = $positionResource;
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     */
    public function save(LinkInterface $link)
    {
        try {
            if ($link->getEntityId()) {
                $link = $this->getById($link->getEntityId())->addData($link->getData());
            }
            $this->linkResource->save($link);
            unset($this->links[$link->getEntityId()]);
        } catch (\Exception $e) {
            if ($link->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save link with ID %1. Error: %2',
                        [$link->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new link. Error: %1', $e->getMessage()));
        }

        return $link;
    }

    /**
     * @inheritdoc
     */
    public function getNew(): LinkInterface
    {
        return $this->linkFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getById($entityId)
    {
        if (!isset($this->links[$entityId])) {
            /** @var Link $link */
            $link = $this->getNew();
            $this->linkResource->load($link, $entityId);
            if (!$link->getEntityId()) {
                throw new NoSuchEntityException(__('Link with specified ID "%1" not found.', $entityId));
            }
            $this->links[$entityId] = $link;
        }

        return $this->links[$entityId];
    }

    /**
     * @inheritdoc
     */
    public function delete(LinkInterface $link)
    {
        try {
            $item = $this->itemRepository->getByEntityId($link->getEntityId(), $link->getStoreId(), 'custom');
            $this->linkResource->delete($link);
            $this->itemRepository->delete($item);
            $this->positionResource->deleteItem($item->getType(), $item->getEntityId());
            unset($this->links[$link->getEntityId()]);
        } catch (\Exception $e) {
            if ($link->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove link with ID %1. Error: %2',
                        [$link->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove link. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $linkModel = $this->getById($entityId);
        $this->delete($linkModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var LinkCollection $linkCollection */
        $linkCollection = $this->linkCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $linkCollection);
        }

        $searchResults->setTotalCount($linkCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $linkCollection);
        }

        $linkCollection->setCurPage($searchCriteria->getCurrentPage());
        $linkCollection->setPageSize($searchCriteria->getPageSize());

        $links = [];
        /** @var LinkInterface $link */
        foreach ($linkCollection->getItems() as $link) {
            $links[] = $this->getById($link->getEntityId());
        }

        $searchResults->setItems($links);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param LinkCollection $linkCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, LinkCollection $linkCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $linkCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param LinkCollection $linkCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, LinkCollection $linkCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $linkCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
