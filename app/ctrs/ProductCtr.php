<?php
/**
 * Created by PhpStorm.
 * User: mrybai,lamnguyenit
 * Date: 10/10/16
 * Time: 6:07 PM
 */

namespace YBAI\Ctrs;

use YBAIConnector;


class ProductCtr
{

    function __construct()
    {
        add_action('wp_ajax_ybai_synchronize_all', array(&$this, 'synchronize_all'));
    }

    function synchronize_all()
    {
        $con = new YBAIConnector();
        $total_products = $this->count_products();
        $channel_name = strtoupper(str_replace('.', '', $this->domain_name()));
        $this->sync_products($con, $channel_name);
        echo json_encode(array(
            'success' => true,
            'message' => "Danh sách sản phẩm ($total_products) đã đồng bộ!"
        ));
        die();
    }

    function sync_products($con, $channel_name, $paged = 1, $posts_per_page = 100)
    {
        $args = array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
        );

        $products = get_posts($args);

        if ($products) {
            $products_data = array();
            foreach ($products as  $product) {
                $item = wc_get_product($product->ID);
                $product_data = array(
                    'price' => $item->get_price() ?: 0,
                    'sku' => $channel_name . '-WOO-' . $item->get_id(),
                    'name' => $item->get_name(),
                    'is_active' => $item->get_status() == 'publish' ? 1 : 0,
                    'description' => $item->get_description()
                );
                $products_data['products'][] = $product_data;
            }

            $con->synchronize($products_data);
            $next_page = $paged + 1;
            $this->sync_products($con, $channel_name, $next_page);
        }
        return true;
    }

    function count_products()
    {

        $args = array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $products_query = new \WP_Query($args);
        return $products_query->found_posts;
    }


    function domain_name()
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
        return $str[0] ? $str[0] : 'localhost';
    }
}
