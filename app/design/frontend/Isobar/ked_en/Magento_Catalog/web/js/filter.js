require(['jquery'], function ($) {
    $(document).ready(function () {

        $(document).on('click', '#amasty-shopby-product-list #showSidebarButton', function() {
            if(!$(".columns .sidebar-main").hasClass('active')) {
                $(".columns .sidebar-main").toggleClass("active");
            }
            if(!$("#amasty-shopby-product-list #overlay").hasClass('active')) {
                $("#amasty-shopby-product-list #overlay").toggleClass("active");
            }
            $(".page-wrapper .page-header").css("z-index", "-1");
            $(".page-wrapper .footer").css("z-index", "-1");
            if ($(".page-wrapper .top-container .block").length > 0) {
                $(".page-wrapper .top-container .block").css({
                    "z-index": "-1",
                    "position": "relative"
                });
            }
        });
        
        $(document).on('click', '.columns .sidebar-main', function(e) { 
            if ($(e.target).closest('.am-show-more.-active').length === 0) {
                $(".columns .sidebar-main").toggleClass("active");
            if($(".columns .sidebar-main").hasClass('active')) {
                $(".columns .sidebar-main").removeClass("active");
            }
            if($("#amasty-shopby-product-list #overlay").hasClass('active')) {
                $("#amasty-shopby-product-list #overlay").removeClass("active");
            }
            $(".page-wrapper .page-header").css("z-index", "2");
            $(".page-wrapper .footer").css("z-index", "1");
            if ($(".page-wrapper .top-container .block").length > 0) {
                $(".page-wrapper .top-container .block").css("z-index", "1");
            }
        }
        });

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

            $('.product-image-photo').hover(function () {
                window.location.href = $(this).closest('a.product-item-photo').attr('href');
            });

            $('.product-item-name').hover(function () {
                window.location.href = $(this).find('a.product-item-link').attr('href');
            });

        }
        if ($('body.checkout-index-index').length > 0) {
            $('.checkout-index-index .page-wrapper .page-main').css('margin-top', '');
        }
    });
});