<?php
/*if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $currentusername = $current_user->user_login;
    $etsy_auth = getEtsyShop($currentuserid);
    @extract($etsy_auth);

    if ($version != 3 && $version = "")
        etsy_authentificate($currentuserid);
} */
ini_set('memory_limit', '-1');
function etsy_authentificate($user_id)
{
    define(OAUTH_CONSUMER_KEY, 'yfv5k4v72c8hklgtycaw2vvd');
    define(OAUTH_CONSUMER_SECRET, 'uloe9lv7em');
    $oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
    $oauth->enableDebug();
}

function getEtsyShop($user_id)
{
    $etsy_auth = array();
    $checkuser = $wpdb->get_result("SELECT `token`,`secret`,`shop`,`etsy_user_id`,`url_file`,`app_consumer_key`,`app_consumer_secret`,`version`,`users_id`,`id` FROM `wp_users_etsy` WHERE `users_id` = $user_id and (version =2 or version = 3)");
    $numshopsetsy = $wpdb->num_rows($checkuser);
    if ($numshopsetsy !== 0) {
        $shoprow = mysql_fetch_array($checkuser);
        $pa_etsy_proxy = get_user_meta($user_id, 'pa_etsy_proxy', true);
        $etsy_auth = array('etsytoken' => $shoprow["token"],
            'etsysecret' => $shoprow["secret"],
            'etsyshop' => $shoprow["shop"],
            'etsyuserid' => ($shoprow["etsy_user_id"] > 0) ? $shoprow["etsy_user_id"] : $shoprow["shop"],
            'url_file' => $shoprow["url_file"],
            'consumer_key' => $shoprow["app_consumer_key"],
            'consumer_secret' => $shoprow["app_consumer_secret"],
            'version' => $shoprow["version"],
            'user_id' => $user_id,
            'etsyshopId' => $shoprow["id"],
            'pa_etsy_proxy' => $pa_etsy_proxy,
            'module_version' => 0
        );
        if ($pa_etsy_proxy == 1) {
            $etsy_auth['url_file'] = "https://solutions.ryankikta.com/wp-content/themes/ryankikta/product-functions/etsy-api/etsy_proxy.php";
        }
        $content = json_decode(file_get_contents($etsy_auth['url_file'] . "?version=1"));
        if (is_array($content) && $content['status'] == 1) {
            $etsy_auth['module_version'] = 1;
        }
    }
    return $etsy_auth;
}

function getEtsyShopById($user_id, $shop_id)
{
    GLOBAL $wpdb;
    $etsy_auth = array();
    $check_shop = $wpdb->get_results("SELECT * 
	                              FROM `wp_users_etsy` 
				      WHERE `users_id` = $user_id 
                                      #AND `id`= $shop_id 
                                      AND (version = 2 or version = 3)", ARRAY_A);
    $val = $wpdb->num_rows;
    if ($val != 0) {
        $shoprow = $check_shop;
        $pa_etsy_proxy = get_user_meta($user_id, 'pa_etsy_proxy', true);
        $etsy_auth = array(
            'etsytoken' => $shoprow["token"],
            'etsysecret' => $shoprow["secret"],
            'etsyshop' => $shoprow["shop"],
            'etsyuserid' => ($shoprow["etsy_user_id"] > 0) ? $shoprow["etsy_user_id"] : $shoprow["shop"],
            'url_file' => $shoprow["url_file"],
            'consumer_key' => $shoprow["app_consumer_key"],
            'consumer_secret' => $shoprow["app_consumer_secret"],
            'version' => $shoprow["version"],
            'user_id' => $user_id,
            'etsyshopId' => $shop_id,
            'pa_etsy_proxy' => $pa_etsy_proxy,
            'language' => ($shoprow["language"] != "" && $shoprow["language"] != null) ? $shoprow["language"] : "",
            'module_version' => 0
        );
        //if ($pa_etsy_proxy == 1) {
        //    $etsy_auth['url_file'] = "https://solutions.ryankikta.com/wp-content/themes/ryankikta/product-functions/etsy-api/etsy_proxy.php";
        //}
        $content = json_decode(file_get_contents($etsy_auth['url_file']));
        if (is_array($content) && $content['status'] == 1) {
            $etsy_auth['module_version'] = 1;
        }
    }
    return $etsy_auth;
}

function get_app_access()
{
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $etsy_app_auth = array();
    $checkuser = $wpdb->get_result("SELECT `app_consumer_key`,`app_consumer_secret` FROM `wp_users_etsy` WHERE `users_id` = $currentuserid");
    $numshopsetsy = $wpdb->num_rows($checkuser);
    if ($numshopsetsy !== 0) {
        $shoprow = $wpdb->get_row($checkuser);
        $key = $shoprow[0];
        $secret = $shoprow[1];
        $url_file = $shoprow[2];
        $etsy_app_auth = array('url_file' => $url_file, 'consumer_key' => $key, 'consumer_secret' => $secret);
    }

    return $etsy_app_auth;
}

function check_etsy_shop($user_id, $type = 1)
{
    global $wpdb;
    $count = $wpdb->get_var("select count(id) from wp_users_etsy where users_id=$user_id");
    if ($type == 2) {
        return (int)$count;
    }
    if ($count > 0) {
        return true;
    }
    return false;
}

function getCurrentEtsyData($prodid)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $etsyactiveold = $row['etsyactive'];
    $etsytags = $row['tags'];
    $etsy_id = get_product_meta($prodid, 'etsy_id');
    $etsy_category_id = get_product_meta($prodid, 'etsy_category_id');
    $etsy_sub1_category_id = get_product_meta($prodid, 'etsy_sub1_category_id');
    $etsy_sub2_category_id = get_product_meta($prodid, 'etsy_sub2_category_id');
    $etsy_sub3_category_id = get_product_meta($prodid, 'etsy_sub3_category_id');
    $etsy_section_id = get_product_meta($prodid, 'etsy_section_id');
    $etsy_shipping_id = get_product_meta($prodid, 'etsy_shipping_id');
    $occasion = get_product_meta($prodid, 'occasion');
    $etsystyle = get_product_meta($prodid, 'etsy_style');
    $recipient = get_product_meta($prodid, 'etsy_recipient');
    $whomadeit = get_product_meta($prodid, 'whomadeit');
    $whenmade = get_product_meta($prodid, 'whenmade');
    $issupply = get_product_meta($prodid, 'issupply');
    $materials = get_product_meta($prodid, 'etsymaterials');
    $price = get_etsy_product_price($prodid);
    $etsy_id = ($etsy_id == "") ? 0 : $etsy_id;
    $etsy_data = array('price' => $price, 'etsyactiveold' => $etsyactiveold, 'etsy_id' => $etsy_id, 'etsycategory' => $etsy_category_id, 'etsysub1category' => $etsy_sub1_category_id, 'etsysub2category' => $etsy_sub2_category_id, 'etsysub3category' => $etsy_sub3_category_id, 'etsysection' => $etsy_section_id,
        'etsyshipping' => $etsy_shipping_id, 'etsyoccasion' => $occasion, 'etsystyle' => $etsystyle, 'etsyrecipient' => $recipient, 'whomadeit' => $whomadeit,
        'whenmade' => $whenmade, 'issupply' => $issupply, 'etsytags' => $etsytags, 'etsymaterials' => $materials);
    return $etsy_data;
}

function get_etsy_product_price($productid)
{
    global $wpdb;
    return $wpdb->get_var("select min(normalprice) from wp_users_products_colors where users_products_id = $productid");
}

function getEtsyData($data)
{
    $etsyactive = ($data['etsyactive']) ? esc_sql($data['etsyactive']) : 0;
    $etsynewproduct = esc_sql($data['etsynewproduct']);
    $etsycategory = esc_sql($data['etsycategory']);
    $etsysub1category = esc_sql($data['etsysub1category']);
    $etsysub2category = esc_sql($data['etsysub2category']);
    $etsysub3category = esc_sql($data['etsysub3category']);
    $etsysection = esc_sql($data['etsysection']);
    $etsyshipping = esc_sql($data['etsyshipping']);
    $etsytags = trim(esc_sql($data['tags']));
    $etsymaterials = trim(esc_sql($data['etsymaterials']));
    $etsyoccasion = esc_sql($data['etsyoccasion']);
    $etsystyle = trim(esc_sql($data['etsystyle']));
    $etsyrecipient = esc_sql($data['etsyrecipient']);
    $whomadeit = esc_sql($data['whomadeit']);
    $whenmade = esc_sql($data['whenmade']);
    $issupply = esc_sql($data['issupply']);
    if ($etsysection == '') {
        $etsysection = 0;
    }
    if ($etsyshipping == '') {
        $etsyshipping = 0;
    }

    return array('etsyactive' => $etsyactive, 'etsynewproduct' => $etsynewproduct, 'etsycategory' => $etsycategory, 'etsysub1category' => $etsysub1category, 'etsysub2category' => $etsysub2category, 'etsysub3category' => $etsysub3category, 'etsysection' => $etsysection,
        'etsyshipping' => $etsyshipping, 'etsyoccasion' => $etsyoccasion, 'etsystyle' => $etsystyle, 'etsyrecipient' => $etsyrecipient, 'whomadeit' => $whomadeit,
        'whenmade' => $whenmade, 'issupply' => $issupply, 'etsytags' => $etsytags, 'etsymaterials' => $etsymaterials);
}

function getEtsyProductData($etsy_id, $auth)
{
    $update_etsy = 0;
    @extract($auth);
    if ($etsy_id != 0 && !empty($auth)) {
        $url = "https://openapi.etsy.com/v2/listings/$etsy_id";
        $result = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
        if ($result["status"] == "success") {
            $belongsto_etsy_userid = $result['response']['results'][0]['user_id'];
            $state = $result['response']['results'][0]['state'];
            if ($belongsto_etsy_userid != $etsyuserid || $state == 'removed') {
                $etsy_status = 'Not Found';
            }
        } else {
            if ($result["code"] == 404) {
                $etsy_status = 'Not Found';
            } else {
                $etsy_status = "Unknown";
            }
        }
        if ($etsy_status != 'Not Found' && $etsy_status != 'Unknown') {
            $update_etsy = 1;
        }
    }
    return $update_etsy;
}

function buildEtsyVariants($variants)
{
    foreach ($variants as $variant) {
        $color_name = $variant['color_name'];
        $size_name = $variant['size_name'];
        $price = $variant['normalprice'];

        $variants_colors[] = array(
            "property_id" => 200,
            "value" => $color_name,
            "is_available" => true,
            "price" => (float)$price
        );

        $variants_sizes[] = array(
            "property_id" => 100,
            "value" => $size_name,
            "is_available" => true
        );
    }

    $variants_ets_colors = multi_unique($variants_colors);
    $variants_ets_sizes = multi_unique($variants_sizes);
    $etsy_variants = array_merge($variants_ets_colors, $variants_ets_sizes);

    return $etsy_variants;
}

function check_exist_all_colors($size_id, $exclud_color_id, $data)
{
    foreach ($data as $color_id => $sizes) {
        if ($color_id != $exclud_color_id) {
            if (!in_array($size_id, $sizes)) {
                return false;
            }
        }
    }

    return true;
}

function buildEtsyVariants_v2($variants)
{
    $data = array();
    $arr = array();
    foreach ($variants as $var) {
        $arr[$var['color_id']][] = $var['size_id'];
    }

    foreach ($variants as $key => $var) {
        $check_exist = check_exist_all_colors($var['size_id'], $var['color_id'], $arr);
        if (!$check_exist) {
            unset($variants[$key]);
        }
    }

    foreach ($variants as $variant) {
        $sku = $variant['sku_color'];
        $sku = substr($sku, -32, 32);
        $sku = preg_replace('/^([^a-zA-Z0-9])*/', '', $sku);
        $data[] = array(
            'property_values' => array(
                array(
                    'property_id' => 200,
                    'values' => array($variant['color_name']),
                ),
                array(
                    'property_id' => 62809790395,
                    'values' => array($variant['size_name']),
                )
            ),
            'sku' => $sku,
            'offerings' => array(
                '0' => array(
                    'price' => $variant['normalprice'],
                    'quantity' => 999,
                    'is_enabled' => 1,
                    'is_deleted' => 0
                )
            )
        );
    }

    return $data;
}

