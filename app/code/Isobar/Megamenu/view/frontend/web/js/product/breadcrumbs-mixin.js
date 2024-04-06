define(['jquery'], function ($) {
    'use strict';

    var modalWidgetMixin = {

        /**
         * Returns crumb data.
         *
         * @param {Object} menuItem
         * @return {Object}
         * @private
         */
        _getCategoryCrumb: function (menuItem) {
            return {
                'name': 'category',
                'label': menuItem.find('span.mm-title').length ? menuItem.find('span.mm-title').text() : menuItem.text(),
                'link': menuItem.attr('href'),
                'title': ''
            };
        },

        /**
         * Returns category menu item.
         *
         * Tries to resolve category from url or from referrer as fallback and
         * find menu item from navigation menu by category url.
         *
         * @return {Object|null}
         * @private
         */
        _resolveCategoryMenuItem: function () {
            var categoryUrl = this._resolveCategoryUrl(),
                menu = $(this.options.menuContainer),
                categoryMenuItem = null;

            if (categoryUrl && menu.length) {
                categoryMenuItem = menu.find(
                    this.options.categoryItemSelector +
                    ' > a[href="' + categoryUrl + '"]'
                ).first();

            }

            return categoryMenuItem;
        },
    };

    return function (targetWidget) {
        // Example how to extend a widget by mixin object
        $.widget('mage.breadcrumbs', targetWidget, modalWidgetMixin); // the widget alias should be like for the target widget

        return $.mage.breadcrumbs; //  the widget by parent alias should be returned
    };
});
