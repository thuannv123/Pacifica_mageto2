<?php
namespace Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\LoginBlock;

/**
 * Class Visibility
 */
class Visibility extends \Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Renderer\Select
{
    /**
     * @var \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Visibility
     */
    protected $visibilitySource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Visibility $visibilitySource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Isobar\SocialLogin\Model\Config\Source\LoginBlock\Visibility $visibilitySource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->visibilitySource = $visibilitySource;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->visibilitySource->toOptionArray();
    }
}
