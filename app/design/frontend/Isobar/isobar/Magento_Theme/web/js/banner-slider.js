define([
    'jquery',
    'slick'
], function ($) {
    'use strict';

    $('#banner-home-id').slick({
        adaptiveHeight: true,
        autoplay: true,
        autoplaySpeed: 2000,
        infinite: true,
        arrows: true,
        dots: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    dots: false
                }
            }
        ]
    });
});
