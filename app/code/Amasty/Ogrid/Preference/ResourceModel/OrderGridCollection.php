<?php
declare(strict_types=1);

namespace Amasty\Ogrid\Preference\ResourceModel;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class OrderGridCollection extends Collection
{
    protected function _getMappedField($field)
    {
        if (is_object($field)) {
            return $field;
        }

        return parent::_getMappedField($field);
    }
}
