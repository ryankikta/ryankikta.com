<?php
require ABSPATH . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

function getWoocommerceShop($user_id)
{
    $woo_shop = array();
    $checkuser = $wpdb->get_result("select `id`,`shop`,`token`,`version`,`wc_key`,`wc_secret`,`wc_version`,`pa_version` from `wp_users_woocommerce` where `users_id` = $user_id");

    if ($wpdb->num_rows($checkuser) != 0) {

        $shoprow = $wpdb->get_row($checkuser);
        $shop_id = $shoprow[0];
        $woocommerceshop = $shoprow[1];
        $woocommercetoken = $shoprow[2];
        $wc_version = $shoprow[3];
        $wc_key = $shoprow[4];
        $wc_secret = $shoprow[5];
        $pa_version = $shoprow[6];

        $woo_shop = array("shop_id" => $shop_id, "user_id" => $user_id, 'woocommerceshop' => $woocommerceshop, 'woocommercetoken' => $woocommercetoken, 'wc_version' => $wc_version, 'wc_key' => $wc_key, 'wc_secret' => $wc_secret, 'pa_version' => $pa_version);

    }

    return $woo_shop;
}

function getWoocommerceShopbyId($id)
{
    GLOBAL $wpdb;
    $woo_shop = array();
    $checkuser = $wpdb->get_results("select `users_id`,`shop`,`token`,`version`,`wc_key`,`wc_secret`,`pa_version` from `wp_users_woocommerce` where `id` = $id");
    if ($wpdb->num_rows != 0) {
        $shoprow = $wpdb->get_row($checkuser);
        $user_id = $shoprow[0];
        $woocommerceshop = $shoprow[1];
        $woocommercetoken = $shoprow[2];
        $wc_version = $shoprow[3];
        $wc_key = $shoprow[4];
        $wc_secret = $shoprow[5];
        $pa_version = $shoprow[6];
        $woo_shop = array("shop_id" => $id, "user_id" => $user_id, 'woocommerceshop' => $woocommerceshop, 'woocommercetoken' => $woocommercetoken, 'wc_version' => $wc_version, 'wc_key' => $wc_key, 'wc_secret' => $wc_secret, 'pa_version' => $pa_version);
    }
    return $woo_shop;
}

function check_woocommerce_shop($user_id, $type = 1)
{
    global $wpdb;
    $count = $wpdb->get_var("select count(id) from wp_users_woocommerce where users_id=$user_id");
    if ($type == 2)
        return (int)$count;
    if ($count > 0)
        return true;
    return false;
}

function getCurrentWoocommerceData($prodid)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $woocommerceactive = $row['woocommerceactive'];
    $woocommerce_id = $row['woocommerce_id'];
    $woocommercecategory = $row['woocommerce_cat_id'];
    $woocommerceshippingid = $row['woocommerce_shipping_id'];
    $woocommerceshortdesc = base64_decode($row['woocommerce_shortdesc']);

    $woocommerce_data = array('woocommerceactiveold' => $woocommerceactive, 'woocommerce_id' => $woocommerce_id,
        'woocommercecategory' => $woocommercecategory, 'woocommerceshippingid' => $woocommerceshippingid, 'woocommerceshortdesc' => $woocommerceshortdesc);
    return $woocommerce_data;
}

function getWoocommerceData($data)
{
    $woocommerceshortdesc = str_replace("\r\n", "", $data['woocommerce_shortdesc']);
    $i = 0;
    while ($i < strlen($woocommerceshortdesc)) {
        $woocommerceshortdesc = str_replace("\\", '', $woocommerceshortdesc);
        $i++;
    }

    $woocommerceshippingid = esc_sql($data['woocommerceshippingid']);

    return array(
        'woocommerceactive' => ($data['woocommerceactive']) ? esc_sql($data['woocommerceactive']) : 0,
        'woocommercenewproduct' => esc_sql($data['woocommercenewproduct']),
        'woocommercecategory' => (!isset($data['woocommercecategory']) || empty($data['woocommercecategory']) ? array(0) : $data['woocommercecategory']),
        'woocommerceshippingid' => ($woocommerceshippingid == "" ? 0 : $woocommerceshippingid),
        'woocommerceshortdesc' => $woocommerceshortdesc
    );
}

function get_current_woocommerce_data_shop($prodid, $shop_id)
{

    $woocommerce_id = get_product_meta_shop($prodid, "woocommerce_id", $shop_id);
    $woocommercecategory = get_product_meta_shop($prodid, "woocommerce_cat_id", $shop_id);
    $woocommerceshippingid = get_product_meta_shop($prodid, "woocommerce_shipping_id", $shop_id);
    $woocommerceshortdesc = base64_decode(get_product_meta_shop($prodid, "woocommerce_shortdesc", $shop_id));

    return array('woocommerce_id' => $woocommerce_id,
        'woocommercecategory' => $woocommercecategory,
        'woocommerceshippingid' => $woocommerceshippingid,
        'woocommerceshortdesc' => $woocommerceshortdesc);
}

function get_woocommerce_data_shop($data, $shop_id)
{
    $woocommerceshortdesc = str_replace("\r\n", "", $data['woocommerce_shortdesc' . $shop_id]);
    $i = 0;
    while ($i < strlen($woocommerceshortdesc)) {
        $woocommerceshortdesc = str_replace("\\", '', $woocommerceshortdesc);
        $i++;
    }
    $woocommerceshippingid = esc_sql($data['woocommerceshippingid' . $shop_id]);
    return array(
        'woocommercenewproduct' => (isset($data['woocommercenewproduct' . $shop_id])) ? esc_sql($data['woocommercenewproduct' . $shop_id]) : 0,
        'woocommercecategory' => (!isset($data['woocommercecategory' . $shop_id]) || empty($data['woocommercecategory' . $shop_id]) ? array(0) : $data['woocommercecategory' . $shop_id]),
        'woocommerceshippingid' => ($woocommerceshippingid == "" ? 0 : $woocommerceshippingid),
        'woocommerceshortdesc' => $woocommerceshortdesc
    );
}

function getWoocommerceProductData($woocommerce_id, $auth)
{
    if ($woocommerce_id != 0 && !empty($auth)) {

        $woo_response = getWooproduct($woocommerce_id, $auth);
        if (isset($woo_response->id)) {
            $wooc_variants = get_wooc_product_variations($woocommerce_id, $auth);
            return array(1, $wooc_variants);
        }
    }
    return array(0, NULL);
}

function get_woocommerce_product($woocommerce_id, $auth, $params = array())
{
    if ($woocommerce_id != 0 && !empty($auth)) {

        $woo_response = getWooproduct($woocommerce_id, $auth);
        return $woo_response;
    }
    return false;
}

function get_woocommerce_product_import($user_id, $woocommerce_id, $shop_id = 0)
{
    try {

        $auth = ($shop_id != 0) ? getWoocommerceShopbyId($shop_id) : getWoocommerceShop($user_id);
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/$woocommerce_id";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'fields' => 'name,description,sku,weight,tags,shipping_class_id,categories,short_description,images,variations');
        $images = array();
        $product = $wc_api->get($endpoint, $params);

        foreach ($product->images as $img) {
            $images[] = $img->src;
        }

        $colors = array();
        foreach ($product->variations as $variant) {
            foreach ($variant->attributes as $attribute) {
                if (get_color_id($attribute->option) != NULL) {
                    $colors[$attribute->option][] = $variant->price;
                    break;
                }
            }
        }

        $shop_colors = array();
        foreach ($colors as $color => $prices) {
            $shop_colors[$color][] = min($prices);
            $shop_colors[$color][] = max($prices);
        }

        $tags = array();
        $cats = array();
        foreach ($product->tags as $tag)
            $tags[] = $tag->name;

        foreach ($product->categories as $cat)
            $cats[] = $cat->id;

        $data = array(
            "title" => $product->name,
            "sku" => $product->sku,
            "description" => $product->description,
            "weight" => $product->weight / 0.45359237,
            "tags" => implode(",", $tags),
            "woocommerceshippingid" => $product->shipping_class_id,
            "woocommercecategory" => implode(",", $cats),
            "woocommerceshortdesc" => $product->short_description,
            "shop_images" => $images,
            "shop_colors" => $shop_colors,
        );
        return $data;

    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e, $woocommerce_id);
        return $return;
    }
}

function getWooPAVersion($user_id)
{

    global $wpdb;
    $version = 2;
    $version = $wpdb->get_var("select version from wp_users_woocommerce where users_id=$user_id");

    return $version;
}

function getWooPAVersionByshop($shop_id)
{

    global $wpdb;
    $version = 2;
    $version = $wpdb->get_var("select version from wp_users_woocommerce where id=$shop_id");

    return $version;
}

function displayWooNoticeBox($user_id)
{

    $version = getWooPAVersion($user_id);

    if ($version == 1) {
        ?>
        <div class="message-box-wrapper red">
            <div class="message-box-content">
                ATTN: We did a major upgrade to our plugin to work with the new Woocommerce API. In order to add/edit
                products you need to doing a few upgrades.
                <?php
                $url_plugin = 'https://ryankikta.com/wp-content/uploads/' . get_post_meta(95239, "ryankikta_plugin_date", true) . '/ryankikta-woocommerce-api-v' . get_post_meta(95239, "ryankikta_plugin_version", true) . '.zip';
                ?>
                1. Make sure you have the most recent version of Woocommerce installed. <br/>
                2. Install the latest PA woo plugin (From Wordpress Dashboard by updating the plugin OR remove the old
                and install the new version which can be downloaded <a href="<?php echo $url_plugin; ?>"
                                                                       target="_blank"> here </a> )<br/>
                3. In Woocommerce settings you need to enable the REST API and generate a key/secret and set appropriate
                read/write access. You can find a guide <a
                        href="http://docs.woothemes.com/document/woocommerce-rest-api/" target="_blank"> here</a> .<br/>
                5. Remove the shop on <a href="/woocommerce/"> Ryan Kikta</a> and then reinstall it using the
                key/secret. Your existing products will remain active in your Woocommerce after reinstalling the plugin
                on Ryan Kikta. <br/>
                <br/>
                If you have any issues/questions please <a href="/contactus/" target="_blank"> contact us</a>


            </div>
        </div>

        <?php

    }
}

/****************Woocommerce Functions Version 1********************************************/
function Create_Variants_Woocommerce($data, $variants)
{
    $ret = array();
    extract($data);
    extract($variants);
    foreach ($variants as $key => $variant) {
        $variant_wc = array(
            'name' => stripslashes($title) . "-" . $variant['color_name'] . "-" . $variant['size_name'],
            'price' => $variant['price'],
            'regular_price' => $variant['price'],
            'sku' => stripslashes($sku) . "-" . $variant['color_name'] . "-" . $variant['size_name'],
            'visibility' => 'visible',
            'product_type' => 'simple',
            'type' => 'product_variation',
            'status' => 'instock',
            'size_attribute' => $variant['size_name'],
            'color_attribute' => $variant['color_name'],
        );
        if ($woocommercenewproduct != 0) {
            if (isset($woo_variants[$variant['size_name'] . '-' . $variant['color_name']]) && $woo_variants[$variant['size_name'] . '-' . $variant['color_name']] != "") {
                $variant_wc['id'] = $woo_variants[$variant['size_name'] . '-' . $variant['color_name']];
            }
        }
        if (in_array($variant['color_id'], $defaults)) {
            array_unshift($ret['variants_wc'], $variant_wc);
        } else {
            $ret['variants_wc'][] = $variant_wc;
        }
        $ret['alread_vars'][] = $variant['size_name'] . '-' . $variant['color_name'];
        $ret['size_arr'] = $size_arr;
        $ret['color_arr'] = $color_arr;
    }
    return $ret;
}

function Prepare_Data_Woocommerce($data, $variants_wc)
{
    global $wpdb;
    $ret = array();
    extract($data);
    extract($variants_wc);
    foreach ($woo_variants as $key => $var_id) {
        if (!in_array($key, $alread_vars)) {
            $toremove[] = $var_id;
        }
    }
    $product_data = array(
        'name' => stripslashes($title),
        'description' => nl2br(str_replace(array('"', '<p>&nbsp;</p>', '\r\n', '\r'), array('&quot;', '<br />', '<br />', '<br />'), stripslashes($description)), false),
        'short_description' => str_replace('"', '&quot;', stripslashes($woocommerceshortdesc)),
        'sku' => stripslashes($sku),
        'visibility' => 'visible',
        'product_type' => 'variable',
        'type' => 'product',
        'attributes' => array(
            'size' => array(
                'name' => 'size',
                'value' => $size_arr,
                'is_variation' => 'yes',
                'is_visible' => 'yes',
                'is_taxonomy' => 'no'
            ),
            'color' => array(
                'name' => 'color',
                'value' => $color_arr,
                'is_variation' => 'yes',
                'is_visible' => 'yes',
                'is_taxonomy' => 'no'
            )
        )
    );
    if ($woocommercenewproduct != 0) {
        $woo_id = $wpdb->escape($woocommercenewproduct);
        $product_data['id'] = $woo_id;
    }

    $product_data['categories'][] = array("id" => (empty($woocommercecategory) ? array(0) : $woocommercecategory));

    $product_data['shippingclasses'][] = array("id" => array($woocommerceshippingid));

    $woocommercetags = str_replace('\\', '', $tags);
    if (!empty($woocommercetags)) {
        $tags = explode(',', $woocommercetags);
        $data = array(
            'action' => 'print_aura_api',
            'proc' => 'get_tags',
            'arguments' => array(
                'token' => $woocommercetoken,
                'hide_empty' => false
            )
        );
        $result = WoocommerceApiCall($woocommerceshop, $data);
        $result = json_decode($result, true);
        $tag_exists = array();
        foreach ($result['payload'] as $wc_tag) {
            if (in_array($wc_tag['name'], $tags)) {
                $product_data['tags'][] = array(
                    "id" => $wc_tag["term_id"],
                );
                $tag_exists[] = $wc_tag['name'];
            }
        }
        $new_tags = array_diff($tags, $tag_exists);
        foreach ($new_tags as $tag) {
            $product_data['tags'][] = array(
                "name" => $tag,
                "slug" => str_replace(' ', '-', $tag),
                "taxonomy" => "product_tag",
                "group_id" => "0",
                "count" => "0",
                "parent_id" => "0",
            );
        }
    }
    $product_data['variations'] = $variants_wc;
    $ret['product_data'] = $product_data;
    $ret['toremove'] = $toremove;
    $ret['woocommerceshop'] = $woocommerceshop;
    $ret['woocommercetoken'] = $woocommercetoken;
    return $ret;
}

