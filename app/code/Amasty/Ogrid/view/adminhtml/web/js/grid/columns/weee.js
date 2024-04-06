define([
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function (Column, _) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Amasty_Ogrid/ui/grid/cells/wee'
        },

        getRows: function (parentRow) {
            return this._getColValue(parentRow, this.index, []);
        },

        getColumnValue: function (row, index) {
            return this._getColValue(row, index, '-');
        },

        _getColValue: function (row, index, defaultValue) {
            if (_.isUndefined(row[index])
                || _.isNull(row[index])
            ) {
                return defaultValue;
            }

            return row[index];
        }
    });
});
