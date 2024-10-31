<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Start Class
if ( ! class_exists( 'WPNEOPN_Options' ) ) {
    class WPNEOPN_Options {

        /**
         * Start things up
         *
         * @since 1.0.0
         */
        public function __construct() {
            // We only need to register the admin panel on the back-end
            if ( is_admin() ) {
                add_action( 'admin_menu', array( 'WPNEOPN_Options', 'add_admin_menu' ) );
                add_action( 'admin_init', array( 'WPNEOPN_Options', 'register_settings' ) );
            }
        }

        /**
         * Returns single theme option
         *
         * @since 1.0.0
         */
        public static function get_wcsb_option( $id ) {
            $options = get_option( 'wcsb_options' );
            if ( isset( $options[$id] ) ) {
                return $options[$id];
            }
        }

        /**
         * Add sub menu page
         *
         * @since 1.0.0
         */
        public static function add_admin_menu() {
            add_menu_page(
                esc_html__( 'Sales Booster for WooCommerce', 'sales-booster-for-woocommerce' ), esc_html__( 'Sales Booster for WooCommerce', 'sales-booster-for-woocommerce' ), 'manage_options', 'wcsb-settings', array( 'WPNEOPN_Options', 'create_admin_page' )
            );
        }

        /**
         * Register a setting and its sanitization callback.
         *
         * @since 1.0.0
         */
        public static function register_settings() {
            register_setting( 'wcsb_options', 'wcsb_options', array( 'WPNEOPN_Options', 'sanitize' ) );
        }

        /**
         * Sanitization callback
         *
         * @since 1.0.0
         */
        public static function sanitize( $options ) {
            // If we have options lets sanitize them
            if ( $options ) {
                // Checkbox
                if ( ! empty( $options['enable_purchase_notifier'] ) ) {
                    $options['enable_purchase_notifier'] = 'on';
                } else {
                    unset( $options['enable_purchase_notifier'] ); // Remove from options if not checked
                }
                //positions
                if ( ! empty( $options['positions'] ) ) {
                    $options['positions'] = sanitize_text_field( $options['positions'] );
                }
                if ( ! empty( $options['sticky_mode'] ) ) {
                    $options['sticky_mode'] = sanitize_text_field( $options['sticky_mode'] );
                }
                if ( ! empty( $options['show_time_life'] ) ) {
                    $options['show_time_life'] = sanitize_text_field( $options['show_time_life'] );
                }
                if ( ! empty( $options['showMethod'] ) ) {
                    $options['showMethod'] = sanitize_text_field( $options['showMethod'] );
                }
                if ( ! empty( $options['hideMethod'] ) ) {
                    $options['hideMethod'] = sanitize_text_field( $options['hideMethod'] );
                }
            }

            // Return sanitized options
            return $options;
        }

        /**
         * Settings page output
         *
         * @since 1.0.0
         */
        public static function create_admin_page() { ?>

            <div class="wrap">
                <h1><?php esc_html_e( 'Sales Booster for WooCommerce Options', 'sales-booster-for-woocommerce' ); ?></h1>

                <form method="post" action="options.php">
                    <?php settings_fields( 'wcsb_options' ); ?>

                    <table class="form-table wpex-custom-admin-login-table">

                        <?php // Checkbox example ?>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Enable Sales Booster for WooCommerce', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <label>
                                    <?php $value = self::get_wcsb_option( 'enable_purchase_notifier' ); ?>
                                    <input type="checkbox" name="wcsb_options[enable_purchase_notifier]" <?php checked( $value, 'on' ); ?>> <?php esc_html_e( 'It will be decided whether this plugin will be active or not', 'sales-booster-for-woocommerce' ); ?>
                                </label>
                            </td>
                        </tr>
                    </table>

                    <h3><?php _e('Toast Notification Option', 'sales-booster-for-woocommerce'); ?></h3>

                    <table class="form-table wpex-custom-admin-login-table">

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Position', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <?php $value = self::get_wcsb_option( 'positions' ); ?>

                                <div class="controls">
                                    <label class="radio">
                                        <input name="wcsb_options[positions]" value="toast-top-right"   <?php checked( $value, 'toast-top-right' ); ?>  type="radio"><?php _e('Top Right', 'sales-booster-for-woocommerce'); ?>
                                    </label>
                                    <label class="radio">
                                        <input name="wcsb_options[positions]" value="toast-bottom-right"  <?php checked( $value, 'toast-bottom-right' ); ?>  type="radio"><?php _e('Bottom Right', 'sales-booster-for-woocommerce'); ?>
                                    </label>
                                    <label class="radio">
                                        <input name="wcsb_options[positions]" value="toast-bottom-left" <?php checked( $value, 'toast-bottom-left' ); ?>  type="radio"><?php _e('Bottom Left', 'sales-booster-for-woocommerce'); ?>
                                    </label>
                                    <label class="radio">
                                        <input name="wcsb_options[positions]" value="toast-top-left" <?php checked( $value, 'toast-top-left' ); ?>  type="radio"><?php _e('Top Left', 'sales-booster-for-woocommerce'); ?>
                                    </label>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Sticky mode', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <?php $value = self::get_wcsb_option( 'sticky_mode' ); ?>

                                <div class="controls">
                                    <label class="radio">
                                        <input name="wcsb_options[sticky_mode]" value="absolute"   <?php checked( $value, 'absolute' ); ?>  type="radio">Disable
                                    </label>
                                    <label class="radio">
                                        <input name="wcsb_options[sticky_mode]" value="fixed"  <?php checked( $value, 'fixed' ); ?>  type="radio">Enable
                                    </label>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Show time life (in sec)', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <?php $value = self::get_wcsb_option( 'show_time_life' ); ?>

                                <div class="controls">
                                    <input type="number" name="wcsb_options[show_time_life]" value="<?php echo $value ?>" class="regular-text">
                                </div>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Show Method', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <?php $value = self::get_wcsb_option('showMethod' ); ?>
                                <select name="wcsb_options[showMethod]">
                                    <?php
                                    $options = array(
                                        'show' => 'Show',
                                        'fadeIn' => 'Fade In',
                                        'slideDown' => 'Slide Down',
                                    );
                                    foreach ( $options as $id => $label ) { ?>
                                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                                            <?php echo strip_tags( $label ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e( 'Hide Method', 'sales-booster-for-woocommerce' ); ?></th>
                            <td>
                                <?php $value = self::get_wcsb_option('hideMethod' ); ?>
                                <select name="wcsb_options[hideMethod]">
                                    <?php
                                    $options = array(
                                        'hide' => 'Hide',
                                        'fadeOut' => 'Fade Out',
                                        'slideUp' => 'Slide Up',
                                    );
                                    foreach ( $options as $id => $label ) { ?>
                                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                                            <?php echo strip_tags( $label ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        
                    </table>

                    <?php submit_button(); ?>

                </form>

            </div><!-- .wrap -->
        <?php }
    }
}
new WPNEOPN_Options();

// Helper function to use in wpneo Sales Booster for WooCommerce to return a theme option value
if ( ! function_exists('get_wcsb_option')){
    function get_wcsb_option( $id = '' ) {
        return WPNEOPN_Options::get_wcsb_option( $id );
    }
}
