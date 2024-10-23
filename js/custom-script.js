jQuery(document).ready(function ($) {
    // Listen for changes in payment method
    $("form.checkout").on(
        "change",
        'input[name="payment_method"]',
        function () {
            update_totals_based_on_payment_method();
        }
    );

    function updatePaymentMethodClass() {
        $('input[name="payment_method"]:checked').closest('li').addClass('payment-active');
    }

    function updateBodyClass() {
        var selectedMethod = $('input[name="payment_method"]:checked').val();

        // Remove previous payment method body classes
        $('body').removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-method-\S+/g) || []).join(' ');
        });

        if (selectedMethod === 'blockonomics') {
            $('body').addClass('payment-method-blockonomics');
        } else {
            $('body').addClass('payment-method-card');
        }
    }

    // Add class based on selected payment type
    function updatePaymentTypeClass() {
        var selectedPaymentType = $('input[name="payment_type"]:checked').val();

        // Remove previous payment type body classes
        $('body').removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-type-\S+/g) || []).join(' ');
        });

        // Add new class based on selected payment type
        if (selectedPaymentType) {
            $('body').addClass('payment-type-' + selectedPaymentType);
        }
    }

    // Initial call to set the classes on page load
    updateBodyClass();
    updatePaymentTypeClass();

    // Change event to update class when payment method is changed
    $('form.woocommerce-checkout').on('change', 'input[name="payment_method"]', function () {
        updatePaymentMethodClass();
        updateBodyClass();
    });

    // Listen for changes in payment type
    $('.payment-box-wrapper').on('change', 'input[name="payment_type"]', function () {
        updatePaymentTypeClass();
    });

    $(document.body).on('updated_checkout', function () {
        // Reapply the class to the selected payment method
        updatePaymentMethodClass();
    });

    function update_totals_based_on_payment_method() {
        var selected_payment_method = $('input[name="payment_method"]:checked').val();
        $(".bitcoin-payments-message-below, .normal-payments-message, .blockonomics-payments-message").hide();
        
        if (selected_payment_method === "blockonomics") {
            $(".bitcoin-payments-message-below").show();
            $(".blockonomics-payments-message").show();
        } else {
            $(".normal-payments-message").show();
        }

        $.ajax({
            url: wc_checkout_params.ajax_url,
            type: "POST",
            data: {
                action: "update_cart_totals_on_payment_method_change",
                payment_method: selected_payment_method,
            },
            success: function (response) {
                $(".checkout-left").find(".total-section").html(response);
            },
            error: function (xhr, status, error) {
                console.log("An error occurred: " + error);
            },
        });

        $.ajax({
            url: wc_checkout_params.ajax_url,
            type: "POST",
            data: {
                action: "update_table_on_payment_method_change",
                payment_method: selected_payment_method,
            },
            success: function (response) {
                $(".checkout-left").find(".product-table").html(response);
            },
            error: function (xhr, status, error) {
                console.log("An error occurred: " + error);
            },
        });
    }
    
    $(document).on('click', '.increase-qty', function() {
        var qtyInput = $(this).siblings('.qty-input');
        var currentVal = parseInt(qtyInput.val());
        if (!isNaN(currentVal)) {
            qtyInput.val(currentVal + 1).trigger('change');
        }
    });

    // Decrease Quantity
    $(document).on('click', '.decrease-qty', function() {
        var qtyInput = $(this).siblings('.qty-input');
        var currentVal = parseInt(qtyInput.val());
        if (!isNaN(currentVal) && currentVal > 1) {
            qtyInput.val(currentVal - 1).trigger('change');
        }
    });

    // Update cart on quantity change
    $(document).on('change', '.qty-input', function() {
        var qty = $(this).val();
        var cartItemKey = $(this).attr('name').match(/\[(.*?)\]/)[1]; // Extract the cart item key

        var data = {
            action: 'woocommerce_update_cart_item_qty',
            cart_item_key: cartItemKey,
            quantity: qty
        };

        $.ajax({
            type: 'POST',
            url: wc_checkout_params.ajax_url,
            data: data,
            success: function(response) {
                // Optionally, you can refresh the table or cart totals here
                location.reload(); // Refresh page to reflect new totals
            }
        });
    });
});
