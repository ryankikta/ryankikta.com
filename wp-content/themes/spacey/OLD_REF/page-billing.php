<?php
// Only show if logged in

$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}

global $wpdb;
// Get Current Balance
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
$currentuseremail = $current_user->user_email;

$query = "SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid";
$balance = $wpdb->get_row($query, ARRAY_A);
$balance = $balance[0];
$options = get_option('wp_stripe_options');
$currency = $options['stripe_currency'];
$autopayment = get_user_meta($currentuserid, 'autopayment', true);
$autopayment = ($autopayment == 1) ? 1 : 0;
$balanceafter = $balance + 10;
$balanceafter = number_format($balanceafter, 2, '.', '');
$autopayment_method = get_user_meta($currentuserid, 'autopayment_method', true);
?>

<?php get_header(); ?>
<div class="container-fluid dashboard_content dashboard dashboard_payments">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <div class="row">
                <div class="col-lg-9">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>Payments</h1>

                    <h2 class="fs1">$<span class="fs1" id="current_balance"><?php echo number_format($balance, 2, '.', ''); ?></span></h2>
                    <h3 class="fs2">Available Account Balance</h3>
                    <p>*Your available balance is the amount you have to apply to all new orders. Available balance reflects the amount in your account minus all completed orders. Pending balances include orders that are new or processing and have not shipped, and do not portray the actual available balance.  If your available balance is $0.00 or less than the amount of an order, your order will be placed on hold until funds are deposited.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 pt-80">
                    <ul class="nav nav-tabs" id="payment_tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fs3 active" id="add-tab" data-toggle="tab" href="#add" role="tab" aria-controls="add" aria-selected="true"><span class="d-none d-md-inline fs3">Manual </span>Deposit</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fs3" id="autopay-tab" data-toggle="tab" href="#autopay" role="tab" aria-controls="autopay" aria-selected="false"><span class="d-none d-md-inline fs3">Enable </span>Autopay</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fs3" id="withdraw-tab" data-toggle="tab" href="#withdraw" role="tab" aria-controls="withdraw" aria-selected="false">Withdrawal<span class="d-none d-md-inline fs3"> Request</span></a>
			</li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fs3" id="subscriber-tab" data-toggle="tab" href="#subscriber" role="tab" aria-controls="subscriber" aria-selected="false">Subscription<span class="d-none d-md-inline fs3"></span></a>
                        </li>
                    </ul>
                    <div class="tab-content tab_content_grey" id="payment_tab_content">
                        <div class="tab-pane fade show active" id="add" role="tabpanel" aria-labelledby="add-tab">
                            <h2>Add funds manually</h2>
                            <p>In order for us to begin processing your orders you will need to manually deposit funds into your Ryan Kikta account to cover the cost of the order through PayPal or Stripe. If you are placing a manual order, you must manually deposit the amount of order into your Ryan Kikta account, the Autopay option does not work for manual orders.</p>
                            <p>If you have received an email requesting you manually deposit funds to cover changes to an order after it is placed (i.e., shipping changes, printing changes, reshipment fees, etc), this is where you would do so.</p>

                            <div class="row">
                                <div class="col-md-6 paypal_deposit">
                                    <h2 class="pt-40">Payment with PayPal</h2>
                                    <p>I would like to deposit: (Min: $10.00)</p>

                                    <form target="_blank" action="/paypal-app.php<?php echo $app; ?>" method="POST" id="form">
                                        <div class="row">
                                            <div class="col-sm">
                                                <p class="fs2">$ <input class="input_white input_white_paypal" value="10" type="text" name="amount" id="amount"></p>
                                            </div>
                                            <div class="col-sm text-sm-right">
                                                <p class="fs3 mb-0">Amount to send:</p>
                                                <p class="fs2">$<span class="amounttosend fs2">10.00</span> USD</p>

                                                <p class="small_label" id="balanceafterp">(Balance after deposit: $<span class="balanceafter"><?php echo $balanceafter; ?></span>)</p>
                                            </div>
                                        </div>

                                        <input name="username" value="<?php echo $currentusername; ?>" type="hidden">
                                        <input name="userid" value="<?php echo $currentuserid; ?>" type="hidden">
                                        <input class="btn-primary" type="submit" name="submit" value="Deposit Money">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="pt-40">Payment with Credit Card</h2>
                                    <p>You can manually add funds to your Ryan Kikta account from your credit card using Stripe</p>
                                    <?php echo do_shortcode("[wp-stripe]"); ?>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="autopay" role="tabpanel" aria-labelledby="autopay-tab">
                            <h2>Authorize/Cancel Autopay</h2>
                            <p>New! If you are using our API or Apps, you can authorize Paypal or Stripe to bill you automatically for all orders that are placed with Ryan Kikta. Once you have authorized payment, your Paypal or Stripe account will be billed for each individual order at the time it enters the Ryan Kikta system. Auto payments do not work with manually placed orders. </p>
                            <h2>Auto Payment Method</h2>
                            <p>AutoPayment method:</p>

                            <?php
                            $payments = array('Paypal', 'Stripe');
                            $payment_method = '<select class="mb-20" id="autopayment_method" name="autopayment_method">';
                            foreach ($payments as $key => $method) {
                                $selected = ($autopayment_method == ($key + 1)) ? 'selected="selected"' : '';
                                $payment_method .= '<option value="' . ($key + 1) . '" ' . $selected . '>' . $method . '</option>';
                            }
                            $payment_method .= '</select>';
                            $method = ($autopayment_method == 2) ? 'Stripe' : 'Paypal';

                            $replace_payment_method = ($autopayment == 1) ? $method : $payment_method;

                            echo $replace_payment_method;

                            $plugin_url = plugins_url() . '/wp-stripe/';

                            $form_authorize_stripe = do_shortcode("[wp-authorize-stripe]");
                            $form_cancel_stripe = '<div style="display: none;" class="wp-stripe-notification wp-stripe-success"></div><button  id="cancel_automatic_billing" class="btn-primary">Cancel Automatic Billing</button>
                                                  <div class="msg_authorized" style="display:none;"></div>';

                            $form_authorize_paypal = '<form action="/billing/" METHOD="POST">
                                <input type="hidden" name="authorize" value="yes" />
                                <input type="submit" name="submit" value="Authorize Automatic Billing" class="btn-primary">
                            </form>';
                            $form_cancel_paypal = '<form action="/billing/" METHOD="POST">
                                <input type="hidden" name="cancel_au" value="yes" />
                                <input type="submit" name="submit" value="Cancel Automatic Billing" class="btn-primary">
                            </form>';
                            if ($autopayment_method == 2) {
                                if ($autopayment == 1)
                                    echo '<div class="authorize_form">' . $form_cancel_stripe . '</div>';
                                else
                                    echo '<div class="authorize_form">' . $form_authorize_stripe . '</div>';
                            } else {
                                if ($autopayment == 1)
                                    echo $form_cancel_paypal;
                                else
                                    echo '<div class="authorize_form">' . $form_authorize_paypal . '</div>';
                            } ?>

			</div>
                        <div class="tab-pane fade" id="subscriber" role="tabpanel" aria-labelledby="subsciber-tab">
                            <h2>Subscription</h2>
                            <?php atlas(); ?>
                            <div class="row">
                                <div class="col-md-6 subscription">
                                    <h2 class="pt-40">Payment</h2>
				    <?php echo apply_filters( 'the_content',' [pmpro_billing] ');
                                    //echo do_shortcode("[pmpro_billing]");   
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="pt-40">Overview?</h2>
                                    <?php atlas(); ?>
                                    <?php// echo do_shortcode("[wp-stripe]"); ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
                            <h2>Withdrawal Request</h2>
                            <p>You can withdraw funds back to your Paypal account but there is a $1.00 charge per withdrawal. To withdraw, please <a href="/contactus">contact us</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<?php include('includes/graphic_design.php'); ?>


