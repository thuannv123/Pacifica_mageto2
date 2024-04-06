<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\DataProvider\DataCollector;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Backend\DataProvider\DataCollectorInterface;
use Isobar\Megamenu\Model\Config\Source\Status;
use Isobar\Megamenu\Model\Config\Source\UrlKey;

/**
 * Class ModifyStatus
 * @package Isobar\Megamenu\Model\Backend\DataProvider\DataCollector
 */
class ModifyStatus implements DataCollectorInterface
{
    /**
     * @var UrlKey
     */
    private $urlKey;

    /**
     * ModifyStatus constructor.
     * @param UrlKey $urlKey
     */
    public function __construct(UrlKey $urlKey)
    {
        $this->urlKey = $urlKey;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $data, int $storeId, int $entityId): array
    {
        if (!in_array($data[LinkInterface::TYPE], $this->urlKey->getValues())) {
            $data[ItemInterface::STATUS] = Status::DISABLED;
        }

        return $data;
    }
}
