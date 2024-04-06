define([
    'jquery',
    'matchMedia'
], function ($, matchMedia) {
    'use strict';
    
    $('.wrap-menu').hover(function () {
        $('a.level-top',this).toggleClass('ui-state-focus');
    });

    $('a.level-top span').hover(function () {
        $(this).next('.wrap-menu').fadeIn(1000);
        $(this).next('.wrap-menu').css('left','0px');
    },function () {
        $(this).next('.wrap-menu').fadeOut(1000);
    });

    if($(window).width() < 768) {
        if($("a.level-top").next().length > 0) {
            $('ul > li.level0 > a.level-top').attr('onclick',"return false;");
            $('ul > li.level0 > a.level-top').bind('click',function(e){
                $('~ ul.level0', this).show();
                $(this).attr('onclick',"return true;");
                $('~ .wrap-menu ul.level0', this).show();
                $(this).toggleClass('ui-state-active-2');

            });
        }
        else {
            $('ul > li.level0 > a.level-top').attr('onclick',"return true;");
        }
    }

    $(window).bind('resize', function () {
        if($(window).width() < 768) {
            if($("a.level-top").next().length > 0) {
                $('ul > li.level0 > a.level-top').attr('onclick',"return false;");
                $('ul > li.level0 > a.level-top').bind('click',function(e){
                    $('~ ul.level0', this).show();
                    $(this).attr('onclick',"return true;");
                    $('~ .wrap-menu ul.level0', this).show();
                    $(this).toggleClass('ui-state-active-2');

                });
            }
            else {
                $('ul > li.level0 > a.level-top').attr('onclick',"return true;");
            }
        }
    }).trigger('resize');

    $( window ).scroll(function() {

        if($( window ).scrollTop() > 38){
            $('body').addClass('scroll');
        }
        else {
            $('body').removeClass('scroll');
        }
    });

    $( document ).ready(function() {
        $('ul.header.links li.nav.item').remove();
    })

    
});