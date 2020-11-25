<?php
require_once(get_stylesheet_directory() . '/API/woocommerce-rest-api-client/lib/woocommerce-api.php');

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
    $woo_shop = array();
    $checkuser = $wpdb->get_result("select `users_id`,`shop`,`token`,`version`,`wc_key`,`wc_secret`,`pa_version` from `wp_users_woocommerce` where `id` = $id");
    if ($wpdb->num_rows($checkuser) != 0) {
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
    if ($type == 2) {
        return (int)$count;
    }
    if ($count > 0) {
        return true;
    }
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
        if (isset($woo_response->product)) {
            return array(1, $woo_response);
        }
    }
    return array(0, null);
}

function get_woocommerce_product($woocommerce_id, $auth, $params = array())
{
    if ($woocommerce_id != 0 && !empty($auth)) {
        $woo_response = getWooproduct($woocommerce_id, $auth, $params = array());
        return $woo_response;
    }
    return false;
}

function get_woocommerce_product_import($user_id, $woocommerce_id, $shop_id = 0)
{
    try {
        $auth = ($shop_id != 0) ? getWoocommerceShopbyId($shop_id) : getWoocommerceShop($user_id);
        @extract($auth);
        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop);
        $params = array('fields' => 'title,description,sku,weight,tags,shipping_class_id,categories,short_description,images,variations');
        $images = array();
        $product = $wc_api->products->get($woocommerce_id, $params);
        $product = $product->product;
        foreach ($product->images as $img) {
            $images[] = $img->src;
        }

        $colors = array();
        foreach ($product->variations as $variant) {
            foreach ($variant->attributes as $attribute) {
                if (get_color_id($attribute->option) != null) {
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

        $data = array(
            "title" => $product->title,
            "sku" => $product->sku,
            "description" => $product->description,
            "weight" => $product->weight / 0.45359237,
            "tags" => implode(",", $product->tags),
            "woocommerceshippingid" => $product->shipping_class_id,
            "woocommercecategory" => implode(",", $product->categories),
            "woocommerceshortdesc" => $product->short_description,
            "shop_images" => $images,
            "shop_colors" => $shop_colors,
        );
        return $data;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $woocommerce_id);
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
                $url_plugin = 'https://ryankikta.com/wp-content/uploads/' . get_post_meta(95239, "ryankikta_plugin_date", true) . '/ryankikta-woocommerce-api-v' . get_post_meta(95239, "ryankikta_plugin_version", true) . '.zip'; ?>
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
                wp_mail('rkikta@ryankikta.com', 'adding product image issue', '');
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
        wp_mail("rkikta@ryankikta.com", "woocommerce response not valid", $result);
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
        if ($wc_cat_id == null) {
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
            wp_mail("rkikta@ryankikta.com", "woocommerce updating woocommerce product id error", $export);
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
        wp_mail('rkikta@ryankikta.com', 'error add woocommerce product', var_export($post, true));
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
            wp_mail("rkikta@ryankikta.com", "woocommerce add variants error", $export);
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

function WoocommerceApiCall($url, array $post = null)
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
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
        wp_mail('rkikta@ryankikta.com', 'call return curl error', var_export($err_rt, true));
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
function build_woo_data($POST, $wc_data, $all_variants, $wc_auth, $type = 1, $old_product = null)
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
        $data['product']['id'] = $woocommercenewproduct;
        $old_product = getWooproduct($woocommercenewproduct, $wc_auth);
        $old_variants = build_old_woo_variants($paproduct_id, $old_product->product->variations);
    }
    if ($type == 1 && (!isset($woocommercenewproduct) || $woocommercenewproduct == '' || $woocommercenewproduct = 0)) {
        $_edit_noexistant = 0;
    }

    if ($type == 2) {
        $old_variants = build_old_woo_variants($paproduct_id, $old_product->product->variations);
    }
    $old_images = array();
    if ($POST['pagetype'] == 2) {
        $old_images = $wc_data['old_images'];
    }

    $data['product']['title'] = $title;
    $data['product']['type'] = 'variable';
    $data['product']['sku'] = $sku;
    $data['product']['description'] = $updatedescription;
    $data['product']['short_description'] = $woocommerceshortdesc;
    $data['product']['visible'] = true;
    $data['product']['status'] = 'publish';
    $data['product']['catalog_visibility'] = 'visible';

    $shipping_class = '';
    if ($wc_data['woocommerceshippingid']) {
        $all_shipping = getWooShipping($wc_auth);
        $shipping_class = $all_shipping[$woocommerceshippingid];
        $data['product']['shipping_class'] = $shipping_class;
    }


    foreach ($wc_data['woocommercecategory'] as $wc_categ) {
        $data['product']['categories'][] = $wc_categ;
    }
    if (empty($wc_data['woocommercecategory'])) {
        $data['product']['categories'][] = 0;
    }

    if ($tags != "") {
        $data['product']['tags'] = explode(',', str_replace('"', '\"', stripslashes($tags)));
    }
    if ($weight != "") {
        $data['product']['weight'] = $weight * 0.45359237;
    }
    $default_color = $color_arr[0];
    $default_size = $size_arr[0];
    list($color_arr, $size_arr) = get_attribute_data($wc_auth, $color_arr, $size_arr);
    if ($wc_auth['user_id'] == 10294) {
        $color_arr = array_map('dave_color_filter', $color_arr);
        $wc_variants = array_map('dave_variant_filter', $wc_variants);
    }
    $data['product']['attributes'][] = array('name' => 'Color', 'slug' => 'color', 'options' => $color_arr, 'position' => 0, 'visible' => true, 'variation' => true);
    $data['product']['attributes'][] = array('name' => 'Size', 'slug' => 'size', 'options' => $size_arr, 'position' => 1, 'visible' => true, 'variation' => true);

    $data['product']['default_attributes'][] = array('name' => 'Color', 'slug' => 'color', 'option' => $default_color);
    $data['product']['default_attributes'][] = array('name' => 'Size', 'slug' => 'size', 'option' => $default_size);
    $wooc_variants = create_woo_variants($POST, $wc_auth, $wc_data, $wc_variants, $old_variants, $_edit_noexistant, $shipping_class);
    @extract($wooc_variants);
    $data['product']['variations'] = $variants_wc;

    $data['product']['images'] = create_woo_images_data($POST, $images, $old_images);
    $data['wooc_edit_images'] = $wooc_edit_images;

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
            if (isset($old_images[$image['id']]) && $old_images[$image['id']] != 0) {
                $all_images[] = array('position' => $key, 'id' => $old_images[$image['id']]);
            } else {
                $all_images[] = array('position' => $key, 'src' => $image['src']);
            }
        }
    } else {
        foreach ($images as $key => $image) {
            $all_images[] = array('position' => $key, 'src' => $image['src']);
        }
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
    $arr = array();
    @extract($data);
    $shop_id = $wc_auth['shop_id'];
    $variants_wc = array();
    $arr['check_old_variant'] = $check_old_variant;
    $arr['variants'] = $variants;
    $arr['old_variants'] = $old_variants;
    $first_color_id = 0;
    $wooc_edit_images = true;
    foreach ($variants as $key => $variant) {
        $sku = ($variant['sku'] == "") ? stripcslashes($title) . '-' . $variant['color_name'] . '-' . $variant['size_name'] : $variant['sku'];
        $color_id = $variant['color_id'];
        $image_id = $variant['image_id'];
        $image_url = "";
        $has_edit_image = false;
        if (isset($POST['pa_product_id']) && $POST['pagetype'] == 2) {
            $products_id = $POST['pa_product_id'];
            $wooc_image_id = get_image_color_meta_shop($products_id, $color_id, $image_id, 'woocommerce_id', $shop_id);
            if ($wooc_image_id) {
                $has_edit_image = true;
            }
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
        if ($first_color_id != $color_id && !$has_edit_image) {
            $first_color_id = $color_id;
            $image_url = $variant['image_url'];
            $wooc_edit_images = false;
        }

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
                    'slug' => 'color',
                    'option' => $variant['color_name'],
                    'position' => $key
                ),
                array(
                    'name' => 'Size',
                    'slug' => 'size',
                    'option' => $variant['size_name'],
                    'position' => $key
                )
            ),
        );
        if ($image_url != "") {
            $variant_wc['image'] = array(
                array(
                    'src' => $image_url,
                )
            );
        }
        if ($has_edit_image) {
            $variant_wc['image'] = array(
                array(
                    'id' => $wooc_image_id,
                )
            );
        }

        if (!empty($old_variants) && $check_old_variant) {
            if (isset($old_variants[$variant['color_id'] . '_' . $variant['size_id']]) && $old_variants[$variant['color_id'] . '_' . $variant['size_id']] != "") {
                $variant_wc_id = array('id' => $old_variants[$variant['color_id'] . '_' . $variant['size_id']]);
                $variant_wc = array_merge($variant_wc_id, $variant_wc);
            }
        }

        $variants_wc[] = $variant_wc;
    }
    $arr['variants_wc'] = $variants_wc;
    //mail("team@ryankikta.com","function create_woo_variants",var_export(array($wooc_edit_images,$variants_wc),true));
    return array('wooc_edit_images' => $wooc_edit_images, 'variants_wc' => $variants_wc);
}

