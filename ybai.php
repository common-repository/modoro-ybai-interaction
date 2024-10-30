<?php
/*
Plugin Name: Ybai Interaction
Plugin URI: https://ybai.vn
Description: Ybai Affiliate, Giải pháp  X2 doanh số đơn giản
Version: 1.4.0
Author: Modoro
*/

require_once "loader.php";

use \YBAI\Boot\BackEnd;
use \YBAI\Boot\FrontEnd;
use \YBAI\Ctrs\MainCtr;
use \YBAI\Ctrs\ProductCtr;
use \YBAI\Boot\YBAIHook;

if(!class_exists('ybaiApp')) {
    class ybaiApp {
        function __construct() {

            register_activation_hook (__FILE__, array ($this, 'plugin_activate'));
            register_deactivation_hook( __FILE__, array ($this,'plugin_deactivate'));

            new YBAIHook();
            new BackEnd();
            new FrontEnd();
            new MainCtr();
            new ProductCtr();

        }

        function plugin_activate(){
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
        function plugin_deactivate() {

        }
    }
}

new ybaiApp();
