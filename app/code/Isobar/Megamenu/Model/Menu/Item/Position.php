<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu\Item;

use Isobar\Megamenu\Api\Data\Menu\Item\PositionInterface;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Position
 * @package Isobar\Megamenu\Model\Menu\Item
 */
class Position extends AbstractModel implements PositionInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PositionResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (int)$this->_getData(PositionInterface::ID) ?: null;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(PositionInterface::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->_getData(PositionInterface::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(PositionInterface::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->_getData(PositionInterface::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(PositionInterface::POSITION, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(PositionInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(PositionInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getStoreView()
    {
        return $this->_getData(PositionInterface::STORE_VIEW);
    }

    /**
     * @inheritDoc
     */
    public function setStoreView($storeView)
    {
        return $this->setData(PositionInterface::STORE_VIEW, $storeView);
    }

    /**
     * @param int $afterItemId
     * @return $this
     * @throws \Exception
     */
    public function move($afterItemId)
    {
        $this->getResource()->beginTransaction();
        try {
            $this->getResource()->changePosition($this, $afterItemId);
            $this->getResource()->commit();
        } catch (\Exception $e) {
            $this->getResource()->rollBack();
            throw $e;
        }

        return $this;
    }
}
