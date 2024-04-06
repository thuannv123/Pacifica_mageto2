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
var config = {
    'map': {
        '*': {
            requireCookie: 'Mageplaza_GdprPro/js/require-cookie'
        }
    },
    config: {
        'mixins': {
            'Magento_Customer/js/address': {
                'Mageplaza_GdprPro/js/check-password': true
            }
        }
    }
};