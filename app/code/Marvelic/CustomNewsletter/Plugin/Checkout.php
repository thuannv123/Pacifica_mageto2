<?php

namespace Marvelic\CustomNewsletter\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Checkout {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $scopeConfig;
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {

        $result['customnewsletter']['title']= $this->getTitle();
        $result['customnewsletter']['title_checkbox']= $this->getTitleCheckbox();
        $result['customnewsletter']['content']= $this->getContent();
        $result['customnewsletter']['content_contact']= $this->getContentContact();

        return $result;
    }

    public function getTitle() {
        return $this->scopeConfig->getValue('amasty_checkout/custom_newsletter/title',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->storeManager->getStore()->getId());
    }
    public function getTitleCheckbox() {
        return $this->scopeConfig->getValue('amasty_checkout/custom_newsletter/title_checkbox',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->storeManager->getStore()->getId());
    }
    public function getContent() {
        return $this->scopeConfig->getValue('amasty_checkout/custom_newsletter/content',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->storeManager->getStore()->getId());
    }
    public function getContentContact() {
        return $this->scopeConfig->getValue('amasty_checkout/custom_newsletter/content_contact',\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$this->storeManager->getStore()->getId());
    }
}
?>