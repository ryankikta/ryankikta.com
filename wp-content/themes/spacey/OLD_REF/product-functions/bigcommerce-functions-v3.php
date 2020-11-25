<?php
require_once('bigcommerce.php');

use Bigcommerce\Api\Client as Bigcommerce;

define("Bigcommerce_client_id", "gck7knt3cv6ugxbe1j9es2d9g7wdk6z");
define("Bigcommerce_client_secret", "m4gu2aoevrrbp25zo94i8ryumnkdhi7");

function bigcommerce_connect($auth)
{

    @extract($auth);
    Bigcommerce::configure(array(
        'client_id' => Bigcommerce_client_id,
        'auth_token' => $bigcommerce_auth_token,
        'store_hash' => $bigcommerce_store_hash
    ));
    Bigcommerce::verifyPeer(false);
}

function check_bigcommerce_shop($user_id, $type = 1)
{
    global $wpdb;
    $count = $wpdb->get_var("select count(id) from wp_users_bigcommerce where users_id=$user_id");
    if ($type == 2)
        return (int)$count;
    if ($count > 0)
        return true;
    return false;
}

function getBigcommerceShop($user_id)
{

    $bigcommerce_auth = array();
    $checkuser = $wpdb->get_result("select `id`,`store_hash`,`access_token`,`store_name`,`shop` from `wp_users_bigcommerce` where `users_id` = $user_id");
    $numbigcomerceshop = $wpdb->num_rows($checkuser);
    if ($numbigcomerceshop != 0) {
        $shoprow = $wpdb->get_row($checkuser);
        $bigcommerce_shop_id = $shoprow[0];
        $bigcommerce_store_hash = $shoprow[1];
        $bigcommerce_auth_token = $shoprow[2];
        $storename = $shoprow[3];
        $bigcommerce_shop = $shoprow[4];
        $bigcommerce_auth = array('shop_id' => $bigcommerce_shop_id, 'user_id' => $user_id, 'bigcommerce_store_hash' => $bigcommerce_store_hash, 'bigcommerce_auth_token' => $bigcommerce_auth_token, 'storename' => $storename, 'bigcommerce_shop' => $bigcommerce_shop);
    }
    return $bigcommerce_auth;
}

function getBigcommerceShopbyId($id)
{
    $bigcommerce_auth = array();
    $checkuser = $wpdb->get_result("select `users_id`,`store_hash`,`access_token`,`store_name`,`shop` from `wp_users_bigcommerce` where `id` = $id");
    $numbigcomerceshop = $wpdb->num_rows($checkuser);
    if ($numbigcomerceshop !== 0) {
        $shoprow = $wpdb->get_row($checkuser);
        $user_id = $shoprow[0];
        $bigcommerce_store_hash = $shoprow[1];
        $bigcommerce_auth_token = $shoprow[2];
        $storename = $shoprow[3];
        $bigcommerce_shop = $shoprow[4];
        $bigcommerce_auth = array('shop_id' => $id, 'user_id' => $user_id, 'bigcommerce_store_hash' => $bigcommerce_store_hash, 'bigcommerce_auth_token' => $bigcommerce_auth_token, 'storename' => $storename, 'bigcommerce_shop' => $bigcommerce_shop);
    }
    return $bigcommerce_auth;
}

