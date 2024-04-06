<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Column\Address;

use Magento\Framework\Data\Collection;

class Billing extends \Amasty\Ogrid\Model\Column
{
    /**
     * @var string
     */
    protected $_alias_prefix = 'amasty_ogrid_billing_';

    protected function _getFieldCondition($mainTableAlias)
    {
        return parent::_getFieldCondition($mainTableAlias) . ' and ' . $this->getAlias() . '.address_type="billing"';
    }
}
