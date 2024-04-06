<?php
namespace Isobar\SocialLogin\Model\LoginBlock;

/**
 * Class Settings
 */
class Settings
{
    /**
     * @var \Isobar\SocialLogin\Model\Config\LoginBlock
     */
    protected $loginBlockConfig;

    /**
     * @var \Isobar\SocialLogin\Model\LoginBlock\Template\Provider
     */
    protected $templateProvider;

    /**
     * @param \Isobar\SocialLogin\Model\Config\LoginBlock $loginBlockConfig
     * @param Template\Provider $templateProvider
     */
    public function __construct(
        \Isobar\SocialLogin\Model\Config\LoginBlock $loginBlockConfig,
        \Isobar\SocialLogin\Model\LoginBlock\Template\Provider $templateProvider
    ) {
        $this->loginBlockConfig = $loginBlockConfig;
        $this->templateProvider = $templateProvider;
    }

    /**
     * Is group visible
     *
     * @param string $group
     * @return bool
     */
    public function isGroupVisible($group)
    {
        $isVisible = $this->loginBlockConfig->isVisibleDefault();

        $settings = $this->loginBlockConfig->getGroupSettings($group);
        if (is_array($settings) && isset($settings['is_visible'])) {
            $isVisible = (bool)$settings['is_visible'];
        }

        return $isVisible;
    }

    /**
     * Get template instance
     *
     * @param string $group
     * @return Template
     * @throws \Isobar\SocialLogin\Exception\InvalidTemplateException
     */
    public function getGroupTemplate($group)
    {
        $templateId = $this->loginBlockConfig->getDefaultTemplate();

        $settings = $this->loginBlockConfig->getGroupSettings($group);
        if (is_array($settings) && isset($settings['template'])) {
            $templateId = $settings['template'];
        }

        return $this->templateProvider->getTemplateInstance($templateId);
    }
}
