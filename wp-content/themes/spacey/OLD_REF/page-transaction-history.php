<?php
/*
Template Name: Transactions History
*/
?>
<?php
GLOBAL $wpdb;
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}
?>
<?php// get_header(); ?>
<!--//this is where sidebar was-->
    <div class="content-wrapper">
        <br/>
        <div class="page-wrapper">
            <?php
            while (have_posts()) {
                the_post();
                $content = apply_filters('the_content', get_the_content());
                ?>
                <h1 class="gdl-page-title gdl-divider gdl-title title-color"><?php echo the_title(); ?></h1>
                <?php
            }
            ?>
            <div class="gdl-page-content">
                <?php
                if ($_GET['action'] == "success") {
                    ?>
                    <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 95%;margin: 0 auto; margin-bottom: 20px;">
                        <strong>Thank you for your deposit </strong> , We are now verifying the payment and you should
                        see it in your account within a few minutes, You will recieve an email from us once the payment
                        has been added to your account!
                    </div>
                    <?php
                }
                ?>
                <?php
                echo $content;
                ?>
                <table>
                    <thead>
                    <tr>
                        <td style="border-bottom: 1px solid #F0EDEE;"><strong>Date</strong></td>
                        <td style="border-bottom: 1px solid #F0EDEE;"><strong>Order #</strong></td>
                        <td style="border-bottom: 1px solid #F0EDEE;"><strong>Transaction</strong></td>
                        <td style="border-bottom: 1px solid #F0EDEE;"><strong>Amount</strong></td>
                        <td style="border-bottom: 1px solid #F0EDEE;"><strong>Balance After</strong></td>
                    </tr>
                    </thead>
                    <?php
                    wp_get_current_user();
                    $currentuserid = $current_user->ID;
                    $currentusername = $current_user->user_login;
                    $total = $wpdb->get_var("SELECT count(id) FROM `wp_transactions` WHERE `userid` = $currentuserid   order by id DESC");
                    if (!$_GET['pagenum'] || $_GET['pagenum'] == "") {
                        $page = 1;
                    } else {
                        $page = $_GET['pagenum'];
                    }
                    $page_rows = 50;
                    $last = ceil($total / $page_rows);
                    if ($page < 1) {
                        $page = 1;
                    } elseif ($page > $last) {
                        $page = $last;
                    }
                    $max = 'limit ' . ($page - 1) * $page_rows . ',' . $page_rows;
                    $_sql = "SELECT * FROM `wp_users_products` WHERE `users_id` = $currentuserid    order by id DESC $max";
                    $transactionsquery = $wpdb->get_results("SELECT * FROM `wp_transactions` WHERE `userid` = $currentuserid ORDER BY id DESC $max", ARRAY_A);
                    while ($rows = $transactionsquery) {
                        ?>
                        <tr style="border-bottom: 1px solid #F0EDEE;">
                            <td style="border-bottom: 1px solid #F0EDEE;"><?php echo date('m/d/Y h:i:s A', strtotime($rows['timestamp'])); ?></td>
                            <td style="border-bottom: 1px solid #F0EDEE;">
                                <?php
                                if ($rows['type'] == 1 || $rows['type'] == 5 || $rows['type'] == 6) {

                                    echo "-";

				} else {
			            $getorderid = 1;
                                    //$getorderid = $wpdb->get_row("SELECT orderid FROM wp_rmproductmanagement_orders WHERE order_id = $rows[transactionid]");
                                    $orderid = $getorderid;
                                    $orderid = $orderid[0];
                                    $url = get_bloginfo('url');
                                    echo "<a href='$url/view-orders/?page=inventory-orders&action=view&order_id=$rows[transactionid]'>$orderid</a>";
                                }
                                ?>
                            </td>
                            <td style="border-bottom: 1px solid #F0EDEE;">
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
                                <span style="color: green;">+
                                <?php
                                }
                                ?>
                                <?php
                                if ($rows['type'] == 2 || $rows['type'] == 3 || $rows['type'] == 6){
                                ?>
                                <span style="color: red;">- 
                                <?php
                                }
                                ?>
                                $<?php echo number_format($rows['amount'], 2, '.', ''); ?>
                                </span>
                            </td>
                            <td style="border-bottom: 1px solid #F0EDEE;">
                                $<?php echo number_format($rows['balance'], 2, '.', ''); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <br/>
                <br class="clear">
            </div>

        </div>
        <br class="clear">
        <?php echo getPaginationString($page, $total, 50, 4, '/transactions-history/', "?pagenum=");
        echo '<br /><br /><br />'; ?>
    </div> <!-- content-wrapper -->
</div>
<?php get_footer(); ?>

