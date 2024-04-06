<?php
namespace Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\LoginBlock;

/**
 * Class Group
 */
class Group extends \Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Renderer\Input
{
    /**
     * {@inheritdoc}
     */
    protected function getAdditionalAttributes()
    {
        return ' <%- !is_group_editable ? \\\'readonly\\\' : \\\'\\\' %>';
    }
}
