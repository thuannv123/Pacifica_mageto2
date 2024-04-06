<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position;

use Isobar\Megamenu\Api\Data\Menu\Item\PositionInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class GetMaxSortOrder
 * @package Isobar\Megamenu\Model\ResourceModel\Menu\Item\Position
 */
class GetMaxSortOrder
{
    /**
     * @var int
     */
    const DEFAULT_SORT_ORDER = 99999;

    /**
     * @var int|null
     */
    private $maxSortOrder;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * GetMaxSortOrder constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @return int
     */
    public function execute(): int
    {
        if ($this->maxSortOrder === null) {
            $order = sprintf('%s DESC', PositionInterface::POSITION);
            $select = $this->resource->getConnection()
                ->select()
                ->from(
                    [$this->resource->getTableName(PositionInterface::TABLE)],
                    [PositionInterface::POSITION]
                )
                ->order($order);

            $this->maxSortOrder = (int)$this->resource->getConnection()->fetchOne($select) ?: self::DEFAULT_SORT_ORDER;
        }

        return ++$this->maxSortOrder;
    }
}
