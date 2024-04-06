/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/listing',
    'jquery',
    'owlcarousel'
], function (ko, _, Listing, $) {
    'use strict';

    return Listing.extend({
        defaults: {
            additionalClasses: '',
            filteredRows: {},
            limit: 5,
            listens: {
                elems: 'filterRowsFromCache',
                '${ $.provider }:data.items': 'filterRowsFromServer'
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.filteredRows = ko.observable();
            this.initProductsLimit();
            this.hideLoader();
        },

        /**
         * Initialize product limit
         * Product limit can be configured through Ui component.
         * Product limit are present in widget form
         *
         * @returns {exports}
         */
        initProductsLimit: function () {
            if (this.source['page_size']) {
                this.limit = this.source['page_size'];
            }

            return this;
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Listing} Chainable.
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        },

        /**
         * Sort and filter rows, that are already in magento storage cache
         *
         * @return void
         */
        filterRowsFromCache: function () {
            this._filterRows(this.rows);
        },

        /**
         * Sort and filter rows, that are come from backend
         *
         * @param {Object} rows
         */
        filterRowsFromServer: function (rows) {
            this._filterRows(rows);
        },

        /**
         * Filter rows by limit and sort them
         *
         * @param {Array} rows
         * @private
         */
        _filterRows: function (rows) {
            this.filteredRows(_.sortBy(rows, 'added_at').reverse().slice(0, this.limit));
        },

        /**
         * Can retrieve product url
         *
         * @param {Object} row
         * @returns {String}
         */
        getUrl: function (row) {
            return row.url;
        },

        /**
         * Get product attribute by code.
         *
         * @param {String} code
         * @return {Object}
         */
        getComponentByCode: function (code) {
            var elems = this.elems() ? this.elems() : ko.getObservable(this, 'elems'),
                component;

            component = _.filter(elems, function (elem) {
                return elem.index === code;
            }, this).pop();

            return component;
        },

        carouselInit: function () {
            var element = $('.block-viewed-products-grid .product-items');

            $(element).owlCarousel({
                stagePadding: 20,
                margin: 20,
                loop: false,
                nav: true,
                navText: [
                    "<i class='fa fa-angle-left'></i>",
                    "<i class='fa fa-angle-right'></i>"
                ],
                autoplay: false,
                autoplayHoverPause: true,
                lazyLoad: true,
                responsive: {
                    0: {
                        items: 3,
                        margin: 0,
                        stagePadding: 0
                    },
                    769: {
                        items: 4,
                        margin: 20,
                        padding: 20
                    },
                    1240: {
                        items: 4,
                        margin: 20,
                        padding: 20
                    }
                },
                onInitialized: function () {
                    updateNavVisibility();
                },
                onChanged: function () {
                    updateNavVisibility();
                }
            });

            function updateNavVisibility() {
                var $element = $(element);
                var $items = $element.find('.owl-item');
                var $prev = $element.find('.owl-prev');
                var $next = $element.find('.owl-next');

                var activeItems = $element.find('.owl-item.active');
                var firstActiveIndex = $items.index(activeItems.first());
                var lastActiveIndex = $items.index(activeItems.last());

                if ($items.length <= 4) {
                    $prev.hide();
                    $next.hide();
                } else if (firstActiveIndex === 0) {
                    $prev.hide();
                    $next.show();
                } else if (lastActiveIndex === $items.length - 1) {
                    $prev.show();
                    $next.hide();
                } else {
                    $prev.show();
                    $next.show();
                }
            }

            $('.owl-next').on('click', function () {
                setTimeout(function () {
                    updateNavVisibility();
                }, 0);
            });

            $('.owl-prev').on('click', function () {
                setTimeout(function () {
                    updateNavVisibility();
                }, 0);
            });

            $('.owl-carousel').on('initialized.owl.carousel', function () {
                updateNavVisibility();
            });
        }
    });
});