function addWoocommerceProductv2($POST, $wc_data, $data, $wc_auth, $products_id, $user_id, $shopid, $toremove = array(), $toUpdate = array())
{
    global $wpdb;
    try {
        @extract($wc_auth);
        $arr = array('user_id' => $user_id);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $separate_image = get_user_meta($user_id, 'woo_image_upload', true);

        $mod_security = ($_mod_security == 1) ? true : false;
        $arr['mod_security'] = $mod_security;
        if ($user_id == 28569) {
            $version = 3;
        } else {
            $version = 2;
        }

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security), $version);
        $wooc_edit_images = $wc_data['wooc_edit_images'];
        unset($wc_data['wooc_edit_images']);
        if ($separate_image == 1) {
            $woo_images = $wc_data['product']['images'];
            unset($wc_data['product']['images']);
        }
        $is_create = true;
        if (isset($wc_data['product']['id']) && $wc_data['product']['id'] > 0) {
            $is_create = false;
        }

        $arr['is_create'] = $is_create;
        $arr['action'] = "create";
        $arr['data'] = $wc_data;
        $prod_bysku = $wc_api->products->get_by_sku($wc_data['product']['sku']);
        //$arr['prod_bysku'] = $prod_bysku;
        //$arr['prod_wooc_id'] = (int)$wc_data['product']['id'];
        //$arr['prod_bysku_id'] = (int)$prod_bysku->product->id;
        if ($prod_bysku != null) {
            if ($is_create || (!$is_create && (int)$wc_data['product']['id'] != (int)$prod_bysku->product->id)) {
                $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
                $count_shop = check_woocommerce_shop($user_id, 2);
                $shop_text = ($count_shop == 1) ? '' : 'for shop "' . $wpdb->get_var("select shop from wp_users_woocommerce where id=$shopid") . '"';
                $error_title = 'Error ' . $text . ' product in woocommerce ' . $shop_text . ':';
                $sku = $wc_data['product']['sku'];
                $errors = array("The sku '$sku' already exist");

                $_SESSION['data'] = $POST;
                $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
                echo json_encode($return);
                exit();
            }
        }
        if (isset($wc_data['product']['id']) && $wc_data['product']['id'] > 0) {
            $arr['action'] = "edit";
            delete_old_variants($wc_api, $wc_auth, $wc_data['product']['id']);

            $update_over_post = get_user_meta($user_id, 'woo_update_over_post', true);

            $arr['update_over_post'] = $update_over_post;

            if ($update_over_post == 1) {
                $woo_response = $wc_api->products->update_pa($wc_data['product']['id'], $wc_data);
            } else {
                $woo_response = $wc_api->products->update($wc_data['product']['id'], $wc_data);
            }
        } else {
            $woo_response = $wc_api->products->create($wc_data);
        }

        $arr['response'] = $woo_response;

        //if($user_id == 26894)
        //wp_mail("team@ryankikta.com","woocommerce response 3",var_export($arr,true));
        if (isset($woo_response->product->id)) {
            //if($user_id == 16111)
            //wp_mail("team@ryankikta.com","add edit works",var_export($arr,true));
            $woocommerce_id = $woo_response->product->id;
            if ($separate_image == 1) {
                // $images_checunk =  array_chunk($woo_images,4);
                //foreach($images_checunk as $woo_images){
                $woo_image_response = $wc_api->products->update($woocommerce_id . "/images", $woo_images);
                $woo_images_ret = $woo_image_response->images;
            // }
            } else {
                $woo_images_ret = $woo_response->product->images;
            }
            if ($POST['pagetype'] == 1) {
                $_SESSION['shops']['woocommerce_ids'][$shopid] = $woocommerce_id;
            }
            $woocommerceshortdescdb = base64_encode($data['woocommerceshortdesc']);

            $wc_cat_id = implode(",", $data['woocommercecategory']);
            if ($wc_cat_id == null) {
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
            foreach ($results as $res) {
                $all_meta[$res['meta_key']] = $res['meta_id'];
            }
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
            udapte_woocommerce_images($woo_images_ret, $products_id, $data['images'], $shopid);
            udapte_woocommerce_variants($woo_response->product->variations, $products_id, $shopid);
            if (!$wooc_edit_images) {
                udapte_woocommerce_variants_images($wc_api, $woo_response->product->variations, $wc_data, $products_id, $shopid, $user_id);
            }
        } else {
            $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
            $count_shop = check_woocommerce_shop($user_id, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_woocommerce where id=$shopid") . '"';
            $error_title = 'Error ' . $text . ' product in woocommerce ' . $shop_text;
            $errors = process_woo_errors($woo_response->errors);
            if (!empty($errors)) {
                $error_title .= ':';
            }

            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
    } catch (WC_API_Client_Exception $e) {
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_woocommerce_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_woocommerce where id=$shopid") . '"';
        $error_title = 'Error ' . $text . ' product in woocommerce ' . $shop_text . ':';
        $errors = generate_errors_wooc($e, $wc_api->store_url);
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function udapte_woocommerce_variants_images($wc_api, $wooc_variants, $wc_data, $products_id, $shopid, $user_id)
{
    global $wpdb;

    $images_ids = array();
    $custom_colors = get_custom_colors();
    foreach ($wooc_variants as $var) {
        foreach ($var->attributes as $attribute) {
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
                    $color_id = get_color_id($color_name);
                }
                $image = $var->image;
                $wooc_image_id = $image[0]->id;
                $image_id = $wpdb->get_var("SELECT image_id FROM `wp_users_products_colors` where `users_products_id` = $products_id and `color_id`=$color_id");
                if ($wooc_image_id > 0 && !isset($images_ids[$color_id]) && $image_id > 0) {
                    $images_ids[$color_id] = $wooc_image_id;
                }
            }
        }
    }

    foreach ($wc_data['product']['variations'] as $key => $var) {
        foreach ($var['attributes'] as $attribute) {
            if (strpos(strtolower($attribute['name']), 'color') !== false) {
                if (in_array($attribute['option'], array_keys($custom_colors))) {
                    $color_name = $custom_colors[$attribute['option']];
                } else {
                    $color_name = $attribute['option'];
                }

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
        }
        if (isset($images_ids[$color_id])) {
            $wc_data['product']['variations'][$key]['image'] = array(array('id' => $images_ids[$color_id]));
        } else {
            $wc_data['product']['variations'][$key]['image'] = array(array('id' => 0));
        }
    }
    $update_over_post = get_user_meta($user_id, 'woo_update_over_post', true);

    if ($update_over_post == 1) {
        $woo_response = $wc_api->products->update_pa($wc_data['product']['id'], $wc_data);
    } else {
        $woo_response = $wc_api->products->update($wc_data['product']['id'], $wc_data);
    }

    udapte_pa_wooc_variants_images($woo_response->product->variations, $products_id, $shopid);
}

function udapte_pa_wooc_variants_images($wooc_variations, $products_id, $shop_id)
{
    global $wpdb;
    $images_to_remove = array();
    $custom_colors = get_custom_colors();
    $wooc_img_colors = array();
    foreach ($wooc_variations as $var) {
        $image = $var->image;
        $wooc_image_id = $image[0]->id;
        foreach ($var->attributes as $attribute) {
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
                    $color_id = get_color_id($color_name);
                }
            }
        }
        $wooc_img_colors[$color_id][] = $wooc_image_id;
    }

    foreach ($wooc_img_colors as $color_id => $wooc_image_id) {
        delete_image_color_by_shop($products_id, $color_id, 'woocommerce_id', $shop_id);
    }

    foreach ($wooc_img_colors as $color_id => $wooc_image) {
        $image_id = $wpdb->get_var("SELECT image_id FROM `wp_users_products_colors` where `users_products_id` = $products_id and `color_id`=$color_id");
        foreach ($wooc_image as $wooc_image_id) {
            if ($image_id > 0 && $wooc_image_id > 0) {
                update_image_color_meta_shop($products_id, $color_id, $image_id, 'woocommerce_id', $wooc_image_id, $shop_id);
            }
        }
    }
}

