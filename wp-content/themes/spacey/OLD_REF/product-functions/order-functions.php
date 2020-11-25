<?php
function check_exist_order($order_id, $user_id, $source, $shop = '')
{
    global $wpdb;
    $orders = $wpdb->get_results("select shop from wp_rmproductmanagement_orders where source=$source and external_id='$order_id' and user_id='$user_id'");
    if (!empty($orders)) {
        foreach ($orders as $ord) {
            if ($ord->shop == NULL || rtrim($ord->shop, '/') == rtrim($shop, '/'))
                return true;
        }
    }
    return false;
}

function setting_brand($user_id, $order_rush = 1)
{
    global $wpdb;
    $access_heat_press_tag = get_user_meta($user_id, 'access_heat_press_tag', true);
    $access_pack_in = get_user_meta($user_id, 'access_pack_in', true);
    $access_attach_hang_tag = get_user_meta($user_id, 'access_attach_hang_tag', true);
    $access_custom_packaging = get_user_meta($user_id, 'access_custom_packaging', true);
    $userinfo = $wpdb->get_row("select `user_email`,`removetag`,`applytag`,`material` from `wp_users` where `ID` = '$user_id'", ARRAY_A);
    $attachhangtag = get_user_meta($user_id, 'attach_hang_tag', true);
    $attachhangtag = ($attachhangtag == "") ? 0 : $attachhangtag;
    $rushorder = get_user_meta($user_id, 'rush_order', true);
    $rushorder = ($rushorder == "") ? 0 : $rushorder;
    if ($rushorder == 1 && $order_rush == 0)
        $rushorder = 0;
    $nonrushorder = get_user_meta($user_id, 'always_noncharged_rush', true);
    $nonrushorder = (isset($nonrushorder[0])) ? $nonrushorder[0] : 0;
    if ($nonrushorder == 1 && $rushorder == 1)
        $rushorder = 2;
    $individualbagging = get_user_meta($user_id, 'individual_bagging', true);
    $individualbagging = ($individualbagging == "") ? 0 : $individualbagging;
    $custompackaging = get_user_meta($user_id, 'custom_packaging', true);
    $custompackaging = ($custompackaging == "") ? 0 : $custompackaging;

    $tagremoval = ($userinfo['removetag'] == 0) ? "No" : "Yes";
    $tagapplication = ($userinfo['applytag'] == 0 || $access_heat_press_tag != 1) ? "No" : "Yes";
    $applytag_location = get_user_meta($user_id, 'applytag_location', true);
    $additionalmaterial = ($userinfo['material'] == 0 || $access_pack_in != 1) ? "No" : "Yes";
    $_attach_hang_tag = ($attachhangtag == 1 && $access_attach_hang_tag == 1) ? "Yes" : "No";
    $hang_tag_location = get_user_meta($user_id, 'hang_tag_location', true);

    $_individual_bagging = ($individualbagging == 1) ? "Yes" : "No";
    $_custom_packaging = ($custompackaging == 1 && $access_custom_packaging == 1) ? "Yes" : "No";

    $youremail = $userinfo['user_email'];
    $businessname = get_user_meta($user_id, "business_name", true);
    $businessname = esc_sql($businessname);
    $businesscontact = get_user_meta($user_id, "full_name", true);
    $businesscontact = esc_sql($businesscontact);
    $businesscontact = ($businesscontact == "" || $businesscontact == " ") ? $businessname : $businesscontact;
    $returnlabel = get_user_meta($user_id, "new_option_name", true);
    $packingslipfilename = NULL;
    $instructions = NULL;

    return array('tagremoval' => $tagremoval, 'tagapplication' => $tagapplication, 'applytag_location' => $applytag_location, 'additionalmaterial' => $additionalmaterial,
        '_attach_hang_tag' => $_attach_hang_tag, 'hang_tag_location' => $hang_tag_location, 'rushorder' => $rushorder, '_individual_bagging' => $_individual_bagging, '_custom_packaging' => $_custom_packaging,
        'youremail' => $youremail, 'businessname' => $businessname, 'businesscontact' => $businesscontact, 'returnlabel' => $returnlabel,
        'packingslipfilename' => $packingslipfilename, 'instructions' => $instructions);
}

function insert_order($user_id, $get_brands, $data, $order_id = 0)
{
    @extract($data);
    @extract($get_brands);
    $shops = array(3 => 'shopify_id', 4 => 'storenvy_id', 5 => 'etsy_id', 6 => 'woocommerce_id', 7 => 'bigcommerce_id', 8 => 'opencart_id', 9 => 'ebay_id', 10 => 'ecwid_id', 11 => 'tictail_id', 12 => 'prestashop_id', 13 => 'americommerce_id', 14 => 'gumroad_id', 15 => 'bigcartel_id', 16 => 'lemonstand_id', 17 => 'mockup_tool');
    $now = time();
    $sql = ($order_id != 0) ? "UPDATE" : "INSERT INTO";
    $sql .= " `wp_rmproductmanagement_orders` SET  `businessname`='" . $businessname . "',
                                                `businesscontact`='" . $businesscontact . "',
                                                `youremail`='" . $youremail . "',
                                                `returnlabel`='" . $returnlabel . "',
                                                `orderid`='#" . $shop_order_name . "',
                                                `shippingaddress`='" . esc_sql($shippingaddress) . "',
                                                `shippingaddress1`='" . esc_sql($shippingaddress1) . "',    
                                                `shipping_method`='" . $shipping_id . "',
                                                `customerphone`='" . $customerphone . "',
                                                `tagremoval`='" . $tagremoval . "',
                                                `attach_hang_tag`='" . $_attach_hang_tag . "',
                                                `rush`='" . $rushorder . "',     
                                                `tagapplication`='" . $tagapplication . "',
                                                `additionalmaterial`='" . $additionalmaterial . "',
                                                `individualbagging`='" . $_individual_bagging . "',  
                                                `custompackaging`='" . $_custom_packaging . "',      
                                                `special_instructions`='" . $instructions . "',
                                                `user_id`='" . $user_id . "',
                                                `user_org`='" . $user_id . "',
                                                `packingslip`='" . $packingslipfilename . "',
                                                `status`='ON HOLD',
                                                `shipping_price`='0',
                                                `source`='" . $source . "',
                                                `" . $shops[$source] . "`='" . $shop_order_id . "',
                                                `shop`='" . $shop . "',  
                                                `shop_id`='" . $shop_id . "',     
                                                `external_id`='" . $shop_order_id . "',     
                                                `order_total`='0'";
    if ($order_id == 0)
        $sql .= ",`order_time`='" . $now . "'";
    if ($tagapplication == "Yes")
        $sql .= ",`applytag_location`='" . $applytag_location . "'";
    if ($_attach_hang_tag == "Yes")
        $sql .= ",`hang_tag_location`='" . $hang_tag_location . "'";
    $sql .= ($order_id != 0) ? " where order_id=$order_id" : "";

    wp_mail("rkikta@ryankikta.com", "product-functions/order-functions.php", $order_id);

    $wpdb->get_result($sql);

    if (mysql_error()) {

        $sys_shop = explode('_', $shops[$source]);
        $sys_shop = $sys_shop[0];
        $logs = array();
        $logs['sql'] = $sql;
        $logs['mysql_error'] = mysql_error();
        $logs['data'] = $data;
        $post_data = array(
            'post_date' => date('Y-m-d H:i:s', time()),
            'post_content' => esc_sql(var_export($logs, true)),
            'post_title' => esc_sql('failure to add new ' . $sys_shop . ' order to database for mysql error :  ' . mysql_error() . ' for order ' . $shop_order_id),
            'post_status' => 'draft',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_name' => esc_sql('failure to add new ' . $sys_shop . ' order to database for mysql error :  ' . mysql_error() . ' for order ' . $shop_order_id),
            'post_type' => 'logs'
        );
        wp_insert_post($post_data);
        //  $headers = 'From: Ryan Kikta <team@ryankikta.com>' . "\r\n" .
        //'Reply-To: Ryan Kikta <team@ryankikta.com>' . "\r\n" ;
        wp_mail("team@ryankikta.com", "Error adding an order  to the PA system (" . $sys_shop . ")", 'user_id:' . $user_id . '<br />businessname: ' . $businessname . '<br />' . 'email: ' . $youremail . '<br />data:' . var_export($logs, true) . ' <br /> Error:' . mysql_error(), $headers);
        //wp_mail("team@ryankikta.com", "Error adding an order  to the PA system (".$sys_shop.")",'user_id:'.$user_id.'<br />businessname: '.$businessname.'<br />'.'email: '.$youremail.'<br />data:'.var_export($logs,true).' <br /> Error:'.mysql_error(),$headers);
        return 0;
    }
    if ($order_id != 0)
        $pa_order_id = $order_id;
        //wpmember_new_order_mail($pa_order_id);
    else
        $pa_order_id = mysql_insert_id();
        //wpmember_new_order_mail($pa_order_id);

    $shop_type = explode("_", $shops[$source]);
    $shop_type = $shop_type[0];
    $sql_logs = "INSERT INTO wp_logs_shop_orders SET `order_id`='" . $pa_order_id . "',
                  `title`='" . esc_sql('adding new ' . $shop_type . ' order ' . date('Y-m-d H:i:s', time())) . "',
                  `content`='" . base64_encode(json_encode($json, TRUE)) . "',
                  `source`='" . $source . "',
                  `sql_order`='" . base64_encode($sql) . "',
                  `order_date`='" . date('Y-m-d H:i:s', time()) . "'
                  ";
    $wpdb->get_result($sql_logs);

    return $pa_order_id;
}

