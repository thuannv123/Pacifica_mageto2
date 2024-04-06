/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'ko',
    'mage/translate'
], function ($, Component, quote,ko,$t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amasty_CheckoutCore/onepage/subcribe-content'
        },
        getTitle: function(){
            return window.checkoutConfig.customnewsletter.title;
        },
        getContent: function(){
            return window.checkoutConfig.customnewsletter.content;
        },
        getTitleCheckbox: function(){
            return window.checkoutConfig.customnewsletter.title_checkbox;
        },
        getContentContact: function(){
            return window.checkoutConfig.customnewsletter.content_contact;
        }
    });
});
