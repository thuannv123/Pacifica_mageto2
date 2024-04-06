/**
 * Extend mage menu
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var mixin = {
        /**
         * @private
         */
        _create: function () {
            this._super();
            
            this._scrollPosition = 0;
        },
        
        /**
         * Toggle.
         */
        toggle: function () {
            var html = $('html'),
                body = $('body'),
                bufferDelay = 500;

            if (html.hasClass('nav-open')) {
                // close nav
                // smooth
                body.css({
                    'overflow': 'auto',
                    'height': 'auto'
                });
                // Restoring page scroll position
                html.scrollTop(this._scrollPosition);
                setTimeout(function () {
                    body.css({
                        'overflow': '',
                        'height': ''
                    });
                }, this.options.hideDelay + bufferDelay);
            } else {
                // open nav
                // save the scroll position
                this._scrollPosition = $(window).scrollTop();
                // smooth
                body.css({
                    'overflow': 'auto',
                    'height': 'auto'
                });
                setTimeout(function () {
                    body.css({
                        'overflow': '',
                        'height': ''
                    });
                }, this.options.showDelay + bufferDelay);
            }
            
            this._super();
        },
    };

    return function (targetWidget) {
        $.widget('mage.menu', targetWidget.menu, mixin);

        return {
            menu: $.mage.menu,
            navigation: $.mage.navigation
        };
    };
});
