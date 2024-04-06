/**
 * Filter Collapse widget
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.amShopbyFilterCollapse', {
        options: {
            mode: null,
            collapseSelector: null,
            currentCategoryId: null,
            filterUniqId: null,
            filterCode: null
        },
        selectors: {
            collapseButton: '.am-collapse-icon',
            collapseItem: '.item.-is-collapsible',
            filterParent: '.amshopby-filter-parent',
            inputChecked: 'input[checked]',
            inputCat: 'input[name^="amshopby[cat]"]',
            itemsList: '.items',
            categoryDropdown: '[data-amshopby-js="category-dropdown"]',
            categoryDropdownElement: '[data-amshopby-js="category-dropdown-{id}"]',
            filterItems: '[data-amshopby-js="filter-items-{code}"]'
        },
        classes: {
            active: '-active',
            folded: '-folded'
        },
        mode: {
            folding: 'folding',
            dropdown: 'dropdown'
        },

        /**
         * inheritDoc
         *
         * @private
         */
        _create: function () {
            this._initNodes();

            if (this.isModeDropdown()) {
                this.processFilterDropdown();
            }

            this.expandCheckedItem();
            this.filterCollapseEvent();
        },

        /**
         * @private
         * @return {void}
         */
        _initNodes: function () {
            this.windowElement = $(window);
            this.documentElement = $(document);
            this.collapseSelector = $(this.options.collapseSelector + ' ' + this.selectors.collapseButton);
            this.categoryDropdownBlock = $(this.selectors.categoryDropdownElement
                .replace('{id}', this.options.filterUniqId) + ' ' + this.selectors.categoryDropdown);
        },

        /**
         * @public
         * @return {void}
         */
        expandCheckedItem: function () {
            var self = this;

            this.collapseSelector.nextAll(self.selectors.itemsList).each(function () {
                if ($(this).find(self.selectors.inputChecked).length === 0) {
                    if (self.isModeFolding() && self.isInputChecked($(this))) {
                        return true;
                    }

                    $(this).hide();
                } else {
                    $(this).prevAll(self.selectors.collapseButton).toggleClass(self.classes.active);
                    $(this).parents(self.selectors.collapseItem).removeClass(self.classes.folded);
                }
            });
        },

        /**
         * @public
         * @return {void}
         */
        filterCollapseEvent: function () {
            var self = this;

            this.collapseSelector.click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                var itemsList = $(this).nextAll(self.selectors.itemsList);
                itemsList.toggle();

                $(this).toggleClass(self.classes.active);
                $(this).parents(self.selectors.collapseItem).toggleClass(self.classes.folded);

                // Toggle the visibility of the ul items based on their current state
                if (itemsList.is(':visible')) {
                    itemsList.find('ul.items').show();
                } else {
                    itemsList.find('ul.items').hide();
                }
            });
        },

        /**
         * @public
         * @return {void}
         */
        processFilterDropdown: function () {
            var self = this;

            if (this.options.currentCategoryId) {
                this.windowElement.on('load', function () {
                    self.documentElement.trigger('baseCategory', self.options.currentCategoryId);
                });
                this.documentElement.ajaxComplete(function () {
                    self.documentElement.trigger('baseCategory', self.options.currentCategoryId);
                });
            }

            this.categoryDropdownBlock.click(function () {
                $(this).parent().toggleClass(self.classes.active);
                $(this).parent().find(self.selectors.filterItems.replace('{code}', self.options.filterCode)).toggle();
            });
        },

        /**
         * @public
         * @return {Boolean}
         */
        isModeFolding: function () {
            return this.options.mode === this.mode.folding;
        },

        /**
         * @public
         * @return {Boolean}
         */
        isModeDropdown: function () {
            return this.options.mode === this.mode.dropdown;
        },

        /**
         * @public
         * @param context
         * @return {Boolean}
         */
        isInputChecked: function (context) {
            if (this.isModeFolding()) {
                return context
                    .siblings(this.selectors.filterParent)
                    .find(this.selectors.inputCat)
                    .first()
                    .prop('checked');
            }

            if (this.isModeDropdown()) {
                return context.find(this.selectors.inputChecked).length === 0;
            }
        }
    });

    return $.mage.amShopbyFilterCollapse;
});
