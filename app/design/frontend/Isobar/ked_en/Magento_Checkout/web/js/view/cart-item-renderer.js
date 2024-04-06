/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'mage/url'
], function ($,Component,url) {
    'use strict';

    return Component.extend({
        /**
         * Prepare the product name value to be rendered as HTML
         *
         * @param {String} productName
         * @return {String}
         */
        getProductNameUnsanitizedHtml: function (productName) {
            // product name has already escaped on backend
            return productName;
        },

        /**
         * Prepare the given option value to be rendered as HTML
         *
         * @param {String} optionValue
         * @return {String}
         */
        getOptionValueUnsanitizedHtml: function (optionValue) {
            // option value has already escaped on backend
            return optionValue;
        },
        checkOptionColor: function(options){
            options.forEach(option => {
                if(option.option_id == '93'){
                    return false;
                }
            });
            return true;
        },
        getColor: function(productId){
            $.ajax({
                url: url.build('marvelicCore/checkout/index?id=' + productId),
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    localStorage.setItem(productId + 'product_color', data['color']);
                    $('[datacolor='+productId+']').text(data['color']);
                    return true;
                },
                error: function () {
                    return '';
                }
            });
        }
    });
});
