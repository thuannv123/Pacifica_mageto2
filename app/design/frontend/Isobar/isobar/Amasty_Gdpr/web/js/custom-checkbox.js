define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    $('.list-checkbox-amasty-gdpr .amgdpr-checkbox').each(function(e) {
        $(this).find('.amgdpr-checkbox').click(function(){
           $(this).parent().find('.amgdpr-label').toggleClass('highlight');
        });
    });

});
