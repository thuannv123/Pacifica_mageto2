<?php

namespace Marvelic\P2c2p\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;

class PaymentMethod extends \P2c2p\P2c2pPayment\Model\PaymentMethod
{
    protected $_filterProvider;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );

        $this->_filterProvider = $filterProvider;
    }

    public function getMethodCode()
    {
        return $this->_code;
    }

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
