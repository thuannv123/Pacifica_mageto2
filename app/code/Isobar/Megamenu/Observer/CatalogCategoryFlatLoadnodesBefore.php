<?php

namespace Isobar\Megamenu\Observer;

use Magento\Framework\Event\ObserverInterface;

class CatalogCategoryFlatLoadnodesBefore implements ObserverInterface
{
    //TODO check if possible use catalog_attributes.xml
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $select = $observer->getSelect();
        $select->columns(
            [
                'mm_turn_on',
                'mm_menu_type',
                'mm_display_on',
                'mm_image',
                'mm_label',
                'mm_label_background',
                'mm_color',
                'mm_configurator',
                'mm_background_image',
                'mm_icon_class',
                'mm_width',
            ]
        );
    }
}