function addEtsyProduct($POST, $etsy_product_id, $etsy_data, $etsy_variants, $method, $etsy_auth, $min_price, $currentuserid, $products_id)
{
    global $wpdb;
    @extract($POST);
    @extract($etsy_data);
    $description = str_replace(array('<br>', '<br />'), array("\n", "\n"), stripslashes($description));
    $description = strip_tags($description);

    $category = (intval($etsysub3category) > 0) ? $etsysub3category : (intval($etsysub2category > 0) ? $etsysub2category : (intval($etsysub1category > 0) ? $etsysub1category : $etsycategory));
    $params1 = array('state' => 'active', 'description' => $description, 'quantity' => 100, 'shop_section_id' => $etsysection, 'title' => str_replace('"', '\"', stripslashes($title)),
        'taxonomy_id' => $category, 'who_made' => $whomadeit, 'materials' => $etsymaterials, 'tags' => $etsytags, 'is_supply' => $issupply);
    $params2 = array('state' => 'active', 'when_made' => $whenmade, 'shipping_template_id' => $etsyshipping, 'processing_min' => 3, 'processing_max' => 5,
        'has_variations' => true, 'recipient' => $etsyrecipient, 'occasion' => $etsyoccasion, 'style' => $etsystyle);

    if ((int)$weight != 0) {
        $params2["item_weight"] = (int)$weight;
    }

    if ($etsy_product_id == 0) {
        $etsyurl = 'https://openapi.etsy.com/v2/listings';
        $params = array_merge($params1, $params2);
        $params['creation_tsz'] = time();
        $params['original_creation_tsz'] = time();
        $params['price'] = (float)$min_price;
        if (!$min_price) {
            $params['price'] = (float)$etsy_data['price'];
        }
        $body = $params;
        $oauth_method = OAUTH_HTTP_METHOD_POST;
        $action = 'POST';
    } else {
        $etsyurl = 'https://openapi.etsy.com/v2/listings/' . $etsy_product_id;
        $body = $params1;
        $oauth_method = OAUTH_HTTP_METHOD_PUT;
        $action = 'PUT';
    }

    $etsycreate = EtsyApiCall($etsy_auth, $etsyurl, $body, $oauth_method, $action);

    if ($etsy_product_id != 0) {
        $params2['last_modified_tsz'] = time();
        $etsycreate = EtsyApiCall($etsy_auth, $etsyurl, $params2, $oauth_method, 'PUT');
    }
    $data = $etsycreate['response'];
    if ($POST['pagetype'] == 1 && $etsy_product_id == 0) {
        $_SESSION['shops']['etsy_id'] = $data['results'][0]['listing_id'];
    }

    $etsy_id = $data['results'][0]['listing_id'];

    update_product_meta($products_id, 'etsy_id', $etsy_id);
    update_product_meta($products_id, 'etsy_category_id', $etsycategory);
    update_product_meta($products_id, 'etsy_sub1_category_id', $etsysub1category);
    update_product_meta($products_id, 'etsy_sub2_category_id', $etsysub2category);
    update_product_meta($products_id, 'etsy_sub3_category_id', $etsysub3category);
    update_product_meta($products_id, 'etsy_section_id', $etsysection);
    update_product_meta($products_id, 'etsy_shipping_id', $etsyshipping);
    update_product_meta($products_id, 'occasion', $etsyoccasion);
    update_product_meta($products_id, 'etsy_style', $etsystyle);
    update_product_meta($products_id, 'etsy_recipient', $etsyrecipient);
    update_product_meta($products_id, 'whomadeit', $whomadeit);
    update_product_meta($products_id, 'whenmade', $whenmade);
    update_product_meta($products_id, 'issupply', $issupply);
    update_product_meta($products_id, 'etsytags', $etsytags);
    update_product_meta($products_id, 'etsymaterials', $etsymaterials);

    $wpdb->get_result("UPDATE `wp_users_products` SET `etsyactive` = 1  WHERE `id` = $products_id");
    if (isset($etsycreate['status']) && $etsycreate['status'] == 'failed') {
        $errors = array();
        $text = ($POST['pagetype'] == 1 || ($POST['pagetype'] == 2 && $etsy_product_id == 0)) ? 'add' : 'edit';
        $error_title = 'Error ' . $text . ' product in Etsy :';
        if (is_array($etsycreate['errors'])) {
            $errors[] = $etsycreate['errors'];
        }
        if (is_string($etsycreate['errors'])) {
            $errors[] = array($etsycreate['errors']);
        }
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }

    updateEtsyVariants($etsy_auth, $etsy_id, $etsy_variants, $method, $POST = array());
    return $etsy_id;
}

function updateEtsyVariants_v2($etsy_auth, $product_id, $etsy_id, $variants_et, $POST = array())
{
    global $wpdb;
    @extract($etsy_auth);
    $url = "https://openapi.etsy.com/v2/listings/$etsy_id/inventory";
    $auth_method = OAUTH_HTTP_METHOD_PUT;
    $action = 'PUT';

    $params = array(
        "listing_id" => (int)$etsy_id,
        "products" => json_encode($variants_et),
        'price_on_property' => 200,
        'sku_on_property' => 200
    );
    //mail("team@ryankikta.com","etsy variants",var_export($variants_et,true));
    $result = EtsyApiCall($etsy_auth, $url, $params, $auth_method, $action);
    if (isset($result['status']) && $result['status'] == 'failed') {
        @extract($etsy_auth);
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_etsy_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $etsyshop . '"';
        $errors = array();
        $errors[] = $result['errors'];
        $error_title = 'Error ' . $text . ' variants in Etsy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
    $all_meta = array();
    $all_vars = array();
    $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $etsyshopId and meta_key='etsy_id' and product_id=$product_id", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['variant_id']] = $res['id'];
    }

    $variants = $result['response']['results']['products'];

    foreach ($variants as $var) {
        $color_name = $var['property_values'][0]['values'][0];
        $size_name = $var['property_values'][1]['values'][0];

        $colors_id = implode(",", get_colors_col($color_name));
        $sizes_id = implode(",", get_sizes_col($size_name));
        $cs_products = $wpdb->get_results("select id from `wp_users_products_colors` where users_products_id=$product_id and color_id in($colors_id) and size_id in($sizes_id)", ARRAY_A);
        $variantid = $cs_products[0]['id'];
        $_tmp = array('variant_id' => $variantid, 'etsy_id' => $var['product_id']);
        $_tmp['id'] = (isset($all_meta[$variantid])) ? $all_meta[$variantid] : 'NULL';
        $all_vars[] = $_tmp;
    }

    $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
    $_tmp = array();
    foreach ($all_vars as $_var) {
        $_tmp[] = " ({$_var['id']},'$product_id','{$_var['variant_id']}','etsy_id','{$_var['etsy_id']}','$etsyshopId') ";
    }

    $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
    $wpdb->query($sql_var);
}

