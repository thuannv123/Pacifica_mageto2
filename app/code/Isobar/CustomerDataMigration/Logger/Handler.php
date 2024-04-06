<?php
namespace Isobar\CustomerDataMigration\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger;

/**
 * Class Handler
 * @package Isobar\CustomerDataMigration\Logger
 */
class Handler extends BaseHandler
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::WARNING;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/sendResetEmailLogger.log';
}