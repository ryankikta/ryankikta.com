<?php

require("wp-config.php");
global $wpdb;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$product_id = esc_sql($_GET['product_id']);
echo ($product_id);

if (isset($_GET['actiontype']) && $_GET['actiontype'] == 'list_colors') {
    $sql_colors = $wpdb->get_results("SELECT DISTINCT i.*
                                      FROM wp_rmproductmanagement_colors AS i
                                      LEFT JOIN wp_rmproductmanagement_colors_to_products AS p
                                      ON p.color_id = i.color_id 
                                      WHERE p.product_id" = $product_id, ARRAY_A);
    //$result = $wpdb->get_results($sql_color, ARRAY_A); 
    /*$sql_colors = mysql_query("select DISTINCT ic.* FROM " . WP_INVENTORY_COLORS_TABLE . " as ic"
        . " left join " . WP_INVENTORY_COLORS_TO_PRODUCTS . " as ctp"
	. " on ctp.color_id = ic.color_id where ctp.product_id =" . $product_id);
     */
    $html = '';
    if (isset($_GET['colors_selected']))
        $colors_selected = explode(',', $_GET['colors_selected']);
    //while ($color = mysql_fetch_assoc($sql_colors)) {
    while ($color = $sql_colors) {
        $class = (!empty($colors_selected) && in_array($color['color_id'], $colors_selected)) ? 'active' : '';
        $background_color_swatch = ($color['color_swatch'] != '') ? $color['color_swatch'] : $color['color_colour'];
        $html .= '<span id="rmpmmg_color_' . $color['color_id'] . '" class="rmpmmg_color_selector ' . $class . '" style="background-color: #' . $color['color_colour'] . ';" data-color_id="' . $color['color_id'] . '" title="' . $color['color_name'] . '"><span style="height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #' . $background_color_swatch . ';"></span></span>';
    }

    echo json_encode(array('htm' => $html));
    exit();



} else {
    $sizes = array();
    $colors_ids = esc_sql($_GET['colors_ids']);
    $hasfront = (int)$_GET['hasfront'];
    $hasback = (int)$_GET['hasback'];
    $front_id = (int)$_GET['front_id'];
    $back_id = (int)$_GET['back_id'];

    $pricing_group_id = get_user_meta($user_id, 'pricing_group', true);
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

    $mysqlselect = $wpdb->get_result("SELECT wps.size_id, wps.size_name, wps.plus_size_charge, sg.size_name AS size_group 
                                      FROM wp_rmproductmanagement_sizes AS wps 
                                      LEFT JOIN wp_rmproductmanagement_size_groups AS sg
                                      ON wps.size_group_id = sg.size_id", ARRAY_A);

    while ($rows = $mysqlselect) {
        $sizes[$rows['size_id']]['size_name'] = $rows['size_name'];
        $plus_size_charge = plus_size_charge($product_id, $rows['size_id'], $product_pricing);
        $sizes[$rows['size_id']]['plus_size_charge'] = $plus_size_charge;
    }
    $front_is_jumbo = $front_nounderbase = $back_is_jumbo = $back_nounderbase = 0;
    $colorprint = $whiteprint = 0.00;
    if ($front_id != "") {
        $printquery = $wpdb->get_result("select `is_jumbo`,`nounderbase` from `wp_userfiles` where `fileID` in ($front_id)");
        $printrow = $wpdb->get_row($printquery);
        $front_is_jumbo = $printrow[0];
        $front_nounderbase = $printrow[1];
    }
    if ($back_id != "") {
        $printquery = $wpdb->get_result("select `is_jumbo`,`nounderbase` from `wp_userfiles` where `fileID` in ($back_id)");
        $printrow = $wpdb->get_row($printquery);
        $back_is_jumbo = $printrow[0];
        $back_nounderbase = $printrow[1];
    }
    // custom print price
    if (empty($product_pricing)) {
        // default print price
        $print_cost = $wpdb->get_results("select `white_print_price`,`second_side_white_print`,`jumbo_print_price` from `wp_rmproductmanagement_price_groups` where `price_id` in(1,2)");
        $prints = $wpdb->get_results("select printing_option_id,white_first_side,white_seconde_side,color_first_side,color_seconde_side from wp_rmproductmanagement where inventory_id=$product_id");

        $whiteprice = $print_cost[0]->white_print_price;
        $secondwhiteprice = $print_cost[0]->second_side_white_print;
        $jumbowhiteprice = $print_cost[0]->jumbo_print_price;

       // $colorprice = $print_cost[1]->white_print_price;
        //$secondcolorprice = $print_cost[1]->second_side_white_print;
        $jumbocolorprice = $print_cost[1]->jumbo_print_price;
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
        @extract($product_pricing);
        $whiteprice = $print_white_front;
        $secondwhiteprice = $print_white_back;
        $jumbowhiteprice = $print_white_jumbo;

        $colorprice = $print_color_front;
        $secondcolorprice = $print_color_back;
        $jumbocolorprice = $print_color_jumbo;
    }

    if ($hasfront == 1) {
        $whiteprint += $whiteprice;
        $colorprint += ($front_nounderbase == 1) ? $whiteprice : $colorprice;
        if ($front_is_jumbo == 1) {
            $whiteprint += $jumbowhiteprice;
            $colorprint += $jumbocolorprice;
        }
    }
    if ($hasback == 1) {
        $whiteprint += $secondwhiteprice;
        $colorprint += ($back_nounderbase == 1) ? $secondwhiteprice : $secondcolorprice;
        if ($back_is_jumbo == 1) {
            $whiteprint += $jumbowhiteprice;
            $colorprint += $jumbocolorprice;
        }
    }

    $charge_print = charge_print_price($product_id);
    if ($charge_print == 0)
        $whiteprint = $colorprint = 0.00;

    $whiteproductprice = $wpdb->get_var("SELECT `inventory_price` FROM `wp_rmproductmanagement` WHERE `inventory_id` = $product_id ");
    $colorproductprice = $wpdb->get_var("SELECT `color_product_price` FROM `wp_rmproductmanagement` WHERE `inventory_id` = $product_id ");

    $product_price_white = $whiteproductprice + $whiteprint;
    $product_price_color = $colorproductprice + $colorprint;


    $returnarray = array();

    // Availables sizes and colors

    $mysqlselect2 = @$wpdb->get_result("SELECT ctp.color_id,ctp.size_id,wpc.color_name,wpc.color_colour,wpc.color_swatch,wpc.color_group from wp_rmproductmanagement_colors_to_products as ctp left join wp_rmproductmanagement_colors as wpc on ctp.color_id = wpc.color_id where ctp.product_id = $product_id and wpc.color_id in($colors_ids)");

    $count2 = 0;
    while ($rows2 = @mysql_fetch_assoc($mysqlselect2)) {

        $explode = explode(",", $rows2['size_id']);
        $returnarray[$count2]['color_id'] = $rows2['color_id'];
        $returnarray[$count2]['color_name'] = $rows2['color_name'];
        $returnarray[$count2]['html'] = $rows2['color_colour'];
        $returnarray[$count2]['color_swatch'] = $rows2['color_swatch'];
        $returnarray[$count2]['group'] = $rows2['color_group'];
        if ($rows2['color_group'] == "Color") {
            $returnarray[$count2]['print_price'] = $colorprint;
        } else {
            $returnarray[$count2]['print_price'] = $whiteprint;
        }


        $count = 0;
        foreach ($explode as $key => $size_id) {
            if ($sizes[$size_id]['plus_size_charge'] == "0.00") {
                $returnarray[$count2]['sizes1'][$count]['size_id'] = $size_id;
                $returnarray[$count2]['sizes1'][$count]['size_name'] = $sizes[$size_id]['size_name'];
                $returnarray[$count2]['sizes1'][$count]['size_plus_charge'] = $sizes[$size_id]['plus_size_charge'];
                if ($rows2['color_group'] == "Color") {
                    $returnarray[$count2]['sizes1'][$count]['cost_price'] = $product_price_color + $sizes[$size_id]['plus_size_charge'];
                } else {
                    $returnarray[$count2]['sizes1'][$count]['cost_price'] = $product_price_white + $sizes[$size_id]['plus_size_charge'];
                }
            } else {
                $returnarray[$count2]['sizes2'][$count]['size_id'] = $size_id;
                $returnarray[$count2]['sizes2'][$count]['size_name'] = $sizes[$size_id]['size_name'];
                $returnarray[$count2]['sizes2'][$count]['size_plus_charge'] = $sizes[$size_id]['plus_size_charge'];
                if ($rows2['color_group'] == "Color") {
                    $returnarray[$count2]['sizes2'][$count]['cost_price'] = $product_price_color + $sizes[$size_id]['plus_size_charge'];
                } else {
                    $returnarray[$count2]['sizes2'][$count]['cost_price'] = $product_price_white + $sizes[$size_id]['plus_size_charge'];
                }
            }

            $count++;
        }


        $count2++;
    }
    echo json_encode($returnarray);
}
?>
