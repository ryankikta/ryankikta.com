<?php
if (!function_exists('array_orderby')) {

    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}

function getOpenCartShop($user_id, $shop_id = 0)
{
    $filter = ($shop_id != 0 && $shop_id != NULL) ? " and id=" . $shop_id : "";
    $checkuser = $wpdb->get_result("select prefix,domain,token,version from wp_users_opencart where users_id='$user_id'" . $filter);

    if ($wpdb->num_rows($checkuser)) {
        $shoprow = $wpdb->get_row($checkuser);
        return array(
            'opencart_prefix' => $shoprow[0],
            'opencart_domain' => $shoprow[1],
            'opencart_token' => $shoprow[2],
            'opencart_version' => $shoprow[3]
        );
    }
    return array();
}

function check_opencart_shop($user_id, $type = 1)
{
    global $wpdb;
    $count = $wpdb->get_var("select count(id) from wp_users_opencart where users_id=$user_id");

    if ($type == 2)
        return (int)$count;
    return $count > 0;
}

function getCurrentOpenCartData($prodid)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);

    return array(
        'opencartactiveold' => $row['opencartactive'],
        'opencart_id' => get_product_meta($prodid, 'opencart_id'),
        'opencartCategory' => get_product_meta($prodid, 'opencartCategory'),
        'opencartModel' => get_product_meta($prodid, 'opencartModel'),
        'manuf_id' => get_product_meta($prodid, 'manufactureur'),
        'meta_tags' => get_product_meta($prodid, 'meta_tags'),
        'meta_tag_description' => get_product_meta($prodid, 'meta_tag_description'),
        'meta_tag_keywords' => get_product_meta($prodid, 'meta_tag_keywords')
    );
}

function getCurrentOpenCartData_shop($prodid, $shop_id = 0)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);

    return array(
        'opencartactiveold' => $row['opencartactive'],
        'opencart_id' => get_product_meta_shop($prodid, 'opencart_id', $shop_id),
        'opencartCategory' => get_product_meta_shop($prodid, 'opencartCategory', $shop_id),
        'opencartModel' => get_product_meta_shop($prodid, 'opencartModel', $shop_id),
        'manuf_id' => get_product_meta_shop($prodid, 'manufactureur', $shop_id),
        'meta_tags' => get_product_meta_shop($prodid, 'meta_tags', $shop_id),
        'meta_tag_description' => get_product_meta_shop($prodid, 'meta_tag_description', $shop_id),
        'meta_tag_keywords' => get_product_meta_shop($prodid, 'meta_tag_keywords', $shop_id)
    );
}

function get_all_opencart_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $op_auth = getOpenCartShop($user_id, $shop_id);
    @extract($op_auth);
    $all_products = array();
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $products = callOCart("GET", $opencart_token, $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products');

    foreach ($products->data as $product) {
        $pa_product_id = $wpdb->get_var("select pm.product_id from wp_products_meta as pm left join wp_users_products as up on(up.id=pm.product_id) where pm.meta_key='opencart_id' and meta_value=" . $product->id . " and up.users_id=" . $user_id);
        $all_products[] = array(
            "id" => $product->id,
            "title" => $product->name,
            "status" => "active",
            "url" => "",
            "image" => $product->image,
            "imported" => ($pa_product_id == NULL) ? 0 : 1,
            "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id,
            "shop_id" => $shop_id
        );
    }
    return $all_products;
}

function get_all_opencart_shops($user_id)
{
    global $wpdb;
    return $wpdb->get_results("select `id`,`domain`,`active` from `wp_users_opencart` where `users_id` = $user_id");
}

function get_opencart_product_import($user_id, $opencart_id, $shop_id = 0)
{

    $auth = getOpenCartShop($user_id, $shop_id);
    if (!empty($auth)) {
        @extract($auth);
        $product = OCgetProduct($opencart_id, $auth);

        if ($product->success) {
            $product = $product->data;
            $images = array();
            if (!in_array($product->image, $product->images))
                $images[] = $product->image;
            foreach ($product->images as $img) {
                $images[] = $img;
            }

            $colors = array();
            foreach ($product->options as $variant) {
                foreach ($variant->option_value as $value) {
                    if (get_color_id($value->name) != NULL) {
                        $colors[$value->name][] = ($value->price == FALSE) ? 0 : $value->price;
                    }
                }
            }

            $shop_colors = array();
            foreach ($colors as $color => $prices) {
                $shop_colors[$color][] = min($prices);
                $shop_colors[$color][] = max($prices);
            }

            return array(
                "title" => $product->name,
                "sku" => $product->sku,
                "description" => $product->description,
                "tags" => $product->tag,
                "meta_tags" => $product->meta_title,
                "meta_tag_keywords" => $product->meta_keyword,
                "meta_tag_description" => $product->meta_description,
                "opencartCategory" => $product->category[0]->id,
                "manuf_id" => $product->manufacturer_id,
                "opencartModel" => $product->model,
                "shop_images" => $images,
                "shop_colors" => $colors
            );
        }
    }
    return array();
}