function getCurrentBigcommerceData($prodid)
{
    $selectproductquery = $wpdb->get_result("select * from `wp_users_products` where `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $bigcommerce_id = get_product_meta($prodid, 'bigcommerce_id');
    $bigcommerce_cat = get_product_meta($prodid, 'bigcommerce_cat');
    $bigcommerce_featured = get_product_meta($prodid, 'bigcommerce_featured');
    $bigcommerce_brand = get_product_meta($prodid, 'bigcommerce_brand');
    $bigcommerce_warranty = get_product_meta($prodid, 'bigcommerce_warranty');

    $bigcommerce_data = array('bigcommerce_id' => $bigcommerce_id, 'bigcommercecategory' => $bigcommerce_cat,
        'bigcommercefeaturedproduct' => $bigcommerce_featured, 'bigcommercebrand' => $bigcommerce_brand, 'bigcommerceinfo' => $bigcommerce_warranty);
    return $bigcommerce_data;
}

function getBigcommerceData($data)
{

    if (isset($data['bigcommercenewproduct']))
        $bigcommercenewproduct = esc_sql($data['bigcommercenewproduct']);
    else
        $bigcommercenewproduct = 0;
    $bigcommercefeaturedproduct = !isset($data['bigcommercefeaturedproduct']) ? 0 : esc_sql($data['bigcommercefeaturedproduct']);
    $bigcommercebrand = (isset($data['bigcommercebrand'])) ? esc_sql($data['bigcommercebrand']) : 0;
    $bigcommercecategory = esc_sql($data['bigcommercecategory']);
    $bigcommerceinfo = trim($data['bigcommerceinfo']);
    $bigcommerceinfo = str_replace("\r\n", "", $bigcommerceinfo);
    $i = 0;
    while ($i < strlen($bigcommerceinfo)) {
        $bigcommerceinfo = str_replace("\\", '', $bigcommerceinfo);
        $bigcommerceinfo = str_replace('"', '\"', $bigcommerceinfo);
        $i++;
    }

    if ($bigcommercecategory == '')
        $bigcommercecategory = 0;
    return array('bigcommercenewproduct' => $bigcommercenewproduct, 'bigcommercecategory' => $bigcommercecategory,
        'bigcommercefeaturedproduct' => $bigcommercefeaturedproduct, 'bigcommercebrand' => $bigcommercebrand, 'bigcommerceinfo' => $bigcommerceinfo);
}

function get_current_bigcommerce_data_shop($prodid, $shop_id)
{

    $bigcommerce_id = get_product_meta_shop($prodid, "bigcommerce_id", $shop_id);
    $bigcommerce_cat = get_product_meta_shop($prodid, "bigcommerce_cat", $shop_id);
    $bigcommerce_featured = get_product_meta_shop($prodid, "bigcommerce_featured", $shop_id);
    $bigcommerce_brand = get_product_meta_shop($prodid, "bigcommerce_brand", $shop_id);
    $bigcommerce_warranty = get_product_meta_shop($prodid, 'bigcommerce_warranty', $shop_id);

    return array('bigcommerce_id' => $bigcommerce_id,
        'bigcommercecategory' => $bigcommerce_cat,
        'bigcommercefeaturedproduct' => $bigcommerce_featured,
        'bigcommercebrand' => $bigcommerce_brand,
        'bigcommerceinfo' => $bigcommerce_warranty);
}

function get_bigcommerce_data_shop($data, $shop_id)
{

    if (isset($data['bigcommercenewproduct' . $shop_id]))
        $bigcommercenewproduct = esc_sql($data['bigcommercenewproduct' . $shop_id]);
    else
        $bigcommercenewproduct = 0;
    $bigcommercefeaturedproduct = !isset($data['bigcommercefeaturedproduct' . $shop_id]) ? 0 : esc_sql($data['bigcommercefeaturedproduct' . $shop_id]);
    $bigcommercebrand = (isset($data['bigcommercebrand' . $shop_id])) ? esc_sql($data['bigcommercebrand' . $shop_id]) : 0;
    $bigcommercecategory = esc_sql($data['bigcommercecategory' . $shop_id]);
    $bigcommerceinfo = trim($data['bigcommerceinfo' . $shop_id]);
    $bigcommerceinfo = str_replace("\r\n", "", $bigcommerceinfo);
    $i = 0;
    while ($i < strlen($bigcommerceinfo)) {
        $bigcommerceinfo = str_replace("\\", '', $bigcommerceinfo);
        $bigcommerceinfo = str_replace('"', '\"', $bigcommerceinfo);
        $i++;
    }

    if ($bigcommercecategory == '')
        $bigcommercecategory = 0;
    return array('bigcommercenewproduct' => $bigcommercenewproduct, 'bigcommercecategory' => $bigcommercecategory,
        'bigcommercefeaturedproduct' => $bigcommercefeaturedproduct, 'bigcommercebrand' => $bigcommercebrand, 'bigcommerceinfo' => $bigcommerceinfo);
}

function getBigcommerceProductData($productid, $bigcommerce_id, $auth)
{
    $update_bigcommerce = 0;
    if (!empty($auth)) {
        @extract($auth);
        bigcommerce_connect($auth);
        if ($bigcommerce_id != 0) {
            $Bigcommerce = callBigcommerceApi("getProduct", $bigcommerce_id);
            if (!empty($Bigcommerce))
                $update_bigcommerce = 1;
            else {
                $optionsetid = get_product_meta_shop($productid, 'option_set_id', $shop_id);
                $optioncolorId = get_product_meta_shop($productid, 'option_color_id', $shop_id);
                $optionsizeId = get_product_meta_shop($productid, 'option_size_id', $shop_id);

                if ($optionsetid)
                    callBigcommerceApi("deleteOptionsets", $optionsetid);

                if ($optioncolorId)
                    callBigcommerceApi("deleteOption", $optioncolorId);

                if ($optionsizeId)
                    callBigcommerceApi("deleteOption", $optionsizeId);
            }
        }
    }
    return $update_bigcommerce;
}

function deleteBigcommerceProduct($bigcommerce_id, $auth, $productid = 0)
{
    global $wpdb;
    @extract($auth);
    bigcommerce_connect($auth);
    callBigcommerceApi("deleteProduct", $bigcommerce_id);
    $prodid = get_product_id_meta_shop($bigcommerce_id, "bigcommerce_id", $shop_id);
    if (!$prodid) {
        $shop_id = 0;
        $prodid = get_product_id_meta_shop($bigcommerce_id, "bigcommerce_id", $shop_id);
    }

    $optionsetid = get_product_meta_shop($prodid, 'option_set_id', $shop_id);
    $optioncolorId = get_product_meta_shop($prodid, 'option_color_id', $shop_id);
    $optionsizeId = get_product_meta_shop($prodid, 'option_size_id', $shop_id);

    callBigcommerceApi("deleteOptionsets", $optionsetid);
    callBigcommerceApi("deleteOption", $optioncolorId);
    callBigcommerceApi("deleteOption", $optionsizeId);
    if ($productid != 0) {
        delete_product_meta_multi_shop($productid, "'bigcommerce_id','bigcommerce_cat','bigcommerce_featured','bigcommerce_brand','bigcommerce_warranty','option_set_id','option_color_id','option_size_id'", $shop_id);
        delete_variants_product_meta_shop($productid, 'bigcommerce_id', $shop_id);
    }
}

function buildBigcommerceVariants($variants)
{
    $bigcommerce_variants = array();

    if ($_SERVER['REMOTE_ADDR'] == "59.162.181.90") {
        $ffff = array();

        $variants = array_orderby($variants, 'color_name', SORT_DESC);
        $sizes = array();

        foreach ($variants as $key => $variant) {
            $sizes[] = $variant['size_name'];
            $bigcommerce_variants[] = array("color_name" => $variant['color_name'], "size_name" => $variant['size_name'], "sku" => $variant['sku'], "price" => $variant['price'], "position" => $variant['position'], "color_code" => $variant['color_code'], "color_id" => $variant['color_id'], "size_id" => $variant['size_id']);
            if (isset($bigcommerce_variants[$key]['bigcommerce_id']) && $bigcommerce_variants[$key]['bigcommerce_id'] != "") {
                $bigcommerce_variants[$key]['id'] = $bigcommerce_variants[$key]['bigcommerce_id'];
            }
        }
        $e = 0;
        $r = 0;
        $s = 0;
        $colorIDS = "";

        foreach ($bigcommerce_variants as $v_new) {
            if ($v_new['color_id'] != $colorIDS && $colorIDS != "") {
                $e = 0;
                $ffff[$r] = $ttt;
                $r++;
            }
            $ttt[$e] = $v_new;
            $colorIDS = $v_new['color_id'];

            if (count($bigcommerce_variants) == $s + 1) {
                $ffff[$r] = $ttt;
            }
            $e++;
            $s++;
        }

        $new_variant_array = array();
        $new_refined_arr = array();

        for ($lp = 0; $lp < count($ffff); $lp++) {
            $colored_array = array();
            $order_with_arr = array();
            $colored_array = $ffff[$lp];

            foreach ($colored_array as $newarr) {
                $size_res = mysql_fetch_array($wpdb->get_result("select * from `wp_rmproductmanagement_sizes` where `size_id` = '" . $newarr['size_id'] . "' "));
                $size_order = $size_res['s_ordering'];
                $newarr['ordering'] = $size_order;
                $order_with_arr[] = $newarr;
            }

            $size_options = array();
            $size_options = array_orderby($order_with_arr, 'ordering', SORT_ASC);

            foreach ($size_options as $refined_arr) {
                unset($refined_arr['ordering']);
                $new_refined_arr[] = $refined_arr;
            }
        }
        $bigcommerce_variants = $new_refined_arr;
    } else {
        foreach ($variants as $key => $variant) {
            $bigcommerce_variants[] = array("color_name" => $variant['color_name'], "size_name" => $variant['size_name'], "sku" => $variant['sku'], "price" => $variant['price'], "position" => $variant['position'], "color_code" => $variant['color_code'], "color_id" => $variant['color_id'], "size_id" => $variant['size_id']);
            if (isset($bigcommerce_variants[$key]['bigcommerce_id']) && $bigcommerce_variants[$key]['bigcommerce_id'] != "") {
                $bigcommerce_variants[$key]['id'] = $bigcommerce_variants[$key]['bigcommerce_id'];
            }
        }
    }
    return $bigcommerce_variants;
}

function create_option($product_id, $field, $data, $shop_id)
{
    $option = callBigcommerceApi("createOptions", $data);
    update_product_meta_shop($product_id, 'option_' . $field . '_id', $option->id, $shop_id);
}

function connect_option_to_color_size($product_id, $optionsetid, $shop_id)
{

    $option_color_id = get_product_meta_shop($product_id, 'option_color_id', $shop_id);
    $option_size_id = get_product_meta_shop($product_id, 'option_size_id', $shop_id);
    $assign_color = array('option_id' => $option_color_id, 'display_name' => "Colors", 'is_required' => true);
    $assign_size = array('option_id' => $option_size_id, 'display_name' => "Sizes", 'is_required' => true);
    callBigcommerceApi("createOptionsets_Options", array($assign_color, $optionsetid), 2);
    callBigcommerceApi("createOptionsets_Options", array($assign_size, $optionsetid), 2);
}

function addBigCommerceProduct($POST, $data, $bigcommerce_data, $big_id, $big_auth, $basicprice, $currentuserid, $variants_big, $products_id, $shop_id)
{
    global $wpdb;
    @extract($data);
    @extract($bigcommerce_data);
    bigcommerce_connect($big_auth);
    $error_connect = false;
    $is_featured = ($bigcommercefeaturedproduct == 1) ? TRUE : FALSE;
    $arr = array('big_id' => $big_id, 'pagetype' => $POST['pagetype'], 'prod_id' => $products_id);
    if ($big_id == 0 || ($big_id != 0 && $POST['pagetype'] == 1)) {
        $optionset = array('name' => 'RyanKikta #' . $products_id);
        $option_sets = callBigcommerceApi("createOptionsets", $optionset);
        $optionsetid = $option_sets->id;
        update_product_meta_shop($products_id, 'option_set_id', $optionsetid, $shop_id);

        $color_opt = array('name' => 'RyanKikta Colors #' . $products_id, 'display_name' => 'Colors', 'type' => 'CS');
        create_option($products_id, 'color', $color_opt, $shop_id);

        $size_opt = array('name' => 'RyanKikta Sizes #' . $products_id, 'display_name' => 'Sizes', 'type' => 'RT');
        create_option($products_id, 'size', $size_opt, $shop_id);
        $optionset_id = get_product_meta_shop($products_id, 'option_set_id', $shop_id);
        $optioncolorId = get_product_meta_shop($products_id, 'option_color_id', $shop_id);
        $optionsizeId = get_product_meta_shop($products_id, 'option_size_id', $shop_id);
        $arr['optionsetid'] = $optionset_id;
        $arr['option_color_id'] = $optioncolorId;
        $arr['option_size_id'] = $optionsizeId;
        /*************************Assign Option Color & Size To Option Set Product**********************/
        connect_option_to_color_size($products_id, $optionsetid, $shop_id);
    } else {
        //check option color
        $optionid = get_product_meta_shop($products_id, 'option_color_id', $shop_id);
        $option = callBigcommerceApi("getOption_byid", $optionid);
        if (!$option || $optionid == "") {
            $error_connect = true;
            $filter = array('name' => 'RyanKikta Colors #' . $products_id);
            $options_color = callBigcommerceApi("getOptions", $filter);
            if (empty($options_color)) {
                $color_opt = array('name' => 'RyanKikta Colors #' . $products_id, 'display_name' => 'Colors', 'type' => 'CS');
                create_option($products_id, 'color', $color_opt, $shop_id);
            } else
                update_product_meta_shop($products_id, 'option_color_id', $options_color[0]->id, $shop_id);
        }
        //check option size
        $optionid = get_product_meta_shop($products_id, 'option_size_id', $shop_id);
        $option = callBigcommerceApi("getOption_byid", $optionid);
        if (!$option || $optionid == "") {
            $error_connect = true;
            $filter = array('name' => 'RyanKikta Sizes #' . $products_id);
            $options_size = callBigcommerceApi("getOptions", $filter);
            if (empty($options_size)) {
                $size_opt = array('name' => 'RyanKikta Sizes #' . $products_id, 'display_name' => 'Sizes', 'type' => 'RT');
                create_option($products_id, 'size', $size_opt, $shop_id);
            } else
                update_product_meta_shop($products_id, 'option_size_id', $options_size[0]->id, $shop_id);
        }
        //check option set
        $optionsetid = get_product_meta_shop($products_id, 'option_set_id', $shop_id);
        $option_set = callBigcommerceApi("getOptionSets_byid", $optionsetid);
        if (!$option_set || $optionsetid == "") {
            $error_connect = true;
            $optionset = array('name' => 'RyanKikta #' . $products_id);
            $option_sets_filter = callBigcommerceApi("getOptionSets", $optionset);
            if (empty($option_sets_filter)) {
                $option_sets = callBigcommerceApi("createOptionsets", $optionset);
                $optionsetid = $option_sets->id;
            } else
                $optionsetid = $option_sets_filter[0]->id;
            update_product_meta_shop($products_id, 'option_set_id', $optionsetid, $shop_id);
        }
        if ($error_connect)
            connect_option_to_color_size($products_id, $optionsetid, $shop_id);
    }
    $bigconfprod = array("name" => $title, "sku" => $sku, "type" => "physical", "description" => $description, "price" => $basicprice,
        "categories" => array($bigcommercecategory), "availability" => "available", "weight" => $weight, "is_visible" => TRUE,
        "is_featured" => $is_featured, "warranty" => strip_tags($bigcommerceinfo), "option_set_id" => $optionsetid
    );
    if (intval($bigcommercebrand) != 0)
        $bigconfprod['brand_id'] = intval($bigcommercebrand);
    if ($big_id == 0) {
        $result = callBigcommerceApi("createProduct", $bigconfprod);
        $bigcommerce_id = $result->id;
        if ($POST['pagetype'] == 1)
            $_SESSION['shops']['bigcommerce_ids'][$shop_id] = $result->id;
        if ($bigcommerce_id == "") {
            $errors = Bigcommerce::getLastError();
            $errors = processBigcommerceErrors($errors);

            $logs = array();
            $logs['post'] = $POST;
            $logs['errors'] = $errors;
            $logs['user_id'] = $currentuserid;
            $export = var_export($logs, true);
            wp_insert_post(array(
                'post_content' => $export,
                'post_title' => esc_sql("could  not creat bigcommerce product"),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'
            ));
            if ($POST['pagetype'] == 1) {
                $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
                $count_shop = check_bigcommerce_shop($currentuserid, 2);
                $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_bigcommerce where id=$shop_id") . '"';
                $error_title = 'Error ' . $text . ' product in Bigcommerce ' . $shop_text . ':';
                $_SESSION['data'] = $POST;
                $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
                echo json_encode($return);
                exit();
            }
        }
    } else {
        $bigcommerce_id = $big_id;
        // remove all product skus
        callBigcommerceApi("deleteAllProductSku", $bigcommerce_id);
        // remove all product rules
        callBigcommerceApi("deleteAllProductRule", $bigcommerce_id);
        // remove all product images
        callBigcommerceApi("deleteProductImages", $bigcommerce_id);
        // update configurable product 
        $response = callBigcommerceApi("updateProduct", array($bigcommerce_id, $bigconfprod), 2);
    }
    $pa_product_id_old = get_product_id_meta_shop($bigcommerce_id, "bigcommerce_id", $shop_id);
    if ($pa_product_id_old && $pa_product_id_old != $products_id)
        delete_variants_product_meta_shop($pa_product_id_old, 'bigcommerce_id', $shop_id);

    $all_meta = array('bigcommerce_id' => 'NULL', 'bigcommerce_featured' => 'NULL', 'bigcommerce_brand' => 'NULL', 'bigcommerce_cat' => 'NULL', 'bigcommerce_warranty' => 'NULL');
    $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $products_id and shopid = $shop_id", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['meta_key']] = $res['meta_id'];
    }
    $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $products_id) ? $pa_product_id_old : 0;
    if ($prod_to_deconnect) {
        update_product_meta_shop($products_id, 'bigcommerce_id', $bigcommerce_id, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'bigcommerce_featured', $bigcommercefeaturedproduct, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'bigcommerce_brand', $bigcommercebrand, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'bigcommerce_cat', $bigcommercecategory, $shop_id, 0, $prod_to_deconnect);
        update_product_meta_shop($products_id, 'bigcommerce_warranty', stripcslashes($bigcommerceinfo), $shop_id, 0, $prod_to_deconnect);
    } else {
        $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['bigcommerce_id']},$products_id,'bigcommerce_id','$bigcommerce_id',$shop_id),"
            . " ({$all_meta['bigcommerce_featured']},$products_id,'bigcommerce_featured','" . $bigcommercefeaturedproduct . "',$shop_id),"
            . " ({$all_meta['bigcommerce_brand']},$products_id,'bigcommerce_brand','" . $bigcommercebrand . "',$shop_id),"
            . " ({$all_meta['bigcommerce_cat']},$products_id,'bigcommerce_cat','" . $bigcommercecategory . "',$shop_id),  "
            . " ({$all_meta['bigcommerce_warranty']},$products_id,'bigcommerce_warranty','" . stripcslashes($bigcommerceinfo) . "',$shop_id)  "
            . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";
        $wpdb->query($sql);
    }
    $sql = "update `wp_users_products` set `bigcommerceactive` = 1   where `id` = $products_id";
    $query = $wpdb->get_result($sql);
    if (!$query) {
        $logs = array();
        $logs['sql'] = mysql_escape_string($sql);
        wp_insert_post(array(
            'post_content' => var_export($logs, true),
            'post_title' => esc_sql("could  not update bigcommerce product id "),
            'post_status' => 'draft',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_type' => 'systems'
        ));
    }
    updateBigcommerceVariants($POST, $bigcommerce_id, $products_id, $big_auth, $variants_big, $basicprice, $currentuserid, $shop_id);
    return $bigcommerce_id;
}

