<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Isobar\AmastyXnotif\Plugins\ConfigurableProduct\Block\Product\View\Type;

use Amasty\Base\Model\Serializer;
use Amasty\Xnotif\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableView;
use Magento\Framework\App\Cache\TypeListInterface;

class Configurable
{
    public const STOCK_STATUS = 'quantity_and_stock_status';
    public const IS_IN_STOCK = 'is_in_stock';
    public const MODULES_WITH_CHECKING_CHILD_STOCK_STATUS = [
        'amasty_promo',
        'checkout'
    ];

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $allProducts = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductResource
     */
    private $productResource;

     /**
     * @var TypeListInterface
     */
    private $typeList;

    public function __construct(
        Manager $moduleManager,
        Serializer $serializer,
        Registry $registry,
        Config $config,
        RequestInterface $request,
        ProductResource $productResource,
        TypeListInterface $typeList
    ) {
        $this->moduleManager = $moduleManager;
        $this->registry = $registry;
        $this->config = $config;
        $this->request = $request;
        $this->serializer = $serializer;
        $this->productResource = $productResource;
        $this->typeList = $typeList;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public function beforeGetAllowProducts($subject)
    {
        if (!$subject->hasAllowProducts()) {
            if (in_array($this->request->getModuleName(), self::MODULES_WITH_CHECKING_CHILD_STOCK_STATUS)) {
                $subject->setAllowProducts($this->getAllProducts($subject, true));
            } else {
                $subject->setAllowProducts($this->getAllProducts($subject));
            }
        }

        return $subject->getData('allow_products');
    }

    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterFetchView($subject, $html)
    {
        $configurableLayout = ['product.info.options.configurable', 'product.info.options.swatches'];
        $allTypes = array_keys($this->typeList->getTypes());
        $this->typeList->cleanType($allTypes[2]);

        if (in_array($subject->getNameInLayout(), $configurableLayout)
            && !$this->moduleManager->isEnabled('Amasty_Stockstatus')
            && !$this->registry->registry('amasty_xnotif_initialization')
            && !$this->request->getParam('is_amp')
        ) {
            if (!$subject->getAllowProducts()) {
                return '';
            }
            $this->registry->register('amasty_xnotif_initialization', 1);

            /* move creating code to Amasty\Xnotif\Plugins\ConfigurableProduct\Data */
            $aStockStatus = $this->registry->registry('amasty_xnotif_data');
            $aStockStatus['changeConfigurableStatus'] = true;
            $data = $this->serializer->serialize($aStockStatus);

            $html
                = '<script type="text/x-magento-init">
                    {
                        ".product-options-wrapper": {
                                    "amnotification": {
                                        "xnotif": ' . $data . '
                                    }
                         }
                    }
                   </script>' . $html;
        }


        return $html;
    }

    private function getAllProducts(
        ConfigurableView $subject,
        bool $checkChildStockStatus = false
    ): array {
        $mainProduct = $subject->getProduct();
        $productId = $mainProduct->getId();

        if (!isset($this->allProducts[$productId])) {
            $products = [];
            $allProducts = $mainProduct->getTypeInstance(true)
                ->getUsedProducts($mainProduct);
            if (isset($mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK])) {
                $mainProductStatus = (bool) $mainProduct->getData(self::STOCK_STATUS)[self::IS_IN_STOCK];
            } else {
                $mainProductStatus = true;
            }

            foreach ($allProducts as $product) {
                if ($this->isProductAllowed($product, $mainProductStatus, $checkChildStockStatus)) {
                    $products[] = $product;
                }
            }
            $this->allProducts[$productId] = $products;
        }

        return $this->allProducts[$productId];
    }

    private function isProductAllowed(
        Product $product,
        bool $mainProductStatus,
        bool $checkChildStockStatus
    ): bool {
        if ($product->getStatus() != Status::STATUS_ENABLED) {
            return false;
        }

        $result = $mainProductStatus || !$this->config->isShowOutOfStockOnly() || !$product->getIsSalable();

        if ($result && $checkChildStockStatus) {
            $result = $product->getIsSalable();
        }

        return $result;
    }
}
