<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Isobar\AmastyXnotif\Plugins\ConfigurableProduct;

use Amasty\Xnotif\Helper\Data as Helper;
use Amasty\Xnotif\Helper\Config;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class Data extends \Magento\ConfigurableProduct\Helper\Data
{
    private const CATEGORY_PAGE_HANDLE = 'catalog_category_view';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var GetSalableQuantityDataBySku
     */
    private $stockState;

    public function __construct(
        Image $imageHelper,
        Helper $helper,
        Registry $registry,
        Manager $moduleManager,
        Layout $layout,
        Config $config,
        GetSalableQuantityDataBySku $stockState
    ) {
        $this->imageHelper = $imageHelper;
        $this->moduleManager = $moduleManager;
        parent::__construct($imageHelper);
        $this->helper = $helper;
        $this->registry = $registry;
        $this->layout = $layout;
        $this->config = $config;
        $this->stockState = $stockState;
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        $aStockStatus = [];
        $allowAttributes = $this->getAllowAttributes($currentProduct);
        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $key = [];
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                if ($this->config->isCategorySubscribeEnabled()
                    || !$this->isCategoryPage()
                    || $product->isSalable()
                ) {
                    $options[$productAttributeId][$attributeValue][] = $productId;
                }

                $options['index'][$productId][$productAttributeId] = $attributeValue;

                /*Amasty code start - code here for improving performance*/
                $key[] = $attributeValue;
            }

            if ($key && !$this->moduleManager->isEnabled('Amasty_Stockstatus')) {
                $saleable = true;
                $qty = $this->stockState->execute($product->getSku());
                if (isset($qty) && isset($qty[0]['qty']) && isset($qty[1]['qty'])){
                    if(count($qty) < 2){
                        $saleable = ($qty[0]['qty'] > 0) ? true : false ;
                        $aStockStatus[implode(',', $key)] = [
                            'is_in_stock'   => $saleable,
                            'custom_status' => (!$saleable) ? __('Out of Stock') : '',
                            'product_id'    => $product->getId(),
                            'qty'           => $qty[0]['qty'],  
                        ];
                    }else{
                        $saleable = ($qty[1]['qty'] > 0) ? true : false ;
                        $aStockStatus[implode(',', $key)] = [
                            'is_in_stock'   => $saleable,
                            'custom_status' => (!$saleable) ? __('Out of Stock') : '',
                            'product_id'    => $product->getId(),
                            'qty'           => $qty[1]['qty'],
                        ];
                    }
                }
                if (!$saleable) {
                    $aStockStatus[implode(',', $key)]['stockalert'] =
                        $this->helper->getStockAlert($product);
                }

                $aStockStatus[implode(',', $key)]['pricealert'] =
                    $this->helper->getPriceAlert($product);
            }
            /*Amasty code end*/
        }
        $aStockStatus['is_in_stock'] = $currentProduct->isSalable();

        $this->registry->unregister('amasty_xnotif_data');
        $this->registry->register('amasty_xnotif_data', $aStockStatus);

        return $options;
    }

    private function isCategoryPage(): bool
    {
        return in_array(self::CATEGORY_PAGE_HANDLE, $this->layout->getUpdate()->getHandles());
    }
}
