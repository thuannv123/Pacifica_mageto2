<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api\Data\Menu;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface LinkSearchResultsInterface
 * @package Isobar\Megamenu\Api\Data\Menu
 */
interface LinkSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Isobar\Megamenu\Api\Data\Menu\LinkInterface[]
     */
    public function getItems();

    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\LinkInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
