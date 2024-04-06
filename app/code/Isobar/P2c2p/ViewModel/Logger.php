<?php

namespace Isobar\P2c2p\ViewModel;

use Psr\Log\LoggerInterface;

class Logger implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log($data){
        $this->logger->error($data);
    }
}
