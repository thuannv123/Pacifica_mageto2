define([
    'jquery'
], function ($) {
    'use strict';
    return function (target) {
        $.validator.addMethod(
            'between-10-720',
            function (value) {
                return value >= 10 && value <= 720;
            },
            $.mage.__('Please enter a integer between 10 to 720.')
        );

        $.validator.addMethod(
            'positive-number',
            function (value) {
                return Math.sign(value) === 1 || Number(value) === 0
            },
            $.mage.__('Please enter a positive number.')
        );


        return target;
    };
});
