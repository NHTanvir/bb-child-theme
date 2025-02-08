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
        var selectedMethod = $('input[name="payment_method"]:checked').closest('li').find('img[data-payment]').attr('data-payment');
    
        // Remove previous payment method body classes
        $("body").removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-method-\S+/g) || []).join(" ");
        });
    
        if (selectedMethod === "crypto") {
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

        if (selected_payment_method === "crypto") {
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
        var macAddressInput = $(".addon-mac-address");

        if (selectedOption === "förnyelse") {
            macAddressInput.show();
        } else {
            macAddressInput.hide().removeClass("red"); // Hide and remove red border
        }
    }

    function validateMacAddress() {
        var selectedOption = $(".addon-option-select").val();
        var macAddressInput = $(".addon-mac-address");

        if (selectedOption === "förnyelse" && !macAddressInput.val()) {
            macAddressInput.addClass("red"); // Add red border if empty
            return false;
        } else {
            macAddressInput.removeClass("red"); // Remove red border if filled
            return true;
        }
    }

    $(document).ready(function () {
        toggleMacAddressInput(); // Check initial state on page load

        // Remove "red" class when user starts typing
        $(".addon-mac-address").on("input", function () {
            if ($(this).val()) {
                $(this).removeClass("red");
            } else {
                $(this).addClass("red");
            }
        });
    });

    $(".addon-option-select").on("change", function () {
        toggleMacAddressInput(); // Toggle MAC address input visibility on change
    });

    $(".add-addon-to-cart").on("click", function (e) {
        if (!validateMacAddress()) {
            e.preventDefault(); // Prevent adding to cart if MAC address is missing for "förnyelse"
            return;
        }

        pc_modal(true); // Show modal
        var product_id = $(this).data("product_id");
        var variation_id = $(".addon-variation-select").val();
        var addon_option = $(".addon-option-select").val();
        var mac_address = $(".addon-mac-address").val();

        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: "POST",
            data: {
                action: "add_addon_to_cart",
                product_id: product_id,
                variation_id: variation_id,
                addon_option: addon_option,
                mac_address: mac_address,
            },
            success: function (response) {
                $(".addon-mac-address").val("");
                if (response.success) {
                    update_totals_based_on_payment_method();
                }
            },
        });
    });

    $(document).on("click", ".remove-cart", function () {
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
