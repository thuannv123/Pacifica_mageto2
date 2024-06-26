<?php
namespace Isobar\Megamenu\Model\Configurator\Row;

use Magento\Framework\DataObject;

class Column extends DataObject
{
    private $_entities = array();

    private $_entityFactory;

    private $_columnData;

    public function __construct(
        \Isobar\Megamenu\Model\Configurator\Row\Column\EntityFactory $entityFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_entityFactory = $entityFactory;
    }

    public function init($columnData, $node)
    {
        $columnData = get_object_vars($columnData);
        $this->_columnData = $columnData;
        $this->setNode($node);
        $this->setData('width', $columnData['width']);
        foreach ($columnData['entities'] as &$entitiesData) {
            $entitiesData = get_object_vars($entitiesData);
            $entity = $this->_entityFactory->create($entitiesData);
            $entity->init($entitiesData, $node);
            $this->_entities []= $entity;
        }
    }

    public function getEntities()
    {
        return $this->_entities;
    }
}
