<?php
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
 * @package   Mageplaza_SocialLogin
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Isobar\MageplazaSocialLogin\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\SocialLogin\Helper\Data as HelperData;

/**
 * Class Social
 *
 * @package Mageplaza\SocialLogin\Helper
 */
class Social extends \Mageplaza\SocialLogin\Helper\Social
{
    
    public function getSocialConfig($type)
    {
        $apiData = [
            'facebook' => ['trustForwarded' => false, 'scope' => 'email, public_profile, user_gender, user_birthday'],
            'Twitter'  => ['includeEmail' => true],
            'LinkedIn' => ['fields' => ['id', 'first-name', 'last-name', 'email-address']],
            'Google'   => ['scope' => 'email'],
            'Yahoo'    => ['scope' => 'profile'],
        ];

        if ($type && array_key_exists($type, $apiData)) {
            return $apiData[$type];
        }

        return [];
    }

}
