<?php
/*
  Template Name: Opencart
 */
require("HTTP/Request2.php");
require('../product-functions-live/opencart-functions.php');
global $wpdb;
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;

if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}
get_header();
?>
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
        ?>
        <div class="gdl-page-content">
            <?php
            // Get Balance, if 0 show a warrning message
            $query = mysql_query("SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid");
            $balance = mysql_fetch_row($query);
            $balance = $balance[0];
            $automatic_payment = get_user_meta($currentuserid, 'autopayment', true);
            if ($balance == 0 && $automatic_payment != 1) {
                ?>
                <p>
                <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                    <strong>Your current Balance is $0.00 . </strong> Any orders placed from your shop will be put ON
                    HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds into
                    your account or setup automatic billing.
                </div>
                </p>
                <?php
            }

            if ($_GET['action'] == "deleteshop") {
                if (isset($_GET['id'])) {
                    $shop_id = (int)$_GET['id'];

                    $users = $wpdb->get_results("select * from wp_users_opencart where id=$shop_id", ARRAY_A);
                    if ($currentuserid == $users[0]['users_id']) {
                        @extract($users[0]);
                        $domain = stripcslashes($domain);

                        mysql_query("insert into `wp_users_shops_deleted` (`users_id`, `shop_name`, `shop_type`, `shop_id`, `deleted_at`,`type`) VALUES ($currentuserid,'$domain','opencart',$id,'" . date("Y-m-d H:i:s", time()) . "','live');");

                        mysql_query("DELETE FROM `wp_users_opencart` WHERE `id` = $shop_id");

                        /* $contact_id = get_user_meta($currentuserid, '_infusionsoft_contact_id', true);
                         $ob = new Infusionsoft_Examples();
                         $ob->remove_tag($contact_id,210);
                         $ob->add_tag($contact_id,212);*/
                        ?>
                        <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                            <p> Shop Successfully deleted. </p>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;margin-bottom: 20px;">
                        An error has occurred while deleting the shop
                    </div>
                    <?php
                }
            }
            // See if this user has a shop installed.
            $shops = $wpdb->get_results("SELECT * FROM `wp_users_opencart` WHERE `users_id` = $currentuserid", ARRAY_A);

            if (empty($shops)) {
                $pos = strpos($content, "[BEFORETEXT]") + 12;
                $len = strlen($content);
                $content = substr($content, $pos);
                echo $content . "<br />";
            } else {
                $session_name = $currentuserid . '_oc_installed';
                if (isset($_SESSION[$session_name]) && $_SESSION[$session_name] == 1) {
                    unset($_SESSION[$session_name]);
                    ?>
                    <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                        <p> Awesome! Your OpenCart Shop is now authorized. </p>
                    </div>
                    <?php
                    //mysql_query("DELETE FROM `wp_users_opencart` WHERE `users_id` = $currentuserid");

                    /* $contact_id = get_user_meta($currentuserid, '_infusionsoft_contact_id', true);
                     $ob = new Infusionsoft_Examples();
                     $ob->add_tag($contact_id,210);
                     $ob->remove_tag($contact_id,212);*/
                }
                $tables_content = "";
                foreach ($shops as $shop) {
                    $date = date("d/m/Y", strtotime($shop["created_at"]));
                    $status = "";
                    if ($shop["active"] == 1) {
                        $status = "<span style='background-color: #5cb85c;display: inline;padding: .2em .6em .3em;font-size: 75%;font-weight: 700;line-height: 1;color: #fff;text-align: center;white-space: nowrap;vertical-align: baseline;border-radius: .25em;'>Active</span>";
                    } else {
                        $status = "<span style='background-color: #d9534f;display: inline;padding: .2em .6em .3em;font-size: 75%;font-weight: 700;line-height: 1;color: #fff;text-align: center;white-space: nowrap;vertical-align: baseline;border-radius: .25em;'>Inaccessible</span>";
                    }
                    $tables_content .= "<tr><td style='border: 1px solid #e5e5e5;'>" . $shop["domain"] . "</td><td style='border: 1px solid #e5e5e5;'>" . $date . "</td><td style='border: 1px solid #e5e5e5;'>" . $status . "</td><td style='border: 1px solid #e5e5e5;'><a href='/opencart-shop/?id=" . $shop["id"] . "' >View</a>&nbsp;&nbsp;<a class='confirmshop' href='/opencart/?action=deleteshop&id=" . $shop["id"] . "'>Remove</a></td></tr>";
                }
                $content = str_replace("[SHOPADDRESS]", $shop, $content);
                $content = str_replace("[ShopTables]", $tables_content, $content);
                $pos = strpos($content, "[BEFORETEXT]");
                $content = substr($content, 0, $pos);

                echo $content;
            }
            ?>
            <br class="clear">
        </div>
    </div> <!-- content-wrapper -->
</div>
</div>
<?php get_footer(); ?>