function updateEtsyVariants($etsy_auth, $etsy_id, $variants_et, $method, $POST = array())
{
    global $wpdb;
    $colorArr = array();
    $sizeArr = array();

    foreach ($variants_et as $v => $variations) {
        if ($variations['property_id'] == 100) {
            $sizeArr[] = $variations['value'];
        }
    }

    foreach ($variants_et as $v => $colorvariations) {
        if ($colorvariations['property_id'] == 200) {
            $colorArr[] = array('property_id' => 200, "value" => $colorvariations['value'], "is_available" => true, "price" => $colorvariations['price']);
        }
    }

    foreach ($sizeArr as $size_name) {
        $fetchSizes = $wpdb->get_row("select size_name,s_ordering from `wp_rmproductmanagement_sizes` where size_name='$size_name'");
        $variants_sizes_temp[] = array('property_id' => 100, "value" => $fetchSizes->size_name, "is_available" => true, "ordering" => $fetchSizes->s_ordering);
    }

    $variants_sizes = array_orderby($variants_sizes_temp, 'ordering', SORT_ASC);

    foreach ($variants_sizes as $k => $v) {
        unset($v['ordering']);
        $variants_sizes[$k] = $v;
    }

    $variants_et = array_merge($colorArr, $variants_sizes);

    $url = "https://openapi.etsy.com/v2/listings/$etsy_id/variations";
    $params = array("listing_id" => $etsy_id, "variations" => json_encode($variants_et), "sizing_scale" => 329);
    if ($method == 'POST') {
        $auth_method = OAUTH_HTTP_METHOD_POST;
        $action = 'POST';
    } else {
        $auth_method = OAUTH_HTTP_METHOD_PUT;
        $action = 'PUT';
    }
    $result = EtsyApiCall($etsy_auth, $url, $params, $auth_method, $action);

    if (isset($result['status']) && $result['status'] == 'failed') {
        @extract($etsy_auth);
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_etsy_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $etsyshop . '"';
        $errors = array();
        $errors[] = $result['errors'];
        $error_title = 'Error ' . $text . ' variants in Etsy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function UploadEtsyStoreImages($POST, $images, $etsy_id, $etsy_auth, $products_id, $remove_old = 0)
{
    $url = "https://openapi.etsy.com/v2/listings/" . $etsy_id . "/images";

    if ($remove_old != 0) {
        DeleteEtsyImage($etsy_id, $etsy_auth, $url);
    }


    $count = count($images);
    if ($count > 5) {
        for ($i = 5; $i < $count; $i++) {
            unset($images[$i]);
        }
    }
    foreach ($images as $key => $image) {
        $position = $key + 1;
        $result = EtsyImageCall(OAUTH_HTTP_METHOD_POST, $etsy_auth, $url, $image['path'], $position);
        if (isset($result['status']) && $result['status'] == 'failed') {
            @extract($etsy_auth);
            $count_shop = check_etsy_shop($user_id, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $etsyshop . '"';
            $errors = array();
            $errors[] = $result['errors'];
            $error_title = 'Error Upload images in Etsy ' . $shop_text . ':';
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        $data = $result['response'];
        $etsy_image_id = $data['results'][0]['listing_image_id'];

        $etsy_image_id = ($etsy_image_id == "") ? 0 : $etsy_image_id;
        $image_id = $image['id'];
        $sql = "update `wp_users_products_images` set `etsy_id`=$etsy_image_id  where `image_id`=$image_id and `users_products_id`=$products_id ";
        $query = $wpdb->get_result($sql);
        if (!$query) {
            $logs = array();
            $logs['sql'] = mysql_escape_string($sql);

            wp_insert_post(array(
                'post_content' => var_export($logs, true),
                'post_title' => esc_sql("adding  etsy product image "),
                'post_status' => 'active',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'
            ));
            //wp_mail('team@ryankikta.com', 'error adding etsy product image', $logs['sql']);
        }
    }
}

function DeleteEtsyImage($etsy_id, $auth, $url)
{
    $result = EtsyApiCall($auth, $url, array(), 'GET');
    $imgs = $result['response']['results'];

    foreach ($imgs as $img) {
        $listing_image_id = $img['listing_image_id'];
        $url1 = "https://openapi.etsy.com/v2/listings/$etsy_id/images/" . $listing_image_id;
        EtsyImageCall(OAUTH_HTTP_METHOD_DELETE, $auth, $url1, null, null);
    }
}

function deleteEtsyProduct($etsy_id, $auth, $productid = 0)
{
    $url = "https://openapi.etsy.com/v2/private/listings/$etsy_id";
    EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_DELETE);
    if ($productid != 0) {
        $wpdb->get_result("UPDATE `wp_users_products` SET `etsyactive` = 0 WHERE `id` = $productid");
        $wpdb->get_result("UPDATE `wp_users_products_colors` SET `etsy_id` = 0 WHERE `users_products_id` = $productid");
        $wpdb->get_result("UPDATE `wp_users_products_images` SET `etsy_id` = 0 WHERE `users_products_id` = $productid");
        delete_product_meta($productid, 'etsy_id');
    }
}

function getEtsyCategoryChildren($auth, $parents = 0)
{
    $url = "https://openapi.etsy.com/v2/taxonomy/seller/get";
    $cats = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    $response = $cats['response']['results'];
    $cat1 = array();
    $cat2 = array();
    $cat3 = array();
    $cat4 = array();
    foreach ($response as $key => $cat) {
        $cat1[$key]['id'] = $cat['id'];
        $cat1[$key]['name'] = $cat['name'];
        if (!empty($cat['children'])) {
            $subcat1 = array();
            foreach ($cat['children'] as $key => $sub1) {
                $subcat1[$key]['id'] = $sub1['id'];
                $subcat1[$key]['name'] = $sub1['name'];
                if ($parents == 1) {
                    $subcat1[$key]['parents'] = $cat['id'];
                }
                if (!empty($sub1['children'])) {
                    $subcat2 = array();
                    foreach ($sub1['children'] as $key => $sub2) {
                        $subcat2[$key]['id'] = $sub2['id'];
                        $subcat2[$key]['name'] = $sub2['name'];
                        if ($parents == 1) {
                            $subcat2[$key]['parents'] = $cat['id'] . ',' . $sub1['id'];
                        }
                        if (!empty($sub2['children'])) {
                            $subcat3 = array();
                            foreach ($sub2['children'] as $key => $sub3) {
                                $subcat3[$key]['id'] = $sub3['id'];
                                $subcat3[$key]['name'] = $sub3['name'];
                                if ($parents == 1) {
                                    $subcat3[$key]['parents'] = $cat['id'] . ',' . $sub1['id'] . ',' . $sub2['id'];
                                }
                            }
                            $cat4[$sub2['id']] = $subcat3;
                        }
                    }
                    $cat3[$sub1['id']] = $subcat2;
                }
            }
            $cat2[$cat['id']] = $subcat1;
        }
    }
    return array($cat1, $cat2, $cat3, $cat4);
}

function GetEtsyCategory($auth, $level = 0, $parents_id = array())
{
    $url = "https://openapi.etsy.com/v2/taxonomy/seller/get";
    $cats = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    $response = $cats['response']['results'];
    if ($level == 0) {
        $return = array();
        foreach ($response as $val) {
            $return[] = array('id' => $val['id'], 'name' => $val['name']);
        }
    } else {
        switch ($level) {
            case 1:
                $child = array();
                foreach ($response as $key => $cat) {
                    if ($cat['id'] == $parents_id[0]) {
                        foreach ($cat["children"] as $key => $sub1) {
                            $child[$key]['id'] = $sub1['id'];
                            $child[$key]['name'] = $sub1['name'];
                        }
                        $return = $child;
                        break;
                    }
                }

                break;

            case 2:
                $child = array();
                foreach ($response as $key => $cat) {
                    if ($cat['id'] == $parents_id[0]) {
                        foreach ($cat["children"] as $key => $sub1) {
                            if ($sub1['id'] == $parents_id[1]) {
                                foreach ($sub1["children"] as $key => $sub2) {
                                    $child[$key]['id'] = $sub2['id'];
                                    $child[$key]['name'] = $sub2['name'];
                                }
                                break;
                            }
                        }
                        $return = $child;
                        break;
                    }
                }
                break;

            case 3:
                $child = array();
                foreach ($response as $key => $cat) {
                    if ($cat['id'] == $parents_id[0]) {
                        foreach ($cat["children"] as $key => $sub1) {
                            if ($sub1['id'] == $parents_id[1]) {
                                foreach ($sub1["children"] as $key => $sub2) {
                                    if ($sub2['id'] == $parents_id[2]) {
                                        foreach ($sub2["children"] as $key => $sub3) {
                                            $child[$key]['id'] = $sub3['id'];
                                            $child[$key]['name'] = $sub3['name'];
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                        $return = $child;
                        break;
                    }
                }
                break;

            default:
                break;
        }
    }
    return $return;
}

function GetEtsyShops($auth)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/shops";
    $data = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    debug($data);
    exit();
    return $data;
}

function GetAllCountries($auth)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/countries";
    $countries = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    return $countries['response']['results'];
}

function GetCountriesByIso($auth, $iso_code)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/countries/iso/$iso_code";
    $countries = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    return $countries['response']['results'][0];
}

function GetEtsySection($auth)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/shops/$etsyshop/sections";
    $sections = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    return $sections['response']['results'];
}

function CreateShippingTemplate($auth, $iso_code, $primary_cost, $secondary_cost)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/shipping/templates";
    $country = GetCountriesByIso($auth, $iso_code);
    $country_id = $country["country_id"];

    $data = array(
        'title' => "RyanKikta Shipping",
        'origin_country_id' => $country_id,
        'destination_country_id' => $country_id,
        'primary_cost' => $primary_cost,
        'secondary_cost' => $secondary_cost,
        'min_processing_days' => 3,
        'max_processing_days' => 5,
    );
    $shipping = EtsyApiCall($auth, $url, $data, OAUTH_HTTP_METHOD_POST, 'POST');

    return $shipping;
}

function CreateShippingTemplateEntry($auth, $shipping_template_id, $iso_code, $primary_cost, $secondary_cost)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/shipping/templates/entries";

    $country = GetCountriesByIso($auth, $iso_code);
    $country_id = $country["country_id"];

    $data = array(
        'shipping_template_id' => $shipping_template_id,
        'destination_country_id' => $country_id,
        'primary_cost' => $primary_cost,
        'secondary_cost' => $secondary_cost,
    );
    $shipping = EtsyApiCall($auth, $url, $data, OAUTH_HTTP_METHOD_POST, 'POST');

    return $shipping;
}

function GetEtsyShippingTemplate($auth)
{
    @extract($auth);
    $url = "https://openapi.etsy.com/v2/users/__SELF__/shipping/templates";
    $shippings = EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_GET);
    return $shippings['response']['results'];
}

function synchronize_etsy($etsy_id, $user_id, $option_name, $desactivate)
{
    $variants = array();
    $variantsToSend = array();
    $etsy_auth = getEtsyShop($user_id);
    @extract($etsy_auth);
    if ($desactivate) {
        $params = array('state' => 'inactive');
        $url = "https://openapi.etsy.com/v2/listings/$etsy_id";
        $result = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_PUT);
    } else {
        $url = "https://openapi.etsy.com/v2/listings/$etsy_id/variations";
        $result = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET);
        if ($result['status'] == 'success') {
            $options = $result['response']['results'];
            foreach ($options as $op) {
                if ($op['property_id'] == 200 || $op['property_id'] == 100) {
                    foreach ($op['options'] as $p) {
                        $variants[] = array_merge($p, array('property_id' => $op['property_id']));
                    }
                }
            }

            foreach ($variants as $v) {
                if (strtoupper($v['value']) != strtoupper($option_name)) {
                    $variantsToSend[] = array('property_id' => $v['property_id'], 'value' => $v['value'], 'is_available' => true, 'price' => $v['price']);
                }
            }
            if (!empty($variantsToSend)) {
                updateEtsyVariants($etsy_auth, $etsy_id, $variantsToSend, 'PUT');
            }
        }
    }
}

function etsy_call_api($post)
{
    $app_access = array('url_file' => $post['url_file'], 'consumer_key' => $post['consumer_key'], 'consumer_secret' => $post['consumer_secret']);
    $url_file = $app_access['url_file'];
    $url_file = rtrim($url_file, '/');
    $post['app_oauth'] = base64_encode(json_encode($app_access));
    $ch = curl_init($url_file);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    etsy_limit_call($post['user_id']);
    if ($post['call_type'] != 1) {
        etsy_limit_call($post['user_id']);
    }

    $return = curl_exec($ch);
    $data = object_to_array(json_decode(trim($return), true));
    curl_close($ch);
    return $data;
}

function test_call_limit_etsy($shop_id, $user_id)
{
    global $wpdb;
    $auth = getEtsyShopById($user_id, $shop_id);
    @extract($auth);
    $total_active = 0;
    $all_resp_active = array();
    $resp_active = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active?includes=Images&limit=100&page=1", null, OAUTH_HTTP_METHOD_GET);

    if (isset($resp_active['status']) && $resp_active['status'] == "failed") {
        return array("error" => $resp_active['errors']);
    }
    $all_resp_active = $resp_active;
    $total_active = $resp_active["response"]["count"];
    $k = 2;

    while (count($all_resp_active) < $total_active & $k < 12) {
        $resp_active = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active?includes=Images&limit=100&page=" . $k, null, OAUTH_HTTP_METHOD_GET);
        if (isset($resp_active['status']) && $resp_active['status'] != "failed") {
            $all_resp_active = array_merge($resp_active, $all_resp_active);
        } else {
            break;
        }
        $k++;
    }

    $resp_inactive = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/inactive?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_inactive['status']) && $resp_inactive['status'] == "failed") {
        return array("error" => $resp_inactive['errors']);
    }

    $resp_expired = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/expired?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_expired['status']) && $resp_expired['status'] == "failed") {
        return array("error" => $resp_expired['errors']);
    }

    $resp_draft = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/draft?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_draft['status']) && $resp_draft['status'] == "failed") {
        return array("error" => $resp_draft['errors']);
    }

    $resp = array($all_resp_active, $resp_inactive, $resp_expired, $resp_draft);

    return $resp;
}

function EtsyApiCall($auth, $url, $body, $oauth_method, $action = 'GET', $script = 2)
{

	//wp_mail('jbuck@ryankikta.com', 'etsy api call', var_export($auth, true));
    if ($auth['version'] == 3 && $script == 1) {
        $tmp = $auth;

        $auth = base64_encode(json_encode($auth, true));
        $body = base64_encode(json_encode($body, true));


        $post = array('auth' => $auth, 'url' => $url, 'data' => $body, 'method' => $oauth_method, 'action' => $action, 'call_type' => 1, 'url_file' => $tmp['url_file'], 'consumer_key' => $tmp['consumer_key'], 'consumer_secret' => $tmp['consumer_secret'], 'user_id' => $tmp['user_id']);
        $response = etsy_call_api($post);
        return $response;
    } else {
        @extract($auth);
        $oauth = new OAuth($consumer_key, $consumer_secret);
        $oauth->enableDebug();
        $oauth->setToken($etsytoken, $etsysecret);
        try {
            if ($action == 'POST' || $action == 'PUT') {
                $oauth->setAuthType(OAUTH_AUTH_TYPE_FORM);
            }

            $oauth->fetch($url, $body, $oauth_method);
            $json = $oauth->getLastResponse();

            $data = json_decode($json, true);

            $result = array(
                'status' => 'success',
                'response' => $data
            );
        } catch (OAuthException $e) {
            //EtsyApiCall($auth, $url, $body, $oauth_method, $action , 1);
            $errors1 = array();
            $errors = explode("\n", $oauth->getLastResponseHeaders());
            foreach ($errors as $error) {
                if (strpos($error, "X-Error-Detail") !== false) {
                    $errors1[] = trim(str_replace("X-Error-Detail:", "", $error));
                }
            }
            $result = array(
                'status' => 'failed',
                'errors' => $errors1,
                'catch_errors' => $errors,
                'code' => $e->getCode()
            );
        }
        return $result;
    }
}

