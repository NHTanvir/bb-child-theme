<?php
/** 
 * Blockonomics Checkout Page (JS Enabled)
 * 
 * The following variables are available to be used in the template along with all WP Functions/Methods/Globals
 * 
 * $order: Order Object
 * $order_id: WooCommerce Order ID
 * $order_amount: Crypto Amount
 * $crypto: Crypto Object (code, name, uri) e.g. (btc, Bitcoin, bitcoin)
 * $payment_uri: Crypto URI with Amount and Protocol
 * $crypto_rate_str: Conversion Rate of Crypto to Fiat. Please see comment on php/Blockonomics.php -> get_crypto_rate_from_params() on rate difference.
 * $qrcode_svg_element: Generate QR Code when NoJS mode is active.
 */
do_action('woocommerce_send_inovice_email',$order_id);
     $orderdata = wc_get_order( $order_id );
     $packageName;
        // Get and Loop Over Order Items
    foreach ( $orderdata->get_items() as $item_id => $item ) {
       $packageName = $item->get_name();
    }
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gatwayasd</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?=get_template_directory_uri()?>-child/assets/blockonomics_nojs_checkout/style.css">
    <script src="<?=get_template_directory_uri()?>-child/assets/blockonomics_nojs_checkout/jquery.beefup.min.js"></script>
    <div class="col-xs-12 qrbox">
        <div class="col-xs-12 navbar">
            <div class="col-xs-6 col-sm-8">
                <img src="https://eeztbxs43d2.exactdn.com/wp-content/uploads/2023/09/logo.svg" alt="">
            </div>
            <div class="col-xs-6 col-sm-4 help_button_cover">
                <a href="https://direct.lc.chat/15884157/1" target="_blank" class="help_button">Hjälp</a>
            </div>
        </div>
        <div class="col-xs-12 wrapper">
            <div class="col-xs-12 col-md-8 leftWrapper">
                <div class="col-xs-12 col-md-10 paymentBox">
                    <div class="box1 form-group">
                        <h3>Genomför Betalning</h3>
                        <p>
                            För att slutföra din beställning köper du Bitcoin via någon utav hemsidorna längst ned på denna sida. (Paybis rekommenderas).
                            <br/>
                            <strong>OBS!</strong> Klicka på knappen <strong>"Instruktioner"</strong> och följ de enkla stegen även om du tror dig kunna processen!
                        </p>
                    </div>
                    <div class="box2 form-group">
                        <p><?=__('Beställning #', 'blockonomics-bitcoin-payments')?><?php echo $order_id; ?></p>
                        <div class="ibox ">
                            <div class="textarea">
                                <p>Paket </p>
                                <p>
                                    <bold><?php echo str_replace('Packages -',' ',$packageName) ?> - <?php echo $order['expected_fiat'] ?> <?php echo $order['currency'] ?></bold>
                                </p>
                            </div>
                            <div class="textarea">
                                <p>Totalt i Bitcoin</p>
                                <p><bold><?php echo $order_amount; ?></bold> <span>(1 <?php echo strtoupper($crypto['code']); ?> = <span id="bnomics-crypto-rate"><?php echo $crypto_rate_str; ?></span> <?php echo $order['currency']; ?></span>)</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="scanbox col-xs-12 qrbox">
                            <a href="<?php echo $payment_uri; ?>" target="_blank">
                              <?php echo $qrcode_svg_element ?>
                            </a>
                        <div class="col-xs-12">
                            <p>Skicka <?php echo $crypto['name'] ?> <?=__('till denna adress:', 'blockonomics-bitcoin-payments')?></p>
                            <p><span class="inputs" id="btc_address"><?php echo $order['address']; ?> <i class="far fa-copy"></i></span></p>
                            <p><small>Summa i Bitcoin (BTC) att betala</small> <bold class="order_amount"><?php echo $order_amount; ?></bold></p>
                            <p><a href="<?php echo $payment_uri; ?>" target="_blank"><strong>Open in wallet</strong></a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4 rightWrapper">
                <div class="greenbox col-xs-12 col-md-11">
                    <div class="title">
                       <h3>Betalningsinstruktioner</h3>
                    </div>
                        <div class="greenboxContent border_bottom">
                            <strong class="cxl-beefup-head">Paybis (Kort till BTC) <span class="recommended">Rekommenderas</span> <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body" style="overflow: hidden;">
                    			<p>
                    			    Med ditt bankkort köper du Bitcoin som skickas till oss.<br/>
                			        <span class="Bankdagar" style="color:black">Betalningstid: 10-40 min</span><br/><br>
                                    <a class="sb" href="<?=get_site_url()?>/guide/betala-med-paybis" target="_blank">Betalningsinstruktioner för Paybis</a><br><br>
                			        <span class="Bankdagar">OBS! Se steg 4 i guiden. Annars landar betalningen fel!</span><br/><br>
                                </p>
                    		</div>
                        </div>
                        <div class="greenboxContent border_bottom">
                            <strong class="cxl-beefup-head">Safello (Swish till BTC) <span class="recommended">Rekommenderas</span> <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body" style="overflow: hidden;">
                    			<p>
                    			    Med Swish köper du Bitcoin som skickas till oss.<br/>
                			        <span class="Bankdagar" style="color:black">Betalningstid: 10-40 min</span><br/><br>
                                    <a class="sb" href="<?=get_site_url()?>/guide/betala-med-safello" target="_blank">Betalningsinstruktioner för Safello</a><br><br>
                			        <span class="Bankdagar">OBS! Var noga med att inte skicka betalningen din gamla adresser!</span><br/><br>
                                </p>
                    		</div>
                        </div>
                        <div class="greenboxContent border_bottom">
                            <strong class="cxl-beefup-head">Quickbit (Kort till BTC) <span class="recommended">Rekommenderas</span> <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body" style="overflow: hidden;">
                    			<p>
                    			    Med ditt bankkort köper du Bitcoin som skickas till oss.<br/>
                			        <span class="Bankdagar" style="color:black">Betalningstid: 10-40 min</span><br/><br>
                                    <a class="sb" href="<?=get_site_url()?>/guide/betala-med-quickbit" target="_blank">Betalningsinstruktioner för Quickbit</a><br><br>
                                </p>
                    		</div>
                        </div>
                        <div class="greenboxContent border_bottom">
                            <strong class="cxl-beefup-head">Ramp (Kort till BTC) <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body">
                    		    <p>
                    		        Med ditt bankkort köper du Bitcoin som skickas till oss.<br/>
                                    <span class="Bankdagar" style="color:black">Betalningstid: 10-40 min</span><br><br>
                                    <a class="sb" href="<?=get_site_url()?>/guide/betala-med-ramp" target="_blank">Betalningsinstruktioner för Ramp</a><br><br>
                                </p>
                    		</div>
                        </div>
                        <div class="greenboxContent border_bottom">
                            <strong class="cxl-beefup-head">Wunder (Kort till BTC) <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body">
                    			<p>
                    			    Med ditt bankkort köper du Bitcoin som skickas till oss.<br/>
                    			    <span class="Bankdagar" style="color:black">Betalningstid: 10-40 min</span> <br/><br>
                                    <a class="sb" href="<?=get_site_url()?>/guide/betala-med-wundertrading" target="_blank">Betalningsinstruktioner för Wunder</a><br><br>
                                </p>
                    		</div>
                        </div>
                        <div class="greenboxContent">
                            <strong class="cxl-beefup-head">Övriga metoder (Bitcoin) <i class="fa fa-chevron-down pull-right"></i></strong>
                            <div class="cxl-beefup-body">
                    			<p>
                    			    <a href="<?=get_site_url()?>/guide/betala-med-safello" style="color:white">Safello (Banköverföring) - <strong>Betalningstid: 1-2 bankdagar</strong></a><br/>
                    			    <a href="<?=get_site_url()?>/guide/betala-med-btcx" style="color:white">BTCX  (Kortbetalning) - <strong>Betalningstid: 10-40 min</strong></a><br/>
                    			    <a href="<?=get_site_url()?>/guide/betala-med-firi" style="color:white">Firi (Banköverföring) - <strong>Betalningstid: 1-2 min</strong></a><br/>
                                </p>
                    		</div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    	jQuery(document).ready(function($){
    		jQuery('.greenboxContent').beefup({
    			trigger: '.cxl-beefup-head',
    			content: '.cxl-beefup-body',
    			openSingle: true,
    			
    		});
    	});
    	jQuery(document).on('click', '.fa-copy', function($){
            	var temp = jQuery("<input>");
                jQuery("body").append(temp);
                temp.val(jQuery('#btc_address').text().trim()).select();
                document.execCommand("copy");
                temp.remove();
                alert('Copied '+jQuery('#btc_address').text());
    	});
    </script>
