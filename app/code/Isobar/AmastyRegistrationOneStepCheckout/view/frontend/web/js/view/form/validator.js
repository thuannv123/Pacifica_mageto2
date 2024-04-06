define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Isobar_AmastyRegistrationOneStepCheckout/js/model/checkout/registration-validator'
], function (Component, additionalValidators, registrationValidator) {
    'use strict';
    
    additionalValidators.registerValidator(registrationValidator);
    
    return Component.extend({});
});
