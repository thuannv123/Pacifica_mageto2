<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model;

use Amasty\Base\Model\Serializer;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Helper;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Column
{
    public const TABLE_PREFIX = 'amasty_ogrid_';

    /**
     * @var string
     */
    protected $_alias_prefix = 'amasty_ogrid_';

    /**
     * @var string
     */
    protected $_fieldKey;

    /**
     * @var string
     */
    protected $_resourceModelName;

    /**
     * @var AbstractDb
     */
    protected $_resource;

    /**
     * @var string
     */
    protected $_primaryKey;

    /**
     * @var string
     */
    protected $_foreignKey;

    /**
     * @var array
     */
    protected $_columns = [];

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var Helper
     */
    protected $dbHelper;

    public function __construct(
        $fieldKey,
        $resourceModel,
        Serializer $serializer,
        Helper $dbHelper,
        $columns = [],
        $primaryKey = 'entity_id',
        $foreignKey = 'entity_id'
    ) {
        $this->_fieldKey = $fieldKey;
        $this->_resourceModelName = $resourceModel;
        $this->_primaryKey = $primaryKey;
        $this->_foreignKey = $foreignKey;
        $this->_columns = $columns;
        $this->serializer = $serializer;
        $this->dbHelper = $dbHelper;
    }

    /**
     * Get resource instance
     *
     * @return AbstractDb
     */

    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = \Magento\Framework\App\ObjectManager::getInstance()->create($this->_resourceModelName);
        }
        return $this->_resource;
    }

    protected function _getMainTable()
    {
        return $this->getResource() instanceof \Magento\Eav\Model\Entity\VersionControl\AbstractEntity ?
            $this->getResource()->getEntityTable() : $this->getResource()->getMainTable();
    }

    public function getAlias()
    {
        return $this->_alias_prefix . $this->_getMainTable();
    }

    public function changeFilter(
        \Magento\Framework\Api\Filter $filter
    ) {
        $filter->setField($this->getAlias() . '.' . $this->_fieldKey);
    }

    public function addField(Collection $collection, $mainTableAlias = 'main_table')
    {
        $alias = $this->getAlias();
        $from = $collection->getSelect()->getPart(Select::FROM);

        if (!array_key_exists($alias, $from)) {
            $collection->getSelect()->joinLeft(
                [
                    $alias => $this->_getMainTable()
                ],
                $this->_getFieldCondition($mainTableAlias),
                []
            );
        }

        $collection->getSelect()->columns([
            $this->_alias_prefix . $this->_fieldKey => $alias . '.' . $this->_fieldKey
        ]);

        foreach ($this->_columns as $column) {
            $collection->getSelect()->columns([
                $this->_alias_prefix . $column => $alias . '.' . $column
            ]);
        }
    }

    protected function _getFieldCondition($mainTableAlias)
    {
        return $mainTableAlias . '.' . $this->_primaryKey . ' = ' . $this->getAlias() . '.' . $this->_foreignKey . '';
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function modifyItem(&$item, $config = [])
    {
    }
}
