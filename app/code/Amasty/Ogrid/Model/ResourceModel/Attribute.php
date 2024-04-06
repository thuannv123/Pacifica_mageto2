<?php
namespace Amasty\Ogrid\Model\ResourceModel;

class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_ogrid_attribute', 'entity_id');
    }
}