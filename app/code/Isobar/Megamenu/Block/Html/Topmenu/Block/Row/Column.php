<?php

namespace Isobar\Megamenu\Block\Html\Topmenu\Block\Row;

use Magento\Framework\View\Element\Template;

class Column extends Template
{
    protected $_template = 'html/topmenu/block/row/column.phtml';
    protected $_position;
    public function renderColumn($column, $position)
    {
        $this->setColumn($column);
        $this->_position = $position;

        return $this->toHtml();
    }

    public function renderEntities()
    {
        $result = '';

        $entities = $this->getColumn()->getEntities();
        $counter = $this->_position != 1 ? $this->_position : 1;
        foreach ($entities as $entity) {
            $entityRenderer = $this->_layout->createBlock(
                'Isobar\Megamenu\Block\Html\Topmenu\Block\Row\Column\\' . $entity->getRendererClass(),
                '',
                ['data' => $this->getData()]
            );
            $entity->setData('position_class', $entity->getNode()->getPositionClass() . '-' . $counter);
            $result .= $entityRenderer->renderEntity($entity);
            $counter++;
        }
        return $result;
    }

    public function getModeRoot()
    {
        return $this->getData('mode');
    }

    public function getColumnWidth()
    {
        return $this->getColumn()->getWidth();
    }
}
