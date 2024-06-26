define([
    'jquery'
], function ($) {
    'use strict';
    return function () {
        var checkBox = $('[name=agree]');

        checkBox.click(function () {
            
            var currentCheckBox = $(this);

            if (currentCheckBox.is(':checked')) {
                currentCheckBox.parentsUntil('div.block-content').find('fieldset.fieldset').removeAttr('hidden');
                currentCheckBox.parentsUntil('div.block-content').find('div.mage-error').remove();
            } else {
                currentCheckBox.parentsUntil('div.block-content').find('fieldset.fieldset').attr('hidden', true);
            }
        });
    }
});