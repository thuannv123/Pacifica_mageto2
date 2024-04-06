<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\ResourceModel\Menu\Link as MenuLinkResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Link
 * @package Isobar\Megamenu\Model\Menu
 */
class Link extends AbstractModel implements LinkInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(MenuLinkResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(LinkInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(LinkInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getLink()
    {
        return $this->_getData(LinkInterface::LINK);
    }

    /**
     * @inheritdoc
     */
    public function setLink($link)
    {
        return $this->setData(LinkInterface::LINK, $link);
    }

    /**
     * @inheritdoc
     */
    public function getLinkType(): int
    {
        return (int) $this->_getData(LinkInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setLinkType($linkType)
    {
        return $this->setData(LinkInterface::TYPE, $linkType);
    }
}
