define(
    [
        'jquery',
        'Amasty_CheckoutCore/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage'
    ],
    function (
        $,
        resourceUrlManager,
        quote,
        errorProcessor,
        storage
    ) {
        "use strict";

        return function (form) {
            var $form = form instanceof $ ? form : $(form),
                params = (resourceUrlManager.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {},
                urls = { 'guest': '/amasty_checkout/guest-carts/:cartId/save-register' },
                serviceUrl =  resourceUrlManager.getUrl(urls, params),
                payload = $form.serializeArray().reduce(function (result, currentValue) {
                    result[currentValue.name] = currentValue.value;
                    return result;
                }, {});

            return storage.post(
                serviceUrl,
                JSON.stringify({
                    customerRegister: payload
                }),
                false
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
        };
    }
);
