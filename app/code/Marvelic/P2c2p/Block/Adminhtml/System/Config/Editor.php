<?php

namespace Marvelic\P2c2p\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends Field
{
    /**
     * @var WysiwygConfig
     */
    protected $_wysiwygConfig;

    /**
     * @param Context       $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array         $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // set wysiwyg for element
        $element->setWysiwyg(true);
        $config = [
            'add_variables' => false,
            'add_widgets' => false
        ];
        $wysiwygConfig = $this->_wysiwygConfig->getConfig($config);
        $wysiwygConfig->setAddImages(false);
        $element->setConfig($wysiwygConfig);

        return parent::_getElementHtml($element);
    }
}
