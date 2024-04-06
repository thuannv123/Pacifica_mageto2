<?php
namespace Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\LoginBlock;

/**
 * Class Template
 */
class Template extends \Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Renderer\Select
{
    /**
     * @var \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Template
     */
    protected $templateSource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Template $templateSource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Template $templateSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->templateSource = $templateSource;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->templateSource->toOptionArray();
    }
}
