<?php
namespace Isobar\SocialLogin\Block\Customer;

use Isobar\SocialLogin\Model\Provider\FactoryInterface as ProviderFactoryInterface;
use Magento\Customer\Model\Context;

/**
 * Class Login
 */
class Login extends \Isobar\SocialLogin\Block\Element\Template
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * @var \Isobar\SocialLogin\Model\ProviderManagement
     */
    protected $providerManagement;
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postDataHelper;


    /**
     * Login constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Isobar\SocialLogin\Model\Config\General $moduleConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Isobar\SocialLogin\Model\ProviderManagement $providerManagement
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Isobar\SocialLogin\Model\Config\General $moduleConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Isobar\SocialLogin\Model\ProviderManagement $providerManagement,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $moduleConfig, $data);
        $this->httpContext = $httpContext;
        $this->providerManagement = $providerManagement;
        $this->postDataHelper = $postDataHelper;
    }

    /**
     * Get providers
     *
     * @return ProviderFactoryInterface[]
     */
    public function getProviders()
    {
        return $this->providerManagement->getEnabledList();
    }

    /**
     * @param ProviderFactoryInterface $provider
     *
     * @return string
     */
    public function getLoginUrl(ProviderFactoryInterface $provider)
    {
        return $this->getUrl('social/account/login', ['provider' => $provider->getConfig()->getCode()]);
    }

    /**
     * Get container css class
     *
     * @return string
     */
    public function getCssClass()
    {
        return $this->getData('css_class') ?: '';
    }

    /**
     * Get login post data
     *
     * @param ProviderFactoryInterface $provider
     * @return string
     */
    public function getLoginPostData(ProviderFactoryInterface $provider)
    {
        return $this->postDataHelper->getPostData(
            $this->getLoginUrl($provider)
        );
    }

    /**
     * Is block visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return !$this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->isVisible() ? parent::_toHtml() : '';
    }
}
