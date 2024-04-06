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
/*jshint evil:true browser:true jquery:true */
define(
    [
    "jquery",
    "Magento_Ui/js/modal/alert",
    "mage/translate",
    "jquery/ui",
    "mage/cookies",
    "Magento_Cookie/js/require-cookie"
    ], function ($, alert) {
        "use strict";

        $.widget(
            'mage.requireCookie', $.mage.requireCookie, {
                /**
                 * This method set the url for the redirect.
                 *
                 * @private
                 */
                _checkCookie: function (event) {
                    if (typeof window.gdprCookie !== 'undefined' && !$.mage.cookies.get(window.gdprCookie.name)) {
                        event.stopPropagation();
                        event.preventDefault();
                        alert(
                            {
                                content: $.mage.__('Please allow to use cookie before you can access this feature.')
                            }
                        );

                        return;
                    }

                    this._super(event);
                }
            }
        );

        return $.mage.requireCookie;
    }
);