function deleteWoocommerceProductV2($woocommerce_id, $wc_auth, $productid, $delete_meta = 0)
{
    global $wpdb;
    try {
        @extract($wc_auth);

        $_mod_security = get_user_meta($wc_auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security));

        $update_over_post = get_user_meta($user_id, 'woo_update_over_post', true);
        if ($update_over_post == 1) {
            $delete = $wc_api->products->delete_pa($woocommerce_id);
            if ($delete == null) {
                $delete = $wc_api->products->delete($woocommerce_id, true);
            }
        } else {
            $delete = $wc_api->products->delete($woocommerce_id, true);
        }

        if ($pa_version == null) {
            $variants = get_variants_product_meta_shop($productid, "woocommerce_id", $shop_id);
            if ($variants) {
                foreach ($variants as $wooc) {
                    $wooc_id = (int)$wooc['meta_value'];
                    if ($update_over_post == 1) {
                        $delete = $wc_api->products->delete_pa($wooc_id);
                    } else {
                        $delete = $wc_api->products->delete($wooc_id, true);
                    }
                }
            } else {
                $variants = $wpdb->get_col("select woocommerce_id from wp_users_products_colors where users_products_id=$productid");
                foreach ($variants as $wooc_id) {
                    $wooc_id = (int)$wooc_id;
                    if ($update_over_post == 1) {
                        $delete = $wc_api->products->delete_pa($wooc_id);
                    } else {
                        $delete = $wc_api->products->delete($wooc_id, true);
                    }
                }
            }
        }

        if ($delete_meta == 1) {
            $prodid = get_product_id_meta_shop($woocommerce_id, "woocommerce_id", $shop_id);
            if (!$prodid) {
                $shop_id = 0;
            }
            delete_product_meta_multi_shop($productid, "'woocommerce_id','woocommerce_cat_id','woocommerce_shipping_id','woocommerce_shortdesc'", $shop_id);
            delete_variants_product_meta_shop($productid, "woocommerce_id", $shop_id);
            delete_images_product_meta_shop($productid, 'woocommerce_id', $shop_id);
        }
        return $delete;
    } catch (WC_API_Client_Exception $e) {
        return $e->getMessage();
        //return  generate_errors_wooc($e,$wc_api->store_url,$woocommerce_id);
    }
}

