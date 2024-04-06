<?php
/**
 * @author Isobar Team
 * @copyright Copyright (c) 2020 Isobar (https://www.isobar.com)
 * @package Isobar_Base
 */

namespace Isobar\Base\Plugin\Backend\Model\Menu;

use Isobar\Base\Model\ConfigProvider;
use Magento\Backend\Model\Menu\Item as NativeItem;

class Item
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param NativeItem $subject
     * @param $url
     *
     * @return string
     */
    public function afterGetUrl(NativeItem $subject, $url)
    {
        $id = $subject->getId();

        /* we can't add guide link into item object - find link again */
        if (strpos($id, '::menuguide') !== false
            && strpos($id, 'Isobar') !== false
        ) {
            $moduleCode = explode('::', $subject->getId());
            $moduleCode = $moduleCode[0];
            $moduleInfo = $this->configProvider->getModuleInfo($moduleCode);

            if (isset($moduleInfo['guide']) && $moduleInfo['guide']) {
                $url = $moduleInfo['guide'];
            } else {
                $url = '';
            }
        }

        return $url;
    }
}
