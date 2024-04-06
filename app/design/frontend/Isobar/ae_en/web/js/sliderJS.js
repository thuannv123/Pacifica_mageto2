define(['jquery', 'owlcarousel'], function($) {
    return function(config) {
        var element = config.elementClass;
        var padding = config.stagePadding;
        var marg = config.margin;

        if ($(window).width() > 768) {
            $(element).owlCarousel({
                stagePadding: Number(padding),
                margin: Number(marg),
                loop: false,
                nav: true,
                navText: [
                    "<i class='fa fa-angle-left'></i>",
                    "<i class='fa fa-angle-right'></i>"
                ],
                autoplay: false,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 2,
                        margin: 10,
                        stagePadding: 10
                    },
                    769: {
                        items: 5,
                        margin: 20,
                        padding: 20
                    },
                    1240: {
                        items: 5
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
                    $prev.addClass('disabled');
                    $next.removeClass('disabled');
                } else if (lastActiveIndex === $items.length - 1) {
                    $prev.removeClass('disabled');
                    $next.addClass('disabled');
                } else {
                    $prev.removeClass('disabled');
                    $next.removeClass('disabled');
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
    }
});
