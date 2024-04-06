<?php

//TODO think about separate helper for FE and BE

namespace Isobar\Megamenu\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_PATH_ACTIVE = 'megamenu/config/megamenu_general_active';
    const CONFIG_PATH_SHOW_LEFT = 'megamenu/config/megamenu_general_show_left';
    const CONFIG_MENU_BACKGROUND_COLOR = 'megamenu/color_setting/menu_background_color';
    const CONFIG_MENU_ACTTIVE_BACKGROUND_COLOR = 'megamenu/color_setting/active_background_color';
    const CONFIG_MENU_TEXT_COLOR = 'megamenu/color_setting/menu_text_color';
    const CONFIG_MENU_SUBMENU_BACKGROUND_COLOR = 'megamenu/color_setting/submenu_background_color';
    const CONFIG_MENU_SUBMENU_BACKGROUND_IMAGE = 'megamenu/color_setting/submenu_background_image';
    const CONFIG_MENU_SUBMENU_TEXT_COLOR = 'megamenu/color_setting/submenu_text_color';
    const CONFIG_MENU_ITEM_HOVER_COLOR = 'megamenu/color_setting/menu_item_hover_color';
    const CONFIG_MENU_COLOR_THEME_ACTIVE = 'megamenu/color_setting/color_theme_active';
    const MEDIA_URL ='media/config/default/';

    const ATTRIBUTE_GROUP = 'Megamenu';

    protected $_scopeConfig;
    protected $_registry;

    protected $attributesToExclude = [
        'mm_turn_on',
        'mm_turn_column_on',
        'mm_column_number',
        'mm_turn_images_on',
        'mm_turn_products_on',
        'mm_products',
        'mm_turn_blocks_on',
        'mm_blocks',
        'mm_turn_label_on',
        'mm_template',
        'mm_label_background',
        'mm_color',
        'mm_configurator',
        'mm_icon_class',
    ];

    protected $attributesToExcludeLevelTwo = [
        'mm_image',
        'mm_turn_column_on',
        'mm_column_number',
        'mm_turn_images_on',
        'mm_turn_products_on',
        'mm_products',
        'mm_turn_blocks_on',
        'mm_blocks',
        'mm_turn_label_on',
        'mm_template',
        'mm_icon_class',
        //'mm_configurator'
    ];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_registry = $registry;
    }

    public function getConfig($path, $scope)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    public function isModuleEnabled()
    {
        return $this->getConfig(
            self::CONFIG_PATH_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function activeMobile() {
        return $this->getConfig(
            'megamenu/config/megamenu_general_active_mobile',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function activeMobileAerie() {
        return $this->getConfig(
            'megamenu/config/megamenu_general_active_mobile_logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }


    public function isVertical()
    {
        return $this->getConfig(
            self::CONFIG_PATH_SHOW_LEFT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function unserialize($string)
    {
        if ($string) {
            return explode(',', str_replace(' ', '', $string));
        }
        return [];
    }

    public function serialize($array)
    {
        return implode(',', $array);
    }

    public function excludeAttributes($attributes, $categoryLevel)
    {
        if ($categoryLevel == 2) {
            return array_diff_key($attributes, array_flip($this->attributesToExcludeLevelTwo));
        } elseif ($categoryLevel) {
            return array_diff_key($attributes, array_flip($this->attributesToExclude));
        }
        return null;
    }

    public function getAttributeGroup()
    {
        return self::ATTRIBUTE_GROUP;
    }

    //TODO check if possible to do in css
    public function hackGrid($html)
    {
        $pos = strpos($html, 'class="admin__control-support-text"');
        $html = substr_replace($html, 'style="margin-left: 0px;" ', $pos, 0);
        $pos = strpos($html, 'class="admin__data-grid-pager-wrap"');
        $html = substr_replace($html, 'style="margin-left: 0px;" ', $pos, 0);
        return $html;
    }
    public function getMenuBackgroundColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_BACKGROUND_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getMenuActiveBackgroundColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_ACTTIVE_BACKGROUND_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getMenuTextColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_TEXT_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSubmenuBackgroundColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_SUBMENU_BACKGROUND_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSubmenuBackgroundImage()
    {
        return self::MEDIA_URL . $this->getConfig(
            self::CONFIG_MENU_SUBMENU_BACKGROUND_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getSubmenuTextColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_SUBMENU_TEXT_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getItemHoverColor()
    {
        return $this->getConfig(
            self::CONFIG_MENU_ITEM_HOVER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getColorThemeActive()
    {
        return $this->getConfig(
            self::CONFIG_MENU_COLOR_THEME_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $nodeId
     * @return bool
     */
    public function isCustomNode($nodeId)
    {
        return strpos($nodeId, 'custom-node-') !== false;
    }

    public function getCategoryCurrent() {
        $category = $this->_registry->registry('current_category');
        if($category){
            $categoryLevel = $category->getLevel();
            for ($i=0; $i <= (int)$categoryLevel ; $i++) { 
                if($category->getLevel() != '2'){
                    $category = $category->getParentCategory();
                }
                else{
                    $categoryName = $category->getName();
                    return strtolower($categoryName);
                }
            }
        }
        return false;
    }
}
