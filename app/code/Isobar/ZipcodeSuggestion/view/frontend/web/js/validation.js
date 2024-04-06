requirejs([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/validation',
], function($){
    'use strict';
    $.validator.addMethod(
        "post-code",
        function(value) {
            var rexRule = /(^\d{5}$)/;

            return !(rexRule.test(value) === false && $('select[name="country_id"]').val() === 'TH');
        },
        $.mage.__("Zip code is invalid.")
    );
});