function UploadWoocommerceStoreImages($POST, $currentusername, $woocommerceactive, $woocommerce_id, $url, $token, $products_id)
{
    global $wpdb;
    $defaultdone = 0;
    $featured_set = 0;

    if ($woocommerceactive == 1) {
        // add images to Woocommerce

//delete image from woocommerce
        if (isset($POST['woocommercenewproduct']) && $POST['woocommercenewproduct'] != "") {
            $Image = GetWoocommerceProduct($woocommerce_id, $token, $url);

            $all_img = $Image["payload"][0]["images"];
            $featured_id = $Image["payload"][0]['featured_image'][0]['id'];
            foreach ($all_img as $img) {
                $ids[] = intval($img['id']);
            }
            $ids[] = intval($featured_id);
            DeleteWoocommerceImage($woocommerce_id, $ids, $token, $url);
        }
    }

    foreach ($POST['storeimages'] as $key => $image_id) {
        if ($image_id !== "") {

            $image_id = esc_sql($image_id);
            $sql = "SELECT `fileName` FROM `wp_userfiles` WHERE `fileID` = $image_id";
            $imagefilenamequery = $wpdb->get_result($sql) or die('this is the error   ' . mysql_error());
            $imagefilerow = $wpdb->get_row($imagefilenamequery);
            $imagefilename = $imagefilerow[0];
            $woocommerce_image_id = 0;
            $featured_image = 0;
            if ($defaultdone == 0 && $image_id == $POST['defaultimage'][0]) {
                $defaultdone = 1;
                $featured_image = 1;
                $featured_set = 1;
            }

            if ($woocommerceactive == 1) {
                // add images to Woocommerce
                $woocommerce_image_id = uploadWoocommerceImages1($url, $token, $woocommerce_id, $featured_image, $key, $imagefilename, $currentusername, count($POST['storeimages']));
            }
// Submit to products_images
            $woocommerce_image_id = ($woocommerce_image_id == "") ? 0 : $woocommerce_image_id;
            $sql = "update `wp_users_products_images` set `woocommerce_id`=$woocommerce_image_id  where `image_id`=$image_id and `users_products_id`=$products_id ";
            $query = $wpdb->get_result($sql);
            if (!$query) {
                $logs = array();
                $logs['sql'] = mysql_escape_string($sql);

                wp_insert_post(array(

                    'post_content' => esc_sql(var_export($logs, true)),
                    'post_title' => esc_sql("adding product image "),
                    'post_status' => 'draft',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_type' => 'systems'

                ));
                wp_mail('team@ryankikta.com', 'adding product image issue', '');
            }

        }
    }

    if ($featured_set != 1 && $woocommerceactive == 1) {
        $image_id = esc_sql($POST['storeimages'][0]);
        if ($image_id != "") {

            $imagefilenamequery = $wpdb->get_result("SELECT `fileName` FROM `wp_userfiles` WHERE `fileID` = $image_id");//or die(mysql_error());
            $imagefilerow = $wpdb->get_row($imagefilenamequery);
            $imagefilename = $imagefilerow[0];
            $ImgWoocommerce_id = uploadWoocommerceImages1($url, $token, $woocommerce_id, 1, 0, $imagefilename, $currentusername);
            $sql = "update `wp_users_products_images` set `defaultimage`=1 where `image_id`=$image_id and `users_products_id`=$products_id ";
            $return = $wpdb->get_result($sql);
            // wp_mail("aladin@ryankikta.com",'sql add default status',mysql_error().' '.$sql);
        }
    }

}

function addWoocommerceProduct($POST, $url, $token, $product_data, $size_arr, $color_arr, $currentuserid, $products_id, $toremove = array())
{
    global $wpdb;
    $list = get_html_translation_table(HTML_ENTITIES);
    unset($list['"']);
    unset($list['<']);
    unset($list['>']);
    unset($list['&']);

    $updatedescription = strtr($product_data['description'], $list);
    $updateshortdescription = strtr($product_data['short_description'], $list);
    $updatedescription = str_replace('"', '\"', $updatedescription);
    $updateshortdescription = str_replace('"', '\"', $updateshortdescription);
    $product_data['description'] = $updatedescription;
    $product_data['short_description'] = $updateshortdescription;

    //if($POST['woocommercenewproduct'] == 0){
    if (!empty($toremove)) {
        $delete = DeleteWoocommerceProduct($toremove, $token, $url);

    }
    //$product_data['description']=$updatedescription;
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'set_products',
        'arguments' => array(
            'token' => $token
        ),
        'payload' => array($product_data)
    );
    $data['model_filters'] = array(
        'WCAPI_product_meta_attributes_table' => array(
            'size_attribute' => array(
                'name' => 'attribute_size',
                'type' => 'string',
                'values' => $size_arr,
                'sizehint' => 3
            ),
            'color_attribute' => array(
                'name' => 'attribute_color',
                'type' => 'string',
                'values' => $color_arr,
                'sizehint' => 3
            )
        )

    );

    $result = WoocommerceApiCall($url, $data);

    $woocommercecreate = json_decode($result, true);
    if (!$woocommercecreate) {
        $_SESSION['errors1'][] = "We encountered a problem connecting to your woocommerce store. Please make sue you have followed the steps to install your shop.";
        wp_mail("aladin@ryankikta.com", "woocommerce response not valid", $result);
        wp_mail("team@ryankikta.com", "woocommerce response not valid", $result);
        wp_redirect("/manage-products?errormessage=couldnotaddtowoocommerce");
        exit();
    }
    /* if(current_user_can('manage_options'))
            die();*/
    $woocommerce_id = $woocommercecreate['payload'][0]['id'];
    $woocommercecreate = $woocommercecreate['payload'][0];
    //debug($woocommercecreate);
    if ($woocommerce_id != "") {
        //$wc_cat_id= ($wpdb->escape($_POST['woocommercecategory']) =="") ? 0 :$wpdb->escape($_POST['woocommercecategory']);
        $wc_cat_id = implode(",", $POST['woocommercecategory']);
        if ($wc_cat_id == NULL) {
            $wc_cat_id = 0;
        }

        $sql = "UPDATE `wp_users_products` SET `woocommerce_id` = $woocommerce_id , `woocommerceactive` = 1,`woocommerce_cat_id`='$wc_cat_id',`woocommerce_tags`='" . $wpdb->escape($_POST['tags']) . "', `updated`='" . date("Y-m-d H:i:s", time()) . "' WHERE `id` = $products_id";
        if (!$wpdb->get_result($sql)) {
            $logs['sql'] = mysql_escape_string($sql);
            $logs['data'] = $data;
            $logs['response'] = $woocommercecreate;
            wp_insert_post(array(

                'post_content' => esc_sql(var_export($logs, true)),
                'post_title' => esc_sql("Error updating woocommerce product id"),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'

            ));
            $post = $POST;
            // $post['errors'][]="Please";
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $products_id;
            $export = var_export($post, true);
            $_SESSION['errors'][] = "An error occured in our end. ";
            wp_mail("aladin@ryankikta.com", "woocommerce updating woocommerce product id error", $export);
            wp_mail("team@ryankikta.com", "woocommerce updating woocommerce product id error", $export);
            wp_redirect("/manage-products?errormessage=couldnotaddtowoocommerce");

            exit();
        }
        return array($woocommerce_id, $woocommercecreate);
    } else {
        //echo ('could not add to woocommerce');
        $post = array();
        $post['result'] = $result;
        $post['data'] = $POST;
        $post['url'] = $url;
        //$post['errors'][]="error in our end";
        $post['user_id'] = $currentuserid;
        $post['user_product_id'] = $products_id;
        $ret = json_decode($result, true);
        $_SESSION['errors'] = $ret['errors'];
        wp_mail('team@ryankikta.com', 'error add woocommerce product', var_export($post, true));
        wp_mail('team@ryankikta.com', 'error add woocommerce product', var_export($post, true));
        //wp_redirect( "/add-products?errormessage=couldnotaddtowoocommerce&action=delete&id=$products_id");
        wp_redirect("/manage-products?errormessage=couldnotaddtowoocommerce&action=delete&id=$products_id");
        exit();
    }
    //}

}

function updateWoocommerceVariants($POST, $token, $url, $woocommercecreate, $woocommerce_id, $products_id, $currentuserid)
{
    global $wpdb;


    $woocommerce_cat = esc_sql($POST['woocommercecategory']);
    $woocommerce_tags = esc_sql($POST['tags']);

    foreach ($woocommercecreate['variations'] as $key => $var) {

        $id = $var['id'];
        $color_name = $var['color_attribute'];
        $size_name = $var['size_attribute'];

        $color_id = $wpdb->get_var("SELECT `color_id` FROM `wp_rmproductmanagement_colors` where color_name='$color_name'");
        $size_id = $wpdb->get_var("SELECT `size_id` FROM `wp_rmproductmanagement_sizes` where size_name='$size_name'");

        if (!$wpdb->get_result("UPDATE `wp_users_products_colors` SET `woocommerce_id` = '$id' WHERE `color_id` = '$color_id' AND `size_id` = '$size_id' AND `users_products_id` = $products_id")) {

            $logs['sql'] = mysql_escape_string("UPDATE `wp_users_products_colors` SET `woocommerce_id` = '$id' WHERE `color_id` = '$color_id' AND `size_id` = '$size_id' AND `users_products_id` = $products_id");
            $logs['woocommercecreate'] = $var;
            $logs['variantid'] = $id;
            wp_insert_post(array(

                'post_content' => esc_sql(var_export($logs, true)),
                'post_title' => esc_sql("Error updating woocommerce variant"),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'

            ));
            $post = $POST;
            $post['errors'][] = "error in our end";
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $products_id;
            $export = export($post, true);
            $_SESSION['errors'][] = "An error occured in our end. ";
            wp_mail("aladin@ryankikta.com", "woocommerce add variants error", $export);
            wp_mail("team@ryankikta.com", "woocommerce add variants error", $export);
        }

    }


}

function DeleteWoocommerceProduct($ids, $token, $url)
{
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'delete_products',
        'arguments' => array(
            'token' => $token,
            'ids' => $ids
        )
    );
    $result = WoocommerceApiCall($url, $data);
    $result = json_decode($result, true);
    return $result;
}

function deleteAllProduct($token, $url, $post_id)
{
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'delete_products_all',
        'arguments' => array(
            'token' => $token,
            'id' => $post_id
        )
    );
    $result = WoocommerceApiCall($url, $data);
    $result = json_decode($result, true);
    return $result;
}

function DeleteWoocommerceImage($parent_id, $ids, $token, $url)
{
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'delete_images',
        'arguments' => array(
            'token' => $token,
            'ids' => $ids,
            'parent_id' => $parent_id
        )
    );
    $result = WoocommerceApiCall($url, $data);
    $result = json_decode($result, true);
    return $result;
}

function GetWoocommerceProduct($woocommerce_id, $token, $url)
{
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'get_products',
        'arguments' => array(
            'token' => $token,
            'ids' => array($woocommerce_id)
        )
    );
    $data['model_filters'] = array(
        'WCAPI_product_meta_attributes_table' => array(
            'size_attribute' => array(
                'name' => 'attribute_size',
                'type' => 'string',
                'sizehint' => 3
            ),
            'color_attribute' => array(
                'name' => 'attribute_color',
                'type' => 'string',
                'sizehint' => 3
            )
        )
    );

    $result = WoocommerceApiCall($url, $data);
    $result = json_decode($result, true);
    return $result;
}

function GetWoocommerceShippingClass($token, $url)
{

    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'get_shipping_class',
        'arguments' => array(
            'token' => $token,
            'hide_empty' => false,
        )
    );
    $result = WoocommerceApiCall($url, $data);

    $result = json_decode($result, true);

    return $result['payload'];
}

function getWoocommerceShipping($shopurl, $token)
{
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'get_shipping_methods',
        'arguments' => array(
            'token' => $token,

        )
    );

    $result = WoocommerceApiCall($shopurl, $data);
    return $result;
}

function GetWoocommerceCategory($token, $url)
{

    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'get_categories',
        'arguments' => array(
            'token' => $token,
            'hide_empty' => false,
        )
    );
    $result = WoocommerceApiCall($url, $data);

    $result = json_decode($result, true);

    return $result['payload'];
}

