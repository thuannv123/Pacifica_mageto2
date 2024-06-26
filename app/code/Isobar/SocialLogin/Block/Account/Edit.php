<?php
namespace Isobar\SocialLogin\Block\Account;

/**
 * Class Edit
 */
class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Isobar\SocialLogin\Helper\State
     */
    private $stateHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Isobar\SocialLogin\Helper\State $stateHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Isobar\SocialLogin\Helper\State $stateHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->stateHelper = $stateHelper;
    }

    /**
     * Get firstname
     *
     * @return string
     * @throws \Isobar\SocialLogin\Exception\InvalidStateException
     */
    public function getFirstnameValue()
    {
        return $this->stateHelper->getAccount()->getFirstName();
    }

    /**
     * Get lastname value
     *
     * @return string
     * @throws \Isobar\SocialLogin\Exception\InvalidStateException
     */
    public function getLastnameValue()
    {
        return $this->stateHelper->getAccount()->getLastName();
    }

    /**
     * Get email value
     *
     * @return string
     * @throws \Isobar\SocialLogin\Exception\InvalidStateException
     */
    public function getEmailValue()
    {
        return $this->stateHelper->getAccount()->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->stateHelper->isAccountExist() ? parent::_toHtml() : '';
    }
}
