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
        $('input[name="payment_method"]:checked').closest('li').addClass('payment-active');
    }

    function updateBodyClass() {
        var selectedMethod = $('input[name="payment_method"]:checked').val();

        $('body').removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-method-\S+/g) || []).join(' ');
        });

        if (selectedMethod === 'blockonomics') {
            $('body').addClass('payment-method-blockonomics');
        } else {
            $('body').addClass('payment-method-card');
        }
    }

    function updatePaymentTypeClass() {
        var selectedPaymentType = $('input[name="payment_type"]:checked').val();

        $('body').removeClass(function (index, className) {
            return (className.match(/(^|\s)payment-type-\S+/g) || []).join(' ');
        });

        if (selectedPaymentType) {
            $('body').addClass('payment-type-' + selectedPaymentType);
        }
    }

    updateBodyClass();
    updatePaymentTypeClass();

    $('form.woocommerce-checkout').on('change', 'input[name="payment_method"]', function () {
        updatePaymentMethodClass();
        updateBodyClass();
    });

    $('.payment-box-wrapper').on('change', 'input[name="payment_type"]', function () {
        updatePaymentTypeClass();
    });

    $(document.body).on('updated_checkout', function () {
        updatePaymentMethodClass();
    });

    function update_totals_based_on_payment_method() {
        pc_modal(true);
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
                $(".checkout-left").find(".table-wrapper").html(response);
                pc_modal(false);
            },
            error: function (xhr, status, error) {
                console.log("An error occurred: " + error);
            },
        });

    }

    $(document).on('change', '.qty-input', function() {
        pc_modal(true);
        var qty = $(this).val();
        var cartItemKey = $(this).attr('name').match(/\[(.*?)\]/)[1]; 

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
                update_totals_based_on_payment_method();
            }
        });
    });
});
