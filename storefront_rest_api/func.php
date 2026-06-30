<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Tools\SecurityHelper;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * Formats the price for further usage in REST API.
 *
 * @param float  $price    Price
 * @param string $currency Currency code
 *
 * @return array
 */
function fn_storefront_rest_api_format_price($price, $currency = CART_PRIMARY_CURRENCY)
{
    /** @var \Tygh\Tools\Formatter $formatter */
    $formatter = Tygh::$app['formatter'];

    $price = $formatter->asPrice($price, $currency);
    // FIXME: Refactor space replacement
    $price = str_replace('&nbsp;', ' ', $price);

    return array(
        'price'  => $price,
        'symbol' => Registry::get('currencies.' . $currency . '.symbol'),
    );
}

/**
 * Formats the prices of a product for their further usage in REST API.
 *
 * @param array  $product  Product data
 * @param string $currency Currency code
 *
 * @return array
 */
function fn_storefront_rest_api_format_product_prices($product, $currency = CART_PRIMARY_CURRENCY)
{
    $fields = array(
        'list_price',
        'price',
        'base_price',
        'original_price',
        'display_price',
        'discount',
        'subtotal',
        'display_subtotal',
    );

    foreach ($fields as $field) {
        if (isset($product[$field])) {
            $product[$field . '_formatted'] = fn_storefront_rest_api_format_price($product[$field], $currency);
        }
    }

    return $product;
}

/**
 * Formats the prices of a order for their further usage in REST API.
 *
 * @param array  $order    Order data
 * @param string $currency Currency code
 *
 * @return array
 */
function fn_storefront_rest_api_format_order_prices($order, $currency = CART_PRIMARY_CURRENCY)
{
    $fields = array(
        'total',
        'subtotal',
        'discount',
        'subtotal_discount',
        'payment_surcharge',
        'shipping_cost',
        'tax_subtotal',
        'display_subtotal',
        'display_shipping_cost',
    );

    foreach ($fields as $field) {
        if (isset($order[$field])) {
            $order[$field . '_formatted'] = fn_storefront_rest_api_format_price($order[$field], $currency);
        }
    }

    if (isset($order['tax_summary'])) {
        foreach ($order['tax_summary'] as $key => $value) {
            $order['tax_summary'][$key . '_formatted'] = fn_storefront_rest_api_format_price($value, $currency);
        }
    }

    if (!empty($order['products'])) {
        foreach ($order['products'] as &$product) {
            $product = fn_storefront_rest_api_format_product_prices($product, $currency);
        }
        unset($product);
    }

    if (!empty($order['product_groups'])) {
        foreach ($order['product_groups'] as &$group) {
            foreach ($group['products'] as &$product) {
                $product = fn_storefront_rest_api_format_product_prices($product, $currency);
            }
            foreach ($group['shippings'] as &$shipping) {
                $shipping['rate_formatted'] = fn_storefront_rest_api_format_price($shipping['rate'], $currency);
            }
            if (isset($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as &$chosen_shipping) {
                    $chosen_shipping['rate_formatted'] = fn_storefront_rest_api_format_price($chosen_shipping['rate'], $currency);
                }
            }
        }
        unset($group);
    }

    return $order;
}

/**
 * Formats the prices of products for their further usage in REST API.
 *
 * @param array  $products List of the product data
 * @param string $currency Currency code
 *
 * @return array
 */
function fn_storefront_rest_api_format_products_prices($products, $currency = CART_PRIMARY_CURRENCY)
{
    foreach ($products as &$product) {
        $product = fn_storefront_rest_api_format_product_prices($product, $currency);
    }
    unset($product);

    return $products;
}

/**
 * Gets current request headers
 *
 * return array
 */
function fn_storefront_rest_api_get_request_headers()
{
    $result = array();

    if (function_exists('getallheaders')) {
        $headers = getallheaders();

        foreach ($headers as $name => $value) {
            $result[$name] = $value;
        }
    } else {
        foreach ($_SERVER as $name => $value) {
            if (strncmp($name, 'HTTP_', 5) === 0) {
                $name = strtolower(str_replace('_', '-', substr($name, 5)));
                $result[$name] = $value;
            }
        }
    }

    foreach ($result as $name => $value) {
        $valid_name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
        unset($result[$name]);
        $result[$valid_name] = $value;
    }

    return $result;
}

/**
 * Handler: on add-on install
 */
function fn_storefront_rest_api_install()
{
    Settings::instance()->updateValue(
        'access_key',
        SecurityHelper::generateRandomString(),
        'storefront_rest_api'
    );
}

/**
 * Hook handler: on before api request handled
 *
 * @param \Tygh\Api $api
 * @param bool      $authorized
 */
function fn_storefront_rest_api_api_handle_request($api, &$authorized)
{
    if (!$authorized) {
        $headers = fn_storefront_rest_api_get_request_headers();

        $key = isset($headers['Storefront-Api-Access-Key']) ? $headers['Storefront-Api-Access-Key'] : null;

        if ($key === Registry::get('addons.storefront_rest_api.access_key')) {
            Registry::set('runtime.api.is_guest_access', true);
            $authorized = true;
        }
    }
}

/**
 * Hook handler: enables the token auth when the customer API access is disabled.
 *
 * @param \Tygh\Api $api  API instance
 * @param string[]  $auth Authetication data from request headers
 */
function fn_storefront_rest_api_api_get_user_data($api, &$auth)
{
    if (!empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW'])) {
        $auth['token'] = $_SERVER['PHP_AUTH_USER'];
        $auth['is_token_auth'] = true;
    }
}

/**
 * Hook handler: on after api checking access
 *
 * @param \Tygh\Api         $api
 * @param \Tygh\Api\AEntity $entity
 * @param string            $method_name
 * @param bool              $can_access
 */
function fn_storefront_rest_api_api_check_access($api, $entity, $method_name, &$can_access)
{
    if (!$can_access && Registry::get('runtime.api.is_guest_access')) {
        $reflection = new ReflectionClass($entity);
        $resource = fn_uncamelize($reflection->getShortName());
        $schema = fn_get_schema('storefront_rest_api', 'guest_access');

        if (isset($schema[$resource][$method_name])) {
            $can_access = $schema[$resource][$method_name];
        }
    }
}

/**
 * Filters out unnecessary profile fields from API response.
 *
 * @param array $field Field data
 *
 * @return array
 */
function fn_storefront_rest_api_filter_profile_fields($field)
{
    $fields = ['field_id', 'field_type', 'field_name', 'description', 'required', 'is_default', 'values', 'value'];

    $field = array_intersect_key($field, array_combine($fields, $fields));

    return $field;
}