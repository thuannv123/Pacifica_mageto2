define([
    "uiComponent",
    "dataServicesBase",
    "jquery",
    "Magento_Catalog/js/price-utils",
    'owlcarousel'
], function (Component, ds, $, priceUnits) {
    "use strict"
    return Component.extend({
        defaults: {
            template:
                "Magento_ProductRecommendationsLayout/recommendations.html",
            recs: [],
        },
        initialize: function (config) {
            this._super(config)
            this.pagePlacement = config.pagePlacement
            this.placeholderUrl = config.placeholderUrl
            this.priceFormat = config.priceFormat
            this.priceUnits = priceUnits
            this.currencyConfiguration = config.currencyConfiguration
            this.alternateEnvironmentId = config.alternateEnvironmentId
            return this
        },
        /**
         * @returns {Element}
         */
        initObservable: function () {
            return this._super().observe(["recs"])
        },

        //Helper function to add addToCart button & convert currency
        /**
         *
         * @param {@} response is type Array.
         * @returns type Array.
         */
        processResponse(response) {
            const units = []
            if (!response.length || response[0].unitId === undefined) {
                return units
            }

            for (let i = 0; i < response.length; i++) {
                response[i].products = response[i].products.slice(
                    0,
                    response[i].displayNumber,
                )
                for (let j = 0; j < response[i].products.length; j++) {
                    if (response[i].products[j].productId) {
                        const form_key = $.cookie("form_key")
                        const url = this.createAddToCartUrl(
                            response[i].products[j].productId,
                        )
                        const postUenc = this.encodeUenc(url)
                        const addToCart = { form_key, url, postUenc }
                        response[i].products[j].addToCart = addToCart
                    }

                    if (response[i].products[j].productId) {
                        const form_key = $.cookie("form_key");
                        const addToCartUrl = this.createAddToCartUrl(
                            response[i].products[j].productId
                        );
                        const addToWishlistUrl = this.createAddToWishlistUrl(
                            response[i].products[j].productId
                        );
                        const postUenc = this.encodeUenc(addToCartUrl);
                        const addToCart = { form_key, url: addToCartUrl, postUenc };
                        const addToWishlist = { form_key, url: addToWishlistUrl };
                        response[i].products[j].addToCart = addToCart;
                        response[i].products[j].addToWishlist = addToWishlist;
                    }

                    if (response[i].products[j].prices && response[i].products[j].prices.minimum) {
                        const originalPrice = response[i].products[j].prices.minimum.final;
                        const finalPrice = response[i].products[j].prices.minimum.final;
                        const discountPercentage = this.calculateDiscountPercentage(originalPrice, finalPrice);
                        response[i].products[j].discountPercentage = discountPercentage;
                    }

                    if (
                        this.currencyConfiguration &&
                        response[i].products[j].currency !==
                        this.currencyConfiguration.currency
                    ) {
                        if (response[i].products[j].prices === null) {
                            response[i].products[j].prices = {
                                minimum: { final: null },
                            }
                        } else {
                            response[i].products[j].prices.minimum.final =
                                response[i].products[j].prices &&
                                    response[i].products[j].prices.minimum &&
                                    response[i].products[j].prices.minimum.final
                                    ? this.convertPrice(
                                        response[i].products[j].prices.minimum
                                            .final,
                                    )
                                    : null
                        }
                        response[i].products[j].currency =
                            this.currencyConfiguration.currency
                    }
                }
                units.push(response[i])
            }
            units.sort((a, b) => a.displayOrder - b.displayOrder)
            return units
        },

        loadJsAfterKoRender: function (self, unit) {
            const renderEvent = new CustomEvent("render", { detail: unit })
            document.dispatchEvent(renderEvent)
        },

        convertPrice: function (price) {
            return parseFloat(price * this.currencyConfiguration.rate)
        },

        createAddToCartUrl(productId) {
            const currentLocationUENC = encodeURIComponent(
                this.encodeUenc(BASE_URL),
            )
            const postUrl =
                BASE_URL +
                "checkout/cart/add/uenc/" +
                currentLocationUENC +
                "/product/" +
                productId
            return postUrl
        },

        createAddToWishlistUrl(productId) {
            const currentLocationUENC = encodeURIComponent(
                this.encodeUenc(BASE_URL),
            );
            const postUrl =
                BASE_URL +
                "wishlist/index/add/product/" +
                productId +
                "/uenc/" +
                currentLocationUENC;
            return postUrl;
        },

        calculateDiscountPercentage(originalPrice, finalPrice) {
            if (originalPrice > finalPrice) {
                const discountAmount = originalPrice - finalPrice;
                const discountPercentage = (discountAmount / originalPrice) * 100;
                return discountPercentage.toFixed(2);
            }
        },

        encodeUenc: function (value) {
            const regex = /=/gi
            return btoa(value).replace(regex, ",")
        },

        carouselInit: function () {
            var element = $('.block-products-list.grid .product-items');

            $(element).owlCarousel({
                stagePadding: 20,
                margin: 20,
                loop: false,
                nav: true,
                navText: [
                    "<i class='fa fa-angle-left'></i>",
                    "<i class='fa fa-angle-right'></i>"
                ],
                autoplay: false,
                autoplayHoverPause: true,
                lazyLoad: true,
                responsive: {
                    0: {
                        items: 3,
                        margin: 0,
                        stagePadding: 0
                    },
                    769: {
                        items: 4,
                        margin: 20,
                        padding: 20
                    },
                    1240: {
                        items: 4,
                        margin: 20,
                        padding: 20
                    }
                },
                onInitialized: function () {
                    updateNavVisibility();
                },
                onChanged: function () {
                    updateNavVisibility();
                }
            });

            function updateNavVisibility() {
                var $element = $(element);
                var $items = $element.find('.owl-item');
                var $prev = $element.find('.owl-prev');
                var $next = $element.find('.owl-next');

                var activeItems = $element.find('.owl-item.active');
                var firstActiveIndex = $items.index(activeItems.first());
                var lastActiveIndex = $items.index(activeItems.last());

                if ($items.length <= 4) {
                    $prev.hide();
                    $next.hide();
                } else if (firstActiveIndex === 0) {
                    $prev.hide();
                    $next.show();
                } else if (lastActiveIndex === $items.length - 1) {
                    $prev.show();
                    $next.hide();
                } else {
                    $prev.show();
                    $next.show();
                }
            }

            $('.owl-next').on('click', function () {
                setTimeout(function () {
                    updateNavVisibility();
                }, 0);
            });

            $('.owl-prev').on('click', function () {
                setTimeout(function () {
                    updateNavVisibility();
                }, 0);
            });

            $('.owl-carousel').on('initialized.owl.carousel', function () {
                updateNavVisibility();
            });
        }
    })
})
