<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */

namespace Atome\MagentoPayment\Services\Order;

use Atome\MagentoPayment\Enum\AdditionalInformationKey;
use Atome\MagentoPayment\Enum\PaymentStatus;
use Atome\MagentoPayment\Services\Config\Atome;
use Atome\MagentoPayment\Services\Logger\Logger;
use Atome\MagentoPayment\Services\Payment\API\CancelPaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentRequest;
use Atome\MagentoPayment\Services\Payment\API\GetPaymentResponse;
use Atome\MagentoPayment\Services\Payment\PaymentGateway;
use Atome\MagentoPayment\Services\Price\PriceService;
use Magento\Quote\Model\Quote;

class QuotePlugin
{
    /**
     * @var PriceService
     */
    protected $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param Quote $quote
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundSave($subject, \Closure $proceed, $quote)
    {
        $payment = $quote->getPayment();

        if ($quote->getIsActive() && $payment) {
            $oldReferenceId = $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID);
            if ($oldReferenceId) {
                $grandTotalFormatted = $this->priceService->format($quote->getGrandTotal());
                if ($grandTotalFormatted != $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED)
                    || $quote->getQuoteCurrencyCode() != $payment->getAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE)
                    || $payment->getMethod() !== Atome::METHOD_CODE
                ) {
                    try {
                        $getPaymentRequest = new GetPaymentRequest();
                        $getPaymentRequest->setReferenceId($oldReferenceId)->request();
                        /** @var GetPaymentResponse $getPaymentResponse */
                        $getPaymentResponse = $getPaymentRequest->getWrappedResponse();

                        switch ($getPaymentResponse->getStatus()) {
                            case PaymentStatus::PROCESSING:
                                $cancelPaymentRequest = new CancelPaymentRequest();
                                $cancelPaymentRequest
                                    ->setReferenceId($oldReferenceId)
                                    ->request();
                                break;
                            case PaymentStatus::PAID:
                                throw new \Exception("Previous payment was paid successfully, cannot edit again.");
                        }
                    } catch (\Exception $e) {
                        Logger::instance()->error(json_encode([
                            'message' => "Failed to cancel previous payment: " . $e->getMessage(),
                            'quote_id' => $quote->getId(),
                        ]));

                        throw $e;
                    }

                    $payment->unsAdditionalInformation(AdditionalInformationKey::PAYMENT_REFERENCE_ID);
                    $payment->unsAdditionalInformation(AdditionalInformationKey::PAYMENT_AMOUNT_FORMATTED);
                    $payment->unsAdditionalInformation(AdditionalInformationKey::PAYMENT_CURRENCY_CODE);
                    $payment->unsAdditionalInformation(AdditionalInformationKey::PAYMENT_DEBUG_SECRET);
                    $payment->unsAdditionalInformation(AdditionalInformationKey::MERCHANT_REFERENCE_ID);
                }
            }
        }

        return $proceed($quote);
    }
}
