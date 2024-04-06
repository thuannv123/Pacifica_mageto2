<?php

namespace Isobar\BankTransferProcess\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;

class InstructionsConfigProvider implements ConfigProviderInterface
{
    protected $cards = null;
    protected $methodCodes = ['banktransfer'];
    protected $methods = [];
    protected $escaper;
    protected $_filterProvider;
    protected $storeManager;
    protected $layout;

    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        LayoutInterface $layout
    ) {
        $this->escaper = $escaper;
        $this->_filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
        $this->layout = $layout;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    public function getConfig()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['descriptions'][$code] = $this->getDescriptions($code);
            }
        }
        return $config;
    }

    protected function getDescriptions($code)
    {
        $instructions = $this->methods[$code]->getDescriptions();
        return $this->convertMediaUrl($instructions);
    }

    public function convertMediaUrl($content)
    {
        $convertedContent = '';
        $pattern =  "/{{media url=&quot;(.*?)&quot;}}/";
        if (preg_match($pattern, $content, $matches)) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $convertedContent = preg_replace($pattern, $baseUrl . '$1', $content);
            return $convertedContent;
        } else {
            return $content;
        }
    }
}
