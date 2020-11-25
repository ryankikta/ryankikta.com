<?php
require("shopify.php");

global $wpdb, $table_prefix;

if(!isset($wpdb))
{
    require_once('/var/www/html/wp-config.php');
    require_once('/var/www/html/wp-includes/wp-db.php');
}

define("Shopify_Key", "1989c326aaaf62c2694cae84866f1a91");
define("Shopify_Secret", "4840139646f6ab8199a601c80430c9e2");
define("DEBUG_LEVEL", 0);
/*********************************
 * Fulfillment services endpoints
 *
 *********************************/
function create_fulfillment_service($auth, $user_id)
{
    global $sc;

    $data = array("fulfillment_service" => array(
        "name" => "Ryan Kikta",
        "callback_url" => "https://RyanKikta.com",
        "inventory_mangagemnet" => false,
        "tracking_support" => true,
        "requires_shipping_method" => true,
        "format" => "json"
    ));
    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/fulfillment_services.json";
        $res = ShopifyApiCall("POST", $path, $data, $user_id);
    }

    return $res;
}

function list_fulfillment_services($auth, $user_id)
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/fulfillment_services.json";
        $res = ShopifyApiCall("GET", $path, $user_id);
    }

    return $res;
}

function delete_fulfillment_service($auth, $user_id, $serviceID)
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/fulfillment_services/$serviceID.json";
        $res = ShopifyApiCall("DELETE", $path, $user_id);
    }

    return $res;
}


/******************************/
function get_shop_info($auth, $user_id)
{
    global $sc;

    $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
    $path = "admin/api/2020-04/shop.json";
    $res = ShopifyApiCall("GET", $path, $user_id);

    return $res;
}

function get_fulfillment_count($auth, $user_id, $order_id)
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "admin/api/2020-04/orders/$order_id/fulfillments/count.json";
        $res = ShopifyApiCall("GET", $path, $user_id);
    }

    return $res;
}


function get_inventory_levels($auth, $variant_id)
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "admin/api/2020-04/inventory_levels.json?inventory_item_ids=$variant_id";
        $res = ShopifyApiCall("GET", $path, $user_id);
    }

    return $res;
}

function getShopifyShop($user_id)
{

    $authshopify = array();
    // check if user has shopify store
    $checkuser = $wpdb->get_result("SELECT `shop`,`token` FROM `wp_users_shopify` WHERE `users_id` = $user_id");
    $numshopsshopify = $wpdb->num_rows($checkuser);

    if ($numshopsshopify != 0) {

        $shoprow = $wpdb->get_row($checkuser);
        $shop = $shoprow[0];
        $token = $shoprow[1];
        $shopify_auth = array('shop' => $shop, 'token' => $token);
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $authshopify = array($shopify_auth, $sc);
    }
    return $authshopify;
}

function getCurrentShopifyData($prodid)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $shopifyactive = $row['active'];
    $shopify_id = $row['shopify_id'];
    $type = $row['type'];
    $vendor = $row['vendor'];
    $collection_id = $row['collection_id'];
    $shopify_data = array('shopifyactiveold' => $shopifyactive, 'shopify_id' => $shopify_id, 'shopifytype' => $type, 'shopifyvendor' => $vendor, 'collection_id' => $collection_id);
    return $shopify_data;
}

function get_shopify_locations($auth)
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/locations.json";
        $res = ShopifyApiCall("GET", $path);
    }
    return $res;
}


function getShopifyData($data)
{

    $shopifyactive = ($data['shopifyactive']) ? esc_sql($data['shopifyactive']) : 0;
    $newproduct = esc_sql($data['newproduct']);
    $shopifytype = esc_sql($data['shopifytype']);
    $shopifyvendor = esc_sql($data['shopifyvendor']);

    $collection_id = "";
    if ($data['collection'] && !empty($data['collection'])) {
        foreach ($data['collection'] as $useless => $collection) {
            $collection_id .= $collection . ",";
        }
        $collection_id = rtrim($collection_id, ",");
    }

    return array('shopifyactive' => $shopifyactive, 'newproduct' => $newproduct, 'shopifytype' => $shopifytype,
        'shopifyvendor' => $shopifyvendor, 'collection_id' => $collection_id);
}

function get_all_products_shopify($auth, $params = array())
{
    global $sc;

    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/products.json";
        $res = ShopifyApiCall("GET", $path, $params);
    }
    return $res;
}

function get_shopify_product_variant_data($auth, $variant_id)
{
    $return = array();
    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/variants/$variant_id.json";
        $return = ShopifyApiCall("GET", $path, null);
    }
    return $return;
}

function get_shopify_collections($user_id, $shopify_id)
{
    $auth = getShopifyShop($user_id);
    $allcollects = ShopifyApiCall1("GET", "/admin/api/2020-04/collects.json?product_id=" . $shopify_id, NULL, $auth[1]);
    $collects = "";
    foreach ($allcollects as $collect) {
        $collects .= $collect["collection_id"] . ",";
    }
    $collects = rtrim($collects, ",");
    return $collects;
}

function get_shopify_product_data($auth, $shopify_id, $params)
{
    $res = array();
    if ($shopify_id != 0 && !empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/products/" . $shopify_id . ".json";
        $res = ShopifyApiCall1("GET", $path, $params, $sc, $auth['users_id']);
    }
    return $res;
}

function get_image_variants($shopify_id, $shop_id, $shopify_images = array())
{
    global $wpdb;

    $variants = array();
    $all_variants = array();
    if (!empty($shopify_images)) {
        foreach ($shopify_images as $ids)
            $all_variants = array_merge($all_variants, $ids);
    }
    $auth = $wpdb->get_row("select token,shop from wp_users_shopify where id = '$shop_id'", ARRAY_A);
    $shopify_data = get_shopify_product_data($auth, $shopify_id, array());

    if ($shopify_data) {
        foreach ($shopify_data['variants'] as $variant) {
            if (!$variant['image_id'] && !in_array($variant['id'], $all_variants))
                $variants[] = $variant['id'];
        }
    }
    //wp_mail('team@ryankikta.com','shopify data',var_export($variants,true));

    return $variants;

}

function shopify_fix_variants_image($shopify_id, $shopify_images)
{
    global $sc;
    //wp_mail('team@ryankikta.com','fix image',var_export(array($shopify_images,$uploadimage) ,true));

    foreach ($shopify_images as $image_id => $variants) {
        $imageadd = array("image" => array('variant_ids' => $variants));
        $path = '/admin/api/2020-04/products/' . $shopify_id . '/images/' . $image_id . '.json';
        //$uploadimage = ShopifyApiCall("PUT", $path, $imageadd);
        //wp_mail('team@ryankikta.com','fix image',var_export(array($shopify_images,$uploadimage) ,true));
    }

}

function getShopifyProductData($shopify_id, $auth)
{
    global $wpdb;

    $shopify_options = array();
    $shopify_weight = array();
    $shopify_old_variants = array();
    $has_title = false;
    $update_shopify = 0;
    $shopify_images = array();

    if ($shopify_id != 0 && !empty($auth)) {

        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);

        $path = "/admin/api/2020-04/products/" . $shopify_id . ".json";
        $shopifycreate = ShopifyApiCall("GET", $path, null);

        $options_t = $shopifycreate['options'];
        foreach ($options_t as $opt) {
            if (strtolower($opt['name']) == 'title') {
                $has_title = true;
            }
        }

        if (is_array($shopifycreate) && !empty($shopifycreate)) {
            $update_shopify = 1;
            foreach ($shopifycreate['variants'] as $sh_variant) {
                $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $sh_variant['option2'] . '"');
                $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $sh_variant['option1'] . '"');
                foreach ($colors_id as $color) {
                    $color_id = $color->color_id;
                    foreach ($sizes_id as $size) {
                        $size_id = $size->size_id;
                        $shopify_old_variants[$color_id . '_' . $size_id] = $sh_variant['id'];
                        if ($sh_variant['option3'])
                            $shopify_options[$color_id . '_' . $size_id] = $sh_variant['option3'];
                        $shopify_weight[$color_id . '_' . $size_id] = $sh_variant['grams'];
                        //$shopify_images[$color_id . '_' . $size_id] = $sh_variant['image_id'];
                        if ($sh_variant['image_id'])

                            $shopify_images[$sh_variant['image_id']][] = $sh_variant['id'];

                    }
                }
            }
        }
    }


    return array($update_shopify, $has_title, $shopify_options, $shopify_weight, $shopify_old_variants, $shopify_images, $shopifycreate);
}

function deleteShopifyProduct($shopify_id, $auth, $user_product_id = 0)
{
    if (!empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = '/admin/api/2020-04/products/' . $shopify_id . '.json';
        ShopifyApiCall("DELETE", $path);
        if ($user_product_id != 0) {
            // unset db shit
            $wpdb->get_result("UPDATE `wp_users_products` SET `active` = 0, `shopify_id` = 0  WHERE `id` = $user_product_id");
            $wpdb->get_result("UPDATE `wp_users_products_colors` SET `shopify_id` = 0 WHERE `users_products_id` = $user_product_id");
            $wpdb->get_result("UPDATE `wp_users_products_images` SET `shopify_id` = 0 WHERE `users_products_id` = $user_product_id");
        }
    }
}

function deleteShopifyProductFromShops($user_product_id, $shopify_id)
{
    $shop_id = get_product_meta($user_product_id, "shopifyshopID");
    $checkuser = $wpdb->get_result("SELECT `shop`,`token` FROM `wp_users_shopify` WHERE `id` = $shop_id");
    $numshopsshopify = $wpdb->num_rows($checkuser);

    if ($numshopsshopify != 0) {

        $shoprow = $wpdb->get_row($checkuser);
        $shop = $shoprow[0];
        $token = $shoprow[1];
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $path = '/admin/api/2020-04/products/' . $shopify_id . '.json';
        ShopifyApiCall("DELETE", $path);
        if ($user_product_id != 0) {
            // unset db shit
            $wpdb->get_result("UPDATE `wp_users_products` SET `active` = 0, `shopify_id` = 0  WHERE `id` = $user_product_id");
            $wpdb->get_result("UPDATE `wp_users_products_colors` SET `shopify_id` = 0 WHERE `users_products_id` = $user_product_id");
            $wpdb->get_result("UPDATE `wp_users_products_images` SET `shopify_id` = 0 WHERE `users_products_id` = $user_product_id");
            delete_product_meta($user_product_id, "shopifyshopID");
        }
    }
}

function get_another_options_shopify($shopify_id)
{
    $count = $size_position = $color_position = 0;
    $path = "/admin/api/2020-04/products/$shopify_id.json";
    $result = ShopifyApiCall("GET", $path, null);
    $options = $result['options'];
    $lenght = count($options);
    foreach ($options as $option) {
        if (!in_array($option['name'], array('Size', 'Color'))) {
            $count++;
            $pos = $option['position'];
        } elseif ($option['name'] == 'Size')
            $size_position = $option['position'];
        elseif ($option['name'] == 'Color')
            $color_position = $option['position'];
    }
    $arr = array('lenght' => $lenght, 'count' => $count, 'size_position' => $size_position, 'color_position' => $color_position);
    if ($count == 1)
        $arr['pos'] = $pos;
    return $arr;
}

function get_color_size_position($res)
{
    $pos = 0;
    if ($res['size_position'] == 0 && $res['color_position'] == 0) {
        $sp = $res['lenght'] + 1;
        $cp = $res['lenght'] + 2;
    } elseif ($res['size_position'] != 0 && $res['color_position'] != 0) {
        $sp = $res['size_position'];
        $cp = $res['color_position'];
    } elseif ($res['size_position'] != 0) {
        $sp = $res['size_position'];
        $cp = $res['lenght'] + 1;
    } elseif ($res['color_position'] != 0) {
        $sp = $res['lenght'] + 1;
        $cp = $res['color_position'];
    }
    if (isset($res['pos']))
        $pos = $res['pos'];
    $positions = array('sp' => $sp, 'cp' => $cp, 'pos' => $pos);
    return $positions;
}

function buildShopifyVariants2($POST, $newproduct, $variants, $user_product_id, $shopify_options = array(), $has_title = false, $shopify_weight = array(), $shopify_old_variants = array(), $shopifyweight)
{
    global $wpdb;
    $sp = 1;
    $cp = 2;
    $pos = 3;
    $count = 0;
    if ($newproduct != 0) {
        $result = get_another_options_shopify($newproduct);
        $count = $result['count'];
        if ($count > 1) {
            $_SESSION['error_title'] = 'impossible to create a product based on the following color / size options';
            $_SESSION['data'] = $POST;
            if ($POST['pagetype'] == 1)
                wp_redirect("/add-products");
            exit();
        } else {
            $sc_pos = get_color_size_position($result);
            @extract($sc_pos);
        }
    }
    $opt3 = ($pos != 0) ? $pos : 3;
    $shopify_variants = array();
    $variants3 = array();
    foreach ($variants as $variant) {
        $color_id = $variant['color_id'];
        $size_id = $variant['size_id'];
        $color_name = $variant['color_name'];
        $size_name = $variant['size_name'];
        $position = $variant['position'];
        $price = $variant['price'];
        $sku = $variant['sku'];
        $shopify_variant_id = $wpdb->get_var("select shopify_id from wp_users_products_colors where color_id=$color_id and size_id=$size_id and users_products_id= $user_product_id");
        $variant1 = array("option{$sp}" => $size_name, "option{$cp}" => $color_name, "sku" => $sku, "price" => $price, "position" => $position, "color_id" => $color_id, "size_id" => $size_id);
        if (isset($shopify_options[$color_id . '_' . $size_id]))
            $variant1['option' . $opt3] = $shopify_options[$color_id . '_' . $size_id];
        else {
            if ($has_title)
                $variant1['option' . $opt3] = str_replace('"', '\"', stripslashes($sku));
            elseif (!$has_title & $count != 0) {
                $variant1['option' . $opt3] = str_replace('"', '\"', stripslashes($sku));
            }
        }
        if (isset($shopify_weight[$color_id . '_' . $size_id]) && $shopifyweight == 0)
            $variant1['grams'] = $shopify_weight[$color_id . '_' . $size_id];
        else if ($shopifyweight != 0)
            $variant1['grams'] = $shopifyweight * 453.59237;
        if (isset($shopify_old_variants[$color_id . '_' . $size_id]))
            $variant1['id'] = $shopify_old_variants[$color_id . '_' . $size_id];
        else {
            if ($shopify_variant_id > 0) {

                $variant1['id'] = $shopify_variant_id;
            } else {
                //$variants3[] = array("option{$sp}" => $size_name, "option{$cp}" => $color_name,"option{$opt3}"=>str_replace('"', '\"',stripslashes($sku)),"sku" => str_replace('"', '\"', stripslashes($sku)) . "-" . $color_name . "-" . $size_name, "price" => $price, "position" => $position, "color_id" => $color_id, "size_id" => $size_id);
            }
        }
        $shopify_variants[] = $variant1;
    }
    return array($shopify_variants, $variants3);
}

function buildShopifyVariants($variants, $user_product_id, $shopifyweight, $shopify_options = array(), $has_title = false, $shopify_weight = array(), $shopify_old_variants = array(), $shop_id = 0)
{
    global $wpdb;
    $shopify_variants = array();
    $variants3 = array();
    foreach ($variants as $variant) {
        $color_id = $variant['color_id'];
        $size_id = $variant['size_id'];
        $color_name = $variant['color_name'];
        $size_name = $variant['size_name'];
        $position = $variant['position'];
        $price = $variant['price'];
        $sku = $variant['sku'];
        $shopify_variant_id = 0;
        $pa_variant_id = $wpdb->get_var("SELECT id FROM `wp_users_products_colors` WHERE `color_id` = '$color_id' AND `size_id` = '$size_id' AND `users_products_id` = $user_product_id");
        if (isset($pa_variant_id) && ($pa_variant_id != "")) {
            $shopify_variant_id = get_variant_meta_shop($pa_variant_id, "shopify_id", $shop_id);
        }
        $variant1 = array("option1" => $size_name, "option2" => $color_name, "sku" => $sku, "price" => $price, "position" => $position);
        if (isset($shopify_options[$color_id . '_' . $size_id]))
            $variant1['option3'] = $shopify_options[$color_id . '_' . $size_id];
        else {
            if ($has_title)
                $variant1['option3'] = str_replace('"', '\"', stripslashes($sku));
        }
        if (isset($shopify_weight[$color_id . '_' . $size_id]) && $shopifyweight == 0)
            $variant1['grams'] = $shopify_weight[$color_id . '_' . $size_id];
        else if ($shopifyweight != 0)
            $variant1['grams'] = $shopifyweight * 453.59237;
        if (isset($shopify_old_variants[$color_id . '_' . $size_id]))
            $variant1['id'] = $shopify_old_variants[$color_id . '_' . $size_id];
        else {
            if ($shopify_variant_id > 0) {

                $variant1['id'] = $shopify_variant_id;
            } else {

                $variants3[] = array("option1" => $size_name, "option2" => $color_name, "sku" => str_replace('"', '\"', stripslashes($sku)), "price" => $price, "position" => $position, "color_id" => $color_id, "size_id" => $size_id);
            }
        }
        $shopify_variants[] = $variant1;
    }
    return array($shopify_variants, $variants3);
}

