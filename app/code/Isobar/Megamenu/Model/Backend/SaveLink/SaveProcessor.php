<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\SaveLink;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Menu\Item;
use Isobar\Megamenu\Model\Provider\FieldsByStore;
use Isobar\Megamenu\Model\Repository\ItemRepository;
use Isobar\Megamenu\Model\Repository\LinkRepository;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position\GetMaxSortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SaveProcessor
 * @package Isobar\Megamenu\Model\Backend\SaveLink
 */
class SaveProcessor
{
    /**
     * @var GetMaxSortOrder
     */
    private $maxSortOrder;

    /**
     * @var LinkRepository
     */
    private $linkRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    /**
     * @var Pool
     */
    private $dataCollector;

    /**
     * SaveProcessor constructor.
     * @param GetMaxSortOrder $maxSortOrder
     * @param LinkRepository $linkRepository
     * @param ItemRepository $itemRepository
     * @param FieldsByStore $fieldsByStore
     * @param Pool $dataCollector
     */
    public function __construct(
        GetMaxSortOrder $maxSortOrder,
        LinkRepository $linkRepository,
        ItemRepository $itemRepository,
        FieldsByStore $fieldsByStore,
        Pool $dataCollector
    ) {
        $this->maxSortOrder = $maxSortOrder;
        $this->linkRepository = $linkRepository;
        $this->itemRepository = $itemRepository;
        $this->fieldsByStore = $fieldsByStore;
        $this->dataCollector = $dataCollector;
    }

    /**
     * @param array $inputData
     * @return int
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(array $inputData): int
    {
        /** @var Item $itemContent */
        $itemContent = $this->retrieveItemContent($inputData);
        $linkEntityId = $this->saveLink($inputData);

        $itemContent->setEntityId($linkEntityId);
        $itemContent->setSortOrder($this->maxSortOrder->execute());
        $this->itemRepository->save($itemContent);

        return $linkEntityId;
    }

    /**
     * @param array $inputData
     * @return int
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function saveLink(array $inputData): int
    {
        $entityId = (int)($inputData[LinkInterface::ENTITY_ID] ?? 0);
        /** @var LinkInterface $model */
        $link = $entityId ? $this->linkRepository->getById($entityId) : $this->linkRepository->getNew();
        $data = $this->dataCollector->execute($inputData);
        $link->setData($data);
        $link = $this->linkRepository->save($link);

        return (int)$link->getEntityId();
    }

    /**
     * @param array $data
     * @return Item
     * @throws LocalizedException
     */
    private function retrieveItemContent(array &$data): Item
    {
        $itemContent = $this->getItemContent($data);

        return $this->updateContentData($itemContent, $data);
    }

    /**
     * @param array $data
     * @return Item
     * @throws NoSuchEntityException
     */
    private function getItemContent(array $data): Item
    {
        $storeId = (int)$data[Item::STORE_ID] ?? 0;
        /** @var Item $itemContent */
        $itemContent = $this->itemRepository->getNew();
        if (isset($data[Item::ENTITY_ID]) && $data[Item::ENTITY_ID]) {
            $itemContentDefault = $this->itemRepository->getByEntityId(
                $data[Item::ENTITY_ID],
                0,
                Item::CUSTOM_TYPE
            );
            if ($storeId) {
                $itemContent->setStoreId($storeId);
                $itemContentTemp = $this->itemRepository->getByEntityId(
                    $data[Item::ENTITY_ID],
                    $storeId,
                    Item::CUSTOM_TYPE
                );
                if ($itemContentTemp) {
                    $itemContent = $itemContentTemp;
                }
            } else {
                $itemContent = $itemContentDefault;
            }
        }
        $itemContent->setType(Item::CUSTOM_TYPE)
            ->setWidth($data[Item::WIDTH])
            ->setContent($data[Item::CONTENT]);

        return $itemContent;
    }

    /**
     * @param Item $itemContent
     * @param array $data
     * @return Item
     * @throws LocalizedException
     */
    private function updateContentData(Item $itemContent, array $data): Item
    {
        $useDefaults = $data['use_default'] ?? [];
        foreach ($this->fieldsByStore->getCustomFields() as $fieldSet) {
            foreach ($fieldSet as $field) {
                if (isset($data[$field])) {
                    if (isset($useDefaults[$field]) && $useDefaults[$field]) {
                        $itemContent->setData($field, null);
                    } else {
                        $itemContent->setData($field, $data[$field]);
                    }
                    unset($data[$field]);
                } else {
                    throw new LocalizedException(__('Please enter valid %1', $field));
                }
            }
        }

        return $itemContent;
    }
}