function getWooDropdownproducts($shopurl, $token)
{
    $data = array(
        'action' => 'print_aura_api',
        'proc' => 'get_products',
        'arguments' => array(
            'token' => $token,
            'order_by' => 'ID',
            'order' => 'asc',
            'include' => array(
                'variations' => false,
                'images' => false,
                'featured_image' => false,
                'categories' => false,
                'tags' => false,
                'reviews' => false,
                'variations_in_products' => true,
            ),
        ),
    );
    $result = WoocommerceApiCall($shopurl, $data);
    $data = json_decode($result, true);
    $return_pr = array();
    if (is_array($data) && $data['status'] == 'success') {
        foreach ($data['payload'] as $product) {
            $return_pr[$product['id']] = $product['name'];
        }
    }
    return $return_pr;
}

function getWoocommerceProductVariants($token, $shop, $product_id)
{
    $product = GetWoocommerceProduct($product_id, $token, $shop);
    $variants = array();
    foreach ($product['payload'][0]['variations'] as $variant) {
        $variants[$variant['size_attribute'] . '-' . $variant['color_attribute']] = $variant['id'];
    }
    return $variants;
}

function checkWoocommereUser($url, $token)
{
    $data_sent = array(
        'action' => 'print_aura_api',
        'proc' => 'get_system_time',
        'arguments' => array(
            'token' => $token,
        ),
    );
    $result = WoocommerceApiCall($url, $data_sent);
    $data = json_decode($result, true);
    return $data;
}

function WoocommerceApiCall($url, array $post = NULL)
{
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $data_return = array();
    $data_return['url'] = $url;
    $data_return['user_id'] = $currentuserid;
    $data_return['post'] = $post;
    $ch = curl_init();
    foreach ($post as &$value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64; rv:28.0) Gecko/20100101 Firefox/28.0");
    $result = curl_exec($ch);

    $data_return['result'] = $result;
    $data_return['user_id'] = $currentuserid;
    $js_decode = json_decode($result, true);
    if (!is_array($js_decode)) {
        $err_rt = array();
        $err_rt['curl_error'] = curl_error($ch);
        $err_rt['curl_errorno'] = curl_errno($ch);
        $err_rt['curl_result'] = $result;
        $err_rt['user_id'] = $currentuserid;
        $err_rt['shop'] = $url;
        $err_rt['post'] = $post;
        wp_mail('team@ryankikta.com', 'call return curl error', var_export($err_rt, true));
    }
    $pos = strpos($result, '{');
    $result = substr_replace($result, '', 0, $pos);
    $pos1 = strrpos($result, '}');
    $result = substr_replace($result, '', $pos1 + 1, (strlen($result) - $pos1));
    curl_close($ch);
    return $result;
}

/****************Woocommerce Functions Version 2********************************************/

/**********************************Woocommerce Product Function*****************************/
function build_woo_data($POST, $wc_data, $all_variants, $wc_auth, $type = 1, $old_product_variations = NULL)
{

    global $wpdb;
    @extract($wc_data);
    $old_images = array();
    $list = get_html_translation_table(HTML_ENTITIES);
    unset($list['"']);
    unset($list['<']);
    unset($list['>']);
    unset($list['&']);

    $updatedescription = strtr($description, $list);
    $updatedescription = str_replace(array("\\r", "\\n"), "", $updatedescription);

    $updatedescription = str_replace("\\r\\n", "", $updatedescription);
    $updatedescription = str_replace("\\", '', $updatedescription);
    $updatedescription = str_replace('"', '\"', $updatedescription);
    $updatedescription = trim($updatedescription);
    $woocommerceshortdesc = strtr($woocommerceshortdesc, $list);
    $woocommerceshortdesc = str_replace('"', '\"', $woocommerceshortdesc);


    $color_arr = $wc_data['color_arr'];
    $size_arr = $wc_data['size_arr'];
    // wp_mail('team@ryankikta.com','size array',var_export(  $size_arr ,true));
    if ($_SERVER['REMOTE_ADDR'] == "59.162.181.90") {
        asort($color_arr, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);

        $lp = 0;

        foreach ($color_arr as $key => $value) {
            $color_arr_new[$lp] = $value;
            $lp++;
        }

        $color_arr = $color_arr_new;

        $lp = 0;
        foreach ($size_arr as $key => $value) {
            $s_order_query = $wpdb->get_row("select * from `wp_rmproductmanagement_sizes` where size_name = '" . $value . "' ");
            $size_arr_new[$lp]['name'] = $value;
            $size_arr_new[$lp]['ordering'] = $s_order_query->s_ordering;
            $lp++;
        }

        $size_arr_order = array_orderby($size_arr_new, 'ordering', SORT_ASC);

        $lp = 0;
        foreach ($size_arr_order as $key => $value) {
            $size_arr_new[$lp] = $value['name'];

            $lp++;
        }
        $size_arr = $size_arr_new;
    }

    $images = $wc_data['images'];
    $paproduct_id = $wc_data['paproduct_id'];
    $wc_variants = $all_variants;
    $data = array();
    $old_variants = array();
    $_edit_noexistant = 1;

    if ($woocommercenewproduct != 0 && $woocommercenewproduct != "" && $type == 1) {
        $data['id'] = $woocommercenewproduct;
        $old_product_variations = get_wooc_product_variations($woocommercenewproduct, $wc_auth);
        $old_variants = build_old_woo_variants($paproduct_id, $old_product_variations);
    }
    if ($type == 1 && (!isset($woocommercenewproduct) || $woocommercenewproduct == '' || $woocommercenewproduct = 0)) {
        $_edit_noexistant = 0;
    }

    if ($type == 2)
        $old_variants = build_old_woo_variants($paproduct_id, $old_product_variations);

    $old_images = array();
    if ($POST['pagetype'] == 2)
        $old_images = $wc_data['old_images'];

    $data['name'] = $title;
    $data['type'] = 'variable';
    $data['sku'] = $sku;
    $data['description'] = $updatedescription;
    $data['short_description'] = $woocommerceshortdesc;
    $data['visible'] = true;
    $data['status'] = 'publish';
    $data['catalog_visibility'] = 'visible';

    $shipping_class = '';
    if ($wc_data['woocommerceshippingid']) {
        $all_shipping = getWooShipping($wc_auth);
        $shipping_class = $all_shipping[$woocommerceshippingid];
        $data['shipping_class'] = $shipping_class;
    }


    foreach ($wc_data['woocommercecategory'] as $wc_categ)
        $data['categories'][] = array('id' => $wc_categ);

    if (empty($wc_data['woocommercecategory'])) {
        $data['product']['categories'][] = 0;
    }

    if ($tags != "")
        $data['tags'] = explode(',', str_replace('"', '\"', stripslashes($tags)));

    if ($weight != "") {
        $wc_weight = $weight * 0.45359237;
        $data['weight'] = "$wc_weight";
    }
    $default_color = str_replace(" ", "-", $color_arr[0]);
    $default_size = $size_arr[0];
    list($color_arr, $size_arr) = get_attribute_data($wc_auth, $color_arr, $size_arr);
    if ($wc_auth['user_id'] == 10294) {
        $color_arr = array_map('dave_color_filter', $color_arr);
        $wc_variants = array_map('dave_variant_filter', $wc_variants);

    }
    $data['attributes'][] = array('name' => 'Color', 'slug' => 'color', 'options' => explode("|", $color_arr), 'position' => 0, 'visible' => true, 'variation' => true);
    $data['attributes'][] = array('name' => 'Size', 'slug' => 'size', 'options' => explode("|", $size_arr), 'position' => 1, 'visible' => true, 'variation' => true);

    $data['default_attributes'][] = array('name' => 'Color', 'slug' => 'color', 'option' => $default_color);
    $data['default_attributes'][] = array('name' => 'Size', 'slug' => 'size', 'option' => $default_size);
    $wooc_variants = create_woo_variants($POST, $wc_auth, $wc_data, $wc_variants, $old_variants, $_edit_noexistant, $shipping_class);
    $data['images'] = create_woo_images_data($POST, $images, $old_images);
    $data['variations'] = $wooc_variants;

    //if($wc_auth['shop_id'] == 5592)
    //mail("team@ryankikta.com","wc_data",var_export($data,true));

    return $data;
}

function dave_variant_filter($variant)
{

    $variant['color_name'] = str_replace(' ', "_", $variant['color_name']);


    return $variant;
}

function dave_color_filter($color)
{

    $color = str_replace(' ', "_", $color);

    return $color;
}

function create_woo_images_data($POST, $images, $old_images = array())
{

    $all_images = array();
    if ($POST['pagetype'] == 2) {
        foreach ($images as $key => $image) {
            if (isset($old_images[$image['id']]) && $old_images[$image['id']] != 0)
                $all_images[] = array('id' => $old_images[$image['id']], 'src' => $image['src'], 'position' => $key);
            else
                $all_images[] = array('src' => $image['src'], 'position' => $key);
        }
    } else {
        foreach ($images as $key => $image)
            $all_images[] = array('src' => $image['src'], 'position' => $key);
    }
    return $all_images;
}

function get_wooc_images($productid, $images, $shopid)
{
    $all_images_data = array();
    foreach ($images as $image) {
        $woo_attachement_id = get_image_meta_shop($productid, $image['id'], 'woocommerce_id', $shopid);
        $all_images_data[$image['id']] = intval($woo_attachement_id);
    }
    return $all_images_data;
}

function create_woo_variants($POST, $wc_auth, $data, $variants, $old_variants = array(), $check_old_variant = 1, $shipping_class = '')
{

    @extract($data);
    $shop_id = $wc_auth['shop_id'];
    $variants_wc = array();

    foreach ($variants as $key => $variant) {
        $sku = ($variant['sku'] == "") ? stripcslashes($title) . '-' . $variant['color_name'] . '-' . $variant['size_name'] : $variant['sku'];
        $color_id = $variant['color_id'];
        $image_id = $variant['image_id'];
        $image_url = "";
        $has_edit_image = false;
        if (isset($POST['pa_product_id']) && $POST['pagetype'] == 2) {
            $products_id = $POST['pa_product_id'];
            $wooc_image_id = get_image_color_meta_shop($products_id, $color_id, $image_id, 'woocommerce_id', $shop_id);
            if ((int)$wooc_image_id > 0)
                $has_edit_image = true;
        }
        if ($POST['pagetype'] == 3) {
            $products_id = $POST['pa_product_id'];
            $wooc_image_id = get_image_color_meta_shop($products_id, $color_id, $image_id, 'woocommerce_id', $shop_id);
            if ($wooc_image_id) {
                $new_product_id = $POST['new_product_id'];
                update_image_color_meta_shop($new_product_id, $color_id, $image_id, 'woocommerce_id', $wooc_image_id, $shop_id);
                $has_edit_image = true;
            }
        }
        if (!$has_edit_image)
            $image_url = $variant['image_url'];

        $variant_wc = array(
            'sku' => $sku,
            'price' => $variant['price'],
            'regular_price' => $variant['price'],
            'sale_price' => $variant['price'],
            'in_stock' => true,
            'visible' => true,
            'shipping_class' => $shipping_class,
            'attributes' => array(
                array(
                    'name' => 'Color',
                    'option' => $variant['color_name'],
                ),
                array(
                    'name' => 'Size',
                    'option' => $variant['size_name'],
                )
            ),
        );
        if ($image_url != "")
            $variant_wc['image'] = array(
                'src' => $image_url
            );
        if ($has_edit_image)
            $variant_wc['image'] = array(
                'id' => $wooc_image_id
            );

        if (!empty($old_variants) && $check_old_variant) {

            if (isset($old_variants[$variant['color_id'] . '_' . $variant['size_id']]) && $old_variants[$variant['color_id'] . '_' . $variant['size_id']] != "") {
                $variant_wc_id = array('id' => $old_variants[$variant['color_id'] . '_' . $variant['size_id']]);
                $variant_wc = array_merge($variant_wc_id, $variant_wc);
            }

        }

        $variants_wc[] = $variant_wc;

    }

    //mail("team@ryankikta.com","variants_wc edit",var_export($variants_wc,true));

    return $variants_wc;
}