function udapte_woocommerce_images($images, $product_id, $pa_image, $shopid)
{
    foreach ($images as $key => $image) {
        $pa_image_id = $pa_image[$key]['id'];
        insert_image_meta_shop($product_id, $pa_image_id, "woocommerce_id", $image->id, $shopid);
    }
}

function udapte_woocommerce_variants($variations, $product_id, $shopid)
{
    global $wpdb;

    $all_meta = array();
    $all_vars = array();
    $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $shopid and meta_key='woocommerce_id' and product_id=$product_id", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['variant_id']] = $res['id'];
    }
    $custom_sizes = get_custom_sizes();
    $custom_colors = get_custom_colors();
    foreach ($variations as $variation) {
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
                    $color_id = get_color_id($color_name);
                }
            }
            if (strpos(strtolower($attribute->name), 'size') !== false) {
                if (in_array($attribute->option, array_keys($custom_sizes))) {
                    $size_name = $custom_sizes[$attribute->option];
                } else {
                    $size_name = $attribute->option;
                }

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
    $arr['wooc_variants'] = $variations;
    $arr['all_meta'] = $all_meta;
    $arr['_tmp'] = $_tmp;
    $arr['all_vars'] = $all_vars;
    $arr["sql_var"] = $sql_var;
    $wpdb->query($sql_var);
}

function build_old_woo_variants($paproduct_id, $old_variants)
{
    global $wpdb;
    $existant_variants = array();

    $custom_sizes = get_custom_sizes();
    $custom_colors = get_custom_colors();
    $arr = array('custom_colors' => $custom_colors);
    foreach ($old_variants as $variation) {
        $color_id = "";
        $size_id = "";
        foreach ($variation->attributes as $attribute) {
            if (strpos(strtolower($attribute->name), 'color') !== false) {
                if (in_array($attribute->option, array_keys($custom_colors))) {
                    $color_name = $custom_colors[$attribute->option];
                    $arr[$variation->id]['color_name_1'] = $color_name;
                } else {
                    $color_name = $attribute->option;
                    $arr[$variation->id]['color_name_2'] = $color_name;
                }
                $color_id = get_color_id($color_name);
                $arr[$variation->id]['color_id_1'] = $color_id;
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $color_id = get_color_id($color_name);
                    $arr[$variation->id]['color_id_2'] = $color_id;
                    $arr[$variation->id]['color_name_3'] = $color_name;
                }
                if (!$color_id) {
                    $color_name = ucwords(implode(" ", explode('_', $attribute->option)));
                    $arr[$variation->id]['color_name_4'] = $color_name;
                }
            }
            if (strpos(strtolower($attribute->name), 'size') !== false) {
                if (in_array($attribute->option, array_keys($custom_sizes))) {
                    $size_name = $custom_sizes[$attribute->option];
                } else {
                    $size_name = $attribute->option;
                }
                $size_id = get_size_id($size_name);
                if (!$size_id) {
                    $size_name = ucwords(implode(" ", explode('-', $attribute->option)));
                    $size_id = get_size_id($size_name);
                }
                if (!$size_id) {
                    $size_name = ucwords(implode(" ", explode('_', $attribute->option)));
                }
            }
        }
        $colors_id = implode(",", get_colors_col($color_name));
        $sizes_id = implode(",", get_sizes_col($size_name));

        $arr[$variation->id]['attributes'] = $variation->attributes;
        $arr[$variation->id]['color_name'] = $color_name;
        $arr[$variation->id]['size_name'] = $size_name;
        $arr[$variation->id]['colors_id'] = $colors_id;
        $arr[$variation->id]['sizes_id'] = $sizes_id;

        $sql = "select color_id,size_id from `wp_users_products_colors` where users_products_id=$paproduct_id and color_id in($colors_id) and size_id in($sizes_id)";
        $arr[$variation->id]['sql'] = $sql;
        $cs_products = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($cs_products)) {
            $color_id = $cs_products[0]['color_id'];
            $size_id = $cs_products[0]['size_id'];
            $existant_variants[$color_id . '_' . $size_id] = $variation->id;
        }
    }
    $arr['pa_product_id'] = $paproduct_id;
    $arr['old_variants'] = $old_variants;
    //if($paproduct_id == 308340)
    //mail("team@ryankikta.com","existant_variants",var_export($arr,true));
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

function delete_old_variants($wc_api, $auth, $woocommerce_id)
{
    $product = $wc_api->products->get($woocommerce_id);
    $product = $product->product;
    $product_sku = $product->sku;
    $variants = $product->variations;
    foreach ($variants as $var) {
        $variant_id = $var->id;
        $variant_sku = $var->sku;
        if ($product_sku == $variant_sku) {
            deleteWoocommerceProductV2($variant_id, $auth);
        }
    }
}

function getWooproduct($woocommerce_id, $auth = array(), $params = array())
{
    try {
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));
        return $wc_api->products->get($woocommerce_id, $params);
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $woocommerce_id);
        return $return;
    }
}

function getProductsList($auth, $params = array())
{
    try {
        $all_products = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));
        $params = (!empty($params)) ? $params : array('filter[limit]' => 400, 'fields' => 'id,title');
        $return = $wc_api->products->get(null, $params);
        if (isset($return->products)) {
            foreach ($return->products as $product) {
                $all_products[$product->id] = $product->title;
            }
        }

        return $all_products;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
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

