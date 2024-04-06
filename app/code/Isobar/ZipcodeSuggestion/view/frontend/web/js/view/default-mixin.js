define([
        'jquery',
        'mage/url',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Ui/js/modal/confirm',
        'Magento_Checkout/js/model/quote',
    ],
    function ($, url, selectShippingAddressAction, checkoutData, confirmation, quote) {
        'use strict';

        var mixin = {

            /** Set selected customer shipping address  */
            selectAddress: function () {
                if (this.address._latestValue.regionId === undefined) {
                    var customerId = this.address._latestValue.customerAddressId;
                    var shippingAddress = quote.shippingAddress();
                    let isRegionRequire = window.checkoutConfig.isRequireRegionIds;
                    if (isRegionRequire[shippingAddress.countryId]) {
                        this.showConfirmDialog(customerId, shippingAddress);
                    }

                }
                this._super();
            },

            showConfirmDialog: function (customerId, shippingAddress) {
                return confirmation({
                    title: $.mage.__('Warning Address'),
                    content: $.mage.__('Region is required. Please edit the shipping address information after checkout'),
                    actions: {
                        confirm: function(){
                            window.location.href = url.build('customer/address/edit/id/' + customerId);
                        },
                        cancel: function(){
                            selectShippingAddressAction(shippingAddress);
                            checkoutData.setSelectedShippingAddress(shippingAddress.getKey());
                        }
                    }
                });
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    });