function getOpenCartData($data, $shop_id = 0)
{
    $oc_id = ($shop_id == 0) ? "" : $shop_id;

    $opencartnewnroduct = esc_sql($data['opencartnewproduct' . $oc_id]);
    $opencartCategory = $data['opencartCategory' . $oc_id];
    $opencartModel = trim($data['opencartModel' . $oc_id]);
    $meta_tags = trim(str_replace('"', '\"', stripslashes($data['meta_tags' . $oc_id])));
    $manuf_id = (isset($data['manuf-id' . $oc_id])) ? $data['manuf-id' . $oc_id] : $data['manuf_id' . $oc_id];
    $manuf_name = (isset($data['manuf-name' . $oc_id])) ? $data['manuf-name' . $oc_id] : $data['manuf_name' . $oc_id];

    $meta_description = str_replace("\r\n", "", $data['meta_tag_description' . $oc_id]);
    $i = 0;
    while ($i < strlen($meta_description)) {
        $meta_description = str_replace("\\", '', $meta_description);
        $meta_description = str_replace('"', '\"', $meta_description);
        $i++;
    }
    $meta_description = trim($meta_description);

    $meta_key = str_replace("\r\n", "", $data['meta_tag_keywords' . $oc_id]);
    $i = 0;
    while ($i < strlen($meta_key)) {
        $meta_key = str_replace("\\", '', $meta_key);
        $meta_key = str_replace('"', '\"', $meta_key);
        $i++;
    }
    $meta_key = trim($meta_key);

    return array(
        'opencartnewnroduct' => $opencartnewnroduct,
        'opencartCategory' => $opencartCategory,
        'opencartModel' => $opencartModel,
        'meta_tags' => $meta_tags,
        'meta_tag_description' => $meta_description,
        'meta_tag_keywords' => $meta_key,
        'manuf_id' => $manuf_id,
        'manuf_name' => $manuf_name
    );
}

function oCCheckProduct($opencart_id, $op_auth)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $Url = $opencart_prefix . $opencart_domain . "/index.php?route=" . $extension . "feed/rest_api/products&id=$opencart_id";
    $out = callOCart("GET", $opencart_token, $Url);
    return $out;
}

function getOpenCartProductData($opencart_id, $op_auth)
{
    if (!empty($op_auth) && $opencart_id != 0) {
        $prod = OCgetProduct($opencart_id, $op_auth);
        if ($prod->success)
            return 1;
        return 0;
    }
    return 0;
}

function opencartVariants($basicVariants, $basicPrice, $max_prices_color)
{
    $bloquage = 0;
    if ($_SERVER['REMOTE_ADDR'] == "59.162.181.90") {

        foreach ($basicVariants as $variant) {
            $plusValue = $max_prices_color[$variant['color_name']] - $basicPrice;
            if ($bloquage == 0) {
                $colors[] = $variant['color_name'];
                $color_options[] = array('option' => $variant['color_name'], 'plus_value' => $plusValue);
                $sizes[] = $variant['size_name'];
                $s_ord = mysql_fetch_array($wpdb->get_result("select s_ordering from `wp_rmproductmanagement_sizes` where size_id='" . $variant['size_id'] . "'"));
                $size_options[] = array('option' => $variant['size_name'], 'ordering' => $s_ord['s_ordering'], 'plus_value' => 0.0);
                $bloquage = 1;
            } else {
                if (!in_array($variant['color_name'], $colors)) {
                    $colors[] = $variant['color_name'];
                    $color_options[] = array('option' => $variant['color_name'], 'plus_value' => $plusValue);
                }
                if (!in_array($variant['size_name'], $sizes)) {
                    $sizes[] = $variant['size_name'];
                    $s_ord = mysql_fetch_array($wpdb->get_result("select s_ordering from `wp_rmproductmanagement_sizes` where size_id='" . $variant['size_id'] . "'"));
                    $size_options[] = array('option' => $variant['size_name'], 'ordering' => $s_ord['s_ordering'], 'plus_value' => 0.0);
                }
            }
        }

        $size_optionsss = array_orderby($size_options, 'ordering', SORT_ASC);

        $size_options = array();

        foreach ($size_optionsss as $k => $v) {
            unset($v['ordering']);
            $size_options[$k] = $v;
        }
    } else {

        foreach ($basicVariants as $variant) {
            $plusValue = $max_prices_color[$variant['color_name']] - $basicPrice;
            if ($bloquage == 0) {
                $colors[] = $variant['color_name'];
                $color_options[] = array('option' => $variant['color_name'], 'plus_value' => $plusValue);
                $sizes[] = $variant['size_name'];
                $size_options[] = array('option' => $variant['size_name'], 'plus_value' => 0.0);
                $bloquage = 1;
            } else {
                if (!in_array($variant['color_name'], $colors)) {
                    $colors[] = $variant['color_name'];
                    $color_options[] = array('option' => $variant['color_name'], 'plus_value' => $plusValue);
                }
                if (!in_array($variant['size_name'], $sizes)) {
                    $sizes[] = $variant['size_name'];
                    $size_options[] = array('option' => $variant['size_name'], 'plus_value' => 0.0);
                }
            }
        }
    }

    return array('color_options' => $color_options, 'size_options' => $size_options);
}

