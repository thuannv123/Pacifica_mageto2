define([
    'jquery',
    'matchMedia'
], function ($, matchMedia) {
    'use strict';
    //toggle footer
    $(document).ready(function () {
        if ($(window).width() <= 768) {
            $('.footer-item ul').hide();
            $('.footer-item').click(function () {
                $(this).find('ul').toggle();
                $(this).toggleClass('active');
            });
        }
    });
    //scroll menu
    $(document).ready(function () {
        const header = $(".page-header");
        let lastScrollTop = 0;
        let timeoutId = null;

        $(window).scroll(function () {
            const scrollTop = $(window).scrollTop();
            if (scrollTop > 150) {
                if (scrollTop > lastScrollTop) {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(function () {
                        header.css("transform", "translateY(-100%)");
                    }, 200);
                } else {
                    clearTimeout(timeoutId);
                    header.css("transform", "translateY(0)");
                }
            } else {
                header.css("transform", "translateY(0)");
            }

            lastScrollTop = scrollTop;
        });
    });

    //
    $(document).ready(function () {

        if ($('.product-info-main').find('.product-add-form .product-options-wrapper').length === 0) {
            $('.product-info-main').addClass('type-one');
            $('.product-info-main.type-one .product-social-links').css('display','block');
        };
        if ($(window).width() >= 769) {
            if ($('.page-wrapper').find('.top-container').length === 0) {
                $('.page-wrapper .breadcrumbs').css('margin-top', '175px');
                $('.page-wrapper .page-main-full-width').css('margin-top', '175px');
            }
            if ($('.page-wrapper').find('.top-container').length === 0 && $('.page-wrapper').find('.breadcrumbs').length === 0) {
                $('.page-wrapper .page-main').css('margin-top', '175px');
            }
            else {
                $('.page-wrapper').find('.top-container').css('margin-top', '175px')
            }
            if($('.cms-track-my-order').length > 0){
                $('.page-wrapper .page-main-full-width').css('margin-top', '0px');
            }
        }
        if ($(window).width() <= 768) {
            if ($('.page-wrapper').find('.top-container').length === 0) {
                $('.page-wrapper .breadcrumbs').css('margin-top', '60px');
                $('.page-wrapper .page-main-full-width').css('margin-top', '60px');
            }
            if ($('.page-wrapper').find('.top-container').length === 0 && $('.page-wrapper').find('.breadcrumbs').length === 0) {
                $('.page-wrapper .page-main').css('margin-top', '60px');
            }
            else {
                $('.page-wrapper').find('.top-container').css('margin-top', '60px')
            }
            if ($('.page-product-configurable')) {
                if ($('.product-info-main').find('.product-add-form .product-options-wrapper').length != 0) {
                    $(".product-social-links").appendTo(".product-info-price");
                }
            }
        }
        if ($('body.checkout-index-index').length > 0) {
            $('.checkout-index-index .page-wrapper .page-main').css('margin-top', '');
        }
        if ($('body.sales-order-print').length > 0) {
            $('.sales-order-print .page-wrapper .page-main').css('margin-top', '2rem');
        }
        if ($(window).width() <= 979 && $(window).width() >= 769) {
            let pageHeaderHeight = $('.page-header').height();
            if ($('body.cms-index-index').length > 0 || $('body.cms-page-view').length > 0) {
                if ($('.page-wrapper').find('.top-container').length === 0) {
                    $('.page-wrapper .page-main-full-width').css('margin-top', pageHeaderHeight +'px');
                } else {
                    $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px')
                }
            }
            if ($('body.checkout-cart-index').length > 0 || $('body.checkout-onepage-success').length > 0) {
                if ($('.page-wrapper').find('.top-container').length === 0) {
                    $('.page-wrapper .page-main').css('margin-top', pageHeaderHeight +'px');
                } else {
                    $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px')
                }
            }
            if ($('body.customer-account-login').length > 0 || $('body.customer-blog-posts').length > 0 || $('body.customer-account-create').length > 0 || $('body.page-layout-1column').length > 0) {
                if ($('.page-wrapper').find('.top-container').length === 0) {
                    if ($('.page-wrapper').find('.breadcrumbs').length === 0) {
                        $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px');
                    } else {
                        $('.page-wrapper .breadcrumbs').css('margin-top', pageHeaderHeight +'px');
                    }
                } else {
                    $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px');
                }
            }
            if ($('body.catalog-product-view').length > 0 || $('body.page-products').length > 0) {
                if ($('.page-header').height() >= 224) {
                    if ($('.page-wrapper').find('.top-container').length === 0) {
                        if ($('.page-wrapper').find('.breadcrumbs').length === 0) {
                            $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px');
                        } else {
                            $('.page-wrapper .breadcrumbs').css('margin-top', pageHeaderHeight +'px');
                        }
                    } else {
                        $('.page-wrapper').find('.top-container').css('margin-top', pageHeaderHeight +'px');
                    }
                } 
            }
        }
    });

    $(document).ready(function(){
        if ($('body').hasClass('page-products')) {
            $(window).on("scroll", function() {
                if (window.scrollY < 500 ) {
                    var btn = $('.amscroll-load-button.-before');
                    if (btn.length >= 1) {
                        btn.trigger('click');
                    }
                }
            });
        }
    })
})