<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\SaveLink;

/**
 * Interface DataCollectorInterface
 * @package Isobar\Megamenu\Model\Backend\SaveLink
 */
interface DataCollectorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function execute(array $data): array;
}
