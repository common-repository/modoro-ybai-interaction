<?php
/**
 * Created by Modoro.
 * User: modoro
 * Date: 8/30/2024 AD
 * Time: 10:27 PM
 */

namespace YBAI\Boot;

use \YBAIConnector;

class YBAIHook
{
    function __construct()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

            add_action('woocommerce_checkout_order_processed', array(&$this, 'woocommerce_checkout_order_processed'), 10, 3);

            add_action('woocommerce_order_status_changed', array(&$this, 'woocommerce_order_status_changed'), 10, 3);

            add_action('woocommerce_update_order', array(&$this, 'handle_order_update'), 10, 1);

            add_action('publish_product', array(&$this, 'synchronize_list_product'), 10, 1);
            
            add_action('wp_insert_post', array(&$this, 'add_insert_post'), 10, 3);

        }
    }
    
    public function handle_order_update($order_id) {
        $order = wc_get_order($order_id);
        $original_order_data = get_post_meta($order_id, '_original_order_data', true);
    
        if (!$original_order_data) {
            update_post_meta($order_id, '_original_order_data', $this->get_order_data($order));
            return;
        }
    
        $current_order_data = $this->get_order_data($order);
        $changed_data = $this->get_changed_data($original_order_data, $current_order_data);

        if (!empty($changed_data)) {

            $api_order_data = $this->prepare_order_data($order);
            $con = new YBAIConnector();
            $result = $con->update_order($order_id,$api_order_data);
        }
    
        update_post_meta($order_id, '_original_order_data', $current_order_data);
    }

    function add_insert_post($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id) || $update) {
            return;
        }

        $post_type = get_post_type($post_id);
        if ('shop_order' == $post_type) {
            if (isset($_COOKIE['ybai_referral'])) {
                update_post_meta($post->ID, '_ybai_affiliate', sanitize_text_field($_COOKIE['ybai_referral']));
            }
        }
    }

    function woocommerce_checkout_order_processed($order_id, $posted_data, $order)
    {
        if (get_post_meta($order_id, '_ybai_sent', true) != true) {
            if (isset($_COOKIE['ybai_affiliate']) && !empty($_COOKIE['ybai_affiliate'])) {
                $ybai_id = sanitize_text_field($_COOKIE['ybai_affiliate']);
            }

            $api_order_data = $this->prepare_order_data($order);
            $api_order_data['ybai_id'] = isset($ybai_id) ? $ybai_id : '';
            $api_order_data['status_id'] = 11;
            $api_order_data['pay_type_id'] = 5;
            $api_order_data['shop_order_id'] = $order_id;

            $api_order_data['meta_data']['cus'] = $this->prepare_customer_meta_data($posted_data);

            $con = new YBAIConnector();
            $result = $con->order($api_order_data);
            
            if ($result['success']) {
                update_post_meta($order_id, '_ybai_sent', $result['success']);
                update_post_meta($order_id, '_ybai_code', $result['data']['code']);
                update_post_meta($order_id, '_ybai_affiliate_code', $api_order_data['ybai_id']);
            } else {
                update_post_meta($order_id, '_ybai_error_message', $result['message']);
            }
            
            update_post_meta($order_id, '_original_order_data', $this->get_order_data($order));
        }
    }

    function woocommerce_order_status_changed($order_id, $old_status, $new_status)
    {
        if ($old_status != $new_status) {
            $con = new YBAIConnector();
            switch ($new_status) {
                case 'pending':
                    $con->change_order_status($order_id, 11);
                    break;
                case 'on-hold':
                case 'processing':
                    $con->change_order_status($order_id, 24);
                    break;
                case 'completed':
                    $con->change_order_status($order_id, 12);
                    break;
                case 'failed':
                case 'trash':
                case 'cancelled':
                    $con->change_order_status($order_id, 13);
                    break;
                default:
                    break;
            }
        }
    }

    function synchronize_list_product($ID)
    {
        $channel_name = $this->_get_channel_domain_name();
        $product_woo = wc_get_product($ID);
        $product['price'] = sanitize_text_field($_POST['_regular_price']);
        $product['description'] = wp_trim_words($product_woo->get_description(), 4000, '...');
        $product['sku'] = $channel_name . '-WOO' . $ID;
        $product['image'] = "";
        $product['name'] = $product_woo->get_name();
        $product['channel_id'] = get_option('channel_id');
        $product['products'][] = $product;
        $con = new YBAIConnector();
        $con->synchronize($product);
    }

    function prepare_order_data($order)
    {
        $api_order_data = [
            "source" => 'channel',
            "note" => $order->get_customer_note(),
            "customer" => array(
                "name" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                "email" => $order->get_billing_email(),
                "mobile" => $order->get_billing_phone()
            ),
        ];

        $coupon_items = $order->get_items('coupon');
        $items = $order->get_items();
        $item_count = $order->get_item_count();
        $channel_name = $this->_get_channel_domain_name();

        foreach ($items as $key => $item) {
            $product = $item->get_product();
            $sku = $channel_name . '-WOO-' . $product->get_id();
            $name = $product->get_name();
            $price = $product->get_price();
            $quantity = $item->get_quantity();
            $item_discount = $this->calculate_item_discount($coupon_items, $price, $quantity, $item_count);

            $api_order_data['order_lines'][] = array(
                "name" => $name,
                "sku" => $sku,
                "price" => $price,
                "discount" => $item_discount,
                "quantity" => $quantity
            );
        }

        return $api_order_data;
    }

    function calculate_item_discount($coupon_items, $price, $quantity, $item_count)
    {
        $item_discount = 0;

        foreach ($coupon_items as $coupon_item) {
            $coupon = new \WC_Coupon($coupon_item['name']);
            $discount_type = $coupon->get_discount_type();
            $discount_amount = $coupon->get_amount();

            switch ($discount_type) {
                case 'percent':
                    $item_discount = round($price * $quantity * ($discount_amount / 100));
                    break;
                case 'fixed_product':
                    $item_discount += round($discount_amount * $quantity);
                    break;
                case 'fixed_cart':
                    $item_discount += round($quantity * $discount_amount / $item_count);
                    break;
                default:
                    break;
            }
        }

        return $item_discount;
    }

    function prepare_customer_meta_data($posted_data)
    {
        $ignore_posted_data = array(
            'payment_method',
            'shipping_method',
            'billing_first_name',
            'billing_last_name',
            'billing_phone',
            'billing_email',
            'billing_country',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_country',
            'order_comments'
        );

        foreach ($posted_data as $key => $value) {
            if (!empty($value) and !in_array($key, $ignore_posted_data)) {
                $customer_info[] = array(
                    "key" => $key,
                    "value" => $value,
                    "label" => $this->format_post_data($key)
                );
            }
        }

        return isset($customer_info) ? $customer_info : [];
    }

    function _get_channel_domain_name()
    {
        $website = $_SERVER['SERVER_NAME'];
        $patterns = array(
            '/http:\/\//',
            '/https:\/\//',
            '/www./',
            '/ /',
        );
        $str = preg_replace($patterns, '', $website);
        $str = explode('/', $str);
        return strtoupper(str_replace('.', '', $str[0] ? $str[0] : 'localhost'));
    }

    function format_post_data($key)
    {
        switch ($key) {
            case 'billing_company':
                $key = '[Hóa đơn] Công ty';
                break;

            case 'billing_address_1':
                $key = '[Hóa đơn] Địa chỉ';
                break;

            case 'billing_address_2':
                $key = '[Hóa đơn] Địa chỉ 2';
                break;

            case 'billing_city':
                $key = '[Hóa đơn] Tỉnh/Tp';
                break;

            case 'billing_postcode':
                $key = '[Hóa đơn] Mã bưu điện';
                break;

            case 'shipping_address_1':
                $key = '[Vận chuyển] Địa chỉ';
                break;

            case 'shipping_address_2':
                $key = '[Vận chuyển] Địa chỉ 2';
                break;

            case 'shipping_city':
                $key = '[Vận chuyển] Tỉnh/Tp';
                break;

            case 'shipping_postcode':
                $key = '[Vận chuyển] Mã bưu điện';
                break;
        }
        return $key;
    }

    private function get_changed_data($original_data, $current_data) {
        $changed_data = [];
        foreach ($current_data as $key => $value) {
            if (!isset($original_data[$key]) || $original_data[$key] !== $value) {
                $changed_data[$key] = $value;
            }
        }
        return $changed_data;
    }

    private function get_order_data($order) {
        $data = [
            "customer" => [
                "name" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                "email" => $order->get_billing_email(),
                "mobile" => $order->get_billing_phone(),
            ],
            "items" => [],
        ];
    
        $items = $order->get_items();
        foreach ($items as $item) {
            $product = $item->get_product();
            $data['items'][] = [
                "name" => $product->get_name(),
                "price" => $product->get_price(),
                "quantity" => $item->get_quantity(),
            ];
        }
    
        return $data;
    }
}
