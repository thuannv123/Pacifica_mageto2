define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('mage.stickyHeader', {
        options: {
            stickyClass : {
                body: 'header-sticky__fix',
                header: 'fixed-header'
            }
        },

        _create: function () {
            var $body = $('body');
            
            $body.addClass(this.options.stickyClass.body);
            
            this._eventBindings();
        },

        _eventBindings: function () {
            var self = this,
                $window = $(window),
                $header = $('header.page-header');
                
            $window.scroll(function () {
                if ($window.scrollTop() > ($header.offset().top + $header.outerHeight()) 
                    && !($header.hasClass(self.options.stickyClass.header))) {
                    
                    $header.addClass(self.options.stickyClass.header);
                } else if ($window.scrollTop() === 0) {
                    $header.removeClass(self.options.stickyClass.header);
                }
            });
        },
    });

    return $.mage.stickyHeader;
});
