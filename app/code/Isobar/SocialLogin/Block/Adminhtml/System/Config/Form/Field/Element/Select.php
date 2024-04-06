<?php

namespace Isobar\SocialLogin\Block\Adminhtml\System\Config\Form\Field\Element;

class Select extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Set input id
     *
     * @param string $id
     * @return $this
     */
    public function setInputId($id)
    {
        return $this->setId($id);
    }

    /**
     * Set input name
     *
     * @param string $name
     * @return $this
     */
    public function setInputName($name)
    {
        return $this->setName($name);
    }
}
