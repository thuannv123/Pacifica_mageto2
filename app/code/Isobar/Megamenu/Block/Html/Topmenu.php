<?php

namespace Isobar\Megamenu\Block\Html;

use Isobar\Megamenu\Helper\Data;
use Isobar\Megamenu\Model\Category\Attribute\Source\DisplayOn;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\View\Element\Template;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    const CONFIG_POSITION_TOP = 0;

    public $_helper;

    public $_layoutFactory;
    public $urlInterface;

    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Data $helper,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->_helper = $helper;
        $this->_layoutFactory = $layoutFactory;
        $this->urlInterface = $urlInterface;
    }

    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        if ($this->_helper->isModuleEnabled()) {
            return $this->renderMenu($menuTree, $childrenWrapClass, $limit, $colBrakes);
        } else {
            return parent::_getHtml($menuTree, $childrenWrapClass, $limit, $colBrakes);
        }
    }


    /**
     * Get block cache life time
     *
     * @return int
     * @since 100.1.0
     */
    protected function getCacheLifetime()
    {
        if($this->_helper->activeMobile()){
            return null;
        }
        return parent::getCacheLifetime() ?: 3600;
    }

    public function isVertical()
    {
        return $this->_helper->isVertical();
    }
    public function renderMenu(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
      
        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;
        if($this->_helper->activeMobile() && !$childLevel){
            $html = '<div class="parent_menu">';
        }else{
            $html = '';
        }

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $mmLabelBackground = '';
            $mmColor = '';
            $label = '';
            $mmIcon = '';
            $image = '';
            $categoryId = explode("-",$child->getId())[2];

            $turnOnMega = $child->getMmTurnOn();
            if ($turnOnMega) {
                $mmLabelBackground = $child->getMmLabelBackground();
                $mmColor = $child->getMmColor();
                $label = $child->getMmLabel();
                $mmIcon = $child->getMmIconClass();
                $image = $child->getMmImage();
            }
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/category/' . $image;

            if ($child->getMmDisplayOn() == DisplayOn::MM_DISPLAY_BOTH || $child->getMmDisplayOn() == null) {
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child)  .' category_id="'. $categoryId . '">';
            } else {
                if ($this->getNameInLayout() == 'catalog.topnav') {
                    if ($child->getMmDisplayOn()== DisplayOn::MM_DISPLAY_TOP ||  $this->_helper->isVertical()== self::CONFIG_POSITION_TOP) {
                        $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child)  .' category_id="'. $categoryId . '">';
                    } elseif ($child->getMmDisplayOn()== DisplayOn::MM_DISPLAY_SIDEBAR) {
                        $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child)  .' category_id="'. $categoryId . '" data-position="sidebar">';
                    }
                } else {
                    if ($child->getMmDisplayOn()== DisplayOn::MM_DISPLAY_SIDEBAR) {
                        $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child)  .' category_id="'. $categoryId . '">';
                    } elseif ($child->getMmDisplayOn()== DisplayOn::MM_DISPLAY_TOP) {
                        $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) .' category_id="'. $categoryId . '" data-position="top">';
                    }
                }
            }

            $hrefAnchor = $this->_storeManager->getStore()->getBaseUrl() . '#';
            if ($child->getUrl() == '' || $child->getUrl() == $hrefAnchor) {
                $html .= '<a ' . $outermostClassCode . '>';
            } else {
                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>';
            }
            if (!empty($label)) {
                $html .= '<strong class="mm-label" style="background-color:' . $mmLabelBackground . '; color:' . $mmColor . '">' . $label . '</strong>';
            }
            if (!empty($image)) {
                $html .= '<span class="mm-image"><img src="' . $imageUrl . '" alt=""></span>';
            }
            if (!empty($mmIcon)) {
                $html .= '<i class="' . $mmIcon . '"></i>';
            }
            $html .= '<span class="mm-title">' . $this->escapeHtml(
                $child->getName()
            );
            $html .= '</span></a>';
            if ($turnOnMega && ($childLevel == 0)) {
                $html .= $this->renderMegamenuBlock($child);
            } else {
                $html .= $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                );
            }
            $html .= '</li>';
            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }
        if($this->_helper->activeMobile() && !$childLevel){
            $html .= '</div>';
            $html .= 
            '<div class="child_menu">
                <div class="top-navigation-title-section">
                    <span class="back-toggle"></span>
                    <h3 class="title"></h3>
                </div>
                <div class="explore">
                    <a href="" class="explore_name"></a>
                </div>
                <div class="content">
                    <div class="box_content">
                        
                    </div>
                </div>
            </div>';
        }
        return $html;
    }

    public function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        if ($this->_helper->isModuleEnabled()) {
            $html = '';
            if (!$child->hasChildren()) {
                return $html;
            }

            $colStops = null;
            if ($childLevel == 0 && $limit) {
                $colStops = $this->_columnBrake($child->getChildren(), $limit);
            }

            $html .= '<ul class="level' . $childLevel . ' submenu rd-navbar-dropdown">'; //TODO check if rd-navbar-dropdown needed?
            $html .= $this->renderMenu($child, $childrenWrapClass, $limit, $colStops);
            $html .= '</ul>';

            return $html;
        } else {
            return parent::_addSubMenu($child, $childLevel, $childrenWrapClass, $limit);
        }
    }

    //TODO fix this method
    public function renderMegamenuBlock($category)
    {
        $block = $this->_layoutFactory->create()->createBlock(
            'Isobar\Megamenu\Block\Html\Topmenu\Block',
            '',
            ['data' => ['mode' => $category->getMmMenuType()]]
        );
        $category->setMenuLevel(1);
        $block->setNode($category);
        return $block->renderBlock();
    }

    public function isMobile() {
        $server     = $this->getRequest()->getServer();
        if( 
            preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$server['HTTP_USER_AGENT'])||
            preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($server['HTTP_USER_AGENT'],0,4))
        ) {
            return true;
        }
        return false;
    }

    public function getUrlCurrently() {
        return $this->urlInterface->getCurrentUrl();
    }
}