<script type='text/javascript'>


        jQuery('body').on('change', '#autopayment_method', function () {
            var elem = jQuery(this);
            var val = elem.val();
            var autopayment = '<?php echo $autopayment; ?>';
            if (val == 2) {
                if (autopayment == 0) {
                    var authorize_stripe = '<?php echo str_replace(array("\r", "\n", "\t"), "", $form_authorize_stripe); ?>';
                    jQuery(".authorize_form").html(authorize_stripe);
                }
            } else {
                var authorize_paypal = '<?php echo str_replace(array("\r", "\n", "\t"), "", $form_authorize_paypal); ?>';
                jQuery(".authorize_form").html(authorize_paypal);
            }
        });

        jQuery(document).on('submit', '#wp-stripe-authorize-form', function (event) {

            event.preventDefault();
            jQuery(".wp-stripe-notification").hide();
            jQuery('.stripe-submit-button').prop("disabled", true).css("opacity", "0.4");
            jQuery('.stripe-submit-button .spinner').fadeIn("slow");
            jQuery('.stripe-submit-button span').addClass('spinner-gap');
            var elem = jQuery(this);
            var $form = jQuery("#wp-stripe-authorize-form");
            var newStripeForm = $form.serialize();


            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: newStripeForm,
                cache: false,
                success: function (response) {
                    if (response.error) {
                        jQuery(".wp-stripe-failure").show().text(response.error.message);
                        jQuery('.stripe-submit-button').prop("disabled", false).css("opacity", "1.0");
                        jQuery('.stripe-submit-button .spinner').fadeOut("slow");
                        jQuery('.stripe-submit-button span').removeClass('spinner-gap');
                    } else {
                        jQuery(".wp-stripe-success").html(response.message).show('slow').delay(2000).fadeOut('slow', function () {
                            jQuery(this).html("");
                            jQuery("#content_autopayment_method").remove();
                            var cancel_stripe = '<?php echo str_replace(array("\r", "\n", "\t"), "", $form_cancel_stripe); ?>';
                            jQuery(".authorize_form").html(cancel_stripe);
                        });
                    }
                },
                error: function (response) {
                    jQuery(".wp-stripe-failure").show().text("Stripe Authorise Payment Error:".concat(btoa(JSON.stringify(response))));
                }
            });
            return false;
        });

        jQuery('body').on('click', '#cancel_automatic_billing', function () {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "stripe_cancel_autopayment",
                    uid: '<?php echo base64_encode($currentuserid) ?>'
                },
                success: function (response) {
                    jQuery(".wp-stripe-success").text("Automatic billing is cancelled").show('slow').delay(4000).fadeOut('slow', function () {
                        jQuery(this).html("");
                        var authorize_stripe = '<?php echo str_replace(array("\r", "\n", "\t"), "", $form_authorize_stripe); ?>';
                        var payment_method = '<?php echo str_replace(array("\r", "\n", "\t"), "", $payment_method); ?>';
                        var select = '<br>AutoPayment method: ' + payment_method;
                        if (jQuery("#content_autopayment_method").length > 0) {
                            jQuery("#content_autopayment_method").html(select);
                            jQuery(".authorize_form").html(authorize_stripe);
                        } else {
                            jQuery(".authorize_form").before('<div id="content_autopayment_method">' + select + '</div>').html(authorize_stripe);
                        }
                    });
                }
            });
        });

        <?php if(get_user_meta($currentuserid, 'old_paypal_payment', true) != 1){ ?>
        var deposit = false;
        jQuery(document).on('click', '#paypal-deposit-button', function () {
            if (deposit)
                return false;
            deposit = true;
            jQuery(this).hide();
            jQuery(this).removeClass('paypal-deposit-button');
            jQuery(this).addClass('paypal-deposit-button-grey');
            jQuery(this).hide();
            jQuery("#deposit-loading").show();
            jQuery("#amount").val(jQuery("#amount_p").val());
            jQuery.ajax({
                type: "GET",
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                url: "/paypal_deposit_ajax.php?action=paypal_deposit",
                data: {uid: '<?php echo base64_encode($currentuserid) ?>', amount: jQuery("#amount_p").val()},
                cache: false,
                async: true,
                success: function (response) {
                    if (response.status == "success") {
                        console.log(response);
                        if (response.url != '') {
                            <?php if($current_user->ID != 479) {?>

                            window.location = response.url;
                            <?php } ?>
                            return false;
                        } else {


                            jQuery(".paypal-deposit-success").html(response.message).show('slow').delay(2000).fadeOut('slow', function () {
                                    window.location = "https://ryankikta.com/billing/";
                                    window.location.href = "https://ryankikta.com/billing/";

                                }
                            );
                        }

                    } else {
                        jQuery("#form").submit();
                    }
                    /* jQuery(".paypal-deposit-error").html(response.message).show('slow').delay(2000).fadeOut('slow',function(){
                        jQuery('#paypal-deposit-button').show();

                     }
                         );*/
                    jQuery('#paypal-deposit-button').addClass('paypal-deposit-button');
                    jQuery('#paypal-deposit-button').removeClass('paypal-deposit-button-grey');

                    jQuery("#deposit-loading").hide();
                },
                error: function (response) {
                    jQuery("#form").submit();
                    jQuery(".paypal-deposit-error").html("Error Paypal Deposit amount").show('slow').delay(2000).fadeOut('slow');
                    jQuery('#paypal-deposit-button').addClass('paypal-deposit-button');
                    jQuery('#paypal-deposit-button').removeClass('paypal-deposit-button-grey');
                    jQuery('#paypal-deposit-button').show();
                    jQuery("#deposit-loading").hide();
                }
            });
            // deposit = false;
            //return false;
        });
        <?php  } ?>



</script>


    <style>


        .paypal-deposit-success {
            background-color: #d1f2a5;
            color: #345607;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .paypal-deposit-error {
            background-color: orange;
            color: #345607;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .paypal-deposit-notification {
            border-radius: 5px;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 10px 0 10px 10px;
            padding: 10px;
            width: 500px;
        }
    </style>

<?php get_footer(); ?>
