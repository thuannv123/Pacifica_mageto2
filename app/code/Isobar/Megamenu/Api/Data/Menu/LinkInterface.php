<?php

namespace Isobar\Megamenu\Api\Data\Menu;

/**
 * Interface LinkInterface
 * @package Isobar\Megamenu\Api\Data\Menu
 */
interface LinkInterface
{
    /**
     * @var string
     */
    const TABLE_NAME = 'isobar_menu_link';

    /**
     * @var string
     */
    const PERSIST_NAME = 'isobar_megamenu_link';

    /**#@+
     * Constants defined for keys of  data array
     */
    const ENTITY_ID = 'entity_id';
    const LINK = 'link';
    const TYPE = 'link_type';
    /**#@-*/

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
    public function getLink();

    /**
     * @param string $link
     * @return $this
     */
    public function setLink($link);

    /**
     * @return int
     */
    public function getLinkType();

    /**
     * @param int $linkType
     * @return $this
     */
    public function setLinkType($linkType);
}
