<?php

namespace Isobar\P2c2p\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Session\SessionStartChecker;

class CallbackSessionChecker
{
    /**
     * Array
     */
    private const PAYMENT_CALLBACK_PATHS = [
        'p2c2p/payment/response',
        'P2c2p/payment/response'
    ];

    /**
     * @var Http
     */
    private $request;

    /**
     * @param Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }
    /**
     * Check if session can be started or not, taking payment extension's response action into consideration
     * @param \Magento\Framework\Session\SessionStartChecker $subject
     * @param bool $result
     * @return bool
     */
    public function afterCheck(\Magento\Framework\Session\SessionStartChecker $subject, bool $result) : bool
    {
        if ($result === false) {
            return false;
        }

        $inArray = true;
        foreach (self::PAYMENT_CALLBACK_PATHS as $path) {
            if (strpos((string)$this->request->getPathInfo(), $path) !== false) {
                $inArray = false;
                break;
            }
        }
        return $inArray;
    }
}
