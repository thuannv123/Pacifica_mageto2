<?php

namespace Marvelic\AnowaveEc4\Helper;

use Psr\Log\LoggerInterface;

class Logger
{
    /**
     * LoggerInterface
     */
    private $logger;
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    /**
     * mixed $objectData
     * @return bool
     */
    public function writelog($data)
    {
        try {
            if (gettype($data) == 'object' || gettype($data) == 'array') {
                $this->logger->warning('Data request to GA4: ', array('data', $data));
            } else {
                $this->logger->warning('Data request to GA4: ' . $data);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        return true;
    }
}
