<?php
/*
   Template Name: Woocommerce
 */
require_once (ABSPATH . 'wp-load.php');
require ABSPATH . '/HTTP/Request2.php';
require_once (ABSPATH . 'wp-content/themes/ryankikta/product-functions/woocommerce-restfull-functions.php');
require ABSPATH . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

global $wpdb;
$site_url = site_url();
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
$errors = array();

if (isset($_POST['wc_shop'])) {
    $shop = strtolower((rtrim($_POST['wc_shop'], '/') . '/'));
    $check_url = file_get_contents($shop . 'wc-api/v3/');
    if (!$check_url) {
        $_POST['wc_shop'] = 
        $_POST['wc_shop'] = strtolower($_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/shop\/$/', 'shop/', $_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/shop\/$/', 'shop/', $_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/shop$/', 'shop/', $_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/store\/$/', 'shop/', $_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/store\/$/', 'shop/', $_POST['wc_shop']);
        $_POST['wc_shop'] = preg_replace('/store$/', 'shop/', $_POST['wc_shop']);
    }
}

if (!empty($_POST) && isset($_POST['wc_shop'])) {
    $shop = strtolower((rtrim($_POST['wc_shop'], '/') . '/'));
    if ($_POST['wcconf'] == 1) {
        if ($shop == '') {
            $errors[] = 'Please enter a shop/key/secret';
        } else {
            wp_redirect($site_url . "/woocommerce/");
            $is_oauth = 1;
            $url_json = $shop . 'wp-json';
            $result = file_get_contents($url_json);

            $pos = strpos($result, '{"');
            $result = substr($result, $pos);

            $data = json_decode($result, true);

            if (is_array($data)) {
                $endpoint = 'wc-auth/v1/authorize';
                $params = array(
                    'app_name' => urlencode('Ryan Kikta'),
                    'scope' => urlencode('read_write'),
                    'user_id' => urlencode(base64_encode($wpdb->escape($shop))),
                    'return_url' => urlencode($site_url . '/woocommerce/'),
                    'callback_url' => urlencode($site_url . '/woocommerce/?callback=1&user_id=' . $currentuserid)
                );
                $_parm = array();
                echo "<script>console.log('$_pram')</script>";
                foreach ($params as $key => $param) {
                    $_parm[] = $key . '=' . $param;
                }
                $query_string = implode('&', $_parm);

                $redirect_url = $shop . $endpoint . '?' . $query_string;

                wp_redirect($redirect_url);
            } else {
                //wp_redirect($site_url . "/woocommerce-authorize/");
            }
        }
    }
}

if (isset($_GET['callback'])) {
    $content = file_get_contents('php://input');
    $data = json_decode($content, true);
    $wc_key = $data['consumer_key'];
    $wc_secret = $data['consumer_secret'];
    $shop = strtolower(base64_decode($data['user_id'], true));
    $currentuserid = $_GET['user_id'];
    $errors = array();
    try {
        $wc_api = new Client($shop, $wc_key, $wc_secret, ['wp_api' => true, 'version' => 'wc/v3', 'verify_ssl' => false]);

        $endpoint = 'system_status';
        $params = array('q' => '/wp-json/wc/v3/' . $endpoint);

        $return = $wc_api->get($endpoint, $params);

        $wc_version = '3.4.5';
        $shop = strtolower($wpdb->escape($shop));
        $key_hashed = wc_api_hash($wc_key);
        $pa_version = get_post_meta(95239, 'ryankikta_plugin_version', true);
        $version = $return->environment->version;

        foreach ($return->active_plugins as $plugin) {
            if ($plugin->name == 'RyanKikta WooCommerce API') {
                $pa_version = $plugin->version;
            }
            if ($plugin->name == 'WooCommerce') {
                $wc_version = $plugin->version;
            }
        }

        $wooc_auth = array('woocommerceshop' => $shop, 'wc_key' => $wc_key, 'wc_secret' => $wc_secret);
        $wooc_data = $wpdb->get_results("select * from wp_users_woocommerce where shop='$shop'");
        if (count($wooc_data) > 0 && ($wooc_data[0]->users_id != $currentuserid || $wooc_data[0]->active == 1)) {
            $errors[] = 'Shop : ' . $shop . ' already installed  in other account';
            update_user_meta($currentuserid, 'wc_install_error', $errors);
        } elseif (empty($wooc_data) || (count($wooc_data) > 0 && $wooc_data[0]->active == 0)) {
            $data = array();
            if (count($wooc_data) > 0) {
                $wpdb->query(
                    "update wp_users_woocommerce set wc_key='$wc_key',wc_key_hash='$key_hashed',wc_secret='$wc_secret',wc_version='$wc_version',pa_version='$pa_version',version='$version',active=1 where users_id=$currentuserid and shop='$shop'"
                );
            } else {
                $shop_id_deleted = $wpdb->get_var("select shop_id from wp_users_shops_deleted where shop_type='woocommerce' and users_id=$currentuserid and shop_name='$shop'");
                if ($shop_id_deleted == null) {
                    $old_url = str_replace('https:', 'http:', $shop);
                    $shop_id_deleted = $wpdb->get_var("select shop_id from wp_users_shops_deleted where shop_type='woocommerce' and users_id=$currentuserid and shop_name='$old_url'");
                }
                $sql = "insert into wp_users_woocommerce (id,users_id,shop,wc_key,wc_key_hash,wc_secret,version,wc_version,firstimedone,active,pa_version,status_accepted) values ($shop_id,$currentuserid,'$shop','$wc_key','$key_hashed','$wc_secret','$version','$wc_version',1,1,'$pa_version','processing')";
                $shop_id = $shop_id_deleted == null ? 'NULL' : (int) $shop_id_deleted;
                $wpdb->query(
                    "insert into wp_users_woocommerce (id,users_id,shop,wc_key,wc_key_hash,wc_secret,version,wc_version,firstimedone,active,pa_version,status_accepted) values ($shop_id,$currentuserid,'$shop','$wc_key','$key_hashed','$wc_secret','$version','$wc_version',1,1,'$pa_version','processing')"
                );
                if ($shop_id_deleted != null) {
                    $wpdb->query("delete from wp_users_shops_deleted where users_id=$currentuserid and shop_type='woocommerce' and shop_id=$shop_id_deleted");
                }
            }
            $shop_id = $wpdb->get_var("select id from wp_users_woocommerce where `wc_key`='" . $wc_key . "' and `users_id`='" . $currentuserid . "';");
            $wc_auth = getWoocommerceShopbyId($shop_id);
            create_shipping_zones_woocommerce($wc_auth);
            update_user_meta($currentuserid, 'wc_install_shop_id', $shop_id);
            //order hook
            $data['name'] = 'RyanKikta Order Hook';
            $data['topic'] = 'order.created';
            $data['delivery_url'] = $site_url . '/woocommerce-order-hook/?shop_id=' . $shop_id;
            $data['api_version'] = '-1';
            $order_cre = create_wooc_webhook($wc_auth, $data);
            $data['topic'] = 'order.updated';
            $order_up = create_wooc_webhook($wc_auth, $data);
            //mail('team@ryankikta.com','order hooks',var_export(array($shop,$currentuserid,$order_cre,$order_up,$wc_key,$wc_secret),true));
            //product hook
            /*
                    $data['name']          = 'RyanKikta Product Hook';
                    $data['topic']         = 'product.deleted';
                    $data['delivery_url']  = $site_url.'/woocommerce-product-hook/?shop_id='.$shop_id;
                    create_wooc_webhook($wooc_auth,$data);
                    $data['topic']         = 'product.updated';
                    create_wooc_webhook($wooc_auth,$data);
            */
        }
    } catch (HttpClientException $e) {
        $errors = generate_errors_wooc($e, $wc_api->environment->site_url);
        if (empty($errors)) {
            $errors[] = 'Please check the shop ,the key and the secret';
        }
        update_user_meta($currentuserid, 'wc_install_error', $errors);
        wp_mail('team@ryankikta.com', 'order hooks errors', var_export(array($shop, $currentuserid, $errors, $wc_key, $wc_secret), true));
    }
}
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $shop = base64_decode($_GET['user_id'], true);
    $shop = strtolower($wpdb->escape($shop));
    $wooc_data = $wpdb->get_results("select * from wp_users_woocommerce where shop='$shop'");
    if (empty($wooc_data) || (count($wooc_data) > 0 && $wooc_data[0]->active == 0)) {
        $session_name = $currentuserid . '_wc_installed';
        $_SESSION[$session_name] = 1;
    }
    wp_redirect($site_url . '/woocommerce/');
}

