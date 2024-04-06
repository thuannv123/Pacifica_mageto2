<?php
namespace Isobar\Megamenu\Model\Configurator\Row\Column;

class Category extends Entity
{
    public $rendererClass = 'Category';

    private $_subCategory;

    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

    public function getCategory()
    {
        if (!$this->_subCategory) {
            $parentNode =$this->getNode();
            $menuLevel = $parentNode->getMenuLevel();
            $nodes = $parentNode->getAllChildNodes();
            if (array_key_exists('category-node-' . $this->getValue(), $nodes)) {
                $nodes['category-node-' . $this->getValue()]['position_class'] = $this->getData('position_class');
                $this->_subCategory = $nodes['category-node-' . $this->getValue()];
                $this->_subCategory->setMenuLevel($menuLevel+1);
            }
        }
        return $this->_subCategory;
    }
}
