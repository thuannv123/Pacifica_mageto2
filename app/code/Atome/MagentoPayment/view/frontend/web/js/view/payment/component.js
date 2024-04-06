/**
 * Atome Payment Module for Magento 2
 *
 * @author Atome
 * @copyright 2020 Atome
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/resource-url-manager',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/section-config'
    ],
    function ($, Component, quote, resourceUrlManager, storage, mageUrl, additionalValidators, globalMessageList, customerData, sectionConfig) {
        'use strict';


        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Atome_MagentoPayment/payment_method'
            },

            data: {
                ...window.checkoutConfig.payment.atome,
                ...{currency: checkoutConfig.quoteData.quote_currency_code}
            },
            isIndonesia() {
                return this.currency === 'IDR';
            },

            formatPrice(priceInstallment, options) {
                var thousandsSeparator = ',';

                if (options && options['precision']) {
                    priceInstallment = priceInstallment.toFixed(options['precision']);
                }

                if (['IDR', 'VND'].indexOf(this.data.currency) !== -1) {
                    thousandsSeparator = ".";
                }

                if (options && options['thousands_format']) {
                    const pieces = (priceInstallment + '').split('.');
                    priceInstallment = pieces[0].replace(/\B(?=(?:\d{3})+$)/g, thousandsSeparator);
                    if (pieces[1]) {
                        priceInstallment += '.'
                        priceInstallment += pieces[1];

                        const zeroPaddingNum = options['precision'] - pieces[1].length;
                        if (zeroPaddingNum > 0) {
                            priceInstallment += '00000'.substr(0, zeroPaddingNum);
                        }
                    }
                }

                return priceInstallment;
            },

            calculateGrandTotal: function (observe = false) {
                const self = this;
                storage.get(
                    resourceUrlManager.getUrlForCartTotals(quote), false
                ).done(function (response) {
                    const format = window.checkoutConfig.priceFormat.pattern;
                    const precision = window.checkoutConfig.priceFormat.precision;
                    let amount = response.grand_total + response.tax_amount;
                    const currency = self.data.currency;


                    const $atomeGateway = $('#atome-payment-method');
                    if ('TWD' === currency && Math.floor(amount) !== amount) {
                        $atomeGateway.find('.atome-checkout-eligible').hide();
                        $atomeGateway.find('.atome-checkout-ineligible.atome-amount-not-integer-error').show();
                        return;
                    }


                    let installmentFee = amount / 3;
                    const installmentFeeLast = amount - installmentFee.toFixed(precision) * 2;
                    const minimumSpend = self.data.min_spend;
                    const formatOptions = {'precision': precision, 'thousands_format': true};

                    if (['IDR', 'VND'].indexOf(currency) !== -1) {
                        formatOptions.precision = 0;
                        amount = Math.ceil(amount);
                        installmentFee = Math.floor(amount / 3);
                        var surplus = amount - installmentFee * 3;

                        const installmentFees = [];
                        while (surplus) {
                            installmentFees[--surplus] = installmentFee + 1;
                        }

                        $atomeGateway.find(".atome_total_amount").text(format.replace(/%s/g, self.formatPrice(amount, formatOptions)));
                        $atomeGateway.find(".atome_instalments_amount").each(function (i) {
                            $(this).text(format.replace(/%s/g, self.formatPrice(installmentFees[i] || installmentFee, formatOptions)));
                        });
                        $atomeGateway.find(".atome_instalments_amount_last").text(format.replace(/%s/g, self.formatPrice(installmentFee, formatOptions)));

                    } else {
                        $atomeGateway.find(".atome_total_amount").text(format.replace(/%s/g, amount.toFixed(precision)));
                        $atomeGateway.find(".atome_instalments_amount").text(format.replace(/%s/g, installmentFee.toFixed(precision)));
                        $atomeGateway.find(".atome_instalments_amount_last").text(format.replace(/%s/g, installmentFeeLast.toFixed(precision)));
                    }


                    if (amount >= minimumSpend) {
                        $atomeGateway.find('.atome-checkout-eligible').show();
                        $atomeGateway.find('.atome-checkout-ineligible').hide();
                    } else {
                        $atomeGateway.find('.atome-checkout-eligible').hide();
                        const $atoemMinimumSpendError = $atomeGateway.find('#atome_minimum_spend_error');
                        const formatedPrice = self.formatPrice(minimumSpend, formatOptions);
                        $atoemMinimumSpendError.text($atoemMinimumSpendError.text().replace(RegExp("\\[\\[minimum_spend\\]\\]", "g"), format.replace(/%s/g, formatedPrice)));

                        $atomeGateway.find('.atome-checkout-ineligible.atome-minimum-spend-error').show();
                    }
                }).fail(function (response) {
                    throw new Error(response);
                }).always(function (response) {
                    !observe && self.observeTotal();
                });

                return '';
            },

            observeTotal: function () {
                const totalPriceContainer = document.querySelector('tr.grand.totals span.price');
                if (!totalPriceContainer) {
                    return;
                }
                const self = this;
                const observer = new MutationObserver(function (mutationsList) {
                    if (mutationsList.length) {
                        self.calculateGrandTotal(true);
                    }
                });
                observer.observe(totalPriceContainer, {
                    subtree: true,
                    childList: true,
                    characterData: true,
                });
            },

            startAtomePayment: function () {
                if (additionalValidators.validate()) {
                    this.placeOrder();
                }
            },

            placeOrder: function (data, event) {
                const self = this;

                this.isPlaceOrderActionAllowed(false);

                $("body").trigger("processStart");
                this.getPlaceOrderDeferredObject()
                    .fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    })
                    .done(function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    })
                    .always(function () {
                        customerData.invalidate(['cart']);
                        $('body').trigger('processStop');
                    });

                return true;
            },
            afterPlaceOrder: function () {
                $("body").trigger("processStart");
                window.location.href = mageUrl.build("atome/payment/prepare");
            }
        });
    }
);