if (0 == $current_user->ID) {
    wp_redirect('/login');
    exit();
}
get_header();
?>
<!--//this is where sidebar was-->
<div class="content-wrapper">
    <br/>
    <div class="page-wrapper">
        <?php while (have_posts()) {
            the_post();
            $content = apply_filters('the_content', get_the_content());
            ?>
            <a href="/my-stores/">My Stores</a>
            <?php
        } ?>
        <div>
            <?php
            // Get Balance, if 0 show a warrning message
            $query = ("SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid");
            $balance = $wpdb->get_row($query);
            //$balance = $balance[0];
            $automatic_payment = get_user_meta($currentuserid, 'autopayment', true);
            if ($balance == 0 && $automatic_payment != 1) { ?>
                <p>
                <div>
                    <strong>Your current Balance is $0.00 . </strong> Any orders placed from your shop will be put ON
                    HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds into
                    your account or setup automatic billing.
                </div>
                </p>
                <?php }
            $shop_id = get_user_meta($currentuserid, 'wc_install_shop_id', true);
            $errors = get_user_meta($currentuserid, 'wc_install_error', true);
            if ($errors != '') {

                delete_user_meta($currentuserid, 'wc_install_error');
                delete_user_meta($currentuserid, 'wc_install_shop_id');
                ?>
                <p>
                <div>
                    <?php foreach ($errors as $error) {
                        echo $error . '<br>';
                    } ?>
                </div>
                </p>
                <?php
            }
            if ((int) $shop_id > 0) {
                delete_user_meta($currentuserid, 'wc_install_shop_id');
                $session_name = $currentuserid . '_wc_installed';
                $_SESSION[$session_name] = 1;
                $location = "/woocommerce-shop/?id=$shop_id";
                wp_redirect($location);
                exit();
            }
            // See if this user has a shop installed.
            $ryankikta_plugin_date = get_post_meta(95239, 'ryankikta_plugin_date', true);
            $pa_plugin_version = get_post_meta(95239, 'ryankikta_plugin_version', true);
            $content = str_replace(array('[PAP_DATE]', '[PAP_VERSION]'), array($ryankikta_plugin_date, $pa_plugin_version), $content);
            echo $content . '<br />';
            ?>
            <p>
            <form method="POST" action="/woocommerce">
                <h3>Woocommerce Shop</h3>
                <p>(Enter the URL of your shop like http://www.heartshirts.com)</p><br/>
                <input id="woo_shop" type="text" name="wc_shop" style="margin-bottom:0;"value="<?php echo $_POST['wc_shop']; ?>"/>
                <input type="hidden" name="wcconf" value="1"/><br/>
                <input type="submit" id="install" value="Install"/>
                <!--img id="ajax-loading" src="/ajax-loader.gif"/-->
            </form>
            </p>
            <?php
            if($currentuserid == 57880) { echo ("Hi Ryan"); }
            ?>
            <!--script>
                jQuery(document).ready(function ($) {
                    jQuery("#install").on('click', function (event) {
                        event.preventDefault();
                        $("#ajax-loading").show();
                        $("#install").hide();
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {action: 'woo_discover', wc_shop: $("#woo_shop").val()},
                            //console.log("Shop Value!");
                            success: function (data, textStatus, jqXHR) {
                                $("#ajax-loading").hide();
                                window.location = data;
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                $("#ajax-loading").hide();
                                $("#install").show();
                            }
                        });
                        return false;
                    });
                });
            </script-->
            <?php