function addWoocommerceProductv2($POST, $wc_data, $data, $wc_auth, $products_id, $user_id, $shopid, $toremove = array(), $toUpdate = array())
{
    global $wpdb;
    @extract($wc_auth);
    $wc_images = array();

    $endpoint = "products/";
    $params = array("q" => "/wp-json/wc/v2/" . $endpoint);

    $wc_data_variants = $wc_data['variations'];
    $wc_images['images'] = $wc_data['images'];
    if (isset($wc_data['tags'])) {
        $prod_tags = $wc_data['tags'];
        unset($wc_data['tags']);
    }
    unset($wc_data['images']);
    unset($wc_data['variations']);

    if (isset($wc_data['product']['id']) && $wc_data['product']['id'] > 0) {
        $wc_data['id'] = $wc_data['product']['id'];
        unset($wc_data['product']['id']);
    }
    $is_create = true;
    if (isset($wc_data['id']) && $wc_data['id'] > 0)
        $is_create = false;
    $prod_bysku = get_woocommerceproduct_per_sku($wc_auth, $wc_data['sku']);

    if (!empty($prod_bysku)) {
        if ($is_create || (!$is_create && (int)$wc_data['id'] != $prod_bysku[0]->id)) {
            $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
            $count_shop = check_woocommerce_shop($user_id, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for shop "' . $wpdb->get_var("select shop from wp_users_woocommerce where id=$shopid") . '"';
            $error_title = 'Error ' . $text . ' product in woocommerce ' . $shop_text . ':';
            $sku = $wc_data['sku'];
            if (isset($prod_bysku[0]->id))
                $errors = array("The sku '$sku' already exist");
            else
                $errors = array($prod_bysku[0]);

            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
    }
    if (isset($wc_data['id']) && $wc_data['id'] > 0) {
        delete_old_variants($wc_auth, $wc_data['id']);
        $endpoint = "products/" . $wc_data['id'];
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);

        $woo_response = call_wooc_data($wc_auth, $endpoint, $params, "put", $wc_data);
    } else
        $woo_response = call_wooc_data($wc_auth, $endpoint, $params, "post", $wc_data);
    if ($shopid == 5621) {
        //mail("team@ryankikta.com","wooc data",var_export($wc_data,true));
        //mail("team@ryankikta.com","wooc response",var_export($woo_response,true));
    }

    if (isset($woo_response->id)) {
        $woocommerce_id = $woo_response->id;

        $endpoint = "products/" . $woocommerce_id;
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        if (!$is_create) {
            $prod_wc = getWooproduct($woocommerce_id, $wc_auth);
            $prod_images = array();
            $arr = array();
            foreach ($prod_wc->images as $img)
                $prod_images[] = $img->id;
            $arr["prod_images"] = $prod_images;
            $arr['wc_images_before'] = $wc_images;
            foreach ($wc_images['images'] as $key => $img) {
                if (isset($img['id']) && in_array($img['id'], $prod_images))
                    unset($wc_images['images'][$key]['src']);
                else
                    unset($wc_images['images'][$key]['id']);
            }
        }
        $arr['wc_images_after'] = $wc_images;
        $arr['prod_wooc_img'] = $prod_wc->images;
        //if($shopid == 4154)
        //mail("team@ryankikta.com","wooc images call",var_export($arr,true));
        $images_response = call_wooc_data($wc_auth, $endpoint, $params, "put", $wc_images);
        udapte_woocommerce_images($images_response->images, $products_id, $data['images'], $shopid);

        $variants_to_create = array();
        $variants_to_update = array();

        foreach ($wc_data_variants as $var) {
            if (isset($var['id']))
                $variants_to_update[] = $var;
            else
                $variants_to_create[] = $var;
        }

        // Variants To Create
        if (count($variants_to_create) > 100)
            multiple_save_woocommerce_variants($wc_auth, $woocommerce_id, $variants_to_create);
        else {
            $endpoint = "products/" . $woocommerce_id . "/variations/batch";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            $all_variants_wc["create"] = $variants_to_create;
            $call_variants = call_wooc_data($wc_auth, $endpoint, $params, "post", $all_variants_wc);
        }

        // Variants To Update
        if (count($variants_to_update) > 100)
            multiple_save_woocommerce_variants($wc_auth, $woocommerce_id, $variants_to_update, "update");
        else {
            $endpoint = "products/" . $woocommerce_id . "/variations/batch";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            $all_variants_wc["update"] = $variants_to_update;
            $call_variants = call_wooc_data($wc_auth, $endpoint, $params, "post", $all_variants_wc);
        }

        // Variants To Remove
        if (!empty($toremove)) {
            $wc_variants_to_delete = array();
            $pa_variants_ids = implode(",", array_filter_key($toremove, 'id'));
            $variants_ids = get_multiple_variants_meta_shop($pa_variants_ids, "woocommerce_id", $shopid);
            foreach ($variants_ids as $wc_var)
                $wc_variants_to_delete[] = $wc_var->meta_value;

            $endpoint = "products/" . $woocommerce_id . "/variations/batch";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            $toremove_variants["delete"] = $wc_variants_to_delete;
            call_wooc_data($wc_auth, $endpoint, $params, "post", $toremove_variants);
        }

        if (isset($prod_tags) & !empty($prod_tags)) {
            $arr = array();
            $all_tags = get_all_products_tags($wc_auth);
            foreach ($all_tags as $tag_id => $tag)
                $all_tags[$tag_id] = str_replace("&amp;", "&", $tag);
            $all_tags_flip = array_flip($all_tags);
            $all_tags_lower = array_map('strtolower', $all_tags);
            $all_tags_flip_lower = array_flip($all_tags_lower);
            $arr["all_tags"] = $all_tags;
            $arr["all_tags_flip"] = $all_tags_flip;
            $arr["all_tags_lower"] = $all_tags_lower;
            $arr["all_tags_flip_lower"] = $all_tags_flip_lower;
            foreach ($prod_tags as $key => $tag) {
                $tag = trim($tag, " ");
                $arr['tag'][$key] = $tag;
                if (in_array($tag, $all_tags)) {
                    $arr['tag_flip'][$key] = $all_tags_flip[$tag];
                    $wc_tags['tags'][] = array('id' => $all_tags_flip[$tag]);
                } elseif (in_array(strtolower($tag), $all_tags_lower)) {
                    $arr['tag_flip_lower'][$key] = $all_tags_flip_lower[strtolower($tag)];
                    $wc_tags['tags'][] = array('id' => $all_tags_flip_lower[strtolower($tag)]);
                } else {
                    $tag_id = create_product_tag($wc_auth, array('name' => $tag));
                    $arr['create_tag'][$key] = $tag_id;
                    if ($tag_id > 0 && $tag_id != NULL && !is_array($tag_id))
                        $wc_tags['tags'][] = array('id' => $tag_id);
                }
            }
            //if($shopid == 4401)
            //mail("team@ryankikta.com","wooc tags",var_export($arr,true));
            $endpoint = "products/" . $woocommerce_id;
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            $tags_res = call_wooc_data($wc_auth, $endpoint, $params, "put", $wc_tags);
        }

        if ($POST['pagetype'] == 1)
            $_SESSION['shops']['woocommerce_ids'][$shopid] = $woocommerce_id;
        $woocommerceshortdescdb = base64_encode($data['woocommerceshortdesc']);

        $wc_cat_id = implode(",", $data['woocommercecategory']);
        if ($wc_cat_id == NULL) {
            $wc_cat_id = 0;
        }

        $wc_shipp_id = $data['woocommerceshippingid'];
        $wpdb->get_result("UPDATE `wp_users_products` SET `woocommerceactive` = 1,`updated`='" . date("Y-m-d H:i:s", time()) . "' WHERE `id` = $products_id");

        $pa_product_id_old = get_product_id_meta_shop($woocommerce_id, "woocommerce_id", $shopid);
        if ($pa_product_id_old && $pa_product_id_old != $products_id) {
            delete_variants_product_meta_shop($pa_product_id_old, 'woocommerce_id', $shopid);
            delete_images_product_meta_shop($pa_product_id_old, 'woocommerce_id', $shopid);
        }
        $all_meta = array('woocommerce_id' => 'NULL', 'woocommerce_cat_id' => 'NULL', 'woocommerce_shipping_id' => 'NULL', 'woocommerce_shortdesc' => 'NULL');
        $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $products_id and shopid = $shopid", ARRAY_A);
        foreach ($results as $res)
            $all_meta[$res['meta_key']] = $res['meta_id'];

        $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $products_id) ? $pa_product_id_old : 0;
        if ($prod_to_deconnect) {
            update_product_meta_shop($products_id, 'woocommerce_id', $woocommerce_id, $shopid, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, 'woocommerce_cat_id', $wc_cat_id, $shopid, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, 'woocommerce_shipping_id', $wc_shipp_id, $shopid, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, 'woocommerce_shortdesc', stripcslashes($woocommerceshortdescdb), $shopid, 0, $prod_to_deconnect);
        } else {
            $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['woocommerce_id']},$products_id,'woocommerce_id','$woocommerce_id',$shopid),"
                . " ({$all_meta['woocommerce_cat_id']},$products_id,'woocommerce_cat_id','" . $wc_cat_id . "',$shopid),"
                . " ({$all_meta['woocommerce_shipping_id']},$products_id,'woocommerce_shipping_id','" . $wc_shipp_id . "',$shopid),"
                . " ({$all_meta['woocommerce_shortdesc']},$products_id,'woocommerce_shortdesc','" . stripcslashes($woocommerceshortdescdb) . "',$shopid)  "

                . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";
            $wpdb->query($sql);
        }

        $wooc_variations = get_wooc_product_variations($woocommerce_id, $wc_auth);
        udapte_woocommerce_variants($wooc_variations, $products_id, $shopid);

        $wooc_variations = get_wooc_product_variations($woocommerce_id, $wc_auth);
        udapte_pa_wooc_variants_images($wooc_variations, $products_id, $shopid);
    } else {
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_woocommerce_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_woocommerce where id=$shopid") . '"';
        $error_title = 'Error ' . $text . ' product in woocommerce ' . $shop_text;
        $errors = process_woo_errors($woo_response->errors);
        //mail("team@ryankikta.com","wooc errors",var_export($woo_response,true));
        if (!empty($errors))
            $error_title .= ':';

        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function multiple_save_woocommerce_variants($wc_auth, $woocommerce_id, $wc_data_variants, $type = "create")
{

    $variants = array_chunk($wc_data_variants, 100);
    foreach ($variants as $variants_part) {
        $all_variants_wc = array();
        $endpoint = "products/" . $woocommerce_id . "/variations/batch";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        if ($type == "create")
            $all_variants_wc["create"] = $variants_part;
        if ($type == "update")
            $all_variants_wc["update"] = $variants_part;
        call_wooc_data($wc_auth, $endpoint, $params, "post", $all_variants_wc);
    }
}

function call_wooc_data($auth, $url, $params, $method, $data = NULL)
{

    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        if ($method == "post")
            $return = $wc_api->post($url, $data, $params);
        elseif ($method == "put")
            $return = $wc_api->put($url, $data, $params);
        elseif ($method == "delete") {
            $return = $wc_api->delete($url, $params);
            $return = call_woocommerce_delete($auth, $url, 0);
        } else
            $return = $wc_api->get($url, $params);
        return $return;
    } catch (HttpClientException $e) {
        $arr = array("message" => $e->getMessage(), "method" => $method, "params" => $params, "auth" => $auth, "response" => $e->getResponse());
        if ($data != NULL && !empty($data))
            $arr['data'] = $data;
        send_mail($url, $arr);
        return $arr;
    }

}

function udapte_woocommerce_variants_images($wc_auth, $woocommerce_id, $wooc_variants, $wc_data_variants, $products_id, $shopid, $user_id)
{
    global $wpdb;
    @extract($wc_auth);
    $images_ids = array();
    $data_variants = array();
    $custom_colors = get_custom_colors();
    $variants_ids = array();
    $wc_data_variants_edit = array();

    foreach ($wooc_variants as $var) {
        $key_attr = "";
        $image = $var->image;
        $wooc_image_id = $image->id;
        foreach ($var->attributes as $attribute) {
            if (strpos(strtolower($attribute->name), 'color') !== false) {
                $key_attr .= $attribute->option . "-";
                if (in_array($attribute->option, array_keys($custom_colors)))
                    $color_name = $custom_colors[$attribute->option];
                else $color_name = $attribute->option;

                $color_id = get_color_id($color_name);
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
                $image_id = $wpdb->get_var("SELECT image_id FROM `wp_users_products_colors` where `users_products_id` = $products_id and `color_id`=$color_id");
                if ($wooc_image_id > 0 && !isset($images_ids[$color_id]) && $image_id > 0)
                    $images_ids[$color_id] = $wooc_image_id;
            }
            if (strpos(strtolower($attribute->name), 'size') !== false)
                $key_attr .= $attribute->option . "-";
        }
        $key_attr = rtrim($key_attr, "-");
        $variants_ids[$key_attr] = $var->id;
    }

    foreach ($wc_data_variants as $key => $var) {
        $key_attr = "";
        foreach ($var['attributes'] as $attribute) {
            if (strpos(strtolower($attribute['name']), 'color') !== false) {
                $key_attr .= $attribute['option'] . "-";
                if (in_array($attribute['option'], array_keys($custom_colors)))
                    $color_name = $custom_colors[$attribute['option']];
                else $color_name = $attribute['option'];

                $color_id = get_color_id($color_name);
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute['option'])));
                    $color_id = get_color_id($color_name);
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute['option'])));
                    $color_id = get_color_id($color_name);
                }
            }
            if (strpos(strtolower($attribute['name']), 'size') !== false)
                $key_attr .= $attribute['option'] . "-";
        }
        $key_attr = rtrim($key_attr, "-");

        if (isset($images_ids[$color_id]))
            $wc_data_variants_edit[] = array("id" => $variants_ids[$key_attr], "image" => array('id' => $images_ids[$color_id]));

    }

    if (count($wc_data_variants_edit) > 100) {
        multiple_save_woocommerce_variants($wc_auth, $woocommerce_id, $wc_data_variants_edit, "update");
    } else {
        $endpoint = "products/" . $woocommerce_id . "/variations/batch";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $all_variants_wc["update"] = $wc_data_variants_edit;
        call_wooc_data($wc_auth, $endpoint, $params, "post", $all_variants_wc);
    }

}

