<?php
namespace Isobar\Megamenu\Model\Category\Attribute\Source;

class DisplayOn extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const MM_DISPLAY_TOP = 'top';
    const MM_DISPLAY_SIDEBAR = 'sidebar';
    const MM_DISPLAY_BOTH = 'both';
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => self::MM_DISPLAY_BOTH, 'label' => __('Both')],
                ['value' => self::MM_DISPLAY_TOP, 'label' => __('Top Menu ')],
                ['value' => self::MM_DISPLAY_SIDEBAR, 'label' => __('Sidebar')],
            ];
        }
        return $this->_options;
    }
}
