<?php

namespace Isobar\BirkenstockCustomize\Block\Product\Renderer;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Model\SwatchAttributesProvider;
use Isobar\BirkenstockCustomize\Model\Config as BirkenstockConfig;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * @var BirkenstockConfig
     */
    private BirkenstockConfig $birkenstockConfig;

    /**
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param Data $helper
     * @param CatalogProduct $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param SwatchData $swatchHelper
     * @param Media $swatchMediaHelper
     * @param BirkenstockConfig $birkenstockConfig
     * @param array $data
     * @param SwatchAttributesProvider|null $swatchAttributesProvider
     * @param UrlBuilder|null $imageUrlBuilder
     */
    public function __construct(
        Context                   $context,
        ArrayUtils                $arrayUtils,
        EncoderInterface          $jsonEncoder,
        Data                      $helper,
        CatalogProduct            $catalogProduct,
        CurrentCustomer           $currentCustomer,
        PriceCurrencyInterface    $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        SwatchData                $swatchHelper,
        Media                     $swatchMediaHelper,
        BirkenstockConfig         $birkenstockConfig,
        array                     $data = [],
        SwatchAttributesProvider  $swatchAttributesProvider = null,
        UrlBuilder                $imageUrlBuilder = null
    ) {
        $this->birkenstockConfig = $birkenstockConfig;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider,
            $imageUrlBuilder
        );
    }

    /**
     * Add media from variation
     *
     * @param array $swatch
     * @param integer $optionId
     * @param array $attributeDataArray
     * @return array
     * @throws LocalizedException
     */
    protected function addAdditionalMediaData(array $swatch, $optionId, array $attributeDataArray)
    {
        $currentWebsite = $this->_storeManager->getWebsite();
        $isWebsiteDisableProductImage = $this->birkenstockConfig->getDisableSwatchWebsites($currentWebsite->getWebsiteId());
        if (isset($attributeDataArray['use_product_image_for_swatch'])
            && $attributeDataArray['use_product_image_for_swatch']
            && !$isWebsiteDisableProductImage
        ) {
            $variationMedia = $this->getVariationMedia($attributeDataArray['attribute_code'], $optionId);
            if (!empty($variationMedia)) {
                $swatch['type'] = Swatch::SWATCH_TYPE_VISUAL_IMAGE;
                $swatch = array_merge($swatch, $variationMedia);
            }
        }
        return $swatch;
    }
}
