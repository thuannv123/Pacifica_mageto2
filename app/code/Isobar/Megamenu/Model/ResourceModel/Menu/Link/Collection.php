<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu\Link;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Menu\Link;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link as MenuLinkResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Isobar\Megamenu\Model\ResourceModel\Menu\Link
 */
class Collection extends AbstractCollection
{
    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(LinkInterface::ENTITY_ID);
        $this->_init(
            Link::class,
            MenuLinkResource::class
        );
    }
}
