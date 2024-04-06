define(
    [
        'jquery'
    ],function (
        $
    ) {
        'use strict';

        var mixin = {
            setCode: function (value) {
                this.code(value);
                /* Hidding extra fields when entity is CART PRICE RULE (POSTBACK) */
                var entityValue = value;
                setTimeout(function ()
                {
                    $('.admin__field').each(function () {
                        var dataIndex = $(this).data('index');
                        if (dataIndex == 'category_levels_separator') {
                            if (entityValue == 'cart_price_rule_behavior' || entityValue == 'order_behavior') {
                                $(this).css('display', 'none');
                            } else {
                                $(this).css('display', 'block');
                            }
                        }
                        if (dataIndex == 'categories_separator') {
                            if (entityValue == 'cart_price_rule_behavior' || entityValue == 'order_behavior') {
                                $(this).css('display', 'none');
                            } else {
                                $(this).css('display', 'block');
                            }
                        }
                        if (dataIndex == 'send_email' ||
                            dataIndex == 'generate_shipment_by_track' ||
                            dataIndex == 'generate_invoice_by_track'
                        ) {
                            if (entityValue == 'order_behavior') {
                                $(this).css('display', 'block');
                            } else {
                                $(this).css('display', 'none');
                            }
                        }
                        if (dataIndex == 'send_reset_password_after_import') {
                            if (entityValue != 'custom_behavior') {
                                $(this).css('display', 'none');
                            } else {
                                $(this).css('display', 'block');
                            }
                        }
                    });
                }, 3000);
            }

        };
        return function (target) {
            return target.extend(mixin);
        };
    });