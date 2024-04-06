<?php

namespace Marvelic\Catalog\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderExtensionFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Marvelic\Catalog\Api\Data\ProductRenderColorInterfaceFactory;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\Swatch;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Isobar\BirkenstockCustomize\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Color implements ProductRenderCollectorInterface
{
    const KEY = "color";
    const LIMIT_SWATCH = "catalog/frontend/swatches_per_product";

    /**
     * @var ProductRenderExtensionFactory
     */
    protected $productRenderExtensionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    protected $configurableProductType;

    /**
     * @var ProductRenderColorInterfaceFactory
     */
    protected $productRenderColorFactory;

    /**
     * @var Data
     */
    protected $swatchHelper;

    /**
     * @var Media
     */
    protected $mediaHelper;

    /**
     * @var UrlBuilder
     */
    protected $imageUrlBuilder;

    /**
     * @var Config
     */
    protected $disableSwatchConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $configSettings;

    /**
     * color constructor.
     * @param ProductRenderExtensionFactory $productRenderExtensionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Configurable $configurableProductType
     * @param ProductRenderColorInterfaceFactory $productRenderColorFactory
     * @param Data $swatchHelper
     * @param Media $mediaHelper
     * @param UrlBuilder $imageUrlBuilder
     * @param ConfigurableProductHelperData $configurableProductHelperData
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $configSettings
     */
    public function __construct(
        ProductRenderExtensionFactory $productRenderExtensionFactory,
        ProductRepositoryInterface $productRepository,
        Configurable $configurableProductType,
        ProductRenderColorInterfaceFactory $productRenderColorFactory,
        Data $swatchHelper,
        Media $mediaHelper,
        UrlBuilder $imageUrlBuilder,
        Config $disableSwatchConfig,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $configSettings
    ) {
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
        $this->productRepository = $productRepository;
        $this->configurableProductType = $configurableProductType;
        $this->productRenderColorFactory = $productRenderColorFactory;
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->imageUrlBuilder = $imageUrlBuilder;
        $this->disableSwatchConfig = $disableSwatchConfig;
        $this->storeManager = $storeManager;
        $this->configSettings = $configSettings;
    }

