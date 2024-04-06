<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api\Data\Menu;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ItemSearchResultsInterface
 * @package Isobar\Megamenu\Api\Data\Menu
 */
interface ItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Isobar\Megamenu\Api\Data\Menu\ItemInterface[]
     */
    public function getItems();

    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\ItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
