define([
    'jquery',
    'Isobar_AmastyRegistrationOneStepCheckout/js/action/save-registration'
],function ($, saveAction) {
    'use strict';

    return {
        /**
         * Validate Register Form on checkout if available
         *
         * @returns {Boolean}
         */
        validate: function () {
            var isValid, 
                $loginForm = $('#form-validate'),
                isCreateAccountChecked = $loginForm.find("input[name='is_create_account']").val() == 'true';
            
            if (isCreateAccountChecked) {
                isValid = $loginForm.validation() && $loginForm.validation('isValid');
            } else {
                isValid = true; // pass if not creating an account
            }

            if (isCreateAccountChecked && isValid) {
                // other threat
                setTimeout(function () {
                    saveAction($loginForm);
                }, 0)
            }
            
            return isValid;
        }
    };
});
