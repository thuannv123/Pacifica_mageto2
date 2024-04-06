<?php

namespace Marvelic\AbandonedCart\Block\Email;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template as AbstractTemplate;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Config;
use Mageplaza\AbandonedCart\Helper\Data as ModuleHelper;
use Mageplaza\AbandonedCart\Model\System\Config\Source\RelatedProductOptions;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class Template
 * @package Mageplaza\AbandonedCart\Block\Email
 */
class Template extends \Mageplaza\AbandonedCart\Block\Email\Template
{
    protected $_productRepository;
    protected $stockItemRepository;
    protected $imageHelper;
    protected $priceCurrency;
    protected $helperData;
    protected $taxHelper;
    protected $quoteFactory;
    protected $abstractProduct;
    protected $frontUrlModel;
    protected $encryptor;
    protected $product;
    protected $itemResolver;
    protected $_imageHelper;

    public function __construct(
        Context                    $context,
        ProductRepositoryInterface $productRepository,
        StockItemRepository        $stockItemRepository,
        PriceCurrency              $priceCurrency,
        ModuleHelper               $helperData,
        QuoteFactory               $quoteFactory,
        AbstractProduct            $abstractProduct,
        UrlInterface               $frontUrlModel,
        EncryptorInterface         $encryptor,
        ItemResolverInterface $itemResolver = null,
        \Magento\Catalog\Helper\Image $imageHelper,
        array                      $data = []
    ) {
        $this->_productRepository   = $productRepository;
        $this->stockItemRepository  = $stockItemRepository;
        $this->imageHelper          = $context->getImageHelper();
        $this->priceCurrency        = $priceCurrency;
        $this->helperData           = $helperData;
        $this->taxHelper            = $context->getTaxData();
        $this->quoteFactory         = $quoteFactory;
        $this->abstractProduct      = $abstractProduct;
        $this->frontUrlModel        = $frontUrlModel;
        $this->encryptor            = $encryptor;
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
        $this->_imageHelper = $imageHelper;

        parent::__construct(
            $context,
            $productRepository,
            $stockItemRepository,
            $priceCurrency,
            $helperData,
            $quoteFactory,
            $abstractProduct,
            $frontUrlModel,
            $encryptor
        );
    }
    public function log($data)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/reminder_order.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info($data);
    }
    /**
     * @return Quote|null
     */
    public function getQuote()
    {
        if ($quoteId = $this->getQuoteId()) {
            return $this->quoteFactory->create()->load($quoteId);
        }

        return null;
    }

    /**
     * Get items in quote
     *
     * @return Item[]
     */
    public function getProductCollection()
    {
        $items = [];

        if ($quote = $this->getQuote()) {
            return $quote->getAllVisibleItems();
        }

        return $items;
    }

    /**
     * Get subtotal in quote
     *
     * @param bool $inclTax
     *
     * @return float|string
     */
    public function getSubtotal($inclTax = false)
    {
        $subtotal = 0;
        if ($quote = $this->getQuote()) {
            $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
            $subtotal = $inclTax ? $address->getSubtotalInclTax() : $address->getSubtotal();
        }

        return $this->priceCurrency->format(
            $subtotal,
            true,
            PriceCurrency::DEFAULT_PRECISION,
            $quote ? $quote->getStoreId() : null,
            $quote ? $quote->getQuoteCurrencyCode() : null
        );
    }

    /**
     * Get image url in quote
     *
     * @param Item $item
     *
     * @return string
     */
    public function getProductImage($item)
    {
        try {
            $productImage = $this->_imageHelper->init($this->itemResolver->getFinalProduct($item), 'cart_page_product_thumbnail');
            $imageUrl = $productImage->getUrl();
            $this->log($imageUrl);
            return $imageUrl;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get item price in quote
     *
     * @param Item $item
     * @param bool $inclTax
     *
     * @return float|string
     */
    public function getProductPrice($item, $inclTax = false)
    {
        $productPrice = $inclTax ? $item->getRowTotalInclTax() : $item->getRowTotal();
        $quote = $this->getQuote();

        return $this->priceCurrency->format(
            $productPrice,
            false,
            PriceCurrency::DEFAULT_PRECISION,
            $quote ? $quote->getStoreId() : null,
            $quote ? $quote->getQuoteCurrencyCode() : null
        );
    }

    /**
     * @return string
     */
    public function getPlaceholderImage()
    {
        return $this->imageHelper->getDefaultPlaceholderUrl('image');
    }

    /**
     * @return Config
     */
    public function getTaxConfig()
    {
        return $this->taxHelper->getConfig();
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        if ($quote = $this->getQuote()) {
            return $quote->getStoreId();
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->helperData->getConfigGeneral('related_product_title', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getLimitNumberProduct()
    {
        return $this->helperData->getConfigGeneral('related_product_limit', $this->getStoreId());
    }

    /**
     * @return mixed
     */
    public function getIsExcludeOutOfStock()
    {
        return $this->helperData->getConfigGeneral('related_product_out_of_stock', $this->getStoreId());
    }

    /**
     * @return array
     */
    public function getRelatedProductCollection()
    {
        $items = [];

        if ($quote = $this->getQuote()) {
            $relatedProduct = $this->helperData->getConfigGeneral('related_product', $this->getStoreId());

            /** @var Item $quoteItem */
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                switch ((int)$relatedProduct) {
                    case RelatedProductOptions::RELATED_PRODUCTS:
                        if ($this->getIsExcludeOutOfStock()) {
                            $dataProduct = [];
                            foreach ($quoteItem->getProduct()->getRelatedProducts() as $productItems) {
                                $productStock = $this->stockItemRepository->get($productItems->getId());
                                if ($productStock->getIsInStock()) {
                                    $dataProduct[] = $productItems;
                                }
                            }
                            $items[$quoteItem->getId()] = $dataProduct;
                        } else {
                            $items[$quoteItem->getId()] = $quoteItem->getProduct()->getRelatedProducts();
                        }
                        break;
                    case RelatedProductOptions::UP_SELL_PRODUCTS:
                        if ($this->getIsExcludeOutOfStock()) {
                            $dataProduct = [];
                            foreach ($quoteItem->getProduct()->getUpSellProducts() as $productItems) {
                                $productStock = $this->stockItemRepository->get($productItems->getId());
                                if ($productStock->getIsInStock()) {
                                    $dataProduct[] = $productItems;
                                }
                            }
                            $items[$quoteItem->getId()] = $dataProduct;
                        } else {
                            $items[$quoteItem->getId()] = $quoteItem->getProduct()->getUpSellProducts();
                        }
                        break;
                    case RelatedProductOptions::CROSS_SELL_PRODUCTS:
                        if ($this->getIsExcludeOutOfStock()) {
                            $dataProduct = [];
                            foreach ($quoteItem->getProduct()->getCrossSellProducts() as $productItems) {
                                $productStock = $this->stockItemRepository->get($productItems->getId());
                                if ($productStock->getIsInStock()) {
                                    $dataProduct[] = $productItems;
                                }
                            }
                            $items[$quoteItem->getId()] = $dataProduct;
                        } else {
                            $items[$quoteItem->getId()] = $quoteItem->getProduct()->getCrossSellProducts();
                        }
                        break;
                }
            }
        }

        return array_filter($items);
    }

    /**
     * @param Product $product
     *
     * @return mixed
     */
    public function getProduct($product)
    {
        return $product->load($product->getId());
    }

    /**
     * @param Product $product
     *
     * @return string
     */
    public function getProductUrl($product)
    {
        return $this->abstractProduct->getProductUrl($product);
    }

    /**
     * @param Product $product
     *
     * @return string|string[]|null
     */
    public function getImage($product)
    {
        try {
            $store = $this->_storeManager->getStore();
            $imageUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();

            return str_replace('\\', '/', $imageUrl);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUnsubscribeUrl()
    {
        return $this->frontUrlModel->getUrl(
            'abandonedcart/subscriber/unsubscribe',
            [
                'email' => $this->getQuote()
                    ? $this->encryptor->encrypt((string)$this->getQuote()->getCustomerEmail()) : '',
                'website_id' => $this->_storeManager->getStore($this->getStoreId())->getWebsiteId()
            ]
        );
    }

    /**
     * @return mixed
     */
    public function isEnableUnsubscribeLink()
    {
        return $this->helperData->isEnableUnsubscribeLink($this->getStoreId());
    }
}