define(['jquery'], function ($) {
    'use strict';

    var mixin = {
        options: {
            hideDelay: 300,
        },

        /**
         * Sets state of the search field to provided value.
         *
         * @param {Boolean} isActive
         */
        setActiveState: function (isActive) {
            var html;
            
            this._super(isActive);
            
            // close nav menu
            html = $('html');
            if (isActive) {
                if (html.hasClass('nav-open')) {
                    html.removeClass('nav-open');
                    setTimeout(function () {
                        html.removeClass('nav-before-open');
                    }, this.options.hideDelay);
                }
            }
        },
    };

    return function (targetWidget) {
        $.widget('mage.quickSearch', targetWidget, mixin);

        return $.mage.quickSearch;
    };
});