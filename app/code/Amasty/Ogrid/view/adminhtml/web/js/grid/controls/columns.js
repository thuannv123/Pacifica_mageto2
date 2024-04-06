define([
    'ko',
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/grid/controls/columns',
    'uiRegistry'
], function (ko, _, utils, layout, uiColumns, registry) {
    'use strict';

    return uiColumns.extend({
        defaults: {
            selectedTab: 'general',
            template: 'Amasty_Ogrid/ui/grid/controls/columns',
            _tabs: [],
            _productCols: [],
            imports: {
                addTabs: '${ $.name }:tabsData',
                addProductColsData: '${ $.name }:productColsData',
                addDefaultColumnsData: '${ $.columnsProvider }:elems'
            },
            clientConfig: {
                component: 'Magento_Ui/js/grid/editing/client',
                name: '${ $.name }_client'
            },
            listens: {
                '${ $.storageConfig.provider }:activeView': 'activeView'
            },
            modules: {
                client: '${ $.clientConfig.name }',
                source: '${ $.provider }'
            }
        },

        activeView: function (view) {
            _.each(this.productCols(), function (el) {
                if (!_.isUndefined(view.data.columns[el.index])) {
                    if (view.data.columns[el.index].amogrid_title !== undefined) {
                        el.amogrid_title = view.data.columns[el.index].amogrid_title;
                    }

                    el.visible = view.data.columns[el.index].visible;
                }
            });
            this.productCols(this.productCols());
            this.showItemsOrderedColumn();
        },

        initialize: function () {
            _.bindAll(this, 'reloadGridData');

            this._super();

            layout([this.clientConfig]);

            return this;
        },

        initObservable: function () {
            this._super()
                .track(['selectedTab'])
                .observe({
                    tabs: [],
                    productCols: []
                });

            return this;
        },

        addTabs: function (tabs) {
            _.map(tabs, function (value, key) {
                return utils.insert({
                    key: key,
                    value: value,
                    _parent: this,
                    visible: this.isVisibleTab
                }, this._tabs);
            }.bind(this));

            this._tabs = this._tabs.reverse();

            this.tabs(this._tabs);
        },

        hasSelected: function (tabKey) {
            return this.selectedTab == tabKey;
        },

        addProductColsData: function (cols) {
            _.map(cols, function (item, index) {
                item.index = index;

                return utils.insert(item, this._productCols);
            }.bind(this));

            this.productCols(this._productCols);
            this.initBookmarks(this._productCols);
        },

        addDefaultColumnsData: function (cols) {
            this.initBookmarks(cols);
        },

        initBookmarks: function (cols) {
            var initBookmarkColumns = function (columns) {
                _.each(cols, function (column) {
                    // var columns = view.data.columns;
                    if (columns[column.index] === undefined) {
                        columns[column.index] = {
                            'sorting': false,
                            'visible': column.visible,
                            'amogrid_label': column.amogrid_label
                        };
                    }
                });
            };

            registry.get(
                'sales_order_grid.sales_order_grid.listing_top.bookmarks_storage',
                function () {
                    if (this.storage().current && this.storage().current.columns) {
                        initBookmarkColumns(this.storage().current.columns);
                        _.each(this.storage().views, function (view) {
                            if (view.data && view.data.columns) {
                                initBookmarkColumns(view.data.columns);
                            }
                        });
                    }
                }.bind(this)
            );
        },

        isVisibleTab: function () {
            return this._parent.getColumns(this.key).length > 0;
        },

        getTabs: function () {
            return this.tabs.filter('visible');
        },

        getColumns: function (tab) {
            var cols = [];

            if (tab === 'product') {
                cols = this.productCols();
            } else {
                cols = this.elems.filter(function (col) {
                    var ret = false;

                    if (tab == 'unassigned' && !col.tab && col.index !== 'amasty_ogrid_items_ordered') {
                        ret = true;
                    } else if (col.tab == tab) {
                        ret = true;
                    }

                    return ret;
                });
            }

            return cols;
        },

        reloadGridData: function (data) {
            this.productCols(this.productCols());
            this.saveBookmark();

            if (data.visible === false) {
                return this;
            }

            var currentData = this.source().get('params');

            currentData.data = JSON.stringify({ 'column': data.index });
            this.client()
                .save(currentData)
                .done(this.amastyReload)
                .fail(this.onSaveError);

            return this;
        },

        saveBookmark: function () {
            this.prepareColumns();
            this.storage().saveState();
            this.showItemsOrderedColumn();
            this.storage().hasChanges = true;
        },

        amastyReload: function () {
            registry.get('index = sales_order_grid').source.reload();
        },

        prepareColumns: function () {
            var columns = this;

            this.elems.each(function (el) {
                var current = columns.storage().get('current.columns.' + el.index);

                el.label = el.amogrid_label;

                if (current) {
                    current.visible = el.visible;
                    current.amogrid_label = el.amogrid_label;
                }
            });

            this.productCols.each(function (el) {
                var current = columns.storage().get('current.columns.' + el.index);

                if (current) {
                    current.visible = el.visible;
                    current.amogrid_label = el.amogrid_label;
                }
            });

            this.productCols(this.productCols());
        },

        countVisible: function () {
            return this.elems.filter('visible').length + this.productCols.filter('visible').length;
        },

        showItemsOrderedColumn: function () {
            var visibleColumns = _.filter(this.productCols(), function (el) {
                    return el.visible === true;
                }),
                cols = this.elems.filter(function (el) {
                    return el.index == 'amasty_ogrid_items_ordered';
                });

            if (cols[0]) {
                cols[0].visible = visibleColumns.length > 0;
            }
        },
        initElement: function (el) {
            el.track(['amogrid_label', 'label']);
        }
    });
});
