<?php

namespace Atome\MagentoPayment\Enum;

class ConfigFormName
{

    const ACTIVE = 'active';
    const COUNTRY = 'country';
    const API_ENV = 'api_env';
    const API_KEY = 'merchant_api_key';
    const API_SECRET = 'merchant_api_secret';
    const DEBUG_MODE = 'debug_mode';
    const EXCLUDE_CATEGORY = 'exclude_category';
    const ORDER_EMAIL_SEND_BY = 'order_email_send_by';
    const ORDER_STATUS = 'order_status';
    const NEW_ORDER_STATUS = 'new_order_status';
    const CLEAR_CART_WITHOUT_PAYING = 'clear_cart_without_paying';
    const MAX_SPEND = 'max_spend';
    const MIN_SPEND = 'min_spend';

    const PRICE_DIVIDER_PRODUCT_LIST = 'price_divider_product_list';
    const PRICE_DIVIDER_PRODUCT_DETAIL = 'price_divider_product_detail';
    const CANCEL_TIMEOUT = 'cancel_timeout';
    const SORT_ORDER = 'sort_order';
}
