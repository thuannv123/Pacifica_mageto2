<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\DataProvider;

/**
 * Interface DataCollectorInterface
 * @package Isobar\Megamenu\Model\Backend\DataProvider
 */
interface DataCollectorInterface
{
    /**
     * @param array $data
     * @param int $storeId
     * @param int $entityId
     * @return array
     */
    public function execute(array $data, int $storeId, int $entityId): array;
}
