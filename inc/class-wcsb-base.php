<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//Inlcude general functions
require WPNEO_SBWC_DIR_PATH.'inc/wcsb_functions.php';
require WPNEO_SBWC_DIR_PATH.'inc/class-wcsb-options.php';

if ( ! class_exists('WPNEO_Sbwc_Base')) {

    class WPNEO_Sbwc_Base{
        public static function init() {
            $enable_purchase_notifier = get_wcsb_option( 'enable_purchase_notifier' );
            if ( $enable_purchase_notifier !== 'on'){
                return false;
            }
            return new self();
        }

        public function __construct(){
            add_action( 'wp_enqueue_scripts', array($this, 'wpneo_enqueue_frontend_script') ); //Add frontend js and css
            add_action('wp_footer', array($this, 'append_sold_product_html'));
        }

        /**
         * Registering necessary js and css
         * @frontend
         */
        public function wpneo_enqueue_frontend_script(){
            $wcsb_options = wp_json_encode(get_option('wcsb_options'));

            wp_enqueue_style('wcsb-toastr-css', WPNEO_SBWC_DIR_URL . 'assets/css/wcsb.css');
            wp_enqueue_script('wcsb-main-js', WPNEO_SBWC_DIR_URL . 'assets/js/wcsb.js', array('jquery'), time(), true);
            wp_localize_script('wcsb-main-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'wcsb_options' => $wcsb_options));
        }

        public function last_sold_products($limit = 50, $show_data_for = 'last_six_months', $product_ids = array()){
            global $woocommerce, $wpdb;
            if ( ! class_exists('WC_Admin_Report')){
                include_once($woocommerce->plugin_path().'/includes/admin/reports/class-wc-admin-report.php');
            }
            // Calculate report start and end dates (timestamps)
            switch ($show_data_for) {
                case 'today':
                    $end_date = strtotime('midnight', current_time('timestamp'));
                    $start_date = $end_date;
                    break;
                case 'yesterday':
                    $end_date = strtotime('midnight', current_time('timestamp'));
                    $start_date = $end_date;
                    break;
                case 'last_seven_days':
                    $end_date = strtotime('midnight', current_time('timestamp'));
                    $start_date = $end_date - (86400 * 6);
                    break;
                case 'last_six_months':
                    $end_date = strtotime('midnight', current_time('timestamp'));
                    $start_date = $end_date - (86400 * 29 * 6);
                    break;
                default: // 30 days is the default
                    $end_date = strtotime('midnight', current_time('timestamp'));
                    $start_date = $end_date - (86400 * 29);
            }


            $wc_report = new WC_Admin_Report();
            $wc_report->start_date = $start_date;
            $wc_report->end_date = $end_date;

            $where_meta = array();

            if (count($product_ids)){
                $where_meta[] = array(
                    'type' => 'order_item_meta',
                    'meta_key' => '_product_id',
                    'operator' => 'in',
                    'meta_value' => $product_ids
                );
            }

            // Get report data

            // Avoid max join size error
            $wpdb->query('SET SQL_BIG_SELECTS=1');

            // Prevent plugins from overriding the order status filter
            add_filter('woocommerce_reports_order_statuses', 'wcsb_get_sold_order_status', 9999);

            // Based on woocoommerce/includes/admin/reports/class-wc-report-sales-by-product.php
            $sold_products = $wc_report->get_order_report_data(array(
                'data' => array(
                    'ID' => array(
                        'type'     => 'post_data',
                        'name'     => 'order_id',
                        'function' => '',
                    ),
                    'post_date' => array(
                        'type'     => 'post_data',
                        'function' => '',
                        'name'     => 'post_date',
                    ),

                    '_product_id' => array(
                        'type' => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function' => '',
                        'name' => 'product_id'
                    ),
                    '_qty' => array(
                        'type' => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function' => 'SUM',
                        'name' => 'quantity'
                    ),
                    '_line_subtotal' => array(
                        'type' => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function' => 'SUM',
                        'name' => 'gross'
                    ),
                    '_line_total' => array(
                        'type' => 'order_item_meta',
                        'order_item_type' => 'line_item',
                        'function' => 'SUM',
                        'name' => 'gross_after_discount'
                    ),

                    'order_item_id' => array(
                        'type' => 'order_item',
                        'order_item_type' => 'line_item',
                        'function' => '',
                        'name' => 'order_item_id'
                    ),
                ),

                'query_type' => 'get_results',
                'group_by' => 'order_id',
                'where_meta' => $where_meta,
                'order_by' => 'order_item_id DESC',
                'limit' => $limit,
                'filter_range' => true,
                'order_types' => wc_get_order_types('order_count'),
                'order_status' =>  wcsb_get_sold_order_status()
            ));
            remove_filter('woocommerce_reports_order_statuses', 'wcsb_get_sold_order_status', 9999);

            $rows = array();

            // Output report rows
            foreach ($sold_products as $product) {
                $row = array();

                $row['order_id'] = $product->order_id;
                $row['post_date'] = $product->post_date;
                $row['product_id'] = $product->product_id;
                $row['campaign_title'] = html_entity_decode(get_the_title($product->product_id));
                $row['total_raised_count'] = $product->quantity;
                $row['total_gross'] = $product->gross;
                $row['total_gross_after_discount'] = $product->gross_after_discount;
                $row['order_item_id'] = $product->order_item_id;
                $rows[] = $row;
            }

            return $rows;
        }

        public function initial_setup(){
            $options = '{"enable_purchase_notifier":"on","positions":"toast-top-right","sticky_mode":"absolute","show_time_life":"5","showMethod":"fadeIn","hideMethod":"hide"}';
            //$options = 'a:6:{s:24:"enable_purchase_notifier";s:2:"on";s:9:"positions";s:15:"toast-top-right";s:11:"sticky_mode";s:8:"absolute";s:14:"show_time_life";s:1:"5";s:10:"showMethod";s:6:"fadeIn";s:10:"hideMethod";s:4:"hide";}';
            $options = json_decode($options, true);
            add_option('wcsb_options', $options);
        }


        /**
         * append_sold_product_html()
         *
         * print html to footer
         */
        public function append_sold_product_html(){
            $position_class = get_wcsb_option('positions');
            $sticky_mode = get_wcsb_option('sticky_mode');
            if( ! $position_class){
                $position_class = ' toast-top-right ';
            }
            if( ! $sticky_mode){
                $sticky_mode = ' absolute ';
            }
            $sticky_mode = 'wcsb_'.$sticky_mode;

            if (is_product()){
                //Get current displaying product
                global $post;
                $sold_products = wpneopm()->last_sold_products(1, null, array($post->ID));
                //Get another last sold product if current product have no data
                if ( ! $sold_products){
                    $sold_products = wpneopm()->last_sold_products(1, null);
                }
            }else{
                $sold_products = wpneopm()->last_sold_products(1, null);
            }

            if (count($sold_products)){
                ?>
                <div class="wcsb-item-wrapper <?php echo $position_class; ?> <?php echo $sticky_mode ?>" style="display: none;">
                    <ul class="wcsb-sold-products">
                        <?php
                        foreach ($sold_products as $c_product){
                            $sold_time_ago = human_time_diff(strtotime($c_product['post_date']), current_time('timestamp'));
                            ?>
                            <li>
                                <a href="<?php echo get_the_permalink($c_product['product_id']); ?>">
                                    <?php echo get_the_post_thumbnail(get_post($c_product['product_id'])); ?>

                                    <div class="descr-box">
                                        <h4 class="product-title"><?php echo get_the_title($c_product['product_id']); ?></h4>
                                        <span class="sold-time-ago"><?php echo sprintf(__('Sold %s ago', 'sales-booster-for-woocommerce'), $sold_time_ago); ?></span>
                                        <span class="view_btn"><?php _e('View Product', 'sales-booster-for-woocommerce'); ?></span>
                                    </div>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                    <a href="javascript:;" class="toastCloseBtn">&times;</a>
                </div>
                <?php
            }
        }

    }
}
WPNEO_Sbwc_Base::init();


//Set a function to access the base class
function wpneopm(){
    return new WPNEO_Sbwc_Base();
}