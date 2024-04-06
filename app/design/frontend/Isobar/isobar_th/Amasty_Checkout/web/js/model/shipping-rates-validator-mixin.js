/*global define*/
define(
    [
        'jquery',
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/address-converter',
        'uiRegistry',
        'Magento_Checkout/js/action/select-shipping-address',
    ],
    function (
        $,
        wrapper,
        formPopUpState,
        addressConverter,
        uiRegistry,
        selectShippingAddress
    ) {
        'use strict';
        return function (target) {
            var mixin = {
                /**
                 * @return {*}
                 */
                postcodeValidation: function (original) {
                    original();

                    return true;
                },

                /**
                 * Fix validation for billing address
                 *
                 * @param {Function} original
                 * @param {Object} element
                 * @param {Number} delay
                 */
                bindHandler: function (original, element, delay) {
                    if (element.component.indexOf('/group') !== -1
                        || (element.name.indexOf('billing') === -1 && element.dataScope.indexOf('billing') === -1)
                    ) {
                        return original(element, delay);
                    }

                    if (element.index === 'postcode') {
                        var self = this;

                        delay = typeof delay === 'undefined' ? 1000 : delay;

                        element.on('value', function () {
                            clearTimeout(self.validateZipCodeTimeout);
                            self.validateZipCodeTimeout = setTimeout(function () {
                                self.postcodeValidation(element);
                            }, delay);
                            if (!formPopUpState.isVisible()) {
                                clearTimeout(self.validateAddressTimeout);
                                self.validateAddressTimeout = setTimeout(function () {
                                    self.validateFields();
                                }, delay);
                            }
                        });
                    }
                },

                /**
                 * Convert form data to quote address and validate fields for shipping rates
                 */
                validateFields: function () {
                    var addressFlat = addressConverter.formDataProviderToFlatData(
                            this.collectObservedData(),
                            'shippingAddress'
                        );

                    if (this.validateAddressData(addressFlat)) {
                        addressFlat = uiRegistry.get('checkoutProvider').shippingAddress;
                        addressConverter.formAddressDataToQuoteAddress(addressFlat);
                    }
                },
            };

            wrapper._extend(target, mixin);
            return target;
        };
    }
);
