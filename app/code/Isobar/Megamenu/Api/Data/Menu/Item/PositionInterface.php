<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api\Data\Menu\Item;

/**
 * Interface PositionInterface
 * @package Isobar\Megamenu\Api\Data\Menu\Item
 */
interface PositionInterface
{
    /**
     * @var string
     */
    const TABLE = 'isobar_menu_item_order';

    /**#@+
     * Constants defined for keys of  data array
     */
    const ID = 'id';
    const TYPE = 'type';
    const POSITION = 'sort_order';
    const ENTITY_ID = 'entity_id';
    const STORE_VIEW = 'store_view';
    /**#@-*/


    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return int
     */
    public function getStoreView();

    /**
     * @param int $storeView
     * @return $this
     */
    public function setStoreView($storeView);
}
