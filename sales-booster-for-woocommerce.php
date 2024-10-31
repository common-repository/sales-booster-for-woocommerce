<?php
/*
 * Plugin Name:       Sales Booster for WooCommerce
 * Plugin URI:        https://www.themeum.com
 * Description:       Show your product sold notification to visitor. It will be increase your sell
 * Version:           1.0.0
 * Author:            Themeum
 * Author URI:        https://www.themeum.com
 * Text Domain:       sales-booster-for-woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Define the version
 */
define('WPNEO_SBWC_VERSION', '1.0.0');

/**
 * Define plugin's url path
 */
define('WPNEO_SBWC_DIR_URL', plugin_dir_url(__FILE__));

/**
 * Define plugin's physical path
 */
define('WPNEO_SBWC_DIR_PATH', plugin_dir_path(__FILE__));

define('WPNEO_SBWC_BASE_FILE', __FILE__);


// language
add_action( 'init', 'wcsb_language_load');
if ( ! function_exists('wcsb_language_load')){
    function wcsb_language_load(){
        $plugin_dir = basename(dirname(__FILE__))."/languages/";
        load_plugin_textdomain( 'sales-booster-for-woocommerce', false, $plugin_dir );
    }
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  ) {
    require WPNEO_SBWC_DIR_PATH.'inc/class-wcsb-base.php';
    register_activation_hook(__FILE__, 'wcsb_initial_setup');
}
