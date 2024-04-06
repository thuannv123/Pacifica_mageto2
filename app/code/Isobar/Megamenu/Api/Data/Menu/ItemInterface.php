<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api\Data\Menu;

/**
 * Interface ItemInterface
 * @package Isobar\Megamenu\Api\Data\Menu
 */
interface ItemInterface
{
    /**
     * @var string
     */
    const TABLE_NAME = 'isobar_menu_item_content';

    /**#@+
     * Constants defined for keys of  data array
     */
    const ID = 'id';
    const ENTITY_ID = 'entity_id';
    const TYPE = 'type';
    const STORE_ID = 'store_id';
    const NAME = 'name';
    const LABEL = 'label';
    const LABEL_TEXT_COLOR = 'label_text_color';
    const LABEL_BACKGROUND_COLOR = 'label_background_color';
    const SORT_ORDER = 'sort_order';
    const CATEGORY_TYPE = 'category';
    const CUSTOM_TYPE = 'custom';
    const STATUS = 'status';
    const WIDTH = 'width';
    const CONTENT = 'content';
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
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

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
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabelTextColor();

    /**
     * @param string $labelColor
     * @return $this
     */
    public function setLabelTextColor($labelColor);

    /**
     * @return string
     */
    public function getLabelBackgroundColor();

    /**
     * @param string $labelColor
     * @return $this
     */
    public function setLabelBackgroundColor($labelColor);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int|null $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getWidth();

    /**
     * @param string $width
     * @return $this
     */
    public function setWidth($width);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);
}
