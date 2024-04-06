requirejs([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/validation',
], function($){
    'use strict';
    $.validator.addMethod(
        "phone-number",
        function(value) {
            var rexRule = /(^\d{10}$)/;

            return !(rexRule.test(value) === false);
        },
        $.mage.__("Please enter 10 symbols.")
    );
});
