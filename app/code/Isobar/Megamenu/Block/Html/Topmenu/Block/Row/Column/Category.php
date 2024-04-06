<?php

namespace Isobar\Megamenu\Block\Html\Topmenu\Block\Row\Column;

use Magento\Framework\View\Element\Template;

class Category extends Entity
{
    const MAX_CATS = 100000;

    protected $_template = 'html/topmenu/block/row/column/category.phtml';

    protected $_layoutFactory;

    public function __construct(Template\Context $context, \Magento\Framework\View\LayoutFactory $layoutFactory, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_layoutFactory = $layoutFactory;
    }


    public function getCategory()
    {
        return $this->getEntity()->getCategory();
    }

    public function getPositionClass()
    {
        return $this->getEntity()->getPositionClass();
    }

    public function getModeRoot()
    {
        return $this->getData('mode');
    }


    public function getLevel()
    {
        return $this->getData('level');
    }

    public function getImage($node)
    {
        $url = "";
        if ($image = $node->getMmImage()) {
            if ($image) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            }
        }
        return $url;
    }

    public function genChildrenHtml($category)
    {
        $block = $this->_layoutFactory->create()->createBlock(
            'Isobar\Megamenu\Block\Html\Topmenu\Block',
            ''
        );
        $block->setNode($category);
        return $block->renderBlock();
    }
}
