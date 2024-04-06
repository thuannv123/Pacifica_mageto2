<?php

namespace Isobar\Megamenu\Block\Html\Topmenu\Block;

use Magento\Framework\View\Element\Template;

class Row extends Template
{
    protected $_template = 'html/topmenu/block/row.phtml';

    public function renderRow($row)
    {
        $this->setRow($row);

        return $this->toHtml();
    }

    public function renderColumns()
    {
        $result = '';

        $columnRenderer = $this->_layout->createBlock(
            'Isobar\Megamenu\Block\Html\Topmenu\Block\Row\Column',
            '',
            ['data' => $this->getData()]
        );
        $counter = 1;
        foreach ($this->getRow()->getColumns() as $column) {
            $result .= $columnRenderer->renderColumn($column, $counter);
            $counter++;
        }
        return $result;
    }
}
