<?php
declare(strict_types = 1);

namespace Isobar\Megamenu\Block\Adminhtml\Builder\Store;

/**
 * Class Switcher
 * @package Isobar\Megamenu\Block\Adminhtml\Builder\Store
 */
class Switcher extends \Magento\Backend\Block\Store\Switcher
{
    /**
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        $storeId = parent::getStoreId();
        if (!$storeId) {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }
}
