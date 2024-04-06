<?php

namespace Marvelic\Catalog\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Api\Data\ProductRender\ImageInterfaceFactory;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\State;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\DesignLoader;

class Image extends \Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Image
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var array
     */
    private $imageCodes;

    /**
     * @var State
     */
    private $state;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var DesignInterface
     * @deprecated 103.0.1 DesignLoader is used for design theme loading
     */
    private $design;

    /**
     * @var ImageInterfaceFactory
     */
    private $imageRenderInfoFactory;

    /**
     * @var DesignLoader
     */
    private $designLoader;

    /**
     * Image constructor.
     * @param ImageFactory $imageFactory
     * @param State $state
     * @param StoreManager|StoreManagerInterface $storeManager
     * @param DesignInterface $design
     * @param ImageInterfaceFactory $imageRenderInfoFactory
     * @param array $imageCodes
     * @param DesignLoader $designLoader
     */
    public function __construct(
        ImageFactory $imageFactory,
        State $state,
        StoreManagerInterface $storeManager,
        DesignInterface $design,
        ImageInterfaceFactory $imageRenderInfoFactory,
        array $imageCodes = [],
        DesignLoader $designLoader = null
    ) {
        parent::__construct(
            $imageFactory,
            $state,
            $storeManager,
            $design,
            $imageRenderInfoFactory,
            $imageCodes,
            $designLoader
        );
        $this->imageFactory = $imageFactory;
        $this->imageCodes = $imageCodes;
        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->design = $design;
        $this->imageRenderInfoFactory = $imageRenderInfoFactory;
        $this->designLoader = $designLoader ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(DesignLoader::class);
    }
    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $images = [];
        /** @var ThemeInterface $currentTheme */
        $currentTheme = $this->design->getDesignTheme();
        $this->design->setDesignTheme($currentTheme);

        foreach ($this->imageCodes as $imageCode) {
            /** @var ImageInterface $image */
            $image = $this->imageRenderInfoFactory->create();
            /** @var \Magento\Catalog\Helper\Image $helper */
            $helper = $this->state
                ->emulateAreaCode(
                    'frontend',
                    [$this, "emulateImageCreating"],
                    [$product, $imageCode, (int) $productRender->getStoreId(), $image]
                );

            $image->setCode($imageCode);
            $height = $helper->getHeight();
            $image->setHeight($height);
            $width = $helper->getWidth();
            $image->setWidth($width);
            $image->setLabel($helper->getLabel());
            $image->setResizedHeight($height);
            $image->setResizedWidth($width);

            $image->setImageType($helper->getType());

            $images[] = $image;
        }

        $productRender->setImages($images);
    }
}
