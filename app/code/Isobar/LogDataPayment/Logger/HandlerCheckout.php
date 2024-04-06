<?php
namespace Isobar\LogDataPayment\Logger;

use Monolog\Logger;

/**
 * Class HandlerCheckout
 * @package Isobar\LogDataPayment\Logger
 */
class HandlerCheckout extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/checkout_success_order.log';
}