function EtsyImageCall($method, $auth, $url, $file, $position)
{
    $call = 1;
    if ($call == 1) {
        $tmp = $auth;
        $auth = base64_encode(json_encode($auth));
        $post = array('auth' => $auth, 'url' => $url, 'method' => $method, 'position' => $position, 'call_type' => 1, 'url_file' => $tmp['url_file'], 'consumer_key' => $tmp['consumer_key'], 'consumer_secret' => $tmp['consumer_secret'], 'user_id' => $tmp['user_id']);
        if ($file != null) {
            $post['file'] = '@' . $file;
            $post['call_type'] = 2;
        }
        $response = etsy_call_api($post);
        return $response;
    } else {
        @extract($auth);
        $oauth1 = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
        $oauth1->enableDebug();
        $detelet_img = false;
        try {
            $oauth1->setToken($etsytoken, $etsysecret);
            if ($file !== null) {
                $image_type = image_type_to_mime_type(exif_imagetype($file));
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext == "png") {
                    $name_image = basename($file, '.png');
                    $file = create_imgpng($file, $name_image);
                    $detelet_img = true;
                }
                $params = array('@image' => '@' . $file, "rank" => $position);
                usleep(300);
                $oauth1->fetch($url, $params, $method);
            } else {
                $oauth1->fetch($url, null, $method);
            }
            $json = $oauth1->getLastResponse();
            if ($detelet_img) {
                @unlink($file);
            }
            $response = json_decode($json, true);
            $result = array(
                'status' => 'success',
                'response' => $response
            );
        } catch (OAuthException $e) {
            if ($file !== null) {
                $tmp = $oauth1->debugInfo;
                $ch = curl_init();
                $files_data = array('image' => "@$file", 'rank' => $position);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    $tmp['headers_sent'],
                    'MIME-Version: 1.0',
                    'Host: openapi.etsy.com',
                    'X-Target-URI:  https://openapi.etsy.com',
                    'Connection:    Keep-Alive',
                ));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $files_data);
                etsy_limit_call();
                $json = curl_exec($ch);
                if (curl_errno($ch)) {
                    $error = 'Curl Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
                    $result = array(
                        'status' => 'failed',
                        'errors' => $error
                    );
                } else {
                    $response = json_decode($json, true);
                    $result = array(
                        'status' => 'success',
                        'response' => $response
                    );
                }
            } else {
                $errors = explode("\n", $oauth1->getLastResponseHeaders());
                foreach ($errors as $error) {
                    if (strpos($error, "X-Error-Detail") !== false) {
                        $errors1[] = trim(str_replace("X-Error-Detail:", "", $error));
                    }
                }
                $result = array(
                    'status' => 'failed',
                    'errors' => $errors1
                );
            }
            if ($detelet_img) {
                @unlink($file);
            }
        }
        return $result;
    }
}

/********************************************Orders Functions**************************************************/

function get_order_etsy_data_v2($etsy_auth, $order)
{
    $order_id = $order["receipt_id"];
    $url = "https://openapi.etsy.com/v2/private/receipts/$order_id/transactions";
    usleep(500000);
    $return = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET, 'GET');
    if ($order_id == 1262295502) {
        wp_mail('team@ryankikta.com', 'orderetsy', var_export($return, true));
    }

    //if($order_id==1253165357)
    //  wp_mail('team@ryankikta.com','transaction 1',var_export( $return,true));
    if ($return['status'] == 'success') {
        $data = $return['response']['results'];
    } else {
        $data = array();
    }
    return array('order_id' => $order_id, 'email' => $order["buyer_email"], 'customerphone' => '', 'itemsinfo' => $data, 'order_data' => $order);
}

function etsy_shipping_address_v2($etsy_auth, $order)
{
    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = $order['name'];
    $shippingaddress1['address1'] = $order['first_line'];
    $shippingaddress1['address2'] = $order['second_line'];
    $shippingaddress1['city'] = $order['city'];
    $shippingaddress1['state'] = $order['state'];
    $shippingaddress1['zipcode'] = $order['zip'];
    $country_id = $order['country_id'];
    $url = "https://openapi.etsy.com/v2/countries/" . $country_id;
    $return = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET, 'GET');
    $country_name = $return['response']['results'][0]['name'];
    $country_code = $return['response']['results'][0]['iso_country_code'];
    $shippingaddress1['country'] = $country_code;
    $address2 = ($order['second_line'] != "") ? $order['second_line'] . "\n" : "";
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $country_name;
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);

    if ($country_code == "US") {
        $shipping_id = 1;
    } elseif ($country_code == "CA") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }

    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $country_code, 'shippingaddress_state' => $order['state'], 'shippingaddress_state_code' => $country_code, 'shippingaddress_zip' => $order['zip'], 'shipping_id' => $shipping_id, 'paypal_address' => $paypal_address);
}

function get_color_size_variant($product_id, $option, $only_color_name = "")
{
    global $wpdb;
    //debug($product_id);
    $variant1 = $option[0];
    $variant2 = $option[1];
    $sizes_name = array();
    $sizes_name = array();
    if (empty($option)) {
        return array('color_id' => 0, "size_id" => 0);
    }
    if ($variant1['formatted_name'] == 'Color' || $variant1['formatted_name'] == 'Color of Shirt' || $variant1['formatted_name'] == 'Primary color') {
        $color_name = $variant1['formatted_value'];
        $size_name = $variant2['formatted_value'];
    } else {
        $color_name = $variant2['formatted_value'];
        $size_name = $variant1['formatted_value'];
    }
    // echo 'size';debug($size_name);
    $color_name = ($color_name == "Gray" && in_array($product_id, array(264063, 305695))) ? 'Tri-Vintage Grey' : $color_name;
    $color_name = ($color_name == "Black" && in_array($product_id, array(305695))) ? 'Tri-Onyx' : $color_name;
    $color_name = ($color_name == "Green" && in_array($product_id, array(305695))) ? 'Tri Kelly' : $color_name;
    if ($size_name == "S US Kids&#039;") {
        $sizes_name[] = "YS";
    }
    //M US Women's
    if ($size_name == "M US Women's") {
        $sizes_name[] = "Medium";
    }
    if ($size_name == "M US Women&#039;s") {
        $sizes_name[] = "Medium";
    }
    $size_name = trim(str_replace(array("Men's", "Women&#039;s", "Men&#039;s", "Men&#039;s", "US Men&#039;s", "US Women&#039;s", " US Women&#039;s", "US Kids&#039;"), array("", "", "", "", "", "", ""), $size_name));

    $size_name = trim(str_replace("US", "", $size_name));
    $sizes_name [] = $size_name;

    if ($size_name == "S US Kids&#039;") {
        $sizes_name[] = "YS";
    }


    if ($color_name == null) {
        $color_name = $only_color_name;
    }
    //echo 'color name';debug($color_name );
    $pos = strpos($size_name, " - ");
    if ($pos !== false) {
        $size_name = substr_replace($size_name, "", $pos);
    }
    $size_name = str_replace(array("XL", "xl", "2XL", "2xl", "3XL", "3xl", "4XL", "4xl", "5XL", "5xl"), array("X-Large", "X-Large", "2X-Large", "2X-Large", "3X-Large", "3X-Large", "4X-Large", "4X-Large", "5X-Large", "5X-Large"), $size_name);
    if ($size_name == "l" || $size_name == "L") {
        $size_name = "Large";
    }

    if ($size_name == "M" || $size_name == "m") {
        $size_name = "Medium";
    }
    if ($size_name == "S" || $size_name == "s") {
        $size_name = "Small";
    }

    $sizes_name[] = $size_name;
    //cho 'size name 1';debug($size_name);
    $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $color_name . '"');
    //echo 'colros is';debug($colors_id);
    $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"');
    //echo 'all sizes';debug($sizes_id);
    if (!$sizes_id) {
        $size_explode = explode(" ", $size_name);
        foreach ($size_explode as $name) {
            $sizes_id = get_size_id($name);
            if ($sizes_id) {
                $size_name = $name;
                break;
            }
        }
    }
    if (empty($colors_id)) {
        //    echo 'check all colors\r\n';
        $colors_id = array();
        $color_explode = explode(" ", $color_name);
        //debug($color_explode);
        foreach ($color_explode as $name) {
            $all_colors_id = get_colors_id(trim($name));
            //debug( $all_colors_id);
            if ($all_colors_id) {
                $colors_id = array_merge($colors_id, $all_colors_id);
            }
        }
    }
    $sizes_name[] = $size_name;
    $sizes_name = array_unique($sizes_name);
    // echo 'size name';debug($sizes_name);
    $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name in ("' . implode('", "', $sizes_name) . '")');
    //echo 'all sizes id';debug($sizes_id);
    // echo 'product id';debug($product_id );
    // debug($colors_id);
    // echo 'color/size id';  debug(array('color_id' => $color_id, 'size_id' => $size_id));
    foreach ($colors_id as $color) {
        $color_id = $color->color_id;
        foreach ($sizes_id as $size) {
            $size_id = $size->size_id;
            $count_item = $wpdb->get_var("select count(id) from wp_users_products_colors where users_products_id=$product_id and color_id=$color_id and size_id=$size_id");
            if ($count_item > 0) {
                break 2;
            }
        }
    }
    // echo 'color/size id';  debug(array('color_id' => $color_id, 'size_id' => $size_id));

    if ($count_item <= 0 /*&& !in_array($pa_product_id,array())*/) {
        $color_id = $size_id = 0;
    }
    if (count($colors_id) == 1) {
        $color_id = $colors_id[0]->color_id;
    }
    if (count($sizes_id) == 1) {
        $size_id = $sizes_id[0]->size_id;
    }

    return array('color_id' => $color_id, 'size_id' => $size_id);
}

function get_color_size_variant_v2($product_id, $option, $only_color_name = "")
{
    global $wpdb;
    //wp_mail('team@ryankikta.com','options',var_export(array($product_id,$option),true));
    $variant1 = $option['property_values'][0];
    $variant2 = $option['property_values'][1];
    $sizes_name = array();
    if ($variant1['property_name'] == 'Color' || $variant1['property_name'] == 'Color of Shirt' || $variant1['property_name'] == 'Primary color') {
        $color_name = $variant1['values'][0];
        $size_name = $variant2['values'][0];
    } else {
        $color_name = $variant2['values'][0];
        $size_name = $variant1['values'][0];
    }
    $sizes_name [] = $size_name;
    if ($color_name == null) {
        $color_name = $only_color_name;
    }
    //echo 'color name';debug($color_name );
    $pos = strpos($size_name, " - ");
    if ($pos !== false) {
        $size_name = substr_replace($size_name, "", $pos);
    }
    $size_name = trim(str_replace(array("Men's", "Women&#039;s", "Men&#039;s", "Men&#039;s", "US Men&#039;s", "US Women&#039;s", "US Kids&#039;"), array("", "", "", "", "", "", ""), $size_name));
    $size_name = trim(str_replace("US", "", $size_name));
    $size_name = str_replace(array("XL", "xl", "2XL", "2xl", "3XL", "3xl", "4XL", "4xl", "5XL", "5xl"), array("X-Large", "X-Large", "2X-Large", "2X-Large", "3X-Large", "3X-Large", "4X-Large", "4X-Large", "5X-Large", "5X-Large"), $size_name);
    if ($size_name == "l" || $size_name == "L") {
        $size_name = "Large";
    }

    if ($size_name == "M" || $size_name == "m") {
        $size_name = "Medium";
    }
    if ($size_name == "S" || $size_name == "s") {
        $size_name = "Small";
    }
    if ($size_name == "S US Kids&#039;") {
        $sizes_name[] = "YS";
    }

    $sizes_name[] = $size_name;
    //$color_name = ($color_name=="Gray") ? 'Baby Blue' : $color_name;
    /*$color_name = ($color_name=="Green") ? 'Leaf' : $color_name;
    $color_name = ($color_name=="Blue") ? 'Athletic Heather' : $color_name;*/


    //wp_mail('team@ryankikta.com','sql color','select color_id from wp_rmproductmanagement_colors where color_name like"%' . $color_name . '"%');
    $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name like "%' . $color_name . '%"');
    //wp_mail('team@ryankikta.com','colors',var_export(  $colors_id ,true));
    $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"');
    if (!$sizes_id) {
        $size_explode = explode(" ", $size_name);
        foreach ($size_explode as $name) {
            $sizes_id = get_size_id($name);
            if ($sizes_id) {
                $size_name = $name;
                break;
            }
        }
    }
    $sizes_name[] = $size_name;
    $sizes_name = array_unique($sizes_name);
    //echo 'size name';debug($sizes_name);
    $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name in ("' . implode('", "', $sizes_name) . '")');
    foreach ($colors_id as $color) {
        $color_id = $color->color_id;
        foreach ($sizes_id as $size) {
            $size_id = $size->size_id;
            $count_item = $wpdb->get_var("select count(id) from wp_users_products_colors where users_products_id=$product_id and color_id=$color_id and size_id=$size_id");
            if ($count_item > 0) {
                break 2;
            }
        }
    }
    //debug($product_id);
    //  echo 'color/size id';  debug(array('color_id' => $color_id, 'size_id' => $size_id));
    if ($count_item <= 0) {
        $color_id = $size_id = 0;
    }
    if (count($colors_id) == 1) {
        $color_id = $colors_id[0]->color_id;
    }
    if (count($sizes_id) == 1) {
        $size_id = $sizes_id[0]->size_id;
    }
    //echo 'color/size id';  debug(array('color_id' => $color_id, 'size_id' => $size_id));
    return array('color_id' => $color_id, 'size_id' => $size_id);
}


