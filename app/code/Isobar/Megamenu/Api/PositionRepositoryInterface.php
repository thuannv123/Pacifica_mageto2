<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Api;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PositionRepositoryInterface
 *
 * @api
 * @package Isobar\Megamenu\Api
 */
interface PositionRepositoryInterface
{
    /**
     * @param int $id
     * @return ItemInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);
}