//}
?>
        <div>
            <?php
            // Get Balance, if 0 show a warrning message
            $query = $wpdb->get_results("SELECT `balance` FROM `wp_users` WHERE `id` = $currentuserid");
            $balance = $wpdb->get_val;
            //$balance = $balance[0];
            $automatic_payment = get_user_meta($currentuserid, 'autopayment', true);
            if ($balance == 0 && $automatic_payment != 1) { ?>
                <p>
                <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;">
                    <strong>Your current Balance is $0.00 . </strong> Any orders placed from your shop will be put ON
                    HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds into
                    your account or setup automatic billing.
                </div>
                </p>
                <?php }
            if ($_GET['action'] == 'deleteshop') {
                if (isset($_GET['id'])) { ?>
                    <?php
                    $shop_id = (int) $_GET['id'];
                    $userid_belongsto = $wpdb->get_var("select users_id from wp_users_woocommerce where id=$shop_id");
                    if ($currentuserid == $userid_belongsto) {
                        $auth = getWoocommerceShopbyId($shop_id);
                        @extract($auth);
                        if (!empty($auth)) {
                            $webhooks = get_wooc_webhook($auth);
                            foreach ($webhooks as $webbhook) {
                                $delivery_url = $webbhook->delivery_url;
                                $host = parse_url($delivery_url);
                                if ($host['host'] == 'ryankikta.com') {
                                    delete_wooc_shop_webhook($webbhook->id, $shop_id);
                                }
                            }
                        }
                        $wpdb->query("insert into `wp_users_shops_deleted` (`users_id`, `shop_name`, `shop_type`, `shop_id`, `deleted_at`,`type`) 
                            VALUES ($currentuserid,'$woocommerceshop','woocommerce',$shop_id,'" .  Date('Y-m-d H:i:s', time()) ."','live');");
                        $wpdb->query("DELETE FROM `wp_users_woocommerce` WHERE `id` = $shop_id");
                        ?>
                        <div style="border: 1px solid #006600; padding: 10px; background-color: #EDFFF2;width: 98%;margin: 0 auto; margin-bottom: 20px;">
                            <p> Shop Successfully deleted. </p>
                        </div>
                        <?php
                    }
                    } else { ?>
                    <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;margin-bottom: 20px;">
                        An error has occurred while deleting the shop
                    </div>
                    <?php }
            }
            // See if this user has a shop installed.
	    $shops = $wpdb->get_results("SELECT `id`,`wc_key`,`wc_secret`,`shop`,`dateadded`,`active` FROM `wp_users_woocommerce` WHERE `users_id` = $currentuserid", ARRAY_A);
	    print_r ($shops);
                $list_shops = '';
                foreach ($shops as $key=>$rows) {
                    $date = date('d/m/Y', strtotime($rows['dateadded']));
                    $check_call_api = true;
                    $status = '';
                    $error = 'Error';
                    $wc_auth = getWoocommerceShopbyId($rows['id']);
                    $res = get_wooc_shop_data($wc_auth);
                    if (!$res->store && !$res->environment) {
                        $check_call_api = false;
                        $error = $res;
                    }
                    if ($check_call_api) {
                        $status =
                            "<span>Active</span>";
                    } else {
                        $status =
                            "<span>" . $error . "</span>";
                    }
		    echo ("<table class='table_base'>
			    <tr>
			       <td style='border: 1px solid #e5e5e5;'>" . $rows['shop'] . "</td>
			       <td style='border: 1px solid #e5e5e5;'>" . $date . "</td>
			       <td style='border: 1px solid #e5e5e5;'>" . $status ."</td>
			       <td style='border: 1px solid #e5e5e5;'>
			           <a class='confirmshop' href='/woocommerce/?action=deleteshop&id=" . $rows['id'] . "'>Remove</a>
                               </td>
			   </tr>
                           </table>");
                }
            ?>
            <br class="clear">
	</div>
</div>
    </div> <!-- content-wrapper -->
</div>
</div>
    <?php get_footer(); ?>