function addOpencartProduct($user_id, $product_data, $opencartData, $basicVariants, $max_prices_color, $basicPrice, $opencart_product_id, $productid, $shop_id = 0)
{
    global $wpdb;
    @extract(opencartVariants($basicVariants, $basicPrice, $max_prices_color));
    @extract($opencartData);
    @extract($product_data);
    $oc_auth = getOpenCartShop($user_id, $shop_id);
    @extract($oc_auth);
    if ($manuf_id == '') {
        $manufs = OCManufacturers($oc_auth);
        foreach ($manufs as $manu) {
            if (strtolower($manu->name) == strtolower($manuf_name))
                $manuf_id = $manu->manufacturer_id;
        }
        if ($manuf_id == '') {
            $new_brand = OCAddManufacturer($oc_auth, $manuf_name);
            if ($new_brand->success == true)
                $manuf_id = $new_brand->manufacturer_id;
        }
    }
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    if ($opencart_product_id != 0) {
        $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products&id=' . $opencart_product_id;
        $method = 'PUT';
    } else {
        $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products';
        $method = 'POST';
    }


    foreach ($size_options as $k => $size_option) {
        $size_options[$k]['sort_order'] = $k + 1;
    }
    $data = array(
        'model' => stripcslashes($opencartModel),
        'sku' => $sku,
        'colors' => $color_options,
        'sizes' => $size_options,
        'tax_class_id' => 9,
        'quantity' => 1000,
        'price' => $basicPrice,
        'weight' => $weight * 453.59237,
        'weight_class_id' => 2,
        'stock_status_id' => 7,
        'manufacturer_id' => intval($manuf_id),
        'sort_order' => 1,
        'status' => 1,
        'shipping' => 1,
        'product_store' => array(0),
        'product_category' => array($opencartCategory),
        'product_description' => array(
            '1' => array(
                'name' => stripcslashes($title),
                'description' => stripcslashes($description),
                'product_tags' => stripcslashes($tags),
                'meta_tags' => stripcslashes($meta_tags),
                'meta_keyword' => stripcslashes($meta_tag_keywords),
                'meta_description' => stripcslashes($meta_tag_description)
            )
        )
    );
    $options = array(
        'method' => $method,
        'header' => "X-Oc-Merchant-Id:" . $opencart_token
    );
    $myoptions = json_encode($data);

    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, $options);
    curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_POSTFIELDS, $myoptions);
    $newProduct = curl_exec($request);

    $response = json_decode($newProduct);

    if ($response->success) {
        if ($opencart_product_id == 0)
            $opencart_product_id = $response->product_id;
        $all_meta = array('opencart_id' => 'NULL', 'opencartModel' => 'NULL', 'opencartCategory' => 'NULL', 'manufactureur' => 'NULL', 'meta_tags' => 'NULL', 'meta_tag_keywords' => 'NULL', 'meta_tag_description' => 'NULL');
        $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $productid and shopid = $shop_id", ARRAY_A);
        foreach ($results as $res) {
            $all_meta[$res['meta_key']] = $res['meta_id'];
        }
        $pa_product_id_old = get_product_id_meta_shop($opencart_product_id, "opencart_id", $shop_id);
        $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $productid) ? $pa_product_id_old : 0;
        if ($prod_to_deconnect) {
            update_product_meta_shop($productid, 'opencart_id', $opencart_product_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'opencartModel', esc_sql($opencartModel), $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'opencartCategory', $opencartCategory, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'manufactureur', $manuf_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'meta_tags', esc_sql($meta_tags), $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'meta_tag_keywords', esc_sql($meta_tag_keywords), $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'meta_tag_description', esc_sql($meta_tag_description), $shop_id, 0, $prod_to_deconnect);
        } else {
            $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['opencart_id']},$productid,'opencart_id','$opencart_product_id',$shop_id),"
                . " ({$all_meta['opencartModel']},$productid,'opencartModel','" . esc_sql($opencartModel) . "',$shop_id),"
                . " ({$all_meta['opencartCategory']},$productid,'opencartCategory','" . $opencartCategory . "',$shop_id),"
                . " ({$all_meta['manufactureur']},$productid,'manufactureur','" . $manuf_id . "',$shop_id),  "
                . " ({$all_meta['meta_tags']},$productid,'meta_tags','" . esc_sql($meta_tags) . "',$shop_id),"
                . " ({$all_meta['meta_tag_keywords']},$productid,'meta_tag_keywords','" . esc_sql($meta_tag_keywords) . "',$shop_id),  "
                . " ({$all_meta['meta_tag_description']},$productid,'meta_tag_description','" . esc_sql($meta_tag_description) . "',$shop_id) "
                . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";
            $wpdb->query($sql);
        }
        $wpdb->get_result("UPDATE `wp_users_products` SET  `opencartactive`=1 WHERE `id`= $productid ");
    }
    return $opencart_product_id;
}

function oCDuplicate($printAuraProdId, $newCopy, $product_data)
{
    $currentUser = wp_get_current_user();
    $userid = $currentUser->ID;
    $data = getOpenCartShop($userid);
    @extract($data);
    global $wpdb;
    $prodId = get_product_meta($printAuraProdId, 'opencart_id');

    $dataPp = array(
        'model' => 'duplicate',
        'sku' => $product_data['sku'],
        'tax_class_id' => 1,
        'quantity' => 1000, //to change with input value
        'manufacturer_id' => $prodId, // porduct id to duplicate
        'sort_order' => 1,
        'status' => 1,
        'product_store' => array(0),
        'product_category' => array(1),
        'product_description' => array(
            '1' => array(
                'name' => $product_data['title'],
                'meta_description' => "OCMetaTagDesc",
                'meta_keyword' => "OCMetaTagWords",
                'description' => "description"
            )
        )
    );
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products';
    $response = callOCart('POST', $opencart_token, $url, $dataPp);
    $productDupId = $response->product_id;
    if ($productDupId != '') {
        insert_product_meta($newCopy, 'opencart_id', $productDupId);
        $opencartCategory = get_product_meta($printAuraProdId, 'opencartCategory');
        insert_product_meta($newCopy, 'opencartCategory', $opencartCategory);
        $opencartModel = get_product_meta($printAuraProdId, 'opencartModel');
        insert_product_meta($newCopy, 'opencartModel', $opencartModel);
        $wpdb->query("update wp_users_products set opencartactive=1 where id =" . $newCopy);
    }
    return $productDupId;
}

