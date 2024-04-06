define(['jquery'], function ($) {
    'use strict';

    var swatchRendererMixin = {

        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $colorName;

            this._super($this, $widget);

            if (!($this.hasClass('color') || $this.hasClass('image'))) return;

            $colorName = $this.closest('.product-item-details').find('.product-item-colorname');
            if ($colorName.length === 0) return;

            if ($this.hasClass('selected')) {
                // select
                $colorName.text($this.attr('aria-label'));
            } else {
                // unselected
                $colorName.text($colorName.data('default-color'));
            }
        },
    };

    return function (swatchRenderer) {
        $.widget('mage.SwatchRenderer', swatchRenderer, swatchRendererMixin);

        return $.mage.SwatchRenderer;
    };
});
