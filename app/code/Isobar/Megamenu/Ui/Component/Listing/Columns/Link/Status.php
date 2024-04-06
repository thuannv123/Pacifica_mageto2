<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Ui\Component\Listing\Columns\Link;

use Isobar\Megamenu\Api\Data\Menu\ItemInterface;
use Isobar\Megamenu\Api\Data\Menu\LinkInterface;
use Isobar\Megamenu\Model\Config\Source\Status as StatusOptions;
use Isobar\Megamenu\Model\Config\Source\UrlKey;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Status
 * @package Isobar\Megamenu\Ui\Component\Listing\Columns\Link
 */
class Status extends Column
{
    /**
     * @var UrlKey
     */
    private $urlKey;

    /**
     * Status constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlKey $urlKey
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlKey $urlKey,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlKey = $urlKey;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!in_array($item[LinkInterface::TYPE], $this->urlKey->getValues())) {
                    $item[ItemInterface::STATUS] = StatusOptions::DISABLED;
                }
            }
        }

        return $dataSource;
    }
}
