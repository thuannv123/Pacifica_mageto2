define([
    'jquery',
    'Amasty_Label/js/configurable/reload',
    'Amasty_Label/js/initLabel'
], function ($, reloader) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _loadMedia: function () {
                if (!window.isAmSwatchClickTriggered) {
                    this.loadLabels();
                } else {
                    window.isAmSwatchClickTriggered = false;
                }

                return this._super();
            },

            _LoadProductMedia: function () {
                this.loadLabels();

                return this._super();
            },

            loadLabels: function () {
                var productIds = this._CalcProducts(),
                    imageContainer = null,
                    inProductList = this.element.closest('li.item'),
                    in_product_list = (inProductList.length >= 1) ? true: false;
                
                if (in_product_list) {
                    imageContainer = this.element.closest('li.item')
                        .find(this.options.jsonConfig['label_category']);
                } else {
                    imageContainer = this.element.closest('.column.main')
                        .find(this.options.jsonConfig['label_product']);
                }

                if (productIds.length === 0) {
                    productIds.push(this.options.jsonConfig['original_product_id']);
                }

                if (typeof this.options.jsonConfig['label_reload'] != 'undefined') {
                    !this.inProductList ?? imageContainer.find('.amlabel-position-wrapper').remove();

                    var $productSale = this._getAllowedProductWithMinPrice(this._CalcProducts());
                    if(!($.isEmptyObject($productSale))){
                        productIds = [$productSale];
                    }
                    if(in_product_list){
                        var clonedElements = imageContainer.slice();
                        clonedElements.splice(-1, 1);

                        imageContainer = clonedElements;
                    }else{
                        in_product_list = this.inProductList;
                    }


                    reloader.reload(
                        imageContainer,
                        productIds[0],
                        this.options.jsonConfig['label_reload'],
                        in_product_list ? 1 : 0
                    );

                }
            }
        });

        return $.mage.SwatchRenderer;
    };
});
