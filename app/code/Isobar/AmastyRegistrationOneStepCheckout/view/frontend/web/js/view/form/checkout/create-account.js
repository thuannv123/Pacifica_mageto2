define([
    'ko',
    'uiComponent',
    'uiRegistry',
    'jquery',
    'Magento_Customer/js/validation',
    'jquery-ui-modules/datepicker',
    'mage/calendar'
], function (ko, Component, uiRegistry, $) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Isobar_AmastyRegistrationOneStepCheckout/form/checkout/create-account',
            isPasswordVisible: false,
            isCreateAccountAction: false,
            firstName: null,
            lastName: null,
            isCreateAccount: false,
            isShowPassword: false,
            createAccConfig: window.checkoutConfig.quoteData.additional_options.create_account,

            imports: {
                'setIsPasswordVisible': '${$.parentName}.customer-email:isPasswordVisible',
                'setIsCreateAccountAction': '${$.parentName}.customer-email:isCreateAccountAction',
                'setFirstName': '${ $.provider }:shippingAddress.firstname',
                'setLastName': '${ $.provider }:shippingAddress.lastname',
            }
        },

        initObservable: function () {
            this._super()
                .observe([
                    'isPasswordVisible',
                    'isCreateAccountAction',
                    'firstName',
                    'lastName',
                    'isCreateAccount',
                    'isShowPassword'
                ]);
            return this;
        },

        setIsPasswordVisible: function (value) {
            this.isPasswordVisible(value);
        },

        setIsCreateAccountAction: function (value) {
            this.isCreateAccountAction(value);
        },

        setFirstName: function (value) {
            this.firstName(value);
        },

        setLastName: function (value) {
            this.lastName(value)
        },

        getRequiredCharacterClassesNumber: function () {
            return parseInt(uiRegistry.get('checkoutProvider').requiredCharacterClassesNumber, 10);
        },

        getMinimumPasswordLength: function () {
            return parseInt(uiRegistry.get('checkoutProvider').minimumPasswordLength, 10);
        },

        onAfterRenderDob: function (element) {
            // init calendar config
            $.extend(true, $, {
                calendarConfig: {
                    "closeText":"Done",
                    "prevText":"Prev",
                    "nextText":"Next",
                    "currentText":"Today",
                    "monthNames":["January","February","March","April","May","June","July","August","September","October","November","December"],
                    "monthNamesShort":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                    "dayNames":["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
                    "dayNamesShort":["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                    "dayNamesMin":["Su","Mo","Tu","We","Th","Fr","Sa"]
                }
            });
            
            // implement dob date picker
            $(element).find('#dob').calendar({
                showsTime: false,
                dateFormat: "M/dd/y",
                yearRange: "-120y:c+nn",
                buttonText: "Select Date", maxDate: "-1d", changeMonth: true, changeYear: true, showOn: "both"
            })
        }
    });
});