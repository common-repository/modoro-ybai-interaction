<?php
/**
 * Created by MrDevNET.
 * User: mrdevnet
 * Date: 8/26/2018 AD
 * Time: 10:55 PM
 */

namespace YBAI\Ctrs;
use YBAIConnector;
use ybaiApp;

class MainCtr
{
    function __construct()
    {
        add_action( 'wp_ajax_ybai_connect', array(&$this, 'connect'));
    }


    function connect(){
        $update_version = new ybaiApp();
        $update_version->plugin_activate();

        $data = sanitize_post($_POST['data']);
        update_option('ybai_access_key',$data['ybai_access_key']);
        update_option('ybai_secret_key',$data['ybai_secret_key']);

        $con = new YBAIConnector();
        $result = $con->connect();

        echo json_encode($result);

        die();
    }
}