function getWoocommerce_sku($auth, $page = 1)
{
    global $wpdb;
    try {
        @extract($auth);
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));
        // $wc_api = new WC_API_Client($wc_key,$wc_secret,$woocommerceshop);
        $return = $wc_api->products->get(null, array('filter[limit]' => '50', 'filter[post_status]' => 'all', 'fields' => 'id,title,status,featured_src,permalink', 'page' => $page));
        if (isset($return->products)) {
            $all_products = array();
            foreach ($return->products as $product) {
                $pa_product_id = get_product_meta_shop_byfield("product_id", "woocommerce_id", $product->id, $shop_id);
                $all_products[] = array(
                    "id" => $product->id,
                    "title" => $product->title,
                    "status" => $product->status,
                    "url" => $product->permalink,
                    "image" => $product->featured_src,
                    "shop_id" => $shop_id,
                    "imported" => ($pa_product_id == null) ? 0 : 1,
                    "pa_id" => ($pa_product_id == null) ? 0 : $pa_product_id
                );
            }
            return $all_products;
        }
        return array();
    } catch (WC_API_Client_Exception $e) {
        return generate_errors_wooc($e, $wc_api->store_url);
    }
}

function get_all_products_wooc($auth, $params = array())
{
    try {
        $all_products = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));
        $return = $wc_api->products->get(null, $params);
        $products = $return->products;
        if (isset($products)) {
            foreach ($products as $key => $product) {
                $all_products[$key]['title'] = $product->title;
                $all_products[$key]['id'] = $product->id;
                $all_products[$key]['sku'] = $product->sku;
                $variants = $product->variations;
                foreach ($variants as $ind => $var) {
                    $all_products[$key]['variants'][$ind]['id'] = $var->id;
                    $all_products[$key]['variants'][$ind]['sku'] = $var->sku;
                }
            }
        }
        return $all_products;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function getWooCategories($auth = array())
{
    try {
        $categories = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));
        $woo_categories = $wc_api->products->get_categories();

        foreach ($woo_categories->product_categories as $category) {
            $categories[$category->id] = $category->name;
        }
        return $categories;
    } catch (WC_API_Client_Exception $e) {
        return generate_errors_wooc($e, $wc_api->store_url);
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
    }
    foreach ($categories as $category) {
        //if ($category->parent == $parent) {
        $checked = (in_array($category->id, $selected_ids)) ? "checked='checked'" : "";
        $tree .= '<li>'
                . '<input type="checkbox" id="node' . $shop . '-' . $category->id . '" checked="checked" />'
                . '<label><input type="checkbox" name="' . $name . '" value="' . $category->id . '" ' . $checked . ' /><span></span></label>'
                . '<label for="node' . $shop . '-' . $category->id . '">' . $category->name . '</label>';
        //$tree .= wc_display_tree_categs($categories, $selected_ids, $shop_id, $category->id, $limit++);
        $tree .= '</li>';
        //}
    }
    if (!empty($categories)) {
        $tree .= '</ul>';
    }
    return $tree;
}

function wc_categories($auth, $checked_ids = array(), $shop_id = 0)
{
    $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
    $mod_security = ($_mod_security == 1) ? true : false;
    $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

    $woo_categories = $wc_api->products->get_categories();
    //wp_mail("jbuck@ryankikta.com", "wc_categories", var_export($woo_categories, true));
    return wc_display_tree_categs($woo_categories->product_categories, $checked_ids, $shop_id);
}

