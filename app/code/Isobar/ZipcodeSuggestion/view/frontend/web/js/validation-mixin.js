define([
    'jquery'
], function($) {
    return function(validator) {
        validator.addRule(
            'post-code',
            function (value) {
                var rexRule = /(^\d{5}$)/;

                return !(rexRule.test(value) === false && $('select[name="country_id"]').val() === 'TH');
            },
            $.mage.__('Zip code is invalid.')
        );

        return validator;
    };
});
