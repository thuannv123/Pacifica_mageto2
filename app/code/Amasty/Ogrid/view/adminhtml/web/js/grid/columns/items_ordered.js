define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'uiCollection',
    'ko',
    'uiRegistry'
], function (_, utils, layout, Collection, ko, registry) {
    'use strict';

    return Collection.extend({
        defaults: {
            headerTmpl: 'ui/grid/columns/text',
            bodyTmpl: 'Amasty_Ogrid/ui/grid/cells/items_ordered',
            disableAction: true,
            controlVisibility: true,
            sortable: true,
            sorting: false,
            visible: true,
            draggable: true,
            listingFiltersPath: 'sales_order_grid.sales_order_grid.listing_top.listing_filters',
            columns: {
                base: {
                    parent: '${ $.name }',
                    component: 'Magento_Ui/js/grid/columns/column',
                    bodyTmpl: 'Amasty_Ogrid/ui/grid/cells/text',
                    headerTmpl: 'Amasty_Ogrid/ui/grid/columns/text',
                    filter: 'text',
                    options: [],
                    defaults: {
                        draggable: false,
                        sortable: false
                    },
                    initObservable: function () {
                        this._super()
                            .track([
                                'visible',
                                'sorting',
                                'disableAction',
                                'subVisible',
                                'label'
                            ])
                            .observe([
                                'dragging'
                            ]);

                        return this;
                    }
                },
                thumbnail: {
                    component: 'Magento_Ui/js/grid/columns/thumbnail',
                    bodyTmpl: 'Amasty_Ogrid/ui/grid/cells/thumbnail',
                    has_preview: true
                },
                weee: {
                    component: 'Amasty_Ogrid/js/grid/columns/weee',
                    bodyTmpl: 'Amasty_Ogrid/ui/grid/cells/weee'
                }
            },
            imports: {
                productCols: '${ $.columnsControlsProvider }:productCols'
            },
            listens: {
                productCols: 'updateProductCols',
                elems: 'updateFilters'
            }
        },
        initElement: function (el) {
            el.track(['label', 'subVisible']);
        },
        initialize: function () {
            this._super();

            registry.async(this.listingFiltersPath)(function (listingFilters) {
                this.listingFilters = listingFilters;
                this.updateFilters();
            }.bind(this));

            return this;
        },
        updateFilters: function () {
            if (this.listingFilters) {
                _.each(this.elems(), function (column) {
                    if (column.filter) {
                        column.visible = column.subVisible;
                        column.label = column.amogrid_label();
                        this.listingFilters.addFilter(column);
                    }
                }.bind(this));
            }
        },
        updateProductCols: function () {
            _.each(this.getVisibleCols(), function (col) {
                var config = utils.extend({}, this.columns.base, {
                    name: col.index,
                    subVisible: col.visible,
                    visible: col.visible,
                    amogrid_label: ko.observable(col.amogrid_label),
                    filter: col.filter,
                    options: col.options
                }),
                    component;

                if (col.productAttribute) {
                    if (col.frontendInput == 'media_image') {
                        config = utils.extend({}, config, this.columns.thumbnail);
                    }
                    if (col.frontendInput == 'weee') {
                        config = utils.extend({}, config, this.columns.weee);
                    }
                }

                component = utils.template(config, {});

                layout([component]);
            }.bind(this));

            _.each(this.elems(), function (elem) {
                _.each(this.productCols, function (col) {
                    if (elem.index === col.index) {
                        elem.visible = col.visible;
                        elem.subVisible = col.visible;

                        if (ko.isObservable(elem.amogrid_label)) {
                            elem.amogrid_label(col.amogrid_label);
                        }
                    }
                });
            }.bind(this));
        },

        initObservable: function () {
            this._super()
                .track([
                    'visible',
                    'sorting',
                    'disableAction',
                    'productCols'
                ])
                .observe([
                    'dragging'
                ]);

            return this;
        },
        initFieldClass: function () {
            _.extend(this.fieldClass, {
                _dragging: this.dragging
            });

            return this;
        },
        getVisibleCols: function () {
            return _.filter(this.productCols, function (el) {
                return el.visible === true;
            });
        },
        getColumns: function () {
            return this.elems.filter('subVisible');
        },
        getItems: function (record) {
            var rows = [],
                orderData = record[this.index];

            return _.map(orderData);
        },
        getFieldClass: function () {},
        getHeader: function () {
            return this.headerTmpl;
        },
        getBody: function () {
            return this.bodyTmpl;
        },
        sort: function (enable) {},
        getFieldHandler: function () {}
    });
});
