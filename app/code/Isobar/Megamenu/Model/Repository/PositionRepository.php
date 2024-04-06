<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Repository;

use Isobar\Megamenu\Api\PositionRepositoryInterface;
use Isobar\Megamenu\Model\Menu\Item\Position;
use Isobar\Megamenu\Model\Menu\Item\PositionFactory;
use Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position as PositionResource;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LinkRepository
 * @package Isobar\Megamenu\Model\Repository
 */
class PositionRepository implements PositionRepositoryInterface
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var PositionFactory
     */
    protected $positionFactory;

    /**
     * @var PositionResource
     */
    protected $positionResource;

    /**
     * PositionRepository constructor.
     * @param PositionFactory $positionFactory
     * @param PositionResource $positionResource
     */
    public function __construct(
        PositionFactory $positionFactory,
        PositionResource $positionResource
    ) {
        $this->positionFactory = $positionFactory;
        $this->positionResource = $positionResource;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        if (!isset($this->registry[$id])) {
            /** @var Position $position */
            $position = $this->positionFactory->create();
            $this->positionResource->load($position, $id);
            if (!$position->getId()) {
                throw new NoSuchEntityException(__('Position with specified ID "%1" not found.', $id));
            }
            $this->registry[$id] = $position;
        }

        return $this->registry[$id];
    }
}
