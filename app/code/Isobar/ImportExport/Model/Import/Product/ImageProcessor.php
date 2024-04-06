<?php

namespace Isobar\ImportExport\Model\Import\Product;

use Firebear\ImportExport\Model\Import\UploaderFactory;
use Firebear\ImportExport\Model\QueueMessage\ImagePublisher;
use Firebear\ImportExport\Api\Data\SeparatorFormatterInterface;
use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory;
use Magento\MediaStorage\Service\ImageResize;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ImageProcessor extends \Firebear\ImportExport\Model\Import\Product\ImageProcessor
{
    protected $importImageResizeProcesser;

    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        UploaderFactory $uploaderFactory,
        ResourceModelFactory $resourceModelFactory,
        ImagePublisher $imagePublisher,
        ProductMetadataInterface $productMetadata,
        ProcessingErrorAggregatorInterface $errorAggregator,
        MediaConfig $mediaConfig,
        MetadataPool $metadataPool,
        ConsoleOutput $output,
        SeparatorFormatterInterface $separatorFormatter,
        AttributeCollectionFactory $attributeCollectionFactory,
        ImageResize $importImageResizeProcesser
    ) {
        parent::__construct(
            $filesystem,
            $logger,
            $storeManager,
            $serializer,
            $uploaderFactory,
            $resourceModelFactory,
            $imagePublisher,
            $productMetadata,
            $errorAggregator,
            $mediaConfig,
            $metadataPool,
            $output,
            $separatorFormatter,
            $attributeCollectionFactory
        );
        $this->importImageResizeProcesser = $importImageResizeProcesser;
    }

    /**
     * @return void
     */
    public function processImageResize()
    {
        if (!$this->imageResizeProcessor) {
            return;
        }
        try {
            if (!empty($this->bunchUploadedImages)) {
                foreach ($this->bunchUploadedImages as $path) {
                    if ($path && !in_array($path, $this->imageResizeIgnoreList)) {
                        $this->importImageResizeProcesser->resizeFromImageName($path);
                        $this->imageResizeIgnoreList[] = $path;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->addLogWriteln($e->getMessage(), $this->getOutput(), 'error');
        }
    }
}
