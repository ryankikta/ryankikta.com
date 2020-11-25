<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$gumroad_api = new GumroadAPI();
$res = $gumroad_api->ListProductsbyUser($currentuserid); //debug($res);die;
$errorMsg = null;
$products = array();
if (isset($res["statut"])) {
    if ($res["statut"] == 400) {
        $errorMsg = "List of your gumroad products temporarily unavailable";
    } else {
        if (isset($res["list"])) {
            $products = $res["list"];
        }
    }
} else {
    $errorMsg = "List of your gumroad products temporarily unavailable";
}

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $has_edit = true;
    $productid = (int)$_GET['id'];
    $token_gumroad = null;
    $res = $gumroad_api->getShopbyId($wpdb->escape($_GET['shop_id']));
    if (isset($res["statut"])) {
        if ($res["statut"] == 200) {
            if (isset($res["data"])) {
                $token_gumroad = $res["data"];
            }
        }
    }
    $gumroad_current_data = $gumroad_api->product_data($productid);
    debug($gumroad_current_data);
    @extract($gumroad_current_data);
    if ($gumroadactiveold == 1) {
        $selected = "selected";
    } else {
        $selected = "";
    }
} else {
    $has_edit = false;
    $selected = "";
}
?>
<div id="gumroadspecific">
    <p class="app_title"><strong>Gumroad Specific Settings</strong></p>
    <div>
        <div style="width: 100%;float: left;">
            <strong>Active</strong><br/>
            <span class="italic_text">Do you want this product to be displayed in your store? Select Yes to display </span><br/><br/>

            <select name="gumroadactive" id="gumroadactive">
                <option value="0">No</option>
                <option value="1" <?php echo $selected; ?> >Yes</option>
            </select>
        </div>
        <br class="clear">
        <?php if (($has_edit == false) || (($has_edit == true) && ($gumroadactiveold == 0))) { ?>
            <div style="width: 100%;float: left;display: none" id="gumroad_data">
                <span>New Product or Assign to an Existing Product</span><br/>
                <?php if (isset($errorMsg)) { ?>
                    <span style="color: #ff0000;"><strong><?php echo $errorMsg; ?></strong> <a
                                href="<?php echo site_url(); ?>/contactus">Contact Us</a></span>
                <?php } else { ?>
                    <select name="gumroadproducts" id="gumroadproducts">
                        <option value="0" selected="">New Product</option>
                        <?php
                        if (isset($products)) {
                            if (count($products) > 0) {
                                foreach ($products as $product) {
                                    echo ' <option value="' . $product["id"] . '">' . $product["name"] . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                <?php } ?>
            </div>
        <?php } ?>

    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#gumroadactive").on("change", function (e) {
            if (jQuery(this).val() == 1) {
                jQuery("#gumroad_data").slideDown("slow");
            } else {
                jQuery("#gumroad_data").slideUp("slow");
            }
        });
    });
</script>