<?php

namespace Isobar\BirkenstockSetup\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ConfigurationBirkenstock implements DataPatchInterface
{
    protected ModuleDataSetupInterface $moduleDataSetup;

    protected WriterInterface $configWriter;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param WriterInterface $configWriter
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        WriterInterface $configWriter
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configWriter = $configWriter;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return ConfigurationBirkenstock|void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $listData = $this->prepareData();
        foreach ($listData as $data) {
            $this->configWriter->save(
                $data['path'],
                $data['value'],
                $data['scope'],
                $data['scope_id']
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array[]
     */
    private function prepareData()
    {
        return array(
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/zero_tax",
                "value" => "0"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/full_summary",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/grandtotal",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/shipping",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/subtotal",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/cart_display/price",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/display/shipping",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/display/type",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/defaults/postcode",
                "value" => "*"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/defaults/region",
                "value" => "0"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/defaults/country",
                "value" => "TH"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/cross_border_trade_enabled",
                "value" => "0"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/apply_tax_on",
                "value" => "0"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/discount_tax",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/shipping_includes_tax",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/price_includes_tax",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/based_on",
                "value" => "shipping"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/calculation/algorithm",
                "value" => "TOTAL_BASE_CALCULATION"
            ),
            array(
                "scope" => "default",
                "scope_id" => 0,
                "path" => "tax/classes/default_product_tax_class",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/classes/wrapping_tax_class",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/classes/shipping_tax_class",
                "value" => "2"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/vertex_delivery_terms/override",
                "value" => "[]"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/vertex_flexfields/date",
                "value" => "[{\"field_id\":\"1\",\"field_source\":\"\"},{\"field_id\":\"2\",\"field_source\":\"\"},{\"field_id\":\"3\",\"field_source\":\"\"},{\"field_id\":\"4\",\"field_source\":\"\"},{\"field_id\":\"5\",\"field_source\":\"\"}]"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/vertex_flexfields/numeric",
                "value" => "[{\"field_id\":\"1\",\"field_source\":\"\"},{\"field_id\":\"2\",\"field_source\":\"\"},{\"field_id\":\"3\",\"field_source\":\"\"},{\"field_id\":\"4\",\"field_source\":\"\"},{\"field_id\":\"5\",\"field_source\":\"\"},{\"field_id\":\"6\",\"field_source\":\"\"},{\"field_id\":\"7\",\"field_source\":\"\"},{\"field_id\":\"8\",\"field_source\":\"\"},{\"field_id\":\"9\",\"field_source\":\"\"},{\"field_id\":\"10\",\"field_source\":\"\"}]"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/vertex_flexfields/code",
                "value" => "[{\"field_id\":\"1\",\"field_source\":\"\"},{\"field_id\":\"2\",\"field_source\":\"\"},{\"field_id\":\"3\",\"field_source\":\"\"},{\"field_id\":\"4\",\"field_source\":\"\"},{\"field_id\":\"5\",\"field_source\":\"\"},{\"field_id\":\"6\",\"field_source\":\"\"},{\"field_id\":\"7\",\"field_source\":\"\"},{\"field_id\":\"8\",\"field_source\":\"\"},{\"field_id\":\"9\",\"field_source\":\"\"},{\"field_id\":\"10\",\"field_source\":\"\"},{\"field_id\":\"11\",\"field_source\":\"\"},{\"field_id\":\"12\",\"field_source\":\"\"},{\"field_id\":\"13\",\"field_source\":\"\"},{\"field_id\":\"14\",\"field_source\":\"\"},{\"field_id\":\"15\",\"field_source\":\"\"},{\"field_id\":\"16\",\"field_source\":\"\"},{\"field_id\":\"17\",\"field_source\":\"\"},{\"field_id\":\"18\",\"field_source\":\"\"},{\"field_id\":\"19\",\"field_source\":\"\"},{\"field_id\":\"20\",\"field_source\":\"\"},{\"field_id\":\"21\",\"field_source\":\"\"},{\"field_id\":\"22\",\"field_source\":\"\"},{\"field_id\":\"23\",\"field_source\":\"\"},{\"field_id\":\"24\",\"field_source\":\"\"},{\"field_id\":\"25\",\"field_source\":\"\"}]"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "tax/vertex_settings/allowed_countries",
                "value" => null
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "multishipping/options/checkout_multiple",
                "value" => "0"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "contact/email/email_template",
                "value" => "contact_email_email_template"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "contact/email/sender_email_identity",
                "value" => "sales"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "contact/contact/enabled",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_support/email",
                "value" => "birkenstock.th@pacifica.co.th"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_support/name",
                "value" => "Birkenstock Thailand Customer Support"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_sales/email",
                "value" => "birkenstock.th@pacifica.co.th"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_sales/name",
                "value" => "Birkenstock Thailand Sales Support"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_general/email",
                "value" => "birkenstock.th@pacifica.co.th"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "trans_email/ident_general/name",
                "value" => "Birkenstock Thailand Admin"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "mpcurrencyformatter/general/currencies",
                "value" => "{\"THB\":{\"show_symbol\":\"before\",\"group_separator\":\",\",\"decimal_number\":\"0\",\"minus_sign\":\"-\",\"symbol\":\"\\u0e3f\",\"decimal_separator\":\".\",\"show_minus\":\"before_symbol\"}}"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "mpcurrencyformatter/general/enabled",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "currency/options/allow",
                "value" => "THB"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "currency/options/default",
                "value" => "THB"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "currency/options/base",
                "value" => "THB"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 7,
                "path" => "web/default/cms_no_cookies",
                "value" => "enable-cookies|19"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 7,
                "path" => "web/default/cms_no_route",
                "value" => "no-route|18"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 4,
                "path" => "web/default/cms_no_cookies",
                "value" => "enable-cookies"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 4,
                "path" => "web/default/cms_no_route",
                "value" => "no-route"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/default/show_cms_breadcrumbs",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/default/no_route",
                "value" => "cms/noroute/index"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/default/front",
                "value" => "cms"
            ),
            array(
                "scope" => "default",
                "scope_id" => 0,
                "path" => "web/secure/use_in_adminhtml",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/secure/use_in_frontend",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/secure/base_url",
                "value" => "https://ikonthailand.com/"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/unsecure/base_url",
                "value" => "https://ikonthailand.com/"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/seo/use_rewrites",
                "value" => "1"
            ),
            array(
                "scope" => "default",
                "scope_id" => 0,
                "path" => "web/url/use_store",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "web/url/redirect_to_base",
                "value" => "1"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/merchant_vat_number",
                "value" => "0-1055-57054-49-8"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/street_line2",
                "value" => "New Petchburi Road, Bangkapi, Huaykwang"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/street_line1",
                "value" => "2032 ltal-Thai Tower, 5th Floor"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/city",
                "value" => null
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/postcode",
                "value" => "10310"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/region_id",
                "value" => null
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/country_id",
                "value" => null
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/hours",
                "value" => null
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/phone",
                "value" => "097-182-5785"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/store_information/name",
                "value" => "Birkenstock Thailand"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 7,
                "path" => "general/locale/code",
                "value" => "en_US"
            ),
            array(
                "scope" => "stores",
                "scope_id" => 4,
                "path" => "general/locale/code",
                "value" => "th_TH"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/locale/weight_unit",
                "value" => "kgs"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/locale/timezone",
                "value" => "Asia/Bangkok"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/country/destinations",
                "value" => "TH"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/country/allow",
                "value" => "TH"
            ),
            array(
                "scope" => "websites",
                "scope_id" => 4,
                "path" => "general/country/default",
                "value" => "TH"
            )
        );
    }
}