function getWooShipping($auth)
{
    try {
        $shipping = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_shipping = $wc_api->product_shipping_class->product_shipping_class();
        foreach ($woo_shipping->product_shipping_class as $shipping_class) {
            $shipping[$shipping_class->id] = $shipping_class->name;
        }
        return $shipping;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function getWooShipping_zones($auth)
{
    try {
        $shipping = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_shipping_zones = $wc_api->shipping_zone->get_shipping_zones();

        return $woo_shipping_zones;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function createWooShipping_zones($auth, $data)
{
    try {
        $shipping = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_shipping_zones = $wc_api->shipping_zone->create_shipping_zones($data);

        return $woo_shipping_zones;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function updateWooShipping_zone_location($auth, $data)
{
    try {
        $shipping = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_shipping_zones = $wc_api->shipping_zone->update_shipping_zone_location($data);

        return $woo_shipping_zones;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function updateWooShipping_zone_methods($auth, $data)
{
    try {
        $shipping = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_shipping_zones = $wc_api->shipping_zone->update_shipping_zone_methods($data);

        return $woo_shipping_zones;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function create_shipping_zones_woocommerce($auth)
{
    @extract($auth);
    if ($pa_version >= "3.3.7") {
        $shipping = getWooShipping_zones($auth);
        if ($shipping == null) {
            $zones = array(
                array(
                    'zone' => array('name' => 'United State'),
                    'zone_locations' => array(
                        'locations' => array(
                            array('code' => 'US')
                        )
                    )),
                array(
                    'zone' => array('name' => 'Canada'),
                    'zone_locations' => array(
                        'locations' => array(
                            array('code' => 'CA')
                        )
                    ))
            );
            foreach ($zones as $ship_zone) {
                $zone = createWooShipping_zones($auth, $ship_zone['zone']);
                $zone_id = $zone->zone_id;
                $ship_zone['zone_locations']['zone_id'] = $zone_id;

                updateWooShipping_zone_location($auth, $ship_zone['zone_locations']);
                $data_methods = array(
                    'zone_id' => $zone_id,
                    'method_id' => 'flat_rate'
                );
                updateWooShipping_zone_methods($auth, $data_methods);
            }
        }
    }
}


function getWootags($auth)
{
    try {
        $categories = array();
        $_mod_security = get_user_meta($auth['user_id'], 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($auth['wc_key'], $auth['wc_secret'], $auth['woocommerceshop'], array('mod_security' => $mod_security));

        $woo_tags = $wc_api->products->get_tags();
        foreach ($woo_tags->product_tags as $tag) {
            $tags[$tag->id] = $tag->name;
        }

        return $tags;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function get_wooc_shop_data($auth)
{
    @extract($auth);
    try {
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;
        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security));

        return $wc_api->store->get();
    } catch (WC_API_Client_Exception $e) {
        return $e->getMessage();
    }
}

function check_product_existe_woocommerce($woocommerce_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($woocommerce_id, "woocommerce_id", $shop_id);
    if (!$pa_product_id) {
        $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=" . $woocommerce_id);
    }
    if ($pa_product_id) {
        $existe = true;
    }
    return array("status" => 200, "data" => $existe);
}

/************************************************Woocommerce Order Functions*****************************************/

function get_order_woocommerce_data($order)
{
    $order_id = $order['id'];
    $email = ($order['billing_address']) ? $order['billing_address']['email'] : $order['billing']['email'];
    $customerphone = ($order['billing_address']) ? $order['billing_address']['phone'] : $order['billing']['phone'];
    $order_status = $order['status'];
    $shipping_addresses = ($order['shipping_address']) ? $order['shipping_address'] : $order['shipping'];
    $billing_address = ($order['billing_address']) ? $order['billing_address'] : $order['billing'];


    return array('order_status' => $order_status, 'shop_order_id' => $order_id, 'shop_order_name' => $order_id, 'email' => $email, 'customerphone' => $customerphone, 'shipping_addresses' => $shipping_addresses, 'itemsinfo' => $order['line_items'], 'billing_address' => $billing_address);
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
    } else {
        return array();
    }
}

function get_all_item_woocommerce($itemsinfo, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();
    $arr = array();
    //mail("team@ryankikta.com","wooc itemsinfo",var_export($itemsinfo,true));
    if (empty($itemsinfo)) {
        return $items;
    }
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
                if ($color_name != "") {
                    $colors_id = get_colors_col($color_name);
                }
            }
            if (strtolower($val['key']) == 'size' || strtolower($val['key']) == 'pa_size') {
                $size_name = $val['value'];
                if ($size_name != "") {
                    $sizes_id = get_sizes_col($size_name);
                }
            }
        }
        //debug($color_name);
        //debug($size_name);
        if ($user_id == 10294) {
            $color_name = str_replace(array("_", "-"), " ", $color_name);
        }
        // bug due to the system can have more than a color and size with the same name

        if (!in_array($variant_sku, array("", null)) && !in_array($variant_id, array("", 0, null))) {
            $pa_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where meta_value = $variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
            //mail('team@ryankikta.com','sql woo',"select variant_id from wp_variants_meta where meta_value = $variant_id and meta_key='woocommerce_id' and shop_id=$shop_id");
            if (!$pa_variant_id) {
                $pa_variant_id = $wpdb->get_var("select id from wp_users_products_colors where woocommerce_id=$variant_id and sku ='$variant_sku'");
            }
            if (!$pa_variant_id) {
                $pa_variant_id = $wpdb->get_var("select id from wp_users_products_colors where sku ='$variant_sku'");
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
                    //mail("team@ryankikta.com","item prod 1",var_export($product,true));
                }
            } else {
                // this is the case when the woocommerce hook sends the product id instead the variant id, $res will return all the variants if the sku is wrong so we need to fix that
                $pa_product_id = get_product_id_meta_shop($variant_id, "woocommerce_id", $shop_id);
                if (!$pa_product_id) {
                    $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=$variant_id");
                }
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
                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                    $items [] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
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
                                    if (in_array($prod->type, array(1, 2))) {
                                        $hasfront = 1;
                                    }
                                    if (in_array($prod->type, array(3, 5))) {
                                        $hasback = 1;
                                    }
                                }
                                $product_id = get_product_meta($pa_product_id, "inventory_id");
                                if ($product_id) {
                                    $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=$product_id");
                                }

                                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                                    $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                                }
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
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12 || $shippin_id1 == 4) {
                $items [0]['only_shirts'] = true;
            } else {
                $items [0]['only_shirts'] = false;
            }
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
            if (strtolower($val['key']) == 'color' || strtolower($val['key']) == 'pa_color') {
                $color_name = $val['value'];
            }
            if (strtolower($val['key']) == 'size' || strtolower($val['key']) == 'pa_size') {
                $size_name = $val['value'];
            }
        }
        //debug($color_name);
        //debug($size_name);
        echo $variant_sku . '(' . $variant_id . ')<br />';
        if ($user_id == 10294) {
            $color_name = str_replace(array("_", "-"), " ", $color_name);
        }
        // bug due to the system can have more than a color and size with the same name
        $colors_id = get_colors_col($color_name);
        $sizes_id = get_sizes_col($size_name);
        if (!in_array($variant_sku, array("", null)) && !in_array($variant_id, array("", 0, null))) {
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
                if (!$pa_product_id) {
                    $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id = $user_id and woocommerce_id=$variant_id");
                }
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
                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                    $items [] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
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
                                if (in_array($prod->type, array(1, 2))) {
                                    $hasfront = 1;
                                }
                                if (in_array($prod->type, array(3, 5))) {
                                    $hasback = 1;
                                }
                            }
                            $product_id = get_product_meta($pa_product_id, "inventory_id");
                            if ($product_id) {
                                $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=$product_id");
                            }

                            if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                                $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                            }
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
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12 || $shippin_id1 == 4) {
                $items [0]['only_shirts'] = true;
            } else {
                $items [0]['only_shirts'] = false;
            }
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
        $url = 'https://ryankikta.com/woocommerce-order-hook/' . $params;
        if ($user) {
            $mod_security = get_woo_modsecurity($user->users_id);

            $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
            $order = $wc_api->orders->get($order_id);
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
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
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
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_to_send))
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
            $return = curl_exec($ch);
            $return = json_decode($return, true);
            return $return;
        }
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $order_id);
        return $return;
    }
}

function get_all_orders_wooc($user_id, $params = array(), $order_id = null)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $mod_security = get_woo_modsecurity($user_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $orders = $wc_api->orders->get($order_id, $params);
        $orders->key = array($user->wc_key);
        $return = object_to_array($orders);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function get_all_orders_wooc_2($shop_id, $params = array(), $order_id = null)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");
        $mod_security = get_woo_modsecurity($user_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $orders = $wc_api->orders->get($order_id, $params);
        $orders->key = array($user->wc_key);
        $return = object_to_array($orders);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function get_wooc_order($user_id, $order_id, $shop_id = 0)
{
    global $wpdb;
    try {
        if ($shop_id != 0) {
            $user = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id ");
        } else {
            $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        }
        $mod_security = get_woo_modsecurity($user->users_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $orders = $wc_api->orders->get($order_id, array());
        $orders->key = array($user->wc_key);
        return $orders;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        wp_mail('rkikta@ryankikta.com', "woo order Error $user_id - $shop_id - $order_id", var_export($e, true));
        return $return;
    }
}

function get_wooc_order_note($user_id, $order_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $mod_security = get_woo_modsecurity($user->users_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $orders = $wc_api->order_notes->get($order_id);
        $return = object_to_array($orders);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function get_wooc_order_status($user_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $mod_security = get_woo_modsecurity($user->users_id);

        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $status = $wc_api->orders->get_statuses();
        $return = object_to_array($status);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function get_wooc_order_status_byshop($shop_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where id = $shop_id");
        $mod_security = get_woo_modsecurity($user->users_id);

        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $status = $wc_api->orders->get_statuses();
        $return = object_to_array($status);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function wooc_order_update_status($user_id, $order_id, $status)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $mod_security = get_woo_modsecurity($user_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $orders = $wc_api->orders->update_status($order_id, $status);
        $return = object_to_array($orders);
        return $return;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $order_id);
        return $return;
    }
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
        $mod_security = get_woo_modsecurity($users_id);

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security));
        if ($webbhook_id > 0) {
            $webhook = $wc_api->webhooks->get($webbhook_id);
        } else {
            $webhook = $wc_api->webhooks->get();
        }
        return $webhook->webhooks;
    } catch (WC_API_Client_Exception $e) {
        if ($webbhook_id > 0) {
            $return = generate_errors_wooc($e, $wc_api->store_url, $webbhook_id);
        } else {
            $return = generate_errors_wooc($e, $wc_api->store_url);
        }
        return $return;
    }
}

function create_wooc_webhook($auth, $data)
{
    @extract($auth);
    $mod_security = get_woo_modsecurity($users_id);
    $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security));
    try {
        $webhook = $wc_api->webhooks->create($data);
        return $webhook;
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url);
        return $return;
    }
}

