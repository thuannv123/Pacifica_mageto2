<?php

namespace Marvelic\Catalog\Controller\Product;

use Magento\Catalog\Pricing\Price\SpecialPrice;

class SimpleProduct extends \Magento\Framework\App\Action\Action
{
    protected $jsonResultFactory;

    protected $requestInterface;

    protected $productRepositoryInterface;

    protected $priceHelper;

    protected $configurableProductType;

    protected $productCollectionFactory;

    protected $imageBuilder;

    protected $timezoneInterface;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->requestInterface = $requestInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->priceHelper = $priceHelper;
        $this->configurableProductType = $configurableProductType;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        $this->timezoneInterface = $timezoneInterface;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $productId = $this->requestInterface->getParam('id');
        $colorId = $this->requestInterface->getParam('color');

        $data = $this->getAssociatedProductsWithColorId($productId, $colorId);

        return $result->setData(
            $data
        );
    }

    public function getAssociatedProductsWithColorId($configurableProductId, $colorId)
    {
        if (is_numeric($configurableProductId)) {
            $configurableProduct = $this->productRepositoryInterface->getById($configurableProductId);
        }

        if (!isset($configurableProduct)) {
            return;
        }

        if ($configurableProduct->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        $associatedProductIds = $this->configurableProductType->getChildrenIds($configurableProductId);

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('entity_id', ['in' => $associatedProductIds]);
        $productCollection->addAttributeToFilter('color', $colorId);

        $lowestPrice = null;
        $baseImage = null;
        $productChild = null;
        $thumbnailImage = null;
        $childProductPrices = [];
        $specialPrices = [];

        foreach ($productCollection->getItems() as $associatedProduct) {
            $associatedColorId = $associatedProduct->getColor();
            if ($associatedColorId == $colorId && $associatedProduct->isSalable()) {
                $a = [];
                if ($associatedProduct->hasSpecialPrice()) {
                    $a[] = $associatedProduct->getSpecialPrice();
                }
                if ($this->isSpecialPriceActive($associatedProduct)) {
                    $specialPrice = $this->getPrice($associatedProduct, SpecialPrice::PRICE_CODE);
                    if ($specialPrice) {
                        $specialPrices[] = $specialPrice;
                        $productChild = $associatedProduct->getId();
                    }
                }

                $price = $associatedProduct->getPrice();

                if ($lowestPrice === null || $price < $lowestPrice) {
                    $lowestPrice = $price;
                    $baseImage = $this->imageBuilder->create($associatedProduct, 'product_base_image')->getImageUrl();
                    $thumbnailImage = $this->imageBuilder->create($associatedProduct, 'product_thumbnail_image')->getImageUrl();
                }

                $childProductPrices[] = $price;
                $productChild = ($productChild)?: $associatedProduct->getId();
            }
        }

        $minPrice = min($childProductPrices);
        $lowestPrice = $this->priceHelper->currency($minPrice, true, false);
        $result = [
            'productIdChild' => $productChild,
            'price' => $lowestPrice,
            'base_image' => $baseImage,
            'thumbnail_image' => $thumbnailImage,
        ];

        if (!empty($specialPrice)) {
            $lowestSpecialPrice = min($specialPrices);
            $result['special_price'] = $this->priceHelper->currency($lowestSpecialPrice, true, false);
        }

        return $result;
    }

    public function getPrice($product, string $code)
    {
        return $product->getPriceInfo()->getPrice($code)->getAmount()->getValue();
    }

    public function isSpecialPriceActive($product): bool
    {
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();

        return ($specialFromDate || $specialToDate)
            && $this->timezoneInterface->isScopeDateInInterval(
                $product->getStoreId(),
                $specialFromDate,
                $specialToDate
            );
    }
}
