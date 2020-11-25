<?php
function product_belongs_to($product_id, $user_id)
{
    global $wpdb;
    $productid = esc_sql($product_id);
    $productbelongs = $wpdb->get_var("SELECT `users_id` FROM `wp_users_products` WHERE `id` = $productid ");
    if ($productbelongs == $user_id)
        return true;
    return false;
}

function get_product_to_duplicates_data($product_id, $new_name, $new_sku)
{
    global $wpdb;
    $productinfo = $wpdb->get_row("select * from wp_users_products where id=$product_id", ARRAY_A);
    if (!empty($productinfo)) {
        $general_data = array(
            'pa_product_id' => $product_id,
            'brand_id' => $productinfo['brand_id'],
            'product_id' => $productinfo['product_id'],
            'title' => $new_name,
            'sku' => $new_sku,
            'weight' => $productinfo['weight'],
            'tags' => $productinfo['tags'],
            'description' => base64_decode($wpdb->escape($productinfo['description'])),
            'dofront' => $productinfo['front'],
            'doback' => $productinfo['back'],
            'default_color' => $productinfo['default_color'],
            'pagetype' => 3
        );
        return array('productinfo' => $productinfo, 'general_data' => $general_data);
    }
}

function prepare_duplication_variants($old_product_id, $sku, $title, $allcolors, $allsizes, $default_color = 0)
{

    global $wpdb;
    $color_arr = array();
    $size_arr = array();
    $variants = array();
    $sql = "select * from wp_users_products_colors where users_products_id='$old_product_id' order by id asc";
    $_variants = $wpdb->get_results($sql, ARRAY_A);
    $default_variants = array();
    $color_default = "";
    $current_user = wp_get_current_user();
    $currentusername = $current_user->user_login;
    $currentuserid = $current_user->ID;
    $cdn_alias = get_user_meta($currentuserid, 'cdn_alias', true);
    $blogurl = get_bloginfo('url');

    foreach ($_variants as $key => $_variant) {
        $size_id = $_variant['size_id'];
        $color_id = $_variant['color_id'];
        $image_id = $_variant['image_id'];
        $image = $wpdb->get_results("SELECT * FROM `wp_userfiles` WHERE `userID` = $currentuserid AND `deleted` = 0 AND `fileID`=$image_id", ARRAY_A);
        $image_url = get_thumb_path($image[0], $currentusername, $cdn_alias, $blogurl);
        $size_name = $allsizes[$size_id]['name'];
        if (!in_array($size_name, $size_arr))
            $size_arr[] = $size_name;
        $size_plus = $allsizes[$size_id]['plus'];
        $normalprice = $_variant['normalprice'];
        $plusprice = $_variant['plusprice'];
        $price = ($size_plus == 0.00) ? $normalprice : $plusprice;
        $color_name = $allcolors[$color_id]['name'];

        if ($color_id == $default_color)
            $color_default = $color_name;
        elseif (!in_array($color_name, $color_arr))
            $color_arr[] = $color_name;

        $color_code = $allcolors[$color_id]['code'];
        $variant = array(
            'title' => stripslashes($title) . '-' . $color_name . '-' . $size_name,
            'size_name' => $size_name,
            'size_id' => $size_id,
            'image_id' => $image_id,
            'image_url' => $image_url,
            'color_name' => $color_name,
            'color_id' => $color_id,
            'sku' => stripslashes($sku) . "-" . $color_name . "-" . $size_name,
            'color_code' => "#" . strtoupper($color_code),
            'normalprice' => $normalprice,
            'plusprice' => $plusprice,
            'price' => $price,
            'position' => $key
        );
        if ($default_color != 0 && $default_color == $color_id)
            $default_variants[] = $variant;
        else
            $variants[] = $variant;

        if (!isset($max_prices_color[$color_name]) || (isset($max_prices_color[$color_name]) && $max_prices_color[$color_name] < max($normalprice, $plusprice)))
            $max_prices_color[$color_name] = max($normalprice, $plusprice);
        if (!isset($min_prices_color[$color_name]) || (isset($min_prices_color[$color_name]) && $min_prices_color[$color_name] > min($normalprice, $plusprice)))
            $min_prices_color[$color_name] = min($normalprice, $plusprice);
    }
    if ($color_default != "")
        array_unshift($color_arr, $color_default);
    $final_variants = array_merge($default_variants, $variants);

    $min_price = min($min_prices_color);
    $max_price = max($max_prices_color);
    $min_max_price = min($max_prices_color);
    return array('variants' => $final_variants, 'min_price' => $min_price, 'max_price' => $max_price, 'min_max_price' => $min_max_price, 'max_prices_color' => $max_prices_color, 'color_arr' => $color_arr, 'size_arr' => $size_arr);
}

function duplicate_images($productid, $duplicated_id, $upload_path = 1)
{
    global $wpdb;
    $all_images = array();
    $current_user = wp_get_current_user();
    $currentusername = $current_user->user_login;
    $currentuserid = $current_user->ID;
    $blogurl = get_bloginfo('url');
    $wpdb->show_errors();
    $images = $wpdb->get_results("select * from wp_users_products_images where  users_products_id=$productid order by id asc", ARRAY_A);
    foreach ($images as $key => $image) {
        $image_id = $image['image_id'];
        $defaultimage = $image['defaultimage'];
        $storeimage = $image['storeimage'];
        $type = $image['type'];
        $wpdb->query("insert into wp_users_products_images (users_products_id,image_id,type,storeimage,defaultimage) values ($duplicated_id,$image_id,$type,$storeimage,$defaultimage)");
    }
    $image_pos = 0;
    $images_stores = $wpdb->get_results("select * from wp_users_products_images where  users_products_id=$productid and type='4' order by id asc", ARRAY_A);
    foreach ($images_stores as $key => $image) {
        $image_id = $image['image_id'];
        $user_images = $wpdb->get_results("select `cdn`,`cdn_orig`,`fileName` from `wp_userfiles` where `fileID` = $image_id", ARRAY_A);
        $user_images = $user_images[0];
        $imagefilename = $user_images['fileName'];
        if ($image['defaultimage'] == 1)
            $position = 0;
        else {
            $image_pos++;
            $position = $image_pos;
        }
        $imageurl = $blogurl . "/wp-content/uploads/user_uploads/" . rawurlencode($currentusername) . "/" . rawurlencode($imagefilename);
        $imageurl_cloud = get_cloud_orig_file_url($currentuserid, $imagefilename);
        $imageurl = ($imageurl_cloud != "") ? $imageurl_cloud : $imageurl;

        $imageadd = array('id' => $image_id, 'position' => $position, 'src' => $imageurl);

        if ($upload_path == 1) {
            $path_file = dirname(realpath(__FILE__)) . "/../../../uploads/user_uploads/" . $currentusername . "/" . $imagefilename;
            if ($user_images['cdn'] == 1)
                $path_file = upload_file_from_url($currentuserid, $currentusername, $imagefilename);
            $imageadd['path'] = $path_file;
        }
        $imageadd['cdn'] = 0;
        if ($user_images['cdn'] == 1)
            $imageadd['cdn'] = 1;

        if ($position == 0)
            array_unshift($all_images, $imageadd);
        else
            $all_images[] = $imageadd;
    }
    return $all_images;
}