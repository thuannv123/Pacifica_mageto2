<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Extended Order Grid for Magento 2
 */

namespace Amasty\Ogrid\Model\Column\Product;

class Percent extends \Amasty\Ogrid\Model\Column\Product
{
    public function modifyItem(&$item, $config = [])
    {
        parent::modifyItem($item, $config);

        $item[$this->_alias_prefix . $this->_fieldKey] *= 1;

        $item[$this->_alias_prefix . $this->_fieldKey] .= '%';
    }
}
