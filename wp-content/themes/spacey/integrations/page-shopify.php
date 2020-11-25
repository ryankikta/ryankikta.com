<?php
/* Template Name: Shopify */
require_once(ABSPATH . "wp-content/themes/ryankikta/product-functions/shopify-functions.php");
wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}
$error = null;
$sucess = null;
if (isset($_GET['code'])) {
    if (isset($_GET['shop'])) {
        $response = InstallShopShopifyUser($currentuserid, $_GET['code'], $_GET['shop']);
        if (isset($response["data"])) {
            $shop_id = $response["data"];
            $location = "/shopify/?firsttimeinstall=1&id=" . $shop_id;
            wp_redirect($location);
            exit();
        } elseif (isset($response["error"])) {
            $_SESSION['error_shopify'] = $response["error"];
        } else {
            $_SESSION['error_shopify'] = "An error has occurred while attempting to install shop : " . $_GET['shop'];
        }
    } else {
        $_SESSION['error_shopify'] = "Shopify shop was not found. Please check and try again later";
    }
    wp_redirect("/shopify/");
    exit();
} // if they posted the form with the shop name
else if (isset($_POST['shop']) || isset($_GET['shop'])) {
    $scope = "write_content,write_products,write_orders,write_customers,read_shipping,write_inventory,write_fulfillments";
    $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
    $response = getShopifyAuthorizeUrl($shop, $scope, $currentuserid);
    //echo json_encode($response);die;
    //wp_mail('team@ryankikta.com','shopify',var_export($response,true));
    if (isset($response["data"])) {
        $location = $response["data"];
        //echo $location;die;
        wp_redirect($location);
        exit();
    } elseif (isset($response["error"])) {
        $_SESSION['error_shopify'] = $response["error"];
    } else {
        $_SESSION['error_shopify'] = "An error has occurred while attempting to install shop : " . $shop;
    }
    wp_redirect("/shopify/");
    exit();
}
if (isset($_SESSION['error_shopify'])) {
    $error = $_SESSION['error_shopify'];
}
if (isset($_SESSION['sucess_shopify'])) {
    $sucess = $_SESSION['sucess_shopify'];
}
// ###################################################################################
// Delete Shop Action
// ###################################################################################
if ($_GET['action'] == "deleteshop") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $response = DeleteShopShopifyById($id, $currentuserid);
        if (isset($response)) {
            if ($response["status"] == 400) {
                $_SESSION['error_shopify'] = $response["error"];
            } elseif ($response["status"] == 200) {
                $_SESSION['sucess_shopify'] = "Shop Successfully deleted. ";
            }
        } else {
            $_SESSION['error_shopify'] = "An error has occurred while deleting the shop. Please try again later";
        }
    } else {
        $_SESSION['error_shopify'] = "Shopify shop was not found. Please check and try again later";
    }
    wp_redirect("/shopify/");
    exit();
}
// ###################################################################################
// Loading shopify shops Action
// ###################################################################################
$response = getListShopifyShopsByUserId($currentuserid);
$numshops = 0;
$shops = array();
if (isset($response["data"])) {
    $shops = $response["data"];
    $numshops = count($shops);
} else {
    $numshops = -1;
    $error = "An error occurred while loading the list of your shopify shops. Please try again later";
}
// ###################################################################################
// Loading User Balance
// ##################################################################################
@extract(getUserBalanceByUserId($currentuserid));
if (isset($_SESSION['error_shopify'])) {
    $error = $_SESSION['error_shopify'];
}
if (isset($_SESSION['sucess_shopify'])) {
    $sucess = $_SESSION['sucess_shopify'];
}
?>

<div class="container-fluid">
<?php while (have_posts()) {
{ the_post();
    $content = apply_filters('the_content', get_the_content());
}
?>
<div>
    <?php 
    if (isset($error) && ($error != "")) { ?>
        <div>
             <?php echo $error ?>
        </div>
    <?php 
    } 
    if (isset($sucess) && ($sucess != "")) { ?>
        <div>
             <?php echo $sucess ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-12 col-lg-6">
            <h2>To Install a Shopify Shop, Please enter the Shop URL:</h2>
            <ul>
                <li>Don’t have a shop to install your app in handy? <a href="http://www.shopify.com/?ref=tshirts">Signup for Shopify</a> or <a href="https://app.shopify.com/services/partners/api_clients/test_shops?ref=tshirts">Create a test shop</a>.</li>
                <li>If you have already installed <a href="https://apps.shopify.com/print-aura" target="_blank" rel="noopener noreferrer">our APP</a> simply login to your Shop at Shopify and you can Manage Products under or Add new Products under.</li>
                <li>If you have any issues getting your store setup please <a href="/contactus/">contact us.</a></li>
            </ul>
            <form class="mb-3" action="" method="post">
                <div class="input_outline input_flex">
                    <label for="shop"><strong>SHOP URL</strong><span> (Excluding (http://, https:// or www.))</span></label>
                    <input id="shop" name="shop" type="text" value="" placeholder="myshop.myshopify.com">
                </div>
                <input class="btn-primary" name="commit" type="submit" value="Install">
            </form>
        </div>
    </div>
    
    <?php 
    if ($numshops == 0) {
        echo "No Shops Installed";
    } else {
        if ($balance == 0 && $automatic_payment != 1) {
            ?>
            < class="message message_error text-center my-20">
                <p class="fs3 mb-0"><strong>Your current Balance is $0.00 . </strong> Any orders placed from your shop will be put ON HOLD automatically, to avoid this please click <a href="/billing"> here </a> to deposit funds into your account or setup automatic billing.</p>
            </>
            <div class="clearfix"><br></div>
            <?php
        }
    }
    ?> <table class='table_base'> <?php
    foreach ($shops as $key=> $rows) {
        $date = date("d/m/Y", strtotime($rows['dateadded']));
	$status = "";
        if ($rows['active'] > 0 ) {
            $status = "<span>Active</span>";
        } else {
            $status = "<span>Inactive</span>";
        }
	echo ("<tr>
                   <td>" . $rows['shop'] . "</td>
                   <td>" . $date . "</td>
                   <td>" . $status . "</td>
                   <td>
                        <a class='confirmshop' href='/shopify/?action=deleteshop&id=" . $rows['id'] . "'>Remove</a>
                    </td>
		</tr>");
    }
    ?> </table> <?php
?>
<br class="clear">
</div>
<div>
    <?php if (isset($error) && ($error != "")) { ?>
        <div>
            <?php echo $error ?>
        </div>
    <?php } ?>
    <?php if (isset($sucess) && ($sucess != "")) { ?>
        <div>
            <?php echo $sucess ?>
        </div>
    <?php } ?>
    <?php echo $content; ?>
</div>
<?php
}
unset($_SESSION['sucess_shopify']);
unset($_SESSION['error_shopify']);
?>
</div>
<?php //get_footer(); ?>

