<?php
/* Template Name: Etsy */
wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
$update_app = 0;
global $wpdb;
if (!empty($_POST)) {
    if ($_POST['etsyconf'] == 1) {
        update_user_meta($currentuserid, 'etsy_shop', $wpdb->escape($_POST['etsy_shop']));
        $update_app = 1;
        wp_redirect(("https://solutions.ryankikta.com/etsy?action=authorize"));
    }
}
include(ABSPATH . 'wp-content/themes/ryankikta/includes/etsy-php/src/Etsy/EtsyClient.php');
require_once(ABSPATH . 'wp-content/themes/ryankikta/product-functions/etsy-functions.php');
//require("etsymodule.php");
if (empty($current_user->ID)) {
    wp_redirect("/login");
    exit();
}
$consumer_key = "jsn7hvt01964lf8cel4y092c";
$consumer_secret = "o6f3ji3udw";

$oauth = new OAuth($consumer_key, $consumer_secret);
$oauth->enableDebug();
$redirect_uri = $current_url . '/?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret . '&etsy_username=' . $etsy_username;
$errors_map = array();
$errors_map['oauth_problem=consumer_key_unknown'] = "There is an error with the consumer user key provided. Please try copy/pasting it again";
$errors_map['oauth_problem=token_rejected'] = "Please click <a href='https://www.etsy.com/your/apps' target='_blank'>here</a> remove the app from your shop.";
$etsy_install = 1;
$etsy_message = "";
if ($_GET['action'] === "authorize") {
    try {
        $req_token = $oauth->getRequestToken("https://openapi.etsy.com/v2/oauth/request_token", "https://solutions.ryankikta.com/etsy/");
    } catch (OAuthException $e) {
        $etsy_install = 0;
        $errors = explode("\n", $oauth->getLastResponseHeaders());
        foreach ($errors as $error) {
            if (strpos($error, "X-Error-Detail") !== false) {
                $etsy_message = trim(str_replace("X-Error-Detail:", "", $error));
                if (array_key_exists($etsy_message, $errors_map) !== false) {
                    $etsy_message = $errors_map[$etsy_message];
                }
            }
        }
        $debug = array();
        $debug['user_id'] = $currentuserid;
        $debug['username'] = $currentusername;
        $debug['error'] = $etsy_message;
        $debug['etsy_return'] = $oauth->debugInfo;
        $debug['shop_name'] = $_POST['etsy_shop'];
        wp_mail('team@ryankikta.com', 'etsy install issue', var_export($debug, true));
    }
    setcookie("oauth_token_secret", $req_token['oauth_token_secret'], time() + 3600);
    wp_redirect($req_token['login_url']);
}
$updated = 0;