function create_all_webhooks($shop_id)
{
    global $wpdb;

    $user = $wpdb->get_row('select * from wp_users_woocommerce where id = ' . $shop_id, ARRAY_A);
    $user['woocommerceshop'] = $user['shop'];
    $data = array();
    $data['webhook']['name'] = 'RyanKikta Ordr Hook';
    $data['webhook']['topic'] = 'order.created';
    $data['webhook']['delivery_url'] = 'https://ryankikta.com/woocommerce-order-hook/?shop_id=' . $shop_id;
    // add order created hook
    $hook = create_wooc_webhook($user, $data);
    //echo "hook create<br>";
    //debug($hook);

    //add order updated hook
    $data['webhook']['topic'] = 'order.updated';
    $hook = create_wooc_webhook($user, $data);
    //echo "hook update<br>";
    //debug($hook);
}

function delete_wooc_webhook($webbhook_id, $user_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $mod_security = get_woo_modsecurity($user->users_id);

        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $wc_api->webhooks->delete($webbhook_id);
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $webbhook_id);
        return $return;
    }
}

function delete_wooc_shop_webhook($webbhook_id, $shop_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where id=$shop_id");
        $mod_security = get_woo_modsecurity($user->users_id);
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop, array('mod_security' => $mod_security));
        $wc_api->webhooks->delete($webbhook_id);
    } catch (WC_API_Client_Exception $e) {
        $return = generate_errors_wooc($e, $wc_api->store_url, $webbhook_id);
        return $return;
    }
}

/********************************************Woocommerce Errors Functions*************************************************/

function generate_errors_wooc($e, $store_url, $string = "", $debug = 0)
{
    $woo_response = $e->get_response();
    $arr = array('code' => $e->getCode(), 'message' => $e->getMessage(), 'errors' => $woo_response, "params_3" => $string, 'e_data' => $e);
    //debug($arr);
    //mail("team@ryankikta.com",'response errors :'.$store_url,var_export($arr,true));
    if ($debug == 1) {
        //mail("team@ryankikta.com",'response errors ',var_export($arr,true));
        //vd($arr);
    }
    if ($e->getCode() == 0) {
        $errors = array(array('code' => 'woocommerce_api_invalid_store_url', 'message' => $e->getMessage()));
        $string = $store_url;
    } elseif ($e->getCode() == 408) {
        $errors = array(array('code' => 'woocommerce_api_request_timeout', 'message' => "Server timeout waiting for the HTTP request from the client"));
        $string = $store_url;
    } elseif ($e->getCode() == 405) {
        $errors = array(array('code' => 'woocommerce_api_noallowed_method', 'message' => "Erreur HTTP 405 Method not allowed"));
        $string = $store_url;
    } else {
        if ($woo_response->body) {
            $pos = strpos($woo_response->body, '{"');
            $woo_response->body = substr($woo_response->body, $pos);
            $woo_response = json_decode($woo_response->body);
            $errors = $woo_response->errors;
        } else {
            $errors = array(array('code' => '', 'message' => $woo_response->error));
        }
    }
    $errors = process_woo_errors($errors, $string);
    return $errors;
}

function process_woo_errors($errors, $string = "")
{
    $wc_errors = array();
    if (!empty($errors)) {
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
                if (empty($err)) {
                    $err = $error_msg;
                }
                if ($err == 'The API key provided does not have write permissions') {
                    $err = 'The API key provided does not have write permissions.  Find info on how to change permissions <a href="http://docs.woothemes.com/document/woocommerce-rest-api/">here</a>';
                }
            }
        } else {
            $err = get_wooc_error_message($errors->code);
        }
        $wc_errors[] = $err;
    }
    return $wc_errors;
}