function get_allitem_etsy_v2($itemsinfo, $user_id, $shop_id)
{
    global $wpdb;
    $items = array();
    foreach ($itemsinfo as $key => $value) {
        // debug($value);
        $product = array();
        $etsy_id = $wpdb->escape($value['listing_id']);
        $item_id = $wpdb->escape($value['transaction_id']);
        $item_price = $value['price'];
        $quantity = $value['quantity'];
        $product = $wpdb->get_results("select pm.product_id as product_id,up.`product_id` as inventory_id,up.`brand_id`,up.`front`,up.`back` from wp_products_meta as pm INNER JOIN wp_users_products as up on pm.product_id = up.id where pm.meta_value = $etsy_id and pm.meta_key = 'etsy_id' and pm.shopid = $shop_id and up.users_id =" . $user_id, ARRAY_A);
        //  echo 'product 1 ';debug( $product );
        if (!$product) {
            $product = $wpdb->get_results("select pm.product_id as product_id,up.`product_id` as inventory_id,up.`brand_id`,up.`front`,up.`back` from wp_products_meta as pm INNER JOIN wp_users_products as up on pm.product_id = up.id where pm.meta_value = $etsy_id and pm.meta_key = 'etsy_id' and up.users_id =" . $user_id, ARRAY_A);
        }
        //  echo 'product 2';debug( $product );

        if ($product) {
            $product = end($product);
            $pa_product_id = $product['product_id'];
            $inventory_id = $product['inventory_id'];
            $brand_id = $product['brand_id'];
            $hasfront = $product['front'];
            $hasback = $product['back'];
            if ($pa_product_id > 0) {
                $color_name = "";
                $prod_colors = $wpdb->get_results("select distinct(color_id) from wp_users_products_colors where users_products_id=$pa_product_id", ARRAY_A);
                if (count($prod_colors) == 1) {
                    $color_id = $prod_colors[0]['color_id'];
                    $color_name = $wpdb->get_var('select color_name from wp_rmproductmanagement_colors where color_id="' . $color_id . '"');
                }
                $color_size = get_color_size_variant($pa_product_id, $value['variations'], $color_name);
                @extract($color_size);

                //wp_mail('team@ryankikta.com','color-sizes',var_export($color_size,true));
                if ($size_id == 0 && $color_id == 0) {
                    $color_size = get_color_size_variant_v2($pa_product_id, $value['product_data'], $color_name);
                    // wp_mail('team@ryankikta.com','color-sizes1',var_export($color_size,true));
                    @extract($color_size);
                }


                if ($size_id > 0 || $color_id > 0) {
                    $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
            }
        } else {
            $pa_product_id = get_product_id_meta_shop($etsy_id, "etsy_id", $shop_id);
            if (!$pa_product_id) {
                $pa_product_id = get_product_id_meta_shop($etsy_id, "etsy_id", 0);
            }
            $color_name = "";
            $prod_colors = $wpdb->get_results("select distinct(color_id) from wp_users_products_colors where users_products_id=$pa_product_id", ARRAY_A);
            if (count($prod_colors) == 1) {
                $color_id = $prod_colors[0]['color_id'];
                $color_name = $wpdb->get_var('select color_name from wp_rmproductmanagement_colors where color_id="' . $color_id . '"');
            }
            $color_size = get_color_size_variant($pa_product_id, $value['variations'], $color_name);
            @extract($color_size);
            $products_fb = $wpdb->get_results("select type from wp_users_products_images where type<>4 and users_products_id=$pa_product_id order by type asc");
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

function get_order_status_etsy($user_id, $order_id)
{
    $is_shipped = false;
    $etsy_auth = getEtsyShop($user_id);
    $url = "https://openapi.etsy.com/v2/private/receipts/" . $order_id;
    $response = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET, 'GET');
    if ($response['status'] == 'success') {
        $is_shipped = $response["response"]["results"][0]["was_shipped"];
    }
    return $is_shipped;
}

function get_etsy_order($order_id, $user_id, $shop_id = 0)
{
    $is_shipped = false;
    $etsy_auth = ($shop_id != 0) ? getEtsyShopById($user_id, $shop_id) : getEtsyShop($user_id);
    $url = "https://openapi.etsy.com/v2/private/receipts/" . $order_id;
    $response = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET, 'GET');
    return $response["response"];
}

function regenerate_etsy_order($order_id, $user_id, $shop_id, $send_type = 0)
{
    global $wpdb;
    $source = 5;
    $etsy_auth = ($shop_id != 0) ? getEtsyShopById($user_id, $shop_id) : getEtsyShop($user_id);
    @extract($etsy_auth);
    $user_orig = $user_id;
    $url = "https://openapi.etsy.com/v2/private/receipts/" . $order_id;
    $params = array('limit' => 50, 'was_paid' => 'true', 'was_shipped' => 'false');
    $res = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_GET, 'GET');
    if ($res['status'] == 'success' && !empty($res['response']['results'])) {
        $receipt = $res['response']['results'][0];
        $order_id = $receipt['receipt_id'];
        $chekexistorder = check_exist_order($order_id, $user_id, $source, $etsyshop);
        if ($send_type == 1) {
            $chekexistorder = false;
        }
        if ($chekexistorder) {
            return array('status' => 'failed', 'message' => 'Order already exists in PA System');
        }

        $order_data = get_order_etsy_data_v2($etsy_auth, $receipt);
        @extract($order_data);
        mail("team@ryankikta.com", "itemsinfo raw etsy", var_export($itemsinfo, true));
        $allitems = get_allitem_etsy_v2($itemsinfo, $user_id, $shop_id);
        if (empty($allitems)) {
            return array('status' => 'failed', 'message' => 'No Product related to ryankikta for this order');
        }
        // exit;
        $ship_address = etsy_shipping_address_v2($etsy_auth, $receipt);
        @extract($ship_address);
        if (count($allitems) == 1 && $allitems[0]['only_shirts'] && $allitems[0]['quantity'] == 1 && in_array(strtolower($shippingaddress_country), american_country())) {
            $firstclass = first_class($user_id);
            if ($firstclass == 1) {
                $shipping_id = 4;
            }
        }
        $order_rush = 1;
        foreach ($allitems as $item) {
            $eligible_rush = $wpdb->get_var("select rush from wp_rmproductmanagement where inventory_id=" . $item['product_id']);
            if ($eligible_rush == 0) {
                $order_rush = 0;
                break;
            }
        }
        $shop_order_name = $order_id;
        $orderinfos = array('source' => $source, 'shop_order_id' => $order_id, 'shop_order_name' => $shop_order_name, 'customerphone' => $customerphone, 'shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shipping_id' => $shipping_id, 'shop' => $etsyshop, "shop_id" => $shop_id, 'json' => $order_data);
        $get_brands = setting_brand($user_id, $order_rush);
        @extract($get_brands);
        if ($send_type == 1) {
            $user_id = 479;
        }
        $pa_order_id = insert_order($user_id, $get_brands, $orderinfos);
        if ($send_type == 1) {
            $user_id = $user_orig;
        }
        $all_shippings_data = insert_orders_details($allitems, $user_id, $pa_order_id, $orderinfos, $get_brands, $source);
        @extract($all_shippings_data);
        $totaltotalprice = calculate_newyork_tax($user_id, $pa_order_id, $shippingaddress_country, $shippingaddress_state, $shippingaddress_state_code, $totaltotalprice);
        $shipping_price = calulate_new_shipping($all_shippings);
        if ($send_type == 1) {
            $user_id = 479;
        }
        $totaltotalprice = $totaltotalprice + $shipping_price;
        $check_all_items_active = check_all_items_active($allitems);
        $check_all_products_exist = check_all_products_exist($allitems);
        if ($check_all_items_active && $check_all_products_exist) {
            $balance = auto_payment($user_id, $totaltotalprice, $pa_order_id, $paypal_address);
        }
        $ret = update_order_informations($user_id, $totaltotalprice, $balance, $shipping_price, $pa_order_id, $youremail, $shop_order_name, $check_all_items_active, $check_all_products_exist);
        // if($send_type == 1){
        @extract($ret);
        $wpdb->get_result("update wp_rmproductmanagement_orders set user_org=$user_orig where order_id=$pa_order_id");
        $return = array(
            'status' => 'success',
            'User ID' => $user_id,
            'User ID Orig' => $user_orig,
            'Order id' => $pa_order_id,
            'Etsy Order ID' => $order_id,
            'Order Status' => $status,
            'Total Price' => "$" . $totaltotalprice,
            'New Balance' => "$" . $balance,
            'Order Url' => "<a href='/wp-admin/admin.php?page=inventory-orders&amp;action=edit&amp;order_id=$pa_order_id' target='_blanc'>
        Click here to open this order</a>",
        );
        return $return;
        // }
    } else {
        $return = array('status' => 'failed', 'message' => 'Order NOT Found in Etsy Shop');
    }
    return $return;
}

function update_order_status_etsy($user_id, $order_id, $is_shipped = 1)
{
    $return = array();
    $etsy_auth = getEtsyShop($user_id);
    $url = "https://openapi.etsy.com/v2/private/receipts/$order_id";
    $params = array('was_shipped' => $is_shipped);
    $response = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_PUT, 'PUT');
    if ($response['status'] == 'success') {
        $return = $response['response']['results'];
    }
    return $return;
}

function get_etsy_orders($user_id)
{
    require_once('product-functions.php');
    require_once('order-functions.php');
    global $wpdb;

    $source = 5;
    $last_time = strtotime('-12 hour');
    //$etsyuser= $wpdb->get_row("select * from wp_users_etsy where users_id = $user_id");
    //$user_id   = $etsyuser['users_id'];
    $etsy_auth = getEtsyShop($user_id);
    @extract($etsy_auth);
    $url = "https://openapi.etsy.com/v2/private/shops/$etsyshop/receipts";
    $params = array('limit' => 20, 'was_paid' => 'true', 'was_shipped' => 'false', 'min_created' => $last_time);
    $return = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_GET, 'GET');
    if ($return['status'] == 'success' && !empty($return['response']['results'])) {
        $data = $return['response'];
        if ($data['count'] != 0) {
            foreach ($data['results'] as $receipt) {
                if (!empty($receipt)) {
                    $totaltotalprice = 0;
                    $order_id = $receipt['receipt_id'];
                    echo $order_id . '<br />';
                    $chekexistorder = check_exist_order($order_id, $user_id, $source, $etsyshop);
                    echo 'order exists ' . $chekexistorder . '<br />';
                    $order_data = get_order_etsy_data_v2($etsy_auth, $receipt);
                    @extract($order_data);
                    $allitems = get_allitem_etsy_v2($itemsinfo, $user_id);
                }
            }
        }
    }
}

/*******************************Etsy API Functions**************************************************************/

