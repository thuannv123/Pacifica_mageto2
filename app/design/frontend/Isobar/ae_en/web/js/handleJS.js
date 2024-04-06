define([
    'jquery',
    'matchMedia'
], function ($, matchMedia) {
    'use strict';

    var oldPosition = 0;
    var newPosition = 0;

    //check page earie
    $(document).ready(function(){
        var d = new Date();
        d.setTime(d.getTime() + (1*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        if(!localStorage.getItem('website')){
            if(window.location.href.indexOf('aerie') == -1){
                localStorage.setItem('website','american');
                document.cookie = "website=american;"+expires+";path=/";
            }else{
                localStorage.setItem('website','aerie');
                document.cookie = "website=aerie;"+expires+";path=/";
            }
        }else{
            if(
                $('body').hasClass('catalog-category-view') ||
                $('body').hasClass('category-product-view') ||
                $('body').hasClass('cms-aerie') ||
                $('body').hasClass('cms-american-eagle-th') || 
                $('body').hasClass('cms-american-eagle-en')
            ){
                var classList = document.getElementById('html-body').className.split(/\s+/);
                for (var i = 0; i < classList.length; i++) {
                    if (classList[i].indexOf('aerie')== -1) {
                        localStorage.setItem('website','american');
                        document.cookie = "website=american;"+expires+";path=/";
                    }else{
                        localStorage.setItem('website','aerie');
                        document.cookie = "website=aerie;"+expires+";path=/";
                        return false;
                    }
                }
            }
        }
    })

    $(document).ready(function () {
        if (localStorage.getItem('website') == 'aerie') {
            $('.page-wrapper').addClass('aerie-page');
            $('.block-static-block:has(.free-shipping)').addClass('aerie-block');
        }
        else {
            $('.page-wrapper').addClass('american-page');
            $('.block-static-block:has(.free-shipping)').addClass('american-block');

        }
    })
    //toggle newletter
    $(document).ready(function () {
        //search
        var $searchLabel = $('.minisearch .search .label'),
            $searchContent = $('.minisearch .search .control'),
            $searchClose = $('.minisearch .search .control .close-search'),
            $inputSearch = $('.minisearch .search input');

        $($searchLabel).click(function () {
            $searchContent.toggleClass('active');
            setTimeout(() => {
                $searchContent.toggleClass('show-search');
            }, 300);
        })
        $($searchClose).click(function () {
            $searchContent.removeClass('active');
            $searchContent.removeClass('show-search');
            setTimeout(() => {
                $inputSearch.val('');
            }, 300);
        })

        var $inputCouponCart = $('.content-coupon #discount-coupon-form #coupon_code');

        $($inputCouponCart).on('input', function () {
            var inputValCart = $($inputCouponCart).val();
            var $buttonApplyCart = $('.content-coupon  #discount-coupon-form .actions-toolbar .primary button');
            if (inputValCart === '') {
                $buttonApplyCart.removeClass('active');
            } else {
                $buttonApplyCart.addClass('active');
            }
        });

        // toggle content
        $('.content-toggle .title').click(function () {
            var $content = $(this).next('.content-toggle .content');
            $(this).toggleClass('active');
            $content.toggleClass('active');
        });

    });
    //scroll-back-to-top
    $(document).ready(function () {

        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scroll-top').fadeIn();
            } else {
                $('.scroll-top').fadeOut();
            }
        });
        $('.scroll-top strong').click(function () {
            $('html, body').animate({ scrollTop: 0 }, 400);
            return false;
        });

    });
    //toggle footer
    $(document).ready(function () {
        if ($(window).width() <= 768) {
            $('.footer-item ul').hide();
            $('.footer-item').click(function () {
                $(this).find('ul').toggle();
                $(this).toggleClass('active');
            });
        };

        $(".box_content_menu .toggle-down").click(function () {
            $(this).parent().toggleClass("active");
        });
        const lang = document.querySelector('html').getAttribute('lang');
        if (lang == 'en') {
            var titleDiv = $("<div> <strong>Shop AE</strong> <span>Explore AE</span> </div> ");
        } else if (lang == 'th') {
            var titleDiv = $("<div> <strong>ช้อป AE</strong> <span>ค้นหาสินค้า AE</span> </div> ");
        }
        titleDiv.addClass('title-shop');
        $(".top_menu .nav-sections #header-tabs .nav-sections-item-content .parent_menu").prepend(titleDiv);
    });
    if ($(window).width() <= 768) {
        //move price
        if ($('.page-product-configurable')) {
            if ($('.product-info-main').find('.product-add-form .product-options-wrapper').length != 0) {
                $(".product-info-price").appendTo(".product-options-wrapper .swatch-opt");
                $(".product-info-price .percent-price").appendTo(".product.media");
            }
        }
    };
    // $(document).ready(function() {
    //     var $filterOptions = $('.amasty-catalog-topnav');
    //     var $newContainer = $('#filter_interaction');
    //     $filterOptions.insertAfter($newContainer);
    // });
    $(document).ready(function() {
        $('.product.data.items .data.item.title:last').addClass('item-title-class');
        var childCategoryView = $('.catalog-category-view .page-wrapper .category-view .page-main').children().length;
        var categoryView = $('.catalog-category-view .page-wrapper .category-view');
        if (childCategoryView == 0) {
            categoryView.remove();
        }

        var colItemCart = $('#shopping-cart-table .cart.item .item-info .col.item').not(':first');
        if (colItemCart.length > 0) {
            colItemCart.css('margin-top', '20px');
        }
    });

    $(document).ready(function () {
        const header = $(".page-header");
        const blockFreeShipping = $('.block-static-block:has(.free-shipping)').length;
        if (localStorage.getItem('website') == 'aerie') {
            var freeShipping = $(".widget.block.block-static-block.aerie-block");
            var blockHeight = freeShipping.length > 0 ? $(".widget.block.block-static-block.aerie-block").height() : 0;
        } else {
            var freeShipping = $(".widget.block.block-static-block.american-block");
            var blockHeight = freeShipping.length > 0 ? $(".widget.block.block-static-block.american-block").height() : 0;
        }
        
        let lastScrollTop = 0;
        
        $(window).on('scroll', function () {
            oldPosition = window.pageYOffset || document.documentElement.scrollTop;
            const scrollTop = $(window).scrollTop();
            if (scrollTop) {
                if (scrollTop > lastScrollTop) {
                    if (blockFreeShipping > 0 && freeShipping.length > 0) {
                        freeShipping.css({
                            "transform": "translateY(-100%)",
                            "position": "fixed",
                            "width": "100%",
                            "z-index": "103"
                        });
                    }
                    if ($(window).width() <= 768) {
                        $(header).removeClass('sticky');
                        $('.panel.wrapper').addClass('sticky');
                        $('.panel.wrapper').css({
                            "transform": "translateY(-100%)",
                            "position": "fixed",
                            "width": "100%",
                            "z-index": "102"
                        });
                        if ($('.page-header.fixed-header').length > 0) {
                            $('.page-header.fixed-header').css('position', 'relative');
                        }
                    } else {
                        $(header).addClass('sticky');
                        $('.panel.wrapper').removeClass('sticky');
                        $(header).css({
                            "transform": "translateY(-100%)",
                            "position": "fixed",
                            "width": "100%",
                            "z-index": "101"
                        });
                        if($('.parent_menu li.level0.level-top:hover').length != 0){
                            $('#maincontent .overlay-menu').removeClass('menu-hover');
                            $('.parent_menu li.level0.level-top').removeClass("hover");
                        }
                    }
                    
                } else if (scrollTop === 0) {
                    if ($(window).width() <= 768) {
                        $('.panel.wrapper').removeClass('sticky');
                        $('.panel.wrapper').css({
                            "transform": "translateY(0)",
                            "position": "",
                            "width": "",
                            "z-index": ""
                        });
                    } else {
                        $(header).removeClass('sticky');
                        $(header).css({
                            "transform": "translateY(0)",
                            "position": "",
                            "width": "",
                            "z-index": ""
                        });
                    }
                    if (blockFreeShipping > 0 && freeShipping.length > 0) {
                        freeShipping.css({
                            "transform": "translateY(0)",
                            "position": "",
                            "width": "",
                            "z-index": ""
                        });
                    }
                } else {
                    if (blockFreeShipping > 0 && freeShipping.length > 0) {
                        freeShipping.css({
                            "transform": "translateY(0)"
                        });
                    }
                    if ($(window).width() <= 768) {
                        $('.panel.wrapper').css("transform", `translateY(${blockHeight}px)`);
                    } else {
                        $(header).css("transform", `translateY(${blockHeight}px)`);
                    }
                }
            } else {
                if (blockFreeShipping > 0 && freeShipping.length > 0) { 
                    freeShipping.css({
                        "transform": "",
                        "position": "",
                        "width": "",
                        "z-index": ""
                    });
                }
                if ($(window).width() <= 768) {
                    $('.panel.wrapper').removeClass('sticky');
                    $('.panel.wrapper').css({
                        "transform": "",
                        "position": "",
                        "width": "",
                        "z-index": ""
                    });
                } else {
                    $(header).removeClass('sticky');
                    $(header).css({
                        "transform": "translateY(0)",
                        "position": "",
                        "width": "",
                        "z-index": ""
                    });
                }
            }
            lastScrollTop = scrollTop;
        });

        $(document).on("click", ".header-right .action.nav-toggle", function () {
                newPosition = oldPosition;
                var closeTab = $("#header-tabs .close-tab");
                closeTab.on('click',function(e){
                    e.preventDefault();
                    $('html').animate({scrollTop:newPosition}, 'slow');
                })
            }
        );

        if ($(window).width() >= 769) {
            $(".parent_menu li.level0.level-top").hover(
                function() {
                    $(this).addClass("hover");
                    $('#maincontent .overlay-menu').addClass("menu-hover");
                    $('.widget.block.block-static-block.american-block').addClass('menu-hover');
                    $('.widget.block.block-static-block.aerie-block').addClass('menu-hover');
                    if ($(window).scrollTop() === 0) { 
                        $('.widget.block.block-static-block.american-block.menu-hover').css({
                            'z-index': '103',
                            'position': 'relative'
                        });
                        $('.widget.block.block-static-block.aerie-block.menu-hover').css({
                            'z-index': '103',
                            'position': 'relative'
                        });
                    }
                },
                function() {
                    $(this).removeClass("hover");
                    $('#maincontent .overlay-menu').removeClass("menu-hover");
                    $('.widget.block.block-static-block.american-block').removeClass('menu-hover');
                    $('.widget.block.block-static-block.aerie-block').removeClass('menu-hover');
                }
            );
        }
    });
})