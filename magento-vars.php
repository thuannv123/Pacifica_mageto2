<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Enable, adjust and copy this code for each store you run
 *
 * Store #0, default one
 *
 * if (isHttpHost("example.com")) {
 *    $_SERVER["MAGE_RUN_CODE"] = "default";
 *    $_SERVER["MAGE_RUN_TYPE"] = "store";
 * }
 *
 * @param string $host
 * @return bool
 */
function isHttpHost(string $host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return strpos($_SERVER['HTTP_HOST'], $host) !== false;
}

if (isHttpHost("thailand.coach.com") || isHttpHost("integration2-hohc4oi-i2idbbg26shzo.ap-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "coachth";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("ikonthailand.com") || isHttpHost("ikonthailand.integration2-hohc4oi-i2idbbg26shzo.ap-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "birkenstockth";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("keds.co.th") || isHttpHost("keds.co.th.integration2-hohc4oi-i2idbbg26shzo.ap-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "kedsth";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("aeo.th") || isHttpHost("aeo.th.integration2-hohc4oi-i2idbbg26shzo.ap-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "aeoth";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}
