<?php

namespace Isobar\Megamenu\Block\Html\Topmenu;

use Isobar\Megamenu\Helper\Data as DataHelper;
use Isobar\Megamenu\Model\Configurator;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

/**
 * Class Block
 * @package Isobar\Megamenu\Block\Html\Topmenu
 */
class Block extends Template
{
    /** @var string */
    protected $_template = 'html/topmenu/block.phtml';

    /**
     * @var Node
     */
    protected $_node;

    /**
     * @var Configurator
     */
    protected $_configurator;

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Block constructor.
     * @param Template\Context $context
     * @param Configurator $configurator
     * @param DataHelper $helper
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Configurator $configurator,
        DataHelper $helper,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_configurator = $configurator;
        $this->_helper = $helper;
        $this->filterProvider = $filterProvider;
    }

    /**
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->_node = $node;
    }

    /**
     * @return int
     */
    public function getMenuLevel()
    {
        return $this->_node->getMenuLevel();
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->_node;
    }

    /**
     * @return string
     */
    public function renderBlock()
    {
        return $this->toHtml();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function renderRows()
    {
        $result = '';
        $nodeId = $this->_node->getId();
        if ($this->_helper->isCustomNode($nodeId)) {
            try {
                return $this->filterProvider->getPageFilter()->filter($this->_node->getContent());
            } catch (\Exception $exception) {
                return $result;
            }
        }
        $this->_configurator->init($this->_node);
        $this->setData('level', $this->getMenuLevel());
        $rowRenderer = $this->_layout->createBlock(
            'Isobar\Megamenu\Block\Html\Topmenu\Block\Row',
            '',
            ['data' => $this->getData()]
        );
        foreach ($this->_configurator->getRows() as $row) {
            $result .= $rowRenderer->renderRow($row);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getAdditionalCssClass()
    {
        if ($this->_helper->isVertical()) {
            return 'in-sidebar';
        }
        return '';
    }

    /**
     * @return string
     */
    public function getMenuType()
    {
        return $this->_node->getMmMenuType();
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBackgroundImage()
    {
        $url = false;
        if ($this->_node->getMmBackgroundImage()) {
            if (is_string($this->_node->getMmBackgroundImage())) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $this->_node->getMmBackgroundImage();
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * @return string|null
     */
    public function getMmWidth()
    {
        return $this->_node->getMmWidth();
    }
}
