define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data'
], function ($, _, customerData) {
    // Show messages error, if response have
    var messages = $.cookieStorage.get('mage-messages');
    if (!_.isEmpty(messages)) {
        customerData.set('messages', {messages: messages});
        $.cookieStorage.set('mage-messages', '');
    }

    var sections = ['cart'];
    customerData.reload(sections, true);
});