// Check If First Time Completed
/**************************************************************************************************************************************/
//if (isset($_GET['oauth_token'])) {
if (isset($oauth)) {
    $request_token = $_GET['oauth_token'];
    $request_token_secret = $_COOKIE['oauth_token_secret'];
    $verifier = $_GET['oauth_verifier'];
    wp_mail('rkikta@ryankikta.com', 'request token', var_export($request_token, true));
    if (!empty($request_token) && !empty($verifier)) {
        $oauth->setToken($request_token, $request_token_secret);
        try {
            $access_token = $oauth->getAccessToken("https://openapi.etsy.com/v2/oauth/access_token", null, $verifier);
        } catch (OAuthException $e) {
            $etsy_install = 0;
            $errors = explode("\n", $oauth->getLastResponseHeaders());
            foreach ($errors as $error) {
                if (strpos($error, "X-Error-Detail") !== false) {
                    $etsy_message = trim(str_replace("X-Error-Detail:", "", $error));
                    if (array_key_exists($etsy_message, $errors_map) !== false) {
                        $etsy_message = $errors_map[$etsy_message];
                    }
                }
            }
            $debug = array();
            $debug['user_id'] = $currentuserid;
            $debug['username'] = $currentusername;
            $debug['error'] = $etsy_message;
            $debug['etsy_return'] = $oauth->debugInfo;
            $debug['shop_name'] = $_POST['etsy_shop'];
            wp_mail('team@ryankikta.com', 'etsy install issue', var_export($debug, true));
        }
        $token = $access_token['oauth_token'];
        $secret = $access_token['oauth_token_secret'];
        $doublecheckquery = $wpdb->get_results("SELECT `id` FROM `wp_users_etsy` WHERE `users_id` = $currentuserid");
        if (count($doublecheckquery) > 0) {
            $oauth->setToken($token, $secret);
	    try {
                $oauth->fetch("https://openapi.etsy.com/v2/shops/" . $shopname . "/");
                $json = $oauth->getLastResponse();
                $shops = json_decode($json, true);
                $shop = $shops['results'][0]['shop_name'];
                $etsy_user_id = $shops['results'][0]['user_id'];
                $wpdb->show_errors();
                if (empty($shop)) {
                    $has_error = true;
                    $etsy_message = "The Ryan Kikta app requires that your Etsy shop be created and OPEN. Please create a shop or set it to open and then install the app.";
                }
                if (!$has_error) {
                    $savesetsy = $wpdb->query("INSERT INTO `wp_users_etsy` (`id`, `users_id`, `shop`, `description`, `token`,`secret`,`version`,`dateadded`, `dateupdated`,`etsy_user_id`,`active`) VALUES (NULL, $currentuserid, '$shop', '', '$token','$secret',2,CURRENT_TIMESTAMP, '0000-00-00 00:00:00','$etsy_user_id',1);");
                    if ($savesetsy === false) {
                        $_errors['db_err'] = $wpdb->last_error;
                        $_errors['call'] = $shops;
                        wp_mail('team@ryankikta.com', 'etsy adduse db error', var_export($_errors, true));
                        $has_error = true;
                        $message = "There was a problem adding you shop.";
                    }
                    $shop_id = $wpdb->insert_id;
                    $updated = 1;
                } else {
                    $etsy_install = 0;
                }
            } catch (OAuthException $e) {
                $etsy_install = 0;
                $errors = explode("\n", $oauth->getLastResponseHeaders());
                foreach ($errors as $error) {
                    if (strpos($error, "X-Error-Detail") !== false) {
                        $etsy_message = trim(str_replace("X-Error-Detail:", "", $error));
                        if (array_key_exists($etsy_message, $errors_map) !== false) {
                            $etsy_message = $errors_map[$etsy_message];
                        }
                    }
                }
                $debug = array();
                $debug['user_id'] = $currentuserid;
                $debug['username'] = $currentusername;
                $debug['error'] = $etsy_message;
                $debug['shop_name'] = $_POST['etsy_shop'];
                $debug['etsy_return'] = $oauth->debugInfo;
                wp_mail('team@ryankikta.com', 'etsy get shop information issue', var_export($debug, true));
            }
        }
    } else {
        $etsy_install = 0;
        $etsy_message = "Your shop could not be authorized.";
    }
}
// Page Content
/**************************************************************************************************************************************/
get_header(); ?>
    <div class="content-wrapper">
    <br/>
    <div class="page-wrapper">
        <?php
        while (have_posts()) {
            the_post();
            $content = apply_filters('the_content', get_the_content());
            ?>
            <h1 class=><?php echo the_title(); ?></h1>
            <?php
            }  
// Get Balance
/**************************************************************************************************************************************/
        ?>
        <div class>
            <?php
            // Get Balance, if 0 show a warrning message
            $query = ("SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid");
            $balance = $wpdb->get_row($query);
            //$balance = $balance[0];
            $automatic_payment = get_user_meta($currentuserid, 'autopayment', true);

