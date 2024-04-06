<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api;

/**
 * Interface LinkRepositoryInterface
 *
 * @api
 * @package Isobar\Megamenu\Api
 */
interface LinkRepositoryInterface
{
    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\LinkInterface $link
     * @return \Isobar\Megamenu\Api\Data\Menu\LinkInterface
     */
    public function save(\Isobar\Megamenu\Api\Data\Menu\LinkInterface $link);

    /**
     * @return \Isobar\Megamenu\Api\Data\Menu\LinkInterface
     */
    public function getNew();

    /**
     * @param int $entityId
     * @return \Isobar\Megamenu\Api\Data\Menu\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * @param \Isobar\Megamenu\Api\Data\Menu\LinkInterface $link
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Isobar\Megamenu\Api\Data\Menu\LinkInterface $link);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($entityId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Isobar\Megamenu\Api\Data\Menu\LinkSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
