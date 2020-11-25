<?php
require_once(ABSPATH . "/HTTP/Request2.php");
require_once(dirname(__FILE__) . "/duplicate-functions.php");
require_once(dirname(__FILE__) . '/shopify-functions.php');
require_once(dirname(__FILE__) . '/storenvy-functions.php');
require_once(dirname(__FILE__) . '/etsy-functions.php');
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$wc_version = $wpdb->get_var("select wc_version from wp_users_woocommerce where users_id=$currentuserid");
if ($wc_version >= 2.6)
    require_once(dirname(__FILE__) . '/woocommerce-restfull-functions.php');
else
    require_once(dirname(__FILE__) . '/woocommerce-functions.php');
require_once(dirname(__FILE__) . '/bigcommerce-functions.php');
require_once(dirname(__FILE__) . '/opencart-functions.php');
require_once(dirname(__FILE__) . '/gumroad-functions.php');
require_once(dirname(__FILE__) . '/bigcartel-functions.php');

/*require_once(dirname(__FILE__).'/../product-functions/ebay-functions.php');
require_once(dirname(__FILE__).'/../product-functions/ecwid-functions.php');
require_once(dirname(__FILE__).'/../product-functions/tictail-functions.php');
require_once(dirname(__FILE__).'/../product-functions/americommerce-functions.php');
require_once(dirname(__FILE__).'/../product-functions/prestashop-functions.php');
require_once(dirname(__FILE__).'/../product-functions/lemonstand-functions.php');
require_once(dirname(__FILE__).'/../product-functions/amazon-functions.php');*/

function get_shop_active($data)
{

    return array(
        'shopifyactive' => ($data['shopifyactive']) ? esc_sql($data['shopifyactive']) : 0,
        'storenvyactive' => ($data['storenvyactive']) ? esc_sql($data['storenvyactive']) : 0,
        'etsyactive' => ($data['etsyactive']) ? esc_sql($data['etsyactive']) : 0,
        'woocommerceactive' => ($data['woocommerceactive']) ? esc_sql($data['woocommerceactive']) : 0,
        'bigcommerceactive' => ($data['bigcommerceactive']) ? esc_sql($data['bigcommerceactive']) : 0,
        'opencartactive' => ($data['opencartactive']) ? esc_sql($data['opencartactive']) : 0,
        'ecwidactive' => ($data['ecwidactive']) ? esc_sql($data['ecwidactive']) : 0,
        'prestaactive' => ($data['prestaactive']) ? esc_sql($data['prestaactive']) : 0,
        'americommerceactive' => ($data['americommerceactive']) ? esc_sql($data['americommerceactive']) : 0,
        'lemonstandactive' => ($data['lemonstandactive']) ? esc_sql($data['lemonstandactive']) : 0
    );
}

