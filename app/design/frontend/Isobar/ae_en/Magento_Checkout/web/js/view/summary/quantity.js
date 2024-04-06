/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'jquery'
], function (Component, quote, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/quantity'
        },

        /**
         * Get pure value.
         *
         * @return {*}
         */
        getPureValue: function () {
            var totals = quote.getTotals()();

            if (totals) {
                return totals.subtotal;
            }
            
            return quote.subtotal;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            var items = quote.getItems();
            var totalQuantity = 0;

            items.forEach(function (item) {
                totalQuantity += parseFloat(item.qty);
            });

            return totalQuantity;
        },

        updateQuantity: function (data, event) {
            var itemId = data.item_id;
            var newQty = $(event.target).val();
            $.ajax({
                url: '/checkout/cart/updatePost',
                type: 'post',
                data: {
                    item_id: itemId,
                    item_qty: newQty
                },
                success: function (response) {
                    if (response.success) {
                        quote.setTotals(response.totals);
                    }
                }
            });
        }
    });
});
