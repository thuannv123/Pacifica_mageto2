<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\DataProvider\DataCollector;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\ItemRepositoryInterface;
use Isobar\Megamenu\Model\Backend\DataProvider\DataCollectorInterface;
use Isobar\Megamenu\Model\Provider\FieldsByStore;

/**
 * Class General
 * @package Isobar\Megamenu\Model\Backend\DataProvider\DataCollector
 */
class General implements DataCollectorInterface
{
    /**
     * @var ItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var FieldsByStore
     */
    protected $fieldsByStore;

    /**
     * General constructor.
     * @param ItemRepositoryInterface $itemRepository
     * @param FieldsByStore $fieldsByStore
     */
    public function __construct(
        ItemRepositoryInterface $itemRepository,
        FieldsByStore $fieldsByStore
    ) {
        $this->itemRepository = $itemRepository;
        $this->fieldsByStore = $fieldsByStore;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $data, int $storeId, int $entityId): array
    {
        if ($storeId) {
            $data['store_id'] = $storeId;
            /** @var ItemInterface $item */
            $item = $this->itemRepository->getByEntityId($entityId, $storeId, 'custom');
            if ($item) {
                $data = $this->collectItemData($item, $data);
            }
        }

        return $data;
    }

    /**
     * @param ItemInterface $item
     * @param array $data
     * @return array
     */
    private function collectItemData(ItemInterface $item, array $data): array
    {
        foreach ($this->fieldsByStore->getCustomFields() as $fieldSet) {
            foreach ($fieldSet as $field) {
                if ($item->getData($field) !== null) {
                    $data[$field] = $item->getData($field);
                }
            }
        }

        return $data;
    }
}