// add product images
function addOCPhotos($user_id, $OCProduct_id, $photos, $currentusername, $shop_id = 0)
{
    $defaultdone;
    $default_image_id;
    $default_image = array();
    $shop_Data = getOpenCartShop($user_id, $shop_id);
    @extract($shop_Data);
    foreach ($photos['storeimages'] as $photo) {
        if ($defaultdone == 0) {
            if (empty($photos['defaultimage'][0])) {
                $default_image_id = intval($photo);
            } else {
                $default_image_id = $photos['defaultimage'][0];
            }
            $defaultdone = 1;
        }
        $photo = intval($photo);
        if ($photo != 0) {
            $imagefilenamequery = $wpdb->get_result("SELECT `fileName`, `fileType` FROM `wp_userfiles` WHERE `fileID` = $photo") or die(mysql_error());
            $imagefilerow = $wpdb->get_row($imagefilenamequery);
            $imagefilename = $imagefilerow[0];
            $imagefiletype = $imagefilerow[1];
            $imagefiletype = str_replace('.', "", $imagefiletype);
            if ($imagefiletype == 'jpeg' || $imagefiletype == 'jpg') {
                $imagefiletype = 'jpeg';
            }
            $position = 2;

            $blogurl = get_bloginfo('url');
            $imageurl = ABSPATH . "wp-content/uploads/user_uploads/$currentusername/$imagefilename";
            $imageurls[] = ABSPATH . "wp-content/uploads/user_uploads/$currentusername/$imagefilename";
            // upload to opencart            
            $options = array(
                'method' => "POST",
                'header' => "X-Oc-Merchant-Id:" . $opencart_token
            );
            $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
            $Url = $opencart_prefix . $opencart_domain . "/index.php?route=" . $extension . "feed/rest_api/productimages&id=$OCProduct_id";      // $OCProduct_id
            if ($photo == $default_image_id) {
                $def_img = TRUE;
            } else {
                $def_img = FALSE;
            }
            $image = array(
                "file" => '@' . $imageurl . ";type=image/jpeg",
                "default" => $def_img,
            );
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, $Url);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_HTTPHEADER, $options);
            curl_setopt($request, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, $image);
            $newPhoto = curl_exec($request);
            $response = json_decode($newPhoto);
            $image_id[] = $response->data[0];
        }
    }


    return $image_id;
}

function UploadOpenCartStoreImages($images, $opencart_id, $op_auth, $upload_path)
{
    @extract($op_auth);
    foreach ($images as $key => $image) {

        $image_id = $image['id'];
        $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
        $options = array('method' => "POST", 'header' => "X-Oc-Merchant-Id:" . $opencart_token);
        $url = $opencart_prefix . $opencart_domain . "/index.php?route=" . $extension . "feed/rest_api/productimages&id=$opencart_id";
        $def_img = ($key == 0) ? true : false;
        if ($upload_path == 0)
            $data = array("src" => $image['src'], "default" => $def_img);
        else
            $data = array("file" => '@' . $image['path'] . ";type=image/png", "default" => $def_img);
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $options);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        $json = curl_exec($request);
        if (curl_errno($request)) {
            $error = 'Curl Error: "' . curl_error($request) . '" - Code: ' . curl_errno($request);
            $result[$image_id] = array('status' => 'failed', 'errors' => $error);
        } else {
            $response = json_decode($json);
            $result[$image_id] = array('status' => 'success', 'data' => $response->data[0]);
        }
    }
    return $result;
}

function OCGetManufactureurId($brand, $user_id, $op_auth)
{
    global $wpdb;
    $manuf_exist = 0;
    $manuf_id = 0;
    $mydata2 = array();
    $manufs = get_user_meta($user_id, 'manufactureurs', true);


    if ($manufs != '') {
        $mydata = unserialize($manufs);
        foreach ($mydata as $mnf) {
            if ($brand == $mnf["name"]) {
                $manuf_id = $mnf["id"];
                $manuf_exist = 1;
            }
        }
    }

    if ($manuf_exist != 1) {
        $new_brand = OCAddManufacturer($op_auth, $brand);
        if ($new_brand->success == true) {
            $manuf_id = $new_brand->manufacturer_id;
            if (!empty($mydata)) {
                $mydata2 = array(sizeof($mydata) => array('name' => $brand, 'id' => $manuf_id));
                $mydata = array_merge($mydata, $mydata2);
            } else {
                $mydata = array('name' => $brand, 'id' => $manuf_id);
            }

            $meta_value = serialize($mydata);
            update_user_meta($user_id, 'manufactureurs', $meta_value);
        }
    }
    return $manuf_id;
}

// list Manufacturers
function OCManufacturers($auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/manufacturers';
    $out = callOCart("GET", $opencart_token, $url);
    return $out->data;
}

//add new manufacturers  
function OCAddManufacturer($op_auth, $brand)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/manufacturers';
    $data = array(
        'name' => $brand,
        'sort_order' => 0,
        'keyword' => "clothes",
        'manufacturer_store' => array(0)
    );
    $out = callOCart("POST", $opencart_token, $url, $data);
    return $out;
}

function OCgetCategories($op_auth)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/categories';
    $out = callOCart("GET", $opencart_token, $url);
    return $out->data;
}

function oc_categories_parent($oc_auth, $parent_category_id = 0)
{
    $categories = array();
    @extract($oc_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $call = callOCart("GET", $opencart_token, $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/categories&parent=' . $parent_category_id);
    $data = $call->data;

    if ($data == NULL)
        $data = array();

    foreach ($data as $main_category) {
        $categories[$main_category->category_id] = array(
            "name" => $main_category->name,
            "parent" => $main_category->parent_id,
        );
        $category = oc_categories_parent($oc_auth, $main_category->category_id);
        $categories = $categories + $category;
    }
    return $categories;
}

function oc_categories_desply_paths($oc_auth)
{
    $categories = array();
    $all_categories = oc_categories_parent($oc_auth);
    foreach ($all_categories as $key => $category) {
        $cat_name = $category['name'];
        $parent_id = $category['parent'];

        while ($parent_id != 0) {
            foreach ($all_categories as $ctg_key => $ctg) {
                if ($ctg_key == $parent_id) {
                    $cat_name = $ctg['name'] . " > " . $cat_name;
                    $parent_id = $ctg["parent"];
                    break;
                }
            }
        }

        $categories[$key] = strtolower($cat_name);
    }

    return $categories;
}

// get opencart product by id
function OCgetProduct($OCProduct_id, $op_auth)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $Url = $opencart_prefix . $opencart_domain . "/index.php?route=" . $extension . "feed/rest_api/products&id=$OCProduct_id";
    $out = callOCart("GET", $opencart_token, $Url);
    return $out;
}

// delete opencart's product
function deleteOpencartProduct($opencart_id, $op_auth, $productid = 0, $shop_id = 0)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products&id=' . $opencart_id;
    $out = callOCart("DELETE", $opencart_token, $url);
    if ($out->success) {
        if ($productid != 0) {
            $prodid = get_product_id_meta_shop($opencart_id, "opencart_id", $shop_id);
            if (!$prodid)
                $shop_id = 0;
            delete_product_meta_multi_shop($productid, "'opencart_id','opencartCategory','opencartModel','meta_tags','meta_tag_description','meta_tag_keywords','manufactureur'", $shop_id);
            delete_variants_product_meta_shop($productid, 'opencar_id', $shop_id);
        }
    }
}

