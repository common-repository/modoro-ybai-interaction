<?php
/**
 * Created by Modoro.
 * User: modoro
 * Date: 10/10/2024
 * Time: 5:53 PM
 */

namespace YBAI\Boot;
class FrontEnd{

    function __construct()
    {
        add_action('wp_head',array($this,'javascript_variables'));
    }

    function javascript_variables(){
        $meta = get_option( 'ybai_head_script', '' );
        if ( $meta != '' ) {
            echo $meta, "\n";
        }

        echo $meta, "\n";
    }
}