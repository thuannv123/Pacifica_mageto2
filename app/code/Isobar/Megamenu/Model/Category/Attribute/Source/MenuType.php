<?php
namespace Isobar\Megamenu\Model\Category\Attribute\Source;

class MenuType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => 'horizontal', 'label' => __('Tab')],
                ['value' => 'vertical', 'label' => __('Grid')],
                ['value' => 'sidebar', 'label' => __('Sidebar')],
            ];
        }
        return $this->_options;
    }
}