function udapte_pa_wooc_variants_images($wooc_variations, $products_id, $shop_id)
{
    global $wpdb;
    $images_to_remove = array();
    $custom_colors = get_custom_colors();
    $wooc_img_colors = array();
    foreach ($wooc_variations as $var) {
        $image = $var->image;
        $wooc_image_id = $image->id;
        foreach ($var->attributes as $attribute) {
            if (strpos(strtolower($attribute->name), 'color') !== false) {
                if (in_array($attribute->option, array_keys($custom_colors)))
                    $color_name = $custom_colors[$attribute->option];
                else $color_name = $attribute->option;

                $color_id = get_color_id($color_name);
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
            }
        }
        $wooc_img_colors[$color_id][] = $wooc_image_id;
    }

    foreach ($wooc_img_colors as $color_id => $wooc_image_id)
        delete_image_color_by_shop($products_id, $color_id, 'woocommerce_id', $shop_id);

    foreach ($wooc_img_colors as $color_id => $wooc_image) {
        $image_id = $wpdb->get_var("SELECT image_id FROM `wp_users_products_colors` where `users_products_id` = $products_id and `color_id`=$color_id");
        foreach ($wooc_image as $wooc_image_id) {
            if ($image_id > 0 && $wooc_image_id > 0)
                update_image_color_meta_shop($products_id, $color_id, $image_id, 'woocommerce_id', $wooc_image_id, $shop_id);
        }
    }
}

function deleteWoocommerceProductV2($woocommerce_id, $wc_auth, $productid, $delete_meta = 0)
{
    @extract($wc_auth);
    $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
        ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
    );
    $endpoint = "products/" . $woocommerce_id;
    $delete = call_woocommerce_delete($wc_auth, $endpoint, $woocommerce_id);

    if ($pa_version == null)
        delete_product_meta_wooc($wc_auth, $productid, $shop_id);

    if ($delete_meta == 1)
        remove_product_meta_shop($woocommerce_id, $productid, $shop_id);

    return $delete;
}

function remove_product_meta_shop($woocommerce_id, $productid, $shop_id)
{
    $prodid = get_product_id_meta_shop($woocommerce_id, "woocommerce_id", $shop_id);
    if (!$prodid)
        $shop_id = 0;
    delete_product_meta_multi_shop($productid, "'woocommerce_id','woocommerce_cat_id','woocommerce_shipping_id','woocommerce_shortdesc'", $shop_id);
    delete_variants_product_meta_shop($productid, "woocommerce_id", $shop_id);
    delete_images_product_meta_shop($productid, 'woocommerce_id', $shop_id);
}

function delete_product_meta_wooc($wc_auth, $productid, $shop_id)
{
    global $wpdb;
    $variants = get_variants_product_meta_shop($productid, "woocommerce_id", $shop_id);
    if ($variants) {
        $all_variants = array();
        foreach ($variants as $wooc)
            $all_variants[] = $wooc['meta_value'];
    } else
        $all_variants = $wpdb->get_col("select woocommerce_id from wp_users_products_colors where users_products_id=$productid");

    foreach ($all_variants as $wooc_id) {
        $wooc_id = (int)$wooc_id;
        $endpoint = "products/" . $wooc_id;
        call_woocommerce_delete($wc_auth, $endpoint, $wooc_id);
    }
}

function call_woocommerce_delete($wc_auth, $endpoint, $recource_id, $force = true)
{
    @extract($wc_auth);
    $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
        ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
    );
    try {
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        if ($force)
            $params['force'] = true;
        return $wc_api->delete($endpoint, $params);
    } catch (HttpClientException $e) {
        try {
            $endpoint = $endpoint . "?_method=DELETE";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            if ($force)
                $params['force'] = true;
            return $wc_api->post($endpoint, $params);
        } catch (HttpClientException $e) {
            //$arr = array("wc_auth"=>$wc_auth,"message"=>$e->getMessage(),"response"=>$e->getResponse());
            //send_mail("deleteWoocommerceProductV2",var_export($arr,true));
            if ($recource_id != 0)
                return generate_errors_wooc($e, $recource_id);
            return generate_errors_wooc($e);
        }
    }
}

function udapte_woocommerce_images($images, $product_id, $pa_image, $shopid)
{
    foreach ($images as $key => $image) {
        $pa_image_id = $pa_image[$key]['id'];
        update_image_meta_shop($product_id, $pa_image_id, "woocommerce_id", $image->id, $shopid);
    }
}

function udapte_woocommerce_variants($variations, $product_id, $shopid)
{
    global $wpdb;

    $all_meta = array();
    $all_vars = array();
    $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $shopid and meta_key='woocommerce_id' and product_id=$product_id", ARRAY_A);
    foreach ($results as $res)
        $all_meta[$res['variant_id']] = $res['id'];

    $custom_sizes = get_custom_sizes();
    $custom_colors = get_custom_colors();
    foreach ($variations as $variation) {
        foreach ($variation->attributes as $attribute) {
            if (strpos(strtolower($attribute->name), 'color') !== false) {
                if (in_array($attribute->option, array_keys($custom_colors)))
                    $color_name = $custom_colors[$attribute->option];
                else $color_name = $attribute->option;

                $color_id = get_color_id($color_name);
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
            }
            if (strpos(strtolower($attribute->name), 'size') !== false) {
                if (in_array($attribute->option, array_keys($custom_sizes)))
                    $size_name = $custom_sizes[$attribute->option];
                else $size_name = $attribute->option;

                $size_id = get_size_id($size_name);
                if (!$size_id) {
                    $size_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $size_id = get_size_id($size_name);
                }
                if (!$size_id) {
                    $size_name = ucwords(implode(" ", explode('_', $attribute->option)));
                    $size_id = get_size_id($size_name);
                }
            }
        }
        $colors_id = implode(",", get_colors_col($color_name));
        $sizes_id = implode(",", get_sizes_col($size_name));
        $sql = "select id from `wp_users_products_colors` where users_products_id=$product_id and color_id in($colors_id) and size_id in($sizes_id)";
        $cs_products = $wpdb->get_results($sql, ARRAY_A);
        $variantid = $cs_products[0]['id'];
        $_tmp = array('variant_id' => $variantid, 'woocommerce_id' => $variation->id);
        $_tmp['id'] = (isset($all_meta[$variantid])) ? $all_meta[$variantid] : 'NULL';
        $all_vars[] = $_tmp;
    }

    $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
    $_tmp = array();
    foreach ($all_vars as $_var) {
        $_tmp[] = " ({$_var['id']},'$product_id','{$_var['variant_id']}','woocommerce_id','{$_var['woocommerce_id']}','$shopid') ";
    }
    $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
    $wpdb->query($sql_var);
}

function get_wooc_product_variations($woocommerce_id, $auth)
{
    $page = 1;
    $all_variants = array();
    $variants = get_wooc_product_variations_per_page($woocommerce_id, $auth);
    $all_variants = array_merge($all_variants, $variants);

    while (!empty($variants)) {
        $page = $page + 1;
        $variants = get_wooc_product_variations_per_page($woocommerce_id, $auth, $page);
        if (!empty($variants))
            $all_variants = array_merge($all_variants, $variants);
    }

    return $all_variants;

}

function get_wooc_product_variations_per_page($woocommerce_id, $auth, $page = 1)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/" . $woocommerce_id . "/variations";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, "per_page" => 100, "page" => $page);
        return $wc_api->get($endpoint, $params);
    } catch (HttpClientException $e) {
        send_mail("get_wooc_product_variations_per_page", array("wc_auth" => $auth, "page" => $page, "message" => $e->getMessage(), "response" => $e->getResponse()));
        $return = generate_errors_wooc($e, $woocommerce_id);
        return $return;
    }
}

function build_old_woo_variants($paproduct_id, $old_variants)
{

    global $wpdb;
    $existant_variants = array();

    $custom_sizes = get_custom_sizes();
    $custom_colors = get_custom_colors();
    foreach ($old_variants as $variation) {
        $color_id = "";
        $size_id = "";
        foreach ($variation->attributes as $attribute) {
            if (strpos(strtolower($attribute->name), 'color') !== false) {
                if (in_array($attribute->option, array_keys($custom_colors))) {
                    $color_name = $custom_colors[$attribute->option];
                } else {
                    $color_name = $attribute->option;
                }
                $color_id = get_color_id($color_name);
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $color_id = get_color_id($color_name);
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute->option)));
                }
            }
            if (strpos(strtolower($attribute->name), 'size') !== false) {
                if (in_array($attribute->option, array_keys($custom_sizes)))
                    $size_name = $custom_sizes[$attribute->option];
                else $size_name = $attribute->option;
                $size_id = get_size_id($size_name);
                if (!$size_id) {
                    $size_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $size_id = get_size_id($size_name);
                }
                if (!$size_id)
                    $size_name = ucwords(implode(" ", explode('_', $attribute->option)));
            }
        }
        $colors_id = implode(",", get_colors_col($color_name));
        $sizes_id = implode(",", get_sizes_col($size_name));


        $sql = "select color_id,size_id from `wp_users_products_colors` where users_products_id=$paproduct_id and color_id in($colors_id) and size_id in($sizes_id)";
        $cs_products = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($cs_products)) {
            $color_id = $cs_products[0]['color_id'];
            $size_id = $cs_products[0]['size_id'];
            $existant_variants[$color_id . '_' . $size_id] = $variation->id;
        }
    }
    return $existant_variants;
}

function get_custom_sizes()
{

    return get_option("custom_sizes_ryankikta");

}

function get_custom_colors()
{

    return get_option("custom_colors_ryankikta");

}

function delete_old_variants($auth, $woocommerce_id)
{
    $product = getWooproduct($woocommerce_id, $auth);
    $variants = get_wooc_product_variations($woocommerce_id, $auth);
    $product_sku = $product->sku;
    foreach ($variants as $var) {
        $variant_id = $var->id;
        $variant_sku = $var->sku;
        if ($product_sku == $variant_sku)
            deleteWoocommerceProductV2($variant_id, $auth);
    }
}

function getWooproduct($woocommerce_id, $auth = array())
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/$woocommerce_id";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->get($endpoint, $params);
    } catch (HttpClientException $e) {
        //send_mail("getWooproduct",array("wc_auth"=>$auth,"endpoint"=>$endpoint,"message"=>$e->getMessage(),"response"=>$e->getResponse()));
        return generate_errors_wooc($e, $woocommerce_id);
    }

}

function getProductsList($auth, $params = array())
{

    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $all_products = array();
        $endpoint = "products";
        $params = (!empty($params)) ? $params : array('per_page' => 100);
        $params["q"] = "/wp-json/wc/v2/" . $endpoint;
        $products = $wc_api->get($endpoint, $params);
        if (!empty($products)) {
            foreach ($products as $product)
                $all_products[$product->id] = $product->name;
        }

        return $all_products;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function get_all_woocommerce_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $wc_products = array();
    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $user_shopids = $wpdb->get_results("select id from wp_users_woocommerce where users_id=$user_id $where order by id asc", ARRAY_A);
    foreach ($user_shopids as $shop) {
        $wc_auth = getWoocommerceShopbyId($shop['id']);
        $page_products = getWoocommerce_sku($wc_auth);
        $page = 2;
        while (!empty($page_products)) {
            $wc_products = array_merge($wc_products, $page_products);
            $page_products = getWoocommerce_sku($wc_auth, $page);
            $page += 1;
        }
    }
    return $wc_products;
}

function get_all_woocommerce_shops($user_id)
{
    global $wpdb;
    return $wpdb->get_results("select `id`,`shop`,`active` from `wp_users_woocommerce` where `users_id` = $user_id");
}

