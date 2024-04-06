define([
    'jquery',
    'prototype'
], function ($) {
    'use strict';

    $.widget('mage.isobarMenuSortable', {
        options: {
            moveUrl: ''
        },

        _create: function () {
            this.setupSortable();
        },

        setupSortable: function () {
            this.element.sortable({
                distance: 8,
                tolerance: 'pointer',
                cancel: 'input, button',
                forcePlaceholderSize: true,
                update: this.sortableDidUpdate.bind(this)
            });
        },

        sortableDidUpdate: function (event, ui) {
            var data = {
                'id': ui.item.attr('id'),
                'aid': ui.item.prev().attr('id')
            };
            $.ajax({
                url: this.options.moveUrl,
                method: 'POST',
                data: data,
                showLoader: true
            }).done(function (data) {
                $('.page-main-actions').next('.messages').remove();
                $('.page-main-actions').next('#messages').remove();
                $('.page-main-actions').after(data.messages);
            }).fail(function (jqXHR, textStatus) {
                if (window.console) {
                    console.log(textStatus);
                }
                location.reload();
            });
        }
    });

    return $.mage.isobarMenuSortable;
});
