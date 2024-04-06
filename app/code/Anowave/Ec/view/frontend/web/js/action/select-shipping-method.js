/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * https://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2023 Anowave (https://www.anowave.com/)
 * @license  	https://www.anowave.com/license-agreement/
 */

define(['mage/utils/wrapper', 'Magento_Checkout/js/model/quote'], function(wrapper, quote)
{
    'use strict';

    return function(shippingMethod) 
    {
        return wrapper.wrap(shippingMethod, function (originalAction, method) 
        {
        	if ('undefined' !== typeof dataLayer && 'undefined' !== typeof AEC.Checkout.data)	
        	{
        		(function(dataLayer, shippingMethod)
        		{
        			var method = '';
        			
        			if (shippingMethod && shippingMethod.hasOwnProperty('method_title'))
        	    	{
        	    		method = shippingMethod.method_title;
        	    	}
        			else if (shippingMethod && shippingMethod.hasOwnProperty('carrier_title'))
        			{
        				method = shippingMethod.carrier_title;
        			}
        			
        			if ('undefined' !== typeof AEC && 'undefined' !== typeof AEC.Const)
        			{
        				AEC.Checkout.stepOption(AEC.Const.CHECKOUT_STEP_SHIPPING, method);
        			}
        			
        		})(dataLayer, method);
        	}
        	
            return originalAction(method);
        });
    };
});