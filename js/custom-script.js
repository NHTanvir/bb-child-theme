jQuery(document).ready(function ($) {
    let pc_modal = (show = true) => {
        if (show) {
            jQuery("#plugin-client-modal").show();
        } else {
            jQuery("#plugin-client-modal").hide();
        }
    };
    $("form.checkout").on(
        "change",
        'input[name="payment_method"]',
        function () {
            update_totals_based_on_payment_method();
        }
    );

    function updatePaymentMethodClass() {
        $('input[name="payment_method"]:checked')
            .closest("li")
            .addClass("payment-active");
    }

    function updateBodyClass() {
        var selectedMethod = $('input[name="payment_method"]:checked').val();

        // Remove previous payment method body classes
        $("body").removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-method-\S+/g) || []).join(
                " "
            );
        });

        if (selectedMethod === "blockonomics") {
            $("body").addClass("payment-method-blockonomics");
        } else {
            $("body").addClass("payment-method-card");
        }
    }

    updateBodyClass();

    // Change event to update class when payment method is changed
    $("form.woocommerce-checkout").on(
        "change",
        'input[name="payment_method"]',
        function () {
            updatePaymentMethodClass();
            updateBodyClass();
        }
    );

    $(document.body).on("updated_checkout", function () {
        // Reapply the class to the selected payment method
        updatePaymentMethodClass();
    });

    function update_totals_based_on_payment_method() {
        pc_modal(true);
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
                $(".checkout-left").find(".table-wrapper").html(response);
                pc_modal(false);
            },
            error: function (xhr, status, error) {
                console.log("An error occurred: " + error);
            },
        });
    }

    // Update cart on quantity change
    $(document).on("change", ".qty-input", function () {
        pc_modal(true);
        var qty = $(this).val();
        var cartItemKey = $(this)
            .attr("name")
            .match(/\[(.*?)\]/)[1]; // Extract the cart item key

        var data = {
            action: "woocommerce_update_cart_item_qty",
            cart_item_key: cartItemKey,
            quantity: qty,
        };

        $.ajax({
            type: "POST",
            url: wc_checkout_params.ajax_url,
            data: data,
            success: function (response) {
                update_totals_based_on_payment_method();
            },
        });
    });
    // Function to toggle MAC address input visibility
    function toggleMacAddressInput() {
        var selectedOption = $(".addon-option-select").val();
        if (selectedOption === "förnyelse") {
            $(".addon-mac-address").show(); // Show the MAC address input
        } else {
            $(".addon-mac-address").hide(); // Hide the MAC address input
        }
    }

    // Call the function on page load
    $(document).ready(function () {
        toggleMacAddressInput(); // Check the initial state on page load
    });

    // Toggle MAC address input visibility based on selected option
    $(".addon-option-select").on("change", function () {
        toggleMacAddressInput(); // Call the function on change
    });

    // Handle the Add to Cart button click event
    $(".add-addon-to-cart").on("click", function (e) {
        e.preventDefault();

        var product_id = $(this).data("product_id");
        var variation_id = $(".addon-variation-select").val();
        var addon_option = $(".addon-option-select").val(); // Get the selected option
        var mac_address = $(".addon-mac-address").val(); // Get the MAC address

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: "POST",
            data: {
                action: "add_addon_to_cart",
                product_id: product_id,
                variation_id: variation_id,
                addon_option: addon_option, // Include addon option
                mac_address: mac_address, // Include MAC address
            },
            success: function (response) {
                if (response.success) {
                    update_totals_based_on_payment_method();
                }
            },
        });
    });

    $(".remove-cart").on("click", function () {
        var cartItemKey = $(this).data("cart-item-key");
        pc_modal(true);
        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: "POST",
            data: {
                action: "remove_cart_item",
                cart_item_key: cartItemKey,
            },
            success: function (response) {
                if (response.success) {
                    update_totals_based_on_payment_method();
                }
            },
        });
    });
});
