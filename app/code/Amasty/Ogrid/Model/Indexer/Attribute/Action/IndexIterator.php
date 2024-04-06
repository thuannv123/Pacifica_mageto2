<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */
namespace Amasty\Ogrid\Model\Indexer\Attribute\Action;

class IndexIterator implements \Iterator
{
    /**
     * @var array
     */
    private $current;

    /**
     * @var int
     */
    private $key;

    /**
     * @var bool
     */
    private $valid = true;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $itemsIds;

    /**
     * @var array
     */
    private $staticFields;

    /**
     * @var array
     */
    private $specialFields;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var int
     */
    private $lastItemId = 0;

    /**
     * @var array
     */
    private $itemAttributes = [];

    public function __construct(
        DataProvider $dataProvider,
        $itemsIds,
        $staticFields,
        array $fields,
        array $specialFields,
        Full $actionFull
    ) {
        $this->dataProvider = $dataProvider;
        $this->dataProvider->setActionFull($actionFull);
        $this->itemsIds = $itemsIds;
        $this->fields = $fields;
        $this->staticFields = $staticFields;
        $this->specialFields = $specialFields;
    }

    public function current(): array
    {
        return $this->current;
    }

    public function next(): void
    {
        \next($this->items);
        if (\key($this->items) === null) {

            $this->items = $this->dataProvider->getSearchableItems(
                $this->staticFields,
                $this->itemsIds,
                $this->lastItemId
            );

            if (!count($this->items)) {
                $this->valid = false;
                return;
            }

            $productsItems = [];

            foreach ($this->items as $itemData) {
                $this->lastItemId = $itemData['item_id'];

                if (!array_key_exists($itemData['store_id'], $productsItems)) {
                    $productsItems[$itemData['store_id']] = [];
                }

                if (!array_key_exists($itemData['product_id'], $productsItems[$itemData['store_id']])) {
                    $productsItems[$itemData['store_id']][$itemData['product_id']] = [];
                }

                $productsItems[$itemData['store_id']][$itemData['product_id']][] = $itemData['item_id'];
            }

            \reset($this->items);

            $this->itemAttributes = $this->dataProvider->getItemAttributes(
                $productsItems,
                $this->fields
            );
            if (!empty($this->specialFields)) {
                $this->dataProvider->collectItemSpecialAttributes(
                    $productsItems,
                    $this->specialFields,
                    $this->itemAttributes
                );
            }
        }

        $itemData = \current($this->items);

        foreach ($this->staticFields as $attributeId => $attributeCode) {
            if (array_key_exists($attributeCode, $itemData)) {
                if (!array_key_exists($itemData['item_id'], $this->itemAttributes)) {
                    $this->itemAttributes[$itemData['item_id']] = [];
                }

                $this->itemAttributes[$itemData['item_id']][$attributeId] = $itemData[$attributeCode];
            }
        }

        if (!isset($this->itemAttributes[$itemData['item_id']])) {
            $this->next();
            return;
        }

        $itemAttr = $this->itemAttributes[$itemData['item_id']];

        $itemIndex = [$itemData['item_id'] => $itemAttr];

        $index = $this->dataProvider->prepareItemIndex(
            $itemIndex,
            $itemData
        );

        $this->current = $index;
        $this->key = $itemData['item_id'];
    }

    public function key(): int
    {
        return $this->key;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function rewind(): void
    {
        $this->lastItemId = 0;
        $this->key = null;
        $this->current = null;
        unset($this->items);
        $this->items = [];
        $this->next();
    }
}