function UploadShopifyStoreImages($POST, $images, $shopify_id, $products_id, $remove_old = 0)
{

    global $sc, $wpdb;
    if ($remove_old == 1) {
        DeleteShopifyImage($shopify_id);
    }
    $errors = array();
    foreach ($images as $key => $image) {

        $shopify_image_id = 0;
        $imageadd = array();
        $image_id = $image['id'];
        $imageurl = $image['src'];
        $position = $key + 1;
        $imageadd = array("image" => array("src" => "$imageurl", "position" => $position));
        $path = '/admin/api/2020-04/products/' . $shopify_id . '/images.json';
        $uploadimage = ShopifyApiCall("POST", $path, $imageadd);
        if (isset($uploadimage['errors'])) {
            foreach ($uploadimage['errors'] as $value) {
                $errors[] = $value;
            }
        }
        $shopify_image_id = $uploadimage['id'];
        if ($shopify_image_id == "") {
            $shopify_image_id = 0;
        }
        $sql = "update `wp_users_products_images` set `shopify_id`=$shopify_image_id  where `image_id`=$image_id and `users_products_id`=$products_id ";
        $query = $wpdb->get_result($sql);
        if (!$query) {
            $logs = array();
            $logs['sql'] = mysql_escape_string($sql);

            /*wp_insert_post(array(
			  'post_content' => var_export($logs, true),
			  'post_title' => esc_sql("adding product image "),
			  'post_status' => 'draft',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_type' => 'systems'
			  ));*/
            wp_mail('cnelson@ryankikta.com', 'adding product image issue', '');
        }
    }
    if (!empty($errors)) {
        $error_title = 'Error upload images in shopify :';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function DeleteShopifyImage($product_id)
{

    $imageslist = ShopifyApiCall("GET", '/admin/api/2020-04/products/' . $product_id . '/images.json');
    foreach ($imageslist as $key => $array) {
        $image_id = $array['id'];
        ShopifyApiCall("DELETE", '/admin/api/2020-04/products/' . $product_id . '/images/' . $image_id . '.json');
    }
}

function delete_variant_shopify($shopify_id, $variant_id)
{
    $del = ShopifyApiCall("DELETE", '/admin/api/2020-04/products/' . $shopify_id . '/variants/' . $variant_id . '.json');
    return $del;
}

function create_default_variant_shopify($shopify_id)
{
    $variant = array("option1" => 'Default option_' . time()/* , "option2" => 'Default opt2' */);
    $charge = array(
        "variant" => $variant,
    );
    $variant = ShopifyApiCall("POST", "/admin/api/2020-04/products/$shopify_id/variants.json", $charge);
    return $variant['id'];
}

function addShopifyProduct($POST, $shopify_data, $description, $variants, $currentuserid, $products_id)
{
    global $wpdb, $sc;

    @extract($shopify_data);
    $list = get_html_translation_table(HTML_ENTITIES);
    unset($list['"']);
    unset($list['<']);
    unset($list['>']);
    unset($list['&']);
    $updatedescription = strtr($description, $list);

    if ($POST['newproduct'] == 0) {

        $charge = array(
            "product" => array
            (
                "title" => str_replace('"', '\"', stripslashes($POST['title'])),
                "body_html" => $updatedescription,
                "vendor" => str_replace('"', '\"', stripslashes($POST['shopifyvendor'])),
                "product_type" => str_replace('"', '\"', stripslashes($POST['shopifytype'])),
                "tags" => str_replace('"', '\"', stripslashes($POST['tags'])),
                "options" => array
                (
                    array
                    (
                        "name" => 'Size'
                    ),
                    array
                    (
                        "name" => 'Color'
                    )
                ),
                "variants" => $variants
            )
        );
        $shopifycreate = ShopifyApiCall("POST", '/admin/api/2020-04/products.json', $charge, $currentuserid);
        if (isset($shopifycreate['errors'])) {
            $errors = array();
            $error_title = 'Error add product in shopify :';
            foreach ($shopifycreate['errors'] as $value) {
                $errors[] = $value;
            }
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        // Shopify ID
        $shopify_id = $shopifycreate['id'];
        if ($POST['pagetype'] == 1 || $POST['pagetype'] == 3)
            $_SESSION['shops']['shopify_id'] = $shopify_id;
    } else {

        $shopify_id = $POST['newproduct'];

        // Delete all Images
        $imageslist = ShopifyApiCall("GET", '/admin/api/2020-04/products/' . $shopify_id . '/images.json');
        foreach ($imageslist as $key => $array) {
            $image_id = $array['id'];
            $return = ShopifyApiCall("DELETE", '/admin/api/2020-04/products/' . $shopify_id . '/images/' . $image_id . '.json');
        }

        // Delete all collections
        $allcollects = ShopifyApiCall("GET", "/admin/api/2020-04/collects.json?product_id=" . $shopify_id);
        foreach ($allcollects as $useless => $array) {
            $return = ShopifyApiCall("DELETE", "/admin/api/2020-04/collects/" . $array['id'] . ".json");
        }

        $list = get_html_translation_table(HTML_ENTITIES);
        unset($list['"']);
        unset($list['<']);
        unset($list['>']);
        unset($list['&']);

        $updatedescription = strtr($description, $list);

        $color_exist = false;
        $size_exist = false;
        $options = array();

        if (!$size_exist)
            $options[] = array("name" => 'Size');
        if (!$color_exist)
            $options[] = array("name" => 'Color');

        // Update the product
        $charge = array(
            "product" => array
            (
                "title" => str_replace('"', '\"', stripslashes($POST['title'])),
                "body_html" => $updatedescription,
                "vendor" => str_replace('"', '\"', stripslashes($POST['shopifyvendor'])),
                "product_type" => str_replace('"', '\"', stripslashes($POST['shopifytype'])),
                "tags" => str_replace('"', '\"', stripslashes($POST['tags'])),
                "options" => $options,
                "variants" => $variants
            )
        );
        $shopifycreate = ShopifyApiCall("PUT", "/admin/api/2020-04/products/$shopify_id.json", $charge);
        if (isset($shopifycreate['errors'])) {
            $errors = array();
            $error_title = 'Error update an existing product in shopify :';
            foreach ($shopifycreate['errors'] as $value) {
                $errors[] = $value;
            }
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        $shopify_id = $shopifycreate['id'];
    }
    // Add it to the collections
    foreach ($POST['collection'] as $useless => $custom_collection_id) {
        if ($custom_collection_id !== "") {
            $collect = array("custom_collection" => array("collects" => array(array("product_id" => $shopify_id))));
            $shopifycollect = ShopifyApiCall("PUT", "/admin/api/2020-04/custom_collections/$custom_collection_id.json", $collect);
        }
    }
    if ($shopify_id == "") {
        $errors = array();
        foreach ($shopifycreate['errors'] as $value) {
            $errors[] = $value;
        }

        // return to manage products
        $post = $POST;
        $post['shopify_return'] = $shopifycreate;
        $post['errors'] = $errors;
        $post['user_id'] = $currentuserid;
        $post['user_product_id'] = $products_id;
        $export = var_export($post, true);
        wp_mail("cnelson@ryankikta.com", "shopify add product error", $export);
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else {
        $sql = "UPDATE `wp_users_products` SET `shopify_id` = $shopify_id ,`type`='$shopifytype',`vendor`='$shopifyvendor',`collection_id`= '$collection_id',`active` = 1  WHERE `id` = $products_id";
        $query = $wpdb->get_result($sql);
        if (!$query) {
            $errors = array();
            $logs['sql'] = mysql_escape_string($sql);
            /*wp_insert_post(array(
			  'post_content' => var_export($logs, true),
			  'post_title' => esc_sql("Error updating shopify product id"),
			  'post_status' => 'draft',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_type' => 'systems'
			  ));*/
            $post = $POST;
            $post['errors'][] = "error in our end";
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $products_id;
            $export = var_export($post, true);
            $errors[] = "An error occured in update shopify data. ";
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        foreach ($shopifycreate['variants'] as $array) {
            $option1 = $array['option1'];
            $option2 = $array['option2'];
            $option3 = $array['option3'];
            $color_id = get_color_id($option1);
            if ($color_id == "")
                $color_id = get_color_id($option2);
            if ($color_id == "")
                $color_id = get_color_id($option3);

            $size_id = get_size_id($option1);
            if ($size_id == "")
                $size_id = get_size_id($option2);
            if ($size_id == "")
                $size_id = get_size_id($option3);

            $thisid = $array['id'];
            $sql = "UPDATE `wp_users_products_colors` SET `shopify_id` = '$thisid' WHERE `color_id` = '$color_id' AND `size_id` = '$size_id' AND `users_products_id` = $products_id";
            $query = $wpdb->get_result($sql);
            if (!$query) {

                $logs['sql'] = mysql_escape_string($sql);
                $logs['shopifycreate'] = $shopifycreate;
                /*wp_insert_post(array(
				  'post_content' => var_export($logs, true),
				  'post_title' => esc_sql("Error updating shopify variant"),
				  'post_status' => 'draft',
				  'comment_status' => 'closed',
				  'ping_status' => 'closed',
				  'post_type' => 'systems'
				  ));*/
                $post = $POST;
                $post['errors'][] = "error in our end";
                $post['user_id'] = $currentuserid;
                $post['user_product_id'] = $products_id;
                $export = export($post, true);
                $errors[] = "An error occured in update shopify variants. ";
                wp_mail("cnelson@ryankikta.com", "shopify add variants error", $export);
                $_SESSION['data'] = $POST;
                $return = array("status" => 0, 'errors' => $errors);
                echo json_encode($return);
                exit();
            }
        }
        return $shopify_id;
    }
}

function updateShopifyProduct($POST, $shopify_data, $currentuserid, $productid, $product_id, $allcolors, $allsizes, $variants, $variants3, $shopify_product_id, $description, $variantsid)
{
    global $wpdb;
    @extract($shopify_data);
    $currentvariants = array();
    // Get current collections
    $the_collections = $wpdb->get_var("select `collection_id` from `wp_users_products` where `id` = $productid");
    $the_shopify_id = $shopify_product_id;
    $explode = ($the_collections) ? explode(",", $the_collections) : array();
    foreach ($explode as $useless => $the_collection_id) {
        if (!in_array($the_collection_id, $POST['collection'])) {
            // No longer should be in that collection so delete.
            // First get all products in that collection
            $allcollects = ShopifyApiCall("GET", "/admin/api/2020-04/collects.json?product_id=" . $the_shopify_id);
            foreach ($allcollects as $useless => $array) {
                if ($array['collection_id'] == $the_collection_id) {
                    // Delete this one
                    ShopifyApiCall("DELETE", "/admin/api/2020-04/collects/" . $array['id'] . ".json");
                }
            }
        }
    }

    // Do we need to add it to a new collection?
    foreach ($POST['collection'] as $useless => $the_collection_id) {
        if (!in_array($the_collection_id, $explode)) {
            // Add this product to this collection
            $collect = array("custom_collection" => array("collects" => array(array("product_id" => $the_shopify_id))));
            $shopifycollect = ShopifyApiCall("PUT", "/admin/api/2020-04/custom_collections/$the_collection_id.json", $collect);
        }
    }


    $list = get_html_translation_table(HTML_ENTITIES);
    unset($list['"']);
    unset($list['<']);
    unset($list['>']);
    unset($list['&']);

    $updatedescription = strtr($description, $list);
    $path = "/admin/api/2020-04/products/$the_shopify_id.json";
    $shopifycreate1 = ShopifyApiCall("GET", $path, null);
    $options = $shopifycreate1['options'];

    // Update the product
    $charge = array(
        "product" => array
        (
            "title" => str_replace('"', '\"', stripslashes($POST['title'])),
            "body_html" => $updatedescription,
            "vendor" => str_replace('"', '\"', stripslashes($POST['shopifyvendor'])),
            "product_type" => str_replace('"', '\"', stripslashes($POST['shopifytype'])),
            "tags" => str_replace('"', '\"', stripslashes($POST['tags'])),
            "variants" => $variants,
            "options" => $options
        )
    );
    $shopifycreate = ShopifyApiCall("PUT", "/admin/api/2020-04/products/$the_shopify_id.json", $charge);

    if (isset($shopifycreate['errors'])) {
        $errors = array();
        $error_title = 'Error Update Product in Shopify :';
        foreach ($shopifycreate['errors'] as $value) {
            $errors[] = $value;
        }
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, 'error_title' => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
    if ($shopifycreate['id'] == "") {
        $errors = array();
        foreach ($shopifycreate['errors'] as $value) {
            $errors[] = $value;
        }
        // return to manage products
        $post = $POST;
        $post['errors'] = $errors;
        $post['user_id'] = $currentuserid;
        $post['user_product_id'] = $productid;
        $post['variants'] = $variants;
        $post['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $post['charge4'] = $charge;
        $export = var_export($post, true);

        wp_mail("cnelson@ryankikta.com", "shopify edit products error", $export);
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else {

        $shopify_id = $shopifycreate['id'];
        foreach ($shopifycreate['variants'] as $useless => $array) {

            $color_name = $array['option2'];
            $size_name = $array['option1'];

            $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $color_name . '"');
            $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"');
            foreach ($colors_id as $color) {
                $color_id = $color->color_id;
                foreach ($sizes_id as $size) {
                    $size_id = $size->size_id;
                    $count = $wpdb->get_var("select count(id) from wp_users_products_colors where users_products_id=$productid and color_id=$color_id and size_id=$size_id");
                    if ($count > 0) {
                        $thiscolorid = $color_id;
                        $thissizeid = $size_id;
                        break 2;
                    }
                }
            }

            $thisid = $array['id'];
            $sql = "update wp_users_products_colors set shopify_id='" . $thisid . "' where users_products_id='$productid' and color_id='$thiscolorid' and size_id='$thissizeid' ";
            $query = $wpdb->query($sql);
            //when adding new color and make it default it ll send it without id and thus the position filed will not take effect
            //the solution is 2 call.
            foreach ($variants as $key => $newvariant) {
                if ($newvariant['color_id'] == $thiscolorid && $newvariant['size_id'] == $thissizeid) {
                    $variants[$key]['id'] = $thisid;
                }
            }
        }
        if ($import == 1)
            $variants3 = array();
        if (!empty($variants3)) {
            $charge = array(
                "product" => array(
                    "variants" => $variants
                )
            );
            $shopifycreate4 = ShopifyApiCall("PUT", "/admin/api/2020-04/products/$the_shopify_id.json", $charge);
            //mail("team@ryankikta.com","update variants",var_export(array('charge'=>$charge,'shopifycreate4'=>$shopifycreate4),true));
            if (isset($shopifycreate4['errors'])) {
                $errors = array();
                $error_title = 'Error update product variants in shopify :';
                foreach ($shopifycreate4['errors'] as $value) {
                    $errors[] = $value;
                }
                $_SESSION['data'] = $POST;
                $return = array("status" => 0, 'error_title' => $error_title, 'errors' => $errors);
                echo json_encode($return);
                exit();
            }
        }

        // Save it back to mysql
        $sql = "UPDATE `wp_users_products` SET `shopify_id` = $shopify_id ,`type`='$shopifytype',`vendor`='$shopifyvendor',`collection_id`= '$collection_id',`active` = 1  WHERE `id` = $productid";
        $query = $wpdb->get_result($sql);
        if (!$query) {
            $errors = array();
            $logs['sql'] = mysql_escape_string($sql);
            /*wp_insert_post(array(
			  'post_content' => var_export($logs, true),
			  'post_title' => esc_sql("Error updating shopify product id"),
			  'post_status' => 'draft',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_type' => 'systems'
			  ));*/
            $post = $POST;
            $post['errors'][] = "error in our end";
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $productid;
            $post['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $post['charge5'] = $charge;
            $export = export($post, true);
            $errors[] = "An error occured in update shopify data. ";

            wp_mail("cnelson@ryankikta.com", "shopify updating shopify product id error", $export);
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'error_title' => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        return $shopify_id;
    }
}

function update_max_shopify_variant($user_id, $product_id, $shopify_id, $auth, $total_variant, $err, $tt)
{
    global $wpdb, $sc;
    if ($shopify_id != 0 && !empty($auth)) {

        $path = "/admin/api/2020-04/products/" . $shopify_id . ".json";
        $shopify_data = ShopifyApiCall("GET", $path, null, $user_id);
        $count_variant = count($shopify_data['variants']);
        if ($count_variant != 0) {
            foreach ($shopify_data['variants'] as $variant) {
                $color_name = $variant['option1'];
                $size_name = $variant['option2'];
                $color_id = $wpdb->get_var("select `color_id` from `wp_rmproductmanagement_colors` where color_name='$color_name'");
                $size_id = $wpdb->get_var("select `size_id` from `wp_rmproductmanagement_sizes` where size_name='$size_name'");
                if ($color_id == null || $size_id == null) {
                    $color_name = $variant['option2'];
                    $size_name = $variant['option1'];
                    $color_id = $wpdb->get_var("select `color_id` from `wp_rmproductmanagement_colors` where color_name='$color_name'");
                    $size_id = $wpdb->get_var("select `size_id` from `wp_rmproductmanagement_sizes` where size_name='$size_name'");
                }
                $variant_id = $variant['id'];
                $old_variant_id = $wpdb->get_var("select shopify_id from wp_users_products_colors where users_products_id='$product_id' and color_id='$color_id' and size_id='$size_id'");
                $total_variant++;
                if ($variant_id == $old_variant_id)
                    $tt++;
                if ($variant_id != $old_variant_id && $old_variant_id != null) {
                    $sql = "update wp_users_products_colors set shopify_id='" . $variant_id . "' where users_products_id='$product_id' and color_id='$color_id' and size_id='$size_id' ";
                    $query = $wpdb->query($sql);
                    if (!$query) {
                        echo 'sql error in product_id(' . $product_id . '),color/size= ' . $color_id . '/' . $size_id . ' : ' . mysql_error() . '<br>';
                        $err++;
                    }
                }
            }
        }
    } else {
        echo 'shopify_id is set a 0 (' . $shopify_id . ') where user_id=' . $user_id . ' and product_id=' . $product_id . '<br>';
    }
    return array($total_variant, $err, $tt);
}

function delete_color_from_shopify($product_id, $user_id, $color_id)
{
    global $wpdb, $sc;
    $results = $wpdb->get_results("select wc.shopify_id,wc.sku from wp_users_products_colors wc left join wp_users_products wu on (wc.users_products_id=wu.id)"
        . "where wu.id='$product_id' and wu.users_id='$user_id' and wc.color_id='$color_id' ");
    foreach ($results as $result) {
        if ($result->shopify_id != '0') {
            $variant = ShopifyApiCall("DELETE", '/admin/api/2020-04/variants/' . $result->shopify_id . '.json', '', $user_id);
        }
    }
}

function delete_size_from_shopify($product_id, $user_id, $size_id)
{
    global $wpdb, $sc;
    $results = $wpdb->get_results("select wc.shopify_id from wp_users_products_colors wc left join wp_users_products wp on wc.users_products_id=wp.id where wp.id='$product_id' and wp.users_id='$user_id' and wc.size_id='$size_id'");
    foreach ($results as $result) {
        if ($result->shopify_id != 0) {
            $variant = ShopifyApiCall("DELETE", '/admin/api/2020-04/variants/' . $result->shopify_id . '.json', '', $user_id);
        }
    }
}

function delete_shopify_variant($product_id, $user_id, $color_id, $size_id, $auth, $variant_id)
{
    global $wpdb, $sc;
    if ($variant_id != 0) {
        @extract($auth);
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $path = "/admin/api/2020-04/variants/" . $variant_id . ".json";
        ShopifyApiCall("DELETE", $path, null, $user_id);
    }
}

function desactive_shopify_product($productId, $userId)
{
    global $sc;
    $charge = array('product' => array("id" => (int)$productId, 'published' => false));
    ShopifyApiCall("PUT", '/admin/api/2020-04/products/' . $productId . '.json', $charge, $userId);
}

/**********************************shopify order functions**********************************************/
function get_order_details($shopify_id, $auth, $user_id)
{
    global $sc;
    global $wpdb;

    $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
    $path = "/admin/api/2020-04/orders/" . $shopify_id . ".json";
    $order_details = ShopifyApiCall("GET", $path, null, $user_id);

    return $order_details;
}

function get_all_orders($auth, $user_id)
{
    global $wpdb;
    global $sc;

    $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
    $path = "/admin/api/2020-04/orders.json";
    $all_orders = ShopifyApiCall("GET", $path, null, $user_id);

    return $all_orders;
}


function get_order_shopify_data($json)
{
    $order_id = $json['id'];
    $order_name = $json['order_number'];
    $customerphone = $json['shipping_address']['phone'];
    if ($json['shipping_address']['country'] == "United States") {
        $shipping_id = 1;
    } elseif ($json['shipping_address']['country'] == "Canada") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }

    return array('shop_order_id' => $order_id, 'shop_order_name' => $order_name, 'customerphone' => $customerphone, 'shipping_id' => $shipping_id);
}

function get_all_item_shopify($user_id, $allitems)
{
    global $wpdb;
    $items = array();
    $user_product_id = 0;
    foreach ($allitems as $key => $value) {
        $product = array();
        $order_product_id = $value['product_id'];
        $item_id = $value['id'];
        $variant_id = $value['variant_id'];
        $variant_sku = $value['sku'];
        $item_price = $value['price'];
        $quantity = $value['quantity'];
        $variant_title = $value['variant_title'];
        $option = explode('/', $variant_title);
        $size_name = trim($option[0]);
        $color_name = trim($option[1]);
        if (isset($option[2]))
            $color_name .= " / " . trim($option[2]);
        $sku = str_replace('-' . $size_name, '', $value['sku']);
        $sku = explode('-', $sku);
        $slice = array_slice($sku, 0, count($sku) - 1);
        $sku = implode('-', $slice);
        $title = $value['title'];
        $vendor = $value['vendor'];
        if ($sku != "") {
            $where = ($user_id != 0) ? " and users_id=$user_id" : "";
            $sql = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where shopify_id = $order_product_id" . $where;
            $sql1 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where title='$title' and sku='$sku' and vendor='$vendor'" . $where;
            $sql2 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where title='$title' and  sku='$sku'" . $where;
            $sql3 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where sku='$sku'" . $where;

            $query = $wpdb->get_result($sql);
            $query1 = $wpdb->get_result($sql1);
            $query2 = $wpdb->get_result($sql2);
            $query3 = $wpdb->get_result($sql3);
            $product = mysql_fetch_assoc($query);
            if (!$product) {
                $product = mysql_fetch_assoc($query1);
                if (!$product)
                    $product = mysql_fetch_assoc($query2);
                if (!$product)
                    $product = mysql_fetch_assoc($query3);
            }
            if ($product) {
                $pa_product_id = $product['id'];
                $product_id = $product['product_id'];
                $user_product_id = $product['users_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                if ($pa_product_id > 0) {
                    if ($variant_id == NULL)
                        $where = "sku='$variant_sku'";
                    else
                        $where = "(shopify_id=$variant_id or sku='$variant_sku')";
                    $query = $wpdb->get_result("select color_id,size_id from wp_users_products_colors where $where and users_products_id=" . $pa_product_id);
                    $row = mysql_fetch_array($query);
                    $color_id = $row['color_id'];
                    $size_id = $row['size_id'];
                    if ($color_id == NULL || $size_id == NULL) {
                        $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $color_name . '"');
                        $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"');
                        foreach ($colors_id as $color) {
                            $color_id = $color->color_id;
                            foreach ($sizes_id as $size) {
                                $size_id = $size->size_id;
                                $count_item = $wpdb->get_var("select count(id) from wp_users_products_colors where users_products_id=$pa_product_id and color_id=$color_id and size_id=$size_id");
                                if ($count_item > 0)
                                    break 2;
                            }
                        }
                    }
                    if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0)
                        $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
            } else {
                $pa_variants = $wpdb->get_row("select users_products_id,color_id,size_id from wp_users_products_colors where (shopify_id=$variant_id or sku='$variant_sku')");
                $pa_product_id = $pa_variants->users_products_id;
                $color_id = $pa_variants->color_id;
                $size_id = $pa_variants->size_id;
                $products_fb = $wpdb->get_results("select type,image_id from wp_users_products_images where type<>4 and users_products_id=$pa_product_id order by type asc");
                $hasfront = 0;
                $hasback = 0;
                foreach ($products_fb as $prod) {
                    $image_id = $prod->image_id;
                    $user_product_id = $wpdb->get_var("select userID from wp_userfiles where fileID=$image_id");
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
            }
        }
    }
    if (count($items) == 1) {
        if ($product_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $product_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12)
                $items [0]['only_shirts'] = true;
            else
                $items [0]['only_shirts'] = false;
        }
    }

    if ($user_id == 0 && $user_product_id != 0 && count($items) > 0)
        $items['user_id'] = $user_product_id;

    return $items;
}

function get_all_item_shopify_shopId($user_id, $allitems, $shop_id)
{
    global $wpdb;
    $items = array();
    $user_product_id = 0;
    foreach ($allitems['line_items'] as $key => $value) {
        // debug($value);
        $product = array();
        $order_product_id = $value['product_id'];
        $item_id = $value['id'];
        $variant_id = $value['variant_id'];
        $variant_sku = $value['sku'];
        $item_price = $value['price'];
        $variant_title = $value['variant_title'];
        $option = explode('/', $variant_title);
        $size_name = trim($option[0]);
        $color_name = trim($option[1]);

        if (isset($option[2])) {
            $color_name .= " / " . trim($option[2]);
        }
        $sku = str_replace('-' . $size_name, '', $value['sku']);
        $sku = explode('-', $sku);
        $slice = array_slice($sku, 0, count($sku) - 1);
        $sku = implode('-', $slice);
        $title = $value['title'];
        $vendor = $value['vendor'];
        $quantity = $value['quantity'];

        $old_version = false;
        $where = ($user_id != 0) ? " and users_id=$user_id" : "";
        if ($shop_id != 0) {
            $pa_product_id = get_product_id_meta_shop($order_product_id, "shopify_id", $shop_id);
            debug($pa_product_id);
            //debug($order_product_id);
            if (isset($pa_product_id) && ($pa_product_id != "")) {
                $query_product = $wpdb->get_result("select id,users_id,brand_id,product_id,front,back  from wp_users_products WHERE `id` =$pa_product_id " . $where);
                $product = mysql_fetch_assoc($query_product);
                //debug($product);
                if (!$product)
                    $old_version = true;
            } else
                $old_version = true;
        } else
            $old_version = true;
        if ($old_version) {
            if ((intval($variant_id) != 0) && (intval($order_product_id) != 0)) {
                $sql = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where shopify_id = $order_product_id" . $where;
                $query = $wpdb->get_result($sql);
                $product = mysql_fetch_assoc($query);
            }
            if ($sku != "" && !$product) {
                $sql1 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where title='$title' and sku='$sku' and vendor='$vendor'" . $where;
                $query1 = $wpdb->get_result($sql1);

                $product = @mysql_fetch_assoc($query1);
                if ($product == false) {
                    $sql2 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where title='$title' and  sku='$sku'" . $where;
                    $query2 = $wpdb->get_result($sql2);
                    $product = mysql_fetch_assoc($query2);

                    if ($product == false) {
                        $sql3 = "select id,users_id,brand_id,product_id,front,back  from wp_users_products where sku='$sku'" . $where;
                        $query3 = $wpdb->get_result($sql3);
                        $product = mysql_fetch_assoc($query3);
                    }
                }
            }
        }
        debug($product);
        if ($product) {
            $pa_product_id = $product['id'];
            $user_product_id = $product['users_id'];
            $product_id = $product['product_id'];
            $brand_id = $product['brand_id'];
            $hasfront = $product['front'];
            $hasback = $product['back'];
            if ($pa_product_id > 0) {
                //to insert or update shopify_product_id to pa_product_id per shop for product that shopify_product_id has been delete
                if (($old_version) && ($shop_id != 0)) {
                    update_product_meta_shop($pa_product_id, "shopify_id", $order_product_id, $shop_id);
                }
                $update_variant = false;
                $where = null;
                if (($variant_id == NULL) && ($variant_sku != "") && (isset($variant_sku))) {
                    $where = "sku='$variant_sku'";
                } else {
                    if ($shop_id != 0) {
                        $pa_variant_id = get_pa_variant_meta_shop($pa_product_id, 'shopify_id', $variant_id, $shop_id);
                        if (isset($pa_variant_id) && ($pa_variant_id != "")) {
                            $where = "id='$pa_variant_id'";
                        } else {
                            $where = "shopify_id=$variant_id";
                        }
                    } else {
                        $where = "shopify_id=$variant_id";
                    }
                }
                $count_variant_query = 0;
                if ($where != null) {
                    //debug("select color_id,size_id,id from wp_users_products_colors where $where and users_products_id = $pa_product_id limit 1");
                    $variant_query = $wpdb->get_result("select color_id,size_id,id from wp_users_products_colors where $where and users_products_id = $pa_product_id limit 1");
                    $count_variant_query = $wpdb->num_rows($variant_query);
                }

                if ($count_variant_query > 0) {
                    $pa_variant = mysql_fetch_assoc($variant_query);
                    $color_id = $pa_variant['color_id'];
                    $size_id = $pa_variant['size_id'];
                    if ($update_variant == true) {
                        //to insert or update shopify_variant_id to pa_product_id per shop for product that shopify_variant_id has been delete
                        $pa_variant_id = $pa_variant["id"];
                        update_variant_meta_shop($pa_product_id, $pa_variant_id, 'shopify_id', $variant_id, $shop_id);
                    }
                } else {
                    if ($color_name == 'yellow') $color_name = 'Daisy';
                    $colors_id = get_colors_col($color_name);
                    $sizes_id = get_sizes_col($size_name);
                    $size_name = str_replace(array("L", "XL", "xl", "2XL", "2xl", "3XL", "3xl", "4XL", "4xl", "5XL", "5xl"), array("Large", "X-Large", "X-Large", "2X-Large", "2X-Large", "3X-Large", "3X-Large", "4X-Large", "4X-Large", "5X-Large", "5X-Large"), $size_name);
                    $sizes_id = array_merge($sizes_id, get_sizes_col($size_name));
                    debug($colors_id);
                    debug($sizes_id);
                    if ((count($sizes_id) > 0) && (count($colors_id) > 0)) {
                        //debug("select color_id,size_id,id from wp_users_products_colors where `color_id` in (" . implode(",", $colors_id) . ") AND `size_id` in (" . implode(",", $sizes_id) . ") and users_products_id = $pa_product_id limit 1");
                        $variant_query = $wpdb->get_result("select color_id,size_id,id from wp_users_products_colors where `color_id` in (" . implode(",", $colors_id) . ") AND `size_id` in (" . implode(",", $sizes_id) . ") and users_products_id = $pa_product_id limit 1");
                        if ($wpdb->num_rows($variant_query) > 0) {
                            $pa_variant = mysql_fetch_assoc($variant_query);
                            $color_id = $pa_variant['color_id'];
                            $size_id = $pa_variant['size_id'];
                            if ($update_variant == true) {
                                //to insert or update shopify_variant_id to pa_product_id per shop for product that shopify_variant_id has been delete
                                $pa_variant_id = $pa_variant["id"];
                                update_variant_meta_shop($pa_product_id, $pa_variant_id, 'shopify_id', $variant_id, $shop_id);
                            }
                        }
                    }
                }
                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                    $items[] = array('item_id' => $item_id, 'variant_id' => $variant_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
            }
        } else {
            $update_variant = false;
            $where = null;
            if ($shop_id != 0) {
                $pa_variant_id = get_pa_variant_id_meta_shop('shopify_id', $variant_id, $shop_id);
                if (isset($pa_variant_id) && ($pa_variant_id != "")) {
                    $where = "id='$pa_variant_id'";
                } else {
                    $update_variant = true;
                    if (isset($variant_id) && ($variant_id != Null)) {
                        $where = "shopify_id=$variant_id";
                    } else {
                        if (($variant_sku != "") && ($variant_sku != Null)) {
                            $where = "sku='$variant_sku'";
                        }
                    }
                }
            } else {
                if (isset($variant_id) && ($variant_id != Null)) {
                    $where = "shopify_id=$variant_id";
                } else {
                    if (($variant_sku != "") && ($variant_sku != Null)) {
                        $where = "sku='$variant_sku'";
                    }
                }
            }
            if (isset($where) && ($where != NULL)) {
                $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id,id from wp_users_products_colors where " . $where);
            } else {
                $pa_variants = null;
            }
            if (!empty($pa_variants)) {
                $pa_variants = (object)$pa_variants[0];
                $pa_product_id = $pa_variants->users_products_id;
                $color_id = $pa_variants->color_id;
                $size_id = $pa_variants->size_id;
                if (($update_variant == true) && ($shop_id != 0)) {
                    //to insert or update shopify_product_id to pa_product_id per shop for product that shopify_product_id has been delete
                    update_product_meta_shop($pa_product_id, "shopify_id", $order_product_id, $shop_id);
                    //to insert or update shopify_variant_id to pa_product_id per shop for product that shopify_variant_id has been delete
                    $pa_variant_id = $pa_variants->id;
                    update_variant_meta_shop($pa_product_id, $pa_variant_id, 'shopify_id', $variant_id, $shop_id);
                }
                $products_fb = $wpdb->get_results("select type,image_id from wp_users_products_images where type<>4 and users_products_id = $pa_product_id order by type asc");
                $hasfront = 0;
                $hasback = 0;
                foreach ($products_fb as $prod) {
                    $prod = (object)$prod;
                    $image_id = $prod->image_id;
                    $user_product_id = $wpdb->get_var("select userID from wp_userfiles where fileID=$image_id");
                    if (in_array($prod->type, array(1, 2)))
                        $hasfront = 1;
                    if (in_array($prod->type, array(3, 5)))
                        $hasback = 1;
                }
                $product_id = get_product_meta($pa_product_id, "inventory_id");
                if ($product_id) {
                    $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=$product_id");
                }

                if ($pa_product_id > 0 && $size_id > 0 && $color_id > 0) {
                    $items[] = array('item_id' => $item_id, 'variant_id' => $variant_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
            }
        }
    }
    if (count($items) == 1) {
        if ($product_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $product_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12)
                $items [0]['only_shirts'] = true;
            else
                $items [0]['only_shirts'] = false;
        }
    }
    if ($user_id == 0 && $user_product_id != 0 && count($items) > 0) {
        $items['user_id'] = $user_product_id;
    }
    return $items;
}

function shopify_Shipping_address($json)
{

    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = trim($json['first_name'] . " " . $json['last_name']);
    $shippingaddress1['address1'] = $json['address1'];
    $shippingaddress1['address2'] = $json['address2'];
    $shippingaddress1['city'] = $json['city'];
    $shippingaddress1['state'] = $json['province'];
    $shippingaddress1['zipcode'] = $json['zip'];
    $shippingaddress1['country'] = $json['country_code'];
    $address2 = ($shippingaddress1['address2'] != "") ? $shippingaddress1['address2'] . "\n" : "";
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $json['country'];
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);

    $shippingaddress_zip = $json['zip'];

    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $json['country'], 'shippingaddress_state' => $json['province'], 'shippingaddress_state_code' => $json['province_code'], 'shippingaddress_zip' => $shippingaddress_zip, 'paypal_address' => $paypal_address);
}

function get_shopify_shop_url($user_id)
{
    $query = $wpdb->get_result("select shop from wp_users_shopify where users_id=" . $user_id);
    $row = mysql_fetch_array($query);
    return $row[0];
}

function get_fulfillment_order($shopify_id, $auth, $user_id)
{
    try {
        if ($shopify_id != 0 && !empty($auth)) {
            $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
            $path = "/admin/api/2020-04/orders/" . $shopify_id . "/fulfillments.json";

            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'get_fulfillment_order';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'get fullfillment order';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }

            $response = ShopifyApiCall("GET", $path, null, $user_id);

            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'get_fulfillment_order';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'completed get fullfillment order';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['response'] = var_export($response, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }
        } else {
            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'get_fulfillment_order';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'did not update';
                $debug['shopify_id'] = var_export($shopify_id, true);
                $debug['auth'] = var_export($auth, true);
                $debug['response'] = var_export(($shopify_id != 0 && !empty($auth)), true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }
        }
    } catch (exception $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'get_fulfillment_order';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'fullfillment order get exception';
            $debug['path'] = var_export($path, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['response'] = var_export($response, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
    }
    return $response;
}

function create_fulfillment_order($shopify_id, $auth, $user_id, $tracking = null, $location_id, $product_ids)
{
    global $sc;
    try {
        $return = array();
        if ($shopify_id != 0 && !empty($auth)) {
            $fulfill_data = array("fulfillment" => array(
                "tracking_number" => $tracking,
                "location_id" => $location_id,
                "notify_customer" => true,
                "line_items" => $product_ids
            ));
            $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
            $path = "/admin/api/2020-04/orders/" . $shopify_id . "/fulfillments.json";

            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'create_fulfillment_order';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'create fullfillment order';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }

            $return = ShopifyApiCall("POST", $path, $fulfill_data, $user_id);

            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'create_fulfillment_order';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'completed create fullfillment order';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['response'] = var_export($response, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            } else {
                if (DEBUG_LEVEL >= 2) {
                    unset($debug);
                    $debug['function'] = 'create_fulfillment_order';
                    $debug['parent'] = 'shopify-functions.php';
                    $debug['section'] = 'did not update';
                    $debug['shopify_id'] = var_export($shopify_id, true);
                    $debug['auth'] = var_export($auth, true);
                    $debug['response'] = var_export(($shopify_id != 0 && !empty($auth)), true);
                    $debug['backtrace'] = debug_backtrace();
                    error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
                }
            }

            wp_mail("team@ryankikta.com", "shopify functions create fullfillment response", var_dump($return));
        }
    } catch (Exception $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'create_fulfillment_order';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'fullfillment order create exception';
            $debug['path'] = var_export($path, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['response'] = var_export($response, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
    }

    return $return;
}

function update_fulfillment_order_item($shopify_id, $fulfill_id, $auth, $user_id, $data)
{
    try {
        if ($shopify_id != 0 && !empty($auth)) {
            $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
            $path = '/admin/api/2020-04/rders/' . $shopify_id . '/fulfillments/' . $fulfill_id . '.json';
            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'update_fulfillment_order_item';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'start update';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }

            $response = ShopifyApiCall("PUT", $path, $data, $user_id);

            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'update_fulfillment_order_item';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'completed update';
                $debug['path'] = var_export($path, true);
                $debug['user_id'] = var_export($user_id, true);
                $debug['response'] = var_export($response, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }
            wp_mail("team@ryankikta.com", "shopify functions update fullfillment response", var_dump($response));
        } else {
            if (DEBUG_LEVEL >= 2) {
                unset($debug);
                $debug['function'] = 'update_fulfillment_order_item';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'did not update';
                $debug['shopify_id'] = var_export($shopify_id, true);
                $debug['auth'] = var_export($auth, true);
                $debug['response'] = var_export(($shopify_id != 0 && !empty($auth)), true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }
        }
    } catch (Exception $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'update_fulfillment_order_item';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'update exception';
            $debug['path'] = var_export($path, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['response'] = var_export($response, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
    }

    return $response;
}

function cancel_fulfillment_order($shopify_id, $fulfill_id, $auth, $user_id)
{
    if ($shopify_id != 0 && !empty($auth)) {
        $sc = new ShopifyClient($auth['shop'], $auth['token'], Shopify_Key, Shopify_Secret);
        $path = '/admin/api/2020-04/orders/' . $shopify_id . '/fulfillments/' . $fulfill_id . '/cancel.json';
        $response = ShopifyApiCall("POST", $path, null, $user_id);
    }
    return $response;
}

function regenerate_shopify_orderv2($shopify_order_id, $user_id, $type = 0)
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where users_id = $user_id ", ARRAY_A);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);
    $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$shopify_order_id.json", array(), $sc, $user_id);
    $url = 'https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=' . $user_id;
    if ($type == 1)
        $url .= '&send_type=1';
    $data_to_send = json_encode($jsons);
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
    debug($return);
}

function regenerate_shopify_orderv3($shopify_order_id, $user_id, $type = 1)
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where users_id = $user_id ", ARRAY_A);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);
    if ($type == 1) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$shopify_order_id.json", array(), $sc, $user_id);
    } elseif ($type == 2) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?status=any&name=$shopify_order_id", array(), $sc, $user_id);
    }
    debug($jsons);
    $data_to_send = json_encode($jsons);
    exit();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    $headers = array(
        "Cache-Control: no-cache",
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, 'https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=' . $user_id);
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
    debug($return);
}

function regenerate_shopify_orderv4($shopify_order_id, $shop_id, $type = 1)
{
    global $wpdb;
    ///$shopify_order_id
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    //debug($user);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);
    if ($type == 1) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$shopify_order_id.json", array(), $sc, $user['users_id']);
    } elseif ($type == 2) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?status=any&name=$shopify_order_id", array(), $sc, $user['users_id']);
    }
    //return $jsons;
    debug($jsons);

    $order_data = ($type == 1) ? $jsons : $jsons[0];
    $data_to_send = json_encode($order_data);
    //debug($data_to_send);
    exit;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    $headers = array(
        "Cache-Control: no-cache",
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, 'https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=' . $user['users_id'] . "&id_shop=" . $shop_id);
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
    debug($return);
}

function analyze_shopify_order_v4($shopify_order_id, $shop_id, $type = 1)
{
    global $wpdb;
    ///$shopify_order_id
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    //debug($user);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);
    if ($type == 1) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$shopify_order_id.json", array(), $sc, $user['users_id']);
    } elseif ($type == 2) {
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?status=any&name=$shopify_order_id", array(), $sc, $user['users_id']);
    }


    $json = ($type == 1) ? $jsons : $jsons[0];
    debug($json);
    $data = get_order_shopify_data($json);
    //debug(  $data );exit;
    @extract($data);
    $allitems = get_all_item_shopify_shopId($user_id, $json, $shop_id);
    debug($allitems);
    exit();
}

function regenerate_shopify_orders($shop_id)
{
    global $wpdb;
    ///$shopify_order_id
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);

    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);

    $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json", array(), $sc, $user['users_id']);

    //return $jsons;

    foreach ($jsons as $json) {

        $data_to_send = json_encode($json);
        // debug($data_to_send);
        // exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        $headers = array(
            "Cache-Control: no-cache",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=' . $user['users_id'] . "&id_shop=" . $shop_id);
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
        debug($return);
    }
}

/***********************************Shopify API functions**********************************************/

function ShopifyApiCall($action, $path, $data = NULL, $user_id = "")
{
    global $sc, $wpdb;

    if (DEBUG_LEVEL >= 2) {
        unset($debug);
        $debug['function'] = 'ShopifyApiCall';
        $debug['parent'] = 'shopify-functions.php';
        $debug['section'] = 'Entering ShopifyApiCall';
        $debug['action'] = var_export($action, true);
        $debug['path'] = var_export($path, true);
        $debug['data'] = var_export($data, true);
        $debug['user_id'] = var_export($user_id, true);
        $debug['backtrace'] = debug_backtrace();
        error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
    }

    $success = 1;
    $error_sh = array();
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $currentusername = $current_user->user_login;
    if ($user_id != "" && is_numeric($user_id))
        $currentuserid = $user_id;
    try {
        check_shopify_call_limit($currentuserid);
        $screturn = $sc->call($action, $path, $data);

        if (DEBUG_LEVEL >= 2) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'Leaving ShopifyApiCall';
            $debug['returns'] = var_export($screturn, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }

        return $screturn;
    } catch (ShopifyApiException $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'shopify api Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }

        // If you're here, either HTTP status code was >= 400 or response contained the key 'errors'
        $error_rt = array();
        $map_fields = array('vendor' => 'Vendor', 'product_type' => 'Product Type', 'title' => 'Title', 'body_html' => 'Description', 'tags' => 'Tags', 'base' => '');
        $return = array();
        $error_sh['user_id'] = $currentuserid;
        $error_sh['method'] = $e->getMethod();
        $error_sh['path'] = $e->getPath();
        $error_sh['responseHeader'] = $e->getResponseHeaders();
        $http_code = $error_sh['responseHeader']['http_status_code'];
        $error_rt['httpd_code'] = $error_sh['responseHeader']['http_status_message'];
        $error_sh['response'] = $e->getResponse();
        $current_user = wp_get_current_user();
        $error_sh['params'] = $e->getParams();
        $export = var_export($error_sh, TRUE);
        /* wp_insert_post(array(
		   'post_title' => $wpdb->escape($error_sh['responseHeader']['http_status_message'] . '(user : ' . $currentuserid . ',' . $currentusername . ')'),
		   'post_content' => $wpdb->escape($export),
		   'post_status' => 'draft',
		   'post_type' => 'systems'
		   ));*/
        $sucess = 0;
        switch ($http_code) {
            case '401' :
                $return['errors'][] = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $return['errors'][] = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $return['errors'][] = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $return['errors'][] = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '422' :
                if (is_array($error_sh['response']['errors'])) {
                    foreach ($error_sh['response']['errors'] as $key => $errors) {
                        foreach ($errors as $error) {
                            if ($error == 'Exceeded maximum number of variants allowed')
                                $return['errors'][] = 'Shopify only allows products to have up to 100 variants. Please note that each color and specific size counts as a variant. You are most likely getting this error if you choose too many color options for one product';
                            else if ($error == 'Option Color cannot be blank') {
                                $return['errors'][] = 'Please choose at least 1 color';
                            } else if ($error == '1 option values given but 2 options exist') {

                            } else
                                $return['errors'][] = $map_fields[$key] . ' ' . $error;
                        }
                    }
                } else
                    $return['errors'][] = $error_sh['response']['errors'];
                break;
        }
        return $return;
    } catch (ShopifyCurlException $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'shopify curl Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
        $sucess = 0;
    } catch (Exception $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'General Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
        $sucess = 0;
    }

    if (DEBUG_LEVEL >= 2) {
        unset($debug);
        $debug['function'] = 'ShopifyApiCall';
        $debug['parent'] = 'shopify-functions.php';
        $debug['section'] = 'Leaving ShopifyApiCall';
        $debug['returns'] = var_export($returns, true);
        $debug['backtrace'] = debug_backtrace();
        error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
    }

    if ($sucess == 1) {
        return $returns;
    } else {
        return FALSE;
    }
}


//2019-1-18 - This Function is the one that currently used talk tio shopify - JB
function ShopifyApiCall1($action, $path, $data = NULL, $sc, $user_id = "")
{
    global $wpdb;
    $success = 1;
    $error_sh = array();
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $currentusername = $current_user->user_login;
    if ($user_id != "" && is_numeric($user_id))
        $currentuserid = $user_id;
    try {
        check_shopify_call_limit($currentuserid);
	if(strpos($path, 'admin/products.json')!== false || strpos($path, 'admin/orders.json') !== false){
		wp_mail('jbuck@ryankikta.com', 'FOUND old calls', $path);
	}
        $return = $sc->call($action, $path, $data);
	//$sc->getLastCallHeader();
	//error_log("GetLastCallHeader: \n");
	//error_log($sc->getLastCallHeader);
        //debug($return);
        return $return;
    } catch (ShopifyApiException $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall1';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'shopify api Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }

        // If you're here, either HTTP status code was >= 400 or response contained the key 'errors'
        $error_rt = array();
        $map_fields = array('vendor' => 'Vendor', 'product_type' => 'Product Type', 'title' => 'Title', 'body_html' => 'Description', 'tags' => 'Tags', 'base' => '');
        $return = array();
        $error_sh['user_id'] = $currentuserid;
        $error_sh['method'] = $e->getMethod();
        $error_sh['path'] = $e->getPath();
        $error_sh['responseHeader'] = $e->getResponseHeaders();
        $http_code = $error_sh['responseHeader']['http_status_code'];
        $error_rt['httpd_code'] = $error_sh['responseHeader']['http_status_message'];
        $error_sh['response'] = $e->getResponse();
        $error_sh['params'] = $e->getParams();
        $export = var_export($error_sh, TRUE);
        //debug($http_code);
        /*wp_insert_post(array(
		  'post_title' => $wpdb->escape($error_sh['responseHeader']['http_status_message'] . '(user : ' . $currentuserid . ',' . $currentusername . ')'),
		  'post_content' => $wpdb->escape($export),
		  'post_status' => 'draft',
		  'post_type' => 'logs'
		  ));*/
        $sucess = 0;
        switch ($http_code) {
            case '401' :
                $return['errors'][] = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $return['errors'][] = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $return['errors'][] = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $return['errors'][] = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '422' :
                if (is_array($error_sh['response']['errors'])) {
                    foreach ($error_sh['response']['errors'] as $key => $errors) {
                        foreach ($errors as $error) {
                            if ($error == 'Exceeded maximum number of variants allowed')
                                $return['errors'][] = 'Shopify only allows products to have up to 100 variants. Please note that each color and specific size counts as a variant. You are most likely getting this error if you choose too many color options for one product';
                            else if ($error == 'Option Color cannot be blank') {
                                $return['errors'][] = 'Please choose at least 1 color';
                            } else if ($error == '1 option values given but 2 options exist') {

                            } else
                                $return['errors'][] = $map_fields[$key] . ' ' . $error;
                        }
                    }
                } else
                    $return['errors'][] = $error_sh['response']['errors'];
                break;
        }
        return $return;
    } catch (ShopifyCurlException $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall1';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'shopify curl Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
        $sucess = 0;
    } catch (Exception $e) {
        if (DEBUG_LEVEL >= 1) {
            unset($debug);
            $debug['function'] = 'ShopifyApiCall1';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'General Exception';
            $debug['action'] = var_export($action, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($data, true);
            $debug['user_id'] = var_export($user_id, true);
            $debug['exception'] = var_export($e, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
        $sucess = 0;
    }

    if (DEBUG_LEVEL >= 2) {
        unset($debug);
        $debug['function'] = 'ShopifyApiCall';
        $debug['parent'] = 'shopify-functions.php';
        $debug['section'] = 'Leaving ShopifyApiCall';
        $debug['returns'] = var_export($returns, true);
        $debug['backtrace'] = debug_backtrace();
        error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
    }

    if ($sucess == 1) {
        return $returns;
    } else {
        return FALSE;
    }
}

function check_shopify_call_limit($currentuserid = "")
{
    if ($currentuserid == "" || !is_numeric($currentuserid)) {
        $current_user = wp_get_current_user();
        $currentuserid = $current_user->ID;
    }

    $query = $wpdb->get_result("SELECT `last_call_time`,`total_call` FROM `wp_users_shopify` WHERE `users_id` = $currentuserid");
    $rows = $wpdb->get_row($query);
    $lastcall = $rows[0];
    $total_call = $rows[1];
    $now = time();
    if ($lastcall == '0') {
        $lastcall = $now;
    }
    $Diff_Time = $now - $lastcall;
    $recup_call = floor(2 * $Diff_Time);
    if ($recup_call == 0) {
        $total_call = ($total_call != 0) ? $rows[1] : 1;
        if ($total_call >= 41) {
            $total_call++;
            $wpdb->get_result("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
            usleep(700000);
        } else {
            $total_call++;
            $wpdb->get_result("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        }
    } else if ($recup_call >= 40) {
        $wpdb->get_result("UPDATE `wp_users_shopify` SET `total_call` = 1 WHERE `users_id` = $currentuserid");
    } else {
        if ($total_call <= $recup_call) {
            $total_call++;
            $wpdb->get_result("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        } else {
            $total_call++;
            $wpdb->get_result("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        }
    }
    $wpdb->get_result("UPDATE `wp_users_shopify` SET `last_call_time` = '$now' WHERE `users_id` = $currentuserid");
}

function CheckShopify($currentuserid)
{

    global $sc;
    $checkuser = $wpdb->get_result("SELECT `shop`,`token`,`active` FROM `wp_users_shopify` WHERE `users_id` = $currentuserid");
    $numshopsshopify = $wpdb->num_rows($checkuser);
    $shoprow = $wpdb->get_row($checkuser);
    $shop = $shoprow[0];
    $token = $shoprow[1];
    $active = $shoprow[2];

    if ($numshopsshopify !== 0 && $active !== "0") {

        // Add to sellers shop using the details.
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $shopdetails = ShopifyApiCall("GET", '/admin/api/2020-04/shop.json');
        $i = 0;
        while (empty($shopdetails)) {
            $shopdetails = ShopifyApiCall("GET", '/admin/api/2020-04/shop.json');
            if ($i == 3)
                break;
            $i++;
        }
        if (empty($shopdetails)) {

            // No Connection
            // Get User Email
            $select = $wpdb->get_result("SELECT `user_email` FROM `wp_users` WHERE `ID` = $currentuserid");
            $rows = $wpdb->get_row($select);
            $user_email = $rows[0];

            // Send Email
            $headers = 'From: Ryan Kikta <team@ryankikta.com>' . "\r\n" .
                'Reply-To: Ryan Kikta <team@ryankikta.com>' . "\r\n" .
                'Bcc: team@ryankikta.com,cnelson@ryankikta.com' . "\r\n";

            //mail($user_email, 'Please re-authorize your Shopify shop at RyanKikta', "Just a heads up , We have noticed that RyanKikta is no longer Authorized to access your Shopify Shop ($shop) as a result, We have temporarily disabled your shop which means products ordered meanwhile from your shop will not be processed, \n\n You may re-authorize your shop by visiting https://ryankikta.com/shopify/ \n\n Let us know if you have any questions, \n Ryan Kikta Team",$headers);

            return 2;
        } else {
            return 1;
        }
    } else {
        return 0;
    }
}

function get_shopify_orders($user_id, $shop_id = 0, $page = 1, $order_id = "", $order_name = "")
{
    global $wpdb;

    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $user = $wpdb->get_row("select shop,token from wp_users_shopify where users_id = $user_id $where");
    $sc = new ShopifyClient($user->shop, $user->token, Shopify_Key, Shopify_Secret);
    if ($order_id)
        $orders = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$order_id.json", null, $sc, $user_id);
    elseif ($order_name)
        $orders = ShopifyApiCall1('GET', '/admin/api/2020-04/orders.json?name=' . $order_name, null, $sc, $user_id);
    else
        $orders = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?limit=250&page_info=", null, $sc, $user_id);
    return $orders;
}

function get_shopify_orders_by_date($shop_id, $since_date, $max_date = "", $page = 1, $per_page = 50)
{
    global $wpdb;


    $user = $wpdb->get_row("select shop,token from wp_users_shopify where id = $shop_id");
    $sc = new ShopifyClient($user->shop, $user->token, Shopify_Key, Shopify_Secret);

    // $orders = ShopifyApiCall1('GET', "/admin/orders.json?fields=id,number,name,line_items,created_at&created_at_min=$since_date&limit=10", null, $sc, $user_id);
    $orders = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?created_at_min=$since_date&created_at_max=$max_date&page_info=", null, $sc, $user_id);
    return $orders;
}

function analyze_shopify_order($shopify_order_id, $shop_id, $type = 1)
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    debug($user);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);
    $today = date("Y-m-d");
    //$json= ShopifyApiCall1('GET',"/admin/orders/352191757.json",array('limit'=>10),$sc,$user_id);
    if ($type == 1) {
        echo $shopify_order_id;
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders/$shopify_order_id.json", array(), $sc, $user['users_id']);
    } else if ($type == 2)
        $jsons = ShopifyApiCall1('GET', "/admin/api/2020-04/orders.json?status=any&name=$shopify_order_id", array(), $sc, $user['users_id']);
    debug($jsons);
    exit;
    // if (empty($jsons))
    //    return false;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    $headers = array(
        "Cache-Control: no-cache",
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, 'https://api.ryankikta.com/shopify_webhook_cnelson.php?action=create&user_id=479&shop_id=' . $user['id']);
    //curl_setopt( $ch, CURLOPT_URL,'https://api.ryankikta.com/shopify_webhook_order_test.php?action=create');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt( $ch, CURLOPT_VERBOSE , 1 );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_to_send))
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
    $return = curl_exec($ch);
    debug($return);
}

function get_all_shopify_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $auth = getShopifyShop($user_id);
    $pages = (int)(ShopifyApiCall1("GET", "/admin/api/2020-04/products/count.json", NULL, $auth[1]) / 50) + 1;
    $all_products = array();
    //for ($i = 1; $i <= $pages; $i++) {
        $resp = ShopifyApiCall1("GET", "/admin/api/2020-04/products.json?limit=250&page_info=", NULL, $auth[1]);
        foreach ($resp as $product) {
            $pa_product_id = $wpdb->get_var("SELECT `product_id`  FROM `wp_products_meta` where `meta_value` = '" . $product["id"] . "' AND `meta_key`='shopify_id'");
            //            if(!isset($pa_product_id) || ($pa_product_id=="") || ($pa_product_id==NULL)){
            //                 $pa_product_id = $wpdb->get_var("select id from wp_users_products where shopify_id=" . $product["id"]);
            //            }
            //
            $all_products[] = array(
                "id" => $product["id"],
                "title" => $product["title"],
                "variants" => $product["variants"],
                "status" => "",
                "url" => "",
                "image" => $product["image"]["src"],
                "imported" => ($pa_product_id == NULL) ? 0 : 1,
                "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
            );
        }
    //}
    return $all_products;
}

function get_shopify_product_byid($shop_id, $shopify_id)
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    //debug($user);
    $sc = new ShopifyClient($user['shop'], $user['token'], Shopify_Key, Shopify_Secret);

    return ShopifyApiCall1('GET', "/admin/api/2020-04/products/$shopify_id.json?fields=id,title,variants", array(), $sc, $user['users_id']);;
}

function get_shopify_products($shop_id)
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    debug($user);

    return ShopifyApiCall1('GET', "/admin/api/2020-04/products.json?fields=id,title", array(), $sc, $user['users_id']);;
}

function search_shopify_by_name($shop_id, $product_name = "")
{
    global $wpdb;
    $user = $wpdb->get_row("select * from wp_users_shopify where id = $shop_id ", ARRAY_A);
    debug($user);


    return ShopifyApiCall1("GET", "/admin/api/2020-04/products.json?fields=id,title", NULL);
}

function DeleteShopShopifyById($id, $user_id)
{
    global $wpdb;
    $response = null;
    $check_shop = $wpdb->get_results("SELECT * 
	                              FROM `wp_users_shopify` 
				      WHERE `id` = $id 
                                      AND `users_id` = $user_id", ARRAY_A);
    if ($wpdb->num_rows > 0) {
        $shop_data = ($check_shop);
        $token = $shop_data["token"];
        $shop_name = $shop_data["shop"];
        $active = intval($shop_data["active"]);
        if ($active == 1) {
            $response_webhook = DeleteShopifyWebhooks($token, $shop_name);
            // debug( $response_webhook);
            /*if ($response_webhook["status"] == 400) {
			  return $response_webhook;
			  }*/
        }
        $query_insert = $wpdb->query("INSERT INTO `wp_users_shops_deleted` (`id`, `users_id`, `shop_name`, `shop_type`, `shop_id`,deleted_at,type) VALUES (NULL, $user_id, '$shop_name', 'shopify', '$id', CURRENT_TIMESTAMP,'LIVE');");
        //debug($query_insert);
        //debug(mysql_error());
        if (!$query_insert) {
            $response = array("status" => 400, "error" => mysql_error());
        } else {
            $query = $wpdb->query("DELETE FROM `wp_users_shopify` WHERE `id` = $id and `users_id` = $user_id");
            if (!$query) {
                $response = array("status" => 400, "error" => mysql_error());
            } else {
                $response = array("status" => 200, "data" => "true");
            }
        }
    } else {
        $response = array("status" => 400, "error" => "Shop not Found. Please check and try again later");
    }

    return $response;
}

function getListShopifyShopsByUserId($user_id)
{
    $response = null;
    $shops = array();
    global $wpdb;
    $sql = "SELECT dateadded, `shop`,`id`,`token`,`active` FROM `wp_users_shopify` WHERE `users_id` = $user_id";
    $shops = $wpdb->get_results($sql, ARRAY_A);
    $response = array("status" => 200, "data" => $shops);
    return $response;
}

function get_all_shopify_shops($user_id)
{
    $shops = array();
    global $wpdb;
    $sql = "SELECT dateadded, `shop`,`id`,`token`,`active` FROM `wp_users_shopify` WHERE `users_id` = $user_id";
    $shops = $wpdb->get_results($sql);
    return $shops;
}

function getUserBalanceByUserId($user_id)
{
    global $wpdb;
    $query = $wpdb->get_results("SELECT `balance` FROM `wp_users` WHERE `id` = $user_id");
    $balance = $wpdb->get_row($query);
    $balance = $balance[0];
    $automatic_payment = (int)get_user_meta($currentuserid, 'autopayment', true);
    return array("balance" => $balance, "automatic_payment" => $automatic_payment);
}

function InstallShopShopifyUser($user_id, $code, $shop)
{
    global $wpdb;
    $response = array("status" => 400, "error" => "An error has occurred while attempting to install shop : " . $shop);
    $shopifyClient = new ShopifyClient($shop, "", Shopify_Key, Shopify_Secret);
    session_unset();
    $_SESSION['token'] = $shopifyClient->getAccessToken($code);
    $_SESSION['shop'] = $shop;
    $sql = ("SELECT COUNT(id) FROM wp_users_shopify WHERE users_id = $user_id AND shop = $Shop");
    $exists = $wpdb->get_var($sql);
    $dateadded = date("Y-m-d");
    if ($exists == 0);{
        $query_update = $wpdb->query("INSERT INTO wp_users_shopify (users_id, shop, token, dateadded, dateupdated, active) VALUES ($user_id, '$shop', '$token', '$dateadded', '$dateadded', '1')");
            if (!$query_update) {
                $response = array("status" => 400, "error" => mysql_error());
            } else {
                $response = array("status" => 200, "data" => $shop);
	    }
    }
    return $response;
}

function getShopifyAuthorizeUrl($shop, $scope, $user_id)
{
    global $wpdb;
    $response = array("status" => 400, "error" => "An error has occurred while attempting to install shop : " . $shop);
    $check_shop_query = $wpdb->get_results("SELECT `users_id`,`active` FROM `wp_users_shopify` WHERE  shop='$shop'");
    $check_shop_count = $wpdb->num_rows;
    if ($check_shop_count > 0) {

        $rows = $wpdb->get_row($check_shop_query);
        $shop_owner = $rows[0];
        $active = (int)$rows[1];
        if ($shop_owner == $user_id) {
            if ($active == 1) {
                $response = array("status" => 400, "error" => "Shop : " . $shop . " already installed and active in your account");
            } else {
                $shop = str_replace("http://", "", $shop);
                $shop = str_replace("www.", "", $shop);
                $shop = str_replace("https://", "", $shop);
                $shopifyClient = new ShopifyClient($shop, "", Shopify_Key, Shopify_Secret);
                $pageURL = 'http';
                if ($_SERVER["HTTPS"] == "on") {
                    $pageURL .= "s";
                }
                $pageURL .= "://";
                if ($_SERVER["SERVER_PORT"] != "80") {
                    $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . "/shopify/"; //. $_SERVER["REQUEST_URI"];
                } else {
                    $pageURL .= $_SERVER["HTTP_HOST"] . "/shopify/";//$_SERVER["REQUEST_URI"];
                } //wp_mail('team@ryankikta.com','page url redirection',var_export($pageURL,true));
                $location = $shopifyClient->getAuthorizeUrl($scope, $pageURL);
                $response = array("status" => 200, "data" => $location);
            }
        } else {
            $response = array("status" => 400, "error" => "Shop : " . $shop . " already installed in other account");
        }
    } else {
        $shop = str_replace("http://", "", $shop);
        $shop = str_replace("www.", "", $shop);
        $shop = str_replace("https://", "", $shop);
        $shopifyClient = new ShopifyClient($shop, "", Shopify_Key, Shopify_Secret);
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . "/shopify/";//. $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["HTTP_HOST"] . "/shopify/";//. $_SERVER["REQUEST_URI"];
        } //wp_mail('team@ryankikta.com','page url redirection1',var_export(array($pageURL,$_SERVER),true));
        $location = $shopifyClient->getAuthorizeUrl($scope, $pageURL);
        $response = array("status" => 200, "data" => $location);
    }


    return $response;
}

function ViewShopifyShopById($shop_id, $user_id)
{
    $response = array("status" => 400, "error" => "Shopify shop was not found. Please check and try again later");
    $check_shop = $wpdb->get_result("SELECT  `shop`,`id`,`token`,`active`,`firstimedone` FROM `wp_users_shopify` WHERE `users_id` = $user_id AND id=$shop_id");
    $check_shop_num = $wpdb->num_rows($check_shop);
    $product_list = array();
    if ($check_shop_num > 0) {
        $rows = $wpdb->get_row($check_shop);
        $shop = $rows[0];
        $shop_id = $rows[1];
        $token = $rows[2];
        $active = (int)$rows[3];
        $firstimedone = (int)$rows[4];
        $shop_data = array(
            "id" => $shop_id,
            "shop" => $shop,
            "active" => $active,
            "token" => $token
        );
        $responce = CheckShopifyShop($user_id, $token, $shop_id, $shop);
        //  return $responce;
        if ($responce["status"] == 200) {
            if (($firstimedone == 0) && ($active == 1)) {
                $response = CreateShopifyWebhook($token, $shop_id, $shop, $user_id);
                if ($response["status"] == 400) {
                    $wpdb->get_result("UPDATE `wp_users_shopify` SET `active` = 0 WHERE `id` = $shop_id");
                    $shop_data["active"] = 0;
                }
            }
        } else {
            $wpdb->get_result("UPDATE `wp_users_shopify` SET `active` = 0 WHERE `id` = $shop_id");
            $shop_data["active"] = 0;
        }
        $response = array("status" => 200, "data" => $shop_data);
    } else {
        $response = array("status" => 400, "error" => "Shopify shop was not found. Please check and try again later");
    }
    return $response;
}

function CheckShopifyShop($currentuserid, $token, $shop_id, $shop)
{

    $response = array("status" => 400, "error" => "");
    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
    try {
        $shopdetails = $sc->call('GET', '/admin/api/2020-04/shop.json');

        if (empty($shopdetails)) {

            $response = array("status" => 400, "error" => "Not Found");
        } else {
            $response = array("status" => 200, "sucess" => "acessible");
        }
    } catch (ShopifyApiException $e) {
        $error = null;
        switch ($http_code) {
            case '401' :
                $error = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $error = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $error = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $error = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '200' :
                $response = array("status" => 200, "success" => "true");
                break;
        }
        if (isset($error)) {
            $response = array("status" => 400, "error" => $error);
        }
    } catch (ShopifyCurlException $e) {
        $response = array("status" => 400, "error" => $e->getMessage());
    }
    if ($response["status"] == 200) {
        $wpdb->get_result("UPDATE `wp_users_shopify` SET `active` = 1 WHERE `id` = $shop_id");
    }
    return $response;
}

function get_shopify_hooks($user_id, $shop_id = 0)
{
    $response = array();
    $auth = ($shop_id != 0) ? getShopifyShopbyId($shop_id) : getShopifyShop($user_id);
    if (!empty($auth)) {
        $path = "/admin/api/2020-04/webhooks.json";
        $response = ShopifyApiCall1("GET", $path, NULL, $auth[1]);
    }
    return $response;
}

function update_shopify_webhook($user_id, $hook_id, $address, $shop_id = 0)
{
    $response = array();
    $auth = ($shop_id != 0) ? getShopifyShopbyId($shop_id) : getShopifyShop($user_id);
    if (!empty($auth)) {
        $path = "/admin/api/2020-04/webhooks/$hook_id.json";
        $webhook = array(
            "webhook" => array(
                "id" => $hook_id,
                "address" => $address,
                "format" => "json"
            )
        );
        $response = ShopifyApiCall1("PUT", $path, $webhook, $auth[1]);
    }
    return $response;
}

function CreateShopifyWebhook($token, $shop_id, $shop, $currentuserid)
{
    global $wpdb;
    $response = array("status" => 400, "error" => "");
    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
    try {
        $blogurl = get_bloginfo('url');
        // Create webhook for orders.
        $createwebhook = 0;
        $edit_createwebhook = 0;
        $webhooks_create_orders = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "orders/create"));
        if (count($webhooks_create_orders) > 0) {
            if (count($webhooks_create_orders) > 1) {
                foreach ($webhooks_create_orders as $webhook_order) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_order["id"] . '.json');
                }
            } else {
                $webhook_order = $webhooks_create_orders[0];
                $createwebhook = (int)$webhook_order['id'];
                if ($webhook_order['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_createwebhook = 1;
                }
            }
        }

        $uninstallwebhook = 0;
        $edit_uninstallwebhook = 0;
        $webhooks_uninstalled = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "app/uninstalled"));
        if (count($webhooks_uninstalled) > 0) {
            if (count($webhooks_uninstalled) > 1) {
                foreach ($webhooks_uninstalled as $webhook_uninstalled) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_uninstalled["id"] . '.json');
                }
            } else {
                $webhook_uninstalled = $webhooks_uninstalled[0];
                $uninstallwebhook = (int)$webhook_uninstalled['id'];
                if ($webhook_uninstalled['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_uninstallwebhook = 1;
                }
            }
        }
        $productupdatewebhook = 0;
        $edit_productupdatewebhook = 0;
        $webhooks_productupdate = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "products/update"));
        if (count($webhooks_productupdate) > 0) {
            if (count($webhooks_productupdate) > 1) {
                foreach ($webhooks_productupdate as $webhook_productupdate) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_productupdate["id"] . '.json');
                }
            } else {
                $webhook_productupdate = $webhooks_productupdate[0];
                $productupdatewebhook = (int)$webhook_productupdate['id'];
                if ($webhook_productupdate['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_productupdatewebhook = 1;
                }
            }
        }
        $productdeletewebhook = 0;
        $edit_productdeletewebhook = 0;
        $webhooks_deletewebhook = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "products/delete"));
        if (count($webhooks_deletewebhook) > 0) {
            if (count($webhooks_deletewebhook) > 1) {
                foreach ($webhooks_deletewebhook as $webhook_deletewebhook) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_deletewebhook["id"] . '.json');
                }
            } else {
                $webhook_deletewebhook = $webhooks_deletewebhook[0];
                $productdeletewebhook = (int)$webhook_deletewebhook['id'];
                if ($webhook_deletewebhook['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_productdeletewebhook = 1;
                }
            }
        }


        if ($createwebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "orders/create",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_createwebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $createwebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $createwebhook . '.json', $webhook);
        }
        if ($uninstallwebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "app/uninstalled",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_uninstallwebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $uninstallwebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $uninstallwebhook . '.json', $webhook);
        }

        if ($productupdatewebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "products/update",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_productupdatewebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $productupdatewebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $productupdatewebhook . '.json', $webhook);
        }

        if ($productdeletewebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "products/delete",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_productdeletewebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $productdeletewebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $productdeletewebhook . '.json', $webhook);
        }


        $wpdb->get_result("UPDATE `wp_users_shopify` SET `firstimedone` = 1 WHERE `id` = $shop_id");
        $response = array("status" => 200, "success" => "true");
    } catch (ShopifyApiException $e) {
        $error = null;
        switch ($http_code) {
            case '401' :
                $error = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $error = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $error = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $error = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '200' :
                $response = array("status" => 200, "success" => "true");
                break;
        }
        if (isset($error)) {
            $response = array("status" => 400, "error" => $error);
        }
    } catch (ShopifyCurlException $e) {
        $response = array("status" => 400, "error" => $e->getMessage());
    }
    return $response;
}

