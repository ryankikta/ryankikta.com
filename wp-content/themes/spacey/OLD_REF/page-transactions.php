<?php                          
/*                                  
Template Name: Transactions History 
*/                              
global $wpdb;
$site_url = site_url();
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
// Only show if logged in
if ($currentuserid == 0) {
wp_redirect("/login");
exit();
}

get_header();
?>

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>Orders</h1>
                    <h2 class="fs1">Transactions</h2>
                    <h3 class="fs2">Get Started</h3>
		            <?php atlas();?>
                </div>
			</div>
			<div class="col-12 col-lg-6 p-0 text-center">
				<h2 class="fs3">YOUR TRANSACTIONS</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
        </div>
    </div>
</div>
<div class="container-fluid py-40">
    <div class="row align-items-center justify-content-center justify-content-lg-between">
        <div class="col-12 py-40">
            <table class="manual-orders-table table my-40">
                <tr>
                    <th>Date</th>
                    <th>Order Number</th>
                    <th>Transaction</th>
                    <th>Amount</th>
                    <th>Remaining Balance</th>
                </tr>
                    <?php
		             $sql = ("SELECT * FROM `wp_transactions` WHERE `userid` = $currentuserid ORDER BY id DESC");
		             $query = $wpdb->get_results(($sql), ARRAY_A);
		                 foreach ($query as $key=> $rows) {
                     ?>
                <tr>
                    <td><?php echo date('m/d/Y h:i:s A', strtotime($rows['timestamp'])); ?></td>
                    <td>
                        <?php
                        if ($rows['type'] == 1 || $rows['type'] == 5 || $rows['type'] == 6) {
                            echo "-";
                		} else {
        				    $url = get_bloginfo('url');
        				    $myid = $rows['transactionid'];
        				    echo "<a href='$url/view-orders/?page=inventory-orders&action=view&order_id=$myid'>$myid</a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($rows['type'] == 1) {
                            if (strpos($rows['transactionid'], 'ch_') === 0) $payment_method = 'Stripe'; else $payment_method = 'Paypal';
                            echo $payment_method . " Deposit (<strong>$rows[payeremail]</strong>) Transaction ID : <strong>$rows[transactionid]</strong>";
                        } elseif ($rows['type'] == 2) {
                            echo "-";
                        } elseif ($rows['type'] == 3) {
                            echo "Order Adjustment";
                        } elseif ($rows['type'] == 4) {
                            echo "Order Refund (Order edited, Not enough balance for new price)";
                        } elseif ($rows['type'] == 5) {
                            echo "$rows[transactionid]";
                        } elseif ($rows['type'] == 6) {
                            echo "$rows[transactionid]";
                        } elseif ($rows['type'] == 7) {
                            echo "Order Adjustment";
                        }

                        ?>
                    </td>
                    <td style="border-bottom: 1px solid #F0EDEE;">        
                        <?php
                        if ($rows['type'] == 1 || $rows['type'] == 4 || $rows['type'] == 5 || $rows['type'] == 7){
                            ?>
                                <span style="color: green;"> + <?php } ?>
                            <?php

                        if ($rows['type'] == 2 || $rows['type'] == 3 || $rows['type'] == 6){
                        ?>
                            <span style="color: red;"> - <?php } ?> $ <?php 
                            echo number_format($rows['amount'], 2, '.', ''); 
                            ?>
                            </span>
                    </td>
                    <td style="border-bottom: 1px solid #F0EDEE;"> $ <?php echo number_format($rows['balance'], 2, '.', ''); ?>
                    </td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div> <!-- content-wrapper -->
</div>
<?php get_footer();?>


