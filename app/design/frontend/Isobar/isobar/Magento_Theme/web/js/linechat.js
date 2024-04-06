define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    var buttonLine = $('.btn-line'),
        contentModal=$('#modal'),
        buttonClose = $('.btn-close');

    jQuery(buttonLine).click(function() {
        jQuery(contentModal).toggleClass('hidden-line');
        if(jQuery(contentModal).hasClass('hidden-line')){
            jQuery(this).removeClass('hidden-button');
        }else {
            jQuery(this).addClass('hidden-button');
        }
    });

    jQuery(buttonClose).click(function() {
        jQuery(contentModal).addClass('hidden-line');
        jQuery(buttonLine).removeClass('hidden-button');
    });

});
