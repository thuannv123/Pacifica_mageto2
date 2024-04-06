<?php
/**
 * Anowave Magento 2 Onestepcheckout Add-on for GTM (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ecoam
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ecoam\Block;

use Anowave\Ec\Block\Track;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;

class Checkout extends Track
{
    /**
     * @var \Anowave\Ecoam\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor 
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Anowave\Ecoam\Helper\Data $checkoutHelper
     * @param \Anowave\Ec\Helper\Data $helper
     * @param \Anowave\Ec\Helper\Datalayer $dataLayer
     * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Template\Context $context,
        \Anowave\Ecoam\Helper\Data $checkoutHelper,
        \Anowave\Ec\Helper\Data $helper,
        \Anowave\Ec\Helper\Datalayer $dataLayer,
        \Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        File $driverFile,
        LoggerInterface $logger,
        array $data = []
    )
    {
        /**
         * Set Helper
         * 
         * @var \Anowave\Ecoam\Helper\Data
         */
        $this->checkoutHelper = $checkoutHelper;
        $this->driverFile = $driverFile;
        $this->logger = $logger;
        /**
         * Parent constructor
         */
        parent::__construct($context, $helper, $dataLayer, $protocol, $currencyFactory, $driverFile,  $logger, $data);
    }
    
    /**
     * Get helper 
     * 
     * @return \Anowave\Ecoam\Helper\Data
     */
    public function getCheckoutHelper()
    {
        return $this->checkoutHelper;
    }
}