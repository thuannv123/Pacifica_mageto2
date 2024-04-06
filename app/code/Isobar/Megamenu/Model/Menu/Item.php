<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Menu;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item as MenuItemResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Item
 * @package Isobar\Megamenu\Model\Menu
 */
class Item extends AbstractModel implements ItemInterface, IdentityInterface
{
    /**
     * @var string
     */
    const CACHE_TAG = 'isobar_mega_menu';

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(MenuItemResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (int)$this->_getData(ItemInterface::ID) ?: null;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(ItemInterface::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(ItemInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(ItemInterface::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_getData(ItemInterface::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(ItemInterface::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(ItemInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(ItemInterface::STORE_ID, $storeId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(ItemInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(ItemInterface::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_getData(ItemInterface::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        return $this->setData(ItemInterface::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getLabelTextColor()
    {
        return $this->_getData(ItemInterface::LABEL_TEXT_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelTextColor($labelColor)
    {
        return $this->setData(ItemInterface::LABEL_TEXT_COLOR, $labelColor);
    }

    /**
     * @inheritdoc
     */
    public function getLabelBackgroundColor()
    {
        return $this->_getData(ItemInterface::LABEL_BACKGROUND_COLOR);
    }

    /**
     * @inheritdoc
     */
    public function setLabelBackgroundColor($labelColor)
    {
        return $this->setData(ItemInterface::LABEL_BACKGROUND_COLOR, $labelColor);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        $status = $this->_getData(ItemInterface::STATUS);

        return $status !== null ? (int)$status : null;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(ItemInterface::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->_getData(ItemInterface::WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function setWidth($width)
    {
        return $this->setData(ItemInterface::WIDTH, $width);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->_getData(ItemInterface::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(ItemInterface::CONTENT, $content);
    }
}
