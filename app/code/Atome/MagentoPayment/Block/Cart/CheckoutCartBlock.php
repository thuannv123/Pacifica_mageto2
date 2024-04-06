<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Block\Cart;

use Atome\MagentoPayment\Services\Config\ConfigService;
use Atome\MagentoPayment\Services\Config\LocaleConfig;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Payment\PaymentGateway;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;

class CheckoutCartBlock extends Template
{
    protected $paymentGatewayConfig;
    protected $localeConfig;
    protected $paymentGateway;
    protected $checkoutSession;

    public function __construct(
        Template\Context     $context,
        PaymentGatewayConfig $paymentGatewayConfig,
        LocaleConfig         $localeConfig,
        PaymentGateway       $paymentGateway,
        CheckoutSession      $checkoutSession,
        array                $data
    )
    {
        parent::__construct($context, $data);

        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->localeConfig = $localeConfig;
        $this->paymentGateway = $paymentGateway;
        $this->checkoutSession = $checkoutSession;
    }

    public function canShow()
    {
        if (!$this->paymentGatewayConfig->isActive()) {
            return false;
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$this->canUseForCurrencyAmount($quote->getQuoteCurrencyCode(), $quote->getGrandTotal())) {
            return false;
        }
        $products = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $products[] = $item->getProduct();
        }
        return $this->canUseForProducts($products);
    }

    public function canUseForCurrencyAmount($currencyCode, $amount)
    {
        $min = $this->paymentGatewayConfig->getMinSpend();
        $max = $this->paymentGatewayConfig->getMaxSpend();
        return $this->paymentGateway->canUseForCurrency($currencyCode)
            && (!$min || $amount >= $min)
            && (!$max || $amount <= $max);
    }


    /**
     * @param Product[] $products
     * @return bool
     */
    public function canUseForProducts($products)
    {
        $excludedCategoriesString = $this->paymentGatewayConfig->getExcludedCategories();
        $excludedCategoriesArray = explode(",", $excludedCategoriesString ?? '');
        foreach ($products as $product) {
            $categoryIds = $product->getCategoryIds();
            foreach ($categoryIds as $k) {
                if (in_array($k, $excludedCategoriesArray)) {
                    return false;
                }
            }
        }
        return true;
    }


    public function getNewUserOffImage()
    {
        return ObjectManager::getInstance()->create(ConfigService::class)->getNewUserOffImage();
    }
}
