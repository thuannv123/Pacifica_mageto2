<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Marvelic\Wishlist\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistItemCollection;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection as WishlistCollection;

/**
 * Multiple wishlist helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\MultipleWishlist\Helper\Data
{
    protected $_defaultWishlistsByCustomer = [];
    protected $_itemCollectionFactory;

    protected $_wishlistCollectionFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $customerSession,
        WishlistFactory $wishlistFactory,
        StoreManagerInterface $storeManager,
        PostHelper $postDataHelper,
        View $customerViewHelper,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        WishlistItemCollection $itemCollectionFactory,
        CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $wishlistProvider,
            $productRepository,
            $itemCollectionFactory,
            $wishlistCollectionFactory
        );
    }
    public function getConfigureUrlNew($item)
    {
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        } else {
            $product = $item->getProduct();
        }
        $buyRequest = $item->getBuyRequest();
        $fragment = [];
        if (is_object($buyRequest)) {
            $config = $buyRequest->getSuperProductConfig();
            if ($config && !empty($config['product_id'])) {
                $product = $this->productRepository->getById(
                    $config['product_id'],
                    false,
                    $this->_storeManager->getStore()->getStoreId()
                );
            }
            $fragment = $buyRequest->getSuperAttribute() ?? [];
            if ($buyRequest->getQty()) {
                $additional['_query']['qty'] = $buyRequest->getQty();
            }
        }
        $data = '';
        foreach ($fragment as $key => $value) {
            switch ($key) {
                case '93':
                    $data .= 'color='. $value;
                    break;
                case '301':
                    $data .= '&width_id='. $value;
                    break;
                case '161':
                    $data .= '&size='. $value;
                    break;
            }
        }
        $url = $this->_getUrl('wishlist/index/configure',
            [
                'id' => $item->getWishlistItemId(),
                'product_id' => $item->getProductId(),
                'qty' => (int)$item->getQty()
            ]
        ). '?' . $data;
        return $url;
    }
}