function get_woocommerceproduct_per_sku($auth, $sku)
{
    try {
        @extract($auth);

        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products";
        $params = array('q' => "/wp-json/wc/v2/" . $endpoint, 'sku' => $sku);
        $products = $wc_api->get($endpoint, $params);
        return $products;
    } catch (HttpClientException $e) {
        send_mail("get_woocommerceproduct_per_sku", array("wc_auth" => $auth, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return generate_errors_wooc($e);
    }
}

function getWoocommerce_sku($auth, $page = 1)
{
    global $wpdb;
    try {
        @extract($auth);

        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products";
        $params = array('q' => "/wp-json/wc/v2/" . $endpoint, 'per_page' => '100', 'page' => $page);
        $products = $wc_api->get($endpoint, $params);

        if (!empty($products)) {
            $all_products = array();
            foreach ($products as $product) {
                $pa_product_id = get_product_meta_shop_byfield("product_id", "woocommerce_id", $product->id, $shop_id);
                $images = $product->images;
                $featured_src = $images[0]->src;
                $all_products[] = array(
                    "id" => $product->id,
                    "title" => $product->name,
                    "status" => $product->status,
                    "url" => $product->permalink,
                    "image" => $featured_src,
                    "shop_id" => $shop_id,
                    "imported" => ($pa_product_id == NULL) ? 0 : 1,
                    "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
                );
            }
            return $all_products;
        }
        return array();
    } catch (HttpClientException $e) {
        return generate_errors_wooc($e);
    }
}

function get_all_products_wooc($auth, $params = array())
{
    try {
        $all_products = array();
        @extract($auth);

        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products";
        $params = (!empty($params)) ? $params : array();
        $params["q"] = "/wp-json/wc/v2/" . $endpoint;
        $products = $wc_api->get($endpoint, $params);
        if (!empty($products)) {
            foreach ($products as $key => $product) {
                $all_products[$key]['title'] = $product->name;
                $all_products[$key]['id'] = $product->id;
                $all_products[$key]['sku'] = $product->sku;
                $variants = $product->variations;
                $variants = get_wooc_product_variations($product->id, $auth);
                foreach ($variants as $ind => $var) {
                    $all_products[$key]['variants'][$ind]['id'] = $var->id;
                    $all_products[$key]['variants'][$ind]['sku'] = $var->sku;
                }
            }
        }
        return $all_products;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function getWooCategories($auth = array())
{
    try {
        $categories = array();
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/categories";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $woo_categories = $wc_api->get($endpoint, $params);

        foreach ($woo_categories as $category) {
            if ($category->name != "Uncategorized")
                $categories[$category->id] = $category->name;
        }
        return $categories;
    } catch (HttpClientException $e) {
        return generate_errors_wooc($e);
    }
}

function wc_display_tree_categs($categories, $selected_ids = array(), $shop_id, $parent = 0, $limit = 0)
{
    if ($limit > 2000) {
        return '';
    };
    $shop = ($shop_id != 0) ? $shop_id : "";
    $tree = '';
    $name = "woocommercecategory" . $shop . "[]";
    if (!empty($categories)) {
        $tree .= '<ul>';
        foreach ($categories as $category) {
           // if ($category->parent == $parent) {
                //$checked = (in_array($category->id, $selected_ids)) ? "checked='checked'" : "";
                $tree .= '<li>'
                    . '<input type="checkbox" id="node' . $shop . '-' . $category->id . '" checked="checked" />'
                    . '<label><input type="checkbox" name="' . $name . '" value="' . $category->id . '" ' . $checked . ' /><span></span></label>'
                    . '<label for="node' . $shop . '-' . $category->id . '">' . $category->name . '</label>';
                //$tree .= wc_display_tree_categs($categories, $selected_ids, $shop_id, $category->id, $limit++);
                $tree .= '</li>';
            //}
        }
        $tree .= '</ul>';
    }
    return $tree;
}

function wc_categories($auth, $checked_ids = array(), $shop_id = 0)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/categories";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $woo_categories = $wc_api->get($endpoint, $params);

        foreach ($woo_categories as $key => $cat) {
            if (in_array($cat->slug, array("uncategorized", "non-classe")))
                unset($woo_categories[$key]);
        }
	//wp_mail('jbuck@ryankikta.com', 'woo_cat', var_export($woo_categories, true));
        return wc_display_tree_categs($woo_categories, $checked_ids, $shop_id);
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }

}

function getWooShipping($auth)
{
    try {
        $shipping = array();

        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/shipping_classes";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, "per_page" => 100);
        $woo_shipping = $wc_api->get($endpoint, $params);
        foreach ($woo_shipping as $shipping_class)
            $shipping[$shipping_class->id] = $shipping_class->name;
        return $shipping;
    } catch (HttpClientException $e) {
        send_mail("getWooShipping", array("wc_auth" => $auth, "message" => $e->getMessage(), "response" => $e->getResponse()));
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function getWooShipping_zones($auth)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "shipping/zones";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->get($endpoint, $params);

    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;

    }
}

function createWooShipping_zones($auth, $data)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "shipping/zones";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->post($endpoint, $data, $params);

    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;

    }
}

function updateWooShipping_zone_location($auth, $zone_id, $data)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "shipping/zones/" . $zone_id . "/locations";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->put($endpoint, $data, $params);

    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;

    }
}

function updateWooShipping_zone_methods($auth, $zone_id, $data)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "shipping/zones/" . $zone_id . "/methods";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->post($endpoint, $data, $params);

    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;

    }
}

function create_shipping_zones_woocommerce($auth)
{

    @extract($auth);

    $shipping = getWooShipping_zones($auth);

    if (count($shipping) == 1 && $shipping[0]->id == 0) {
        $zones = array(
            array(
                'zone' => array('name' => 'United State'),
                'zone_locations' => array(
                    array('code' => 'US')
                )),
            array(
                'zone' => array('name' => 'Canada'),
                'zone_locations' => array(
                    array('code' => 'CA')
                ))
        );
        foreach ($zones as $ship_zone) {

            $zone = createWooShipping_zones($auth, $ship_zone['zone']);

            updateWooShipping_zone_location($auth, $zone->id, $ship_zone['zone_locations']);
            $data_methods = array(
                'method_id' => 'flat_rate'
            );
            updateWooShipping_zone_methods($auth, $zone->id, $data_methods);
        }
    }

}

function check_product_existe_woocommerce($woocommerce_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($woocommerce_id, "woocommerce_id", $shop_id);
    if (!$pa_product_id)
        $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=" . $woocommerce_id);
    if ($pa_product_id)
        $existe = true;
    return array("status" => 200, "data" => $existe);
}

/************************************************Woocommerce Order Functions******************************************/

function get_order_woocommerce_data($order)
{

    $order_id = $order['id'];
    $email = ($order['billing_address']) ? $order['billing_address']['email'] : $order['billing']['email'];
    $customerphone = ($order['billing_address']) ? $order['billing_address']['phone'] : $order['billing']['phone'];
    $order_status = $order['status'];
    $shipping_addresses = ($order['shipping_address']) ? $order['shipping_address'] : $order['shipping'];
    $billing_address = ($order['billing_address']) ? $order['billing_address'] : $order['billing'];


    return array(
        'order_status' => $order_status,
        'shop_order_id' => $order_id,
        'shop_order_name' => $order_id,
        'email' => $email,
        'customerphone' => $customerphone,
        'shipping_addresses' => $shipping_addresses,
        'itemsinfo' => $order['line_items'],
        'billing_address' => $billing_address,
    );
}

function woocommerce_shipping_address($order)
{

    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = $order['first_name'] . " " . $order['last_name'];
    $shippingaddress1['address1'] = $order['address_1'];
    $shippingaddress1['address2'] = $order['address_2'];
    $shippingaddress1['city'] = $order['city'];
    $shippingaddress1['state'] = $order['state'];
    $shippingaddress1['zipcode'] = $order['postcode'];
    $shippingaddress1['country'] = $order['country'];

    $address2 = ($shippingaddress1['address2'] != "") ? $shippingaddress1['address2'] : "";
    $countries = get_all_countries();
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . "\n" . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $countries[$order['country']];
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], 'address1' => $shippingaddress1['address1'], 'address2' => $address2, "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);
    $state = $order['state'];
    $state_code = $state;
    $shippingaddress_zip = $order['postcode'];

    if ($order['country'] == "US") {
        $shipping_id = 1;
    } elseif ($order['country'] == "CA") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }

    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $order['country'], 'shippingaddress_state' => $state, 'shippingaddress_state_code' => $state_code, 'shippingaddress_zip' => $shippingaddress_zip, 'shipping_id' => $shipping_id, 'paypal_address' => $paypal_address);
}

function fix_empty_shipping_address($shippingaddress, $billing_address)
{
    if ($shippingaddress['first_name'] == "" && $shippingaddress['last_name'] == "" && $shippingaddress['address_1'] == "" && $shippingaddress['city'] == "" && $shippingaddress['state'] == "" && $shippingaddress['postcode'] == "" && $shippingaddress['country'] == "") {
        return woocommerce_shipping_address($billing_address);
    } else return array();
}

