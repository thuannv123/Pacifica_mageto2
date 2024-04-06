<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Model;

class Logger
{
    /**
     * @var \Anowave\Ec\Model\LogFactory
     */
    protected $logFactory;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * Constructor 
     * 
     * @param \Anowave\Ec\Model\LogFactory $logFactory
     */
    public function __construct
    (
        \Anowave\Ec\Model\LogFactory $logFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logFactory = $logFactory;
        
        /**
         * Set Psr logger
         * 
         * @var \Psr\Log\LoggerInterface $logger
         */
        $this->logger = $logger;
    }
    
    /**
     * Log message 
     * 
     * @param string $message
     * @return \Anowave\Ec\Model\Logger
     */
    public function log(string $message = '')
    { 
        try 
        {
            $log = $this->logFactory->create();
            
            $log->setLog($message);
            $log->save();
        }
        catch (\Exception $e)
        {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        
        return $this;
    }
}