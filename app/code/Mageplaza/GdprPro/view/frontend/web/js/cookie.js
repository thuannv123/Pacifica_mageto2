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
require([
    "jquery",
    'jquery-ui-modules/widget',
    'mage/cookies'
], function ($) {
    'use strict';

    $(document).ready(function () {
        $.ajax({
            url: window.BASE_URL + 'customer/cookie/cookie',
            showLoader: true,
            type: 'POST',
            success: function (data) {
                if (data.checkCookieEnable) {
                    $('.page-wrapper').append(data.output);

                    if ($.mage.cookies.get(data.cookieName)) {
                        $('#gdpr-notice-cookie-block').hide();
                    } else {
                        $('#gdpr-notice-cookie-block').show();
                    }

                    $('#gdpr-btn-cookie-allow').on('click', $.proxy(function () {
                        var cookieExpires = new Date(new Date().getTime() + data.cookieLifetime * 1000);

                        $.mage.cookies.set(data.cookieName, JSON.stringify(data.cookieValue), {
                            expires: cookieExpires
                        });

                        $.mage.cookies.set('mpGTMCookie', JSON.stringify(data.cookieValue), {
                            expires: cookieExpires
                        });

                        if ($.mage.cookies.get(data.cookieName)) {
                            $('#gdpr-notice-cookie-block').hide();
                            $(document).trigger('user:allowed:save:cookie');
                        } else {
                            window.location.href = data.noCookiesUrl;
                        }
                    }, this));
                }
            }
        });
    });
});
