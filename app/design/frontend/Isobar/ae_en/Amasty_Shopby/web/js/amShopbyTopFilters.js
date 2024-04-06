/**
 * Methods to moving and removing filters from top to sidebar on mobile devices
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var options = {
        widgets: {
            collapsible: '{"collapsible":{"openedState": "active", "collapsible": true, "active": false, '
                + '"collateral": { "openedState": "filter-active", "element": "body" } }}',
            accordion: '{"accordion":{"openedState": "active", "collapsible": true, "active": false, '
                + '"multipleCollapsible": %1}}'
        },
        selectors: {
            sidebar: '.sidebar.sidebar-main',
            sidebarList: '.sidebar.sidebar-main #narrow-by-list',
            list: '#narrow-by-list',
            topNav: '.catalog-topnav',
            topFilterActions: '.catalog-topnav .filter-actions',
            topNarrowList: '.catalog-topnav #narrow-by-list',
            layeredFilter: '#layered-filter-block',
            blockFilter: '.block.filter',
            optionsItem: '.filter-options-item',
            filterTitle: '.filter-title:before',
            filterActions: '.block-actions.filter-actions',
            swatches: '.items, .swatch-attribute',
            topContainer: '.page-wrapper .top-container'
        },
        classes: {
            allTop: 'amshopby-all-top-filters-append-left',
            filterTop: 'amshopby-filter-top'
        }
    };

    return {
        /**
         * @public
         * @returns {void}
         */
        moveTopFiltersToSidebar: function () {
            if ($(options.selectors.sidebarList).length === 0) {
                var blockClass = $(options.selectors.layeredFilter).length
                    ? options.selectors.layeredFilter
                    : options.selectors.blockFilter,
                    $element = $(options.selectors.topNav + ' ' + blockClass).clone(),
                    $sidebar = $(options.selectors.sidebar).first();

                $(options.selectors.topFilterActions).hide();
                $element.find(options.selectors.filterTitle).css('display', 'none');
                $element
                    .addClass(options.classes.allTop)
                    .attr('data-mage-init', options.widgets.collapsible);
                $element.find(options.selectors.list)
                    .attr('data-mage-init', options.widgets.accordion.replace('%1', 'false'));
                $element.find(options.selectors.filterActions).remove();
                $sidebar.append($element);
                $sidebar.trigger('contentUpdated');

                return;
            }

            $(options.selectors.topNarrowList + ' ' + options.selectors.optionsItem).each(function () {
                var isPresent = false,
                    classes = $(this).find(options.selectors.swatches).first().attr('class'),
                    i;

                if (classes) {
                    var listClasses = classes.split(' '),
                        currentClass = '';

                    for (i = 0; i < listClasses.length; i++) {
                        if (listClasses[i].indexOf('am-filter-items-') !== -1) {
                            currentClass = listClasses[i];

                            break;
                        }
                    }

                    if (currentClass !== '' && $(options.selectors.sidebarList + ' .' + currentClass).length > 0) {
                        isPresent = true;
                    }
                }

                if (isPresent) {
                    return;
                }

                if ($(window).width() <= 768) {
                    if (($('.amshopby-filter-top').length == 0)) {
                        $(this).addClass(options.classes.filterTop).appendTo($(options.selectors.topContainer));
                    }
                }else{
                    $(this).addClass(options.classes.filterTop).appendTo($(options.selectors.sidebarList).first());
                } 
                

            });
            if($(window).width() <= 768){
                if($('#am-shopby-container').length >= 1){
                    var firstElement = document.getElementById("filter-category");
                    var secondElement = document.getElementById("am-shopby-container");
                    var parentElement = firstElement.parentNode;
                    var nextSibling = secondElement.nextSibling;
                    parentElement.insertBefore(firstElement, secondElement.nextSibling);
                    parentElement.insertBefore(secondElement, nextSibling);
                }
            }
            $(options.selectors.sidebar + ' ' + options.selectors.blockFilter).first().trigger('contentUpdated');
        },

        /**
         * @public
         * @returns {void}
         */
        removeTopFiltersFromSidebar: function () {
            if ($(options.selectors.topNav).length === 0) {
                return;
            }

            $(options.selectors.sidebarList + ' .' + options.classes.filterTop)
                .appendTo($(options.selectors.topNarrowList));
            $(options.selectors.sidebar + ' .' + options.classes.allTop).remove();
        }
    };
});
