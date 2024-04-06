<?php

namespace Marvelic\P2c2p\Model;

class CompositeConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    protected $methodsCode = '';

    protected $methods = [];

    protected $marvelicP2c2pPaymentMethod;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Marvelic\P2c2p\Model\PaymentMethod $marvelicP2c2pPaymentMethod
    ) {
        $this->marvelicP2c2pPaymentMethod = $marvelicP2c2pPaymentMethod;
        $this->methodsCode = $this->marvelicP2c2pPaymentMethod->getMethodCode();
        $this->methods[$this->methodsCode] = $paymentHelper->getMethodInstance($this->methodsCode);
    }

    public function getConfig()
    {
        $config = [];
        if ($this->methods[$this->methodsCode]->isAvailable()) {
            $config['payment']['descriptions'][$this->methodsCode] = $this->marvelicP2c2pPaymentMethod->getDescriptions();
        }
        return $config;
    }
}
