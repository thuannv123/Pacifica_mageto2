<?php
namespace Isobar\Megamenu\Block\Adminhtml;

class DefaultColor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $html .= '<script type="text/javascript">
            require(["jquery"], function ($) {
                $(document).ready(function () {
                    var input = $("#megamenu_color_setting_color_scheme_template"),
                        AmColorTemplateConfig = {
                        "orange_peel":{
                            "menu_background_color":"#ff9600",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#de8704",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#de8704"
                        },
                        "ecru":{
                            "menu_background_color":"#BFAF80",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#A79254",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#A79254"
                        },
                        "feijoa":{
                            "menu_background_color":"#abd07e",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#84b943",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#84b943"
                        },
                        "jambalaya":{
                            "menu_background_color":"#F7F7F7",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#684F39",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#684F39"
                        },
                        "prussian_blue":{
                            "menu_background_color":"#002c4e",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#005387",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#005387"
                        },
                        "night_rider":{
                            "menu_background_color":"#313131",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#646464",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#646464"
                        },
                        "eclipse":{
                            "menu_background_color":"#000000",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#3f3f3f",
                            "submenu_background_color":"#3f3f3f",
                            "submenu_text_color":"#ffffff",
                            "menu_item_hover_color":"#3f3f3f"
                        },
                        "pacific_blue":{
                            "menu_background_color":"#0088CC",
                            "menu_text_color":"#FCFCFC",
                            "active_background_color":"#2d7ab6",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#2d7ab6"
                        },
                        "radical_red":{
                            "menu_background_color":"#fffffff",
                            "menu_text_color":"#333333",
                            "active_background_color":"#FF4157",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#333333",
                            "menu_item_hover_color":"#FF4157"
                        },
                        "iris_blue":{
                            "menu_background_color":"#0CB4CE",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#2d93a8",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#000000",
                            "menu_item_hover_color":"#2d93a8"
                        },
                        "cinnabar":{
                            "menu_background_color":"#000000",
                            "menu_text_color":"#a8a8a8",
                            "active_background_color":"#e74847",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#e74847"
                        },
                        "atlantis":{
                            "menu_background_color":"#f7f7f7",
                            "menu_text_color":"#39342e",
                            "active_background_color":"#95c03e",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#5f5f5f",
                            "menu_item_hover_color":"#95c03e"
                        },
                        "dark_red":{
                            "menu_background_color":"#8f0100",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#530000",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#000000",
                            "menu_item_hover_color":"#530000"
                        },
                        "lima":{
                            "menu_background_color":"#fffffff",
                            "menu_text_color":"#909090",
                            "active_background_color":"#80b601",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#909090",
                            "menu_item_hover_color":"#80b601"
                        },
                        "radical_red_2":{
                            "menu_background_color":"#fd495f",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#de4155",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#000000",
                            "menu_item_hover_color":"#de4155"
                        },
                        "paris_m":{
                            "menu_background_color":"#292560",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#00adef",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#393939",
                            "menu_item_hover_color":"#00adef"
                        },
                        "clay_creek":{
                            "menu_background_color":"#958457",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#ab9d77",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#ab9d77"
                        },
                        "whisper":{
                            "menu_background_color":"#eeeeee",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#ff3366",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#ff3366"
                        },
                        "pumpkin":{
                            "menu_background_color":"#f96d10",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#e80000",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#e80000"
                        },
                        "surfie_green":{
                            "menu_background_color":"#007176",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#00abb3",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#00abb3"
                        },
                        "scarlet":{
                            "menu_background_color":"#e62e04",
                            "menu_text_color":"#ffffff",
                            "active_background_color":"#f04923",
                            "submenu_background_color":"#ffffff",
                            "submenu_text_color":"#666666",
                            "menu_item_hover_color":"#f04923"
                        },
                    };
                        
                    input.change(function() {
                        var value = $(this).val();
                        if (AmColorTemplateConfig[value]) {
                            console.log(AmColorTemplateConfig);
                            console.log(AmColorTemplateConfig[value]);
                            $.each(AmColorTemplateConfig[value], function(key, $value) {
                                var input = $("#megamenu_color_setting_" + key);
                                input.val($value);
                                input.css({"backgroundColor" : "#" + $value});
                            });
                        }
                    });
                });
            });
            
            </script>';
        return $html;
    }
}
