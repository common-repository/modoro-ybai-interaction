<?php
/**
 * Created by Modoro.
 * User: modoro
 * Date: 10/12/16
 * Time: 1:49 PM
 */

namespace YBAI\Boot;
use YBAI\Views\ConfigsView;
use YBAI\Views\ConnectView;

class BackEnd
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_init', array($this, 'admin_init_hooking'));
        add_action('admin_enqueue_scripts', array($this, 'admin_script'));
        add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'display_ybai_order_meta'), 10, 1);
    }

    public function add_menu()
    {
        add_menu_page('YBAI Setting', 'Ybai Affiliate', 'administrator', 'ybai-connect', array($this, 'ybai_connect'), YBAI_URL . '/assets/images/ybai.svg', 80);
        add_submenu_page('ybai-connect', 'Kết Nối', 'Kết Nối', 'administrator', 'ybai-connect', array($this, 'ybai_connect'));
        add_submenu_page('ybai-connect', 'Cài Đặt', 'Cài Đặt', 'administrator', 'ybai-config', array($this, 'ybai_configs'));
    }

    public function admin_script()
    {
        wp_enqueue_script('ybai-lib', YBAI_URL . '/assets/js/ybai.js', false);
        wp_enqueue_script('ybai-config', YBAI_URL . '/assets/js/ybai-config.js', true);
        wp_localize_script('ybai-config', 'ybai_ajax_object', array(
            'admin_url' => admin_url('admin-ajax.php'),
            'option_url' => admin_url('options.php')
        ));
    }

    public function ybai_connect()
    {
        return ConnectView::content();
    }

    public function ybai_configs()
    {
        ConfigsView::content();
    }

    public function admin_init_hooking()
    {
        register_setting('ybai-connect', 'ybai_access_key');
        register_setting('ybai-connect', 'ybai_secret_key');
        register_setting('ybai-configs', 'ybai_head_script');
    }

    public function display_ybai_order_meta($order)
    {
        $ybai_sent = get_post_meta($order->get_id(), '_ybai_sent', true);
        $ybai_code = get_post_meta($order->get_id(), '_ybai_code', true);
        $ybai_affiliate_code = get_post_meta($order->get_id(), '_ybai_affiliate_code', true);
        $ybai_error_message = get_post_meta($order->get_id(), '_ybai_error_message', true);
        
        echo '<div class="ybai_order_data_column">';
        echo '<h4>YBAI Information</h4>';
        if ($ybai_sent) {
            echo '<p><strong>Sent To YBAI:</strong> Yes</p>';
            echo '<p><strong>YBAI Order Code:</strong> ' . esc_html($ybai_code) . '</p>';
            echo '<p><strong>YBAI Affiliate Code:</strong> ' . esc_html($ybai_affiliate_code) . '</p>';
        } else {
            echo '<p><strong>Sent To YBAI:</strong> No</p>';
            echo '<p><strong>YBAI Error Message:</strong> ' . esc_html($ybai_error_message) . '</p>';
        }
        echo '</div>';
    }
}