    /**
     * @param ProductInterface $product
     * @param ProductRenderInterface $productRender
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $extensionAttributes = $productRender->getExtensionAttributes() ?? $this->productRenderExtensionFactory->create();

        $colors = $this->getColorOptions($product->getId());
        if ($colors) {
            $existingColors = $extensionAttributes->getColor() ?? [];
            $existingOptionId = $extensionAttributes->getOptionId() ?? [];
            $existingOptionValue = $extensionAttributes->getOptionValue() ?? [];
            $existingOptionProductId = $extensionAttributes->getOptionProductId() ?? [];
            $existingOptionThumb = $extensionAttributes->getOptionThumb() ?? [];
            $existingOptionType = $extensionAttributes->getOptionType() ?? [];

            foreach ($colors as $color) {
                $productRenderColor = $this->productRenderColorFactory->create();
                $productRenderColor->setColor($color['label']);
                $productRenderColor->setOptionId($color['option-id']);
                $productRenderColor->setOptionValue($color['option-value']);
                $productRenderColor->setOptionProductId($product->getId());
                $productRenderColor->setOptionThumb($color['thumb']);
                $productRenderColor->setOptionType($color['type']);

                $existingColors[] = $productRenderColor->getColor();
                $existingOptionId[] = $productRenderColor->getOptionId();
                $existingOptionValue[] = $productRenderColor->getOptionValue();
                $existingOptionProductId[] = $productRenderColor->getOptionProductId();
                $existingOptionThumb[] = $productRenderColor->getOptionThumb();
                $existingOptionType[] = $productRenderColor->getOptionType();
            }

            $storeId = $this->storeManager->getStore()->getId();
            $swatchLimit = $this->configSettings->getValue(self::LIMIT_SWATCH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);

            $extensionAttributes->setColor($existingColors);
            $extensionAttributes->setOptionId($existingOptionId);
            $extensionAttributes->setOptionValue($existingOptionValue);
            $extensionAttributes->setOptionProductId($existingOptionProductId);
            $extensionAttributes->setOptionThumb($existingOptionThumb);
            $extensionAttributes->setOptionType($existingOptionType);
            $extensionAttributes->setOptionLimit($swatchLimit);
        }

        $productRender->setExtensionAttributes($extensionAttributes);
    }

    public function getColorOptions($configurableProductId)
    {
        try {
            $configurableProduct = $this->productRepository->getById($configurableProductId);
            $attributes = $this->configurableProductType->getConfigurableAttributes($configurableProduct);

            if ($configurableProduct->getTypeId() == Configurable::TYPE_CODE) {
                $colorOptions = [];
                $optionIds = [];
                $attributeDataArray = [];
                $optionLabels = [];

                foreach ($attributes as $attribute) {
                    if ($attribute->getProductAttribute()->getAttributeCode() === 'color') {
                        $attributeDataArray = $attribute->getProductAttribute()->getData();
                        foreach ($attribute->getOptions() as $option) {
                            $optionIds[] = $option['value_index'];
                            $optionLabels[$option['value_index']] = $option['label'];
                        }
                    }
                }

                $swatches = $this->swatchHelper->getSwatchesByOptionsId($optionIds);

                $optionValues = $this->addSwatchDataRecentlyViewedForAttribute(
                    $swatches,
                    $attributeDataArray,
                    $configurableProduct
                );

                foreach ($optionValues as $optionId => $optionValue) {
                    $colorOption = [];
                    if ($optionValue['type'] == 1 || $optionValue['type'] == 3) {
                        foreach ($swatches as $swatch) {
                            if ($swatch['option_id'] == $optionId) {
                                $colorOption['option-id'] = $swatch['option_id'];
                                $colorOption['option-value'] = $swatch['value'];
                                $colorOption['type'] = (int)$optionValue['type'];
                                $colorOption['thumb'] = $swatch['thumb'] ?? 'empty';
                            }
                        }
                        if (!empty($optionLabels)) {
                            foreach ($optionLabels as $optionLabelId => $optionLabel) {
                                if ($optionLabelId == $optionId) {
                                    $colorOption['label'] = $optionLabel;
                                }
                            }
                        }
                    } else if ($optionValue['type'] == 2) {
                        $colorOption['option-id'] = $optionId;
                        $colorOption['option-value'] = $optionValue['value'];
                        $colorOption['type'] = (int)$optionValue['type'];
                        $colorOption['thumb'] = $optionValue['thumb'];
                        $colorOption['label'] = $optionValue['label'];
                    }
                    $colorOptions[] = $colorOption;
                }

                usort($colorOptions, function($a, $b) {
                    return strcmp($a['option-id'], $b['option-id']);
                });

                return $colorOptions;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function addSwatchDataRecentlyViewedForAttribute(
        array $swatchesCollectionArray,
        array $attributeDataArray,
        $configurableProduct
    ) {
        $result = [];
        foreach ($swatchesCollectionArray as $optionId => $option) {
            $result[$optionId] = $this->extractNecessarySwatchDataRecentlyViewed($swatchesCollectionArray[$optionId]);
            $result[$optionId] = $this->addAdditionalMediaDataRecentlyViewed($result[$optionId], $optionId, $attributeDataArray, $configurableProduct);
            $result[$optionId]['label'] = $option['value'];
        }

        return $result;
    }

    protected function extractNecessarySwatchDataRecentlyViewed(array $swatchDataArray)
    {
        $result['type'] = $swatchDataArray['type'];

        if ($result['type'] == Swatch::SWATCH_TYPE_VISUAL_IMAGE && !empty($swatchDataArray['value'])) {
            $result['value'] = $this->mediaHelper->getSwatchAttributeImage(
                Swatch::SWATCH_IMAGE_NAME,
                $swatchDataArray['value']
            );
            $result['thumb'] = $this->mediaHelper->getSwatchAttributeImage(
                Swatch::SWATCH_THUMBNAIL_NAME,
                $swatchDataArray['value']
            );
        } else {
            $result['value'] = $swatchDataArray['value'];
        }

        return $result;
    }

    protected function addAdditionalMediaDataRecentlyViewed(
        array $swatch,
        $optionId,
        array $attributeDataArray,
        $configurableProduct
    ) {
        $websiteId = $this->storeManager->getStore($configurableProduct->getStoreId())->getWebsiteId();
        $isWebsiteDisableProductImage = $this->disableSwatchConfig->getDisableSwatchWebsites($websiteId);
        if (
            isset($attributeDataArray['use_product_image_for_swatch'])
            && $attributeDataArray['use_product_image_for_swatch']
            && !$isWebsiteDisableProductImage
        ) {
            $variationMedia = $this->getVariationMedia($attributeDataArray['attribute_code'], $optionId, $configurableProduct);
            if (!empty($variationMedia)) {
                $swatch['type'] = Swatch::SWATCH_TYPE_VISUAL_IMAGE;
                $swatch = array_merge($swatch, $variationMedia);
            }
        }
        return $swatch;
    }

    protected function getVariationMedia($attributeCode, $optionId, $configurableProduct)
    {
        $variationProduct = $this->swatchHelper->loadFirstVariationWithSwatchImage(
            $configurableProduct,
            [$attributeCode => $optionId]
        );

        if (!$variationProduct) {
            $variationProduct = $this->swatchHelper->loadFirstVariationWithImage(
                $configurableProduct,
                [$attributeCode => $optionId]
            );
        }

        $variationMediaArray = [];
        if ($variationProduct) {
            $variationMediaArray = [
                'value' => $this->getSwatchProductImage($variationProduct, Swatch::SWATCH_IMAGE_NAME),
                'thumb' => $this->getSwatchProductImage($variationProduct, Swatch::SWATCH_THUMBNAIL_NAME),
            ];
        }

        return $variationMediaArray;
    }

    protected function getSwatchProductImage(Product $childProduct, $imageType)
    {
        if ($this->isProductHasImage($childProduct, Swatch::SWATCH_IMAGE_NAME)) {
            $swatchImageId = $imageType;
            $imageAttributes = ['type' => Swatch::SWATCH_IMAGE_NAME];
        } elseif ($this->isProductHasImage($childProduct, 'image')) {
            $swatchImageId = $imageType == Swatch::SWATCH_IMAGE_NAME ? 'swatch_image_base' : 'swatch_thumb_base';
            $imageAttributes = ['type' => 'image'];
        }

        if (!empty($swatchImageId) && !empty($imageAttributes['type'])) {
            return $this->imageUrlBuilder->getUrl($childProduct->getData($imageAttributes['type']), $swatchImageId);
        }
    }

    protected function isProductHasImage(Product $product, $imageType)
    {
        return $product->getData($imageType) !== null && $product->getData($imageType) != Data::EMPTY_IMAGE_VALUE;
    }
}