function save_additional_cost_order_item($oid, $pricing)
{
    $additional_cost = array();
    if (empty($pricing)) {
        $query = $wpdb->get_result("select `hang_tag_removal`,`tag_application`,`additional_material`,`attach_hang_tag`,`rush`,`individual_bagging`,`custom_packaging` from `wp_rmproductmanagement_additional_settings` where `setting_id` = 1");
        $getadditional = $wpdb->get_row($query);
        $additional_cost['hang_tag_removal'] = $getadditional[0];
        $additional_cost['tag_application'] = $getadditional[1];
        $additional_cost['additional_material'] = $getadditional[2];
        $additional_cost['attach_hang_tag'] = $getadditional[3];
        $additional_cost['rush'] = $getadditional[4];
        $additional_cost['individual_bagging'] = $getadditional[5];
        $additional_cost['custom_packaging'] = $getadditional[6];
    } else {
        @extract($pricing);
        $packins = get_cost_pack_in();
        $additional_cost['hang_tag_removal'] = $tag_removal;
        $additional_cost['tag_application'] = $heat_press_tag;
        $additional_cost['additional_material'] = $packins;
        $additional_cost['attach_hang_tag'] = $attach_hang_tag;
        $additional_cost['individual_bagging'] = $individual_bagging;
        $additional_cost['custom_packaging'] = $custom_packaging;
        $additional_cost['rush'] = $rush;
    }
    $additional_cost = json_encode($additional_cost, true);
    $wpdb->get_result("UPDATE " . WP_INVENTORY_ORDER_DETAILS . " SET `additional_cost_item`='$additional_cost' where oid=$oid");
}

