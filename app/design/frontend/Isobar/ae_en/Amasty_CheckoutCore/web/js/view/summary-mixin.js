define([
    'Amasty_CheckoutCore/js/view/utils',
    'Magento_Checkout/js/model/quote'
], function (viewUtils, quote) {
    'use strict';

    return function (Component) {
        return Component.extend({
            getNameSummary: function () {
                return viewUtils.getBlockTitle('summary');
            },
            getItems: function() {
                return JSON.parse(window.localStorage.getItem('mage-cache-storage')).cart.summary_count;
            }
        });
    };
});
