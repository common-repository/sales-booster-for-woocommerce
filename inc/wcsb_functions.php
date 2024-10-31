<?php

if ( ! function_exists('wcsb_get_sold_order_status')){
    function wcsb_get_sold_order_status(){
        return array('completed');
    }
}

/**
 * Setup some initial settings
 */
if ( ! function_exists('wcsb_initial_setup')){
    function wcsb_initial_setup(){
        $options = unserialize('a:6:{s:24:"enable_purchase_notifier";s:2:"on";s:9:"positions";s:15:"toast-top-right";s:11:"sticky_mode";s:8:"absolute";s:14:"show_time_life";s:1:"5";s:10:"showMethod";s:6:"fadeIn";s:10:"hideMethod";s:4:"hide";}');
        add_option('wcsb_options', $options);
    }
}

if ( ! function_exists('wcsb_view_raw')) {
    function wcsb_view_raw($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}