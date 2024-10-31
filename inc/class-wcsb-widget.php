<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists('Wpneopn_Widget')) {

    class Wpneopn_Widget extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            parent::__construct(
                'purchase_notifier', // Base ID
                esc_html__( 'Purchase Notifier', 'sales-booster-for-woocommerce' ), // Name
                array( 'description' => esc_html__( 'Purchase notifier widget help you to notify about last purhcase', 'sales-booster-for-woocommerce' ), ) // Args
            );
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget( $args, $instance ) {
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }

            //Get settings
            $amount_of_last_purchase = $instance['amount_of_last_purchase'];
            $show_data_for  = $instance['show_data_for'];

            $data_period = '';
            switch ($show_data_for) {
                case 'today':
                    $data_period = __('today', 'sales-booster-for-woocommerce');
                    break;
                case 'yesterday':
                    $data_period = __('yesterday', 'sales-booster-for-woocommerce');
                    break;
                case 'last_seven_days':
                    $data_period = __('in last 7 days', 'sales-booster-for-woocommerce');
                    break;
                case 'last_six_months':
                    $data_period = __('in last 6 months', 'sales-booster-for-woocommerce');
                    break;
                default: // 30 days is the default
                    $data_period = __('in last 30 days', 'sales-booster-for-woocommerce');
            }

            if (is_product()){
                global $post;
                $current_product_sold = wpneopm()->last_sold_products($amount_of_last_purchase, $show_data_for, array($post->ID));
                if (count($current_product_sold)){
                    foreach ($current_product_sold as $c_product);
                    //print_r($c_product);
                    echo '<p class="pn-single-product-sold-widget-info"><a href="'.get_the_permalink($c_product['product_id']).'">'.$c_product['campaign_title'].' </a> '.__('has been sold ', 'sales-booster-for-woocommerce').' '.$c_product['total_raised_count'].' '.__('times', 'sales-booster-for-woocommerce').' '.$data_period.'</p>';
                }
            }

            $sold_products = wpneopm()->last_sold_products($amount_of_last_purchase, $show_data_for);
            if (count($sold_products)){
                $html = '<ul class="purchase-notifier">';
                foreach ($sold_products as $product){
                    $product_url = get_permalink($product['product_id']);
                    $sold_time_ago = human_time_diff( strtotime($product['post_date']), current_time('timestamp') ).' '.__('ago', 'sales-booster-for-woocommerce');
                    $html .= "<li><a href='{$product_url}'>{$product['campaign_title']}</a> <br /> <small> ".__('has been sold', 'sales-booster-for-woocommerce')." {$sold_time_ago}</small></li>";
                }
                $html .= '</ul>';

                echo $html;


            }

            echo $args['after_widget'];
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form( $instance ) {
            $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'sales-booster-for-woocommerce' );
            $amount_of_last_purchase = ! empty( $instance['amount_of_last_purchase'] ) ? $instance['amount_of_last_purchase'] : '';
            $show_data_for = ! empty( $instance['show_data_for'] ) ? $instance['show_data_for'] : '';
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'sales-booster-for-woocommerce' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>


            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'amount_of_last_purchase' ) ); ?>"><?php esc_attr_e('Show Last Purchase Limit:', 'sales-booster-for-woocommerce' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'amount_of_last_purchase' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'amount_of_last_purchase' ) ); ?>" type="number" value="<?php echo esc_attr( $amount_of_last_purchase ); ?>">
            </p>


            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_data_for' ) ); ?>"><?php esc_attr_e('Show data for:', 'sales-booster-for-woocommerce' ); ?></label>

                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_data_for' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_data_for' ) ); ?>" >
                    <?php
                    $options = array(
                        'today'             => esc_html__( 'Today', 'sales-booster-for-woocommerce' ),
                        'yesterday'         => esc_html__( 'Yesterday', 'sales-booster-for-woocommerce' ),
                        'last_seven_days'   => esc_html__( 'Last Seven Days', 'sales-booster-for-woocommerce' ),
                        'last_thirty_days'  => esc_html__( 'Last 30 days', 'sales-booster-for-woocommerce' ),
                        'last_six_months'   => esc_html__( 'Last 6 Months', 'sales-booster-for-woocommerce' ),
                    );
                    foreach ( $options as $id => $label ) { ?>
                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $show_data_for, $id, true ); ?>>
                            <?php echo strip_tags( $label ); ?>
                        </option>
                    <?php } ?>
                </select>

            </p>

            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update( $new_instance, $old_instance ) {
            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['amount_of_last_purchase'] = ( ! empty( $new_instance['amount_of_last_purchase'] ) ) ? strip_tags( $new_instance['amount_of_last_purchase'] ) : '';
            $instance['show_data_for'] = ( ! empty( $new_instance['show_data_for'] ) ) ? strip_tags( $new_instance['show_data_for'] ) : '';

            return $instance;
        }

    } // class Wpneopn_Widget


    // register Foo_Widget widget
    function register_wcsb_widget() {
        register_widget( 'Wpneopn_Widget' );
    }
    //add_action( 'widgets_init', 'register_wcsb_widget' );

}
