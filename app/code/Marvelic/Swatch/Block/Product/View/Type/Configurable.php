<?php
namespace Marvelic\Swatch\Block\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Catalog\Model\ProductRepository;

class Configurable
{
    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    protected $salable;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        ProductRepository $productRepository
    ) {
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->productRepository = $productRepository;
    }

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result)
    {
        $config = $this->jsonDecoder->decode($result);

        $options = $config['index'];
        if($subject->getNameInLayout() == 'product.info.options.swatches'){
            if ($options) {
                $quantities = [];
                $colors = [];

                // product configurable
                $productConfigId = $config['productId'];
                $_product = $this->productRepository->getById($productConfigId);
                $colorBase = $_product->getResource()->getAttribute('color')->getFrontend()->getValue($_product);

                foreach ($options as $key => $value) {
                    $product = $this->productRepository->getById($key);
                    $quantities[$key] = ($product->isSaleable()) ? 1 : 0;
                    $colors[$key] = $product->getResource()->getAttribute('color')->getFrontend()->getValue($product);
                }
                $config['quantities'] = $quantities;
                $config['colors'] = $colors;
                $config['colorBase'] = $colorBase;
            }
        }

        return $this->jsonEncoder->encode($config);
    }
}
