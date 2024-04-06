<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Isobar\CustomerDataMigration\Controller\Adminhtml\Job;

use Firebear\ImportExport\Controller\Adminhtml\Job\Save as SaveController;

/**
 * Class Save
 * @package Isobar\CustomerDataMigration\Controller\Adminhtml\Job
 */
class SaveJob extends SaveController
{

    /**
     * @var array
     */
    protected $additionalFields = [
        'associate_child_review_to_configurable_parent_product',
        'associate_child_review_to_bundle_parent_product',
        'platforms',
        'clear_attribute_value',
        'remove_product_association',
        'remove_product_website',
        'remove_all_customer_address',
        'remove_product_categories',
        'type_file',
        'configurable_switch',
        'configurable_create',
        'configurable_type',
        'configurable_field',
        'configurable_part',
        'configurable_symbols',
        'round_up_prices',
        'round_up_special_price',
        'copy_simple_value',
        'language',
        'reindex',
        'indexers',
        'generate_url',
        'enable_product_url_pattern',
        'product_url_pattern',
        'xml_switch',
        'root_category_id',
        'replace_default_value',
        'remove_current_mappings',
        'remove_images',
        'remove_images_dir',
        'remove_related_product',
        'remove_crosssell_product',
        'remove_upsell_product',
        'use_only_fields_from_mapping',
        'disable_products',
        'product_supplier',
        'send_email',
        'generate_shipment_by_track',
        'send_reset_password_after_import',
        'generate_invoice_by_track',
        'translate_attributes',
        'translate_store_ids',
        'translate_version',
        'translate_key',
        'translate_referer',
        'xlsx_sheet',
        'cron_groups',
        'email_type',
        'template',
        'receiver',
        'sender',
        'copy',
        'copy_method',
        'is_attach',
        'image_resize',
        'delete_file_after_import',
        'deferred_images',
        'cache_products',
        'increase_product_stock_by_qty',
        'archive_file_after_import',
        'include_option_id',
        'scan_directory',
        'stop_loop_on_fail'
    ];
}
