define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/item/details',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/modal/confirm',
        'Amasty_CheckoutCore/js/action/remove-item',
        'Amasty_CheckoutCore/js/action/update-item',
        'mage/translate',
        'ko',
        'Amasty_CheckoutCore/js/options/configurable',
        'priceOptions',
        'mage/url',
        'mage/validation',
    ],
    function ($, Component, quote, confirm, removeItemAction, updateItemAction, $t, ko, configurable, priceOptions,url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Amasty_CheckoutCore/checkout/summary/item/details',
                messages: {
                    deleteItem: $t('Are you sure you would like to remove this item from the shopping bag?')
                }
            },

            /**
             * @param {Object} item
             * @return {*}
             */
            getItemConfig: function (item) {
                return this.getPropertyDataFromItem(item, 'amcheckout');
            },

            /**
             *
             * @param {Object} item
             * @param {Object} propertyName
             * @return {*}
             */
            getPropertyDataFromItem: function (item, propertyName) {
                var property,
                    itemDetails,
                    quoteItem;

                if (item.hasOwnProperty(propertyName)) {
                    property = item[propertyName];
                }

                quoteItem = this.getItemFromQuote(item);

                if (quoteItem.hasOwnProperty(propertyName)) {
                    property = quoteItem[propertyName];
                }

                if (property) {
                    this.storage().set('item_details' + item.item_id + propertyName, property);

                    return property;
                }

                itemDetails = this.storage().get('item_details' + item.item_id + propertyName);

                return itemDetails || false;
            },

            /**
             *
             * @param {Object} item
             * @return {*}
             */
            getItemFromQuote: function (item) {
                var items = quote.getItems(),
                    quoteItems = items.filter(function (quoteItem) {
                        return +quoteItem.item_id === item.item_id;
                    });

                if (quoteItems.length === 0) {
                    return false;
                }

                return quoteItems[0];
            },

            getConfigurableOptions: function (item) {
                var itemConfig = this.getItemConfig(item);

                if (itemConfig.hasOwnProperty('configurableAttributes')) {
                    return itemConfig.configurableAttributes.template;
                }

                return '';
            },

            getCustomOptions: function (item, element) {
                var itemConfig = this.getItemConfig(item),
                    template = '';

                if (itemConfig.hasOwnProperty('customOptions')) {
                    template = itemConfig.customOptions.template;
                }

                $(element).html(template).trigger('contentUpdated');
            },

            isDecimal: function (item) {
                var quoteItem = this.getItemFromQuote(item);

                return quoteItem.is_qty_decimal;
            },

            isEditingAvailable: function (item) {
                return this.isEditable(item) && !this.isNegotiableQuote();
            },

            isEditingLocked: function (item) {
                return this.isEditable(item) && this.isNegotiableQuote();
            },

            isEditable: function (item) {
                var itemConfig = this.getItemConfig(item);

                return itemConfig.isEditable;
            },

            isNegotiableQuote: function () {
                return window.checkoutConfig.isNegotiableQuote && this.backQuoteUrl();
            },

            backQuoteUrl: function () {
                return window.checkoutConfig.backQuoteUrl;
            },

            initOptions: function (item) {
                var itemConfig = this.getItemConfig(item),
                    containerSelector = '[data-role="product-attributes"][data-item-id=' + item.item_id + ']',
                    container = $(containerSelector);

                if (itemConfig.hasOwnProperty('configurableAttributes')) {
                    container.amcheckoutConfigurable({
                        spConfig: JSON.parse(itemConfig.configurableAttributes.spConfig),
                        superSelector: containerSelector + ' .super-attribute-select'
                    });
                }

                if (itemConfig.hasOwnProperty('customOptions')) {
                    container.priceOptions({
                        optionConfig: JSON.parse(itemConfig.customOptions.optionConfig)
                    });
                }

                item.form = container;
                item.isUpdated = ko.observable(false);
                item.validation = container.validation();

                container.find('input, select, textarea').change(function () {
                    item.isUpdated(true);
                });
            },

            updateItem: function (item) {
                if (item.validation.valid()) {
                    updateItemAction(item.item_id, item.form.serialize());
                }
            },

            /**
             * Automatically updates the order form if it`s changed
             *
             * @param {Object} item
             * @return {*}
             */
            updateItemAuto: function (item) {
                var isNotEmpty = true,
                    options;

                if (!Object.prototype.hasOwnProperty.call(item, 'form')) {
                    return;
                }

                options = item.form.serializeArray();
                $.each(options, function () {
                    if (this.value === '') {
                        isNotEmpty = false;
                    }
                });

                if (isNotEmpty) {
                    item.validation.valid();
                    updateItemAction(item.item_id, item.form.serialize());
                }
            },

            deleteItem: function (item) {
                confirm({
                    content: this.messages.deleteItem,
                    actions: {
                        confirm: function () {
                            removeItemAction(item.item_id);
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            },

            canShowDeleteButton: function () {
                $(".admin__field.admin__field-option").appendTo(".title-checkbox");
                return quote.getItems().length >= 1 && !this.isNegotiableQuote();
            },

            showProductStyle: function(item){
                this.getProductStyle(item.item_id);
                var style = localStorage.getItem(item.item_id + 'product_style');
                return style;
            },
            getProductStyle: function(productId){
                $.ajax({
                    url: url.build('marvelicCore/checkout/index?id=' + productId),
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        localStorage.setItem(productId + 'product_style', data['style']);
                        localStorage.setItem(productId + 'product_price', data['price']);
                        $('[datastyle='+productId+']').text(data['style']);
                        $('[datastylelabel='+productId+']').text(data['labelStype'] + ': ');
                        // return data;
                    },
                    error: function () {
                        return '';
                    }
                });
            }
        });
    }
);