function updateBigcommerceVariants($POST, $big_id, $products_id, $auth, $variants, $basicprice, $userid, $shop_id)
{

    global $wpdb;

    $all_vars = array();
    $all_meta = array();
    @extract($auth);
    bigcommerce_connect($auth);
    $optionsizeId = get_product_meta_shop($products_id, 'option_size_id', $shop_id);
    $optioncolorId = get_product_meta_shop($products_id, 'option_color_id', $shop_id);
    callBigcommerceApi("deleteOptionValues", $optionsizeId);
    callBigcommerceApi("deleteOptionValues", $optioncolorId);

    $colors = array();
    $sizes = array();
    foreach ($variants as $variant) {
        sleep(1);
        if (!in_array($variant['color_name'], $colors)) {
            $colors[] = $variant['color_name'];
            $color = array("label" => $variant['color_name'], "value" => $variant['color_code']);
            Bigcommerce::createOptionsValue($optioncolorId, $color);
        }
        if (!in_array($variant['size_name'], $sizes)) {
            $sizes[] = $variant['size_name'];
            $size = array("label" => $variant['size_name'], "value" => $variant['size_name']);
            Bigcommerce::createOptionsValue($optionsizeId, $size);
        }
        sleep(0.6);
    }

    $bigcommercecolors = callBigcommerceApi("getOptionValues", $optioncolorId);
    $bigcommercesizes = callBigcommerceApi("getOptionValues", $optionsizeId);

    $result = callBigcommerceApi("getProductOptionValues", $big_id);
    foreach ($result as $value) {
        if ($value->option_id == $optionsizeId)
            $sizeprodoptid = $value->id;
        if ($value->option_id == $optioncolorId)
            $colorprodoptid = $value->id;
    }

    $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $shop_id and meta_key='bigcommerce_id' and product_id=$products_id", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['variant_id']] = $res['id'];
    }
    foreach ($variants as $key => $variant) {
        $sku = $variant['sku'];
        $price = $variant['price'];
        $sizename = $variant['size_name'];
        $size_id = $variant['size_id'];
        $colorname = $variant['color_name'];
        $color_id = $variant['color_id'];
        $color_code = $variant['color_code'];

        foreach ($bigcommercesizes as $size) {
            if ($size->value == $sizename) {
                $sizevalueid = $size->id;
                $x = array('product_option_id' => $sizeprodoptid, 'option_value_id' => $sizevalueid);
                break;
            }
        }

        foreach ($bigcommercecolors as $color) {
            if ($color->value == $color_code) {
                $colorvalueid = $color->id;
                $y = array('product_option_id' => $colorprodoptid, 'option_value_id' => $colorvalueid);
                break;
            }
        }

        $fields = array(
            "sku" => $sku,
            "cost_price" => $price,
            "price" => $price,
            "upc" => "",
            "inventory_level" => 0,
            "inventory_warning_level" => 0,
            "bin_picking_number" => "",
            "adjusted_weight" => null,
            "is_purchasing_disabled" => false,
            "purchasing_disabled_message" => "",
            "image_file" => "",
            "options" => array((object)$x, (object)$y)
        );
        $skucreated = callBigcommerceApi("createProductSku", array($big_id, $fields), 2);
        if ($POST['pagetype'] == 1) {
            $errors = Bigcommerce::getLastError();
            $errors = processBigcommerceErrors($errors);
        }

        $variantid = $wpdb->get_var("select id from `wp_users_products_colors` where users_products_id=$products_id and color_id = '$color_id' and size_id = '$size_id'");
        //update_variant_meta_shop($products_id, $variantid, 'bigcommerce_id', $skucreated->id, $shop_id);
        $_tmp = array('variant_id' => $variantid, 'bigcommerce_id' => $skucreated->id);
        $_tmp['id'] = (isset($all_meta[$variantid])) ? $all_meta[$variantid] : 'NULL';
        $all_vars[] = $_tmp;
    }
    $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
    $_tmp = array();
    foreach ($all_vars as $_var) {
        $_tmp[] = " ({$_var['id']},'$products_id','{$_var['variant_id']}','bigcommerce_id','{$_var['bigcommerce_id']}','$shop_id') ";

    }
    $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
    $wpdb->query($sql_var);
    if (!empty($errors)) {
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_bigcommerce_shop($userid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_bigcommerce where id=$shop_id") . '"';
        $error_title = 'Error ' . $text . ' variants in Bigcommerce ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function UploadBigcommerceStoreImages($POST, $images, $bigcommerce_id, $auth, $products_id, $shop_id, $remove_old = 0)
{
    global $wpdb;
    @extract($auth);
    if ($remove_old == 1)
        DeleteBigcommerceImage($bigcommerce_id, $auth);

    bigcommerce_connect($auth);
    foreach ($images as $key => $image) {
        $is_thumbnail = ($key == 0) ? true : false;
        $fields = array(
            "image_file" => $image['src'],
            "is_thumbnail" => $is_thumbnail,
            "sort_order" => $key
        );
        $result = callBigcommerceApi("createProductimage", array($bigcommerce_id, $fields), 2);
        if ($POST['pagetype'] == 1 && $remove_old == 0) {
            $errors = Bigcommerce::getLastError();
            $errors = processBigcommerceErrors($errors);
        }
        if ($result->id != "")
            insert_image_meta_shop($products_id, $image['id'], "bigcommerce_id", $result->id, $shop_id);
    }
    if (!empty($errors)) {
        $count_shop = check_bigcommerce_shop($user_id, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_bigcommerce where id=$shop_id") . '"';
        $error_title = 'Error upload images in Bigcommerce ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function DeleteBigcommerceImage($bigcommerce_id, $auth)
{
    bigcommerce_connect($auth);
    callBigcommerceApi("deleteProductImages", $bigcommerce_id);
}

function delete_bigcommerce_variant($bigcommerce_id, $user_id, $prodid, $color_id, $size_id, $variant_id)
{
    global $wpdb;
    $auth = getBigcommerceShop($user_id);
    bigcommerce_connect($auth);
    $color_name = $wpdb->get_var("select color_name from wp_rmproductmanagement_colors where color_id=" . $color_id);
    $size_name = $wpdb->get_var("select size_name from wp_rmproductmanagement_sizes where size_id=" . $size_id);
    // delete sku
    callBigcommerceApi("deleteProductSku", array($bigcommerce_id, $variant_id), 2);
    // delete rule sku
    $option_size_id = get_product_meta($prodid, 'option_size_id');
    $option_color_id = get_product_meta($prodid, 'option_color_id');
    $bigcommerce_options_colors = callBigcommerceApi("getOptionValues", $option_color_id);
    $bigcommerce_options_sizes = callBigcommerceApi("getOptionValues", $option_size_id);
    $product_rules = callBigcommerceApi("getProductRules", $bigcommerce_id);
    foreach ($bigcommerce_options_sizes as $option) {
        if (strtolower($option->label) == strtolower($size_name)) {
            $option_size_value_id = $option->id;
            break;
        }
    }
    foreach ($bigcommerce_options_colors as $option) {
        if (strtolower($option->label) == strtolower($color_name)) {
            $option_color_value_id = $option->id;
            break;
        }
    }
    foreach ($product_rules as $rule) {
        $conditions = $rule->conditions;
        if ($conditions[0]->option_value_id == $option_size_value_id && $conditions[1]->option_value_id == $option_color_value_id)
            callBigcommerceApi("deleteProductRule", array($bigcommerce_id, $rule->id), 2);
    }
    $count_color = $wpdb->get_var("select count(color_id) from wp_users_products_colors where color_id =" . (int)$color_id . " and users_products_id=" . (int)$prodid);
    if ($count_color == 0)
        callBigcommerceApi("deleteOptionValue", array($option_color_id, $option_color_value_id), 2);
}

/*
 * type = 1 : remove size
 * type = 2 : remove color
 * type = 3 : desactivate product
 */

function synchroniz_bigcommerce_variant($user_id, $prodid, $type, $option_toremove)
{
    global $wpdb;
    $auth = getBigcommerceShop($user_id);
    bigcommerce_connect($auth);
    $bigcommerce_id = get_product_meta($prodid, 'bigcommerce_id');
    if ($type == 1 || $type == 2) {
        // delete sku
        if ($type == 1) {
            $attribute = "size_id";
            $option_variant_id = get_size_id($option_toremove);
        } else {
            $attribute = "color_id";
            $option_variant_id = get_color_id($option_toremove);
        }
        $all_variants = $wpdb->get_results("select `bigcommerce_id` from `wp_users_products_colors` where `" . $attribute . "` = '$option_variant_id' and `users_products_id` = $prodid", ARRAY_A);
        foreach ($all_variants as $var) {
            $big_variant_id = $var['bigcommerce_id'];
            callBigcommerceApi("deleteProductSku", array($bigcommerce_id, $big_variant_id), 2);
        }
        // delete rule sku
        $option_type_id = ($type == 1) ? get_product_meta($prodid, 'option_size_id') : get_product_meta($prodid, 'option_color_id');
        $bigcommerce_options = callBigcommerceApi("getOptionValues", $option_type_id);
        $product_rules = callBigcommerceApi("getProductRules", $bigcommerce_id);
        foreach ($bigcommerce_options as $option) {
            if ($option->label == $option_toremove) {
                $option_value_id = $option->id;
                break;
            }
        }
        foreach ($product_rules as $rule) {
            $conditions = $rule->conditions;
            if ($conditions[$type - 1]->option_value_id == $option_value_id)
                callBigcommerceApi("deleteProductRule", array($bigcommerce_id, $rule->id), 2);
        }
        //delete option value
        callBigcommerceApi("deleteOptionValue", array($option_type_id, $option_value_id), 2);
    }

    if ($type == 3) {
        // desactivate product
        callBigcommerceApi("updateProduct", array($bigcommerce_id, array("is_visible" => FALSE)), 2);
    }
    if ($type == 4) {
        // activate product
        callBigcommerceApi("updateProduct", array($bigcommerce_id, array("is_visible" => TRUE)), 2);
    }
}

function DuplicateBigcommerceImages($currentusername, $images, $auth, $big_prod_id, $has_defaultimage, $duplicate_id, $shop_id)
{
    global $wpdb;
    $featured_Bigc_set = 0;
    bigcommerce_connect($auth);
    foreach ($images as $key => $image) {
        $default_image = 0;
        if ($image['type'] == 4) {
            $imagefilename = $wpdb->get_var("select `fileName` from `wp_userfiles` where `fileID` = " . $image['image_id']);
            $blogurl = get_bloginfo('url');
            $imageurl = $blogurl . "/download.php?source=RGlzcGxheTI=&f=" . urlencode(base64_encode($imagefilename)) . "&u=" . urlencode(base64_encode($currentusername)) . "&ftype=SW1hZ2U=";
            if ($has_defaultimage == 0) {
                $sort_order = $key;
                $is_thumbnail = ($key == 0) ? true : false;
            } else {
                if ($image['defaultimage'] == 1) {
                    $default_image = 1;
                    $sort_order = 0;
                    $featured_Bigc_set = 1;
                    $is_thumbnail = true;
                } else {
                    $is_thumbnail = false;
                    $sort_order = $key + 1;
                    if ($featured_Bigc_set == 1)
                        $sort_order--;
                }
            }
            $fields = array(
                "image_file" => $imageurl,
                "is_thumbnail" => $is_thumbnail,
                "sort_order" => $sort_order
            );
            $result = callBigcommerceApi("createProductimage", array($big_prod_id, $fields), 2);
            if ($result->id != "")
                insert_image_meta_shop($duplicate_id, $image['image_id'], "bigcommerce_id", $result->id, $shop_id);
        }
    }
}

function processBigcommerceErrors($errors)
{
    $all_errors = array();
    if (is_array($errors))
        foreach ($errors as $error) {
            if ($error->status == 400)
                $all_errors[] = $error->message;
            if ($error->status == 409)
                $all_errors[] = $error->details->conflict_reason;
        }
    elseif ($errors !== false && $errors->error != "")
        $all_errors[] = $errors->error;
    return $all_errors;
}

/*************************************Order Functions******************************************/

function delete_order($order_id, $Auth)
{
    bigcommerce_connect($Auth);
    callBigcommerceApi("deleteOrder", $order_id);
}

function get_order_bigcommerce_data($orderid, $Auth)
{
    bigcommerce_connect($Auth);
    $order = callBigcommerceApi("getOrder", $orderid);
    return array('order_status' => $order->status, 'status_id' => $order->status_id, 'shop_order_id' => $order->id, 'shop_order_name' => $order->id, 'shipping_addresses' => $order->shipping_addresses, 'billing_address' => $order->billing_address, 'itemsinfo' => $order->products);
}

function bigcommerce_billing_address($billing_addr)
{
    $email = $billing_addr->email;
    $customerphone = $billing_addr->phone;
    return array('email' => $email, 'customerphone' => $customerphone);
}

function bigcommerce_shipping_address($shipping_addr)
{

    $shippingaddressinfo = $shipping_addr[0];
    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = $shippingaddressinfo->first_name . " " . $shippingaddressinfo->last_name;
    $shippingaddress1['address1'] = $shippingaddressinfo->street_1;
    $shippingaddress1['address2'] = $shippingaddressinfo->street_2;
    $shippingaddress1['city'] = $shippingaddressinfo->city;
    $shippingaddress1['state'] = $shippingaddressinfo->state;
    $shippingaddress1['zipcode'] = $shippingaddressinfo->zip;
    $shippingaddress1['country'] = $shippingaddressinfo->country_iso2;
    $address2 = ($shippingaddressinfo->street_2 != "") ? $shippingaddress1['address2'] . "\n" : "";
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $shippingaddressinfo->country;
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);

    if ($shippingaddressinfo->country_iso2 == "US") {
        $shipping_id = 1;
    } elseif ($shippingaddressinfo->country_iso2 == "CA") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }
    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $shippingaddressinfo->country, 'shippingaddress_state' => $shippingaddressinfo->state, 'shippingaddress_state_code' => $shippingaddressinfo->country_iso2, 'shippingaddress_zip' => $shippingaddressinfo->zip, 'shipping_id' => $shipping_id, 'paypal_address' => $paypal_address);
}

function get_allitem_bigcommerce($itemsinfo, $user_id, $auth)
{
    global $wpdb;
    $items = array();
    @extract($auth);
    bigcommerce_connect($auth);
    foreach ($itemsinfo as $key => $value) {
        $product = array();
        $big_id = $wpdb->escape($value->product_id);
        $item_id = $wpdb->escape($value->id);
        $item_price = $value->base_price;
        $variant_sku = $value->sku;
        $quantity = $value->quantity;
        if ($variant_sku != "") {
            $product = $wpdb->get_results("select up.id,up.`product_id`,up.`brand_id`,up.`front`,up.`back` from wp_products_meta as pm left join wp_users_products as up on pm.product_id = up.id where pm.meta_key = 'bigcommerce_id' and pm.meta_value = $big_id and pm.shopid= $shop_id and up.users_id =" . $user_id, ARRAY_A);
            if (!$product) {
                $product = $wpdb->get_results("select up.id,up.`product_id`,up.`brand_id`,up.`front`,up.`back` from wp_products_meta as pm left join wp_users_products as up on pm.product_id = up.id where pm.meta_key = 'bigcommerce_id' and pm.meta_value = $big_id and up.users_id =" . $user_id, ARRAY_A);
            }
            if ($product) {
                $product = end($product);
                $pa_product_id = $product['id'];
                $inventory_id = $product['product_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                if ($pa_product_id > 0) {
                    $skus = callBigcommerceApi("ListSkus", array($big_id, urlencode($variant_sku)), 2);
                    if (!empty($skus)) {
                        $variant_id = $skus[0]->id;
                        $variant_sku = $skus[0]->sku;
                    } else {
                        $options = $value->product_options;
                        foreach ($options as $opt) {
                            if (strtolower($opt->display_name) == 'colors')
                                $color_name = $opt->display_value;
                            if (strtolower($opt->display_name) == 'sizes')
                                $size_name = $opt->display_value;
                        }
                        $variant_id = 0;
                    }
                    $variant_sku = mysql_escape_string($variant_sku);
                    if ($variant_id == 0) {
                        $color_id = $wpdb->get_var("select `color_id` from `wp_users_products_colors` where sku='$variant_sku' and `users_products_id`=" . $pa_product_id);
                        $size_id = $wpdb->get_var("select `size_id` from `wp_users_products_colors` where sku='$variant_sku' and `users_products_id`=" . $pa_product_id);
                    } else {
                        $color_variant_id = get_variant_meta_shop_byfield('variant_id', 'bigcommerce_id', $variant_id, $shop_id);
                        $color_id = $wpdb->get_var("select `color_id` from `wp_users_products_colors` where (`id` = $color_variant_id and sku='$variant_sku') and `users_products_id`=" . $pa_product_id);
                        $size_id = $wpdb->get_var("select `size_id` from `wp_users_products_colors` where (`id` = $color_variant_id and sku='$variant_sku') and `users_products_id`=" . $pa_product_id);
                        if (!$color_id || !$size_id) {
                            $color_id = $wpdb->get_var("select `color_id` from `wp_users_products_colors` where (`id` = $color_variant_id or sku='$variant_sku') and `users_products_id`=" . $pa_product_id);
                            $size_id = $wpdb->get_var("select `size_id` from `wp_users_products_colors` where (`id` = $color_variant_id or sku='$variant_sku') and `users_products_id`=" . $pa_product_id);
                        }
                    }
                    if ($color_id > 0 && $size_id > 0)
                        $items[] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
                }
            } else {
                $skus = callBigcommerceApi("ListSkus", array($big_id, urlencode($variant_sku)), 2);
                if (!empty($skus)) {
                    $variant_id = $skus[0]->id;
                    $variant_sku = $skus[0]->sku;
                } else {
                    $options = $value->product_options;
                    foreach ($options as $opt) {
                        if (strtolower($opt->display_name) == 'colors')
                            $color_name = $opt->display_value;
                        if (strtolower($opt->display_name) == 'sizes')
                            $size_name = $opt->display_value;
                    }
                    $variant_id = 0;
                    $variant_sku = $variant_sku . '-' . $color_name . '-' . $size_name;
                }
                if ($variant_id == 0) {
                    $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where sku='$variant_sku'");
                } else {
                    $color_variant_id = get_variant_meta_shop_byfield('variant_id', 'bigcommerce_id', $variant_id, $shop_id);
                    $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where (id=$color_variant_id and sku='$variant_sku')");

                    if (!$pa_variants)
                        $pa_variants = $wpdb->get_results("select users_products_id,color_id,size_id from wp_users_products_colors where (id=$color_variant_id or sku='$variant_sku')");
                }
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

    if (count($items) == 1) {
        if ($inventory_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $inventory_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12)
                $items [0]['only_shirts'] = true;
            else
                $items [0]['only_shirts'] = false;
        }
    }
    return $items;
}

function get_orders_bigcommerce($auth, $filter = array())
{
    bigcommerce_connect($auth);
    return callBigcommerceApi("getOrders", $filter);
}

function regenerate_big_order($order_id, $shop_id, $type = 0)
{
    global $wpdb;
    $return = array();
    $auth = getBigcommerceShopbyId($shop_id);
    if (!empty($auth)) {
        @extract($auth);
        bigcommerce_connect($auth);
        $order = array(
            'data' => array('id' => $order_id),
            'producer' => "shop/$bigcommerce_store_hash"
        );
        if ($type == 1)
            $order['send_type'] = 1;
        $order = (object)$order;
        $data_to_send = json_encode($order);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        $headers = array(
            "Cache-Control: no-cache",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://ryankikta.com/bigcommerce-orders/');
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
        if ($return == null)
            $return = array();
    }
    return $return;
}

function get_big_product($shop_id, $prod_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $product = callBigcommerceApi("getProduct", $prod_id);
    return $product;
}

function get_product_skus_big($shop_id, $prod_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $product_skus = callBigcommerceApi("getProductskus", $prod_id);
    return $product_skus;
}

function getOption_big($shop_id, $id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $option = callBigcommerceApi("getOption_byid", $id);
    return $option;
}

function get_order_bigcommerce_alldata($paorderid)
{
    global $wpdb;
    $order = $wpdb->get_row("select `orderid`,`shop_id` from `wp_rmproductmanagement_orders` where `order_id` = " . $paorderid, ARRAY_A);
    $auth = getBigcommerceShopbyId($order['shop_id']);
    bigcommerce_connect($auth);
    $order_Bigid = explode('#', $order["orderid"]);
    $order_Bigid = $order_Bigid[1];
    $order_data = callBigcommerceApi("getOrder", $order_Bigid);
    return $order_data;
}

/*
  order status id:
  Incomplete: 0;
  Pending :1;
  Shipped:2;
  Partially Shipped:3;
  Refunded:4;
  Cancelled:5;
  Declined;6;
  Awaiting Payment:7;
  Awaiting Pickup:8;
  Awaiting Shipment:9;
  Completed:10;
  Awaiting Fulfillment:11;
  Manual Verification Required:12;
  Disputed:13;
 */

function update_status_Order_Bigcommerce($order_id, $status_id = 2)
{
    global $wpdb;
    $order = $wpdb->get_row("select `orderid`,`shop_id` from `wp_rmproductmanagement_orders` where `order_id` = " . $order_id, ARRAY_A);
    $auth = getBigcommerceShopbyId($order['shop_id']);
    bigcommerce_connect($auth);
    $order_Bigid = explode('#', $order["orderid"]);
    $order_Bigid = intval($order_Bigid[1]);
    $order = array('status_id' => $status_id);
    $status_order = callBigcommerceApi("updateOrder", array($order_Bigid, $order), 2);
    return $status_order;
}

function get_big_order_status($shop_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $status = callBigcommerceApi("getOrderStatuses");
    return $status;
}

function update_status_Order_Bigcommerce2($shop_id, $order_Bigid, $status_id = 11)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $order = array('status_id' => $status_id);
    $status_order = callBigcommerceApi("updateOrder", array($order_Bigid, $order), 2);
    return $status_order;
}

function Create_Tracking_Order_Bigcommerce($order_id, $items_ids, $tracking)
{
    global $wpdb;
    $print_orderid = $wpdb->get_row("select `orderid`,`shop_id` from `wp_rmproductmanagement_orders` where `order_id` = " . $order_id, ARRAY_A);
    $shop_id = $print_orderid['shop_id'];
    $order_Bigid = explode('#', $print_orderid["orderid"]);
    $items = array();
    $count_item_to_ship = 0;
    foreach ($items_ids as $id) {
        $item = $wpdb->get_row("select `bigcommerce_id`,`quantity` from `wp_rmproductmanagement_order_details` where `oid` = " . $id, ARRAY_A);
        $items[] = array('order_product_id' => $item["bigcommerce_id"], 'quantity' => $item["quantity"]);
        $count_item_to_ship += (int)$item["quantity"];
    }
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $order = callBigcommerceApi("getOrder", $order_Bigid[1]);
    $address_id = $order->shipping_addresses;
    $shipment = array(
        'order_address_id' => $address_id[0]->id,
        'items' => $items,
        'tracking_number' => $tracking
    );
    $tracking_data = callBigcommerceApi("createOrderShipments", array($order_Bigid[1], $shipment), 2);
    if (isset($tracking_data->id) && $tracking_data->id != '') {
        $return = array(
            'status' => "success",
            'count_item_to_ship' => $count_item_to_ship,
            'response' => $tracking_data
        );
    } else {
        $errors = Bigcommerce::getLastError();
        $errors = processBigcommerceErrors($errors);
        $return = array(
            'status' => "failed",
            'errors' => $errors
        );
    }
    return $return;
}

function get_all_hook_bigcommerce($shop_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $result = callBigcommerceApi("listHook");
    return $result;
}

function get_hook_bigcommerce($shop_id, $hook_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $hook = callBigcommerceApi("getHook", $hook_id);
    return $hook;
}

function update_hook($shop_id, $hook_id, $data = array("is_active" => true))
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $result = callBigcommerceApi("updateHook", array($hook_id, $data), 2);
    return $result;
}

function delete_big_hook($shop_id, $hook_id)
{
    $auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($auth);
    $result = callBigcommerceApi("deleteHook", $hook_id);
    return $result;
}

function callBigcommerceApi($method, $args, $lenght = 1)
{
    if ($lenght == 2) {
        $response = Bigcommerce::$method($args[0], $args[1]);
    } else
        $response = Bigcommerce::$method($args);
    if (!$response) {
        $errors = Bigcommerce::getLastError();
        if (is_array($errors) && $errors[0]->status == 429) {
            sleep(Bigcommerce::connection()->getHeader("X-Retry-After"));
            if ($lenght == 2)
                callBigcommerceApi($method, $args, 2);
            else
                callBigcommerceApi($method, $args);
        } else
            $response = processBigcommerceErrors($errors);
    }
    return $response;
}

function get_all_bigcommerce_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $all_products = array();
    $auth = ($shop_id != 0) ? getBigcommerceShopbyId($shop_id) : getBigcommerceShop($user_id);
    $where = ($shop_id != 0) ? " and pm.shopid=$shop_id" : "";
    @extract($auth);
    bigcommerce_connect($auth);
    $pages = (int)(Bigcommerce::getProductsCount() / 250) + 1;
    for ($i = 1; $i <= $pages; $i++) {
        foreach (Bigcommerce::getProductsPage($i) as $product) {
            $pa_product_id = get_product_meta_shop_byfield("product_id", "bigcommerce_id", $product->id, $shop_id);
            $all_products[] = array(
                "id" => $product->id,
                "title" => $product->name,
                "status" => $product->availability,
                "url" => $bigcommerce_shop . $product->custom_url,
                "image" => $product->primary_image->standard_url,
                "imported" => ($pa_product_id == NULL) ? 0 : 1,
                "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
            );
        }
    }
    return $all_products;
}

function get_all_bigcommerce_shops($user_id)
{
    global $wpdb;
    return $wpdb->get_results("select `id`,`shop`,`active` from `wp_users_bigcommerce` where `users_id` = $user_id");
}

function get_bigcommerce_product_import($user_id, $bigcommerce_id, $shop_id = 0)
{
    $data = array();
    $auth = ($shop_id != 0) ? getBigcommerceShopbyId($shop_id) : getBigcommerceShop($user_id);
    bigcommerce_connect($auth);
    $product = callBigcommerceApi("getProduct", $bigcommerce_id);
    if ($product) {
        $images = array();
        $product_images = callBigcommerceApi("getProductImages", $bigcommerce_id);
        foreach ($product_images as $img) {
            $images[] = $img->thumbnail_url;
        }
        $data = array(
            "title" => $product->name,
            "weight" => $product->weight,
            "description" => $product->description,
            "sku" => $product->sku,
            "bigcommerceinfo" => $product->warranty,
            "bigcommercecategory" => $product->categories[0],
            "bigcommercebrand" => $product->brand_id,
            "bigcommercefeaturedproduct" => ($product->is_featured) ? 1 : 0,
            "shop_images" => $images,
        );
        return $data;
    }
}

function check_product_existe_bigcommerce($bigcommerce_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($bigcommerce_id, "bigcommerce_id", $shop_id);
    if ($pa_product_id)
        return array("status" => 200, "data" => true);
    if (!$pa_product_id)
        $pa_product_id = get_product_meta_byfield("product_id", "bigcommerce_id", $bigcommerce_id);
    if ($pa_product_id) {
        $userid_product = $wpdb->get_var("select users_id from wp_users_products where id=$pa_product_id");
        if ($userid_product == $user_id)
            $existe = true;
    }
    return array("status" => 200, "data" => $existe);
}