define(
    [
        'jquery',
        'underscore',
        'uiComponent',
        'ko',
        'uiRegistry',
        'consoleLogger',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Amasty_CheckoutCore/js/action/is-equal-ignore-functions',
        'Amasty_CheckoutCore/js/model/one-step-layout',
        'Amasty_CheckoutCore/js/model/payment-validators/shipping-validator',
        'Amasty_CheckoutCore/js/model/address-form-state',
        'Amasty_CheckoutCore/js/model/statistic',
        'Amasty_CheckoutCore/js/model/shipping-registry',
        'Amasty_CheckoutCore/js/action/recollect-shipping-rates',
        'Amasty_CheckoutCore/js/model/payment/salesrule-observer',
    ],
    function (
        $,
        _,
        Component,
        ko,
        registry,
        consoleLogger,
        customer,
        selectBillingAddress,
        quote,
        paymentValidatorRegistry,
        paymentMethodConverter,
        paymentService,
        checkoutDataResolver,
        isEqualIgnoreFunctions,
        oneStepLayout,
        shippingValidator,
        addressFormState,
        statistic,
        shippingRegistry,
        recollectRates,
        salesRuleObserver
    ) {
        'use strict';
        setTimeout(() => {
            $(document).ready(function () {
                $(window).scroll(function () {
                    if ($(window).scrollTop()) {
                        $("header").removeClass("fixed-header");
                    }
                });
                window.addEventListener("click", function(event) {
                    var targetCheckbox = $('.amasty-gdpr-consent').find('input[type="checkbox"]'),
                    placeOrderBtn = $('.action.primary.checkout');
                    if(targetCheckbox.is(":checked")){
                        if(placeOrderBtn.length > 1){
                            if(check_input_shipping()){
                                placeOrderBtn.removeClass('btn-fail');
                                placeOrderBtn.removeClass('btn-active');
                                placeOrderBtn.removeClass('btn-active-no-accept');
                                if(placeOrderBtn.hasClass('disabled')){
                                    placeOrderBtn.removeClass('btn-active');
                                }else{
                                    placeOrderBtn.addClass('btn-active');
                                }
                            }else{
                                if(placeOrderBtn.hasClass('disabled')){
                                    placeOrderBtn.removeClass('btn-active');
                                }
                            }
                        }
                    }else{
                        if(placeOrderBtn.length > 1){
                            if(check_input_shipping()){
                                placeOrderBtn.removeClass('btn-fail');
                                placeOrderBtn.removeClass('btn-active');
                                placeOrderBtn.addClass('btn-active-no-accept');
                            }
                        }
                    }
                });
                

            });
            function check_input_shipping (){
                // check input require
                if(!($('#opc-new-shipping-address').css('display') == 'none')){
                    var input_first_name = $("#shipping-new-address-form [name='firstname']"),
                        input_last_name = $("#shipping-new-address-form [name='firstname']"),
                        input_zip = $("#shipping-new-address-form [name='postcode']"),
                        input_address = $("#shipping-new-address-form [name='street[0]']"),
                        input_country = $("#shipping-new-address-form [name='country_id']"),
                        input_region = $("#shipping-new-address-form [name='region_id']"),
                        input_district = $("#shipping-new-address-form [name='city']"),
                        input_phone = $("#shipping-new-address-form [name='telephone']"),
                        input_subdistrict = $("#shipping-new-address-form [name='custom_attributes[subdistrict]']"),
                        i = 0
                
                    var arr_input_shipping = [
                        input_first_name.val(),
                        input_last_name.val(),
                        input_zip.val(),
                        input_address.val(),
                        input_country.val(),
                        input_region.val(),
                        input_district.val(),
                        input_phone.val(),
                        input_subdistrict.val()
                    ]
                    $.each(arr_input_shipping,function(index,value){
                        if(value == null || value == '' || value == 'undefined' || value == 'false'){
                            i++;
                        }
                    });
                    return (i>0) ? false: true;
                }else{
                    var billingSameAsShip = $(".checkout-billing-address [name='billing-address-same-as-shipping']");
                    if(billingSameAsShip.is(":checked")){
                        return true;
                    }else{
                        var billingSameAsShipBtn = $('.checkout-billing-address').find('.action-edit-address');
                        var billing_address_form = $('.checkout-billing-address').find('fieldset');
                        if(billing_address_form.css('display') == 'none'){
                            if(billingSameAsShipBtn.length > 0){
                                return true; 
                            }else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    }
                }
               
            }
        }, 1000);
        return Component.extend({
            /** @inheritdoc */
            initialize: function () {
                this._super();

                oneStepLayout.checkoutDesign = window.checkoutDesign;
                oneStepLayout.checkoutLayout = !quote.isVirtual() ? window.checkoutLayout : '2columns';
                oneStepLayout.mainAdditionalClasses = this.additionalClasses;
                oneStepLayout.setContainerClassNames();

                this.initCheckoutLayout();
                this.replaceEqualityComparer();
                this.initChangePlaceButtonText();

                statistic.initialize();
            },

            initObservable: function () {
                var addressComponentPromise;

                this._super().observe({
                    isAmazonLoggedIn: null
                });

                

                if (!quote.isVirtual()) {
                    quote.shippingAddress.subscribe(this.shippingAddressObserver.bind(this));
                    paymentValidatorRegistry.registerValidator(shippingValidator);
                }
                shippingRegistry.setInitialValues();
                addressComponentPromise = registry.promise('checkout.steps.shipping-step.shippingAddress');

                registry.get('checkout.steps.billing-step.payment', function (component) {
                    if (addressComponentPromise.state() !== 'pending') {
                        this.initializePaymentStep(component);
                        return;
                    }
                    addressComponentPromise.done(this.initializePaymentStep.bind(this, component));

                }.bind(this));

                registry.get('checkout.sidebar.summary_additional.discount', function (couponView) {
                    try {
                        //recollect shipping rates on apply/cancel coupon code
                        couponView.isApplied.subscribe(recollectRates);
                    } catch (e) {
                        consoleLogger.error(
                            'Coupon field failed. Cannot subscribe on isApplied for recollect shipping rates.'
                        );
                    }
                });

                return this;
            },

            /**
             * Set initial payment information.
             * payment information should be setted after shipping address.
             * @param {Collection} component
             */
            initializePaymentStep: function (component) {
                if (_.isNull(quote.guestEmail) && !customer.isLoggedIn()) {
                    quote.guestEmail = '';
                }

                quote.setTotals(window.checkoutConfig.quoteData.initPayment.totals);

                paymentService.setPaymentMethods(
                    paymentMethodConverter(window.checkoutConfig.quoteData.initPayment.payment_methods)
                );
                // subscribes to payment method
                salesRuleObserver.initialize();
                component.isVisible(true);
            },

            /**
             * Init checkout layout by quote type
             * @returns {void}
             */
            initCheckoutLayout: function () {
                if (!quote.isVirtual()) {
                    oneStepLayout.selectedLayout = window.checkoutConfig.checkoutBlocksConfig;
                } else {
                    oneStepLayout.selectedLayout = oneStepLayout.getVirtualLayout();
                }
            },

            /**
             * [ Used it template ]
             * Getting oneStepLayout model in view
             * @returns {Object}
             */
            getOneStepModel: function () {
                return oneStepLayout;
            },

            shippingAddressObserver: function (address) {

                if(!this.check_input()){
                    $('.action.primary.checkout').removeClass('btn-fail');
                    $('.action.primary.checkout').removeClass('btn-active');
                    $('.action.primary.checkout').addClass('btn-fail');
                }else{
                    $('.action.primary.checkout').removeClass('btn-fail');
                    $('.action.primary.checkout').removeClass('btn-active');
                    $('.action.primary.checkout').addClass('btn-active');
                }

                if (!address) {
                    return;
                }

                this.isAccountLoggedInAmazon();

                this.setShippingToBilling(address);
            },

            /**
             * fix default "My billing and shipping address are the same" checkbox behaviour
             *
             * @param {object|null} address
             * @returns {void}
             */
            setShippingToBilling: function (address) {

                if(!this.check_input()){
                    $('.action.primary.checkout').removeClass('btn-fail');
                    $('.action.primary.checkout').removeClass('btn-active');
                    $('.action.primary.checkout').addClass('btn-fail');
                }else{
                    $('.action.primary.checkout').removeClass('btn-fail');
                    $('.action.primary.checkout').removeClass('btn-active');
                    $('.action.primary.checkout').addClass('btn-active');
                }

                if (!address) {
                    return;
                }

                if (!address.canUseForBilling()) {
                    checkoutDataResolver.resolveBillingAddress();

                    return;
                }

                if (_.isNull(address.street) || _.isUndefined(address.street)) {
                    // fix: some payments (paypal) takes street.0 without checking
                    address.street = [];
                }

                if (!addressFormState.isFormRendered()) {
                    addressFormState.isFormRendered.subscribe(this.setShippingToBilling.bind(this, address));

                    return;
                }

                if (addressFormState.isBillingSameAsShipping()) {
                    selectBillingAddress(address);
                }
                
            },


            check_input: function(){
                // check input require
                var input_first_name = $("#shipping-new-address-form [name='firstname']"),
                input_last_name = $("#shipping-new-address-form [name='firstname']"),
                input_zip = $("#shipping-new-address-form [name='postcode']"),
                input_address = $("#shipping-new-address-form [name='street[0]']"),
                input_country = $("#shipping-new-address-form [name='country_id']"),
                input_region = $("#shipping-new-address-form [name='region_id']"),
                input_district = $("#shipping-new-address-form [name='city']"),
                input_phone = $("#shipping-new-address-form [name='telephone']"),
                input_subdistrict = $("#shipping-new-address-form [name='custom_attributes[subdistrict]']"),
                i = 0,
                input_accept = $(".amcheckout-onepage-gdpr-container input[type='checkbox']");
            
                var arr_input_shipping = [
                    input_first_name.val(),
                    input_last_name.val(),
                    input_zip.val(),
                    input_address.val(),
                    input_country.val(),
                    input_region.val(),
                    input_district.val(),
                    input_phone.val(),
                    input_subdistrict.val(),
                    input_accept.is(':checked')
                ]
                $.each(arr_input_shipping,function(index,value){
                    if(value == null || value == '' || value == 'undefined' || value == 'false'){
                        i++;
                    }
                });
                return (i>0) ? false: true;
            },

            /**
             * Set customer Amazon logged in status and hide billing address if customer logged in Amazon
             * @returns {void}
             */
            isAccountLoggedInAmazon: function () {
                if (require.defined('Amazon_Payment/js/model/storage')) {
                    if (this.isAmazonLoggedIn()) {
                        $('.checkout-billing-address').hide();
                    } else {
                        require([ 'Amazon_Payment/js/model/storage' ], function (amazonStorage) {
                            amazonStorage.isAmazonAccountLoggedIn.subscribe(function (isLoggedIn) {
                                this.isAmazonLoggedIn(isLoggedIn);
                            }, this);
                            this.isAmazonLoggedIn(amazonStorage.isAmazonAccountLoggedIn());
                        }.bind(this));
                    }
                }
            },

            /**
             * Main observables equality comparer replacement
             * @returns {void}
             */
            replaceEqualityComparer: function () {
                quote.shippingAddress.equalityComparer = isEqualIgnoreFunctions;
                quote.billingAddress.equalityComparer = isEqualIgnoreFunctions;
                quote.shippingMethod.equalityComparer = isEqualIgnoreFunctions;
                quote.paymentMethod.equalityComparer = isEqualIgnoreFunctions;
            },

            initChangePlaceButtonText: function () {
                var placeOrderButtonSelector = '.payment-method .action.primary.checkout';

                if (!this.customPlaceButtonText) {
                    return;
                }

                $.async(placeOrderButtonSelector, function (element) {
                    $(element).attr('title', this.customPlaceButtonText);
                    $(element).attr('aria-label', this.customPlaceButtonText);
                    $(element).text(this.customPlaceButtonText);
                }.bind(this));
            }
        });
    }
);
