<?php
/*
  Template Name: Storenvy
 */
require_once(ABSPATH . 'wp-content/themes/ryankikta/product-functions/storenvy-functions.php');
require(ABSPATH . 'wp-content/themes/ryankikta/product-functions/storenvy.php');
require("HTTP/Request2.php");
wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}
?>
<?php get_header(); ?>
<!--//this is where sidebar was-->
<div class="content-wrapper">
    <br/>
    <div class="page-wrapper">
        <?php
        while (have_posts()) {
            the_post();
            $content = apply_filters('the_content', get_the_content());
            ?> <h1 class="gdl-page-title gdl-divider gdl-title title-color"><?php echo the_title(); ?></h1> <?php
        }
        //displayStorenvyNoticeBox($currentuserid);
        ?>
        <div class="gdl-page-content">
            <?php
            if ($_GET['action'] == "deleteshop") {
                $response = removeStorenvyShop($_GET['id'], $currentuserid);
                if ($response['status'] == 200) {
                    ?>
                    <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                        <p> Shop Successfully deleted. </p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                        <strong><?php echo $response['error']; ?></strong>
                    </div>
                <?php }
            }

            if (isset($_GET['code'])) {
                $code = $_GET['code'];
                $response = SaveShopData($code, $currentuserid, $currentusername);
                if ($response['status'] == 200) {
                    $shop_id = $response['data'];
                    //wp_redirect("/storenvy-shop-view/?id=" . $shop_id);
			
		    $getShopInfo = mysql_query("SELECT `shop`,`shop_name`,`id`,`token` ,`firstimedone`,`active` FROM `wp_users_storenvy` WHERE `users_id` = $currentuserid and id=$shop_id ");
		    $cols = mysql_fetch_row($getShopInfo);

                    $shop = $cols[0];
                    $shop_name = $cols[1];
                    $shop_name = ($shop_name == "") ? $shop : $shop_name;
                    $shop_id = $cols[2];
                    $access_token = $cols[3];
                    $firstimedone = $cols[4];
                    $active = $cols[5];		    

		    if($active == "1"){
			    if($firstTimeDone == 0){
				$check_existing_webhooks = checkExistingStorenvyWebhooks($access_token, $shop_id, $currentuserid);
				
				if ($check_Existing_webhooks['create'] == false) {
                                	$orderPaidHook = saveOrderPaidHook($access_token, $shop_id, $currentuserid);
                        
				}
				
				if ($check_Existing_webhooks['delete'] == false) {
	                                $productHook = saveDeleteProductsHook($access_token, $shop_id, $currentuserid);
                            
				}
	
				if ($check_Existing_webhooks['update'] == false) {
        	                        $updateProductHook = saveUpdateProductsHook($access_token, $shop_id, $currentuserid);              
				}
				
				 mysql_query("UPDATE `wp_users_storenvy` SET `firstimedone` = 1  WHERE `id` = $shop_id");
			    }
		    }

		} else {
                    echo $response['error'];
                    ?>
                    <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                        <strong>Your shop could not be authorized, if you encounter this problem again please let us
                            know so we can help. </strong>
                    </div>
                    <?php
                }
	    }
            
	    echo ('<a class="btn-primary" href="https://www.storenvy.com/oauth/authorize?client_id=e8ebafb6148d1e57c829d07a24805ff2dfb2071358cfd570f8153ede03353e8e&amp;response_type=code&amp;redirect_uri=' . get_site_url() . '/storenvy&amp;scope=user%20store_read%20store_write">Install</a>');
            
	    $wpdb->get_results("SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid");
            $balance = $wpdb->get_row;
            $balance = $balance[0];
            $automatic_payment = get_user_meta($currentuserid, 'autopayment', true);
                if ($balance == 0 && $automatic_payment != 1) {
                    ?>
                    <p>
                    <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;">
                        <strong>Your current Balance is $0.00 . </strong> Any orders placed from your shop will be put
                        ON HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds
                        into your account or setup automatic billing.
                    </div>
		    </p>
                <?php 
                }
            // See if this user has a shop installed.
            $shops = $wpdb->get_results("SELECT * FROM `wp_users_storenvy` WHERE `users_id` = $currentuserid", ARRAY_A);
	    $numshops = $wpdb->num_rows;
	    //print_r ($shops);
	    ?> <div class="table_base_wrapper">
		    <table class="table_base">
                        <tr>
                            <th>Shop</th>
                            <th>Date Added</th>
                            <th>Status</th>
                            <th>Uninstall</th>
                        </tr>
                    <?php
                    foreach ($shops as $key=> $rows) {
                        $date = date("d/m/Y", strtotime($rows['dateadded']));
                        $status = "";
                        if ($rows['active'] > 0 ) {
                            $status = "<span>Active</span>";
                        } else {
                            $status = "<span>Inactive</span>";
                        }
                        echo $rows['id'];
                        echo ("<tr>
                                   <td>" . $rows['shop'] . "</td>
                                   <td>" . $date . "</td>
                                   <td>" . $status . "</td>
                                   <td>
                                        <a class='confirmshop' href='/storenvy/?action=deleteshop&id=" . $rows['id'] . "'>Remove</a>
                                   </td>
                               </tr>");
                    }
                ?> </table> 
               </div><?php
            ?>
            <br class="clear">
        </div>
    </div>
</div>
</div>
<!-- content-wrapper -->
<?php get_footer(); ?>
