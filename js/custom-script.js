jQuery(document).ready(function ($) {
    // Listen for changes in payment method
    $("form.checkout").on(
        "change",
        'input[name="payment_method"]',
        function () {
            update_totals_based_on_payment_method();
        }
    );
    function updateBodyClass() {
        var selectedMethod = $('input[name="payment_method"]:checked').val();

        // Remove previous payment method classes
        $('body').removeClass(function(index, className) {
            return (className.match(/(^|\s)payment-method-\S+/g) || []).join(' ');
        });

        // Add the new payment method class
        $('body').addClass('payment-method-' + selectedMethod);
    }

    // Initial call to set the class on page load
    updateBodyClass();

    // Change event to update class when payment method is changed
    $('form.woocommerce-checkout').on('change', 'input[name="payment_method"]', function() {
        updateBodyClass();
    })

    function update_totals_based_on_payment_method() {
        var selected_payment_method = $(
            'input[name="payment_method"]:checked'
        ).val();
        $(
            ".bitcoin-payments-message-below, .normal-payments-message, .blockonomics-payments-message"
        ).hide();
        if (selected_payment_method === "blockonomics") {
            $(".bitcoin-payments-message-below").show();
            $(".blockonomics-payments-message").show();
        } else {
            $(".normal-payments-message").show();
        }

        $.ajax({
            url: wc_checkout_params.ajax_url, // WooCommerce AJAX URL
            type: "POST",
            data: {
                action: "update_cart_totals_on_payment_method_change",
                payment_method: selected_payment_method, // Pass the selected payment method
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
});
