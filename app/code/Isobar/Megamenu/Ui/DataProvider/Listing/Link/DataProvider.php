<?php
declare(strict_types=1);

namespace Isobar\Megamenu\Ui\DataProvider\Listing\Link;

use Magento\Framework\Api\Filter;

/**
 * Class DataProvider
 * @package Isobar\Megamenu\Ui\DataProvider\Listing\Link
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var array
     */
    private $mappedFields = [
        'entity_id' => 'main_table.entity_id'
    ];

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter): void
    {
        if (array_key_exists($filter->getField(), $this->mappedFields)) {
            $mappedField = $this->mappedFields[$filter->getField()];
            $filter->setField($mappedField);
        }

        parent::addFilter($filter);
    }
}
