<?php

/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

namespace Marvelic\BeamCheckout\Model;

class BeamCheckoutEwallet extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    const CODE = 'beamcheckout_ewallet';

    /** 
     * @var string 
     */
    protected $_code = self::CODE;

    /** 
     * @var bool 
     */
    protected $_isInitializeNeeded = true;

    /** 
     * @var bool 
     */
    protected $_canCapture = true;

    /** 
     * @var bool 
     */
    protected $_isGateway = true;

    /** 
     * @var bool 
     */
    protected $_canUseInternal = true;

    /** 
     * @var bool 
     */
    protected $_canUseCheckout = true;

    /** 
     * @var bool 
     */
    protected $_canUseForMultishipping = false;

    /** 
     * @var string 
     */
    protected $_infoBlockType = \Magento\Payment\Block\Info\Instructions::class;

    /** 
     * @var \Magento\Cms\Model\Template\FilterProvider 
     */
    protected $_filterProvider;

    /**
     * BeamCheckoutEwallet constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Cms\Model\Template\FilterProvider
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger);
        $this->_filterProvider = $filterProvider;
    }

    //Set additional data and session object and use it further process.
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        return $this;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setState("Pending_BeamCheckout");
        $stateObject->setStatus("Pending_BeamCheckout");
        $stateObject->setIsNotified(false);
    }

    //Get Beam Description
    public function getDescriptions()
    {
        $instructions = $this->getConfigData('descriptions');
        if ($instructions == null) {
            return $instructions !== null ? trim($instructions) : '';
        } else {
            $html = $this->_filterProvider->getPageFilter()->filter($this->getConfigData('descriptions'));
            return $html;
        }
    }
}