function unset_row_table($tabs, $option)
{
    foreach ($tabs as $key => $opt) {
        if (in_array($opt['option'], $option))
            unset($tabs[$key]);
    }
    return $tabs;
}

// synchronise opencart
function synchronize_opencart($user_id, $pa_product_id, $opencart_id, $color_name, $size_name, $syn_type)
{
    global $wpdb;
    $color_id = get_color_id($color_name);
    $size_id = get_size_id($size_name);
    $count_color = (int)$wpdb->get_var("select count(color_id) from wp_users_products_colors where color_id =$color_id and users_products_id=$pa_product_id");
    if ($syn_type == 1 || ($syn_type == 2 && $count_color == 0)) {
        $user = get_user_by('id', $user_id);
        $user_name = sanitize_text_field($user->user_login);
        $op_auth = getOpenCartShop($user_id);
        @extract($op_auth);
        $url = $opencart_prefix . $opencart_domain . '/index.php?route=feed/rest_api/products&id=' . $opencart_id;
        $productinfo = $wpdb->get_row("select * from wp_users_products where id=" . $pa_product_id, ARRAY_A);
        @extract($productinfo);
        $product_data = getCurrentOpenCartData($pa_product_id);
        @extract($product_data);
        $opencart_description = base64_decode($description);
        $all_colors_sizes = GET_All_colors_sizes();
        @extract($all_colors_sizes);
        $variants = prepare_duplication_variants($pa_product_id, $sku, $title, $allcolors, $allsizes);
        @extract($variants);
        $op_variants = opencartVariants($variants, $min_max_price, $max_prices_color);
        @extract($op_variants);

        if ($syn_type == 2)
            $color_options = unset_row_table($color_options, array($color_name));
        else {
            $_SESSION['sizes_toremove'][$pa_product_id][] = $size_name;
            $size_options = unset_row_table($size_options, $_SESSION['sizes_toremove'][$pa_product_id]);
        }
        $data = array(
            'model' => $opencartModel,
            'sku' => $sku,
            'colors' => $color_options,
            'sizes' => $size_options,
            'tax_class_id' => 9,
            'quantity' => 1000,
            'price' => $min_max_price,
            'weight' => $weight * 453.59237,
            'weight_class_id' => 2,
            'stock_status_id' => 7,
            'manufacturer_id' => $manufactureur,
            'sort_order' => 1,
            'status' => 1,
            'shipping' => 1,
            'product_store' => array(0),
            'product_category' => array($opencartCategory),
            'product_description' => array(
                '1' => array(
                    'name' => $title,
                    'description' => $opencart_description,
                    'meta_tags' => $meta_tags,
                    'meta_description' => $meta_tag_description,
                    'meta_keyword' => $meta_tag_keywords,
                    'product_tags' => $product_tags
                )
            )
        );
        $options = array(
            'method' => 'PUT',
            'header' => "X-Oc-Merchant-Id:" . $opencart_token
        );

        $myoptions = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myoptions);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');

        $return = curl_exec($ch);
        json_decode($return);
        $images = duplicate_images($pa_product_id, $user_name);
        UploadOpenCartStoreImages($images, $opencart_id, $op_auth);
    }
}

