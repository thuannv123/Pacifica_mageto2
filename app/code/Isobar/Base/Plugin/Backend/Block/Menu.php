<?php
/**
 * @author Isobar Team
 * @copyright Copyright (c) 2020 Amasty (https://www.isobar.com)
 * @package Isobar_Base
 */

namespace Isobar\Base\Plugin\Backend\Block;

use Magento\Backend\Block\Menu as NativeMenu;

class Menu
{
    const MAX_ITEMS = 300;

    /**
     * @param NativeMenu $subject
     * @param $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     *
     * @return array
     */
    public function beforeRenderNavigation(NativeMenu $subject, $menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        if ($level !== 0 && $menu->get('Isobar_Base::extensions')) {
            $level = 0;
            $limit = self::MAX_ITEMS;
            if (is_array($colBrakes)) {
                foreach ($colBrakes as $key => $colBrake) {
                    if (isset($colBrake['colbrake'])
                        && $colBrake['colbrake']
                    ) {
                        $colBrakes[$key]['colbrake'] = false;
                    }

                    if (isset($colBrake['colbrake']) && (($key - 1) % $limit) === 0) {
                        $colBrakes[$key]['colbrake'] = true;
                    }
                }
            }
        }

        return [$menu, $level, $limit, $colBrakes];
    }
}
