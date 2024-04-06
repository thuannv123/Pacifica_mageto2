var config = 
{
    config: 
    {
        mixins: 
        {
        	'Magento_Checkout/js/action/select-payment-method':
			{
				'Anowave_Ec/js/action/select-payment-method':true
			},
			'Magento_Checkout/js/action/select-shipping-method':
			{
				'Anowave_Ec/js/action/select-shipping-method':true
			},
			'Magento_Checkout/js/action/place-order': 
			{
			    'Anowave_Ec/js/action/place-order': true
			},
            'Magento_Checkout/js/model/step-navigator': 
            {
                'Anowave_Ec/js/step-navigator/plugin': true
            },
            'Magento_Checkout/js/view/shipping-information': 
			{
			    'Anowave_Ec/js/view/shipping-information': true
			},
            'Magento_Customer/js/action/check-email-availability':
			{
				'Anowave_Ec/js/action/check-email-availability':true
			},
			'Magento_Checkout/js/sidebar':
            {
            	'Anowave_Ec/js/sidebar': true
            },
            'Magento_Catalog/js/price-box':
            {
            	'Anowave_Ec/js/price-box': true
            },
            'Magento_SalesRule/js/view/payment/discount':
            {
            	'Anowave_Ec/js/discount': true
            },
            'Magento_ConfigurableProduct/js/configurable':
            {
            	'Anowave_Ec/js/configurable': true
            },
    		'Magento_Swatches/js/swatch-renderer':
    		{
    			'Anowave_Ec/js/swatch-renderer': true
    		}
        }
    }
};