function DeleteShopifyWebhooks($token, $shop)
{
    $response = array("status" => 400, "error" => "");
    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
    try {
        // get list webhook
        $webhooks = $sc->call('GET', '/admin/api/2020-04/webhooks.json');
        //debug($webhooks );
        if (count($webhooks) > 0) {
            foreach ($webhooks as $webhook) {
                if ((strpos($webhook["address"], 'ryankikta.com') !== false) && ($webhook["topic"] != "products/delete")) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook["id"] . '.json');
                }
            }
        }
        $response = array("status" => 200, "success" => "true");
    } catch (ShopifyApiException $e) {
        $error = null;
        switch ($http_code) {
            case '401' :
                $error = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $error = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $error = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $error = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '200' :
                $response = array("status" => 200, "success" => "true");
                break;
        }
        if (isset($error)) {
            $response = array("status" => 400, "error" => $error);
        }
    } catch (ShopifyCurlException $e) {
        $response = array("status" => 400, "error" => $e->getMessage());
    }
    return $response;
}

function CreateShopifyWebhookLive($token, $shop_id, $shop, $currentuserid)
{
    global $wpdb;
    $response = array("status" => 400, "error" => "");
    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
    try {
        $blogurl = get_bloginfo('url');
        // Create webhook for orders.
        $createwebhook = 0;
        $edit_createwebhook = 0;
        $webhooks_create_orders = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "orders/create"));
        if (count($webhooks_create_orders) > 0) {
            if (count($webhooks_create_orders) > 1) {
                foreach ($webhooks_create_orders as $webhook_order) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_order["id"] . '.json');
                }
            } else {
                $webhook_order = $webhooks_create_orders[0];
                $createwebhook = (int)$webhook_order['id'];
                if ($webhook_order['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_createwebhook = 1;
                }
            }
        }

        $uninstallwebhook = 0;
        $edit_uninstallwebhook = 0;
        $webhooks_uninstalled = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "app/uninstalled"));
        if (count($webhooks_uninstalled) > 0) {
            if (count($webhooks_uninstalled) > 1) {
                foreach ($webhooks_uninstalled as $webhook_uninstalled) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_uninstalled["id"] . '.json');
                }
            } else {
                $webhook_uninstalled = $webhooks_uninstalled[0];
                $uninstallwebhook = (int)$webhook_uninstalled['id'];
                if ($webhook_uninstalled['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_uninstallwebhook = 1;
                }
            }
        }
        $productupdatewebhook = 0;
        $edit_productupdatewebhook = 0;
        $webhooks_productupdate = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "products/update"));
        if (count($webhooks_productupdate) > 0) {
            if (count($webhooks_productupdate) > 1) {
                foreach ($webhooks_productupdate as $webhook_productupdate) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_productupdate["id"] . '.json');
                }
            } else {
                $webhook_productupdate = $webhooks_productupdate[0];
                $productupdatewebhook = (int)$webhook_productupdate['id'];
                if ($webhook_productupdate['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_productupdatewebhook = 1;
                }
            }
        }
        $productdeletewebhook = 0;
        $edit_productdeletewebhook = 0;
        $webhooks_deletewebhook = $sc->call('GET', '/admin/api/2020-04/webhooks.json', array("topic" => "products/delete"));
        if (count($webhooks_deletewebhook) > 0) {
            if (count($webhooks_deletewebhook) > 1) {
                foreach ($webhooks_deletewebhook as $webhook_deletewebhook) {
                    $sc->call('DELETE', '/admin/api/2020-04/webhooks/' . $webhook_deletewebhook["id"] . '.json');
                }
            } else {
                $webhook_deletewebhook = $webhooks_deletewebhook[0];
                $productdeletewebhook = (int)$webhook_deletewebhook['id'];
                if ($webhook_deletewebhook['address'] != "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id") {
                    $edit_productdeletewebhook = 1;
                }
            }
        }


        if ($createwebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "orders/create",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_createwebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $createwebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=create&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $createwebhook . '.json', $webhook);
        }
        if ($uninstallwebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "app/uninstalled",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_uninstallwebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $uninstallwebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=uninstall&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $uninstallwebhook . '.json', $webhook);
        }

        if ($productupdatewebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "products/update",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_productupdatewebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $productupdatewebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productupdate&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $productupdatewebhook . '.json', $webhook);
        }

        if ($productdeletewebhook == 0) {
            $webhook = array();
            $webhook = array(
                "webhook" => array(
                    "topic" => "products/delete",
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id",
                    "format" => "json"
                )
            );
            $webhookcreate = $sc->call('POST', '/admin/api/2020-04/webhooks.json', $webhook);
        } elseif ($edit_productdeletewebhook == 1) {
            $webhook = array(
                "webhook" => array(
                    "id" => $productdeletewebhook,
                    "address" => "https://api.ryankikta.com/shopify_webhook.php?action=productdelete&user_id=$currentuserid&id_shop=$shop_id",
                )
            );
            $webhookcreate = $sc->call('PUT', '/admin/api/2020-04/webhooks/' . $productdeletewebhook . '.json', $webhook);
        }


        $wpdb->get_result("UPDATE `wp_users_shopify` SET `firstimedone` = 1 WHERE `id` = $shop_id");
        $response = array("status" => 200, "success" => array(
            "edit_createwebhook" => $edit_createwebhook,
            "edit_productdeletewebhook" => $edit_productdeletewebhook,
            "edit_productupdatewebhook" => $edit_productupdatewebhook,
            "edit_uninstallwebhook" => $edit_uninstallwebhook
        ));
    } catch (ShopifyApiException $e) {
        $error = null;
        switch ($http_code) {
            case '401' :
                $error = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $error = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $error = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $error = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '200' :
                $response = array("status" => 200, "success" => "true");
                break;
        }
        if (isset($error)) {
            $response = array("status" => 400, "error" => $error);
        }
    } catch (ShopifyCurlException $e) {
        $response = array("status" => 400, "error" => $e->getMessage());
    }
    return $response;
}