// get opencart products
function OCGetProducts($op_auth)
{
    @extract($op_auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products';
    $out = callOCart("GET", $opencart_token, $url);
    return $out;
}

function saveOpencartVariants($pAProductId, $opencartProductId, $varaiants, $update)
{
    global $wpdb;
    $bVariants = opencartVariants($varaiants, 0, 0);
    @extract($bVariants);
    if ($update) {
        $oldOpencartVariants = $wpdp->get_results("select opencart_id from wp_rmproductmanagement_colors where users_products_id= '$pAProductId' and opencart_id is not null");
    }
    foreach ($color_options as $color) {
        $PAcolor_id [] = $wpdb->get_var('select color_id from wp_rmproductmanagement_colors where color_name="' . $color['option'] . '"');
        foreach ($size_options as $size) {
            $PAsize_id = $wpdb->get_var('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size['option'] . '"');
            $oc_id = $opencartProductId . '-' . $size['option'] . '-' . $color['option'];
            $product_id = intval($pAProductId);
            $color_id = intval($PAcolor_id);
            $size_id = intval($PAsize_id);
            $variant1 = $wpdb->query("UPDATE `wp_users_products_colors` SET `opencart_id`='$oc_id' WHERE `color_id`='$color_id' AND `size_id`='$size_id' AND `users_products_id`='$product_id' ") or ('dieUpd' . mysql_error());
            $vrs[] = $variant1;
        }
    }
    return $vrs;
}

//save porduct variants : forama  $opencartId-size_name-color_name
function saveVariants($prodctData, $pa_product_id, $variants_info)
{
    global $wpdb;
    $vars = array();
    $colorsOPC;
    $sizes;
    $OCProd_id = $prodctData->data->id;
    // separation of options
    foreach ($prodctData->data->options as $option) {
        if ($option->name == 'colors') {
            $colorsOPC = $option;
        } elseif ($option->name == 'Size') {
            $sizes = $option;
        }
    }
    // all combinations available 
    foreach ($colorsOPC->option_value as $color) {
        $colorName = $color->name;
        $PAcolor_id = $wpdb->get_var('select color_id from wp_rmproductmanagement_colors where color_name="' . $colorName . '"');
        foreach ($sizes->option_value as $size) {
            $sizeName = $size->name;
            $PAsize_id = $wpdb->get_var('select size_id from wp_rmproductmanagement_sizes where size_name="' . $sizeName . '"');
            $variant_name = $colorName . "-" . $sizeName;
            foreach ($variants_info as $variant) {
                if ($variant['name'] == $variant_name) {
                    $normal_price = $variant['normal_price'];
                    $plus_price = $variant['plus_price'];
                    $oc_id = $OCProd_id . '-' . $sizeName . '-' . $colorName;
                    $product_id = intval($pa_product_id);
                    $color_id = intval($PAcolor_id);
                    $size_id = intval($PAsize_id);
                    $wpdb->show_errors();
                    $variant1 = $wpdb->query("UPDATE `wp_users_products_colors` SET `opencart_id`='$oc_id' WHERE `color_id`='$color_id' AND `size_id`='$size_id' AND `users_products_id`='$product_id' ") or ('dieUpd' . mysql_error());
                }
            }
        }
    }
    return $variant1;
}

function checkOCShop($domain, $token, $version = "")
{
    $extension = "";
    if (in_array($version, array("2.3", "3.0")))
        $extension = "extension/";

    return callOCart("GET", $token, $domain . '/index.php?route=' . $extension . 'feed/rest_api/getchecksum');
}

// if the opencart already exist -> return 2 if yes else return 1
function chekIfAlreadyExist($domain)
{
    global $wpdb;
    $return = array();
    $user_id = $wpdb->get_var("select `users_id` from `wp_users_opencart` where domain='$domain'");
    if ($user_id)
        return array('user_id' => $user_id);
    return $return;
}

function callOCart($method, $token, $url, $data = 0)
{
    $options = array(
        'header' => "X-Oc-Merchant-Id:" . $token
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    if (!empty($data) || $data != 0) {
        $dataToSend = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToSend);
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $options);
    $output = curl_exec($ch);
    /*if($token == "596482"){
    debug(array(curl_error($ch),$output,$url,$method,$token));     
    }*/

    $pos = strpos($output, '{"success"');
    $output = substr($output, $pos);
    $out = json_decode($output);
    return $out;
}

function opencartUpdateOrder($orderId, $user_id, $trackinNumber, $shop_id = 0)
{

    $auth = getOpenCartShop($user_id, $shop_id);
    extract($auth);
    $orderStatus = 'Shipped';
    if ($orderStatus == 'Shipped')
        $notify = '1';
    else
        $notify = '';
    $trakingData = array(
        'status' => $orderStatus,
        'tracking' => $trackinNumber,
        'Notify' => $notify
    );
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';

    $Url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/orderstatus&id=' . $orderId;
    return $output = callOCart('PUT', $token, $Url, $trakingData);
}

function oCDuplicate2($userid, $printAuraProdId, $newCopy, $shop_id = 0)
{

    $data = getOpenCartShop($userid, $shop_id);
    @extract($data);
    global $wpdb;
    $prodId = get_product_meta($printAuraProdId, 'opencart_id');
    $dataPp = array(
        'model' => 'duplicate',
        'sku' => 'sku',
        'tax_class_id' => 9,
        'quantity' => 1000, //to change with input value
        'price' => 'price',
        'manufacturer_id' => $prodId, // porduct id to duplicate
        'sort_order' => 1,
        'status' => 1,
        'product_store' => array(0),
        'product_category' => array(1),
        'product_description' => array(
            '1' => array(
                'name' => "title",
                'meta_description' => "OCMetaTagDesc",
                'meta_keyword' => "OCMetaTagWords",
                'description' => "description"
            )
        )
    );
    $text = 'opencartid';
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $Url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/products';
    $response = callOCart('POST', $opencart_token, $Url, $dataPp);
    $productDupId = $response->product_id;
    return $productDupId;
}

/* * ****************************************OpenCart Orders Functions***************************************************** */

function getOCOrders($user_id, $shop_id = 0)
{
    $shop_Data = getOpenCartShop($user_id, $shop_id);
    @extract($shop_Data);
    //$url = $opencart_prefix.$opencart_domain . '/index.php?route=feed/rest_api/listorders';
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/orders';
    $out = callOCart("GET", $opencart_token, $url);
    return $out->data;
}

function get_order_opencart_data($order_id, $auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/orders&id=' . $order_id;
    $out = callOCart("GET", $opencart_token, $url);
    $order = $out->data;

    return array('order_status_id' => $order->order_status_id, 'shop_order_id' => $order->order_id, 'shop_order_name' => $order->order_id, 'email' => $order->email, 'customerphone' => $order->telephone, 'itemsinfo' => $order->products, 'order_data' => $order);
}

function get_order_opencart($order_id, $auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/orders&id=' . $order_id;
    $out = callOCart("GET", $opencart_token, $url);
    return $out;
}

function get_order_opencart_data2($order_id, $auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $url = $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/orders';
    $out = callOCart("GET", $opencart_token, $url);
    return $out->data;
    /*$shop_Data = getOpenCartShop($user_id);extract($shop_Data);
      $url = $opencart_prefix. $opencart_domain . '/index.php?route=feed/rest_api/listorderswithdetails';
      $out=callOCart("GET",$opencart_token,$url);
      return $out->data;*/
}

function get_allitem_opencart($itemsinfo, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();

    foreach ($itemsinfo as $item) {
        $oc_id = $wpdb->escape($item->product_id);
        $order_product_id = $item->order_product_id;
        $item_price = filter_var($item->price, FILTER_SANITIZE_NUMBER_FLOAT) / 100;
        $quantity = $item->quantity;

        $product = $wpdb->get_results("select t2.id,t2.`product_id`,t2.`brand_id`,t2.`front`,t2.`back` from wp_products_meta as t1 inner join wp_users_products as t2 on (t1.product_id = t2.id) where t1.meta_value = $oc_id and t1.meta_key = 'opencart_id' and t1.shopid = $shop_id and  t2.users_id =" . $user_id, ARRAY_A);
        if (!$product)
            $product = $wpdb->get_results("select t2.id,t2.`product_id`,t2.`brand_id`,t2.`front`,t2.`back` from wp_products_meta as t1 inner join wp_users_products as t2 on (t1.product_id = t2.id) where t1.meta_value = $oc_id and t1.meta_key = 'opencart_id' and  t2.users_id =" . $user_id, ARRAY_A);
        if ($product) {
            $product = end($product);
            $pa_product_id = $product['id'];
            $inventory_id = $product['product_id'];
            $brand_id = $product['brand_id'];
            $hasfront = $product['front'];
            $hasback = $product['back'];

            $productoptions = $item->option;
            $sku = $item->sku;

            if ($pa_product_id > 0) {
                foreach ($productoptions as $prod_option) {
                    if (strtoupper($prod_option->name) == strtoupper('Color'))
                        $color_name = $prod_option->value;
                    if (strtoupper($prod_option->name) == strtoupper('Size'))
                        $size_name = $prod_option->value;
                }

                if ($color_name != "" && $size_name != "")
                    $variant_sku = $sku . '-' . $color_name . '-' . $size_name;

                $color_size = $wpdb->get_results("select `color_id`,`size_id` from `wp_users_products_colors` where `sku` = '$variant_sku' and `users_products_id`='$pa_product_id'");
                $color_id = $color_size[0]->color_id;
                $size_id = $color_size[0]->size_id;

                if ($color_id == null || $size_id == null) {
                    $colors_id = get_colors_col($color_name);
                    $sizes_id = get_sizes_col($size_name);
                    if (count($colors_id) > 0 && count($sizes_id) > 0) {
                        $variants = $wpdb->get_results("select color_id,size_id from wp_users_products_colors where `color_id` in (" . implode(",", $colors_id) . ") and `size_id` in (" . implode(",", $sizes_id) . ") and users_products_id = $pa_product_id limit 1");
                        if ($variants) {
                            $color_id = $variants[0]->color_id;
                            $size_id = $variants[0]->size_id;
                        }
                    }
                }
                $items[] = array('item_id' => $order_product_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
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

function get_allitem_opencart_byshop($itemsinfo, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();

    foreach ($itemsinfo as $item) {
        $oc_id = $wpdb->escape($item->product_id);
        $order_product_id = $item->order_product_id;
        $item_price = filter_var($item->price, FILTER_SANITIZE_NUMBER_FLOAT) / 100;
        $quantity = $item->quantity;

        $product = $wpdb->get_results("SELECT t1.product_id as product_id,t2.`product_id` as inventory_id,t2.`brand_id`,t2.`front`,t2.`back`  FROM wp_products_meta AS t1 INNER JOIN wp_users_products AS t2 ON t1.product_id = t2.id WHERE t1.meta_value = $oc_id and t1.meta_key = 'opencart_id' and t1.shopid = $shop_id and  t2.users_id =" . $user_id, ARRAY_A);
        if ($product) {
            $product = end($product);
            $pa_product_id = $product['product_id'];
            $inventory_id = $product['inventory_id'];
            $brand_id = $product['brand_id'];
            $hasfront = $product['front'];
            $hasback = $product['back'];

            $productoptions = $item->option;
            $sku = $item->sku;

            if ($pa_product_id > 0) {
                foreach ($productoptions as $prod_option) {
                    if (strtoupper($prod_option->name) == strtoupper('Color'))
                        $color_name = $prod_option->value;
                    if (strtoupper($prod_option->name) == strtoupper('Size'))
                        $size_name = $prod_option->value;
                }

                if ($color_name != "" && $size_name != "")
                    $variant_sku = $sku . '-' . $color_name . '-' . $size_name;

                $color_id = $wpdb->get_var("select `color_id` from `wp_users_products_colors` where `sku` = '$variant_sku' and `users_products_id`='$pa_product_id'");
                $size_id = $wpdb->get_var("select `size_id` from `wp_users_products_colors` where `sku` = '$variant_sku' and `users_products_id`='$pa_product_id'");

                if ($color_id == null || $size_id == null) {
                    $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $color_name . '"', ARRAY_A);
                    $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"', ARRAY_A);
                    foreach ($colors_id as $color) {
                        $color_id = $color['color_id'];
                        foreach ($sizes_id as $size) {
                            $size_id = $size['size_id'];
                            $count_item = $wpdb->get_var("select count(id) from `wp_users_products_colors` where users_products_id=$pa_product_id and color_id=$color_id and size_id = $size_id");
                            if ($count_item > 0)
                                break 2;
                        }
                    }
                }

                $items[] = array('item_id' => $order_product_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
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

function opencart_shipping_address($order)
{

    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = $order->shipping_firstname . " " . $order->shipping_lastname;
    $shippingaddress1['address1'] = $order->shipping_address_1;
    $shippingaddress1['address2'] = $order->shipping_address_2;
    $shippingaddress1['city'] = $order->shipping_city;
    $shippingaddress1['state'] = $order->shipping_zone;
    $shippingaddress1['zipcode'] = $order->shipping_postcode;
    $shippingaddress1['country'] = $order->shipping_iso_code_2;
    $address2 = ($order->shipping_address_2 != "") ? $shippingaddress1['address2'] . "\n" : "";
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $order->shipping_country;
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);

    $state = $order->shipping_zone;
    $state_code = $order->shipping_zone_code;
    $shippingaddress_zip = $order->shipping_postcode;
    if ($order->shipping_iso_code_2 == "US")
        $shipping_id = 1;
    else if ($order->shipping_iso_code_2 == "CA")
        $shipping_id = 2;
    else
        $shipping_id = 3;
    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $order->shipping_country, 'shippingaddress_state' => $state, 'shippingaddress_state_code' => $state_code, 'shippingaddress_zip' => $shippingaddress_zip, 'shipping_id' => $shipping_id, 'paypal_address' => $paypal_address);
}

function get_opencart_order_status($auth = array())
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $out = callOCart("GET", $opencart_token, $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/order_statuses');
    if ($out->success) {
        $statuses = array();
        foreach ($out->data as $status) {
            $statuses[$status->order_status_id] = $status->name;
        }
        return $statuses;
    }
    return array();
}

function get_oc_default_order_statuses($auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $out = callOCart("GET", $opencart_token, $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/order_statuses');
    if ($out->success) {
        $statuses = array();
        foreach ($out->data as $status) {
            if ($status->name == "Processed" || $status->name == "Processing")
                $statuses[] = (int)$status->order_status_id;
        }
        return $statuses;
    }
    return array();
}

function get_store_info($auth)
{
    @extract($auth);
    $extension = (in_array($opencart_version, array("2.3", "3.0"))) ? 'extension/' : '';
    $out = callOCart("GET", $opencart_token, $opencart_prefix . $opencart_domain . '/index.php?route=' . $extension . 'feed/rest_api/get_store_info');
    if ($out->success) {
        $return = array();
        foreach ($out->data as $key => $value) {
            $return[$key] = $value;
        }
        return $return;
    }
    return array();
}

function get_prod_oc_shops($paproduct_id, $oc_shops)
{
    $pa_oc_prods = array();
    $checkoc = $wpdb->get_result("select shopid FROM wp_products_meta WHERE meta_key='opencart_id' and product_id=" . $paproduct_id);

    while ($row = mysql_fetch_array($checkoc)) {
        $pa_oc_prods[$row[0]] = $row[0];
    }

    foreach ($oc_shops as $shopid) {
        $ret[$shopid] = (isset($pa_oc_prods[$shopid])) ? TRUE : FALSE;
    }

    foreach ($pa_oc_prods as $shopid) {
        $vr = FALSE;
        foreach ($oc_shops as $oc_shopid) {
            if ($oc_shopid == $shopid) {
                $vr = TRUE;
                break;
            }
        }
        if (!$vr)
            $ret[$shopid] = TRUE;
    }

    return $ret;
}

class opencartUser
{

    private $user_id;
    private $shop;
    private $domain;
    private $token;
    private $status;
    private $shop_id;
    private $version;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->shop = $user_id;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getshop()
    {
        return $this->shop;
    }

    public function Setshop($shop)
    {
        $this->shop = $shop;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setOldId($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function addOCShop()
    {
        global $wpdb;
        if (!empty($this->user_id) && !empty($this->domain) && !empty($this->token) && !empty($this->prefix)) {
            $accepted_status = array(2, 15);
            $accepted_status = implode(",", $accepted_status);
            $wpdb->query("INSERT INTO `wp_users_opencart` (`users_id`,`domain`,`token`, `active`,`status_accepted`,`prefix`,`version`) VALUES ($this->user_id, '$this->domain','$this->token',1,'$accepted_status','$this->prefix','$this->version')");
            return true;
        } else {
            return false;
        }
    }

    public function addOCShop_multi()
    {
        global $wpdb;
        $shop_id = 0;
        if (!empty($this->user_id) && !empty($this->domain) && !empty($this->token) && !empty($this->prefix)) {
            $accepted_status = array(2, 15);
            $accepted_status = implode(",", $accepted_status);

            $shop_id = ($this->shop_id == NULL) ? "NULL" : (int)$this->shop_id;

            $wpdb->query("INSERT INTO `wp_users_opencart` (`id`,`users_id`,`domain`,`token`, `active`,`status_accepted`,`prefix`,`version`) VALUES ($shop_id, $this->user_id, '$this->domain','$this->token',1,'$accepted_status','$this->prefix','$this->version')");
            $shop_id = mysql_insert_id();
        }
        return $shop_id;
    }

    public function isActive($user_id)
    {
        global $wpdb;
        $checkuser = $wpdb->get_var("SELECT `active` FROM `wp_users_opencart` WHERE `users_id` ='$user_id'");

        if ($checkuser == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getOpenCartShop($user_id)
    {
        $shop = array();
        if (!empty($user_id)) {
            $this->user_id = $user_id;
        }
        $shop_information = $wpdb->get_result("SELECT * FROM wp_users_opencart WHERE users_id='$user_id'");
        $opencart = $wpdb->get_row($shop_information);
        if ($opencart) {
            $shop = array(
                'ID' => $opencart[0],
                'user_id' => $opencart[1],
                'domain' => $opencart[2],
                'token' => $opencart[3],
                'created_at' => $opencart[4],
                'updated_at' => $opencart[5],
                'active' => $opencart[6],
                'status' => $opencart[7],
                'prefix' => $opencart[8]
            );
        }
        return $shop;
    }

}

function check_product_existe_opencarte($opencart_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($opencart_id, "opencart_id", $shop_id);
    if ($pa_product_id)
        return array("status" => 200, "data" => true);
    if (!$pa_product_id)
        $pa_product_id = get_product_meta_byfield("product_id", "opencart_id", $opencart_id);
    if ($pa_product_id) {
        $userid_product = $wpdb->get_var("select users_id from wp_users_products where id=$pa_product_id");
        if ($userid_product == $user_id)
            $existe = true;
    }
    return array("status" => 200, "data" => $existe);
}