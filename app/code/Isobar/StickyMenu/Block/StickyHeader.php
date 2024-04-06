<?php
namespace Isobar\StickyMenu\Block;

class StickyHeader extends \Magento\Framework\View\Element\Template
{

    /**
     * IS Enable Sticky or not
     */
    const IS_ENABLE_STICKY = 'theme_setting/navigation/enable_sticky_menu';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return boolean
     */
    public function isStickyEnable()
    {
        $isEnable = $this->scopeConfig->getValue(self::IS_ENABLE_STICKY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $isEnable == '1' ? true : false;
    }
}
