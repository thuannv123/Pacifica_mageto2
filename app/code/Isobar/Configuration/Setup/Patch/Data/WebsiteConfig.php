<?php

namespace Isobar\Configuration\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class WebsiteConfig implements DataPatchInterface
{
    /**
     * Default scope type
     */
    const SCOPE_WEBSITE_TH = 'coachth';

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface;

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var WebsiteRepositoryInterface
     */
    protected WebsiteRepositoryInterface $websiteRepository;

    /**
     * @param ModuleDataSetupInterface                              $moduleDataSetup
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\App\Config\Storage\WriterInterface $writerInterface,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->writerInterface = $writerInterface;
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * @return string[]
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
     *
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        //Scope default
        $this->writerInterface->save("general/country/default", 'TH');
        $this->writerInterface->save("general/country/destinations", 'TH');
        $this->writerInterface->save("general/store_information/name", 'Pacifica');
        $this->writerInterface->save("general/store_information/hours", '24 hours');
        $this->writerInterface->save("general/store_information/country_id", 'TH');
        $this->writerInterface->save("general/store_information/region_id", '1179');
        $this->writerInterface->save("general/store_information/city", 'Bangkok');
        $this->writerInterface->save("web/url/use_store", '1');
        $this->writerInterface->save("web/url/redirect_to_base", '1');
        $this->writerInterface->save("web/seo/use_rewrites", '1');
        $this->writerInterface->save("web/secure/use_in_adminhtml", '1');
        $this->writerInterface->save("web/secure/use_in_frontend", '1');
        $this->writerInterface->save("currency/options/base", 'THB');
        $this->writerInterface->save("currency/options/default", 'THB');
        $this->writerInterface->save("currency/options/allow", 'THB');
        $this->writerInterface->save("contact/email/email_template", 'contact_email_email_template');
        $this->writerInterface->save("contact/contact/enabled", '1');
        $this->writerInterface->save("cms/pagebuilder/enabled", '1');
        $this->writerInterface->save("shipping/origin/country_id", 'TH');
        $this->writerInterface->save("multishipping/options/checkout_multiple", '0');
        $this->writerInterface->save("tax/classes/shipping_tax_class", '2');
        $this->writerInterface->save("tax/classes/wrapping_tax_class", '2');
        $this->writerInterface->save("tax/classes/default_product_tax_class", '2');
        $this->writerInterface->save("tax/classes/default_customer_tax_class", '3');
        $this->writerInterface->save("tax/calculation/algorithm", 'TOTAL_BASE_CALCULATION');
        $this->writerInterface->save("tax/calculation/based_on", 'shipping');
        $this->writerInterface->save("tax/calculation/price_includes_tax", '1');
        $this->writerInterface->save("tax/calculation/shipping_includes_tax", '1');
        $this->writerInterface->save("tax/calculation/discount_tax", '1');
        $this->writerInterface->save("tax/calculation/apply_after_discount", '1');
        $this->writerInterface->save("tax/calculation/apply_tax_on", '0');
        $this->writerInterface->save("tax/defaults/country", 'TH');
        $this->writerInterface->save("tax/defaults/postcode", '*');
        $this->writerInterface->save("tax/defaults/region", '0');
        $this->writerInterface->save("tax/display/type", '2');
        $this->writerInterface->save("tax/display/shipping", '2');
        $this->writerInterface->save("tax/cart_display/price", '2');
        $this->writerInterface->save("tax/cart_display/subtotal", '2');
        $this->writerInterface->save("tax/cart_display/shipping", '2');
        $this->writerInterface->save("tax/cart_display/grandtotal", '1');
        $this->writerInterface->save("tax/cart_display/full_summary", '1');
        $this->writerInterface->save("tax/sales_display/price", '2');
        $this->writerInterface->save("tax/sales_display/subtotal", '2');
        $this->writerInterface->save("tax/sales_display/shipping", '2');
        $this->writerInterface->save("tax/sales_display/grandtotal", '1');
        $this->writerInterface->save("tax/sales_display/full_summary", '1');
        $this->writerInterface->save("tax/sales_display/zero_tax", '0');
        $this->writerInterface->save("carriers/freeshipping/active", '1');
        $this->writerInterface->save("carriers/freeshipping/title", 'Free Shipping');
        $this->writerInterface->save("carriers/freeshipping/name", 'Free');
        $this->writerInterface->save("carriers/freeshipping/free_shipping_subtotal", '5000');
        $this->writerInterface->save("carriers/freeshipping/tax_including", '1');
        $this->writerInterface->save("carriers/freeshipping/specificerrmsg", 'This shipping method is not available.  please contact us.');
        $this->writerInterface->save("carriers/freeshipping/sallowspecific", '1');
        $this->writerInterface->save("carriers/freeshipping/specificcountry", 'TH');
        $this->writerInterface->save("carriers/freeshipping/showmethod", '0');
        $this->writerInterface->save("carriers/freeshipping/sort_order", '0');
        $this->writerInterface->save("carriers/instore/active", '0');
        $this->writerInterface->save("carriers/instore/name", 'In store Pickup');
        $this->writerInterface->save("carriers/instore/title", 'In store Pickup');
        $this->writerInterface->save("carriers/instore/specificerrmsg", 'In-Store Delivery is not available. Please contact us.');
        $this->writerInterface->save("carriers/flatrate/active", '0');
        $this->writerInterface->save("cataloginventory/options/can_subtract", '1');
        $this->writerInterface->save("cataloginventory/options/can_back_in_stock", '1');
        $this->writerInterface->save("cataloginventory/options/show_out_of_stock", '1');
        $this->writerInterface->save("cataloginventory/options/stock_threshold_qty", '0');
        $this->writerInterface->save("cataloginventory/options/display_product_stock_status", '1');
        $this->writerInterface->save("cataloginventory/item_options/manage_stock", '1');
        $this->writerInterface->save("cataloginventory/item_options/backorders", '0');
        $this->writerInterface->save("cataloginventory/item_options/max_sale_qty", '10000');
        $this->writerInterface->save("cataloginventory/item_options/min_qty", '0');
        $this->writerInterface->save("cataloginventory/item_options/min_sale_qty", '1');
        $this->writerInterface->save("cataloginventory/item_options/notify_stock_qty", '3');
        $this->writerInterface->save("cataloginventory/item_options/enable_qty_increments", '1');
        $this->writerInterface->save("cataloginventory/item_options/qty_increments", '1');
        $this->writerInterface->save("cataloginventory/item_options/auto_return", '0');
        $this->writerInterface->save("cataloginventory/source_selection_distance_based/provider", 'google');
        $this->writerInterface->save("cataloginventory/source_selection_distance_based_google/mode", 'driving');
        $this->writerInterface->save("cataloginventory/source_selection_distance_based_google/value", 'distance');
        $this->writerInterface->save("customer/account_share/scope", '0');
        $this->writerInterface->save("customer/create_account/auto_group_assign", '1');
        $this->writerInterface->save("customer/create_account/confirm", '1');
        $this->writerInterface->save("customer/create_account/default_group", '1');
        $this->writerInterface->save("customer/create_account/email_identity", 'general');
        $this->writerInterface->save("customer/create_account/email_confirmation_template", 'customer_create_account_email_confirmation_template');
        $this->writerInterface->save("customer/create_account/email_confirmed_template", 'customer_create_account_email_confirmed_template');
        $this->writerInterface->save("customer/create_account/email_no_password_template", 'customer_create_account_email_no_password_template');
        $this->writerInterface->save("customer/create_account/email_template", 'customer_create_account_email_template');
        $this->writerInterface->save("customer/create_account/generate_human_friendly_id", '1');
        $this->writerInterface->save("customer/create_account/vat_frontend_visibility", '1');
        $this->writerInterface->save("customer/password/autocomplete_on_storefront", '1');
        $this->writerInterface->save("customer/password/forgot_email_identity", 'support');
        $this->writerInterface->save("customer/password/forgot_email_template", 'customer_password_forgot_email_template');
        $this->writerInterface->save("customer/password/lockout_failures", '10');
        $this->writerInterface->save("customer/password/lockout_threshold", '10');
        $this->writerInterface->save("customer/password/max_number_password_reset_requests", '5');
        $this->writerInterface->save("customer/password/minimum_password_length", '8');
        $this->writerInterface->save("customer/password/min_time_between_password_reset_requests", '10');
        $this->writerInterface->save("customer/password/password_reset_protection_type", '1');
        $this->writerInterface->save("customer/password/remind_email_template", 'customer_password_remind_email_template');
        $this->writerInterface->save("customer/password/required_character_classes_number", '4');
        $this->writerInterface->save("customer/password/reset_link_expiration_period", '1');
        $this->writerInterface->save("customer/password/reset_password_template", 'customer_password_reset_password_template');
        $this->writerInterface->save("customer/account_information/change_email_template", 'customer_account_information_change_email_template');
        $this->writerInterface->save("customer/account_information/change_email_and_password_template", 'customer_account_information_change_email_and_password_template');
        $this->writerInterface->save("customer/address/company_show", 'opt');
        $this->writerInterface->save("customer/address/telephone_show", 'req');
        $this->writerInterface->save("customer/address/prefix_options", 'Change Email (Default)');
        $this->writerInterface->save("customer/address/suffix_options", 'Change Email and Password (Default)');
        $this->writerInterface->save("customer/magento_customerbalance/email_identity", 'general');
        $this->writerInterface->save("customer/magento_customerbalance/email_template", 'customer_magento_customerbalance_email_template');
        $this->writerInterface->save("customer/magento_customerbalance/is_enabled", '0');
        $this->writerInterface->save("customer/magento_customerbalance/refund_automatically", '0');
        $this->writerInterface->save("customer/magento_customerbalance/show_history", '0');
        $this->writerInterface->save("customer/startup/redirect_dashboard", '1');
        $this->writerInterface->save("customer/address_templates/html", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{depend subdistrict}}{{var subdistrict}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{var country}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}');
        $this->writerInterface->save("customer/address_templates/oneline", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var subdistrict}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}');
        $this->writerInterface->save("customer/address_templates/pdf", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{depend subdistrict}}{{var subdistrict}}{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|');
        $this->writerInterface->save("customer/address_templates/text", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{depend subdistrict}}{{var subdistrict}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{var country}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}');
        $this->writerInterface->save("customer/magento_customersegment/is_enabled", '1');
        $this->writerInterface->save("customer/captcha/enable", '0');
        $this->writerInterface->save("catalog/frontend/list_mode", 'grid-list');
        $this->writerInterface->save("catalog/frontend/grid_per_page_values", '16,24,32');
        $this->writerInterface->save("catalog/frontend/grid_per_page", '24');
        $this->writerInterface->save("catalog/frontend/list_per_page_values", '10,15,20,25');
        $this->writerInterface->save("catalog/frontend/list_per_page", '10');
        $this->writerInterface->save("catalog/frontend/default_sort_by", 'position');
        $this->writerInterface->save("catalog/frontend/list_allow_all", '1');
        $this->writerInterface->save("catalog/frontend/remember_pagination", '1');
        $this->writerInterface->save("catalog/frontend/flat_catalog_category", '0');
        $this->writerInterface->save("catalog/frontend/flat_catalog_product", '0');
        $this->writerInterface->save("catalog/frontend/swatches_per_product", '20');
        $this->writerInterface->save("catalog/frontend/show_swatches_in_product_list", '1');
        $this->writerInterface->save("catalog/review/active", '1');
        $this->writerInterface->save("catalog/review/allow_guest", '1');
        $this->writerInterface->save("catalog/productalert/allow_price", '1');
        $this->writerInterface->save("catalog/productalert/email_price_template", 'catalog_productalert_email_price_template');
        $this->writerInterface->save("catalog/productalert/allow_stock", '1');
        $this->writerInterface->save("catalog/productalert/email_stock_template", 'catalog_productalert_email_stock_template');
        $this->writerInterface->save("catalog/productalert/email_identity", 'general');
        $this->writerInterface->save("catalog/productalert_cron/error_email_identity", 'general');
        $this->writerInterface->save("catalog/productalert_cron/error_email_template", 'catalog_productalert_cron_error_email_template');
        $this->writerInterface->save("catalog/product_video/play_if_base", '1');
        $this->writerInterface->save("catalog/product_video/show_related", '1');
        $this->writerInterface->save("catalog/product_video/video_auto_restart", '1');
        $this->writerInterface->save("catalog/recently_products/synchronize_with_backend", '1');
        $this->writerInterface->save("catalog/recently_products/scope", 'website');
        $this->writerInterface->save("catalog/recently_products/viewed_count", '5');
        $this->writerInterface->save("catalog/recently_products/compared_count", '5');
        $this->writerInterface->save("catalog/recently_products/recently_viewed_lifetime", '1000');
        $this->writerInterface->save("catalog/recently_products/recently_compared_lifetime", '1000');
        $this->writerInterface->save("catalog/price/scope", '1');
        $this->writerInterface->save("catalog/price/default_product_price", '0');
        $this->writerInterface->save("catalog/layered_navigation/display_product_count", '1');
        $this->writerInterface->save("catalog/layered_navigation/price_range_calculation", 'auto');
        $this->writerInterface->save("catalog/search/min_query_length", '3');
        $this->writerInterface->save("catalog/search/max_count_cacheable_search_terms", '100');
        $this->writerInterface->save("catalog/search/max_query_length", '128');
        $this->writerInterface->save("catalog/search/enable_eav_indexer", '1');
        $this->writerInterface->save("catalog/search/autocomplete_limit", '8');
        $this->writerInterface->save("catalog/search/search_recommendations_enabled", '1');
        $this->writerInterface->save("catalog/search/search_recommendations_count", '5');
        $this->writerInterface->save("catalog/search/search_recommendations_count_results_enabled", '1');
        $this->writerInterface->save("catalog/search/search_suggestion_enabled", '1');
        $this->writerInterface->save("catalog/search/search_suggestion_count", '2');
        $this->writerInterface->save("catalog/search/search_suggestion_count_results_enabled", '0');
        $this->writerInterface->save("catalog/navigation/max_depth", '0');
        $this->writerInterface->save("catalog/seo/search_terms", '1');
        $this->writerInterface->save("catalog/seo/product_url_suffix", '.html');
        $this->writerInterface->save("catalog/seo/category_url_suffix", '.html');
        $this->writerInterface->save("catalog/seo/product_use_categories", '1');
        $this->writerInterface->save("catalog/seo/save_rewrites_history", '1');
        $this->writerInterface->save("catalog/seo/generate_category_product_rewrites", '1');
        $this->writerInterface->save("catalog/seo/title_separator", '-');
        $this->writerInterface->save("catalog/seo/category_canonical_tag", '1');
        $this->writerInterface->save("catalog/seo/product_canonical_tag", '1');
        $this->writerInterface->save("catalog/downloadable/order_item_status", '9');
        $this->writerInterface->save("catalog/downloadable/downloads_number", '0');
        $this->writerInterface->save("catalog/downloadable/shareable", '1');
        $this->writerInterface->save("catalog/downloadable/samples_title", 'Samples');
        $this->writerInterface->save("catalog/downloadable/links_title", 'Links');
        $this->writerInterface->save("catalog/downloadable/links_target_new_window", '1');
        $this->writerInterface->save("catalog/downloadable/content_disposition", 'inline');
        $this->writerInterface->save("catalog/downloadable/disable_guest_checkout", '1');
        $this->writerInterface->save("catalog/custom_options/use_calendar", '1');
        $this->writerInterface->save("catalog/custom_options/date_fields_order", 'd,m,y');
        $this->writerInterface->save("catalog/magento_catalogevent/enabled", '1');
        $this->writerInterface->save("catalog/magento_catalogevent/lister_output", '1');
        $this->writerInterface->save("catalog/magento_catalogevent/lister_widget_limit", '5');
        $this->writerInterface->save("catalog/magento_catalogevent/lister_widget_scroll", '2');

        //Scope website TH
        $websiteId = $this->websiteRepository->get(self::SCOPE_WEBSITE_TH)->getId();
        $this->writerInterface->save("general/country/default", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/country/destinations", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/country/allow", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/locale/timezone", 'Asia/Bangkok', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/locale/weight_unit", 'kgs', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/name", 'COACH Thailand', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/phone", '02 034 9770', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/postcode", '10310', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/hours", '24 hours', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/country_id", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/region_id", '1179', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/city", 'Bangkok', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/street_line1", '2032 Ital-Thai Tower, 5th Floor', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/street_line2", 'New Petchburi Road, Bangkapi, Huaykwang', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("general/store_information/merchant_vat_number", '0105546125208', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("web/url/use_store", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("web/url/redirect_to_base", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("web/seo/use_rewrites", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("web/secure/use_in_frontend", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("currency/options/base", 'THB', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("currency/options/default", 'THB', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("currency/options/allow", 'THB', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_general/name", 'Coach Thailand Admin', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_general/email", 'admin@coach.co.th', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_sales/name", 'Coach Thailand Sales Support', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_sales/email", 'sales@coach.co.th', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_support/name", 'Coach Thailand Customer Support', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("trans_email/ident_support/email", 'support@coach.co.th', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("contact/email/sender_email_identity", 'general', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("contact/email/email_template", 'contact_email_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("contact/contact/enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("shipping/origin/country_id", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("multishipping/options/checkout_multiple", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/classes/shipping_tax_class", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/classes/wrapping_tax_class", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/classes/default_product_tax_class", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/classes/default_customer_tax_class", '3', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/algorithm", 'TOTAL_BASE_CALCULATION', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/based_on", 'shipping', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/price_includes_tax", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/shipping_includes_tax", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/discount_tax", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/apply_after_discount", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/calculation/apply_tax_on", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/defaults/country", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/defaults/postcode", '*', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/defaults/region", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/display/type", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/display/shipping", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/price", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/subtotal", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/shipping", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/grandtotal", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/full_summary", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/cart_display/zero_tax", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/price", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/subtotal", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/shipping", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/grandtotal", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/full_summary", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("tax/sales_display/zero_tax", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/active", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/title", 'Free Shipping', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/name", 'Free', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/free_shipping_subtotal", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/tax_including", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/specificerrmsg", 'This shipping method is not available.  please contact us.', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/sallowspecific", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/specificcountry", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/showmethod", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/freeshipping/sort_order", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/instore/active", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/instore/name", 'In store Pickup', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/instore/title", 'In store Pickup', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/instore/specificerrmsg", 'In-Store Delivery is not available. Please contact us.', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("carriers/flatrate/active", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/enabled_on_product", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/use_store_address", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/store_name", 'COACH Thailand', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/address", '2032 Ital-thai Tower, 5th Floor', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/address1", 'New Petchburi Road, Bangkapi, Huaykwang', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/region_id", '1179', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/zip", '10310', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("sales/magento_rma/country_id", 'TH', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("cataloginventory/options/stock_threshold_qty", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("cataloginventory/options/display_product_stock_status", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/auto_group_assign", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/confirm", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/default_group", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_domain", 'coach.co.th', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_identity", 'general', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_confirmation_template", 'customer_create_account_email_confirmation_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_confirmed_template", 'customer_create_account_email_confirmed_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_no_password_template", 'customer_create_account_email_no_password_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/email_template", 'customer_create_account_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/generate_human_friendly_id", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/create_account/vat_frontend_visibility", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/autocomplete_on_storefront", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/forgot_email_identity", 'support', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/forgot_email_template", 'customer_password_forgot_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/lockout_failures", '10', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/lockout_threshold", '10', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/max_number_password_reset_requests", '5', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/minimum_password_length", '8', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/min_time_between_password_reset_requests", '10', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/password_reset_protection_type", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/remind_email_template", 'customer_password_remind_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/required_character_classes_number", '4', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/reset_link_expiration_period", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/password/reset_password_template", 'customer_password_reset_password_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/account_information/change_email_template", 'customer_account_information_change_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/account_information/change_email_and_password_template", 'customer_account_information_change_email_and_password_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address/company_show", 'opt', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address/telephone_show", 'req', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address/prefix_options", 'Change Email (Default)', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address/suffix_options", 'Change Email and Password (Default)', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/magento_customerbalance/email_identity", 'general', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/magento_customerbalance/email_template", 'customer_magento_customerbalance_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/magento_customerbalance/show_history", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/startup/redirect_dashboard", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address_templates/html", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{depend subdistrict}}{{var subdistrict}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{var country}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address_templates/oneline", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}, {{var street}}, {{var subdistrict}}, {{var city}}, {{var region}} {{var postcode}}, {{var country}}', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address_templates/pdf", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{depend subdistrict}}{{var subdistrict}}{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{var country}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/address_templates/text", '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{depend subdistrict}}{{var subdistrict}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{var country}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("customer/captcha/enable", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/list_mode", 'grid-list', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/grid_per_page_values", '16,24,32', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/grid_per_page", '24', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/list_per_page_values", '10,15,20,25', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/list_per_page", '10', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/default_sort_by", 'position', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/list_allow_all", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/remember_pagination", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/flat_catalog_category", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/flat_catalog_product", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/swatches_per_product", '20', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/frontend/show_swatches_in_product_list", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/review/active", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/review/allow_guest", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert/allow_price", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert/email_price_template", 'catalog_productalert_email_price_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert/allow_stock", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert/email_stock_template", 'catalog_productalert_email_stock_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert_cron/error_email_identity", 'general', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/productalert_cron/error_email_template", 'catalog_productalert_cron_error_email_template', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/product_video/play_if_base", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/product_video/show_related", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/product_video/video_auto_restart", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/synchronize_with_backend", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/scope", 'website', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/viewed_count", '5', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/compared_count", '5', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/recently_viewed_lifetime", '1000', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/recently_products/recently_compared_lifetime", '1000', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/price/scope", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/price/default_product_price", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/layered_navigation/display_product_count", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/layered_navigation/price_range_calculation", 'auto', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/min_query_length", '3', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/max_count_cacheable_search_terms", '100', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/max_query_length", '128', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/enable_eav_indexer", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/autocomplete_limit", '8', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_recommendations_enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_recommendations_count", '5', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_recommendations_count_results_enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_suggestion_enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_suggestion_count", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/search/search_suggestion_count_results_enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/navigation/max_depth", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/search_terms", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/product_url_suffix", '.html', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/category_url_suffix", '.html', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/product_use_categories", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/save_rewrites_history", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/generate_category_product_rewrites", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/title_separator", '-', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/category_canonical_tag", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/seo/product_canonical_tag", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/order_item_status", '9', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/downloads_number", '0', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/shareable", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/samples_title", 'Samples', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/links_title", 'Links', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/links_target_new_window", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/content_disposition", 'inline', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/downloadable/disable_guest_checkout", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/custom_options/use_calendar", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/custom_options/date_fields_order", 'd,m,y', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/magento_catalogevent/enabled", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/magento_catalogevent/lister_output", '1', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/magento_catalogevent/lister_widget_limit", '5', ScopeInterface::SCOPE_WEBSITES, $websiteId);
        $this->writerInterface->save("catalog/magento_catalogevent/lister_widget_scroll", '2', ScopeInterface::SCOPE_WEBSITES, $websiteId);

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
