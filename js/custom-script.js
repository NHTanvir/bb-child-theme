jQuery(document).ready(function($) {
    alert('working');
    // Initial state: Show Card description, hide Bitcoin description and Bitcoin button
    $('#bitcoin-description').hide();
    $('#card-description').show();
    $('#pay-with-bitcoin').hide();  // Hide "Pay With Bitcoin" button initially
    $('#pay-with-card').show();     // Show "Pay With Card" button initially

    // Add initial styles for the Card payment option
    $('#cart-payment').css({
        'background': '#ebfff4',
        'border': '1px solid green'
    });

    // Show Bitcoin description and apply styles when Bitcoin option is clicked
    $('#bitcoin-payment').on('click', function() {
        // Apply styles to Bitcoin payment option
        $(this).css({
            'border': '1px solid burlywood',
            'background': '#fff6e5'
        });

        // Remove styles from Card payment option
        $('#cart-payment').css({
            'border': '',
            'background': ''
        });

        // Show Bitcoin description, hide Card description
        $('#bitcoin-description').show();
        $('#card-description').hide();
        $('#pay-with-bitcoin').show(); // Show "Pay With Bitcoin" button
        $('#pay-with-card').hide();    // Hide "Pay With Card" button
    });

    // Show Card description and apply styles when Card option is clicked
    $('#cart-payment, #pay-with-card').on('click', function() {
        // Apply styles to Card payment option
        $('#cart-payment').css({
            'background': '#ebfff4',
            'border': '1px solid green'
        });

        // Remove styles from Bitcoin payment option
        $('#bitcoin-payment').css({
            'border': '',
            'background': ''
        });

        // Show Card description, hide Bitcoin description
        $('#card-description').show();
        $('#bitcoin-description').hide();
        $('#pay-with-bitcoin').hide(); // Hide "Pay With Bitcoin" button
        $('#pay-with-card').show();    // Show "Pay With Card" button
    });
});
