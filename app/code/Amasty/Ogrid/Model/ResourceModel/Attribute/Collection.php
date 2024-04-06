<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */
namespace Amasty\Ogrid\Model\ResourceModel\Attribute;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Amasty\Ogrid\Model\Attribute::class, \Amasty\Ogrid\Model\ResourceModel\Attribute::class);
    }

    public function joinProductAttributes()
    {
        $this->getSelect()->joinLeft(
            ['product_attributes' => $this->getTable('eav_attribute')],
            'main_table.attribute_id = product_attributes.attribute_id',
            ['frontend_input']
        );

        return $this;
    }
}
