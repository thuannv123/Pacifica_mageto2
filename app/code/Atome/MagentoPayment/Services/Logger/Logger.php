<?php

namespace Atome\MagentoPayment\Services\Logger;

use Atome\MagentoPayment\Services\Config\PaymentGatewayConfig;
use Magento\Framework\App\ObjectManager;

class Logger
{
    /**
     * @var MonologLogger
     */
    protected $monoLogger;

    /**
     * @var PaymentGatewayConfig
     */
    protected $paymentGatewayConfig;

    /**
     * @var Logger
     */
    protected static $instance;

    public function __construct(
        MonologLogger        $monoLogger,
        PaymentGatewayConfig $paymentGatewayConfig
    )
    {
        $this->monoLogger = $monoLogger;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }


    /**
     * @return Logger
     */
    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = ObjectManager::getInstance()->create(Logger::class);
        }

        return static::$instance;
    }

    public function debug($message, array $context = [])
    {
        if ($this->paymentGatewayConfig->isDebugEnabled()) {
            $this->monoLogger->debug($message, $context);
        }
    }

    public function info($message, array $context = [])
    {
        $this->monoLogger->info($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->monoLogger->error($message, $context);
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function exception($exception, $method = '', $append = [])
    {
        $this->monoLogger->error(json_encode(
                array_merge(
                    [
                        'message' => $exception->getMessage(),
                        'method' => $method,
                        'trace' => $exception->getTraceAsString(),
                    ],
                    $append
                )
            )
        );
    }

}