// Notifications
/**************************************************************************************************************************************/
            if ($balance == 0 && $automatic_payment != 1) {
                ?>
                <p>
                <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                    <strong>Your current Balance is $1.00 . </strong> Any orders placed from your shop will be put ON
                    HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds into
                    your account or setup automatic billing.
                </div>
                </p>
                <?php
            }
            if ($etsy_install == 0) {
                ?>
                <p>
                <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                    <strong><?php echo $etsy_message; ?>. If you continue to have issues please <a href="/contactus/">
                            contact us</a>.</strong>
                </div>
                </p>
                <?php
            }
            if ($update_app == 1) {
                ?>
                <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                    <p> Etsy App keys updated Successfully. </p>
                </div>
                <?php
            }
            if ($_GET['action'] == "deleteshop") {
                ?>
                <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                    <p> Shop Successfully deleted. </p>
                </div>
                <?php
                // Get token and delete webhooks
                $query = ("SELECT `token`,`secret` FROM `wp_users_etsy` WHERE `users_id` = $currentuserid");
                $fetch = $wpdb->get_row($query, ARRAY_A);
                $token = $fetch[0];
                $secret = $fetch[1];
                $wpdb->query("UPDATE `wp_users_products` SET `etsyactive` = 0  WHERE `users_id` = $currentuserid");
                // Malformed Updates
                //$wpdb->query("UPDATE `wp_users_etsy_colors` SET `etsy_id` = 0 WHERE `users_id` = $currentuserid");
                //$wpdb->query("UPDATE `wp_users_etsy_images` SET `etsy_id` = 0 WHERE `users_id` = $currentuserid");
                $wpdb->query("DELETE FROM `wp_users_etsy` WHERE `users_id` = $currentuserid");
                delete_user_meta($currentuserid, 'etsy_shop');
            }
// Display if this user has a shop installed.
/**************************************************************************************************************************************/
	    $shops = $wpdb->get_results("SELECT * FROM `wp_users_etsy` WHERE `users_id` = $currentuserid", ARRAY_A);
	    $numshops = $wpdb->num_rows;
            print_r ($shops);
                ?>
                <p>
                <form action="/etsy/" method="POST">
                    <?php
	               $shopname = (isset($_POST['shopname'])) ? $_POST['shopname'] : "";
                    ?>
		        <input name="etsyconf" value="1" type=""><br>
                    <input name="shopname" type="text">
                    <input value="Install" type="submit"></form>
                </p>
                <?php
                ?> <table> <?php
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
                                   <td style='border: 1px solid #e5e5e5;'>" . $rows['shop'] . "</td>
                                   <td style='border: 1px solid #e5e5e5;'>" . $date . "</td>
                                   <td style='border: 1px solid #e5e5e5;'>" . $status . "</td>
                                   <td style='border: 1px solid #e5e5e5;'>
                                        <a class='confirmshop' href='/etsy/?action=deleteshop&id=" . $rows['id'] . "'>Remove</a>
                                    </td>
                                </tr>");
                    }
                    ?> </table>  <?php
            if ($numshops == 0) {
                if ($updated == 1) {
                    ?>
                    <div>
                        <p> Awesome! Your Etsy Shop is now authorized. </p>
                    </div>
                    <?
                }

                // Check If First Time Completed
                /**************************************************************************************************************************************/
                $rows = mysql_fetch_row($checkuser);
                $shop = $rows[0];
                $shop_id = $rows[1];
                $token = $rows[2];
                if ($updated == 1) {
                    ?>
                    <?php

                    // Double check this is actually first time
                    $doublecheckrow = $wpdb->get_row("SELECT `firstimedone` FROM `wp_users_etsy` WHERE `id` = $shop_id");

                    if (empty($doublecheckrow[0])) {
                        // update
                        $wpdb->query("UPDATE `wp_users_etsy` SET `firstimedone` = 1  WHERE `id` = $shop_id");
                    }
                }
                echo $content;
            }
            ?>
            <br class="clear">
        </div>
    </div> <!-- content-wrapper -->
<?php get_footer(); ?>

