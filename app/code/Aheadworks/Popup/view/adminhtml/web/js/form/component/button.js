define([
    'jquery',
    'button'
], function ($, button) {
    'use strict';

    $.widget('mage.awPopupButton', button, {
        _click: function () {
            var form = $('#edit_form');
            if(form.validation('isValid') !== false){
                $('body').trigger('processStart');
                this._super();
            }
        }
    });

    return $.mage.awPopupButton;
});
