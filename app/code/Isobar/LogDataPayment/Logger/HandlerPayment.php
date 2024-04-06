<?php
namespace Isobar\LogDataPayment\Logger;

use Monolog\Logger;

/**
 * Class HandlerPayment
 * @package Isobar\LogDataPayment\Logger
 */
class HandlerPayment extends \Magento\Framework\Logger\Handler\Base
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
    protected $fileName = '/var/log/payment_success_order.log';
}
