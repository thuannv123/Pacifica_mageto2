<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Marvelic\MagentoPricePermissions\Observer;

use \Magento\Framework\Event\Observer as EventObserver;
use \Magento\PricePermissions\Observer\ObserverData;
use \Magento\PricePermissions\Observer\AdminhtmlBlockHtmlBeforeObserver as MagentoAdminhtmlBlockHtmlBeforeObserver;

class AdminhtmlBlockHtmlBeforeObserver extends MagentoAdminhtmlBlockHtmlBeforeObserver
{
    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ObserverData $observerData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ObserverData $observerData,
        array $data = []
    ) {
        parent::__construct($coreRegistry, $request, $storeManager, $observerData);
        if (isset($data['can_edit_product_price']) && false === $data['can_edit_product_price']) {
            $this->observerData->setCanEditProductPrice(false);
        }
        if (isset($data['can_read_product_price']) && false === $data['can_read_product_price']) {
            $this->observerData->setCanReadProductPrice(false);
        }
        if (isset($data['can_edit_product_status']) && false === $data['can_edit_product_status']) {
            $this->observerData->setCanEditProductStatus(false);
        }
        if (isset($data['default_product_price_string'])) {
            $this->observerData->setDefaultProductPriceString($data['default_product_price_string']);
        }
    }

    /**
     * Handle adminhtml_block_html_before event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /** @var $block Template */
        $block = $observer->getBlock();

        $this->_filterByBlockName($block);

        // Handle prices that are shown when admin reviews customers shopping cart
        if($block->getNameInLayout() != NULL){
            if (stripos('logo', 'customer_cart_') === 0) {
                if (!$this->observerData->isCanReadProductPrice()) {
                    if ($block->getParentBlock()->getNameInLayout() == 'admin.customer.carts') {
                        $this->_removeColumnFromGrid($block, 'price');
                        $this->_removeColumnFromGrid($block, 'total');
                    }
                }
            }
        }
    }
}