function get_wooc_error_message($code, $string = "")
{
    $errors = array(
        'woocommerce_api_authentication_error' => 'Consumer Key or Consumer  Secret is invalid',
        'woocommerce_api_invalid_handler' => 'The handler for the route is invalid',
        'woocommerce_api_jsonp_disabled' => 'JSONP support is disabled on your store',
        'woocommerce_api_invalid_store_url' => 'Invalid Store URL "' . $string . '"',
        'woocommerce_api_no_route' => 'You need to install our Ryan Kikta integration plugin. click <a href="/wp-content/uploads/' . get_post_meta(95239, 'ryankikta_plugin_date', true) . '/ryankikta-woocommerce-api-v' . get_post_meta(95239, 'ryankikta_plugin_version', true) . '.zip">here </a>to download it',
        'woocommerce_api_no_product_found' => 'No product found with the ID equal to ' . $string,
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
    $wooc_error_msg = (!empty($code) && !empty($errors[$code])) ? $errors[$code] : 'woocommerce server returned error:' . $code ;

    return $wooc_error_msg;
}

function get_product_attributes_by_userid($user_id)
{
    global $wpdb;
    try {
        $user = $wpdb->get_row("select * from wp_users_woocommerce where users_id = $user_id ");
        $wc_api = new WC_API_Client($user->wc_key, $user->wc_secret, $user->shop);
        $attributes = $wc_api->products_attributes->get();
        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function get_product_attributes($auth, $id = null)
{
    try {
        @extract($auth);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security, 'return_as_array' => true));

        $attributes = $wc_api->products_attributes->get($id);

        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function create_products_attribute($auth, $data)
{
    try {
        @extract($auth);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security, 'return_as_array' => true));

        $attributes = $wc_api->products_attributes->create($data);

        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function create_products_attribute_terms($auth, $id, $data)
{
    try {
        @extract($auth);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security, 'return_as_array' => true));

        $attributes = $wc_api->products_attributes->create_terms($id, $data);

        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function update_products_attribute($auth, $id, $data)
{
    try {
        @extract($auth);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security, 'return_as_array' => true));

        $attributes = $wc_api->products_attributes->update($id, $data);

        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function delete_product_attribute($auth, $id)
{
    try {
        @extract($auth);
        $_mod_security = get_user_meta($user_id, 'woo_mod_security', true);
        $mod_security = ($_mod_security == 1) ? true : false;

        $wc_api = new WC_API_Client($wc_key, $wc_secret, $woocommerceshop, array('mod_security' => $mod_security, 'return_as_array' => true));

        $attributes = $wc_api->products_attributes->delete($id, true);

        return $attributes;
    } catch (WC_API_Client_Exception $e) {
        return array();
    }
}

function get_attribute_data($auth, $color_arr, $size_arr)
{
    $attr = get_product_attributes($auth);
    $attributes = $attr['product_attributes'];
    $attr_color_exist = $attr_size_exist = false;

    if (!empty($attributes)) {
        foreach ($attributes as $attribute) {
            if ($attribute['slug'] == 'pa_color') {
                $attr_color_exist = true;
                if ($attribute['name'] == 'color') {
                    update_products_attribute($auth, $attribute['id'], array('name' => 'Color'));
                }
            }
            if ($attribute['slug'] == 'pa_size') {
                $attr_size_exist = true;
                if ($attribute['name'] == 'size') {
                    update_products_attribute($auth, $attribute['id'], array('name' => 'Size'));
                }
            }
        }
    }

    if (!$attr_color_exist) {
        create_products_attribute($auth, array('name' => 'Color'));
    }

    if (!$attr_size_exist) {
        create_products_attribute($auth, array('name' => 'Size'));
    }

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


    $order_note = [
	    'order_nots' => [
		'note' => 'Tracking Number: ' . $tracking,
		'customer_notify' => True
	    ]
    ];
   
    wp_mail('jbuck@ryankikta.com', "Order $order_id has shipped", "");
    $note_return = $wc_api->create_order_note($order->external_id, $order_note);
    wp_mail('jbuck@ryankikta.com', 'Woocommerce Order Note', var_export($note_return, true));



    //exit;
    //$order_shippment = json_decode(json_encode($wc_api->update_order_shipment($order->external_id, $items),true));
    //debug($order_shippment);
    //$wc_api1 = new WC_API_Client($shop['wc_key'], $shop['wc_secret'], $shop['shop']);
    //debug($wc_api1->orders->get($order->external_id));
    // json_decode(json_encode($wc_api->custom->post($order->external_id.'/shipment',$items)), true);
    //  debug($wc_api->custom->post($order->external_id.'/shipment',$items));
}

function resend_woocommerce_order_data($shop_id, $woocommerce_id)
{
    global $wpdb;

    $shop = $wpdb->get_row("select * from wp_users_woocommerce where id=$shop_id ", ARRAY_A);
    debug($shop);
    $wc_api = new WC_API_Client($shop['wc_key'], $shop['wc_secret'], $shop['shop']);
    $order = $wc_api->orders->get($woocommerce_id);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://ryankikta.com/woocommerce-order-hook/?shop_id=" . $shop_id);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order));  // we don't need body
    $return = curl_exec($ch);
    debug($return);

    curl_close($ch);
}

//regenerate user orders
function regenerate_user_woo_orders($shop_id, $limit = 50, $page = 1)
{
    global $wpdb;

    $shop = $wpdb->get_row("select * from wp_users_woocommerce where id=$shop_id ", ARRAY_A);
    //debug($shop);
    $wc_api = new WC_API_Client($shop['wc_key'], $shop['wc_secret'], $shop['shop']);


    $orders = $wc_api->orders->get(null, array('filter[limit]' => $limit, 'filter[offset]' => ($page - 1) * $limit));
    //debug($orders);exit;
    foreach ($orders->orders as $order) {
        $order_to_send = array("order" => json_decode(json_encode($order), true));
        //debug($order_to_send);
        // exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://ryankikta.com/woocommerce-order-hook/?shop_id=" . $shop_id);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("order" => json_decode(json_encode($order), true))));  // we don't need body
        $return = curl_exec($ch);
        //debug($return);

        curl_close($ch);
        //exit;
    }
}

//regenerate missing orders
function regnerate_woo_users_order()
{
    global $wpdb;
    $shops = $wpdb->get_col("select distinct(shop_id) from wp_rmproductmanagement_orders where shop_id <>0 and  source = 6 and order_time > 1519925008 ");
    foreach ($shops as $shop_id) {
        regenerate_user_woo_orders($shop_id, 100);
    }
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

//Analyzing woocommerce order
function analyze_woocommerce_order($woocommerce_id, $shop_id)
{
    global $wpdb;

    $shop = $wpdb->get_row("select * from wp_users_woocommerce where id=$shop_id ", ARRAY_A);
    //debug($shop);
    $wc_api = new WC_API_Client($shop['wc_key'], $shop['wc_secret'], $shop['shop']);


    $order_req = $wc_api->orders->get($woocommerce_id);

    if ($order_req->order) {
        $order = $order_req->order;
        debug($order->status);
        debug($order->line_items);
        $alllineitems = json_decode(json_encode($order->line_items), true);
        $all_items = get_all_item_woocommerce($alllineitems, $shop['users_id'], $shop_id);
        debug($all_items);
        foreach ($order->line_items as $item) {
            debug($item->name);

            //  debug("select * from wp_variants_meta where meta_key='woocommerce_id' and meta_value='".$item->product_id."' and shop_id=$shop_id");
            $var_meta = $wpdb->get_results("select * from wp_variants_meta where meta_key='woocommerce_id' and meta_value='" . $item->product_id . "' and shop_id=$shop_id");
            debug($var_meta);
        }
    }
}