function save_shipping_cost_order_item($shipping_id, $productshippinggroup_id, $oid)
{
    global $wpdb;
    $getshippingprice = $wpdb->get_result("SELECT `first_item_price`,`additional_item_price` FROM `wp_rmproductmanagement_shipping_to_options`
                                      WHERE `shipping_option_id` = $shipping_id AND `shipping_group` = $productshippinggroup_id");
    $getshippingpricerow = $wpdb->get_row($getshippingprice);

    $first_item_price = $getshippingpricerow[0];
    $additional_item_price = $getshippingpricerow[1];
    $data = array('first_item_price' => $first_item_price, 'additional_item_price' => $additional_item_price);
    $shipping_item_prices = json_encode($data, true);
    $wpdb->query("UPDATE " . WP_INVENTORY_ORDER_DETAILS . " SET `shipping_cost_item`='$shipping_item_prices' where oid=$oid");
}

function save_cost_order_item($inventory_id, $oid, $pricing)
{
    global $wpdb;

    if (empty($pricing)) {
        $prices = $wpdb->get_results("select inventory_price,color_product_price from " . WP_INVENTORY_TABLE . " where inventory_id=$inventory_id");
        $white_price = $prices[0]->inventory_price;
        $color_price = $prices[0]->color_product_price;
    } else
        @extract($pricing);
    $item_prices['white_price'] = $white_price;
    $item_prices['color_price'] = $color_price;
    $item_prices = json_encode($item_prices, true);
    $wpdb->query("UPDATE " . WP_INVENTORY_ORDER_DETAILS . " SET `cost_item`='$item_prices' where oid=$oid");
}

function save_printing_cost_order_item($oid, $pricing, $charge_print, $product_id)
{
    global $wpdb;
    if ($charge_print == 1) {
        if (empty($pricing)) {
            $pr = $wpdb->get_results("select price_id,white_print_price,second_side_white_print,jumbo_print_price from " . WP_INVENTORY_PRICES_TABLE);
            $fb_printing = array();
            foreach ($pr as $p) {
                if ($p->price_id == 1) {
                    $fb_printing['white_front_print_price'] = $p->white_print_price;
                    $fb_printing['white_back_print_price'] = $p->second_side_white_print;
                    $fb_printing['white_jumbo_print_price'] = $p->jumbo_print_price;
                } else {
                    $fb_printing['color_front_print_price'] = $p->white_print_price;
                    $fb_printing['color_back_print_price'] = $p->second_side_white_print;
                    $fb_printing['color_jumbo_print_price'] = $p->jumbo_print_price;
                }
            }
            $prints = $wpdb->get_results("select printing_option_id,white_first_side,white_seconde_side,color_first_side,color_seconde_side from wp_rmproductmanagement where inventory_id=$product_id");
            if ($prints[0]->printing_option_id == 2) {
                $fb_printing['white_front_print_price'] = $prints[0]->white_first_side;
                $fb_printing['white_back_print_price'] = $prints[0]->white_seconde_side;

                $fb_printing['color_front_print_price'] = $prints[0]->color_first_side;
                $fb_printing['color_back_print_price'] = $prints[0]->color_seconde_side;
            }
        } else {
            @extract($pricing);
            $fb_printing['white_front_print_price'] = $print_white_front;
            $fb_printing['white_back_print_price'] = $print_white_back;
            $fb_printing['white_jumbo_print_price'] = $print_white_jumbo;
            $fb_printing['color_front_print_price'] = $print_color_front;
            $fb_printing['color_back_print_price'] = $print_color_back;
            $fb_printing['color_jumbo_print_price'] = $print_color_jumbo;
        }
    } else {
        if ($product_id != null) {
            $fb_printing['white_front_print_price'] = 0.00;
            $fb_printing['white_back_print_price'] = 0.00;
            $fb_printing['white_jumbo_print_price'] = 0.00;
            $fb_printing['color_front_print_price'] = 0.00;
            $fb_printing['color_back_print_price'] = 0.00;
            $fb_printing['color_jumbo_print_price'] = 0.00;
        } else {
            $pr = $wpdb->get_results("select price_id,white_print_price,second_side_white_print,jumbo_print_price from " . WP_INVENTORY_PRICES_TABLE);
            $fb_printing = array();
            foreach ($pr as $key => $p) {
                if ($p->price_id == 1) {
                    $fb_printing['white_front_print_price'] = $p->white_print_price;
                    $fb_printing['white_back_print_price'] = $p->second_side_white_print;
                    $fb_printing['white_jumbo_print_price'] = $p->jumbo_print_price;
                } else {
                    $fb_printing['color_front_print_price'] = $p->white_print_price;
                    $fb_printing['color_back_print_price'] = $p->second_side_white_print;
                    $fb_printing['color_jumbo_print_price'] = $p->jumbo_print_price;
                }
            }
        }
    }
    $fb_printing_enc = json_encode($fb_printing, true);
    $wpdb->query("UPDATE " . WP_INVENTORY_ORDER_DETAILS . " SET `printing_fbj_price`='$fb_printing_enc' where oid=$oid");
}


function extract_data($value)
{
    $pa_product_id = $value['pa_product_id'];
    $product_id = $value['product_id'];
    $brand_id = $value['brand_id'];
    $hasfront = $value['hasfront'];
    $hasback = $value['hasback'];
    $size_id = $value['size_id'];
    $color_id = $value['color_id'];
    $quantity = $value['quantity'];
    $variant_id = (isset($value['variant_id'])) ? $value['variant_id'] : 0;
    $shop_item_id = $value['item_id'];
    $shop_item_price = $value['item_price'];
    $printer_id = $value['printer_id'];

    if ($hasfront == 1 && $hasback == 1 && $brand_id == 29)
        $hasback = 0;

    $data = array('pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'size_id' => $size_id, 'color_id' => $color_id, 'quantity' => $quantity, 'shop_item_price' => $shop_item_price, 'shop_item_id' => $shop_item_id, 'variant_id' => $variant_id, 'printer_id' => $printer_id);

    $imagesdetailsquery = $wpdb->get_result("select `image_id`,`type` from `wp_users_products_images` where `users_products_id` = $pa_product_id");
    while ($imagesrows = mysql_fetch_array($imagesdetailsquery)) {

        if (intval($imagesrows[1]) == 1) {
            $frontprint = $imagesrows[0];
            $data['frontprint'] = $frontprint;
        }
        if (intval($imagesrows[1]) == 2) {
            $front = $imagesrows[0];
            $data['front'] = $front;
        }
        if (intval($imagesrows[1]) == 3) {
            $back = $imagesrows[0];
            $data['back'] = $back;
        }
        if (intval($imagesrows[1]) == 5) {
            $backprint = $imagesrows[0];
            $data['backprint'] = $backprint;
        }
    }
    return $data;
}

function get_cost_pack_in()
{
    $query = $wpdb->get_result("select `additional_material` from `wp_rmproductmanagement_additional_settings` where `setting_id` = 1");
    $row = $wpdb->get_row($query);
    $pack_in = $row[0];
    return $pack_in;
}

function calcul_totaltotalprice($pa_product_id, $totaltotalprice, $quantity, $get_user_brands, $additional_product, $pricing)
{
    @extract($get_user_brands);
    @extract($additional_product);
    if (empty($pricing)) {
        $query = $wpdb->get_result("select `hang_tag_removal`,`tag_application`,`attach_hang_tag`,`rush`,`individual_bagging`,`custom_packaging` from `wp_rmproductmanagement_additional_settings` where `setting_id` = 1");
        $getadditional = $wpdb->get_row($query);
        $hang_tag_removal = $getadditional[0];
        $tag_application = $getadditional[1];
        $attach_hang_tag = $getadditional[2];
        $rush_price = $getadditional[3];
        $individual_bagging = $getadditional[4];
        $custom_packaging = $getadditional[5];
    } else {
        @extract($pricing);
        $hang_tag_removal = $tag_removal;
        $tag_application = $heat_press_tag;
        $attach_hang_tag = $attach_hang_tag;
        $rush_price = $rush;
        $individual_bagging = $individual_bagging;
        $custom_packaging = $custom_packaging;
    }

    if ($inv_access_neck_label_removal == 1) {
        if (get_product_meta($pa_product_id, "removetag") != NULL)
            $tagremoval = (get_product_meta($pa_product_id, "removetag") == 1) ? "Yes" : "No";
        if ($tagremoval == "Yes")
            $totaltotalprice += $hang_tag_removal * $quantity;
    }
    if ($inv_access_heat_press_tag == 1) {
        if (get_product_meta($pa_product_id, "applytag") != NULL)
            $tagapplication = (get_product_meta($pa_product_id, "applytag") == 1) ? "Yes" : "No";
        if ($tagapplication == "Yes")
            $totaltotalprice += $tag_application * $quantity;
    }
    if ($inv_access_attach_hang_tag == 1) {
        if (get_product_meta($pa_product_id, "attach_hang_tag") != NULL)
            $_attach_hang_tag = (get_product_meta($pa_product_id, "attach_hang_tag") == 1) ? "Yes" : "No";
        if ($_attach_hang_tag == "Yes")
            $totaltotalprice += $attach_hang_tag * $quantity;
    }
    if ($rushorder == 1)
        $totaltotalprice += $rush_price * $quantity;

    if ($inv_access_individual_bagging == 1) {
        if (get_product_meta($pa_product_id, "individual_bagging") != NULL)
            $_individual_bagging = (get_product_meta($pa_product_id, "individual_bagging") == 1) ? "Yes" : "No";
        if ($_individual_bagging == "Yes")
            $totaltotalprice += $individual_bagging * $quantity;
    }
    if ($_custom_packaging == "Yes")
        $totaltotalprice += $custom_packaging * $quantity;

    return $totaltotalprice;
}

function front_back_data($print, $mockup, $type)
{

    $printquery = $wpdb->get_result("select `fileName`,`is_jumbo`,`nounderbase`,`always_underbase` from `wp_userfiles` where `fileID` = $print");
    $mockupquery = $wpdb->get_result("select `fileName` from `wp_userfiles` where `fileID` = $mockup");

    $printrow = $wpdb->get_row($printquery);
    $mockuprow = $wpdb->get_row($mockupquery);

    $image_print = $printrow[0];
    $is_jumbo = $printrow[1];
    $image_nounderbase = $printrow[2];
    $image_always_use_underbase = $printrow[2];

    $image_mockup = $mockuprow[0];
    return array($type . '_print' => $image_print, $type . '_mockup' => $image_mockup, $type . '_is_jumbo' => $is_jumbo, $type . '_nounderbase' => $image_nounderbase, $type . '_always_use_underbase' => $image_always_use_underbase);
}

function white_calcul_price($color_group, $f_data, $b_data, $hasfront, $hasback, $pricing, $product_id, $use_underbase = 0)
{
    global $wpdb;
    @extract($f_data);
    @extract($b_data);
    if (empty($pricing)) {
        $whitepricemysql = $wpdb->get_result("select `white_print_price`,`second_side_white_print`,`jumbo_print_price` from `wp_rmproductmanagement_price_groups` where `price_id` = 1");

        $whitepricefetch = $wpdb->get_row($whitepricemysql);
        $whiteprice = $whitepricefetch[0];
        $secondwhiteprice = $whitepricefetch[1];
        $jumbowhiteprice = $whitepricefetch[2];

        $colorpricemysql = $wpdb->get_result("select `white_print_price`,`second_side_white_print`,`jumbo_print_price` from `wp_rmproductmanagement_price_groups` where `price_id` = 2");

        $colorpricefetch = $wpdb->get_row($colorpricemysql);
        $colorprice = $colorpricefetch[0];
        $secondcolorprice = $colorpricefetch[1];
        $jumbocolorprice = $colorpricefetch[2];

        $prints = $wpdb->get_results("select printing_option_id,white_first_side,white_seconde_side,color_first_side,color_seconde_side from wp_rmproductmanagement where inventory_id=$product_id");
        if ($prints[0]->printing_option_id == 1) {
            $whiteprice = $prints[0]->white_first_side;
            $secondwhiteprice = $prints[0]->white_seconde_side;

            $colorprice = $prints[0]->color_first_side;
            $secondcolorprice = $prints[0]->color_seconde_side;
            if ($hasfront != 1 || $hasback != 1) {
                $secondwhiteprice = $whiteprice;
                $secondcolorprice = $colorprice;
            }
        }
    } else {
        @extract($pricing);
        $whiteprice = $print_white_front;
        $secondwhiteprice = $print_white_back;
        $jumbowhiteprice = $print_white_jumbo;

        $colorprice = $print_color_front;
        $secondcolorprice = $print_color_back;
        $jumbocolorprice = $print_color_jumbo;
    }

    // Return price using: 
    $totalprice = 0;
    $printprice = 0;
    if ($color_group == "White") {
        if ($hasfront == 1 && $hasback == 1) {
            $totalprice = $whiteprice + $secondwhiteprice;
            $printprice = $whiteprice + $secondwhiteprice;
            if ($front_is_jumbo == 1) {
                $totalprice += $jumbowhiteprice;
                $printprice += $jumbowhiteprice;
            }
            if ($back_is_jumbo == 1) {
                $totalprice += $jumbowhiteprice;
                $printprice += $jumbowhiteprice;
            }
        } else {
            $totalprice = ($hasfront == 1) ? $whiteprice : $secondwhiteprice;
            $printprice = ($hasfront == 1) ? $whiteprice : $secondwhiteprice;
            if ($front_is_jumbo == 1 || $back_is_jumbo == 1) {
                $totalprice += $jumbowhiteprice;
                $printprice += $jumbowhiteprice;
            }
        }
    } else {
        //  if($use_underbase == 1)
        //      $front_nounderbase = $back_nounderbase = 0;

        if ($hasfront == 1 && $hasback == 1) {
            $colorprice = ($front_nounderbase == 1) ? $whiteprice : $colorprice;
            $secondcolorprice = ($back_nounderbase == 1) ? $secondwhiteprice : $secondcolorprice;
            $totalprice = $colorprice + $secondcolorprice;
            $printprice = $colorprice + $secondcolorprice;
            if ($front_is_jumbo == 1) {
                $totalprice += $jumbocolorprice;
                $printprice += $jumbocolorprice;
            }
            if ($back_is_jumbo == 1) {
                $totalprice += $jumbocolorprice;
                $printprice += $jumbocolorprice;
            }
        } else {
            $colorprice = (($front_nounderbase == 1 && $hasfront == 1)) ? $whiteprice : $colorprice;
            $secondcolorprice = ($back_nounderbase == 1 && $hasback == 1) ? $secondwhiteprice : $secondcolorprice;
            $totalprice = ($hasfront == 1) ? $colorprice : $secondcolorprice;
            $printprice = ($hasfront == 1) ? $colorprice : $secondcolorprice;
            if ($front_is_jumbo == 1 || $back_is_jumbo == 1) {
                $printprice += $jumbocolorprice;
                $totalprice += $jumbocolorprice;
            }
        }
    }
    return array('totalprice' => $totalprice, 'printprice' => $printprice);
}

function first_class($user_id)
{
    global $wpdb;
    $firstclass = $wpdb->get_var("select firstclass from wp_users where ID=" . $user_id);
    return $firstclass;
}

function ship_calculation($shipping_id, $productshippinggroup_id, $all_shippings, $quantity)
{

    $getshippingprice = $wpdb->get_result("SELECT `first_item_price`,`additional_item_price` FROM `wp_rmproductmanagement_shipping_to_options`
                                      WHERE `shipping_option_id` = $shipping_id AND `shipping_group` = $productshippinggroup_id");
    $getshippingpricerow = $wpdb->get_row($getshippingprice);

    $first_item_price = $getshippingpricerow[0];
    $additional_item_price = $getshippingpricerow[1];

    $getshippingclass = $wpdb->get_result("select shipping_class from wp_rmproductmanagement_shipping_groups where shipping_id=$productshippinggroup_id");
    $getshippingclassrow = $wpdb->get_row($getshippingclass);
    $shipping_class = $getshippingclassrow[0];

    if (isset($all_shippings[$shipping_class][$productshippinggroup_id]['quantity']))
        $quantity1 = $all_shippings[$shipping_class][$productshippinggroup_id]['quantity'] + $quantity;
    else
        $quantity1 = $quantity;

    $all_shippings[$shipping_class][$productshippinggroup_id]['first_item_price'] = $first_item_price;
    $all_shippings[$shipping_class][$productshippinggroup_id]['additional_item_price'] = $additional_item_price;
    $all_shippings[$shipping_class][$productshippinggroup_id]['quantity'] = $quantity1;
    return array('all_shippings' => $all_shippings, 'quantity1' => $quantity1);
}

function check_product($product_id, $pricing)
{
    $getproductdetails = $wpdb->get_result("select `inventory_price`,`color_product_price`,`brand_id`,`shipping_id` 
                              from `wp_rmproductmanagement` where `inventory_id` = $product_id");
    $productdetailsrow = $wpdb->get_row($getproductdetails);
    $whiteproductprice = $productdetailsrow[0];
    $colorproductprice = $productdetailsrow[1];
    $productbrand_id = $productdetailsrow[2];
    $productshippinggroup_id = $productdetailsrow[3];
    if (!empty($pricing)) {
        @extract($pricing);
        $whiteproductprice = $white_price;
        $colorproductprice = $color_price;
    }
    return array('whiteproductprice' => $whiteproductprice, 'colorproductprice' => $colorproductprice, 'productbrand_id' => $productbrand_id, 'productshippinggroup_id' => $productshippinggroup_id);
}

function check_color_group($color_id)
{

    $getcolordetails = $wpdb->get_result("select `color_group` from `wp_rmproductmanagement_colors` where `color_id` = $color_id");
    $color_group = $wpdb->get_row($getcolordetails);
    $color_group = $color_group[0];
    return $color_group;
}

function check_underbase_setting($inv_access_always_use_underbase, $use_underbase, $pa_product_id, &$f_data, &$b_data)
{

    if ($inv_access_always_use_underbase != 1)
        return;
    $underbase_setting = get_product_meta($pa_product_id, 'underbase_setting');

    switch ($underbase_setting) {
        case 2:
            if (isset($f_data['front_nounderbase']))
                $f_data['front_nounderbase'] = 0;
            if (isset($b_data['back_nounderbase']))
                $b_data['back_nounderbase'] = 0;
            //code to be executed if n=label1;
            break;
        case 1:
            if (is_array($f_data))
                $f_data['front_nounderbase'] = 1;
            if (is_array($b_data))
                $b_data['back_nounderbase'] = 1;
            //code to be executed if n=label2;
            break;
        case 0:

            if ($use_underbase == 1) {
                if (isset($f_data['front_nounderbase']))
                    $f_data['front_nounderbase'] = 0;
                if (isset($b_data['back_nounderbase']))
                    $b_data['back_nounderbase'] = 0;
            } else {
                if (isset($f_data['front_always_use_underbase']) && $f_data['front_always_use_underbase'] == 1)
                    $f_data['front_nounderbase'] = 0;
                if (isset($b_data['back_always_use_underbase']) && $b_data['back_always_use_underbase'] == 1)
                    $b_data['back_nounderbase'] = 0;
            }

            break;


    }


}

function insert_orders_details($allitems, $user_id, $pa_order_id, $orderinfos, $get_user_brands, $source)
{

    global $wpdb;
    @extract($orderinfos);
    $base_shipping_price = 0.00;
    $base_shipping_id = NULL;
    $all_shippings = array();
    $totaltotalprice = 0.00;
    $shops = array(3 => 'shopify_id', 4 => 'storenvy_id', 5 => 'etsy_id', 6 => 'woocommerce_id', 7 => 'bigcommerce_id', 8 => 'opencart_id', 9 => 'ebay_id', 10 => 'ecwid_id', 11 => 'tictail_id', 12 => 'prestashop_id', 13 => 'americommerce_id', '14' => 'gumroad_id', 15 => 'bigcartel_id', 16 => 'lemonstand_id');
    $pricing_group_id = get_user_meta($user_id, 'pricing_group', true);
    $use_underbase = get_user_meta($user_id, 'always_use_underbase', true);
    $use_underbase = ($use_underbase != 1) ? 0 : 1;
    $sql = "delete from wp_rmproductmanagement_order_details where order_id=$pa_order_id";
    $mysql_del = $wpdb->get_result($sql);
    $sql_items = array();
    foreach ($allitems as $key => $value) {

        $data = extract_data($value);
        @extract($data);
        $additional_product = access_product_additional_settings($product_id);
        @extract($additional_product);
        $f_data = array('front_print' => '', 'front_mockup' => '', 'is_jumbo' => 0, 'front_nounderbase' => 0);
        $b_data = array('back_print' => '', 'back_mockup' => '', 'back_is_jumbo' => 0, 'back_nounderbase' => 0);

        if ($hasfront == 1)
            $f_data = front_back_data($frontprint, $front, 'front');

        if ($hasback == 1)
            $b_data = front_back_data($backprint, $back, 'back');
        //  wp_mail('team@ryankikta.com','f data beofr',var_export($f_data,true));
        check_underbase_setting($inv_access_always_underbase, $use_underbase, $pa_product_id, $f_data, $b_data);
        //    wp_mail('team@ryankikta.com','f data after',var_export($f_data,true));
        @extract($f_data);
        @extract($b_data);
        $quantity = intval(esc_sql($quantity));

        // Checking color
        $color_group = check_color_group($color_id);
        // Checking size
        $product_pricing = array();
        $user_product_pricing = $wpdb->get_var("select list_pricing from wp_rmproductmanagement_settings_pricing where user_id=$user_id and product_id=$product_id");
        if ($user_product_pricing)
            $product_pricing = unserialize($user_product_pricing);
        elseif ($pricing_group_id) {
            $group_product_pricing = $wpdb->get_var("select list_pricing from wp_rmproductmanagement_settings_group_pricing where group_id=$pricing_group_id and product_id=$product_id");
            if ($group_product_pricing)
                $product_pricing = unserialize($group_product_pricing);
        }
        if (empty($product_pricing)) {
            $group_product_pricing = $wpdb->get_var("select list_pricing from wp_rmproductmanagement_settings_group_pricing where group_id=1 and product_id=$product_id");
            if ($group_product_pricing)
                $product_pricing = unserialize($group_product_pricing);
        }
        // Check product
        $prod = check_product($product_id, $product_pricing);
        @extract($prod);
        $w_price = white_calcul_price($color_group, $f_data, $b_data, $hasfront, $hasback, $product_pricing, $product_id, $use_underbase);
        extract($w_price);
        $charge_print = charge_print_price($product_id);
        $printprice = ($product_id != null) ? (($charge_print == 1) ? $printprice : 0) : $printprice;
        $totalprice = ($product_id != null) ? (($charge_print == 1) ? $totalprice : 0) : $totalprice;
        if ($color_group == "White")
            $totalprice = $totalprice + $whiteproductprice;
        else
            $totalprice = $totalprice + $colorproductprice;
        $plus_size_price = plus_size_charge($product_id, $size_id, $product_pricing);
        $totalprice = $totalprice + $plus_size_price;
        $inv_access_additional = serialize($additional_product);

        $additional_product_config = pa_product_additional_settings($pa_product_id, $get_user_brands, $additional_product);
        $prod_additional = serialize($additional_product_config);

        // additional items
        $totaltotalprice = calcul_totaltotalprice($pa_product_id, $totaltotalprice, $quantity, $get_user_brands, $additional_product, $product_pricing);

        $totalprice = $totalprice * $quantity;
        $totaltotalprice = $totaltotalprice + $totalprice;

        $is_front_jumbo = ($hasfront == 1 && $front_is_jumbo == 1) ? 1 : 0;
        $is_back_jumbo = ($hasback == 1 && $back_is_jumbo == 1) ? 1 : 0;
        $is_front_underbase = ($hasfront == 1 && $front_nounderbase == 1) ? 1 : 0;
        $is_back_underbase = ($hasback == 1 && $back_nounderbase == 1) ? 1 : 0;

        $printer_id = get_printer_product($user_id, $product_id, $color_id, $size_id);

        $applytag_note = "";
        if ($inv_access_heat_press_tag == 1) {
            $tagapplication = $get_user_brands['tagapplication'];
            if (get_product_meta($pa_product_id, "applytag") != NULL)
                $tagapplication = (get_product_meta($pa_product_id, "applytag") == 1) ? "Yes" : "No";
            if ($tagapplication == "Yes")
                $applytag_note = get_product_meta($pa_product_id, "applytag_note");
        }

        $sql = "INSERT INTO `wp_rmproductmanagement_order_details` 
                          (`oid`, `order_id`, `brand_id`, `product_id`, `color_id`, `size_id`, `plus_size_charge`,`quantity`,`access_additional`,`product_additional`,`applytag_note`,`front_print`, `front_mockup`,`back_print`, `back_mockup`, `cost`, `base_tax`, `status`, `source`, `printer_id`,`shipping_method_id`, `tracking`, `shipped_date`, `printing_price`,`" . $shops[$source] . "`
                          ,`variant_id`,`external_id`,`is_front_jumbo`,`is_front_underbase`,`is_back_jumbo`,`is_back_underbase`)
                          VALUES
                          (NULL, '$pa_order_id', '$brand_id', '$product_id', '$color_id', '$size_id','$plus_size_price', '$quantity','$inv_access_additional','$prod_additional','$applytag_note',
                          '$front_print', '$front_mockup', '$back_print', '$back_mockup', '$totalprice','$shop_item_price',
                          'New', '$source','$printer_id','', '', '', '$printprice','$shop_item_id',$variant_id,'$shop_order_id',$is_front_jumbo,$is_front_underbase,$is_back_jumbo,$is_back_underbase);";

        $mysqladd = $wpdb->get_result($sql);
        $item_id = mysql_insert_id();
        $sql_items[$item_id] = array('oid' => $item_id, 'sql' => $sql);
        if (mysql_error()) {
            wp_mail('team@ryankikta.com', 'error adding order item', var_export(array(mysql_error(), 'sql' => $sql), true));
        }
        save_shipping_cost_order_item($shipping_id, $productshippinggroup_id, $item_id);
        save_cost_order_item($product_id, $item_id, $product_pricing);
        save_printing_cost_order_item($item_id, $product_pricing, $charge_print, $product_id);
        save_additional_cost_order_item($item_id, $product_pricing);
        // Shipping calculation per type
        $ship_calculation = ship_calculation($shipping_id, $productshippinggroup_id, $all_shippings, $quantity);
        @extract($ship_calculation);
        $sql_logs = "UPDATE wp_logs_shop_orders SET 
                  `sql_items`='" . base64_encode(json_encode($sql_items, TRUE)) . "' 
                  where `order_id`='" . $pa_order_id . "'
                  ";
        $wpdb->get_result($sql_logs);
    }

    $printers = $wpdb->get_results("select oid,product_id,color_id,size_id,printer_id from wp_rmproductmanagement_order_details where order_id=$pa_order_id", ARRAY_A);
    printer_route_order($printers);

    if ($get_user_brands['additionalmaterial'] == "Yes")
        $totaltotalprice += get_cost_pack_in();

    $firstclass = get_user_meta($user_id, 'auto_invoice', true);

    if ($firstclass !== 0) {
        $results = file_get_contents(site_url() . "/packingslipgenerator.php?key=Jpr2C4rdhwCwgfASp4Pw&order_id=$pa_order_id");
        $result = json_decode($results, true);
        if ($result['status'] == 'success') {
            $pckg = $result['name'];
            $wpdb->get_result("update `wp_rmproductmanagement_orders` set packingslip='$pckg' where order_id='$pa_order_id'");

        }
    }
    return array('all_shippings' => $all_shippings, 'totaltotalprice' => $totaltotalprice);

}

function get_base_tax($order_id)
{
    global $wpdb;
    $base_tax = 0.00;
    $data = $wpdb->get_results("select quantity,base_tax from wp_rmproductmanagement_order_details where order_id=$order_id");
    foreach ($data as $key => $or) {
        $base_tax += $or->base_tax * $or->quantity;
    }
    return $base_tax;
}

function calculate_newyork_tax($user_id, $order_id, $shippingaddress_country, $shippingaddress_state, $shippingaddress_state_code, $totaltotalprice)
{

    global $wpdb;

    $is_ca_order = 0;
    $ca_rate = 0.00;
    $is_ny_order = 0;
    $ny_rate = 0.00;
    $tax_price = 0.00;
    $cus_from_ny = 0;
    $ca_rate = 8.75;
    $marges = array();

    $items = $wpdb->get_results("select oid,product_id,color_id,size_id,cost,quantity from wp_rmproductmanagement_order_details where order_id = $order_id", ARRAY_A);

    $inventories = $wpdb->get_col("select product_id from wp_rmproductmanagement_order_details where order_id = $order_id");

    $all_marges = $wpdb->get_results("select * from wp_rmproductmanagement_sales_tax where inventory_id in (" . implode(",", $inventories) . ") ", ARRAY_A);
    foreach ($all_marges as $marge) {
        $marges[$marge['inventory_id']][] = $marge;
    }


    $cus_address = get_user_meta($user_id, 'full_address', true);
    $billing_state = get_user_meta($user_id, 'billing_state', true);
    $billing_address = get_user_meta($user_id, 'billing_address', true);
    $ca_license = get_user_meta($user_id, 'ca_license', true);
    /*if ($cus_address == '' && $billing_address=='')
        $cus_from_ny = 1;
    else */
    if (preg_match('/\bNew York\b/i', $cus_address) || preg_match('/\bNew York\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/,New York\b/i', $cus_address) || preg_match('/,New York\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/\bNY\b/i', $cus_address) || preg_match('/\bNY\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/\,NY\b/i', $cus_address) || preg_match('/\,NY\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/\bN\.Y\.\b/i', $cus_address) || preg_match('/\bN\.Y\.\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/\bN\.Y\b/i', $cus_address) || preg_match('/\bN\.Y\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/,\bN\.Y\b/i', $cus_address) || preg_match('/,\bN\.Y\b/i', $billing_address))
        $cus_from_ny = 1;
    else if (preg_match('/\bNew York\b/i', $billing_state))
        $cus_from_ny = 1;
    else if (preg_match('/\bNY\b/i', $billing_state))
        $cus_from_ny = 1;
    else if (preg_match('/\bN\.Y\b/i', $billing_state))
        $cus_from_ny = 1;
    //Get order info 
    if ($cus_from_ny && $ca_license != 1) {
        if (in_array(strtolower($shippingaddress_country), array("us", "united states"))) {

            if (in_array(strtolower($shippingaddress_state), array("ny", "new york")) || strtolower($shippingaddress_state_code) == 'ny') {
                $is_ny_order = 1;

                foreach ($items as $item) {
                    $total = $item['cost'];
                    $range_id = 0;
                    $_rate = 8.5;
                    $base_tax = 0.00;
                    foreach ($marges[$item['product_id']] as $marge) {
                        if ($total >= $marge['min_cost'] && $total < $marge['max_cost']) {
                            $range_id = $marge['id'];
                            $_rate = $marge['tax_rate'];
                            $base_tax = number_format($total * $_rate * 0.01, 2);
                            $tax_price += $base_tax;

                            break;
                        }


                    }
                    if (!$range_id) {
                        $base_tax = number_format($total * $_rate * 0.01, 2);
                        $tax_price += $base_tax;
                    }

                    $wpdb->get_result("update wp_rmproductmanagement_order_details set ny_tax = '$base_tax',ny_rate='$_rate' where oid = " . $item['oid']);

                }


            }
        }
    }

    $wpdb->get_result("update wp_rmproductmanagement_orders set is_ny_tax=$is_ny_order,ny_tax='$tax_price' where order_id=$order_id");
    $totaltotalprice += $tax_price;

    return $totaltotalprice;
}

function check_all_items_supported($order_id, $items)
{
    global $wpdb;
    $items_supported = true;
    foreach ($items as $item) {
        if (isset($item['product_id']) && $item['product_id'] != null) {
            $product_id = $item['product_id'];
            $color_id = $item['color_id'];
            $size_ids = $wpdb->get_var("select size_id from wp_rmproductmanagement_colors_to_products where product_id=$product_id and color_id=$color_id");
            if ($size_ids) {
                $size_id = $item['size_id'];
                if (!in_array($size_id, explode(",", $size_ids))) {
                    $items_supported = false;
                    $wpdb->get_result("update wp_rmproductmanagement_order_details set supported=0 where order_id=$order_id and color_id=$color_id and size_id=$size_id");
                }
            } else {
                $items_supported = false;
                $wpdb->get_result("update wp_rmproductmanagement_order_details set supported=0 where order_id=$order_id and color_id=$color_id");
            }
        }
    }
    return $items_supported;
}

function check_all_items_active($items)
{
    global $wpdb;
    $all_active = true;
    foreach ($items as $item) {
        if (isset($item['product_id']) && $item['product_id'] != null) {
            $product_id = $item['product_id'];
            $pr_status = $wpdb->get_var("select active from wp_rmproductmanagement where inventory_id=$product_id");
            if ($pr_status == 0) {
                $all_active = false;
                break;
            }
        }
    }
    return $all_active;
}

function check_all_products_exist($items)
{
    $all_exist = true;
    foreach ($items as $item) {
        if (!isset($item['product_id']) || (isset($item['product_id']) && $item['product_id'] == null)) {
            $all_exist = false;
            break;
        }
    }
    return $all_exist;
}

function replace_inactive_products($allitems)
{
    global $wpdb;
    foreach ($allitems as $k => $item) {

        if (isset($item['product_id']) && $item['product_id'] != null) {
            $product_id = $item['product_id'];
            $active = $wpdb->get_var("select active from wp_rmproductmanagement where inventory_id=$product_id");
            if ($active == 0) {
                $inventory_replacement_id = $wpdb->get_var("select inventory_replacement_id from wp_rmproductmanagement where inventory_id=$product_id");

                if ($inventory_replacement_id != 0) {
                    $brand_id = $wpdb->get_var("select brand_id from wp_rmproductmanagement where inventory_id=" . $inventory_replacement_id);

                    if ($brand_id != 0) {
                        $size = $wpdb->get_var("select size_id from wp_rmproductmanagement_colors_to_products where product_id = $inventory_replacement_id and color_id =  " . $item['color_id']);

                        if ($size) {
                            $sizes = explode(",", $size);

                            if (in_array($item['size_id'], $sizes)) {

                                $allitems[$k]['product_id'] = $inventory_replacement_id;
                                $allitems[$k]['brand_id'] = $brand_id;
                            }
                        }

                    }


                }


            }
        }

    }


    return $allitems;
}

function auto_payment($user_id, $totaltotalprice, $order_id, $paypal_address, $active_payment = 1)
{
    include_once dirname(__FILE__) .  '/../ref_trans_functions.php';
	
    // get user balance
    $query = $wpdb->get_result("select `balance` from `wp_users` where `ID` = $user_id");
    $balance = $wpdb->get_row($query);
    $balance = $balance[0];
    $plus_balance = 0;
    if ($totaltotalprice > $balance && $active_payment == 0) {
        $diff = $totaltotalprice - $balance;
        $balance = $balance + $diff;
    }

    $debug_arr = array();
    $debug_arr['user_id'] = $user_id;
    $debug_arr['totalprice'] = $totaltotalprice;
    $debug_arr['balance'] = $balance;

    if ($totaltotalprice > $balance && $active_payment == 1) {
        $diff = $totaltotalprice - $balance;
        $debug_arr['diff'] = $diff;
        $payment_status = false;

        $autopayment = get_user_meta($user_id, 'autopayment', true);
        $payment_type = get_user_meta($user_id, 'autopayment_method', true);
        $debug_arr['autopayment'] = $autopayment;
        if ($autopayment && $active_payment == 1) {
            $order_query = $wpdb->get_result("select orderid from wp_rmproductmanagement_orders where order_id=" . $order_id);
            $row = $wpdb->get_row($order_query);
            $your_order_id = $row[0];
            if ($payment_type == 2) {
                $payment_method = 'strip';
                $amount = str_replace(',', '', sanitize_text_field($diff));
                $amount = (int)(str_replace('$', '', $amount) * 100);
                if ($amount < 50) {
                    $plus_balance += (50 - $amount) / 100;
                    $amount = 50;
                }
                $response = stripe_charge_orders($user_id, $amount, "Order: $your_order_id (ref: $order_id)");
                $debug_arr['response'] = $response;
                $ack = $response['status'];
                if ($ack == 'success') {
                    $payment_status = true;
                    $_transactionid = $response['charge_id'];
                    $debug_arr['tranasaction_id'] = $_transactionid;
                }

            } else {
                $payment_method = 'paypal';
                $billingid = get_user_meta($user_id, 'billing_agreement_id', true);
                $debug_arr['billing_id'] = $billingid;
                $paymentType = "Sale";
                $k = 0;
                $debug_arr['paypal_address'] = $paypal_address;
                while ($k < 3 && !$payment_status) {
                    $responseArray = callRefenceTransaction($billingid, $paymentType, $diff, "Order: $your_order_id (ref: $order_id)", $paypal_address);
                    $debug_arr['payment_response'] = $responseArray;
                    $ack = strtoupper($responseArray["ACK"]);
                    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
                        $payment_status = true;
                        $_transactionid = $responseArray['TRANSACTIONID'];
                        $debug_arr['tranasaction_id'] = $_transactionid;
                    }
                    if ($responseArray['L_ERRORCODE0'] == 10736)
                        break;
                    if ($responseArray['L_ERRORCODE0'] == 10729)
                        break;
                    $k++;
                }
                //accept order with unconfirmed shipping address
                if (!$payment_status && ($responseArray['L_ERRORCODE0'] == 10736 || $responseArray['L_ERRORCODE0'] == 10729)) {

                    $responseArray = callRefenceTransaction($billingid, $paymentType, $diff, "Order: $your_order_id (ref: $order_id)");
                    $debug_arr['payment_response'] = $responseArray;
                    $ack = strtoupper($responseArray["ACK"]);
                    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
                        wp_mail('team@ryankikta.com', 'Unconfirmed address payment for order ' . $order_id, 'unconfirmed');
                        $payment_status = true;
                        $_transactionid = $responseArray['TRANSACTIONID'];
                        $debug_arr['tranasaction_id'] = $_transactionid;
                    }
                }
            }

            if (!$payment_status) {
                wp_insert_post(array(
                    'post_date' => date('Y-m-d H:i:s', time()),
                    'post_content' => var_export($debug_arr, true),
                    'post_title' => esc_sql('failure to get automatic payment for order (' . $payment_method . ') ' . $order_id),
                    'post_status' => 'draft',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_name' => sanitize_title(esc_sql('failure to get automatic payment  for order' . $order_id)),
                    'post_type' => 'systems'
                ));
                wp_mail('team@ryankikta.com', 'failure to get automatic payment for order' . $order_id, json_encode($debug_arr, true));
            }
        }

        if ($payment_status) {
            $diff = $diff + $plus_balance;
            $balance = $balance + $diff;
            $wpdb->get_result("UPDATE `wp_users` SET `balance` = '$balance' where `ID` = $user_id");
            $wpdb->get_result("INSERT INTO `wp_transactions` (`id`, `userid`, `payeremail`,`payment_method`,`amount`, `transactionid`,`balance`,`type`, `timestamp`) VALUES (NULL, '$user_id', 'automatic payment billing transaction id : $_transactionid','$payment_method','$diff', '$_transactionid','$balance' , '1', CURRENT_TIMESTAMP);");
        }
    }
    return $balance;
}

function update_order_informations($user_id, $totaltotalprice, $balance, $shippingprice, $pa_order_id, $youremail, $order_id, $all_active = true, $all_exist = true, $all_supported = true)
{
    $wpdb->get_result("UPDATE `wp_rmproductmanagement_orders` SET `shipping_price` = '$shippingprice' , `order_total` = '$totaltotalprice' where `order_id` = $pa_order_id");
    if ($all_active && $all_exist && $all_supported) {
        $comp = (float)($totaltotalprice - $balance);
        if ($comp <= 0.0000001) {
            $payment_type = get_user_meta($user_id, 'autopayment_method', true);
            $payment_method = ($payment_type == 2) ? 'strip' : 'paypal';
            $newbalance = $balance - $totaltotalprice;
            // if user balance is enough, update mysql and add transaction and let order through
            // Update order
            $new_status = 'New';
            $wpdb->get_result("UPDATE `wp_rmproductmanagement_orders` SET `status` = 'New' where `order_id` = $pa_order_id");
            // update balance
            $wpdb->get_result("UPDATE `wp_users` SET `balance` = '$newbalance' where `ID` = $user_id");
            wpmember_new_order_mail($pa_order_id);
            // update transactions
            $query = $wpdb->get_result("INSERT INTO `wp_transactions` (`id`, `userid`, `payeremail`,`payment_method`,`amount`, `transactionid`,`balance`,`type`, `timestamp`) VALUES (NULL, '$user_id', '','$payment_method','$totaltotalprice', '$pa_order_id','$newbalance' , '2', CURRENT_TIMESTAMP);");
        } else {
            $newbalance = $balance;
            $new_status = 'ON HOLD';
            // update order and set status to ON HOLD
            $wpdb->get_result("UPDATE `wp_rmproductmanagement_orders` SET `status` = 'ON HOLD' where `order_id` = $pa_order_id");

            wpmember_new_order_mail($pa_order_id);
            // Make order note
            $wpdb->get_result("INSERT INTO `wp_rmproductmanagement_order_notes` (`note_id`, `order_id`, `user_id`, `changed_time`, `changed_field`) VALUES (NULL, '$pa_order_id', '$user_id', now(), 'Order put ON HOLD due to insufficient Account Balance.');");

            $full_name = get_user_meta($user_id, 'full_name', true);
            $businessname = get_user_meta($user_id, 'business_name', true);
            $client_name = ($full_name == "" || $full_name == " ") ? $businessname : $full_name;
            $explode = explode(" ", $client_name);
            $firstname = $explode[0];
            add_filter('wp_mail_from', 'wpmem_mail_from1');
            add_filter('wp_mail_from_name', 'wpmem_mail_from_name');
            $source = get_source_from_order($pa_order_id);
            wpmember_onhold_order_mail($youremail, $order_id, $firstname, $source);
        }
    } else {
        $newbalance = $balance;
        $new_status = 'Pending';
        $text = ($all_exist) ? (($all_active) ? "not supported" : "inactive") : "deleted";
        $wpdb->get_result("UPDATE `wp_rmproductmanagement_orders` SET `status` = 'Pending' where `order_id` = $pa_order_id");
        $subject = "Order $pa_order_id with $text products";
        $order_url = site_url() . "/wp-admin/admin.php?page=inventory-orders&action=edit&order_id=$pa_order_id";
        $body = "This order ($order_url) comes to the system with $text products";
        $headers = 'Cc: aladin@ryankikta.com' . "\r\n";
        wp_mail('team@ryankikta.com', $subject, $body, $headers);
    }

    if ($user_id == 479 && $new_status != "Pending")
        $wpdb->get_result("UPDATE `wp_rmproductmanagement_orders` SET `status` = 'ON HOLD' where `order_id` = $pa_order_id");

    return array('balance' => $newbalance, 'status' => $new_status);
}

function get_source_from_order($order_id)
{
    global $wpdb;
    $source = $wpdb->get_var("select source from wp_rmproductmanagement_orders where order_id=$order_id");
    return $source;
}

function wpmember_onhold_order_mail($email, $shop_order_id, $firstname, $source = 0)
{
    $shop_order_id = str_replace("#", "", $shop_order_id);
    $headers = 'From:Adam at Ryan Kikta billing <billing@ryankikta.com>' . "\r\n";
    $headers .= 'Reply-To: Ryan Kikta <billing@ryankikta.com>' . "\r\n";
    //$headers .='Cc: team@ryankikta.com' . "\r\n" ;
    //$headers .='X-Mailer: PHP/' . phpversion();
    $bigcommerce_subject = "BigCommerce Order #$shop_order_id - On Hold";
    $bigcommerce_body = "Dear $firstname, \n\nJust a heads up, your account balance is low and so order #$shop_order_id is on hold until a deposit is made. The transaction between you and your customer is completely separate from you and Ryan Kikta. Please make a deposit so we can start to process your order.\n\nYou may deposit funds into your account by visiting BigCommerce https://login.bigcommerce.com/login then going into the Ryan Kikta app and clicking on Billing. \n\nPlease know that you can setup automatic billing for future orders. Doing so will prevent delays in processing of your orders due to lack of payment. \n\n Let us know if you have any questions, \n\n Ryan Kikta Team \n\n\ On Hold: {message:14}";
    $default_subject = "Order #$shop_order_id - On Hold";
    $default_body = "Dear $firstname, \n\n Just a heads up, your account balance is low and so order #$shop_order_id is on hold until a deposit is made. Please make a deposit so we can start to process your order. \n\nYou may deposit funds into your account by visiting http://ryankikta.com/billing/ \n\nLet us know if you have any questions, \n\n Ryan Kikta Team \n\n\ On Hold: {message:15}";
    add_filter('wp_mail_from', 'wpmem_mail_from1');
    $subject = ($source == 7) ? $bigcommerce_subject : $default_subject;
    $body = ($source == 7) ? $bigcommerce_body : $default_body;
    wp_mail($email, $subject, $body, $headers);
}

function cron_onhold_order_mail($email, $liste_orders, $firstname)
{
    $headers = 'From: Ryan Kikta billing <billing@ryankikta.com>' . "\r\n";
    $headers .= 'Reply-To: Ryan Kikta <billing@ryankikta.com>' . "\r\n";
    $subject = "Reminder: Orders On Hold";
    $body = "Hello $firstname, you currently have some orders on payment hold. Please log into your account and either deposit the funds required to continue processing your orders or set up an automatic billing plan with Stripe or Paypal.

Here are the order(s) that are currently on hold:

$liste_orders

Sincerely,

The Ryan Kikta Team";
    wp_mail($email, $subject, $body, $headers);
}

function cron_new_order_mail($email, $liste_orders, $firstname)
{
    $headers = 'From: Ryan Kikta billing <support@ryankikta.com>' . "\r\n";
    $headers .= 'Reply-To: Ryan Kikta <support@ryankikta.com>' . "\r\n";
    $subject = "Reminder: Orders With Issues";
    $body = "Hello $firstname, you currently have some orders that are missing some information. Please log into your account and either enter the missing information to continue processing your orders or email support@ryankikta.com with the missing information so one of our team can do this for you.

Here are the order(s) that currently have an issue:

$liste_orders

Sincerely,

The Ryan Kikta Team";
    wp_mail($email, $subject, $body, $headers);
    wp_mail('hcanaway@ryankikta.com', $subject, $body, $headers);
}


function wpmember_new_order_mail($order_id)
{

    global $wpdb;

    $arr = get_option('wpmembers_email_new_order_seller');
    $arr['body'] = apply_filters('wpmem_email_new_order_seller', $arr['body']);


    $sql1 = "select * from " . WP_INVENTORY_ORDERS . " where order_id=" . $order_id;
    $od_details = $wpdb->get_row($sql1);
    $sql = $wpdb->get_results("select * from " . WP_INVENTORY_ORDER_DETAILS . " where order_id='" . $order_id . "'");

    $user = $wpdb->get_results("select * from $wpdb->users where ID='" . $od_details->user_id . "'");
    $user_login = stripslashes($user[0]->user_login);

    $brands = array();
    $brnd_q = $wpdb->get_results("select brand_id,brand_name from " . WP_INVENTORY_BRANDS_TABLE . " order by brand_id ASC");
    foreach ($brnd_q as $brnd) {
        $brands[$brnd->brand_id] = stripslashes($brnd->brand_name);
    }

    $products = array();
    $prd_q = $wpdb->get_results("select inventory_id,inventory_name,inventory_model from " . WP_INVENTORY_TABLE . " order by inventory_id ASC");
    foreach ($prd_q as $prd) {
        $products[$prd->inventory_id] = stripslashes($prd->inventory_name) . ' - ' . stripslashes($prd->inventory_model);
    }

    $colors = array();
    $clr_q = $wpdb->get_results("select color_id,color_name from " . WP_INVENTORY_COLORS_TABLE . " order by color_id ASC");
    foreach ($clr_q as $clr) {
        $colors[$clr->color_id] = stripslashes($clr->color_name);
    }

    $sizes = array();
    $sz_q = $wpdb->get_results("select size_id,size_name from " . WP_INVENTORY_SIZES_TABLE . " order by size_id ASC");
    foreach ($sz_q as $sz) {
        $sizes[$sz->size_id] = stripslashes($sz->size_name);
    }

    $products_list = '';
    $base_url = get_site_url();
    $userid = $od_details->user_id;
    $cdn_alias = get_user_meta($userid, 'cdn_alias', true);
    foreach ($sql as $sq) {
        // front print
        $image_data = $wpdb->get_results("select cdn_orig,fileName from wp_userfiles where userID=$userid and fileName='$sq->front_print'", ARRAY_A);
        $front_print = ($userid == 1) ? get_download_path($image_data[0], $user_login, $cdn_alias, $base_url) : site_url() . "/download.php?u=" . urlencode(base64_encode($user_login)) . "&f=" . urlencode(base64_encode($sq->front_print)) . "&ftype=" . urlencode(base64_encode("Image")) . "&source=" . base64_encode("Email");
        // front mockup
        $image_data = $wpdb->get_results("select cdn_orig,fileName from wp_userfiles where userID=$userid and fileName='$sq->front_mockup'", ARRAY_A);
        $front_mockup = ($userid == 1) ? get_download_path($image_data[0], $user_login, $cdn_alias, $base_url) : site_url() . "/download.php?u=" . urlencode(base64_encode($user_login)) . "&f=" . urlencode(base64_encode($sq->front_mockup)) . "&ftype=" . urlencode(base64_encode("Image")) . "&source=" . base64_encode("Email");
        // back print
        $image_data = $wpdb->get_results("select cdn_orig,fileName from wp_userfiles where userID=$userid and fileName='$sq->back_print'", ARRAY_A);
        $back_print = ($userid == 1) ? get_download_path($image_data[0], $user_login, $cdn_alias, $base_url) : site_url() . "/download.php?u=" . urlencode(base64_encode($user_login)) . "&f=" . urlencode(base64_encode($sq->back_print)) . "&ftype=" . urlencode(base64_encode("Image")) . "&source=" . base64_encode("Email");
        // back mockup
        $image_data = $wpdb->get_results("select cdn_orig,fileName from wp_userfiles where userID=$userid and fileName='$sq->back_mockup'", ARRAY_A);
        $back_mockup = ($userid == 1) ? get_download_path($image_data[0], $user_login, $cdn_alias, $base_url) : site_url() . "/download.php?u=" . urlencode(base64_encode($user_login)) . "&f=" . urlencode(base64_encode($sq->back_mockup)) . "&ftype=" . urlencode(base64_encode("Image")) . "&source=" . base64_encode("Email");

        $products_list .= "<tr>";
        $products_list .= "<td>" . $brands[$sq->brand_id] . "</td>";
        $products_list .= "<td>" . $products[$sq->product_id] . "</td>";
        $products_list .= "<td>" . $colors[$sq->color_id] . "</td>";
        $products_list .= "<td>" . $sizes[$sq->size_id] . "</td>";
        $products_list .= "<td>" . $sq->quantity . "</td>";
        $products_list .= "<td><a href='" . $front_print . "'>" . $sq->front_print . "</a></td>";
        $products_list .= "<td><a href='" . $front_mockup . "'>" . $sq->front_mockup . "</a></td>";
        $products_list .= "<td><a href='" . $back_print . "'>" . $sq->back_print . "</a></td>";
        $products_list .= "<td><a href='" . $back_mockup . "'>" . $sq->back_mockup . "</a></td>";
        $products_list .= "</tr>";
    }

    $shippings = $wpdb->get_results("select * from " . WP_INVENTORY_SHIPPING_OPTIONS_TABLE . " where shipping_option_id=" . $od_details->shipping_method);
    $packingslip = $wpdb->get_var("select packingslip from wp_rmproductmanagement_orders where order_id=$order_id");
    //$packing_slip=$od_details->packingslip;
    $pack = "";
    if ($packingslip != '') {
        $pack = "<a href='" . site_url() . "/download.php?u=" . base64_encode($user_login) . "&f=" . base64_encode($packingslip) . "&ftype=" . base64_encode("Slip") . "&source=" . base64_encode("Email") . "'>" . $packingslip . "</a>";
    }
    // else $pack=$packing_slip;

    $rt_bus = $od_details->businessname;
    $rt_adr1 = get_user_meta($user[0]->ID, 'rt_address1', true);
    $rt_adr2 = get_user_meta($user[0]->ID, 'rt_address2', true);
    $rt_city = get_user_meta($user[0]->ID, 'rt_city', true);
    $rt_state = get_user_meta($user[0]->ID, 'rt_state', true);
    $rt_zip = get_user_meta($user[0]->ID, 'rt_zip', true);
    if ($rt_adr1 != "" && $rt_zip != "" && $rt_state != "" && $rt_city != "") {
        $rt_adr2 = ($rt_adr2 != "") ? $rt_adr2 . "<br />" : "";
        $return_label = $rt_bus . "<br />" . $rt_adr1 . "<br />" . $rt_adr2 . $rt_city . ", " . $rt_state . " " . $rt_zip;
    } else
        $return_label = $rt_bus . "<br />" . "2 Wurz Ave. " . "<br />Yorkville, NY 13495";

    $shortcd = array('[blogname]', '[username]', '[orderid]', '[businessname]', '[businesscontact]', '[youremail]', '[returnlabel]', '[ShippingMethod]', '[shippingaddress]', '[customerphone]', '[PackingSlip]', '[tagremoval]', '[tagapplication]', '[AddMaterial]', '[AttachTag]', '[SpecialInstructions]', '[ProductsList]', '[IndividualBagging]', '[CustomPckaging]');

    $access_additional = $sq->access_additional;
    $prod_access_additional = $sq->product_additional;
    $cus_cost = unserialize($access_additional);
    $cus_cost_prod = unserialize($prod_access_additional);
    @extract($cus_cost);
    @extract($cus_cost_prod);

    $neck_label_removal = 'Yes';
    if ($inv_access_neck_label_removal == 0)
        $neck_label_removal = 'No';
    else {
        if ($prod_access_additional != "" && isset($prod_neck_label_removal)) {
            if ($prod_neck_label_removal == 0)
                $neck_label_removal = 'No';
        } elseif ($od_details->tagremoval == 'No')
            $neck_label_removal = 'No';
    }

    $heat_press_tag = 'Yes';
    if ($inv_access_heat_press_tag == 0)
        $heat_press_tag = 'No';
    else {
        if ($prod_access_additional != "" && isset($prod_heat_press_tag)) {
            if ($prod_heat_press_tag == 0)
                $heat_press_tag = 'No';
        } elseif ($od_details->tagapplication == 'No')
            $heat_press_tag = 'No';
    }

    $attach_hang_tag = 'Yes';
    if ($inv_access_attach_hang_tag == 0)
        $attach_hang_tag = 'No';
    else {
        if ($prod_access_additional != "" && isset($prod_attach_hang_tag)) {
            if ($prod_attach_hang_tag == 0)
                $attach_hang_tag = 'No';
        } elseif ($od_details->attach_hang_tag == 'No')
            $attach_hang_tag = 'No';
    }

    $individual_bagging = 'Yes';
    if ($inv_access_individual_bagging == 0)
        $individual_bagging = 'No';
    else {
        if ($prod_access_additional != "" && isset($prod_individual_bagging)) {
            if ($prod_individual_bagging == 0)
                $individual_bagging = 'No';
        } elseif ($od_details->individualbagging == 'No')
            $individual_bagging = 'No';
    }

    $access_pack_in = get_user_meta($user[0]->ID, 'access_pack_in', true);
    $access_custom_packaging = get_user_meta($user[0]->ID, 'access_custom_packaging', true);

    $pack_in = ($access_pack_in == 1) ? $od_details->additionalmaterial : 'No';
    $custom_packaging = ($access_custom_packaging == 1) ? $od_details->custompackaging : 'No';

    $replace = array($blogname, $user_login, $od_details->orderid, $od_details->businessname, $od_details->businesscontact, $od_details->youremail, $return_label, $shippings[0]->shipping_option_name, nl2br($od_details->shippingaddress), $od_details->customerphone, $pack, $neck_label_removal, $heat_press_tag, $pack_in, $attach_hang_tag, $od_details->special_instructions, $products_list, $individual_bagging, $custom_packaging);
    $subj = str_replace($shortcd, $replace, $arr['subj']);
    $body = str_replace($shortcd, $replace, $arr['body']);


    //add_filter('wp_mail_from', 'wpmem_mail_from');
    //add_filter('wp_mail_from_name', 'wpmem_mail_from_name');

    $headers = 'Content-type: text/html';
    /* Filter headers */
    //$headers = apply_filters( 'wpmem_email_headers', 'Content-type: text/html' );

    $headers = 'Content-type: text/html' . "\r\n";
    //$headers .= 'To: orders <orders@ryankikta.com>' . "\n";
    $headers .= 'From: Ryan Kikta Team<team@ryankikta.com>' . "\r\n";
    $headers .= 'Reply-To: Ryan Kikta <team@ryankikta.com>' . "\r\n";
    wp_mail($user[0]->user_email, stripslashes($subj), stripslashes($body), $headers);

    $headers = 'Content-type: text/html' . "\r\n";
    //$headers .= 'To: orders <orders@ryankikta.com>' . "\n";
    $headers .= 'From: Ryan Kikta Team <team@ryankikta.com>' . "\n";
    $headers .= 'Reply-To: Ryan Kikta <team@ryankikta.com>' . "\r\n";
    //$headers .= 'Cc: aladin@ryankikta.com' . "\n";
    $to_email = "orders@ryankikta.com";
    $campaign = "";
    if (isset($_GET['campaign']) && $_GET['campaign'] == "api")
        $campaign = " API";
    wp_mail('orders@ryankikta.com', stripslashes($subj . $campaign), stripslashes($body), $headers);
    //wp_mail('team@ryankikta.com', stripslashes($subj . $campaign), stripslashes($body), $headers);
    return "success";
}

if (!function_exists('wpmem_mail_from')) {
    function wpmem_mail_from($email)
    {
        if (get_option('wpmembers_email_wpfrom')) {
            $email = get_option('wpmembers_email_wpfrom');
        }
        return $email;
    }
}

if (!function_exists('wpmem_mail_from1')) {
    function wpmem_mail_from1($email)
    {

        return "billing@ryankikta.com";
    }
}

if (!function_exists('wpmem_mail_from_name')) {
    function wpmem_mail_from_name($name)
    {
        if (get_option('wpmembers_email_wpname')) {
            $name = get_option('wpmembers_email_wpname');
        }
        return $name;
    }
}

function check_empty_shipping($shippingaddress, $user_id, $order_id)
{
    global $wpdb;
    $empty_shipping = false;
    $shippingaddress = unserialize($shippingaddress);
    if ($shippingaddress['clientname'] == "" || !$shippingaddress['address1'] || !$shippingaddress['city'] || !$shippingaddress['zipcode'] || !$shippingaddress['country']) {
        $empty_shipping = true;
        $orderid = $wpdb->get_var("select orderid from wp_rmproductmanagement_orders where order_id=$order_id");
        $body = " Hi there\n\n, your order $orderid is missing some shipping information. Please log onto your Ryan Kikta account and fix it <a href='" . site_url() . "/view-orders/?page=inventory-orders&action=edit&order_id=$order_id'> here </a> .";
        $user_email = $wpdb->get_var("select user_email from wp_users where ID = " . $user_id);

        $headers = 'Content-type: text/html' . "\r\n";
        //$headers .= 'To: orders <orders@ryankikta.com>' . "\n";
        $headers .= 'From: Ryan Kikta Team<team@ryankikta.com>' . "\r\n";
        $headers .= 'Reply-To: Ryan Kikta Team<team@ryankikta.com>' . "\r\n";
        wp_mail($user_email, "Order with incomplete shipping information", $body, $headers);


    }

    return $empty_shipping;
}