function get_shop_oldactive($prodid)
{
    $selectproductquery = $wpdb->get_result("select * from `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $shopifyactive = $row['active'];
    $storenvyactive = $row['storenvyactive'];
    $etsyactive = $row['etsyactive'];
    $woocommerceactive = $row['woocommerceactive'];
    $bigcommerceactive = $row['bigcommerceactive'];
    $opencartactive = $row['opencartactive'];
    $ecwidactive = $row['ecwidactive'];
    $prestaactive = $row['prestashopactive'];
    $americommerceactive = $row['americommerceactive'];
    $lemonstandactive = $row['lemonstandactive'];
    $ebayactive = $row['ebayactive'];
    $tictailactive = $row['tictailactive'];

    return array(
        'shopifyactiveold' => $shopifyactive,
        'storenvyactiveold' => $storenvyactive,
        'etsyactiveold' => $etsyactive,
        'woocommerceactiveold' => $woocommerceactive,
        'bigcommerceactiveold' => $bigcommerceactive,
        'opencartactiveold' => $opencartactive,
        'ecwidactiveold' => $ecwidactive,
        'prestaactiveold' => $prestaactive,
        'americommerceactiveold' => $americommerceactive,
        'lemonstandactiveold' => $lemonstandactive,
        'ebayactiveold' => $ebayactive,
        'tictailactiveold' => $tictailactive,
    );
}

function insert_product_ryankikta($data, $userid)
{
    @extract($data);
    $db_description = base64_encode($description);
    $sku = esc_sql($sku);
    $title = esc_sql($title);
    $weight = esc_sql($weight);
    $tags = esc_sql($tags);
    $products_query = $wpdb->get_result("INSERT INTO `wp_users_products` (`users_id`, `brand_id`, `product_id`, `sku`, `title`,`weight`,`tags`,`description`,`front`,`back`,`default_color`,`updated`) VALUES ($userid,'$brand_id','$product_id','$sku', '$title','$weight','$tags','$db_description','$dofront','$doback','$default_color','" . date("Y-m-d H:i:s", time()) . "');");
    $insert_id = mysql_insert_id();
    insert_product_meta($insert_id, 'inventory_id', $product_id);

    return $insert_id;
}

function insert_meta_product_ryankikta($userid, $prod_id, $new_prod_id, $inventory_id)
{

    $removetag = get_product_meta($prod_id, 'removetag');
    $applytag = get_product_meta($prod_id, 'applytag');
    $applytag_location = get_product_meta($prod_id, 'applytag_location');
    $applytag_note = get_product_meta($prod_id, 'applytag_note');
    $attach_hang_tag = get_product_meta($prod_id, 'attach_hang_tag');
    $hang_tag_location = get_product_meta($prod_id, 'hang_tag_location');
    $individual_bagging = get_product_meta($prod_id, 'individual_bagging');
    $underbase_setting = get_product_meta($prod_id, 'underbase_setting');

    $applytag_note = ($applytag_note == NULL) ? "" : $applytag_note;

    $data = array('removetag' => $removetag, 'applytag' => $applytag, 'applytag_location' => $applytag_location,
        'applytag_note' => $applytag_note, 'attach_hang_tag' => $attach_hang_tag, 'hang_tag_location' => $hang_tag_location,
        'individual_bagging' => $individual_bagging, 'underbase_setting' => $underbase_setting);

    config_brand_product($userid, $data, $inventory_id, $new_prod_id);
}

function update_product_ryankikta($data, $userid, $productid)
{
    global $wpdb;
    @extract($data);
    $db_description = base64_encode($description);
    $sku = esc_sql($sku);
    $title = esc_sql($title);
    $weight = esc_sql($weight);
    $tags = esc_sql($tags);
    $oldproduct = $wpdb->get_results("select product_id,front,back from wp_users_products where `id` = $productid", ARRAY_A);
    $sql = "UPDATE `wp_users_products` SET `sku` = '$sku' , `title` = '$title',`weight`='$weight',`tags`='$tags', `description` = '$db_description',`front` = $dofront , `back` = $doback ,`default_color` = $default_color,`brand_id`=$brand_id,`product_id`=$product_id,`updated`='" . date("Y-m-d H:i:s", time()) . "'  WHERE `id` = $productid";
    $wpdb->query($sql);
    update_product_meta($productid, 'inventory_id', $product_id);
    if ($product_id != $oldproduct[0]['product_id']) {
        $now = date("m/d/y h:i:s a");
        $note = $now . " Product id changed From " . $oldproduct[0]['product_id'] . " To " . $product_id;
        insert_product_meta($productid, 'note', $note);
    }
    if ($dofront != $oldproduct[0]['front']) {
        $now = date("m/d/y h:i:s a");
        $note = $now . " Front changed From " . $oldproduct[0]['front'] . " To " . $dofront;
        insert_product_meta($productid, 'note', $note);
    }
    if ($doback != $oldproduct[0]['back']) {
        $now = date("m/d/y h:i:s a");
        $note = $now . " Back changed From " . $oldproduct[0]['back'] . " To " . $doback;
        insert_product_meta($productid, 'note', $note);
    }
}

function config_brand_product($userid, $data, $inventory_id, $productid)
{
    @extract($data);
    @extract(access_product_additional_settings($inventory_id));
    $access_heat_press_tag = (int)get_user_meta($userid, 'access_heat_press_tag', true);
    $access_attach_hang_tag = (int)get_user_meta($userid, 'access_attach_hang_tag', true);

    if ($inv_access_neck_label_removal == 1) {
        $removetag = esc_sql($removetag);
        update_product_meta($productid, "removetag", $removetag);
    }

    if ($inv_access_heat_press_tag == 1 && $access_heat_press_tag == 1) {
        $applytag = esc_sql($applytag);
        $applytag_location = esc_sql($applytag_location);
        $applytag_note = esc_sql($applytag_note);
        update_product_meta($productid, "applytag", $applytag);
        update_product_meta($productid, "applytag_location", $applytag_location);
        update_product_meta($productid, "applytag_note", $applytag_note);
    }

    if ($inv_access_attach_hang_tag == 1 && $access_attach_hang_tag == 1) {
        $attach_hang_tag = esc_sql($attach_hang_tag);
        $hang_tag_location = esc_sql($hang_tag_location);
        update_product_meta($productid, "attach_hang_tag", $attach_hang_tag);
        update_product_meta($productid, "hang_tag_location", $hang_tag_location);
    }

    if ($inv_access_individual_bagging == 1) {
        $individual_bagging = esc_sql($individual_bagging);
        update_product_meta($productid, "individual_bagging", $individual_bagging);
    }
    if ($inv_access_always_underbase == 1) {
        $underbase_setting = esc_sql($underbase_setting);
        update_product_meta($productid, "underbase_setting", $underbase_setting);
    }

}

function GET_All_colors_sizes()
{
    $ret = array();
    $selectsizesquery = $wpdb->get_result("SELECT `size_id`,`size_name`,`plus_size_charge` FROM `wp_rmproductmanagement_sizes`");
    while ($sizes = mysql_fetch_array($selectsizesquery)) {

        $ret['allsizes'][$sizes[0]]['name'] = $sizes[1];
        $ret['allsizes'][$sizes[0]]['plus'] = $sizes[2];
    }
    $selectcolorsquery = $wpdb->get_result("SELECT `color_id`,`color_name`,`color_colour`,`color_group` FROM `wp_rmproductmanagement_colors`");
    while ($colors = mysql_fetch_array($selectcolorsquery)) {
        $ret['allcolors'][$colors[0]]['name'] = $colors[1];
        $ret['allcolors'][$colors[0]]['code'] = $colors[2];
        $ret['allcolors'][$colors[0]]['group'] = $colors[3];
    }
    return $ret;
}

function getproductGeneralData($data)
{

    $brand_id = esc_sql($data['brand_id']);
    $product_id = esc_sql($data['product_id']);
    $sku = trim(str_replace('"', '\"', stripslashes($data['sku'])));
    $title = trim(str_replace('"', '\"', stripslashes($data['title'])));
    $weight = trim(str_replace('"', '\"', stripslashes($data['weight'])));
    $tags = trim(str_replace('"', '\"', stripslashes($data['tags'])), ',');
    $tags = trim($tags, ', ');
    $dofront = isset($data['dofront']) ? esc_sql($data['dofront']) : 0;
    $doback = isset($data['doback']) ? esc_sql($data['doback']) : 0;
    $default_color = $data['defaults'];
    $description = $data['description'];
    $description = str_replace("\r\n", "", $description);
    $description = str_replace("\\", '', $description);
    $description = str_replace('"', '\"', $description);
    $description = trim($description);
    
   /*
    if ($data['mockupback'] != "" && $data['brand_id'] == 29)
        $doback = 1;
    */

    if ($weight == '')
        $weight = 0;
    if (intval($dofront) != 1)
        $dofront = 0;
    if (intval($doback) != 1)
        $doback = 0;
    if (empty($default_color))
        $default_color = 0;
    else
        $default_color = $default_color[0];
    // Brand Settings
    $removetag = esc_sql($data['removetag']);
    $applytag = esc_sql($data['applytag']);
    $applytag_location = esc_sql($data['applytag_location']);
    $applytag_note = esc_sql($data['applytag_note']);
    $attach_hang_tag = esc_sql($data['attach_hang_tag']);
    $hang_tag_location = esc_sql($data['hang_tag_location']);
    $individual_bagging = esc_sql($data['individual_bagging']);
    $underbase_setting = esc_sql($data['underbase_setting']);

    return array('brand_id' => $brand_id, 'product_id' => $product_id, 'sku' => $sku, 'title' => $title, 'weight' => $weight, 'tags' => $tags, 'description' => $description, 'dofront' => $dofront, 'doback' => $doback, 'default_color' => $default_color,
        'removetag' => $removetag, 'applytag' => $applytag, 'applytag_location' => $applytag_location,
        'applytag_note' => $applytag_note, 'attach_hang_tag' => $attach_hang_tag, 'hang_tag_location' => $hang_tag_location,
        'individual_bagging' => $individual_bagging, 'underbase_setting' => $underbase_setting);
}

function getGeneralCurrentData($productid)
{
    $general_data = $wpdb->get_result("select * from `wp_users_products` where `id` = $productid");
    $check_general_data = mysql_fetch_assoc($general_data);
    return array(
        'users_id' => $check_general_data['users_id'],
        'brand_id' => $check_general_data['brand_id'],
        'product_id' => $check_general_data['product_id'],
        'sku' => $check_general_data['sku'],
        'title' => $check_general_data['title'],
        'weight' => $check_general_data['weight'],
        'tags' => $check_general_data['tags'],
        'description' => base64_decode($check_general_data['description']),
        'dofront' => $check_general_data['front'],
        'doback' => $check_general_data['back'],
        'default_color' => $check_general_data['default_color'],
        'active' => $check_general_data['active'],
        'storenvyactive' => $check_general_data['storenvyactive'],
        'etsyactive' => $check_general_data['etsyactive'],
        'woocommerceactive' => $check_general_data['woocommerceactive'],
        'bigcommerceactive' => $check_general_data['bigcommerceactive'],
        'opencartactive' => $check_general_data['opencartactive'],
        'ecwidactive' => $check_general_data['ecwidactive'],
        'ebayactive' => $check_general_data['ebayactive'],
        'prestashopactive' => $check_general_data['prestashopactive'],
        'americommerceactive' => $check_general_data['americommerceactive'],
        'tictailactive' => $check_general_data['tictailactive'],
        'gumroadactive' => $check_general_data['gumroadactive'],
        'bigcartelactive' => $check_general_data['bigcartelactive'],
        'lemonstandactive' => $check_general_data['lemonstandactive']
    );
}

//need amelioration
function getCountDefaultColorProduct($inventory_id, $default_color)
{

    $count = 1;
    $selectsizesquery = $wpdb->get_result("SELECT `size_id` FROM `wp_rmproductmanagement_colors_to_products` WHERE `product_id` = $inventory_id AND `color_id` = $default_color");
    $selectsizesrow = $wpdb->get_row($selectsizesquery);
    $sizes = $selectsizesrow[0];
    foreach (explode(",", $sizes) as $key => $size_id) {
        $count++;
    }
    return $count;

}

function getUnusedColors($user_product_id, $product_id, $allcolors, $newcolors)
{
    global $wpdb;
    $toremove = array();
    $oldcolors = $wpdb->get_results("select id,color_id,size_id,shopify_id,storenvy_id from wp_users_products_colors where users_products_id=$user_product_id", ARRAY_A);
    foreach ($oldcolors as $oldcolor) {
        $color_id = $oldcolor['color_id'];
        $size_id = $oldcolor['size_id'];
        $assign_sizes = $wpdb->get_var("select `size_id` from `wp_rmproductmanagement_colors_to_products` where `product_id` = $product_id and `color_id` = $color_id");
        $assign_sizes = explode(',', $assign_sizes);
        if (!in_array($color_id, $newcolors) || (in_array($color_id, $newcolors) && !in_array($size_id, $assign_sizes))) {
            $toremove[] = array(
                'id' => $oldcolor['id'],
                'color_id' => $color_id,
                'color_name' => $allcolors[$color_id]['name'],
                'shopify_id' => $oldcolor['shopify_id'],
                'storenvy_id' => $oldcolor['storenvy_id']
            );
        }
    }
    return $toremove;
}

function createPAVariants($data, $allcolors, $allsizes, $product_id, $user_product_id, $count)
{

    global $wpdb;
    $color_arr = array();
    $size_arr = array();
    $pos_default = $count - 1;
    $variants = array();
    $toInsert = array();
    $toUpdate = array();
    $default_color = "";
    $all_color_ids = $wpdb->get_col("select distinct(color_id) from wp_users_products_colors where users_products_id=$user_product_id");
    //wp_mail('team@ryankikta.com','product variants',var_export(array($user_product_id ,$all_color_ids) ,true));
    $user_id = $wpdb->get_var("select users_id from wp_users_products where id=$user_product_id");
    $plus_size_disabled = get_user_meta($user_id, 'plus_size_disabled', true);
    $plus_size_disabled = ($plus_size_disabled == 1) ? 1 : 0;
    $custom_price_per_size = get_user_meta($user_id, 'custom_price_per_size', true);
    $custom_price_per_size = ($custom_price_per_size == 1) ? 1 : 0;
    //wp_mail('team@ryankikta.com','data colors',var_export($allcolors,true));
    foreach ($data['colors'] as $color_id) {
        $color_id = esc_sql($color_id);
        $normalpricevar = "normalprices_" . $color_id;
        $normalprice = esc_sql($data[$normalpricevar]);

        $image = "image_" . $color_id;
        $image_id = isset($data[$image]) ? $data[$image] : 0;
        $image_url = "image_url_" . $color_id;
        $image_url = isset($data[$image_url]) ? $data[$image_url] : "";

        $color_name = $allcolors[$color_id]['name'];
        $color_group = $allcolors[$color_id]['group'];
        $color_code = $allcolors[$color_id]['code'];
        if (!empty($data['defaults'])) {
            if (in_array($color_id, $data['defaults']))
                $default_color = $color_name;
            else
                $color_arr[] = $color_name;
        } else {
            $color_arr[] = $color_name;
        }
        // get sizes
        $selectsizesquery = $wpdb->get_result("SELECT `size_id` FROM `wp_rmproductmanagement_colors_to_products` WHERE `product_id` = $product_id AND `color_id` = $color_id");
        $selectsizesrow = $wpdb->get_row($selectsizesquery);
        $sizes = $selectsizesrow[0];

        $prd_sizes = array();
        $ordered_sizes = $wpdb->get_results("select size_id from " . WP_INVENTORY_SIZES_TABLE . " where size_id in (" . $sizes . ") order by s_ordering asc");
        foreach ($ordered_sizes as $size) {
            $prd_sizes[] = $size->size_id;
        }
        $sizes = (!empty($prd_sizes)) ? $prd_sizes : explode(",", $sizes);
        if (is_array($data['defaults'])) {
            if (in_array($color_id, $data['defaults'])) {
                $sizes = array_reverse($sizes);
            }
        }
        foreach ($sizes as $key => $size_id) {
            $plus_size_charge = get_default_plus_size_charge($size_id);
            if ($plus_size_charge != "0.00" && $plus_size_disabled == 1)
                unset($sizes[$key]);
        }
        $max_price_color = 0.00;
        $min_price_color = $normalprice;
        foreach ($sizes as $key => $size_id) {

            $size_name = $allsizes[$size_id]['name'];
            $size_plus = $allsizes[$size_id]['plus'];
            if (!in_array($size_name, $size_arr))
                $size_arr[] = $size_name;

            $pluspricevar = ($custom_price_per_size == 1) ? "plusprices_" . $color_id . "_" . $size_id : "plusprices_" . $color_id;

            if (!isset($data[$pluspricevar]))
                $pluspricevar = $normalpricevar;
            $plusprice = esc_sql($data[$pluspricevar]);

            $max_price_color = max($normalprice, $plusprice, $max_price_color);
            $min_price_color = min($normalprice, $plusprice, $min_price_color);

            $price = ($size_plus == "0.00") ? $normalprice : $plusprice;

            if (is_array($data['defaults']) && in_array($color_id, $data['defaults'])) {
                $position = $pos_default;
                $pos_default--;
            } else {
                $position = $count;
                $count++;
            }
            $variant = array("sku" => str_replace('"', '\"', stripslashes($data['sku'])) . "-" . $color_name . "-" . $size_name,
                "sku_color" => str_replace('"', '\"', stripslashes($data['sku'])) . "-" . $color_name,
                "title" => str_replace('"', '\"', stripslashes($data['title'])) . "-" . $color_name . "-" . $size_name,
                "price" => $price,
                "plusprice" => $plusprice,
                "normalprice" => $normalprice,
                "image_id" => $image_id,
                "image_url" => $image_url,
                "position" => $position,
                "size_name" => $size_name,
                "color_name" => $color_name,
                "size_id" => $size_id,
                "color_id" => $color_id,
                "color_code" => "#" . strtoupper($color_code)
            );//wp_mail('team@ryankikta.com','data colors conditions',var_export(array(((!in_array($color_id, $all_color_ids) && is_array($all_color_ids) && !empty($all_color_ids) ) || (in_array($color_id, $all_color_ids) && !in_array($size_id,$all_sizes_color_ids))),$color_id,$all_color_id,$size_id,$all_sizes_color_ids),true));
            $all_sizes_color_ids = $wpdb->get_col("select size_id from wp_users_products_colors where users_products_id=$user_product_id and color_id=$color_id");
            if (!in_array($color_id, $all_color_ids) || (in_array($color_id, $all_color_ids) && !in_array($size_id, $all_sizes_color_ids))) {
                if (is_array($data['defaults']) && in_array($color_id, $data['defaults']))
                    array_unshift($toInsert, $variant);
                else
                    $toInsert[] = $variant;
            } else {
                if (is_array($data['defaults']) && in_array($color_id, $data['defaults']))
                    array_unshift($toUpdate, $variant);
                else
                    $toUpdate[] = $variant;
            }

            $variant['woocommerce_id'] = $wpdb->get_var("select woocommerce_id from wp_users_products_colors where color_id=$color_id and size_id=$size_id and users_products_id= $user_product_id");
            $variant['shopify_id'] = $wpdb->get_var("select shopify_id from wp_users_products_colors where color_id=$color_id and size_id=$size_id and users_products_id= $user_product_id");

            //   if (!empty($data['defaults'])){
            if (is_array($data['defaults']) && in_array($color_id, $data['defaults'])) {
                array_unshift($variants, $variant);

            } else {
                $variants[] = $variant;
            }
            // }
            $variantsid[$position]['color_id'] = $color_id;
            $variantsid[$position]['size_id'] = $size_id;
        }

        if (!isset($max_prices_color[$color_name]) || (isset($max_prices_color[$color_name]) && $max_prices_color[$color_name] < $max_price_color))
            $max_prices_color[$color_name] = $max_price_color;
        if (!isset($min_prices_color[$color_name]) || (isset($min_prices_color[$color_name]) && $min_prices_color[$color_name] > $min_price_color))
            $min_prices_color[$color_name] = $min_price_color;
    }
    if ($default_color != "")
        array_unshift($color_arr, $default_color);

    $min_price = min($min_prices_color);
    $max_price = max($max_prices_color);
    $min_max_price = min($max_prices_color);
    //wp_mail('team@ryankikta.com','product variants after',var_export(array($variants,$toInsert,$toUpdate) ,true));
    return array($variants, $toInsert, $toUpdate, $min_price, $max_price, $min_max_price, $max_prices_color, $variantsid, $size_arr, $color_arr);

}

function insertVariants($user_product_id, $toInsert)
{

    if (!empty($toInsert)) {
        foreach ($toInsert as $insert) {
            $color_id = $insert['color_id'];
            $size_id = $insert['size_id'];
            $normalprice = $insert['normalprice'];
            $plusprice = $insert['plusprice'];
            $image_id = ($insert['image_id']) ? $insert['image_id'] : 0;
            $sku = esc_sql($insert['sku']);
            $sql = "INSERT INTO `wp_users_products_colors` (`id`,`users_products_id`,`color_id`,`size_id`,`normalprice`,`plusprice`,`sku`,`image_id`) VALUES (NULL,$user_product_id,$color_id,$size_id,'$normalprice','$plusprice','$sku',$image_id);";
            @$wpdb->get_result($sql);
            if (mysql_error()) {
                wp_mail('team@ryankikta.com', 'error sql', var_export(array($sql, mysql_error()), true));
            }
        }
    }

}

function updateVariants($user_product_id, $toUpdate)
{
    if (!empty($toUpdate)) {
        foreach ($toUpdate as $update) {
            $color_id = $update['color_id'];
            $size_id = $update['size_id'];
            $normalprice = $update['normalprice'];
            $plusprice = $update['plusprice'];
            $sku = esc_sql($update['sku']);
            $image_id = ($update['image_id']) ? $update['image_id'] : 0;
            $sql = "UPDATE `wp_users_products_colors` SET  `normalprice` = '$normalprice' , `plusprice` = '$plusprice' ,`sku` = '$sku',`image_id`= '$image_id' WHERE `users_products_id` = $user_product_id AND `color_id` = $color_id AND `size_id` = $size_id";
            @$wpdb->get_result($sql);
        }
    }

}

/**
 * remove unused variants
 * @param type $toremove
 */
function removeVariants($toremove)
{
    $variants_id = implode(",", array_filter_key($toremove, 'id'));
    $wpdb->get_result("delete from `wp_users_products_colors` where `id` in($variants_id)");
    $wpdb->get_result("delete from `wp_variants_meta` where `variant_id` in($variants_id)");
}

function prepareImages($images, $defaultimage = 0, $upload_path = 1)
{
    global $wpdb;
    $all_images = array();
    $current_user = wp_get_current_user();
    $currentusername = $current_user->user_login;
    $currentuserid = $current_user->ID;
    $blogurl = get_bloginfo('url');
    $pos = 0;
    foreach ($images as $key => $image_id) {
        if ($image_id != "") {
            $image_id = esc_sql($image_id);
            $store_id = ($key > 15264) ? $key : 0;
            $user_images = $wpdb->get_results("select `cdn`,`cdn_orig`,`fileName`,`fileType` from `wp_userfiles` where `fileID` = $image_id", ARRAY_A);
            $user_images = $user_images[0];
            $imagefilename = $user_images['fileName'];
            $image_ext = $user_images['fileType'];
            $image_ext = ($image_ext[0] == '.') ? substr($image_ext, 1) : $image_ext;
            $defaultdone = 0;
            if (($defaultdone == 0 && $image_id == $defaultimage) || ($defaultimage == 0 && $key == 0)) {
                $defaultdone = 1;
                $position = 0;
            } else {
                $pos++;
                $position = $pos;
            }

            $imageurl = $blogurl . "/wp-content/uploads/user_uploads/" . rawurlencode($currentusername) . "/" . rawurlencode($imagefilename);
            $imageurl_cloud = get_cloud_orig_file_url($currentuserid, $imagefilename, 0);
            $imageurl = ($imageurl_cloud != "") ? $imageurl_cloud : $imageurl;
            $imageadd = array('id' => $image_id, 'position' => $position, 'src' => $imageurl);
            if ($upload_path == 1) {
                $path_file = dirname(realpath(__FILE__)) . "/../../../uploads/user_uploads/" . $currentusername . "/" . $imagefilename;
                if ($user_images['cdn'] == 1)
                    $path_file = upload_file_from_url($currentuserid, $currentusername, $imagefilename, $image_ext);
                $imageadd['path'] = $path_file;
            }
            $imageadd['cdn'] = 0;
            if ($user_images['cdn'] == 1) {
                $imageadd['cdn'] = 1;
            }
            $imageadd['store_id'] = $store_id;
            if ($defaultdone == 1)
                array_unshift($all_images, $imageadd);
            else
                $all_images[] = $imageadd;
        }
    }
    return $all_images;
}

function removeImagesVariants($productid, $toremove)
{
    foreach ($toremove as $val) {
        $color_id = $val['color_id'];
        delete_image_color($productid, $color_id);
    }
}

function destroy_images($images)
{
    foreach ($images as $image) {
        if ($image['cdn'] == 1 && !empty($image['path']) && file_exists($image['path']))
            unlink($image['path']);
    }
}

function upload_file_from_url($user_id, $username, $filename, $ext_file = "png")
{
    $fileurl = get_cloud_orig_file_url($user_id, $filename);
    $targetPath = dirname(realpath(__FILE__)) . "/../../../uploads/user_uploads/" . $username;
    if (!file_exists($targetPath))
        mkdir($targetPath);
    if (in_array($ext_file, array('psd', 'tif', 'pdf'))) {
        $filename_exp = explode(".", $filename);
        $filename = $filename_exp[0] . '-full.png';
    }
    $path_file = $targetPath . "/" . $filename;
    if (is_file($path_file)) {
        $explode_file = explode('.', $filename);
        $filetitle = $explode_file[0];
        $ext = $explode_file[1];
        $file_exist = true;
        $count = 1;
        while ($file_exist == true) {
            $path_file = rtrim($targetPath, '/') . '/' . $filetitle . "_$count." . $ext;
            if (!is_file($path_file)) {
                $file_exist = false;
            }
            $count++;
        }
    }
    copy($fileurl, $path_file);
    return $path_file;
}

function uploadRyanKiktaStoreImages($images, $products_id)
{
    global $wpdb;
    $all_old_images = array();
    $all_img = array();
    $updated_images = array();
    $old_images = $wpdb->get_results("select id,image_id from wp_users_products_images where users_products_id = $products_id and type=4 ", ARRAY_A);
    foreach ($old_images as $old_image) {
        $all_old_images[$old_image['image_id']] = $old_image['id'];

    }
    foreach ($images as $key => $image) {
        $image_id = $image['id'];
        $default_image = 0;
        $updated_images[] = $image_id;
        $store = 0;
        if ($key == 0)
            $default_image = 1;
        $old_id = (isset($all_old_images[$image_id])) ? $all_old_images[$image_id] : 'NULL';
        $sql = "INSERT INTO `wp_users_products_images` (`id`,`users_products_id`,`image_id`,`type`,`storeimage`,`defaultimage`) VALUES ($old_id,$products_id,$image_id,'4','$store',$default_image)"
            . " on duplicate key update users_products_id = values(users_products_id),image_id = values(image_id),type=values(type),storeimage = values(storeimage),defaultimage=values(defaultimage);";
        $query = $wpdb->get_result($sql);


        if (!$query) {
            $logs = array();
            $logs['sql'] = mysql_escape_string($sql);

            wp_insert_post(array(
                'post_content' => var_export($logs, true),
                'post_title' => esc_sql("adding product image "),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'
            ));
            wp_mail('team@ryankikta.com', 'adding product image issue', '');
        }
    }

    //delete images
    $images_to_del = array_diff(array_keys($all_old_images), $updated_images);
    foreach ($images_to_del as $img_id) {
        $sql = "delete from wp_users_products_images where image_id =$img_id and users_products_id =$products_id and type=4";
        $wpdb->query($sql);
        //wp_mail('team@ryankikta.com','image to del',$sql);

    }
}

function get_front_back_printfile($productid)
{
    global $wpdb;
    $frontprint = $wpdb->get_col("select image_id from wp_users_products_images where users_products_id=$productid and type=1");
    $frontmockup = $wpdb->get_col("select image_id from wp_users_products_images where users_products_id=$productid and type=2");
    $backprint = $wpdb->get_col("select image_id from wp_users_products_images where users_products_id=$productid and type=5");
    $backmockup = $wpdb->get_col("select image_id from wp_users_products_images where users_products_id=$productid and type=3");

    $frontprint = (count($frontprint) == 0) ? array() : $frontprint;
    $frontmockup = (count($frontmockup) == 0) ? array() : $frontmockup;
    $backprint = (count($backprint) == 0) ? array() : $backprint;
    $backmockup = (count($backmockup) == 0) ? array() : $backmockup;
    return array($frontprint, $frontmockup, $backprint, $backmockup);
}

function updatePrintFiles($POST, $productid, $dofront, $doback, $fb_print = array())
{

    global $wpdb;
    $to_elemenate_value = array(0, '0', NULL, FALSE, "");
    if ($dofront !== 0) {
        // front print multiple
        if (isset($POST["frontprintfile"]) && (count($POST["frontprintfile"]) > 0)) {
            $frontprintfiles = array_values(array_diff($POST["frontprintfile"], $to_elemenate_value));
            if (isset($frontprintfiles) && (count($frontprintfiles) > 0)) {
                foreach ($frontprintfiles as $frontprintfile) {
                    $frontprintfile = intval(esc_sql($frontprintfile));
                    $images_query = $wpdb->get_result("INSERT INTO `wp_users_products_images` (`id`, `users_products_id`, `image_id`, `type`,`storeimage`,`defaultimage`) VALUES (NULL, $productid, $frontprintfile, '1',0,0);");
                }
                if ($POST['pagetype'] == 2) {
                    $now = date("m/d/y h:i:s a");
                    if (count($fb_print[0]) > 0) {
                        $delete_frontprintfile = array_diff($fb_print[0], $frontprintfiles);
                        $insert_frontprintfile = array_diff($frontprintfiles, $fb_print[0]);
                        if ((count($delete_frontprintfile) > 0) || (count($insert_frontprintfile) > 0)) {
                            $note = $now . " image_id front print changed From " . implode(",", $fb_print[0]) . " To " . implode(",", $frontprintfiles);
                            insert_product_meta($productid, 'note', $note);
                        }
                    } else {
                        $note = $now . " front print image affected where image_id = " . implode(",", $frontprintfiles);
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            } else {
                if ($POST['pagetype'] == 2) {
                    if (isset($fb_print[0]) && (count($fb_print[0]) > 0)) {
                        $now = date("m/d/y h:i:s a");
                        $note = $now . " front print image removed";
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            }
        } else {
            if ($POST['pagetype'] == 2) {
                if (isset($fb_print[0]) && (count($fb_print[0]) > 0)) {
                    $now = date("m/d/y h:i:s a");
                    $note = $now . " front print image removed";
                    insert_product_meta($productid, 'note', $note);
                }
            }
        }
        // front mockup multiple
        if (isset($POST['mockupfront']) && (count($POST['mockupfront']) > 0)) {
            $mockupfronts = array_values(array_diff($POST["mockupfront"], $to_elemenate_value));
            if (count($mockupfronts) > 0) {
                foreach ($mockupfronts as $mockupfront) {
                    $mockupfront = esc_sql($mockupfront);
                    $images_query = $wpdb->get_result("INSERT INTO `wp_users_products_images` (`id`, `users_products_id`, `image_id`, `type`,`storeimage`,`defaultimage`) VALUES (NULL, $productid, $mockupfront, '2',0,0);");
                }
                if ($POST['pagetype'] == 2) {
                    $now = date("m/d/y h:i:s a");
                    if (count($fb_print[1]) > 0) {
                        $delete_mockupfrontprintfile = array_diff($fb_print[1], $mockupfronts);
                        $insert_mockupfrontprintfile = array_diff($mockupfronts, $fb_print[1]);
                        if ((count($delete_mockupfrontprintfile) > 0) || (count($insert_mockupfrontprintfile) > 0)) {
                            $note = $now . " image_id front mockup changed From " . implode(",", $fb_print[1]) . " To " . implode(",", $mockupfronts);
                            insert_product_meta($productid, 'note', $note);
                        }
                    } else {
                        $note = $now . " front mockup image affected where image_id = " . implode(",", $mockupfronts);
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            } else {
                if ($POST['pagetype'] == 2) {
                    if ((isset($fb_print[1])) && (count($fb_print[1]) > 0)) {
                        $now = date("m/d/y h:i:s a");
                        $note = $now . " front mockup image removed";
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            }
        } else {
            if ($POST['pagetype'] == 2) {
                if ((isset($fb_print[1])) && (count($fb_print[1]) > 0)) {
                    $now = date("m/d/y h:i:s a");
                    $note = $now . " front mockup image removed";
                    insert_product_meta($productid, 'note', $note);
                }
            }
        }
    }

    if ($doback != 0) {
        // back print multiple
        if (isset($POST['backprintfile']) && (count($POST['backprintfile']) > 0)) {
            $backprintfiles = array_values(array_diff($POST["backprintfile"], $to_elemenate_value));
            if (count($backprintfiles) > 0) {
                foreach ($backprintfiles as $backprintfile) {
                    $backprintfile = esc_sql($backprintfile);
                    $images_query = $wpdb->get_result("INSERT INTO `wp_users_products_images` (`id`, `users_products_id`, `image_id`, `type`,`storeimage`,`defaultimage`) VALUES (NULL, $productid, $backprintfile, '5',0,0);");
                }
                if ($POST['pagetype'] == 2) {
                    if (count($fb_print[2]) > 0) {
                        $delete_backprintfile = array_diff($fb_print[2], $backprintfiles);
                        $insert_backprintfile = array_diff($backprintfiles, $fb_print[2]);
                        if ((count($delete_backprintfile) > 0) || (count($insert_backprintfile) > 0)) {
                            $note = $now . " image_id back print changed From " . implode(",", $fb_print[2]) . " To " . implode(",", $backprintfiles);
                            insert_product_meta($productid, 'note', $note);
                        }
                    } else {
                        $note = $now . " back print image affected where image_id = " . implode(",", $backprintfiles);
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            } else {
                if (isset($fb_print[2]) && (count($fb_print[2]) > 0)) {
                    $now = date("m/d/y h:i:s a");
                    $note = $now . " back print image removed";
                    insert_product_meta($productid, 'note', $note);
                }
            }
        } else {
            if (isset($fb_print[2]) && (count($fb_print[2]) > 0)) {
                $now = date("m/d/y h:i:s a");
                $note = $now . " back print image removed";
                insert_product_meta($productid, 'note', $note);
            }
        }
        // back mockup multiple
        if (isset($POST['mockupback']) && (count($POST['mockupback'])) > 0) {
            $mockupbacks = array_values(array_diff($POST["mockupback"], $to_elemenate_value));
            if (count($mockupbacks) > 0) {
                foreach ($mockupbacks as $mockupback) {
                    $mockupback = esc_sql($mockupback);
                    $images_query = $wpdb->get_result("INSERT INTO `wp_users_products_images` (`id`, `users_products_id`, `image_id`, `type`,`storeimage`,`defaultimage`) VALUES (NULL, $productid, $mockupback, '3',0,0);");
                }
                if ($POST['pagetype'] == 2) {
                    if (count($fb_print[3]) > 0) {
                        $delete_mockupbacks = array_diff($fb_print[3], $mockupbacks);
                        $insert_mockupbacks = array_diff($mockupbacks, $fb_print[3]);
                        $now = date("m/d/y h:i:s a");
                        if (count($delete_mockupbacks) > 0 || count($insert_mockupbacks) > 0) {
                            $note = $now . " image_id back mockup changed From " . implode(",", $fb_print[3]) . " To " . implode(",", $mockupbacks);
                            insert_product_meta($productid, 'note', $note);
                        }
                    } else {
                        $note = $now . " back mockup image affected where image_id = " . $mockupback;
                        insert_product_meta($productid, 'note', $note);
                    }
                }
            } else {
                if (count($fb_print[3]) > 0) {
                    $now = date("m/d/y h:i:s a");
                    $note = $now . " back mockup image removed";
                    insert_product_meta($productid, 'note', $note);
                }
            }
        } else {
            if (count($fb_print[3]) > 0) {
                $now = date("m/d/y h:i:s a");
                $note = $now . " back mockup image removed";
                insert_product_meta($productid, 'note', $note);
            }
        }
    }
}

function get_headers_from_curl_response($response)
{
    $headers = array();

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else {
            list ($key, $value) = explode(': ', $line);

            $headers[$key] = $value;
        }

    return $headers;
}

function removeProductImages($productid)
{
    // $ids = $wpdb->get_col("select id from wp_users_products_images where users_products_id = $productid and type <> 4");
    $wpdb->get_result("DELETE FROM `wp_users_products_images` WHERE type <> 4 and `users_products_id` = $productid");

    //$wpdb->get_result("delete from `wp_images_meta` where `product_id` = $prodid");
    //delete_images_product_meta($productid);
}

function get_approval_product_brand()
{
    global $wpdb;
    $return = array();
    $approval_brands = $wpdb->get_results("select distinct brand_id from wp_users_brand_permissions order by brand_id asc", ARRAY_A);
    foreach ($approval_brands as $key => $brand) {
        $return[] = $brand['brand_id'];
    }
    return $return;
}

/***************** New Codes for add an item (new product page) @aranyak START ******************************* */
function map_product_color_image($product_id, $colorImageArray, $user_id)
{
    // type values -> 1->frontprintfile, 2->mockupfront, 5->backprintfile, 3->mockupback

    foreach ($colorImageArray as $color_id => $val) {

        $typeVal = "";

        switch ($val['type']) {

            case 'frontprintfile':
                $typeVal = 1;
                break;

            case 'mockupfront':
                $typeVal = 2;
                break;

            case 'backprintfile':
                $typeVal = 5;

                break;

            case 'mockupback':
                $typeVal = 3;

                break;

        }

        $image_id = $val['image_id'];

        $sql = "INSERT INTO wp_users_products_image_color_map (product_id, color_id, image_id, user_id, type) VALUES ($product_id,$color_id, $image_id, $typeVal)   ";

        $images_query = $wpdb->get_result($sql);

    }
}

function vd($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}

//fixing variant_id = 0 for some woocommerce product
function fix_wrong_shop_id($user_id, $shop, $keys, $old_shop_id, $shop_id)
{
    global $wpdb;

    $products = $wpdb->get_col("select id from wp_users_products where users_id = $user_id");
    $sql = "update wp_products_meta set shopid = $shop_id where shopid = $old_shop_id and meta_key in ('" . implode("','", $keys) . "') and product_id in (" . implode(",", $products) . ") ";
    debug($sql);
    //debug($wpdb->query($sql));
    $sql1 = "update wp_variants_meta set shop_id = $shop_id where shop_id=$old_shop_id and product_id in (" . implode(",", $products) . ") and meta_key='" . $shop . "_id'";
    debug($sql1);
    //debug($wpdb->query($sql1));

    //debug()

}