function get_all_item_woocommerce($itemsinfo, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();
    $arr = array();
    //mail("team@ryankikta.com","wooc itemsinfo",var_export($itemsinfo,true));
    if (empty($itemsinfo))
        return $items;
    foreach ($itemsinfo as $value) {

        $product = array();
        $res = array();
        $colors_id = array();
        $sizes_id = array();
        $item_id = $value['id'];
        $variant_id = $value['product_id'];
        $variant_sku = $wpdb->escape($value['sku']);
        $quantity = $value['quantity'];
        $item_price = $value['subtotal'] / $quantity;

        $arr[$item_id]['variant_id'] = $variant_id;
        $arr[$item_id]['variant_sku'] = $variant_sku;
        foreach ($value['meta'] as $val) {
            if (strtolower($val['key']) == 'color' || strtolower($val['key']) == 'pa_color') {
                $color_name = $val['value'];
                if ($color_name != "")
                    $colors_id = get_colors_col($color_name);
            }
            if (strtolower($val['key']) == 'size' || strtolower($val['key']) == 'pa_size') {
                $size_name = $val['value'];
                if ($size_name != "")
                    $sizes_id = get_sizes_col($size_name);
            }
        }
        //debug($color_name);
        //debug($size_name);
        if ($user_id == 10294) $color_name = str_replace(array("_", "-"), " ", $color_name);
        // bug due to the system can have more than a color and size with the same name

        if (!in_array($variant_sku, array("", NULL)) && !in_array($variant_id, array("", 0, NULL))) {
            $pa_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where meta_value = $variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
            //mail('team@ryankikta.com','sql woo',"select variant_id from wp_variants_meta where meta_value = $variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
            if (!$pa_variant_id)
                $pa_variant_id = $wpdb->get_var("select id from wp_users_products_colors where woocommerce_id=$variant_id and sku ='$variant_sku'");
            if (!$pa_variant_id)
                $pa_variant_id = $wpdb->get_var("select id from wp_users_products_colors where sku ='$variant_sku'");
            //debug("select id from wp_users_products_colors where woocommerce_id=$variant_id and sku ='$variant_sku'");
            //debug($pa_variant_id);
            if ($pa_variant_id) {
                $variants_data = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where id=$pa_variant_id", ARRAY_A);
                //debug($variants_data);
                $pa_product_id = $variants_data[0]['users_products_id'];
                $res = $wpdb->get_results("select id,users_id,brand_id,product_id,front,back from `wp_users_products` where id = $pa_product_id", ARRAY_A);
                //debug($res);
                if ($res) {
                    $product = end($res);
                    $product['color_id'] = $variants_data[0]['color_id'];
                    $product['size_id'] = $variants_data[0]['size_id'];
                    //mail("team@ryankikta.com","item prod 1",var_export($product,true));
                }
            } else {
                // this is the case when the woocommerce hook sends the product id instead the variant id, $res will return all the variants if the sku is wrong so we need to fix that
                $pa_product_id = get_product_id_meta_shop($variant_id, "woocommerce_id", $shop_id);
                if (!$pa_product_id)
                    $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=$variant_id");
                //debug( $pa_product_id);
                if ($pa_product_id) {
                    $res = $wpdb->get_results("select id,users_id,brand_id,product_id,front,back from `wp_users_products` where users_id = $user_id and id=$pa_product_id", ARRAY_A);
                    if ($res) {
                        $res = end($res);
                        $res['color_id'] = $wpdb->get_var("select color_id from wp_users_products_colors where users_products_id=$pa_product_id and sku='$variant_sku'");
                        $res['size_id'] = $wpdb->get_var("select size_id from wp_users_products_colors where users_products_id=$pa_product_id and sku='$variant_sku'");
                        foreach ($colors_id as $clr_id) {
                            foreach ($sizes_id as $sz_id) {
                                if ($clr_id == $res['color_id'] && $sz_id == $res['size_id']) {
                                    $product = $res;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
            if ($product) {
                $pa_product_id = $product['id'];
                $inventory_id = $product['product_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                $color_id = $product['color_id'];
                $size_id = $product['size_id'];
                if ($color_id == null || $size_id == null) {
                    $colors_id = implode(",", $colors_id);
                    $sizes_id = implode(",", $sizes_id);
                    $cs_products = $wpdb->get_results("select color_id,size_id from `wp_users_products_colors` where users_products_id=$pa_product_id and color_id in($colors_id) and size_id in($sizes_id)", ARRAY_A);
                    $color_id = $cs_products[0]['color_id'];
                    $size_id = $cs_products[0]['size_id'];
                }
                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0)
                    $items [] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
            } else {
                $pa_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where meta_value=$variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
                if ($pa_variant_id) {
                    $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where id=$pa_variant_id");
                    if (!empty($pa_variants)) {
                        foreach ($pa_variants as $var) {
                            $pa_product_id = $var->users_products_id;
                            $color_id = $var->color_id;
                            $size_id = $var->size_id;
                            $products_fb = $wpdb->get_results("select type,image_id from wp_users_products_images where type<>4 and users_products_id=$pa_product_id order by type asc");
                            $image_id = $products_fb[0]->image_id;
                            $image_user_id = $wpdb->get_var("select userID from wp_userfiles where fileID=$image_id");
                            if ($image_user_id == $user_id) {
                                $hasfront = 0;
                                $hasback = 0;
                                foreach ($products_fb as $prod) {
                                    if (in_array($prod->type, array(1, 2)))
                                        $hasfront = 1;
                                    if (in_array($prod->type, array(3, 5)))
                                        $hasback = 1;
                                }
                                $product_id = get_product_meta($pa_product_id, "inventory_id");
                                if ($product_id)
                                    $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=$product_id");

                                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0)
                                    $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    if (count($items) == 1) {
        if ($inventory_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $inventory_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12 || $shippin_id1 == 4)
                $items [0]['only_shirts'] = true;
            else
                $items [0]['only_shirts'] = false;
        }
    }
    return $items;
}

function get_all_item_woocommerce_2($itemsinfo, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();
    $arr = array();
    foreach ($itemsinfo as $value) {

        $product = array();
        $res = array();
        $item_id = $value['id'];
        $variant_id = $value['product_id'];
        $variant_sku = $wpdb->escape($value['sku']);
        $quantity = $value['quantity'];
        $item_price = $value['subtotal'] / $quantity;

        $arr[$item_id]['variant_id'] = $variant_id;
        $arr[$item_id]['variant_sku'] = $variant_sku;
        foreach ($value['meta'] as $val) {
            if (strtolower($val['key']) == 'color' || strtolower($val['key']) == 'pa_color')
                $color_name = $val['value'];
            if (strtolower($val['key']) == 'size' || strtolower($val['key']) == 'pa_size')
                $size_name = $val['value'];
        }
        //debug($color_name);
        //debug($size_name);
        echo $variant_sku . '(' . $variant_id . ')<br />';
        if ($user_id == 10294) $color_name = str_replace(array("_", "-"), " ", $color_name);
        // bug due to the system can have more than a color and size with the same name
        $colors_id = get_colors_col($color_name);
        $sizes_id = get_sizes_col($size_name);
        if (!in_array($variant_sku, array("", NULL)) && !in_array($variant_id, array("", 0, NULL))) {
            $pa_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where meta_value = $variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");

            if (!$pa_variant_id) {
                echo 'not working<br />';
                $pa_variant_id = $wpdb->get_var("select id from wp_users_products_colors where woocommerce_id=$variant_id and sku ='$variant_sku'");
                debug($pa_variant_id);

            }
            //debug("select id from wp_users_products_colors where woocommerce_id=$variant_id and sku ='$variant_sku'");
            //debug($pa_variant_id);
            if ($pa_variant_id) {
                $variants_data = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where id=$pa_variant_id", ARRAY_A);
                //debug($variants_data);
                $pa_product_id = $variants_data[0]['users_products_id'];
                $res = $wpdb->get_results("select id,users_id,brand_id,product_id,front,back from `wp_users_products` where id = $pa_product_id", ARRAY_A);
                //debug($res);
                if ($res) {
                    $product = end($res);
                    $product['color_id'] = $variants_data[0]['color_id'];
                    $product['size_id'] = $variants_data[0]['size_id'];
                }
            } else {
                // this is the case when the woocommerce hook sends the product id instead the variant id, $res will return all the variants if the sku is wrong so we need to fix that
                $pa_product_id = get_product_id_meta_shop($variant_id, "woocommerce_id", $shop_id);
                if (!$pa_product_id)
                    $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=$variant_id");
                //debug( $pa_product_id);
                if ($pa_product_id) {
                    $res = $wpdb->get_results("select id,users_id,brand_id,product_id,front,back from `wp_users_products` where users_id = $user_id and id=$pa_product_id", ARRAY_A);
                    if ($res) {
                        $res = end($res);
                        $res['color_id'] = $wpdb->get_var("select color_id from wp_users_products_colors where users_products_id=$pa_product_id and sku='$variant_sku'");
                        $res['size_id'] = $wpdb->get_var("select size_id from wp_users_products_colors where users_products_id=$pa_product_id and sku='$variant_sku'");
                        foreach ($colors_id as $clr_id) {
                            foreach ($sizes_id as $sz_id) {
                                if ($clr_id == $res['color_id'] && $sz_id == $res['size_id']) {
                                    $product = $res;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
            if ($product) {
                $pa_product_id = $product['id'];
                $inventory_id = $product['product_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                $color_id = $product['color_id'];
                $size_id = $product['size_id'];
                if ($color_id == null || $size_id == null) {
                    $colors_id = implode(",", $colors_id);
                    $sizes_id = implode(",", $sizes_id);
                    $cs_products = $wpdb->get_results("select color_id,size_id from `wp_users_products_colors` where users_products_id=$pa_product_id and color_id in($colors_id) and size_id in($sizes_id)", ARRAY_A);
                    $color_id = $cs_products[0]['color_id'];
                    $size_id = $cs_products[0]['size_id'];
                }
                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0)
                    $items [] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
            } else {
                $pa_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where meta_value=$variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
                $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where id=$pa_variant_id");
                if (!empty($pa_variant_id)) {
                    foreach ($pa_variants as $var) {
                        $pa_product_id = $var->users_products_id;
                        $color_id = $var->color_id;
                        $size_id = $var->size_id;
                        $products_fb = $wpdb->get_results("select type,image_id from wp_users_products_images where type<>4 and users_products_id=$pa_product_id order by type asc");
                        $image_id = $products_fb[0]->image_id;
                        $image_user_id = $wpdb->get_var("select userID from wp_userfiles where fileID=$image_id");
                        if ($image_user_id == $user_id) {
                            $hasfront = 0;
                            $hasback = 0;
                            foreach ($products_fb as $prod) {
                                if (in_array($prod->type, array(1, 2)))
                                    $hasfront = 1;
                                if (in_array($prod->type, array(3, 5)))
                                    $hasback = 1;
                            }
                            $product_id = get_product_meta($pa_product_id, "inventory_id");
                            if ($product_id)
                                $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=$product_id");

                            if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0)
                                $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                            break;
                        }
                    }
                }
            }
        }
    }
    if (count($items) == 1) {
        if ($inventory_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $inventory_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12 || $shippin_id1 == 4)
                $items [0]['only_shirts'] = true;
            else
                $items [0]['only_shirts'] = false;
        }
    }
    return $items;
}


function regenerate_wooc_order_v2($order_id, $user_id, $shop_id = 0, $type = 0)
{
    global $wpdb;
    try {
        $where = ($shop_id != 0) ? " and id=$shop_id" : "";
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id.$where ");
        $params = ($shop_id != 0) ? "?shop_id=$shop_id" : "";
        //$url = 'https://ryankikta.com/woocommerce-order-hook/'.$params;
        $url = 'https://ryankikta.com/woocommerce-order-v3/' . $params;
        if ($user) {

            $wc_api = new Client($user->shop, $user->wc_key, $user->wc_secret,
                ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
            );
            $endpoint = "orders/$order_id";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
            $order = $wc_api->get($endpoint, $params);

            $order->key = array($user->wc_key);
            //debug($order);exit();
            //$new_order = object_to_array($order);
            if ($type == 1) {
                $order->send_type = 1;
                $order->user_orig = $user_id;
            }
            $data_to_send = json_encode($order);
            //debug($order);
            //exit();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            $headers = array(
                "Cache-Control: no-cache",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_to_send))
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $return = curl_exec($ch);
            $return = json_decode($return, true);
            return $return;
        }
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e, $order_id);
        return $return;
    }
}

function get_all_orders_wooc($shop_id, $order_id = NULL, $params = array())
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");

        $wc_api = new Client($user->shop, $user->wc_key, $user->wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "orders/$order_id";
        if ($order_id == NULL)
            $endpoint = "orders";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $order = $wc_api->get($endpoint, $params);

        return object_to_array($order);
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function get_wooc_order($user_id, $order_id, $shop_id = 0)
{
    global $wpdb;
    try {
        if ($shop_id != 0)
            $user = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");
        else
            $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");

        $wc_api = new Client($user->shop, $user->wc_key, $user->wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "orders/$order_id";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $order = $wc_api->get($endpoint, $params);
        $order->key = array($user->wc_key);
        return object_to_array($order);;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function get_wooc_order_note($shop_id, $order_id)
{
    global $wpdb;
    try {
        $shop = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");

        $wc_api = new Client($shop->shop, $shop->wc_key, $shop->wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "orders/$order_id/notes";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $order_note = $wc_api->get($endpoint, $params);
        $return = object_to_array($order_note);
        return $return;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }
}

function wooc_order_update_status($shop_id, $order_id, $status)
{
    global $wpdb;
    try {
        $shop = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");
        $wc_api = new Client($shop->shop, $shop->wc_key, $shop->wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $data = ['status' => $status];
        $endpoint = "orders/$order_id";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $order = $wc_api->put($endpoint, $data, $params);
        $return = object_to_array($order);
        return $return;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e, $order_id);
        return $return;
    }
}

//regenerate user orders
function regenerate_user_woo_orders($shop_id, $limit = 50, $page = 1)
{
    global $wpdb;

    $auth = getWoocommerceShopbyId($shop_id);
    @extract($auth);

    try {

        $url = "https://ryankikta.com/woocommerce-order-v3/?shop_id=$shop_id";
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        //$orders = $wc_api->orders->get(null,array('filter[limit]'=>$limit,'filter[offset]'=>($page-1)*$limit));
        $endpoint = "orders";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, "per_page" => $limit);
        $orders = $wc_api->get($endpoint, $params);

        $resposes = array();
        foreach ($orders as $order) {

            $order->send_type = 1;
            $data_to_send = json_encode($order);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            $headers = array(
                "Cache-Control: no-cache",
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_to_send))
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $return = curl_exec($ch);
            $return = json_decode($return, true);
            $order_id = $order->id;
            $resposes[$order_id] = $return;
        }

        return $resposes;
    } catch (HttpClientException $e) {
        return $e->getMessage();
    }

}

//regenerate missing orders
function regnerate_woo_users_order()
{
    global $wpdb;
    $shops = $wpdb->get_col("select distinct(shop_id) from wp_rmproductmanagement_orders where shop_id <>0 and  source = 6 and order_time > 1519925008 ");
    foreach ($shops as $shop_id)
        regenerate_user_woo_orders($shop_id, 100);
}

/********************************************Woocommerce User Data Functions******************************************/

function get_woocommerce_shop($user_id, $shop_id = 0)
{
    global $wpdb;
    $where = ($shop_id == 0) ? "" : " and id=$shop_id";
    return $wpdb->get_var("select shop from wp_users_woocommerce where users_id=$user_id $where");
}

function get_user_bytoken($token)
{
    global $wpdb;
    return $wpdb->get_var("select users_id from wp_users_woocommerce where `token`='" . $wpdb->escape($token) . "'");

}

function get_user_bykey($key)
{
    global $wpdb;
    $user_id = $wpdb->get_var("select users_id from wp_users_woocommerce where `wc_key`='" . $wpdb->escape($key) . "' or `wc_key_hash`='" . $wpdb->escape($key) . "';");
    return $user_id;
}

function get_user_byshopid($shop_id)
{
    global $wpdb;
    $user_id = $wpdb->get_var("select users_id from wp_users_woocommerce where `id`='" . $shop_id . "';");
    return $user_id;
}

function get_shopid_bykey($key)
{
    global $wpdb;
    $shop_id = $wpdb->get_var("select id from wp_users_woocommerce where `wc_key`='" . $wpdb->escape($key) . "' or `wc_key_hash`='" . $wpdb->escape($key) . "';");
    return $shop_id;
}

function wc_api_hash($data)
{
    return hash_hmac('sha256', $data, 'wc-api');
}

/**************************************Woocommerce Webhook Functions**********************************/
function get_wooc_webhook($auth, $webbhook_id = 0)
{
    try {
        @extract($auth);

        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );

        if ($webbhook_id > 0)
            $endpoint = "webhooks/" . $webbhook_id;
        else
            $endpoint = "webhooks/";

        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        return $wc_api->get($endpoint, $params);
    } catch (HttpClientException $e) {
        if ($webbhook_id > 0)
            $return = generate_errors_wooc($e, $webbhook_id);
        else
            $return = generate_errors_wooc($e);
        return $return;
    }
}

function create_wooc_webhook($auth, $data)
{
    @extract($auth);
    try {
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v3', 'verify_ssl' => false]
        );
        $endpoint = "webhooks";
        $params = array("q" => "wp-json/wc/v3/" . $endpoint);
        return $wc_api->post($endpoint, $data, $params);
    } catch (HttpClientException $e) {
        return generate_errors_wooc($e);
    }
}

function create_all_webhooks($shop_id)
{
    global $wpdb;

    $user = $wpdb->get_row('select * from wp_users_woocommerce where id = ' . $shop_id, ARRAY_A);
    $user['woocommerceshop'] = $user['shop'];
    $data = array();
    $data['name'] = 'RyanKikta Ordr Hook';
    $data['topic'] = 'order.created';
    $data['delivery_url'] = 'https://ryankikta.com/woocommerce-order-hook/?shop_id=' . $shop_id;
    // add order created hook
    create_wooc_webhook($user, $data);

    //add order updated hook
    $data['topic'] = 'order.updated';
    create_wooc_webhook($user, $data);
}

function delete_wooc_webhook($webbhook_id, $user_id, $method = 1)
{
    global $wpdb;
    $auth = getWoocommerceShop($user_id);
    @extract($auth);
    $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
        ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
    );
    try {
        $endpoint = "webhooks/" . $webbhook_id;
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
        return $wc_api->delete($endpoint, $params);
    } catch (HttpClientException $e) {
        try {
            $endpoint = "webhooks/" . $webbhook_id . "?_method=DELETE";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
            return $wc_api->post($endpoint, $params);
        } catch (HttpClientException $e) {
            $return = generate_errors_wooc($e);
            return $return;
        }
    }
}

function delete_wooc_shop_webhook($webbhook_id, $shop_id, $method = 1)
{
    global $wpdb;
    $auth = getWoocommerceShopbyId($shop_id);
    @extract($auth);
    $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
        ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
    );
    try {
        $endpoint = "webhooks/" . $webbhook_id;
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
        return $wc_api->delete($endpoint, $params);
    } catch (HttpClientException $e) {
        try {
            $endpoint = "webhooks/" . $webbhook_id . "?_method=DELETE";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
            return $wc_api->post($endpoint, $params);
        } catch (HttpClientException $e) {
            $return = generate_errors_wooc($e);
            return $return;
        }
    }
}

function get_wooc_shop_data($auth)
{
    @extract($auth);
    try {
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]);

        $endpoint = "system_status";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);

        return $wc_api->get($endpoint, $params);
    } catch (HttpClientException $e) {
        return $e->getMessage();
    }
}

/********************************************Woocommerce Errors Functions*********************************/

function generate_errors_wooc($e, $string = "")
{

    $woo_response = $e->getResponse();
    if ($e->getCode() == 0) {
        $errors = array(array('code' => 'woocommerce_api_invalid_store_url', 'message' => $e->getMessage()));
    } elseif ($e->getCode() == 408) {
        $errors = array(array('code' => 'woocommerce_api_request_timeout', 'message' => "Server timeout waiting for the HTTP request from the client"));
    } elseif ($e->getCode() == 405) {
        $errors = array(array('code' => 'woocommerce_api_noallowed_method', 'message' => "Erreur HTTP 405 Method not allowed"));
    } else {
        $woo_response = end(object_to_array($woo_response));
        if ($woo_response) {
            $pos = strpos($woo_response, '{"');
            $woo_response = substr($woo_response, $pos);
            $woo_response = json_decode($woo_response);
            $errors = array(array('code' => $woo_response->code, 'message' => $woo_response->message));
        } else
            $errors = array(array('code' => '', 'message' => $e->getMessage()));
    }
    $errors = process_woo_errors($errors, $string);
    return $errors;
}

function process_woo_errors($errors, $string = "")
{

    $wc_errors = array();
    if ($errors != NULL) {
        if (is_array($errors)) {
            foreach ($errors as $error) {
                if (is_object($error)) {
                    $code = $error->code;
                    $error_msg = $error->message;
                } else {
                    $code = $error['code'];
                    $error_msg = $error['message'];
                }
                $err = get_wooc_error_message($code, $string);
                if ($err == "")
                    $err = $error_msg;
                if ($err == 'The API key provided does not have write permissions')
                    $err = 'The API key provided does not have write permissions.  Find info on how to change permissions <a href="http://docs.woothemes.com/document/woocommerce-rest-api/">here</a>';
                $wc_errors[] = $err;
            }
        } else {
            $err = get_wooc_error_message($errors->code);
            $wc_errors[] = $err;
        }
    }
    return $wc_errors;
}

function get_wooc_error_message($code, $string = "")
{
    $wooc_error_msg = "";
    $errors = array(
        'woocommerce_api_authentication_error' => 'Consumer Key or Consumer  Secret is invalid',
        'woocommerce_api_invalid_handler' => 'The handler for the route is invalid',
        'woocommerce_api_jsonp_disabled' => 'JSONP support is disabled on your store',
        'woocommerce_api_invalid_store_url' => 'Invalid Store URL',
        'woocommerce_api_no_route' => 'You need to install our Ryan Kikta integration plugin. click <a href="/wp-content/uploads/' . get_post_meta(95239, 'ryankikta_plugin_date', true) . '/ryankikta-woocommerce-api-v' . get_post_meta(95239, 'ryankikta_plugin_version', true) . '.zip">here </a>to download it',
        'woocommerce_rest_product_invalid_id' => 'No product found with the ID equal to ' . $string,
        'woocommerce_rest_cannot_view' => 'Sorry, you cannot list resources.',
        'woocommerce_api_no_order_found' => 'No order found with the ID equal to ' . $string,
        'woocommerce_api_no_webhook_found' => 'No webhook found with the ID equal to  ' . $string,
        'woocommerce_api_invalid_webhook_topic' => 'Webhook topic is required and must be valid',
        'woocommerce_api_invalid_customer' => 'Invalid customer',
        'woocommerce_api_invalid_product' => 'Invalid product',
        'woocommerce_api_invalid_order' => 'Invalid order',
        'woocommerce_api_disabled' => 'The WooCommerce API is disabled on your store',
        'woocommerce_api_unsupported_method' => 'Unsupported request method',
        'woocommerce_api_jsonp_callback_invalid' => 'The JSONP callback function is invalid',
        'woocommerce_api_product_sku_already_exists' => 'The SKU already exists on another product',
        'woocommerce_api_missing_callback_param' => 'Missing parameter data'
    );
    if ($code != "" && isset($errors[$code]) && $errors[$code] != "")
        $wooc_error_msg = $errors[$code];
    return $wooc_error_msg;
}

function get_product_attributes_by_userid($user_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $wc_api = new Client($user->shop, $user->wc_key, $user->wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/attributes";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);

        $attributes = $wc_api->get($endpoint, $params);

        return $attributes;
    } catch (HttpClientException $e) {
        send_mail("get_product_attributes_by_userid", array("user_id" => $user_id, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return array();
    }
}

function get_product_attributes($auth, $id = null)
{

    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/attributes";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);

        $attributes = $wc_api->get($endpoint, $params);

        return $attributes;
    } catch (HttpClientException $e) {
        send_mail("get_product_attributes", array("wc_auth" => $auth, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return array();
    }

}

function create_products_attribute($auth, $data)
{

    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false, 'query_string_auth' => true]
        );
        $endpoint = "products/attributes";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $attributes = $wc_api->post($endpoint, $data, $params);
        return $attributes;
    } catch (HttpClientException $e) {
        return array();
    }

}

function create_products_attribute_terms($auth, $id, $data)
{

    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/attributes/" . $id . "/terms";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $terms = $wc_api->post($endpoint, $data, $params);
        return $terms;
    } catch (HttpClientException $e) {
        send_mail("create_products_attribute_terms", array("wc_auth" => $auth, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return array();
    }

}

function get_all_products_tags($auth)
{
    $page = 1;
    $return = array();
    $all_tags = array();
    $tags = get_wooc_product_tags_per_page($auth);
    $all_tags = array_merge($all_tags, $tags);

    while (!empty($tags)) {
        $page = $page + 1;
        $tags = get_wooc_product_tags_per_page($auth, $page);
        if (!empty($tags))
            $all_tags = array_merge($all_tags, $tags);
    }
    foreach ($all_tags as $tag)
        $return[$tag->id] = $tag->name;
    return $return;

}

function get_wooc_product_tags_per_page($auth, $page = 1)
{
    try {
        $return = array();
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/tags";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, "per_page" => 100, "page" => $page);
        return $wc_api->get($endpoint, $params);
    } catch (HttpClientException $e) {
        send_mail("get_wooc_product_tags_per_page", array("wc_auth" => $auth, "page" => $page, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return array();
    }
}

function create_product_tag($auth, $data)
{
    try {
        @extract($auth);
        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/tags";
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $tag = $wc_api->post($endpoint, $data, $params);
        return $tag->id;
    } catch (HttpClientException $e) {
        send_mail("create_product_tag", array("wc_auth" => $auth, "message" => $e->getMessage(), "response" => $e->getResponse()));
        return array();
    }
}

function update_products_attribute($auth, $id, $data)
{

    try {
        @extract($auth);

        $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
            ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
        );
        $endpoint = "products/attributes/" . $id;
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint);
        $attributes = $wc_api->put($endpoint, $data, $params);
        return $attributes;
    } catch (HttpClientException $e) {
        $return = generate_errors_wooc($e);
        return $return;
    }

}

function delete_product_attribute($auth, $id, $method = 1)
{
    @extract($auth);
    $wc_api = new Client($woocommerceshop, $wc_key, $wc_secret,
        ['wp_api' => true, 'version' => 'wc/v2', 'verify_ssl' => false]
    );
    try {
        $endpoint = "products/attributes/" . $id;
        $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
        $attributes = $wc_api->delete($endpoint, $params);
        return $attributes;
    } catch (HttpClientException $e) {
        try {
            $endpoint = "products/attributes/" . $id . "?_method=DELETE";
            $params = array("q" => "/wp-json/wc/v2/" . $endpoint, 'force' => true);
            return $wc_api->post($endpoint, $params);
        } catch (HttpClientException $e) {
            $return = generate_errors_wooc($e);
            return $return;
        }
    }

}

function get_attribute_data($auth, $color_arr, $size_arr)
{

    $attributes = get_product_attributes($auth);
    $attr_color_exist = $attr_size_exist = false;

    if (!empty($attributes)) {
        foreach ($attributes as $attribute) {
            if ($attribute->slug == 'pa_color') {
                $attr_color_exist = true;
                if ($attribute->name == 'color')
                    update_products_attribute($auth, $attribute->id, array('name' => 'Color'));
            }
            if ($attribute->slug == 'pa_size') {
                $attr_size_exist = true;
                if ($attribute->name == 'size')
                    update_products_attribute($auth, $attribute->id, array('name' => 'Size'));
            }
        }
    }

    if (!$attr_color_exist)
        create_products_attribute($auth, array('name' => 'Color'));

    if (!$attr_size_exist)
        create_products_attribute($auth, array('name' => 'Size'));

    $color_arr = implode(' | ', $color_arr);
    $size_arr = implode(' | ', $size_arr);

    return array($color_arr, $size_arr);
}

function ship_woo_order($order_id, $printer_id, $tracking = "")
{
    require_once(ABSPATH . 'wp-content/themes/ryankikta/product-functions/ryankikta-wc-api-client.php');
    global $wpdb;
    $items = array();
    $order_details = $wpdb->get_results("select oid,woocommerce_id,tracking from wp_rmproductmanagement_order_details where order_id = $order_id and printer_id = $printer_id");
    //debug($order_details );
    $order = $wpdb->get_row("select shop_id,external_id from wp_rmproductmanagement_orders where order_id = $order_id");
    //debug($order );
    $shop = $wpdb->get_row("select * from wp_users_woocommerce where id = " . $order->shop_id, ARRAY_A);

    foreach ($order_details as $ord) {
        $items[] = array(
            "id" => $ord->woocommerce_id,
            "tracking" => $tracking
        );
    }
    $wc_api = new WC_API_Client1($shop['wc_key'], $shop['wc_secret'], $shop['shop']);
    //         debug($wc_api->get_order($order->external_id));

    // $wc_api->custom->setup('orders','order');
    debug($order->external_id);
    debug($items);
    //debug($wc_api->custom->get($order->external_id));
    debug($wc_api->update_order_shipment($order->external_id, array('order' => array("line_items" => $items, "notify_customer" => 1))));
    //exit;
    //$order_shippment = json_decode(json_encode($wc_api->update_order_shipment($order->external_id, $items),true));
    //debug($order_shippment);
//$wc_api1 = new WC_API_Client($shop['wc_key'], $shop['wc_secret'], $shop['shop']);
//debug($wc_api1->orders->get($order->external_id));
    // json_decode(json_encode($wc_api->custom->post($order->external_id.'/shipment',$items)), true);
    //  debug($wc_api->custom->post($order->external_id.'/shipment',$items));
}

function get_woo_modsecurity($user_id)
{

    global $wpdb;

    $mod_security = get_user_meta($user_id, 'woo_mod_security', true);

    return ($mod_security == 1) ? true : false;

}

//fix the vairant_id = 0 in woocommece products variant meta
function fix_woo_variant_meta_id($product_id, $shop_id)
{

    global $wpdb;
    $_variants = $wpdb->get_results("select id,sku from wp_users_products_colors where users_products_id = $product_id ", ARRAY_A);
    $variants = array();
    foreach ($_variants as $_variant) {
        $variants[$_variant['id']] = $_variant['sku'];
    }

    $meta = $wpdb->get_results("select id,variant_id,meta_value,shop_id from wp_variants_meta where product_id = $product_id and shop_id=$shop_id and meta_key='woocommerce_id'", ARRAY_A);
    $metas = array();
    foreach ($meta as $met) {
        $metas[$met['meta_value']] = $met['id'];
    }

    $auth = $wpdb->get_row("select *,shop as woocommerceshop from wp_users_woocommerce where id =$shop_id ", ARRAY_A);
//debug($auth);
    $woo_id = $wpdb->get_var("select meta_value from wp_products_meta where meta_key='woocommerce_id' and  product_id = $product_id");

    $woo_product = getWooproduct($woo_id, $auth);
//debug($woo_product);
    $all_woo_vars = $woo_product->product->variations;
//debug($all_woo_vars);

    $woo_variations = array();
    $woo_variations_sku = array();
    foreach ($all_woo_vars as $woo_var) {

//$woo_variations[$woo_var->id]=$woo_var->sku;
        //$woo_variations_sku[$woo_var['sku']]=$woo_var['id'];
        $var_id = $woo_var->id;
        $var_sku = $woo_var->sku;
        //find variant_id from wp_users_products_colors
        $pr_variant_id = array_search($var_sku, $variants);
        //find meta_value id from  wp_variants_meta
        $pr_meta_id = $metas[$var_id];
        $sql = "update wp_variants_meta set variant_id = $pr_variant_id where id = $pr_meta_id ";
        //echo $sql.'<br />';
        $wpdb->query($sql);


    }

}

function fix_all_vaiant_meta_id()
{

    global $wpdb;
    $fixed_prod = array();
    $all_prds = $wpdb->get_results("select product_id,shop_id from wp_variants_meta where variant_id = 0 and meta_key='woocommerce_id' order  by product_id ASC", ARRAY_A);
    foreach ($all_prds as $prd) {

        if (!in_array($prd['product_id'], $fixed_prod)) {
            $product_id = $prd['product_id'];
            $shop_id = $prd['shop_id'];
            $shop_exists = $wpdb->get_var("select count(id) from wp_users_woocommerce where id = $shop_id");
            if ($shop_exists) {
                fix_woo_variant_meta_id($product_id, $shop_id);
                $fixed_prod[] = $product_id;
            }
        }
//break;


    }
    return $fixed_prod;
}

function send_mail($subject, $data)
{
    mail("team@ryankikta.com", $subject, var_export($data, true));
}
