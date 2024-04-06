<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\Payment;

use Atome\MagentoPayment\Block\PaymentDisplayInfoBlock;
use Atome\MagentoPayment\Enum\AdditionalInformationKey;
use Atome\MagentoPayment\Enum\PaymentStatus;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Config\LocaleConfig;
use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentResponse;
use Atome\MagentoPayment\Services\Payment\API\RefundRequest;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Model\Order\Payment;

class PaymentGateway extends AbstractMethod
{
    protected $_code = Atome::METHOD_CODE;
    protected $_isGateway = true;
    protected $_isInitializeNeeded = true;
    protected $_canOrder = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canUseInternal = false;
    protected $_canFetchTransactionInfo = false;
    protected $_infoBlockType = PaymentDisplayInfoBlock::class;

    protected $paymentGatewayConfig;
    protected $localeConfig;

    public function __construct(
        Context                              $context,
        Registry                             $registry,
        ExtensionAttributesFactory           $extensionFactory,
        AttributeValueFactory                $customAttributeFactory,
        Data                                 $paymentData,
        ScopeConfigInterface                 $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        Session                              $checkoutSession,
        PaymentGatewayConfig                 $paymentGatewayConfig,
        LocaleConfig                         $localeConfig,
        AbstractResource                     $resource = null,
        AbstractDb                           $resourceCollection = null,
        array                                $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->localeConfig = $localeConfig;
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @return void
     * @throws LocalizedException
     */
    public function initialize($paymentAction, $stateObject)
    {
        /** @var Payment $payment */
        $payment = $this->getInfoInstance();

        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);

        $state = $this->paymentGatewayConfig->getNewOrderState();
        $status = $this->paymentGatewayConfig->getNewOrderStatus();
        $stateObject->setState($state);
        $stateObject->setStatus($status);
        $stateObject->setIsNotified(false);
    }

    public function refund(InfoInterface $payment, $amount)
    {
        if (!$payment instanceof Payment) {
            throw new LocalizedException(__('unknown class to refund: ' . get_class($payment)));
        }

        if ($this->paymentGatewayConfig->getCountry() === 'tw' && round($amount) != $amount) {
            throw new LocalizedException(__('The refund amount must be integer'));
        }

        $this->paymentGatewayConfig->setStoreId($payment->getOrder()->getStoreId());
        // check current status of payment, if it is "REFUNDED", then return directly
        $referenceId = $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID);

        $getPaymentRequest = new GetPaymentRequest();
        $getPaymentRequest->setReferenceId($referenceId);
        $getPaymentRequest->request();
        /** @var GetPaymentResponse $getPaymentResponse */
        $getPaymentResponse = $getPaymentRequest->getWrappedResponse();

        if ($getPaymentResponse->getStatus() === PaymentStatus::REFUNDED) {
            Logger::instance()->info("payment {$referenceId} has been refunded, return");
            return $this;
        }
        Logger::instance()->info("the amount from magento refund function: " . $amount);
        $amount = $payment->getCreditmemo()->getGrandTotal();
        Logger::instance()->info("the amount from credit memo: " . $amount);

        $refundRequest = new RefundRequest();
        $refundRequest->setReferenceId($payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID))
            ->setAmount($amount)
            ->request();

        return $this;
    }

    public function isAvailable(CartInterface $quote = null)
    {
        if (!$this->paymentGatewayConfig->getMerchantApiKey() || !$this->paymentGatewayConfig->getMerchantApiSecret()) {
            return false;
        }

        $maxSpend = $this->paymentGatewayConfig->getMaxSpend();
        if ($maxSpend && $quote->getGrandTotal() > $maxSpend) {
            return false;
        }

        $minSpend = $this->paymentGatewayConfig->getMinSpend();
        if ($minSpend && $quote->getGrandTotal() < $minSpend) {
            return false;
        }

        if ($this->isExcludedCategory($quote)) {
            return false;
        }

        return true;
    }

    /**
     * @param CartInterface $quote
     * @return bool
     */
    protected function isExcludedCategory($quote)
    {
        if ($excludedCategories = $this->paymentGatewayConfig->getExcludedCategories()) {
            $excludedCategoriesArray = explode(",", $excludedCategories??'');

            foreach ($quote->getAllVisibleItems() as $item) {
                $product = $item->getProduct();
                $categoryIds = $product->getCategoryIds();
                foreach ($categoryIds as $k) {
                    if (in_array($k, $excludedCategoriesArray)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


    public function canUseForCurrency($currencyCode)
    {
        return in_array($currencyCode, $this->localeConfig->getSupportedCurrencyCodes());
    }





}
