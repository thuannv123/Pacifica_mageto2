define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/payment/default'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Isobar_BankTransferProcess/payment/banktransfer'
        },

        /**
         * Get value of instruction field.
         * @returns {String}
         */
        getDescriptions: function () {
            return window.checkoutConfig.payment.descriptions[this.item.method];
        }
    });
});
