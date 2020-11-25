<?php
require_once('Gumroad/GumroadAPI.php');

function get_all_gumroad_products($user_id, $shop_id = 0)
{
    global $wpdb;

    $gumroad_api = new GumroadAPI();
    $gm_products = array();
    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $user_shopids = $wpdb->get_results("select id from wp_users_gumroad where users_id=$user_id $where order by id asc", ARRAY_A);
    foreach ($user_shopids as $shop) {

        $gumroad_products = $gumroad_api->ListProductsbyUser($shop['id']);
        $page = 2;
        foreach ($gumroad_products['list'] as $gumroad_prd) {
            $pa_product_id = get_product_meta_shop_byfield("product_id", "gumroad_id", $gumroad_prd['id'], $shop_id);
            $all_products[] = array(
                "id" => $gumroad_prd['id'],
                "title" => $gumroad_prd['name'],
                "status" => $gumroad_prd['published'] ? 'Published' : 'Unpublished',
                "url" => $gumroad_prd['short_url'],
                "image" => $gumroad_prd['preview_url'],
                "shop_id" => $shop['id'],
                "imported" => ($pa_product_id == NULL) ? 0 : 1,
                "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
            );
        }

        return $all_products;
        /* while(!empty($page_products)){
             $wc_products = array_merge($wc_products,$page_products);
             $page_products = getWoocommerce_sku($wc_auth, $page);
             $page += 1;
         }*/
    }


}

function get_product_from_gumroad_id($gumroad_id)
{
    global $wpdb;
    return $wpdb->get_var("select product_id from wp_products_meta where meta_key='gumroad_id' and meta_value='$gumroad_id' limit 1");
}

/**
 * Get the gumroad id from user product id
 * @global type $wpdb
 * @param type $user_product_id
 * @return type int gumroad product id
 */
function get_gumroad_product_id($user_product_id)
{
    global $wpdb;

    return $wpdb->get_var("select meta_value from wp_products_meta where meta_key='gumroad_id' and product_id='$user_product_id' limit 1");
}

function save_gumroad_options($POST, $productid, $shop_id, $gumroad_options, $title, $gr_price)
{
    global $wpdb;

    foreach ($gumroad_options as $gumroad_option) {
        echo 'Gumroad Option: ' . $gumroad_option . '<br />';
        $gr_plus_price = $POST['price_' . $gumroad_option] + $gr_price;
        echo ' $gr_plus_price:';
        debug($gr_plus_price);
        $gr_color_id = $POST["color_id_" . $gumroad_option];
        $gr_size_id = $POST["size_id_" . $gumroad_option][0];
        echo ' $gr_color_id:';
        debug($gr_color_id);
        echo ' $gr_size_id:';
        debug($gr_size_id);
        $gr_color_name = $POST["color_name_" . $gumroad_option];
        $gr_size_name = $POST["size_name_" . $gumroad_option];
        echo ' $gr_color_name:';
        debug($gr_color_name);
        echo ' $gr_size_name:';
        debug($gr_size_name);
        $option_sku = str_replace('"', '\"', stripslashes($title)) . "-" . $gr_color_name . "-" . $gr_size_name;
        $variant_id = $wpdb->get_var("select id from `wp_users_products_colors` where color_id = $gr_color_id and size_id = $gr_size_id and sku='$option_sku' ");
        $variant_id = ($variant_id) ? $variant_id : 'NULL';
        $sql = "INSERT INTO `wp_users_products_colors` (`id`,`users_products_id`,`color_id`,`size_id`,`normalprice`,`plusprice`,`sku`) VALUES ($variant_id,$productid,$gr_color_id,$gr_size_id,'$gr_price','$gr_plus_price','$option_sku')"
            . " on duplicate key update users_products_id = values(users_products_id),color_id=values(color_id),size_id=values(size_id),normalprice=values(normalprice),plusprice=values(plusprice),sku=values(sku)"
            . "";
        $wpdb->query($sql);
        $variantid = $wpdb->insert_id;
        //echo 'variant id '.$variantid.'';
        //wp_mail('team@ryankikta.com', 'sql', $sql);
        if ($wpdb->last_error) {
            wp_mail('team@ryankikta.com', 'gumroad add/edit error sql', $sql);
        }
        $all_vars = array();

        $var_meta_id = $wpdb->get_var("select  id from wp_variants_meta where variant_id ='$variantid' and meta_key='gumroad_id' and meta_value='$gumroad_option'");
        $var_meta_id = ($var_meta_id) ? $var_meta_id : 'NULL';
        $_tmp = array('id' => $var_meta_id, 'variant_id' => $variantid, 'gumroad_id' => $gumroad_option);
        $all_vars[] = $_tmp;
        $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
        $_tmp = array();
        foreach ($all_vars as $_var) {
            $_tmp[] = " ({$_var['id']},'$productid','{$_var['variant_id']}','gumroad_id','{$_var['gumroad_id']}','$shop_id') ";

        }
        $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
        $wpdb->query($sql_var);
        //wp_mail('team@ryankikta.com', 'sql var', $sql_var);
        if ($wpdb->last_error) {
            wp_mail('team@ryankikta.com', 'gumroad add/edit error sql', $sql_var);
        }


    }
    $gumroad_id = $_POST['gumroad_id'];
    $wpdb->get_result("update wp_users_products set gumroadactive=1 where id=" . $productid);
    $_id = $wpdb->get_var("select meta_id from wp_products_meta where product_id=$productid and meta_key='gumroad_id' and meta_value=''");
    $_id = ($_id) ? $_id : 'NULL';
    $sql_pr = "insert into wp_products_meta (meta_id,product_id,meta_key,meta_value,shopid) values($_id,$productid,'gumroad_id','$gumroad_id',$shop_id)"
        . " on duplicate key update product_id = values(product_id),meta_key=values(meta_key),meta_value=values(meta_value),shopid=values(shopid)";
    $wpdb->query($sql_pr);
    //  wp_mail('team@ryankikta.com', 'sql_pr', $sql_pr);


}