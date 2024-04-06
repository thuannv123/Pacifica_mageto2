<?php
namespace Marvelic\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Framework\Pricing\Helper\Data as PriceData;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use \Magento\Customer\Model\Url as CustomerUrl;

class Data extends AbstractHelper
{
    protected $_urlBuilder;
    protected $storeManager;
    protected $scopeConfig;
    protected $productRepositoryInterface;
    protected $session;
    protected $quote;
    protected $priceHelper;
    protected $_registry;
    protected $_requestInter;
    protected $customerUrl;
    
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
    public function __construct(
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepositoryInterface,
        Session $session,
        Quote $quote,
        PriceData $priceHelper,
        Registry $registry,
        RequestInterface $request,
        CustomerUrl $customerUrl,
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->session = $session;
        $this->quote = $quote;
        $this->priceHelper = $priceHelper;
        $this->_registry = $registry;
        $this->_requestInter = $request;
        $this->customerUrl = $customerUrl;
    }

    public function getColorProduct($productId)
    {
        $items = $this->session->getQuote()->getAllItems();
        foreach ($items as $key => $item) {
            if ($productId == $item->getProductId()) {
                if ($item->getProduct()->getTypeId() == 'configurable') {
                    $i = $key + 1;
                    if (isset($items[$i])) {
                        $product = $this->productRepositoryInterface->getById($items[$i]->getProductId());
                        return $product->getResource()->getAttribute('color')->getFrontend()->getValue($product);
                    }
                } else {
                    $product = $this->productRepositoryInterface->getById($item->getProductId());
                    return $product->getResource()->getAttribute('color')->getFrontend()->getValue($product);
                }
            }
        }
        return false;
    }
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
    }

    public function getTitleBase() {
        return $this->scopeConfig->getValue('design/head/default_title','websites');
    }
    public function getActiveMobileAerie() {
        return $this->scopeConfig->getValue('megamenu/config/megamenu_general_active_mobile_logo','websites');
    }
    public function getStoreCurrent()
    {
        return $this->storeManager->getStore()->getCode();
    }

    public function getProductPrice($price, $qty) {
        if ($qty > 0) {
            $price = $price * $qty;
        }
        $price = $this->priceHelper->currency($price, true, false);
        return $price;
    }

    public function getProductSpecialPrice($product, $qty) {
        if ($product->hasSpecialPrice()) {
            $oldPrice = $product->getSpecialPrice() * $qty;
        } else {
            $oldPrice = $product->getPrice() * $qty;
        }
        return $this->priceHelper->currency($oldPrice, true, false);
    }

    public function getProductNormalPrice($price) {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getStyle($id) {
        $product = $this->productRepositoryInterface->getById($id);
        if (!empty($product->getStyle())) {
            return $product->getStyle();
        } else {
            return '';
        }
    }
    public function getCurrentUrl() {
        if(strpos($this->_requestInter->getRequestUri(),'checkout') !== false){
            if(strpos($this->_requestInter->getRequestUri(),'checkout/cart') !== false){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    public function checkAE_logo() {
        if(isset($_COOKIE['website'])){
            $checkWebsite = $_COOKIE['website'];
            $categoryCurrent = $this->getCategoryCurrent();
            if($categoryCurrent){
                if((strpos($categoryCurrent, 'aerie') !== false)){
                    if($checkWebsite == 'american'){
                        $checkWebsite = 'aerie';
                    }
                }else{
                    if($checkWebsite == 'aerie'){
                        $checkWebsite = 'american';
                    }
                }
            }else{
                if($this->_requestInter->getControllerModule() == 'Magento_Cms'){
                    if(strpos($this->_requestInter->getRequestUri(),'aerie') !== false){
                        $checkWebsite = 'aerie';
                    }
                }
            }
        }else{
            if((strpos($this->_urlBuilder->getCurrentUrl(), 'aerie') !== false)){
                $checkWebsite = 'aerie';
            }else{
                $checkWebsite = 'american';
            }
        }
        return $checkWebsite;
    }
    public function getCategoryCurrent() {
        $category = $this->_registry->registry('current_category');
        if($category){
            $categoryLevel = $category->getLevel();
            for ($i=0; $i <= (int)$categoryLevel ; $i++) { 
                if($category->getLevel() != '2'){
                    $category = $category->getParentCategory();
                }
                else{
                    $categoryName = $category->getName();
                    return strtolower($categoryName);
                }
            }
        }
        return false;
    }
    public function getCustomerUrlLogged() {
        return $this->customerUrl->getAccountUrl();
    }
    public function getProductCartPage($sku) {
        $product = $this->productRepositoryInterface->get($sku);
        return $product;
    }
}