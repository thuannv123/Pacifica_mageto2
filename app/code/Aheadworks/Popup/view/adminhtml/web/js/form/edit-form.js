define([
    'jquery',
    'mage/mage',
    'mage/backend/form',
    'mage/backend/validation'
], function($, config) {

    $('#edit_form').mage('form', {
        handlersData: {
            saveAndNew: {
                action: {
                    args: {back: "new"}
                }
            }
        }
    }).mage('validation', {
        validationUrl: config.validationUrl,
        highlight: function(element) {
            var detailsElement = $(element).closest('details');
            if (detailsElement.length && detailsElement.is('.details')) {
                var summaryElement = detailsElement.find('summary');
                if (summaryElement.length && summaryElement.attr('aria-expanded') === "false") {
                    summaryElement.trigger('click');
                }
            }
            $(element).trigger('highlight.validate');
        }
    });
});
