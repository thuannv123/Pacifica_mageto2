/**
 * @copyright: Copyright © 2024 Marvelic. All rights reserved.
 * @author   : Marvelic Engine Co.,Ltd. <info@marvelic.co.th>
 */

define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        $,
        Component,
        rendererList
    ) {
        'use strict';

        var methods = [
            {
                type: 'beamcheckout_creditcard',
                component: 'Marvelic_BeamCheckout/js/view/payment/method-renderer/beamcheckout-creditcard'
            },
            {
                type: 'beamcheckout_ewallet',
                component: 'Marvelic_BeamCheckout/js/view/payment/method-renderer/beamcheckout-ewallet'
            },
            {
                type: 'beamcheckout_qrcode',
                component: 'Marvelic_BeamCheckout/js/view/payment/method-renderer/beamcheckout-qrcode'
            }
        ];

        $.each(methods, function (k, method) {
            rendererList.push(method);
        });
        return Component.extend({});
    }
);