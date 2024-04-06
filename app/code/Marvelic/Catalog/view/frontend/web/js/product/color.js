define([
    'Magento_Ui/js/grid/columns/column',
    'Magento_Catalog/js/product/list/column-status-validator',
    'jquery',
    'knockout',
    'mage/url',
    'Amasty_Label/js/configurable/reload'
], function (Column, columnStatusValidator, $, ko, url,reloader) {
    'use strict';

    return Column.extend({
        defaults: {
            swatchOptions: ko.observableArray([]),
            selectedOption: ko.observable(null),
            oldFinalPrice: {},
            oldMaxPrice: {},
            oldMaxRegularPrice: {},
            oldMinimalPrice: {},
            oldRegularPrice: {},
            oldBaseImage: {},
            oldThumbnailImage: {}
        },

        /**
         * @param row
         * @returns {boolean}
         */
        hasValue: function (row) {
            return row && row['extension_attributes'] && "color" in row['extension_attributes'];
        },

        /**
         * @param row
         * @returns {*|boolean}
         */
        isAllowed: function (row) {
            this.getOldImages(row);
            return (columnStatusValidator.isValid(this.source(), 'color', 'show_attributes') && this.hasValue(row));
        },

        /**
         * @param row
         * @returns {*}
         */
        getOptionColor: function (row) {
            if (row && row['extension_attributes']) {
                const { color, option_id, option_value, option_product_id, option_thumb, option_type, option_limit } = row['extension_attributes'];

                return color.map((label, i) => ({
                    label,
                    id: option_id[i],
                    tooltipValue: option_value[i],
                    productId: option_product_id[i],
                    thumb: option_thumb[i],
                    type: option_type[i],
                    limit: option_limit,
                    index: i
                }));
            }

            return [];
        },

        getOptionLimit: function (row) {
            if (row && row['extension_attributes'] && row['extension_attributes']['option_limit']) {
                return row['extension_attributes']['option_limit'];
            }
            return 0;
        },


        swatchMore: function (row) {
            var productItemInfo = "#product-item-info-" + row.id,
                limit = row['extension_attributes']['option_limit'];
            $(productItemInfo + ' .swatch-option.color').each(function (i, e) {
                if (i >= limit) {
                    $(e).css('display', '');
                    $(productItemInfo + ' .swatch-opt .swatch-more').remove();
                }
            });
        },

        getOldImages: function (row) {
            this.getOldPrices(row);
            var oldBaseImage = $("#product-item-info-" + row.id + " .product-image-base .product-image-photo").attr('src'),
                oldThumbnailImage = $("#product-item-info-" + row.id + " .product-image-thumbnail .product-image-photo").attr('src');
            this.oldBaseImage[row.id] = oldBaseImage;
            this.oldThumbnailImage[row.id] = oldThumbnailImage;
        },

        getOldPrices: function (row) {
            const priceInfo = row.price_info.formatted_prices;
            const prices = {};
            for (var key in priceInfo) {
                if (priceInfo.hasOwnProperty(key)) {
                    var price = priceInfo[key];
                    prices[key] = this.extractPriceUsingRegex(price);
                }
            }
            this.oldFinalPrice[row.id] = prices.final_price;
            this.oldMaxPrice[row.id] = prices.max_price;
            this.oldMaxRegularPrice[row.id] = prices.max_regular_price;
            this.oldMinimalPrice[row.id] = prices.minimal_price;
            this.oldRegularPrice[row.id] = prices.regular_price;
        },

        extractPriceUsingRegex: function (price) {
            var regex = /<span class="price">(.*?)<\/span>/;
            var match = regex.exec(price);
            return match ? match[1] : '';
        },

        onClick: function (option) {
            var selectedOption = this.selectedOption(),
                productItemInfo = "#product-item-info-" + option.productId,
                oldNormalPrice = $(productItemInfo + " .price-container .minimal-price-link .price").text(),
                oldSpecialPrice = $(productItemInfo + " .special-price .price").text(),
                oldPrice = $(productItemInfo + " .old-price .price").text(),
                oldBaseImage = $(productItemInfo + " .product-image-base .product-image-photo").attr('src'),
                oldThumbnailImage = $(productItemInfo + " .product-image-thumbnail .product-image-photo").attr('src');

            if (!selectedOption || selectedOption.id !== option.id || selectedOption.productId !== option.productId) {
                this.selectedOption(option);
                this.updatePriceAndImage(
                    option,
                    oldNormalPrice,
                    oldSpecialPrice,
                    oldPrice,
                    oldBaseImage,
                    oldThumbnailImage
                );
            } else {
                this.selectedOption(null);
                this.disableUpdatePriceAndImage(
                    option,
                    this.oldFinalPrice,
                    this.oldMaxPrice,
                    this.oldMaxRegularPrice,
                    this.oldMinimalPrice,
                    this.oldRegularPrice,
                    this.oldBaseImage,
                    this.oldThumbnailImage
                );
            }
        },

        getProductUrl: function (option) {
            var selectedOption = this.selectedOption();

            $('[data-bind*="attr: {href: $row().url}"]').each(function () {
                var productUrl = $(this).attr('href');
                var color = 'color';
                var reg = new RegExp("([?&]" + color + "=)[^&]+", "");

                if (reg.test(productUrl)) {
                    productUrl = productUrl.replace(reg, "");
                }

                if (selectedOption) {
                    addParam("color", option.id);
                } else {
                    productUrl = productUrl.replace(/[?&]color=[^&]+/, "");
                }

                function addParam(name) {
                    function add(sep) {
                        if (option.id != undefined) {
                            productUrl += sep + name + "=" + option.id;
                        } else {
                            productUrl += productUrl;
                        }
                    }

                    if (productUrl.indexOf("?") === -1) {
                        add("?");
                    }
                }

                $(this).attr('href', productUrl);
            });
        },

        updatePriceAndImage: function (
            option,
            normalPrice,
            oldSpecialPrice,
            oldPrice,
            oldBaseImage,
            oldThumbnailImage
        ) {
            var productId = option.productId,
                productItemInfo = "#product-item-info-" + productId,
                productImagePhoto = $(productItemInfo + " .product-image-thumbnail .product-image-photo");

            productImagePhoto.addClass('swatch-option-loading');

            $.ajax({
                url: url.build('marvelicRecentlyViewed/product/simpleproduct'),
                type: 'POST',
                dataType: 'json',
                data: {
                    id: productId,
                    color: option.id
                },
                success: function (data) {
                    if (normalPrice && normalPrice !== data.price) {
                        $(productItemInfo + " .price-container .minimal-price-link .price").text(data.price);
                    }

                    if (oldSpecialPrice) {
                        if (data.special_price) {
                            if (oldSpecialPrice !== data.special_price) {
                                $(productItemInfo + " .special-price .price").text(data.special_price);
                            } else {
                                $(productItemInfo + " .old-price").css('display', '');
                                $(productItemInfo + " .special-price").css('display', '');
                                $(productItemInfo + " .price-box .percent-price").css('display', '');
                                $(productItemInfo + " .price-box .price-container-new-price").css('display', 'none');
                            }
                        } else if (data.price) {
                            var simplePrice =
                                '<span class="price-container-new-price">' +
                                '<span class="price-wrapper price-including-tax">' +
                                '<span class="minimal-price-link" style="margin-top: 0;">' +
                                '<span class="price">' + data.price +
                                '</span>' +
                                '</span>' +
                                '</span>' +
                                '</span>';
                            $(productItemInfo + " .special-price").css('display', 'none');
                            $(productItemInfo + " .price-box .percent-price").css('display', 'none');
                            $(productItemInfo + " .old-price").css('display', 'none');
                            if ($(productItemInfo + " .price-box .price-container-new-price").length === 0) {
                                $(productItemInfo + " .price-box").append(simplePrice);
                            } else {
                                $(productItemInfo + " .price-box .price-container-new-price").remove();
                                $(productItemInfo + " .price-box").append(simplePrice);
                                // $(productItemInfo + " .price-box .price-container-new-price").css('display', '');
                            }
                        }
                    } else {
                        if ($(productItemInfo + " .special-price .price").length == 0) {
                            if (data.special_price) {
                                var specialPrice =
                                    '<span class="special-price new-price">' +
                                    '<span class="price-container tax weee">' +
                                    '<span class="price-wrapper price-including-tax" style="margin-top: 5.5px;">' +
                                    '<span class="price">' + data.special_price +
                                    '</span>' +
                                    '</span>' +
                                    '</span>' +
                                    '</span>';
                                if ($(productItemInfo + " .price-box .special-price.new-price").length === 0) {
                                    $(productItemInfo + " .price-box").prepend(specialPrice);
                                    $(productItemInfo + " .price-box .minimal-price-link").css({
                                        'margin-left': '15px'
                                    });
                                }
                            }
                        }
                    }

                    if (oldBaseImage && oldBaseImage !== data.base_image) {
                        $(productItemInfo + " .product-image-base .product-image-photo").attr('src', data.base_image);
                    }

                    if (oldThumbnailImage && oldThumbnailImage !== data.thumbnail_image) {
                        $(productItemInfo + " .product-image-thumbnail .product-image-photo").attr('src', data.thumbnail_image);
                    }
                    // update label
                    var imageContainer = $(productItemInfo + " .product-image-container"),
                        inProductList =(imageContainer.closest('li.product-item').length) ? true : false;
                    
                    if(inProductList){
                        imageContainer.find('.amlabel-position-wrapper').remove();
                        var clonedElements = imageContainer.slice();
                        clonedElements.splice(-1, 1);
                        imageContainer = clonedElements;
                    }

                    reloader.reload(imageContainer,data.productIdChild,'amasty_label/ajax/label',inProductList);
                    productImagePhoto.removeClass('swatch-option-loading');
                }
            });
        },

        disableUpdatePriceAndImage: function (
            option,
            oldFinalPrice,
            oldMaxPrice,
            oldMaxRegularPrice,
            oldMinimalPrice,
            oldRegularPrice,
            oldBaseImage,
            oldThumbnailImage
        ) {
            var productItemInfo = "#product-item-info-" + option.productId;
            $(productItemInfo + " .product-image-thumbnail .product-image-photo").addClass('swatch-option-loading');
            setTimeout(() => {
                if ($(productItemInfo + " .price-container .minimal-price-link .price").length > 0) {
                    $(productItemInfo + " .price-container .minimal-price-link .price").text(oldFinalPrice[option.productId]);
                }
                if ($(productItemInfo + " .special-price .price").length > 0) {
                    if ($(productItemInfo + " .price-box .price-container-new-price").length > 0) {
                        $(productItemInfo + " .special-price .price").text(oldFinalPrice[option.productId]);
                        $(productItemInfo + " .price-box .price-container-new-price").remove();
                        $(productItemInfo + " .old-price").css('display', '');
                        $(productItemInfo + " .special-price").css('display', '');
                        $(productItemInfo + " .price-box .percent-price").css('display', '');
                    }
                }
                if ($(productItemInfo + " .special-price.new-price").length > 0) {
                    $(productItemInfo + " .special-price.new-price").remove();
                    $(productItemInfo + " .price-box .minimal-price-link").css({
                        'margin-left': '0'
                    });
                }
                $(productItemInfo + " .product-image-base .product-image-photo").attr('src', oldBaseImage[option.productId]);
                $(productItemInfo + " .product-image-thumbnail .product-image-photo").attr('src', oldThumbnailImage[option.productId]);

                // update label
                var imageContainer = $(productItemInfo + " .product-image-container"),
                inProductList =(imageContainer.closest('li.product-item').length) ? true : false;
            
                if(inProductList){
                    imageContainer.find('.amlabel-position-wrapper').remove();
                    var clonedElements = imageContainer.slice();
                    clonedElements.splice(-1, 1);
                    imageContainer = clonedElements;
                }

                reloader.reload(imageContainer,option.productId,'amasty_label/ajax/label',inProductList);

                $(productItemInfo + " .product-image-photo").removeClass('swatch-option-loading');
            }, 100);
        }
    });
});