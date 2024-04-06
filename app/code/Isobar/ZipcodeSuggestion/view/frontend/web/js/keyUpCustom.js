define([
    "jquery",
    "jquery/ui",
    'mage/validation'
], function($) {
    "use strict";
    //creating jquery widget
    $.widget('key_up_custom.js', {
        _create: function() {
            this._bind();
        },

        /**
         * Event binding, will monitor change, keyup and paste events.
         * @private
         */
        _bind: function () {
            this._on(this.element, {
                'change': this.validateField,
                'keyup': this.validateField,
                'paste': this.validateField,
            });
        },

        validateField: function () {
            $.validator.validateSingleElement(this.element);
        },

    });

    return $.key_up_custom.js;
});