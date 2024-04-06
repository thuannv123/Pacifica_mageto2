<?php
namespace Isobar\SocialLogin\Block\Element\Html\Link;

use \Isobar\SocialLogin\Block\Element\Template\VisibilityTrait;

/**
 * Class Current
 */
class Current extends \Magento\Framework\View\Element\Html\Link\Current
{
    use VisibilityTrait;

    /**
     * @var \Isobar\SocialLogin\Model\Config\General
     */
    protected $moduleConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Isobar\SocialLogin\Model\Config\General $moduleConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Isobar\SocialLogin\Model\Config\General $moduleConfig,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @return \Isobar\SocialLogin\Model\Config\General
     */
    protected function getModuleConfig()
    {
        return $this->moduleConfig;
    }
}
