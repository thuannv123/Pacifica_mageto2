/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_GdprPro
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
    'jquery'
    ], function ($) {
        'use strict';

        return function (widget) {
            $.widget(
                'mage.address', widget, {
                    /**
                     * @param   e
                     * @private
                     */
                    _deleteAddress: function (e) {
                        var extraData = this.options.extraData;
                        if (extraData) {
                            var currentControllerAction = extraData.currentControllerAction;
                            if (currentControllerAction === 'account-edit') {
                                this._confirmPassword();
                                return;
                            }
                        }

                        this._super(e);
                    },

                    /**
                     * Bind event handlers for confirm password before delete account
                     *
                     * @private
                     */
                    _confirmPassword: function (e) {
                        var options = this.options,
                        extraData = options.extraData,
                        checkpasswordUrl = extraData.checkpasswordUrl,
                        deleteUrlPrefix = options.deleteUrlPrefix,
                        lazyload = extraData.lazyload;

                        var html_element = "<input type='password' name='gdpr-password' width='100' id='gdpr-password'/>"
                        + "<p id='gdpr_load' style='width: 20px; height: 20px; margin: 20px 0 0 20px; display: none;'><img src=" + lazyload + " /></p>"
                        + "<p id='cfnotify' style='color:red; height: 20px; margin-top: 20px; display: none;'></p>";

                        $('<div/>').html(html_element)
                        .modal(
                            {
                                title: 'Please confirm password',
                                autoOpen: true,
                                closed: function () {
                                    location.reload();
                                },
                                modalClass: 'mpGdprConfirm',
                                buttons: [{
                                    text: 'Confirm',
                                    attr: {
                                        'data-action': 'confirm'
                                    },
                                    class: 'action save primary',
                                    click: function (event) {
                                        var password = $("#gdpr-password").val(),
                                        notifyEl = $("#cfnotify");
                                        if (!password) {
                                            notifyEl.show();
                                            notifyEl.text('Password is empty, please fill out your password again!.');
                                        } else {
                                            $("#gdpr_load").show();
                                            $.ajax(
                                                {
                                                    url: checkpasswordUrl,
                                                    type: "post",
                                                    dataType: "text",
                                                    data: {
                                                        password: password
                                                    },
                                                    success: function (data) {
                                                        var result = JSON.parse(data);
                                                        if (result.status) {
                                                            $(location).attr("href", deleteUrlPrefix);
                                                        } else {
                                                            $("#gdpr_load").hide();
                                                            notifyEl.show();
                                                            notifyEl.text('Password is not correctly. Please try it again!');
                                                        }
                                                    }
                                                }
                                            );
                                        }
                                    }
                                }]
                            }
                        );
                        var input = document.getElementById('gdpr-password');

                        // Execute a function when the user releases a key on the keyboard
                        input.addEventListener("keyup", function(event) {
                            // Number 13 is the "Enter" key on the keyboard
                            if (event.keyCode === 13) {
                                // Cancel the default action, if needed
                                event.preventDefault();
                                // Trigger the button element with a click
                                $("[data-action=confirm]").click();
                            }
                        });
                    }
                }
            );

            return $.mage.address;
        }
    }
);
