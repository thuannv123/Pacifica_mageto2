<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Model\Backend\SaveLink\DataCollector;

use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Backend\SaveLink\DataCollectorInterface;
use Isobar\Megamenu\Model\Config\Source\UrlKey;

/**
 * Class General
 * @package Isobar\Megamenu\Model\Backend\SaveLink\DataCollector
 */
class General implements DataCollectorInterface
{
    /**
     * @inheritdoc
     */
    public function execute(array $data): array
    {
        if (isset($data[LinkInterface::ENTITY_ID])) {
            $data[LinkInterface::ENTITY_ID] = $data[LinkInterface::ENTITY_ID] ?: null;
        }

        if ($this->isLinkValueNotSelect($data)) {
            $data[LinkInterface::TYPE] = UrlKey::NO;
        }

        if (!$this->isLinkSelected((int) $data[LinkInterface::TYPE])) {
            $data[LinkInterface::LINK] = '';
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isLinkValueNotSelect(array $data): bool
    {
        return $this->isLinkSelected((int) $data[LinkInterface::TYPE]) && !$data[LinkInterface::LINK];
    }

    /**
     * @param int $type
     * @return bool
     */
    private function isLinkSelected($type): bool
    {
        return in_array($type, [UrlKey::LINK, UrlKey::EXTERNAL_URL]);
    }
}