function CheckExistingShopifyUser($user_id)
{
    $authshopify = false;
    // check if user has shopify store
    $checkuser = $wpdb->get_result("SELECT `shop`,`token` FROM `wp_users_shopify` WHERE `users_id` = $user_id");
    $numshopsshopify = $wpdb->num_rows($checkuser);

    if ($numshopsshopify != 0) {

        $authshopify = true;
    }

    return $authshopify;
}

function getShopifyShopbyId($id)
{

    $authshopify = array();
    // check if user has shopify store
    $check_shop = $wpdb->get_result("SELECT `users_id`,`shop`,`token` FROM `wp_users_shopify` WHERE `id` = $id");
    $numshopsshopify = $wpdb->num_rows($check_shop);
    if ($numshopsshopify > 0) {

        $shoprow = $wpdb->get_row($check_shop);
        $user_id = $shoprow[0];
        $shop = $shoprow[1];
        $token = $shoprow[2];
        $shopify_auth = array('user_id' => $user_id, 'shop' => $shop, 'token' => $token);
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $authshopify = array($shopify_auth, $sc);
    }

    return $authshopify;
}

function ShopifyProductData($prodid)
{
    $shopifyactive = 0;
    $type = "";
    $vendor = "";
    $shopids = "";
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products_shopify` WHERE `product_id` = $prodid");
    $numshops = $wpdb->num_rows($selectproductquery);
    if ($numshops > 0) {
        $shopifyactive = 1;
        while ($row = mysql_fetch_assoc($selectproductquery)) {
            if ($row["active"] == 1) {
                $shopids = $shopids . "," . $row["shopid"];
                $vendor = $row["vendor"];
                $type = $row["type"];
            }
        }
    }
    $shopify_data = array('shopifyactiveold' => $shopifyactive, 'shopifytype' => $type, 'shopifyvendor' => $vendor, "shopids" => $shopids, "numshops_shopify" => $numshops);
    return $shopify_data;
}

function ShopifyShopsProductData($prodid)
{
    global $wpdb;
    $pa_product = $wpdb->get_row("select `active`,shopify_id,users_id from `wp_users_products` where `id` = $prodid");
    $shopifyactive = (int)$pa_product->active;
    $shopids = "";
    $shops_shopify_ids = get_product_meta_shops($prodid, 'shopify_id');

    $numshops = count($shops_shopify_ids);
    if ($numshops > 0) {
        $shopids = implode(",", $shops_shopify_ids);
    } else {
        if ($shopifyactive == 1 && isset($pa_product->shopify_id) && ($pa_product->shopify_id != "") && ($pa_product->shopify_id != NULL)) {
            $user_id = $pa_product->users_id;
            $shopids = $wpdb->get_var("select `id` from `wp_users_shopify` where `users_id` = $user_id");
        }
    }
    $shopify_data = array('shopifyactiveold' => $shopifyactive, "shopids" => $shopids, "numshops_shopify" => $numshops);
    return $shopify_data;
}

function getShopifyDataByShop($data, $shop_id)
{

    $newproduct = esc_sql($data['newproduct' . $shop_id]);
    $shopifytype = esc_sql($data['shopifytype' . $shop_id]);
    $shopifyvendor = esc_sql($data['shopifyvendor' . $shop_id]);
    return array('newproduct' => $newproduct, 'shopifytype' => $shopifytype, 'shopifyvendor' => $shopifyvendor);
}

function addShopifyProductByShop($POST, $shopify_data, $description, $variants, $currentuserid, $products_id, $shop_id, $collections_shopify_id = NULL)
{
    global $wpdb, $sc;
    //wp_mail('team@ryankikta.com','variants',var_export($variants,true));
    @extract($shopify_data);
    $list = get_html_translation_table(HTML_ENTITIES);
    unset($list['"']);
    unset($list['<']);
    unset($list['>']);
    unset($list['&']);
    $updatedescription = strtr($description, $list);

    if ($newproduct == 0) {

        $charge = array(
            "product" => array
            (
                "title" => str_replace('"', '\"', stripslashes($POST['title'])),
                "body_html" => $updatedescription,
                "vendor" => str_replace('"', '\"', stripslashes($shopifyvendor)),
                "product_type" => str_replace('"', '\"', stripslashes($shopifytype)),
                "tags" => str_replace('"', '\"', stripslashes($POST['tags'])),
                "options" => array
                (
                    array
                    (
                        "name" => 'Size'
                    ),
                    array
                    (
                        "name" => 'Color'
                    )
                ),
                "variants" => $variants
            )
        );
        $shopifycreate = ShopifyApiCall("POST", '/admin/api/2020-04/products.json', $charge, $currentuserid);
        //wp_mail('team@ryankikta.com','shopify add',var_export(array($charge),true));
        if (isset($shopifycreate['errors'])) {
            $errors = array();
            $error_title = 'Error add product in shopify :';
            foreach ($shopifycreate['errors'] as $value) {
                $errors[] = $value;
            }
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'error_title' => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        // Shopify ID
        $shopify_id = $shopifycreate['id'];
        if ($POST['pagetype'] == 1 || $POST['pagetype'] == 3)
            $_SESSION['shops']['shopify_id'] = $shopify_id;
    } else {

        $shopify_id = $newproduct;
        // Delete all collections
        $allcollects = ShopifyApiCall("GET", "/admin/api/2020-04/collects.json?product_id=" . $shopify_id);

        foreach ($allcollects as $useless => $array) {
            $return = ShopifyApiCall("DELETE", "/admin/api/2020-04/collects/" . $array['id'] . ".json");
        }

        $list = get_html_translation_table(HTML_ENTITIES);
        unset($list['"']);
        unset($list['<']);
        unset($list['>']);
        unset($list['&']);

        $updatedescription = strtr($description, $list);

        $color_exist = false;
        $size_exist = false;
        $options = array();
        if (!$size_exist)
            $options[] = array("name" => 'Size');
        if (!$color_exist)
            $options[] = array("name" => 'Color');

        // Update the product
        $charge = array(
            "product" => array
            (
                "title" => str_replace('"', '\"', stripslashes($POST['title'])),
                "body_html" => $updatedescription,
                "vendor" => str_replace('"', '\"', stripslashes($shopifyvendor)),
                "product_type" => str_replace('"', '\"', stripslashes($shopifytype)),
                "tags" => str_replace('"', '\"', stripslashes($POST['tags'])),
                "options" => $options,
                "variants" => $variants
            )
        );
        $shopifycreate = ShopifyApiCall("PUT", "/admin/api/2020-04/products/$shopify_id.json", $charge);
        //wp_mail('team@ryankikta.com','shopify edit '.$shopify_id,var_export(array($shopifycreate),true));

        //wp_mail('team@ryankikta.com','shopify data',var_export( $shopifycreate,true));

        if (isset($shopifycreate['errors'])) {
            $errors = array();
            $error_title = 'Error update an existing product in shopify :';
            foreach ($shopifycreate['errors'] as $value) {
                $errors[] = $value;
            }
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'error_title' => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        $shopify_id = $shopifycreate['id'];
    }
    if (isset($collections_shopify_id) && ($collections_shopify_id != NULL) && ($collections_shopify_id != "")) {
        $collection_id = $collections_shopify_id;
        $array_collections = explode(",", $collections_shopify_id);
        foreach ($array_collections as $custom_collection_id) {
            if ($custom_collection_id != "") {
                $collection_id = $collection_id . "," . $custom_collection_id;
                $collect = array("custom_collection" => array("collects" => array(array("product_id" => $shopify_id))));
                $shopifycollect = ShopifyApiCall("PUT", "/admin/api/2020-04/custom_collections/$custom_collection_id.json", $collect);
            }
        }
    } else {
        $collection_id = "";
        // Add it to the collections
        if (isset($POST['collection' . $shop_id])) {
            foreach ($POST['collection' . $shop_id] as $custom_collection_id) {
                if ($custom_collection_id != "") {
                    $collection_id = $collection_id . "," . $custom_collection_id;
                    $collect = array("custom_collection" => array("collects" => array(array("product_id" => $shopify_id))));
                    $shopifycollect = ShopifyApiCall("PUT", "/admin/api/2020-04/custom_collections/$custom_collection_id.json", $collect);
                }
            }
        }
    }

    if ($shopify_id == "") {
        $errors = array();
        foreach ($shopifycreate['errors'] as $value) {
            $errors[] = $value;
        }

        // return to manage products
        $post = $POST;
        $post['shopify_return'] = $shopifycreate;
        $post['errors'] = $errors;
        $post['user_id'] = $currentuserid;
        $post['user_product_id'] = $products_id;
        $export = var_export($post, true);
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else {
        $pa_product_id_old = get_product_id_meta_shop($shopify_id, "shopify_id", $shop_id);
        if ($pa_product_id_old && $pa_product_id_old != $products_id)
            delete_variants_product_meta_shop($pa_product_id_old, 'shopify_id', $shop_id);

        $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $products_id) ? $pa_product_id_old : 0;

        $shopfiy_old_id = get_product_meta_shop($products_id, "shopify_id", $shop_id);

        if (isset($shopfiy_old_id) && $shopfiy_old_id != "") {
            if ($shopfiy_old_id != $shopify_id) {
                $checkshop = $wpdb->get_result("SELECT `shop`,`token` FROM `wp_users_shopify` WHERE `id` = $shop_id");
                $numshopsshopify = $wpdb->num_rows($checkshop);

                if ($numshopsshopify != 0) {

                    $shoprow = $wpdb->get_row($checkshop);
                    $shop = $shoprow[0];
                    $token = $shoprow[1];
                    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
                    $path = '/admin/api/2020-04/products/' . $shopfiy_old_id . '.json';
                    ShopifyApiCall("DELETE", $path);
                }
            }
        }
        $all_times = array();
        $start = microtime(true);
        $all_meta = array('shopify_id' => 'NULL', 'shopifyvendor' => 'NULL', 'shopifytype' => 'NULL', 'shopifycollection' => 'NULL');
        $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $products_id and shopid = $shop_id", ARRAY_A);
        foreach ($results as $res) {
            $all_meta[$res['meta_key']] = $res['meta_id'];
        }

        if ($prod_to_deconnect) {
            update_product_meta_shop($products_id, "shopify_id", $shopify_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, "shopifyvendor", stripcslashes($shopifyvendor), $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, "shopifytype", stripcslashes($shopifytype), $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($products_id, "shopifycollection", trim($collection_id, ','), $shop_id, 0, $prod_to_deconnect);

        } else {
            $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['shopify_id']},$products_id,'shopify_id','$shopify_id',$shop_id),"
                . " ({$all_meta['shopifyvendor']},$products_id,'shopifyvendor','" . stripcslashes($shopifyvendor) . "',$shop_id),"
                . " ({$all_meta['shopifytype']},$products_id,'shopifytype','" . stripcslashes($shopifytype) . "',$shop_id),"
                . " ({$all_meta['shopifycollection']},$products_id,'shopifycollection','" . trim($collection_id, ',') . "',$shop_id) "
                . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";

            $wpdb->query($sql);
        }


        $all_times['shopify_meta'] = microtime(true) - $start;
        $sql = "UPDATE `wp_users_products` SET `active` = 1  WHERE `id` = $products_id";
        $query = $wpdb->get_result($sql);
        if (!$query) {
            $errors = array();
            $logs['sql'] = mysql_escape_string($sql);

            $post = $POST;
            $post['errors'][] = "error in our end";
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $products_id;
            $export = var_export($post, true);
            $errors[] = "An error occured in update shopify data. ";
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
        $all_meta = array();
        $all_vars = array();
        //$all_vars_id = array();
        //$shopify_image_id = get_image_color_meta_shop($products_id,$color_id,$image_id,'shopify_id',$shop_id);
        //mail("team@ryankikta.com","shopify variants data",var_export(array($products_id,$shop_id,$POST),true));
        $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $shop_id and meta_key='shopify_id' and product_id=$products_id", ARRAY_A);
        foreach ($results as $res) {
            $all_meta[$res['variant_id']] = $res['id'];
        }
        //mail("team@ryankikta.com","response variants shopify",var_export(array('data1'=>$variants,'data2'=>$shopifycreate['variants']),true));
        $variants_images = array();
        $images_to_create = array();
        $variants_colors = array();
        if ($POST['pagetype'] == 3) {
            $current_user = wp_get_current_user();
            $currentusername = $current_user->user_login;
            $currentuserid = $current_user->ID;
            $cdn_alias = get_user_meta($currentuserid, 'cdn_alias', true);
            $blogurl = get_bloginfo('url');
        }

        foreach ($shopifycreate['variants'] as $array) {
            $start = microtime(true);
            $option1 = $array['option1'];
            $option2 = $array['option2'];
            $option3 = $array['option3'];
            $sku = $array['sku'];
            $variant_id = $array['id'];
            $color_id = get_colors_col($option1);
            if (count($color_id) == 0) {
                $color_id = get_colors_col($option2);
                if (count($color_id) == 0) {
                    $color_id = get_colors_col($option3);
                }
            }
            $size_id = get_sizes_col($option1);
            if (count($size_id) == 0) {
                $size_id = get_sizes_col($option2);
                if (count($size_id) == 0) {
                    $size_id = get_sizes_col($option3);
                }
            }
            foreach ($color_id as $col_id) {
                $variants_colors[$col_id][] = $variant_id;
                if (isset($POST['image_' . $col_id])) {
                    $image_id = $POST['image_' . $col_id];
                    $image_src = $POST['image_url_' . $col_id];
                    break;
                } elseif ($POST['pagetype'] == 3) {
                    $pa_product_id = $POST['pa_product_id'];
                    $image_id = $wpdb->get_var("SELECT image_id FROM `wp_users_products_colors` where `users_products_id` = $pa_product_id and `color_id`=$col_id");
                    $image = $wpdb->get_results("SELECT * FROM `wp_userfiles` WHERE `userID` = $currentuserid AND `deleted` = 0 AND `fileID`=$image_id", ARRAY_A);
                    $image_src = get_thumb_path($image[0], $currentusername, $cdn_alias, $blogurl);
                }
            }
            if ($image_id > 0) {
                $shopify_image_id = get_image_color_meta_shop($products_id, $col_id, $image_id, 'shopify_id', $shop_id);
                if (!$shopify_image_id) {
                    $images_to_create[$col_id]['image_id'] = $image_id;
                    $images_to_create[$col_id]['src'] = $image_src;
                    $images_to_create[$col_id]['variants_ids'] = $variants_colors[$col_id];
                }
            }
            $all_times[$array['id']]['names'] = microtime(true) - $start;
            $start = microtime(true);

            $shopify_variant_id = $array['id'];
            $sql = "SELECT id FROM `wp_users_products_colors` WHERE `color_id` in (" . implode(",", $color_id) . ") AND `size_id` in (" . implode(",", $size_id) . ") AND `users_products_id` = $products_id";
            $pa_variant_id = $wpdb->get_var($sql);
            $all_times[$array['id']]['var_id'] = microtime(true) - $start;
            $start = microtime(true);
            if (isset($pa_variant_id) && ($pa_variant_id != "")) {

                //update_variant_meta_shop($products_id, $pa_variant_id, "shopify_id", $shopify_variant_id, $shop_id);
                $_tmp = array('variant_id' => $pa_variant_id, 'shopify_id' => $shopify_variant_id);
                $_tmp['id'] = (isset($all_meta[$pa_variant_id])) ? $all_meta[$pa_variant_id] : 'NULL';
                $all_vars[] = $_tmp;

            } else {
                $logs['sql'] = mysql_escape_string($sql);
                $logs['shopifycreate'] = $shopifycreate;

                $post = $POST;
                $post['errors'][] = "error in our end";
                $post['user_id'] = $currentuserid;
                $post['variant_shopify'] = $array;
                $post['sql'] = mysql_escape_string($sql);
                $post['user_product_id'] = $products_id;
                $export = var_export($post, true);
                $errors[] = "An error occured in update shopify variants. ";
                wp_mail("cnelson@ryankikta.com", "shopify add variants error", $export);

                $_SESSION['data'] = $POST;
                $return = array("status" => 0, 'errors' => $errors);
                echo json_encode($return);
                exit();
            }
        }
        UploadShopifyStoreImagesVariantsbyShop($variants, $images_to_create, $shopify_id, $products_id, $shop_id);
        $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
        $_tmp = array();
        foreach ($all_vars as $_var) {
            $_tmp[] = " ({$_var['id']},'$products_id','{$_var['variant_id']}','shopify_id','{$_var['shopify_id']}','$shop_id') ";
        }
        $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
        $wpdb->query($sql_var);
        return $shopify_id;
    }
}

function update_shopify_meta($product_id, $shopify_id, $shopify_data, $all_meta, $prod_to_deconnect)
{
    global $wpdb;
    if (!empty($all_meta['shopify_id']))
        $wpdb->query("update wp_products_meta set meta_value='$shopify_id' where meta_id in (" . implode(",", $all_meta['shopify_id']) . ")");
}

function UploadShopifyStoreImagesVariantsbyShop($variants, $images_to_create, $shopify_id, $products_id, $shop_id)
{
    global $wpdb;

    $images_to_remove = array();
    $send = array();
    $res = array();
    $path = '/admin/api/2020-04/products/' . $shopify_id . '/images.json';
    foreach ($images_to_create as $color_id => $image)
        delete_image_color_by_shop($products_id, $color_id, 'shopify_id', $shop_id);

    foreach ($images_to_create as $color_id => $image) {
        $img_src = $image['src'];
        $image_id = $image['image_id'];
        $imageadd = array("image" => array("src" => "$img_src", "variant_ids" => $image['variants_ids']));
        $send[] = $imageadd;
        $uploadimage = ShopifyApiCall('POST', $path, $imageadd);
        $res[] = $uploadimage;
        $shopify_image_id = $uploadimage['id'];
        $old_shopify_image_id = get_image_shop_by_color($products_id, $color_id, 'shopify_id', $shop_id);
        $images_to_remove[] = $old_shopify_image_id;
        if ($image_id > 0 && $shopify_image_id > 0)
            update_image_color_meta_shop($products_id, $color_id, $image_id, 'shopify_id', $shopify_image_id, $shop_id);
    }

    foreach ($images_to_remove as $shop_image_id)
        ShopifyApiCall("DELETE", '/admin/api/2020-04/products/' . $shopify_id . '/images/' . $shop_image_id . '.json');

}

function UploadShopifyStoreImagesbyShop($POST, $images, $shopify_id, $products_id, $remove_old = 0, $shop_id, $shopify_images = array())
{

    global $sc, $wpdb;

    $all_old_images = $wpdb->get_results("select * from wp_images_meta where product_id = $products_id");

    /*if ($remove_old == 1 && $shop_id !=4698)
	  DeleteShopifyImagebyShop($shopify_id, $shop_id, $products_id);*/
    //get variants with no image associated
    $variants = get_image_variants($shopify_id, $shop_id, $shopify_images);

    foreach ($images as $key => $image) {

        $shopify_image_id = 0;
        $imageadd = array();
        $image_id = $image['id'];
        $imageurl = $image['src'];
        $position = $key + 1;
        $store_id = $image['store_id'];
        $imageadd = array("image" => array("src" => "$imageurl", "position" => $position));
        if ($position == 1 && !empty($variants))
            $imageadd['image']['variant_ids'] = $variants;
        $imageadd['image']['variant_ids'] = array();
        $old_shopify_id = get_image_meta_shop($products_id, $image_id, 'shopify_id', $shop_id);
        if ($store_id > 45987)
            $old_shopify_id = $store_id;
        $method = ($old_shopify_id) ? 'PUT' : 'POST';
        //$imageadd = ($old_shopify_id) ? array("image" => array("id" => $old_shopify_id, "position" => $position)):array("image" => array("src" => "$imageurl", "position" => $position));
        $path = ($old_shopify_id) ? '/admin/api/2020-04/products/' . $shopify_id . '/images/' . $old_shopify_id . '.json' : '/admin/api/2020-04/products/' . $shopify_id . '/images.json';
        //mail("team@ryankikta.com","imageadd",var_export($imageadd,true));

        if (DEBUG_LEVEL >= 2) {
            unset($debug);
            $debug['function'] = 'UploadShopifyStoreImagesbyShop';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'ShopifyApiCall to upload images';
            $debug['action'] = var_export($method, true);
            $debug['path'] = var_export($path, true);
            $debug['data'] = var_export($imageadd, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }

        try {
            $uploadimage = ShopifyApiCall($method, $path, $imageadd);
        } catch (Exception $e) {
            if (DEBUG_LEVEL >= 1) {
                unset($debug);
                $debug['function'] = 'UploadShopifyStoreImagesbyShop';
                $debug['parent'] = 'shopify-functions.php';
                $debug['section'] = 'ShopifyApiCall to upload images exception';
                $debug['action'] = var_export($uploadimage, true);
                $debug['data'] = var_export($e, true);
                $debug['backtrace'] = debug_backtrace();
                error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
            }
        }

        if (DEBUG_LEVEL >= 2) {
            unset($debug);
            $debug['function'] = 'UploadShopifyStoreImagesbyShop';
            $debug['parent'] = 'shopify-functions.php';
            $debug['section'] = 'CompletedShopifyApiCall to upload images';
            $debug['action'] = var_export($uploadimage, true);
            $debug['backtrace'] = debug_backtrace();
            error_log(json_encode($debug) . "\r\n", 3, '/home/ryankikta/logs/debugger.log');
        }
        /*if (isset($uploadimage['errors'])) {
		  $errors = array();
		  foreach ($uploadimage['errors'] as $value) {
		  $errors[] = $value;
		  }

		  $_SESSION['data'] = $POST;
		  $error_title = 'Error upload images in shopify :';
		  $_SESSION['data'] = $POST;
		  $return = array("status"=>0,'error_title'=>$error_title,'errors'=>$errors);
		  echo json_encode($return);
		  exit();
		  }*/
        $shopify_image_id = $uploadimage['id'];
        if (empty($shopify_image_id)) {
            $shopify_image_id = 0;
        }
        if (!empty($shopify_image_id))
            update_image_meta_shop($products_id, $image_id, 'shopify_id', $shopify_image_id, $shop_id);
        /*
        if ($shopify_image_id == 0) {
            wp_insert_post(array(
			  'post_content' => var_export(array("shopify_id" => $shopify_id, "shopify_image_id" => $shopify_image_id), true),
			  'post_title' => esc_sql("adding product image "),
			  'post_status' => 'draft',
			  'comment_status' => 'closed',
			  'ping_status' => 'closed',
			  'post_type' => 'systems'
			));
            wp_mail('team@ryankikta.com', 'adding product image issue', '');
        }
        */
    }
    //this didn't do anything anymore?
    //$all_old_images = $wpdb->get_results("select * from wp_images_meta where product_id = $products_id");

    /*if (!empty($errors)) {
	  $_SESSION['data'] = $POST;
	  $error_title = 'Error upload images in shopify :';
	  $_SESSION['data'] = $POST;
	  $return = array("status"=>0,'error_title'=>$error_title,'errors'=>$errors);
	  echo json_encode($return);
	  exit();
	  }*/
}

function DeleteShopifyImagebyShop($shopify_product_id, $shop_id, $pa_product_id)
{

    /* $imageslist = ShopifyApiCall("GET", '/admin/products/' . $shopify_product_id . '/images.json');
	   foreach ($imageslist as $key => $array) {
	   $image_id = $array['id'];

	   ShopifyApiCall("DELETE", '/admin/products/' . $shopify_product_id . '/images/' . $image_id . '.json');
	   }
	   delete_images_product_meta_shop($pa_product_id, 'shopify_id', $shop_id);*/
}

function buildShopifyVariantsByShop($variants, $user_product_id, $shopifyweight, $shopify_options = array(), $has_title = false, $shopify_weight = array(), $shopify_old_variants = array(), $shop_id = 0, $shopify_images = array())
{
    global $wpdb;
    //wp_mail('team@ryankikta.com','var inside build before',var_export(array($variants,$shopify_options),true));
    $shopify_variants = array();
    $variants3 = array();
    foreach ($variants as $variant) {
        $color_id = $variant['color_id'];
        $size_id = $variant['size_id'];
        $color_name = $variant['color_name'];
        $size_name = $variant['size_name'];
        $position = $variant['position'];
        $price = $variant['price'];
        $sku = $variant['sku'];
        $shopify_variant_id = 0;

        $pa_variant_id = $wpdb->get_var("SELECT id FROM `wp_users_products_colors` WHERE `color_id` = '$color_id' AND `size_id` = '$size_id' AND `users_products_id` = $user_product_id");

        if (isset($pa_variant_id) && ($pa_variant_id != "")) {
            $shopify_variant_id = get_variant_meta_shop($pa_variant_id, "shopify_id", $shop_id);
            if ((!isset($shopify_variant_id)) || ($shopify_variant_id == "")) {
                $shopify_variant_id = 0;
            }
        }

        $variant1 = array("option1" => $size_name, "option2" => $color_name, "sku" => $sku, "price" => $price, "position" => $position);
        if (isset($shopify_options[$color_id . '_' . $size_id]))
            $variant1['option3'] = $shopify_options[$color_id . '_' . $size_id];
        else {
            if ($has_title)
                $variant1['option3'] = str_replace('"', '\"', stripslashes($sku));
        }
        if (isset($shopify_weight[$color_id . '_' . $size_id]) && $shopifyweight == 0)
            $variant1['grams'] = $shopify_weight[$color_id . '_' . $size_id];
        else if ($shopifyweight != 0)
            $variant1['grams'] = $shopifyweight * 453.59237;
        if (isset($shopify_old_variants[$color_id . '_' . $size_id]))
            $variant1['id'] = $shopify_old_variants[$color_id . '_' . $size_id];
        else {
            if ($shopify_variant_id > 0) {

                $variant1['id'] = $shopify_variant_id;
            } else {

                $variants3[] = array("option1" => $size_name, "option2" => $color_name, "sku" => str_replace('"', '\"', stripslashes($sku)), "price" => $price, "position" => $position, "color_id" => $color_id, "size_id" => $size_id);
            }
        }

        if (isset($shopify_images[$color_id . '_' . $size_id]))
            $variant1['image_id'] = $shopify_images[$color_id . '_' . $size_id];
        $shopify_variants[] = $variant1;
    }
    //wp_mail('team@ryankikta.com','var inside build',var_export(array($variants,$shopify_variants),true));
    return array($shopify_variants, $variants3);
}

function DeleteproductFromShops($prodid, $shop_array)
{
    $response = array("status" => 400, "error" => "");
    global $wpdb, $sc;
    if (isset($shop_array) && (count($shop_array) > 0)) {
        foreach ($shop_array as $shopid) {
            $old_version = false;
            $shopify_id = get_product_meta_shop($prodid, "shopify_id", $shopid);
            if (!$shopify_id) {
                $shopify_id = $wpdb->get_var("select shopify_id  from wp_users_products where id=" . $prodid);
                $old_version = true;
            }
            if ($shopify_id) {
                list($shopify_auth_shop, $sc) = getShopifyShopbyId($shopid);
                @extract($shopify_auth_shop);
                $path = '/admin/api/2020-04/products/' . $shopify_id . '.json';
                ShopifyApiCall("DELETE", $path);
                if ($old_version)
                    $shopid = 0;
                delete_product_meta_multi_shop($prodid, "'shopify_id','shopifyvendor','shopifytype','shopifycollection'", $shopid);
                delete_variants_product_meta_shop($prodid, 'shopify_id', $shopid);
                delete_images_product_meta_shop($prodid, 'shopify_id', $shopid);
            }
        }
        $response = array("status" => 200, "sucess" => "TRUE");
    } else
        $response = array("status" => 400, "error" => "Shopify Shops not found.");
    return $response;
}

function DeleteAllShopifyProduct($prodid)
{
    global $wpdb, $sc;
    $response = array("status" => 400, "error" => "");
    $shop_array = get_product_meta_shops($prodid, "shopify_id");
    if (!$shop_array) {
        $user_id = $wpdb->get_var("select users_id from wp_users_products where id=$prodid");
        $shop_array = array($wpdb->get_var("select id from wp_users_shopify where users_id=$user_id"));
    }
    if (isset($shop_array) && (count($shop_array) > 0)) {
        foreach ($shop_array as $shopid) {
            $shopify_id = get_product_meta_shop($prodid, "shopify_id", $shopid);
            if (!$shopify_id)
                $shopify_id = $wpdb->get_var("select shopify_id  from wp_users_products where id=" . $prodid);
            if ($shopify_id) {
                list($shopify_auth_shop, $sc) = getShopifyShopbyId($shopid);
                @extract($shopify_auth_shop);
                if (is_array($shopify_auth_shop)) {
                    $path = '/admin/api/2020-04/products/' . $shopify_id . '.json';
                    ShopifyApiCall("DELETE", $path);
                }
            }
        }
        $response = array("status" => 200, "sucess" => "TRUE");
    } else
        $response = array("status" => 400, "error" => "Shopify Shops not found.");
    return $response;
}

function getCurrentShopifyDataByShop($prodid, $shop_id)
{
    $type = get_product_meta_shop($prodid, "shopifytype", $shop_id);
    $vendor = get_product_meta_shop($prodid, "shopifyvendor", $shop_id);
    $collection_id = get_product_meta_shop($prodid, "shopifycollection", $shop_id);
    $shopify_data = array('newproduct' => 0, 'shopifytype' => $type, 'shopifyvendor' => $vendor, 'collections_id' => $collection_id);
    return $shopify_data;
}

function get_all_shopify_products_by_shop($shop, $token, $shop_id, $user_id)
{
    global $wpdb;
    $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
    $pages = (int)(ShopifyApiCall1("GET", "/admin/api/2020-04/products/count.json", NULL, $sc) / 250) + 1;
    $all_products = array();
    //for ($i = 1; $i <= $pages; $i++) {
        $resp = ShopifyApiCall1("GET", "/admin/api/2020-04/products.json?limit=250&page_info=", NULL, $sc);
	//error_log(print_r($sc->getLastCallHeader(), true));
	preg_match('~<(.*?)>~',$sc->getLastCallHeader()["link"], $next_page_header_link);
	error_log(print_r($next_page_header_link, true));
        foreach ($resp as $product) {
            $pa_product_id = get_product_id_meta_shop($product["id"], "shopify_id", $shop_id);
            if ((!isset($pa_product_id)) || ($pa_product_id == "")) {
                $pa_product_id = $wpdb->get_var("select id from wp_users_products where shopify_id=" . $product["id"]);
            }

            $all_products[] = array(
                "id" => $product["id"],
                "title" => $product["title"],
                "variants" => $product["variants"],
                "status" => "",
                "url" => "",
                "image" => $product["image"]["src"],
                "imported" => ($pa_product_id == NULL) ? 0 : 1,
                "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
            );
        }
    //}
    return $all_products;
}

function get_shopify_product_import($user_id, $shopify_id, $shop_id = 0)
{
    $return = array();
    $auth = ($shop_id != 0) ? getShopifyShopbyId($shop_id) : getShopifyShop($user_id);
    $path = "/admin/api/2020-04/products/" . $shopify_id . ".json";
    $images = array();
    $params = array("fields" => "title,body_html,tags,product_type,vendor,images, variants");
    $res = ShopifyApiCall1("GET", $path, $params, $auth[1]);
    foreach ($res['images'] as $img) {
        $images[$img['id']] = $img['src'];
    }

    $colors = array();
    foreach ($res['variants'] as $variant) {
        if (get_color_id($variant['option1']) != NULL) {
            $colors[$variant['option1']][] = $variant["price"];
        } elseif (get_color_id($variant['option2']) != NULL) {
            $colors[$variant['option2']][] = $variant["price"];
        } elseif (get_color_id($variant['option3']) != NULL) {
            $colors[$variant['option3']][] = $variant["price"];
        }
    }

    $shop_colors = array();
    foreach($colors as $color => $prices){
        $shop_colors[$color][] = min($prices);
        $shop_colors[$color][] = max($prices);
    }

    return array(
        'title' => $res['title'],
        'description' => $res['body_html'],
        'tags' => $res['tags'],
        'shopifyvendor' => $res['vendor'],
        'shopifytype' => $res['product_type'],
        'shop_images' => $images,
        'shop_colors' => $shop_colors,
    );
}

function get_shopify_collections_per_shop($user_id, $shopify_id, $shop_id)
{
    $auth = getShopifyShopbyId($shop_id);
    $allcollects = ShopifyApiCall1("GET", "/admin/api/2020-04/custom_collections.json?product_id=" . $shopify_id, NULL, $auth[1]);

    return $allcollects;
}

function check_product_existe_shopify($shopify_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($shopify_id, "shopify_id", $shop_id);
    if (!$pa_product_id)
        $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id=$user_id and shopify_id=" . $shopify_id);
    if ($pa_product_id)
        $existe = true;
    return array("status" => 200, "data" => $existe);
}

/**
 * check missing webhooks for users with shopify orders for the past 2 weeks
 */
function regenerate_users_webhook($start_date, $end_date)
{
    global $wpdb;
    $k = 0;
    $sql = "select distinct(user_id) from wp_rmproductmanagement_orders where source = 3 and order_time >=  " . strtotime($start_date) . " and order_time <=" . strtotime($end_date);
    //echo $sql;
    $users = $wpdb->get_col($sql);
    //debug($users );exit;
    foreach ($users as $user_id) {
        $shops = $wpdb->get_results("select shop,id,token from wp_users_shopify where users_id=" . $user_id, ARRAY_A);
        // debug( $shops);exit;
        foreach ($shops as $shop) {
            try {
                CreateShopifyWebhook($shop['token'], $shop['id'], $shop['shop'], $user_id);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $k++;

        }
    }

    echo 'number of shops :' . $k;
}
