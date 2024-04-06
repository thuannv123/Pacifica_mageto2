<?php
/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */
namespace Atome\MagentoPayment\Services\Payment\Context;

use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class ApplyQuotePaymentContext
{
    /** @var Quote $quote */
    public $quote;

    /** @var Order $quote */
    public $order;

    /** @var PaymentResponse */
    public $paymentResponse;

    /** @var Order */
    public $orderCreated;
}
