var config = {
    config: {
        map: {
            "*": {
                validationZipcodeSuggestion: 'Isobar_ZipcodeSuggestion/js/validation'
            }
        },
        mixins: {
            'Magento_Ui/js/lib/validation/validator': {
                'Isobar_ZipcodeSuggestion/js/validation-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'Isobar_ZipcodeSuggestion/js/view/default-mixin': true
            },
        }
    }
};