function CheckEtsy($user_id)
{
    global $wpdb, $oauth;

    $etsyuser = $wpdb->get_results("SELECT `token`,`secret`,`active`,`shop` FROM `wp_users_etsy` WHERE `users_id` = $user_id", ARRAY_A);


    if (!empty($etsyuser)) {
        $etsytoken = $etsyuser[0]['token'];
        $etsysecret = $etsyuser[0]['secret'];
        $etsyshop = $etsyuser[0]['shop'];
        $active = $etsyuser[0]['active'];

        if ($active != 0) {
            $oauth->setToken($etsytoken, $etsysecret);
            $url = "https://openapi.etsy.com/v2/countries";
            try {
                $oauth->fetch($url);

                $json = $oauth->getLastResponse();
                $data = json_decode($json, true);
                if (!empty($data)) {
                    return 1;
                }
            } catch (OAuthException $e) {
                $error = getBetweenStr($oauth->getLastResponseHeaders(), 'X-Error-Detail:', "X-Etsy-Request-Uuid");
                $user_email = $wpdb->get_var("SELECT `user_email` FROM `wp_users` WHERE `ID` = $user_id");
                $headers = 'From: Ryan Kikta <team@ryankikta.com>' . "\r\n" .
                    'Reply-To: Ryan Kikta <team@ryankikta.com>' . "\r\n";
                wp_mail($user_email, 'Please re-authorize your Etsy shop at RyanKikta', "Just a heads up , We have noticed that RyanKikta is no longer Authorized to access your Etsy Shop  as a result, We have temporarily disabled your shop which means products ordered meanwhile from your shop will not be processed, \n\n You may re-authorize your shop by visiting https://ryankikta.com/etsy/ \n\n Let us know if you have any questions, \n Ryan Kikta Team", $headers);
                return 2;
            }
        }
    }
    return 0;
}

function buildetsyBaseString($baseURI, $method, $params)
{
    $r = array();
    ksort($params);
    foreach ($params as $key => $value) {
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

function buildAuthorizationHeader($oauth)
{
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach ($oauth as $key => $value) {
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    }
    $r .= implode(', ', $values);
    return $r;
}

if (!function_exists('object_to_array')) {
    function object_to_array($obj)
    {
        if (is_object($obj)) {
            $obj = (array)$obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = object_to_array($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }
}

function etsy_limit_call($user_id = "")
{
    $now = time();
    $etsy_call_time = get_user_meta($user_id, 'etsy_call_time', true);
    if (($now - $etsy_call_time) < 1) {
        $etsy_call_rate = get_user_meta($user_id, 'etsy_call_time', true);
        if ($etsy_call_rate == 5) {
            wait(1);
            $now = time();
            update_user_meta($user_id, 'etsy_call_time', $now);
            update_user_meta($user_id, 'etsy_call_rate', 1);
        } else {
            $etsy_call_rate++;
            update_user_meta($user_id, 'etsy_call_rate', $etsy_call_rate);
        }
    } else {
        update_user_meta($user_id, 'etsy_call_time', $now);
        update_user_meta($user_id, 'etsy_call_rate', 1);
    }
}

function fix_missing_etsy_products($user_id)
{
    global $wpdb;
    $mydb = new wpdb('nuvane_wrdp1', 'v[8KQEcOe2EQ', 'nuvane_wrdp1', '35.184.141.59');

    $current_prds = $wpdb->get_col("select distinct(id) from wp_users_products where users_id = $user_id");
    //echo 'total current products '.$current_prds.'<br />';
    $old_prds = $mydb->get_col("select distinct(id) from wp_users_products where users_id = $user_id");
    //echo 'total old products '.$old_prds.'<br />';
    $non_existant = array_diff($old_prds, $current_prds);
    //debug($non_existant);
    $etsy_auth = getEtsyShop($user_id);
    extract($etsy_auth);
    foreach ($non_existant as $user_product_id) {
        $etsy_pr_id = $mydb->get_var("select meta_value from wp_products_meta where product_id = $user_product_id and meta_key='etsy_id' limit 1");
        debug($etsy_pr_id);

        $product = $mydb->get_row("select * from wp_users_products where id = $user_product_id", ARRAY_A);

        $sql = "insert into wp_users_products ";
        $_values = array();
        $fields = array();
        foreach ($product as $field => $val) {
            if ($field == 'title') {
                echo $val . '<br />';
            }
            $fields[] = $field;
            $_values[] = "'" . $wpdb->escape($val) . "'";
        }
        $sql .= "( " . implode(",", $fields) . ") values( " . implode(",", $_values) . " )";
        debug($wpdb->query($sql));
        //echo $sql ;//exit;
        //debug($wpdb->get_results("select * from wp_users_products_colors where users_products_id = $user_product_id"));
        //debug($mydb->get_results("select * from wp_users_products_colors where users_products_id = $user_product_id"));
        //exit;
        //$url="https://openapi.etsy.com/v2/private/listings/$etsy_pr_id";
        //$params=array('limit'=>50,'was_paid'=>'true','was_shipped'=>'false','min_created'=>$last_time);
        // $return = EtsyApiCall($etsy_auth,$url,array(),OAUTH_HTTP_METHOD_GET,'GET');
        //debug($return);exit;
        // exit;
    }
}

function get_all_etsy_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $auth = getEtsyShop($user_id);

    $resp_active = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/" . $auth['etsyshop'] . "/listings/active?" . $auth['consumer_key'], null, OAUTH_HTTP_METHOD_GET);
    $resp_inactive = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/" . $auth['etsyshop'] . "/listings/inactive?" . $auth['consumer_key'], null, OAUTH_HTTP_METHOD_GET);
    $resp_expired = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/" . $auth['etsyshop'] . "/listings/expired?" . $auth['consumer_key'], null, OAUTH_HTTP_METHOD_GET);
    $resp_draft = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/" . $auth['etsyshop'] . "/listings/draft?" . $auth['consumer_key'], null, OAUTH_HTTP_METHOD_GET);

    $resp = array_merge(
        $resp_active["response"]["results"],
        $resp_inactive["response"]["results"],
        $resp_expired["response"]["results"],
        $resp_draft["response"]["results"]
    );

    $all_products = array();
    foreach ($resp as $product) {
        $pa_product_id = $wpdb->get_var("select pm.product_id from wp_products_meta as pm left join wp_users_products as up on(up.id=pm.product_id) where pm.meta_key='etsy_id' and meta_value=" . $product['listing_id'] . " and up.users_id=" . $user_id);
        $all_products[] = array(
            "id" => $product["listing_id"],
            "title" => $product["title"],
            "status" => $product["state"],
            "url" => $product["url"],
            "image" => "http://apifortress.com/wp-content/uploads/2015/08/etsy.jpg",
            "imported" => ($pa_product_id == null) ? 0 : 1,
            "pa_id" => ($pa_product_id == null) ? 0 : $pa_product_id
        );
    }
    return $all_products;
}

function get_all_category_etsy($user_id, $taxonomy_id)
{
    $res = array();
    if ($taxonomy_id != null) {
        $etsy_auth = getEtsyShop($user_id);
        $cats = getEtsyCategoryChildren($etsy_auth, 1);
        $data = array_searc_result($cats, 'id', $taxonomy_id);
        if (count($data) > 0) {
            $etsy_cats = explode(',', $data['parents']);
            $etsy_cats[] = $taxonomy_id;
            foreach ($etsy_cats as $key => $value) {
                if ($key == 0) {
                    $res["etsycategory"] = (int)$value;
                } else {
                    $res["etsysub" . $key . "category"] = (int)$value;
                }
            }
        }
    }
    return $res;
}

function ViewEtsyShopById($shop_id, $user_id)
{
    $response = array("status" => 400, "error" => "Etsy shop was not found. Please check and try again later");
    $check_shop = $wpdb->get_result("SELECT  * FROM `wp_users_etsy` WHERE `users_id` = $user_id AND id=$shop_id");
    $check_shop_num = $wpdb->num_rows($check_shop);
    if ($check_shop_num > 0) {
        $shop_data = mysql_fetch_array($check_shop);

        $response = array("status" => 200, "data" => $shop_data);
    } else {
        $response = array("status" => 400, "error" => "Etsy shop was not found. Please check and try again later");
    }
    return $response;
}

function get_all_etsy_products_by_shop($shop_id, $user_id)
{
    global $wpdb;
    $auth = getEtsyShopById($user_id, $shop_id);
    @extract($auth);
    $total_active = 0;
    $all_resp_active = array();
    $resp_active = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active?includes=Images&limit=100&page=1", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_active['status']) && $resp_active['status'] == "failed") {
        return array("error" => $resp_active['errors']);
    }
    $all_resp_active = $resp_active["response"]["results"];
    $total_active = $resp_active["response"]["count"];
    $k = 2;
    //        wp_mail('team@ryankikta.com','total active',$total_active);

    while (count($all_resp_active) < $total_active & $k < 12) {
        $resp_active = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active?includes=Images&limit=100&page=" . $k, null, OAUTH_HTTP_METHOD_GET);
        if (isset($resp_active['status']) && $resp_active['status'] != "failed") {
            $all_resp_active = array_merge($resp_active["response"]["results"], $all_resp_active);
        } else {
            break;
        }
        $k++;
    }
    // wp_mail('team@ryankikta.com','k',$k);


    $resp_inactive = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/inactive?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_inactive['status']) && $resp_inactive['status'] == "failed") {
        return array("error" => $resp_inactive['errors']);
    }

    $resp_expired = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/expired?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_expired['status']) && $resp_expired['status'] == "failed") {
        return array("error" => $resp_expired['errors']);
    }

    $resp_draft = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/$etsyshop/listings/draft?includes=Images", null, OAUTH_HTTP_METHOD_GET);
    if (isset($resp_draft['status']) && $resp_draft['status'] == "failed") {
        return array("error" => $resp_draft['errors']);
    }

    $resp = array_merge(
        $all_resp_active,
        $resp_inactive["response"]["results"],
        $resp_expired["response"]["results"],
        $resp_draft["response"]["results"]
    );

    $all_products = array();
    foreach ($resp as $product) {
        $pa_product_id = $wpdb->get_var("select pm.product_id from wp_products_meta as pm left join wp_users_products as up on(up.id=pm.product_id) where pm.meta_key='etsy_id' and meta_value=" . $product['listing_id'] . " and up.users_id=" . $user_id);
        $img = "http://apifortress.com/wp-content/uploads/2015/08/etsy.jpg";
        if (isset($product["Images"][0]['url_570xN'])) {
            $img = $product["Images"][0]['url_570xN'];
        }
        $all_products[] = array(
            "id" => $product["listing_id"],
            "title" => $product["title"],
            "status" => $product["state"],
            "url" => $product["url"],
            "image" => $img,
            "imported" => ($pa_product_id == null) ? 0 : 1,
            "pa_id" => ($pa_product_id == null) ? 0 : $pa_product_id
        );
    }
    //wp_mail('team@ryankikta.com','ount products',count($all_products));
    return $all_products;
}

function CheckEtsyShop($user_id)
{
    $etsy_auth = false;
    $checkuser = $wpdb->get_result("SELECT `token`,`secret`,`shop`,`etsy_user_id`,`url_file`,`app_consumer_key`,`app_consumer_secret`,`version`,`users_id` FROM `wp_users_etsy` WHERE `users_id` = $user_id and (version =2 or version = 3)");
    $numshopsetsy = $wpdb->num_rows($checkuser);
    if ($numshopsetsy > 0) {
        $etsy_auth = true;
    }
    return $etsy_auth;
}

