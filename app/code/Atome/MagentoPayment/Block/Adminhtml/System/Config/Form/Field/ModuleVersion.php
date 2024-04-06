<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Block\Adminhtml\System\Config\Form\Field;

use Atome\MagentoPayment\Services\Config\Atome;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleVersion extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        return Atome::version();
    }
}
