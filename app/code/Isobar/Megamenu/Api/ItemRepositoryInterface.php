<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api;

/**
 * Interface ItemRepositoryInterface
 *
 * @api
 * @package Isobar\Megamenu\Api
 */
interface ItemRepositoryInterface
{
    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\ItemInterface $item
     *
     * @return \Isobar\Megamenu\Api\Data\Menu\ItemInterface
     */
    public function save(\Isobar\Megamenu\Api\Data\Menu\ItemInterface $item);

    /**
     * @return \Isobar\Megamenu\Api\Data\Menu\ItemInterface
     */
    public function getNew(): \Isobar\Megamenu\Api\Data\Menu\ItemInterface;

    /**
     * @param int $id
     *
     * @return \Isobar\Megamenu\Api\Data\Menu\ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param int $entityId
     * @param int $storeId
     * @param string $type
     * @return Data\Menu\ItemInterface
     */
    public function getByEntityId($entityId, $storeId, $type);

    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\ItemInterface $item
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Isobar\Megamenu\Api\Data\Menu\ItemInterface $item);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);

    /***
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Isobar\Megamenu\Api\Data\Menu\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
