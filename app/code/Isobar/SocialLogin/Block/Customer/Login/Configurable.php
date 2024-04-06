<?php
namespace Isobar\SocialLogin\Block\Customer\Login;

use Isobar\SocialLogin\Block\Customer\Login;

/**
 * Class Configurable Login
 */
class Configurable extends Login
{
    /**
     * Is block visible
     *
     * @var bool
     */
    protected $isVisible = true;
    /**
     * @var \Isobar\SocialLogin\Model\LoginBlock\Settings
     */
    protected $blockSettings;

    /**
     * Configurable constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Isobar\SocialLogin\Model\Config\General $moduleConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Isobar\SocialLogin\Model\ProviderManagement $providerManagement
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Isobar\SocialLogin\Model\LoginBlock\Settings $blockSettings
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Isobar\SocialLogin\Model\Config\General $moduleConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Isobar\SocialLogin\Model\ProviderManagement $providerManagement,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Isobar\SocialLogin\Model\LoginBlock\Settings $blockSettings,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $moduleConfig,
            $httpContext,
            $providerManagement,
            $postDataHelper,
            $data
        );
        $this->blockSettings = $blockSettings;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->prepareBlockSettings();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare block settings
     *
     * @return $this
     */
    protected function prepareBlockSettings()
    {
        $this->isVisible = $this->blockSettings->isGroupVisible($this->getConfigurableGroup());

        $template = $this->blockSettings->getGroupTemplate($this->getConfigurableGroup());
        $this->setTemplate($template->getPath());
        $this->addData($template->getAdditionalData());

        return $this;
    }

    /**
     * Get configurable group
     *
     * @return string
     */
    public function getConfigurableGroup()
    {
        return $this->getData('configurable_group');
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->isVisible ? parent::_toHtml() : '';
    }
}