function ListShopsByUserId($user_id)
{
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_etsy` WHERE `users_id` = $user_id");
    $numshops_shopifys = $wpdb->num_rows($shop_list_query);

    if ($numshops_shopifys == 0) {
        $shop_list[] = array(
            "id" => "0", "value" => "Select Shop"
        );
    } else {
        while ($row = mysql_fetch_assoc($shop_list_query)) {
            $shop_list[] = array(
                "id" => $row["id"], "value" => $row["shop"], "active" => $row["active"]
            );
        }
    }
    return $shop_list;
}

function getListActiveProdctsByShop($etsy_auth)
{
    @extract($etsy_auth);
    $continue = true;
    $offset = 0;
    $limit = 250;
    $products = array();
    while ($continue) {
        $params = array('limit' => $limit, "offset" => $offset);
        $url = "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active";
        $result = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_GET);
        if ($result['status'] == "success") {
            $total = $result['response']['count'];
            $offset += 50;
            if ($offset > $total) {
                $continue = false;
            }
            foreach ($result['response']['results'] as $pr) {
                $products[] = array('id' => $pr['listing_id'], 'title' => $pr['title']);
            }
        } else {
            $continue = false;
        }
    }
    return $products;
}

function getEtsyDataByShopId($data, $shop_id)
{
    $etsynewproduct = esc_sql($data['etsynewproduct' . $shop_id]);
    $etsycategory = esc_sql($data['etsycategory' . $shop_id]);
    $etsysub1category = esc_sql($data['etsysub1category' . $shop_id]);
    $etsysub2category = esc_sql($data['etsysub2category' . $shop_id]);
    $etsysub3category = esc_sql($data['etsysub3category' . $shop_id]);
    $etsysection = esc_sql($data['etsysection' . $shop_id]);
    $etsyshipping = esc_sql($data['etsyshipping' . $shop_id]);
    $etsytags = trim(esc_sql($data['tags']));
    $etsymaterials = trim(esc_sql($data['etsymaterials' . $shop_id]));
    $etsyoccasion = esc_sql($data['etsyoccasion' . $shop_id]);
    $etsystyle = trim(esc_sql($data['etsystyle' . $shop_id]));
    $etsyrecipient = esc_sql($data['etsyrecipient' . $shop_id]);
    $whomadeit = esc_sql($data['whomadeit' . $shop_id]);
    $issupply = esc_sql($data['issupply' . $shop_id]);
    $whenmade = esc_sql($data['whenmade' . $shop_id]);
    if ($etsysection == '' || $etsysection == null) {
        $etsysection = 0;
    }

    if ($etsyshipping == '' || $etsyshipping == null) {
        $etsyshipping = 0;
    }


    return array('etsynewproduct' => $etsynewproduct, 'etsycategory' => $etsycategory, 'etsysub1category' => $etsysub1category, 'etsysub2category' => $etsysub2category, 'etsysub3category' => $etsysub3category, 'etsysection' => $etsysection,
        'etsyshipping' => $etsyshipping, 'etsyoccasion' => $etsyoccasion, 'etsystyle' => $etsystyle, 'etsyrecipient' => $etsyrecipient, 'whomadeit' => $whomadeit, 'issupply' => $issupply,
        'whenmade' => $whenmade, 'etsytags' => $etsytags, 'etsymaterials' => $etsymaterials);
}

function addEtsyProductByShop($POST, $etsy_product_id, $etsy_data, $etsy_variants, $method, $etsy_auth, $min_price, $currentuserid, $products_id, $shop_id)
{
    global $wpdb;
    @extract($etsy_data);
    $description = strip_tags(str_replace(array('<br>', '<br />'), array("\n", "\n"), stripslashes($POST["description"])));

    $category = (intval($etsysub3category) > 0) ? $etsysub3category : (intval($etsysub2category > 0) ? $etsysub2category : (intval($etsysub1category > 0) ? $etsysub1category : $etsycategory));

    $etsyuserid = $etsy_auth['etsyuserid'];
    $url = "https://openapi.etsy.com/v2/users/$etsyuserid/shops";
    $result = EtsyApiCall($etsy_auth, $url, null, OAUTH_HTTP_METHOD_GET);
    $is_vacation = $result['response']['results'][0]['is_vacation'];
    $state = ($is_vacation) ? "draft" : "active";
    if ($etsy_auth['language'] == "") {
        $default_language = $result['response']['results'][0]['languages'][0];
        $sql = "update wp_users_etsy set language='$default_language' where users_id=$currentuserid and id=$shop_id and (version =2 or version = 3)";
        $wpdb->query($sql);
    } else {
        $default_language = $etsy_auth['language'];
    }

    $params1 = array(
        'state' => $state,
        'description' => $description,
        'quantity' => 100,
        'shop_section_id' => $etsysection,
        'title' => str_replace('"', '\"', stripslashes($POST["title"])),
        'taxonomy_id' => intval($category),
        'who_made' => $whomadeit,
        'materials' => $etsymaterials,
        'tags' => $etsytags,
        'is_supply' => $issupply,
    );
    $params2 = array(
        'state' => $state,
        'when_made' => $whenmade,
        'shipping_template_id' => $etsyshipping,
        'processing_min' => 3,
        'processing_max' => 5,
        'has_variations' => true,
        'recipient' => $etsyrecipient,
        'occasion' => $etsyoccasion,
        'style' => $etsystyle,
    );

    if ((int)$POST["weight"] != 0) {
        $params2["item_weight"] = (int)$POST["weight"];
        $params2["item_weight_units"] = 'lbs';
    }

    if ($etsy_product_id == 0) {
        $etsyurl = 'https://openapi.etsy.com/v2/listings?language=' . $default_language;
        $params = array_merge($params1, $params2);
        $params['creation_tsz'] = time();
        $params['original_creation_tsz'] = time();
        $params['price'] = (float)$min_price;
        if (!$min_price) {
            $params['price'] = (float)$etsy_data['price'];
        }
        $body = $params;
        $oauth_method = OAUTH_HTTP_METHOD_POST;
        $action = 'POST';
    } else {
        $etsyurl = 'https://openapi.etsy.com/v2/listings/' . $etsy_product_id . "?language=" . $default_language;
        $body = $params1;
        $oauth_method = OAUTH_HTTP_METHOD_PUT;
        $action = 'PUT';
    }

    $etsycreate = EtsyApiCall($etsy_auth, $etsyurl, $body, $oauth_method, $action);

    if ($etsy_product_id != 0) {
        $params2['last_modified_tsz'] = time();
        $etsycreate = EtsyApiCall($etsy_auth, $etsyurl, $params2, $oauth_method, 'PUT');
    }
    $data = $etsycreate['response'];
    if ($POST['pagetype'] == 1 && $etsy_product_id == 0) {
        $_SESSION['shops']['etsy_id'] = $data['results'][0]['listing_id'];
    }

    if (isset($etsycreate['status']) && $etsycreate['status'] == 'failed') {
        @extract($etsy_auth);
        $text = ($POST['pagetype'] == 1 || ($POST['pagetype'] == 2 && $etsy_product_id == 0)) ? 'add' : 'edit';
        $count_shop = check_etsy_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $etsyshop . '"';
        $errors = array();
        if (is_array($etsycreate['errors'])) {
            $errors = $etsycreate['errors'];
        }
        if (is_string($etsycreate['errors'])) {
            $errors[] = array($etsycreate['errors']);
        }
        $error_title = 'Error ' . $text . ' product in Etsy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
    $etsy_id = $data['results'][0]['listing_id'];
    $pa_product_id_old = get_product_id_meta_shop($etsy_id, "etsy_id", $shop_id);
    $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $products_id) ? $pa_product_id_old : 0;
    $all_meta = array('etsy_id' => 'NULL', 'etsy_category_id' => 'NULL', 'etsy_sub1_category_id' => 'NULL', 'etsy_sub2_category_id' => 'NULL', 'etsy_sub3_category_id' => 'NULL'
    , 'etsy_section_id' => 'NULL', 'etsy_shipping_id' => 'NULL', 'occasion' => 'NULL', 'etsy_style' => 'NULL', 'etsy_recipient' => 'NULL', 'whomadeit' => 'NULL'
    , 'whenmade' => 'NULL', 'issupply' => 'NULL', 'etsymaterials' => 'NULL');
    $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $products_id and shopid = $shop_id", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['meta_key']] = $res['meta_id'];
    }
    $all_times = array();
    $start = microtime(true);
    if ($prod_to_deconnect) {
        update_product_meta_shop($products_id, 'etsy_id', $etsy_id, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_category_id', $etsycategory, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_sub1_category_id', $etsysub1category, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_sub2_category_id', $etsysub2category, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_sub3_category_id', $etsysub3category, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_section_id', $etsysection, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_shipping_id', $etsyshipping, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'occasion', $etsyoccasion, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_style', $etsystyle, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsy_recipient', $etsyrecipient, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'whomadeit', $whomadeit, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'whenmade', $whenmade, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'issupply', $issupply, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'etsymaterials', $etsymaterials, $shop_id, 0, $prod_to_deconnect);
    } else {
        $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['etsy_id']},$products_id,'etsy_id','$etsy_id',$shop_id),"
            . "({$all_meta['etsy_category_id']},$products_id,'etsy_category_id','$etsycategory',$shop_id),"
            . "({$all_meta['etsy_sub1_category_id']},$products_id,'etsy_sub1_category_id','$etsysub1category',$shop_id),"
            . "({$all_meta['etsy_sub2_category_id']},$products_id,'etsy_sub2_category_id','$etsysub2category',$shop_id),"
            . "({$all_meta['etsy_sub3_category_id']},$products_id,'etsy_sub3_category_id','$etsysub3category',$shop_id),"
            . "({$all_meta['etsy_section_id']},$products_id,'etsy_section_id','$etsysection',$shop_id),"
            . "({$all_meta['etsy_shipping_id']},$products_id,'etsy_shipping_id','$etsyshipping',$shop_id),"
            . "({$all_meta['occasion']},$products_id,'occasion','$etsyoccasion',$shop_id),"
            . "({$all_meta['etsy_style']},$products_id,'etsy_style','$etsystyle',$shop_id),"
            . "({$all_meta['etsy_recipient']},$products_id,'etsy_recipient','$etsyrecipient',$shop_id),"
            . "({$all_meta['whomadeit']},$products_id,'whomadeit','$whomadeit',$shop_id),"
            . "({$all_meta['whenmade']},$products_id,'whenmade','$whenmade',$shop_id),"
            . "({$all_meta['issupply']},$products_id,'issupply','$issupply',$shop_id),"
            . "({$all_meta['etsymaterials']},$products_id,'etsymaterials','$etsymaterials',$shop_id) "
            . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";
        $wpdb->query($sql);
    }
    $all_times['meta'] = microtime(true) - $start;
    $start = microtime(true);
    $sql = "UPDATE `wp_users_products` SET `etsyactive` = 1  WHERE `id` = $products_id";
    $query = $wpdb->get_result($sql);
    $all_times['update status'] = microtime(true) - $start;
    $start = microtime(true);
    updateEtsyVariants_v2($etsy_auth, $products_id, $etsy_id, $etsy_variants, $POST);
    $all_times['update_var'] = microtime(true) - $start;
    return $etsy_id;
}

function UploadEtsyStoreImagesByShop($POST, $images, $etsy_id, $etsy_auth, $products_id, $remove_old = 0, $shop_id)
{
    $url = "https://openapi.etsy.com/v2/listings/" . $etsy_id . "/images";

    if ($remove_old != 0) {
        DeleteEtsyImage($etsy_id, $etsy_auth, $url);
        delete_images_product_meta_shop($products_id, "etsy_id", $shop_id);
    }

    foreach ($images as $key => $image) {
        $position = $key + 1;
        $result = EtsyImageCall(OAUTH_HTTP_METHOD_POST, $etsy_auth, $url, $image['path'], $position);
        if (isset($result['status']) && $result['status'] == 'failed') {
            @extract($etsy_auth);
            $count_shop = check_etsy_shop($user_id, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $etsyshop . '"';
            $errors = array();
            $errors[] = $result['errors'];
            $error_title = 'Error upload images in Etsy ' . $shop_text . ':';
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        $data = $result['response'];
        $etsy_image_id = $data['results'][0]['listing_image_id'];

        $etsy_image_id = ($etsy_image_id == "") ? 0 : $etsy_image_id;
        $image_id = $image['id'];
        // update_image_meta_shop($products_id, $image_id, "etsy_id", $etsy_image_id, $shop_id);
    }
}

function getCurrentEtsyDataByShop($prodid)
{
    global $wpdb;
    $pa_product = $wpdb->get_row("select `etsyactive`,users_id from `wp_users_products` where `id` = $prodid");
    $etsyactiveold = intval($pa_product->etsyactive);
    $user_id = intval($pa_product->users_id);
    $shopids = "";
    $shops_etsy_ids = get_product_meta_shops($prodid, 'etsy_id');

    if (($etsyactiveold == 1) && (count($shops_etsy_ids) == 0)) {
        $shops_etsy_ids = array($wpdb->get_var("select id FROM `wp_users_etsy` WHERE `users_id` = $user_id"));
    }
    $shopids = implode(",", $shops_etsy_ids);
    $etsy_data = array('etsyactiveold' => $etsyactiveold, "shop_ids_etsy" => $shopids);
    return $etsy_data;
}

function deleteEtsyProductByShop($etsy_id, $auth, $productid, $shop_id)
{
    $url = "https://openapi.etsy.com/v2/private/listings/$etsy_id";
    EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_DELETE);
    $prodid = get_product_id_meta_shop($etsy_id, "etsy_id", $shop_id);
    if (!$prodid) {
        $shop_id = 0;
    }
    delete_product_meta_multi_shop($productid, "'etsymaterials','issupply','whenmade','whomadeit','etsy_recipient','etsy_style','occasion','etsy_id','etsy_category_id','etsy_sub1_category_id','etsy_sub2_category_id' , 'etsy_sub3_category_id' , 'etsy_section_id', 'etsy_shipping_id'", $shop_id);
    delete_images_product_meta_shop($productid, "etsy_id", $shop_id);
}

function getEtsyProductDataByShop($pa_product_id, $shop_id)
{
    $etsysection = get_product_meta_shop($pa_product_id, 'etsy_section_id', $shop_id);
    $etsyshipping = get_product_meta_shop($pa_product_id, 'etsy_shipping_id', $shop_id);

    if ($etsysection == '' || $etsysection == null) {
        $etsysection = 0;
    }
    if ($etsyshipping == '' || $etsyshipping == null) {
        $etsyshipping = 0;
    }

    return array(
        'etsynewproduct' => 0,
        'etsycategory' => get_product_meta_shop($pa_product_id, 'etsy_category_id', $shop_id),
        'etsysub1category' => get_product_meta_shop($pa_product_id, 'etsy_sub1_category_id', $shop_id),
        'etsysub2category' => get_product_meta_shop($pa_product_id, 'etsy_sub2_category_id', $shop_id),
        'etsysub3category' => get_product_meta_shop($pa_product_id, 'etsy_sub3_category_id', $shop_id),
        'etsysection' => $etsysection,
        'etsyshipping' => $etsyshipping,
        'etsyoccasion' => get_product_meta_shop($pa_product_id, 'occasion', $shop_id),
        'etsystyle' => get_product_meta_shop($pa_product_id, 'etsy_style', $shop_id),
        'etsyrecipient' => get_product_meta_shop($pa_product_id, 'etsy_recipient', $shop_id),
        'etsymaterials' => get_product_meta_shop($pa_product_id, 'etsymaterials', $shop_id),
        'whomadeit' => "collective",
        'whenmade' => "made_to_order",
        'issupply' => 0
    );
}

function get_etsy_product_import($user_id, $etsy_product_id, $shop_id = 0)
{
    global $wpdb;
    $auth = ($shop_id != 0) ? getEtsyShopById($user_id, $shop_id) : getEtsyShop($user_id);
    $product = EtsyApiCall($auth, "https://openapi.etsy.com/v2/listings/" . $etsy_product_id, null, OAUTH_HTTP_METHOD_GET);
    //$inventory = EtsyApiCall($auth, "https://openapi.etsy.com/v2/listings/" . $etsy_product_id."/inventory", NULL, OAUTH_HTTP_METHOD_GET);
    $product_images = EtsyApiCall($auth, "https://openapi.etsy.com/v2/listings/" . $etsy_product_id . "/images", null, OAUTH_HTTP_METHOD_GET);

    $product_variations = EtsyApiCall($auth, "https://openapi.etsy.com/v2/listings/$etsy_product_id/variations", null, OAUTH_HTTP_METHOD_GET);
    $product_variations = ($product_variations["status"]) ? $product_variations["response"]["results"] : array();


    $product = $product["response"]["results"][0];
    $sku_exp = explode("-", $product['sku'][0]);
    $sku = $sku_exp[0];
    //mail('team@ryankikta.com',"etsy product import",var_export($product,true));
    $product_images = $product_images["response"]["results"];
    $images = array();
    foreach ($product_images as $img) {
        $images[] = $img['url_570xN'];
    }

    $colors = array();
    //mail("team@ryankikta.com","product_variations import",var_export($product_variations,true));
    foreach ($product_variations as $variant) {
        if (!in_array(strtolower($variant["formatted_name"]), array("color", "primary color"))) {
            continue;
        }
        foreach ($variant["options"] as $option) {
            $color_value = $option['value'];
            $color_price = $option['price'];
            if (get_color_id($color_value) != null) {
                $colors[$color_value][] = $color_price;
            } else {
                $option_exp = explode("-", $color_value);
                $option_exp = array_map('trim', $option_exp);
                foreach ($option_exp as $opt) {
                    if (get_color_id($opt) != null) {
                        $colors[$opt][] = $color_price;
                    }
                }
            }
        }
    }
    $shop_colors = array();
    foreach ($colors as $color => $prices) {
        $shop_colors[$color][] = min($prices);
        $shop_colors[$color][] = max($prices);
    }
    $data = array(
        "sku" => $sku,
        "title" => stripcslashes($product['title']),
        "description" => $product['description'],
        "weight" => ($product['item_weight'] == null) ? "" : $product['item_weight'] / 16,
        "tags" => implode(",", $product['tags']),
        "etsymaterials" => implode(",", $product['materials']),
        "etsyoccasion" => $product['occasion'],
        "etsyshippingid" => $product['shipping_template_id'],
        "etsyrecipient" => $product['recipient'],
        "etsystyle" => ($product["style"] == null) ? "" : implode(",", $product['style']),
        "etsysection" => $product["shop_section_id"],
        "who_made" => $product["who_made"],
        "is_supply" => ($product["is_supply"] == '') ? '' : (($product["is_supply"]) ? 1 : 0),
        "when_made" => $product["when_made"],
        "shop_images" => $images,
        "shop_colors" => $shop_colors,
        "etsy_shop" => $auth,
    );
    $taxonomy_id = (isset($product["taxonomy_id"]) && $product["taxonomy_id"] != null) ? $product["taxonomy_id"] : ((isset($product["suggested_taxonomy_id"]) && $product["suggested_taxonomy_id"] != null) ? $product["suggested_taxonomy_id"] : 0);
    $cats = get_all_category_etsy_per_shop($user_id, $taxonomy_id, $shop_id);
    //mail("team@ryankikta.com","etsy data prod import",var_export(array_merge($data, $cats),true));
    return array_merge($data, $cats);
}

function get_all_category_etsy_per_shop($user_id, $taxonomy_id, $shop_id)
{
    $res = array();
    if ($taxonomy_id != null) {
        $etsy_auth = getEtsyShopById($user_id, $shop_id);
        $cats = getEtsyCategoryChildren($etsy_auth, 1);
        $data = array_searc_result($cats, 'id', $taxonomy_id);
        if (count($data) > 0) {
            $etsy_cats = explode(',', $data['parents']);
            $etsy_cats[] = $taxonomy_id;
            foreach ($etsy_cats as $key => $value) {
                if ($key == 0) {
                    $res["etsycategory"] = (int)$value;
                } else {
                    $res["etsysub" . $key . "category"] = (int)$value;
                }
            }
        }
    }
    return $res;
}

function DeleteproductEtsyFromShop($prodid, $shopid, $auth)
{
    $etsy_id = get_product_meta_shop($prodid, "etsy_id", $shopid);
    if ((!isset($etsy_id)) || ($etsy_id == null) || ($etsy_id == "")) {
        $etsy_id = get_product_meta($prodid, 'etsy_id');
    }
    $url = "https://openapi.etsy.com/v2/private/listings/$etsy_id";
    EtsyApiCall($auth, $url, null, OAUTH_HTTP_METHOD_DELETE);
    delete_product_meta_multi_shop($prodid, "'etsymaterials','issupply','whenmade','whomadeit','etsy_recipient','etsy_style','occasion','etsy_id','etsy_category_id','etsy_sub1_category_id','etsy_sub2_category_id','etsy_sub3_category_id','etsy_section_id','etsy_shipping_id'", $shopid);
    delete_images_product_meta_shop($prodid, 'etsy_id', $shopid);
    $response = array("status" => 200, "sucess" => "TRUE");
    return $response;
}

function DeleteproductEtsyFromPA($prodid, $shopid)
{
    $response = array("status" => 400, "error" => "");
    $etsy_id = get_product_meta_shop($prodid, "etsy_id", $shopid);
    if ((!isset($etsy_id)) || ($etsy_id == null) || ($etsy_id == "")) {
        $etsy_id = get_product_meta($prodid, 'etsy_id');
    }
    if (isset($etsy_id) && ($etsy_id != "")) {
        $wpdb->get_result("DELETE FROM `wp_products_meta` WHERE `product_id` = $prodid AND `shopid`=$shopid AND  `meta_key` in ('etsymaterials','issupply','whenmade','whomadeit','etsy_recipient','etsy_style','occasion','etsy_id','etsy_category_id','etsy_sub1_category_id','etsy_sub2_category_id' , 'etsy_sub3_category_id' , 'etsy_section_id', 'etsy_shipping_id')");
        delete_images_product_by_shop_meta($prodid, $shopid);
    } else {
    }
    $response = array("status" => 200, "sucess" => "TRUE");
    return $response;
}

function check_product_existe_etsy($etsy_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($etsy_id, "etsy_id", $shop_id);
    if ($pa_product_id) {
        return array("status" => 200, "data" => true);
    }
    if (!$pa_product_id) {
        $pa_product_id = get_product_meta_byfield("product_id", "etsy_id", $etsy_id);
    }
    if ($pa_product_id) {
        $userid_product = $wpdb->get_var("select users_id from wp_users_products where id=$pa_product_id");
        if ($userid_product == $user_id) {
            $existe = true;
        }
    }
    return array("status" => 200, "data" => $existe);
}

function analyze_etsy_order($order_id, $etsy_id = "")
{
    global $wpdb;
    $order = $wpdb->get_row("select user_id,external_id from wp_rmproductmanagement_orders where order_id= $order_id ");
    if ($order) {
        $etsy_shop = $wpdb->get_row("select * from `wp_users_etsy` where users_id = " . $order->user_id, ARRAY_A);
        $shop_id = $etsy_shop["id"];
        debug($etsy_shop);
        $etsy_auth = array(
            'etsytoken' => $etsy_shop["token"],
            'etsysecret' => $etsy_shop["secret"],
            'etsyshop' => $etsy_shop["shop"],
            'etsyuserid' => $etsy_shop["etsy_user_id"],
            'url_file' => $etsy_shop["url_file"],
            'consumer_key' => $etsy_shop["app_consumer_key"],
            'consumer_secret' => $etsy_shop["app_consumer_secret"],
            'version' => $etsy_shop["version"],
            'user_id' => $user_id,
            'etsyshopId' => $shop_id
        );
        @extract($etsy_auth);
        //$url = "https://openapi.etsy.com/v2/private/shops/$etsyshop/receipts";
        $transaction_id = (!$etsy_id) ? $order->external_id : $etsy_id;
        $url = "https://openapi.etsy.com/v2/receipts/" . $transaction_id;
        $params = array('limit' => 20, 'was_paid' => 'true', 'was_shipped' => 'false', 'min_created' => $last_time);
        $return = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_GET, 'GET');
        //debug($return);
        if ($return['status'] == 'success' && !empty($return['response']['results'])) {
            $data = $return['response'];
            if ($data['count'] != 0) {
                echo 'here';
                foreach ($data['results'] as $receipt) {
                    if (!empty($receipt)) {
                        $etsy_id = $receipt['receipt_id'];
                        $order_data = get_order_etsy_data_v2($etsy_auth, $receipt);
                        @extract($order_data);
                        $allitems = get_allitem_etsy_v2($itemsinfo, $order->user_id, $shop_id);
                        debug($allitems);
                    }
                }
            }
        }
    }
}
