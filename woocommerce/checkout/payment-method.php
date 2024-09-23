<?php if ( isset( $gateway ) && $gateway ) : ?>
    <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
    <label for="payment_method_<?php echo esc_html( $gateway->get_title() ); ?>">
        <?php echo esc_html( $gateway->get_title() ); ?>
    </label>
<?php else : ?>
    <p><?php _e( 'Payment method unavailable.', 'woocommerce' ); ?></p>
<?php endif; ?>
