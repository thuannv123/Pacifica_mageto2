define(
    [
        "jquery",
        "matchMedia",
        "mage/url",
        "menu",
        "owlCarousel",
        "slick",
    ], function ($, mediaCheck,urlBuilder) {
        "use strict";

        $.widget(
            'Isobar.megamenu', $.mage.menu, {

                _create: function () {
                    var megamenu_not_sidebar = $('.tm-megamenu:not(.sidebar)', this.element);
                    var megamenu_not_vertical = $('.level1.tm-megamenu:not(.vertical)', this.element);
                    var megamenu = $('.level0', this.element);
                    var sidebar_magemenu = $('.sidebar .level1.tm-megamenu, .horizontal-vertical-menu .level1.tm-megamenu');

                    this._checkParent(megamenu);
                    this._super();
                    this._toggleClass(megamenu_not_sidebar);
                    this._setWidthMegamenu(megamenu);
                    this._setWidthSidebar(sidebar_magemenu);
                    this._hoverType(this, megamenu_not_vertical);
                    this._CloneMenu(megamenu_not_vertical);
                    this._productSliderSimple();
                    this._mobileMenu(this, megamenu_not_vertical);
                },

                _setOption: function ( key, value ) {
                    this._super("_setOption", key, value);
                },

                _toggleMobileMode: function () {
                    var subMenus;

                    $(this.element).off('mouseenter mouseleave');
                    this._on({

                        /**
                         * @param {jQuery.Event} event
                         */
                        'click .level0.ui-menu-item:not(.li-megamenu) > a': function (event) {
                            var target;

                            event.preventDefault();
                            target = $(event.target).closest('.ui-menu-item');
                            target.get(0).scrollIntoView();

                            if (!target.hasClass('level-top') || !target.has('.ui-menu').length) {
                                window.location.href = target.find('> a').attr('href');
                            }
                        },

                        /**
                         * @param {jQuery.Event} event
                         */
                        'click .ui-menu-item:has(.ui-state-active)': function (event) {
                            this.collapseAll(event, true);
                        }
                    });

                    subMenus = this.element.find('.level-top:not(.li-megamenu)');
                    $.each(subMenus, $.proxy(function (index, item) {
                        var category = $(item).find('> a span').not('.ui-menu-icon').text(),
                            categoryUrl = $(item).find('> a').attr('href'),
                            menu = $(item).find('> .ui-menu');

                        this.categoryLink = $('<a>')
                            .attr('href', categoryUrl)
                            .text($.mage.__('All %1').replace('%1', category));

                        this.categoryParent = $('<li>')
                            .addClass('ui-menu-item all-category')
                            .html(this.categoryLink);

                        if (menu.find('.all-category').length === 0) {
                            menu.prepend(this.categoryParent);
                        }

                    }, this));
                },

                _mobileMenu: function (ele, megamenu_not_vertical) {
                    var topmenu = $(ele.element);
                    if(topmenu.length) {
                        mediaCheck(
                            {
                                media: '(max-width: 767px)',
                                entry: function () {
                                    $('.tm-megamenu .parent > a, .level0.parent > a', topmenu).each(function () {
                                        $('.opener', this).remove();
                                        $(this).append('<span class="opener"></span>');
                                    }).on('click', '.opener', function () {
                                        var menuChild;

                                        if( $(this).closest('.mm-submenu-level1').length ) {
                                            menuChild = $(this).closest('.mm-submenu').next()
                                        }else {
                                            menuChild = $(this).parent().siblings('.submenu');
                                        }
                                        if(window.checkConfig){
                                            if($(this).closest('.parent_menu').length){
                                                var category_id = $(this).closest('.level0').attr('category_id');
                                                $.ajax({
                                                    showLoader: false,
                                                    url: urlBuilder.build('rest/V1/ajax_menu/' + category_id),
                                                    data: null,
                                                    type: 'GET',
    
                                                }).always(function (data) {
                                                    var box_content = $('.top_menu').find('.box_content'),
                                                    child_menu = $('.child_menu .top-navigation-title-section .title'),
                                                    explore_name = $('.explore_name'),
                                                    content = data[0],
                                                    name = data[1],
                                                    url = data[2],
                                                    lang = document.querySelector('html').getAttribute('lang');
    
                                                    child_menu.html('');
                                                    child_menu.html(name);
                                                    box_content.html('');
                                                    box_content.html(content);
                                                    if (lang == 'en') {
                                                        explore_name.html('Explore ' + name);
                                                    } else if (lang == 'th') {
                                                        explore_name.html('ค้นหาสินค้า' + name);
                                                    }
                                                    explore_name.attr('href',url);
    
                                                    $('#ui-id-1').addClass('slide-toggle');
                                                    $('#ui-id-2').addClass('slide-toggle');
    
                                                    $(".child_menu .box_content .row .level1 strong").append(newDiv);
                                                    $(".child_menu .toggle-down").click(function(){
                                                        $(this).closest('.level1').find('.level3.megamenu-wrapper').toggleClass("active");
                                                        $(this).parent().toggleClass("active");
                                                    });
                                                })
                                            }
                                            var newDiv = $("<div></div>");
                                            newDiv.addClass("toggle-down");
                                            $(".child_menu .box_content .level0 .level1.category-item .level1.submenu").append(newDiv);
                                            $(".child_menu .toggle-down").click(function(){
                                                $(this).parent().toggleClass("active");
                                            });
                                        }
                                        if(!window.checkConfig){
                                            $(this).toggleClass('active');
                                            menuChild.slideToggle(function () {
                                                menuChild.toggleClass('expand')
                                            });
                                            ele._productSliderMobile(megamenu_not_vertical);
                                        }
                                        return false;
                                    });

                                    var closeDiv = $("<div></div>");
                                    closeDiv.addClass("close-tab");
                                    $(".top_menu .nav-sections #header-tabs .nav-sections-item-content").append(closeDiv);
                                    $(".close-tab").click(function(){
                                        $("html").removeClass("nav-open")
                                        setTimeout(function(){
                                            $("html").removeClass("nav-before-open");
                                          }, 500);
                                    });

                                    if($(".parent_menu").length > 0){
                                        $(".parent_menu").parent().addClass("menu-custom")
                                    }

                                    $('.tm-megamenu').on('click', function (event) {
                                        event.stopPropagation();
                                    });
                                    $('.child_menu .top-navigation-title-section .back-toggle').click(function(){
                                        $('#ui-id-1').removeClass('slide-toggle');
                                        $('#ui-id-2').removeClass('slide-toggle');
                                    })

                                    //menu aerie
                                   
                                    $("#ui-id-998 .menu-edit .opener").click(function(){
                                        $("#ui-id-998 .menu-edit").toggleClass('slide-right');
                                        $(this).closest('.level0').parent().addClass("highlight");
                                        $(".title-shop").addClass('hide-title');
                                        var title = $(this).closest('.level-top').find('.mm-title').text();
                                        var newSpan = $("<span>Explore "+ title +"</span>");
                                        newSpan.addClass("newSpan");
                                        $('.slide-right .slide_menu #ui-id-888').append(newSpan);
                                    });
                                    $("#ui-id-998 .menu-edit .back-toggle").click(function(){
                                        $("#ui-id-998 .menu-edit").removeClass('slide-right');
                                        $("#ui-id-998 .menu-edit").removeClass('highlight');
                                        $(".title-shop").removeClass('hide-title');
                                        $('#ui-id-888 .newSpan').remove();
                                    });
                                    
                                }
                            }
                        );
                        return false;
                    }

                    var nav = $('nav.mobile-only');
                    var navDesktop = $(this.element).parent('.desktop-only');

                    mediaCheck(
                        {
                            media: '(min-width: 767px)',
                            entry: function () {
                                nav.removeClass('active');
                                navDesktop.addClass('active');
                            },
                            exit: function () {
                                nav.addClass('active');
                                navDesktop.removeClass('active');
                            }
                        }
                    );
                },

                _open: function ( submenu ) {
                    this._super(submenu);

                    if (submenu.is(this.options.differentPositionMenus)) {
                        var position = $.extend(
                            {
                                of: this.active
                            }, this.options.differentPosition
                        );

                        let submenu_width = submenu.width();

                        if(this.active.children().hasClass('tm-megamenu-pc')) {
                            submenu_width = this.active.children('.tm-megamenu-pc').width();
                        }

                        let activeOffset = this.active.offset(),
                            activeWidth = this.active.width(),
                            submenu_position_left = activeWidth + activeOffset.left - submenu_width,
                            submenu_position_right = activeWidth + activeOffset.left + submenu_width;

                        let screen_width = $(window).width();

                        let cate_menu = $('.tm-top-navigation');

                        if( cate_menu.length > 0 ) {
                            const cate_menu_pos = cate_menu.offset();

                            if( submenu_position_left < cate_menu_pos.left && submenu_position_left > 0
                                || submenu_position_left < cate_menu_pos.left && submenu_position_right > screen_width ) {
                                submenu.addClass('vertical-sub-menu');
                            }
                        }

                        let activePosition = this.active.position();

                        mediaCheck(
                            {
                                media: '(min-width: 992px)',
                                entry: function () {
                                    submenu.position(activePosition);
                                },
                                exit: function () {
                                    submenu.position(position);
                                }
                            }
                        );
                    }
                },

                _toggleClass: function ( selector ) {
                    var ownClass = 'megamenu-wrapper';
                    mediaCheck(
                        {
                            media: '(max-width: 767px)',
                            entry: function () {
                                // selector.removeClass(ownClass);
                                $(selector).removeClass(ownClass);
                            },
                            exit: function () {
                                if(!$(selector).hasClass(ownClass) ) {
                                    $(selector).addClass(ownClass);
                                }
                            }
                        }
                    );
                },

                _setWidthMegamenu: function( selector ) {
                    selector.each(function () {
                        const dataWidth = $(this).data('width');

                        if( dataWidth && typeof dataWidth === 'number' ) {
                            $(this).width(dataWidth);
                        }
                    });
                },

                _setWidthSidebar: function ( selector ) {
                    if(selector.hasClass('in-sidebar')) {
                        $(window).on(
                            'resize.menu', function () {
                                var pageWidth = $('.columns').innerWidth();
                                var sidebarWidth = 0;

                                if( $('.sidebar .navigation').length ) {
                                    sidebarWidth = $('.sidebar .navigation').innerWidth();
                                }else if( $('.horizontal-vertical-menu').length ) {
                                    sidebarWidth = $('.horizontal-vertical-menu').innerWidth();
                                }

                                const selectorWidth = pageWidth - sidebarWidth;

                                selector.each(function () {
                                    const dataWidth = $(this).data('width');

                                    if(dataWidth) {
                                        const detectPecent = dataWidth.toString().indexOf('%');

                                        if(detectPecent !== -1) {
                                            const percentWidth = dataWidth.substr(0, dataWidth.lastIndexOf('%'));
                                            $(this).width(percentWidth * selectorWidth / 100);
                                        }
                                    }
                                });

                                selector.css('max-width', selectorWidth)
                                    .parent().addClass('li-megamenu');
                            }
                        ).trigger('resize.menu');
                    }
                },

                _hoverType: function ( ele, selector ) {
                    mediaCheck(
                        {
                            media: '(min-width: 768px)',
                            entry: function () {
                                selector.parent().on('mouseenter',
                                    function () {
                                        if($(this).find('.mm-submenu-level1.active').length === 0) {
                                            $(this).find('.mm-submenu-level1:first-child').addClass('active');
                                            $(this).find('.level2:first-child').addClass('show').show(100, function () {
                                                ele._productSlider($(this));
                                            });
                                        }else {
                                            ele._productSlider($(this).find('.tm-megamenu-pc .level2.show'));
                                        }
                                    }
                                ).on('click', function (e) {
                                    e.stopPropagation();
                                });

                                $(document).on('mouseenter', '.level0.ui-menu-item', function () {
                                    ele._productSlider($(this).find('.level1.horizontal.tm-megamenu-pc'));
                                });

                               $(document).on('mouseenter', '.mm-submenu-level1', function () {
                                    let index = $(this).index() + 1;

                                    $(this).addClass('active')
                                        .siblings().removeClass('active');

                                    const tabContent = $(this).parent().siblings('.navi-content').find('.level2:nth-child('+index+')');

                                    tabContent.addClass('show').show().siblings('.level2').removeClass('show').hide();

                                    ele._productSlider(tabContent);
                                });
                            }
                        }
                    );
                },

                _CloneMenu: function ( selector ) {
                    if( selector.length ) {
                        selector.each(function () {
                            const $this = $(this);
                            const menuClone = $this.clone();
                            $this.addClass('tm-megamenu-pc');

                            let colCurrentNum = null;

                            if( $this.hasClass('sidebar') ) {
                                const divCont = $this.find('> .container > .row > div');
                                const classCol = divCont.attr('class');
                                divCont.removeClass().addClass('navi-display row');
                                colCurrentNum = classCol.substring(classCol.lastIndexOf('-') + 1);

                                $this.parent().addClass('submenu-sidebar');
                            }

                            $this.parent().append(menuClone.addClass('tm-megamenu-sp'));
                            $this.find('.mm-submenu-level1').wrapAll('<div class="navi-label col-sm-'+colCurrentNum+'"></div>');
                            $this.find('.level2').wrapAll('<div class="navi-content"></div>');
                        });
                    }
                },

                _checkParent: function (megamenu) {
                    megamenu.each(
                        function () {
                            $('.tm-megamenu.level1, .level1.vertical .tm-megamenu, .level2 .tm-megamenu', this).each(function () {
                                if( $(this).find('div').length ) {
                                    $(this).parent().addClass('parent');
                                }else {
                                    $(this).remove();
                                }
                            });

                            const mmSubmenu = $('.mm-submenu', this);
                            mmSubmenu.each(function () {
                                var mmMenu = $(this).next('.tm-megamenu');
                                if ( mmMenu.length && $.trim(mmMenu.html())) {
                                    $('li', this).addClass('parent');
                                }

                                $('li', this).each(function () {
                                    var mmMenu = $(this).children('.tm-megamenu');
                                    if ( mmMenu.length && !mmMenu.is(':empty') ) {
                                        $(this).addClass('parent');
                                    }
                                });
                            });

                            $('.tm-megamenu.level1').each(function () {
                                $(this).parent().addClass('li-megamenu');
                            });
                        }
                    );
                },

                _productSlider: function ( slider ) {
                    var settings = {
                            slidesToShow: 4,
                            arrows: true,
                            slidesToScroll: 4,
                            rows: 2,
                            responsive: [
                                {
                                    breakpoint: 1100,
                                    settings: {
                                        slidesToShow: 3
                                    }
                                },
                                {
                                    breakpoint: 640,
                                    settings: {
                                        slidesToShow: 1
                                    }
                                }
                            ]
                        };

                    if(slider.css('display') === 'block'){
                        const proSlide = slider.find('.product-items');
                        const proSlide_length = slider.find('.product-items .product-item');

                        if(proSlide.find('.product-item-info').width() === 0) {
                            slider.css('opacity', 0);
                            setTimeout(function () {
                                setting(slider.find('.widget-mega').width());
								if (proSlide.hasClass('slick-initialized')) {
									proSlide.slick('unslick');
								}
                                proSlide.slick(settings);
                                slider.css('opacity', 1);
                            }, 300);
                        } else {
                            setting(slider.find('.product-items').width());

                            if (!proSlide.hasClass('slick-initialized')
                                && proSlide_length.length >= 8)
                            {
                                proSlide.slick(settings);
                            }
                        }
                    }

                    function setting(sliderWidth) {
                        if( sliderWidth < 600 && sliderWidth > 426 ) {
                            settings.slidesToShow = 3;
                            settings.slidesToScroll = 3;
                            settings.responsive[0].settings.slidesToShow = 2;
                        } else if( sliderWidth < 425 ) {
                            settings.slidesToShow = 1;
                            settings.slidesToScroll = 1;
                            settings.responsive[0].settings.slidesToShow = 1;
                        }
                    }
                },

                _productSliderMobile: function (megamenu_not_vertical) {
                    const slider = $('.tm-megamenu.level2', megamenu_not_vertical);

                    const settings = {
                        slidesToShow: 1,
                        arrows: true,
                        slidesToScroll: 1,
                        rows: 6,
                        adaptiveHeight: true
                    };

                    slider.each(
                        function () {
                            const proSlide = $(this).find('.widget-mega .product-items');
                            const proSlide_length = $(this).find('.widget-mega .product-items .product-item');

                            if (!proSlide.hasClass('slick-initialized')
                                && $(this).is(':visible')
                                && proSlide_length.length >= 6)
                            {
                                proSlide.slick(settings);
                            }

                            if(proSlide.find('.slick-track').width() === 0 && $(this).is(':visible')) {
                                proSlide.slick('unslick');
                                proSlide.slick(settings);
                            }
                        }
                    );
                },

                _productSliderSimple: function () {
                    var sliderVetical = $('.level1.tm-megamenu.vertical'),
                        settingsVertical = {
                            items: 2,
                            nav: true
                        };

                    sliderVetical.each(
                        function () {
                            $(this).on('click', function (e) {
                                e.stopPropagation();
                            });
                            const proSlide = $(this).find('.widget-mega .product-items').addClass('owl-carousel ');

                            proSlide.owlCarousel(settingsVertical);
                        }
                    );
                }
            }
        );

        
        return $.Isobar.megamenu;
    }
);
