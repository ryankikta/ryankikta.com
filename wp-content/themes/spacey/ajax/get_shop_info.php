<?php
require("wp-config.php");
require_once(dirname(__FILE__) . '/wp-content/themes/ryankikta/product-functions');

use Bigcommerce\Api\Client as Bigcommerce;

$user_id = $_GET['user_id'];
//list apps for user
if ($_GET['action'] == "list_apps") {
    $all_apps = array();
    $shops = $wpdb->get_results("select id,shop from wp_users_shopify where users_id=$user_id and active=1", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['shop']);
        }
        $all_apps[] = array(
            "id" => "shopify",
            "title" => "Shopify",
            "count" => count($shops),
            "shops" => $apps
        );
    }
    $shops = $wpdb->get_results("select id,shop from wp_users_storenvy where users_id=$user_id", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['shop']);
        }
        $all_apps[] = array(
            "id" => "storenvy",
            "title" => "Storenvy",
            "count" => count($shops),
            "shops" => $apps
        );
    }

    $shops = $wpdb->get_results("select id,shop from wp_users_etsy where users_id=$user_id and active=1", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['shop']);
        }
        $all_apps[] = array(
            "id" => "etsy",
            "title" => "Etsy",
            "count" => count($shops),
            "shops" => $apps
        );
    }

    $shops = $wpdb->get_results("select id,shop from wp_users_woocommerce where users_id=$user_id and active=1", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['shop']);
        }
        $all_apps[] = array(
            "id" => "woocommerce",
            "title" => "Woocommerce",
            "count" => count($shops),
            "shops" => $apps
        );
    }

    $shops = $wpdb->get_results("select id,shop from wp_users_bigcommerce where users_id=$user_id and active=1", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['shop']);
        }
        $all_apps[] = array(
            "id" => "bigcommerce",
            "title" => "Bigcommerce",
            "count" => count($shops),
            "shops" => $apps
        );
    }

    $shops = $wpdb->get_results("select id,prefix,domain from wp_users_opencart where users_id=$user_id and active=1", ARRAY_A);
    if (count($shops) > 0) {
        $apps = array();
        foreach ($shops as $shop) {
            $apps[] = array('shop_id' => $shop['id'], 'shop_url' => $shop['prefix'] . $shop['domain']);
        }
        $all_apps[] = array(
            "id" => "opencart",
            "title" => "OpenCart",
            "count" => count($shops),
            "shops" => $apps
        );
    }

    echo json_encode(array('apps' => $all_apps));
    exit();
}
// shopify
if ($_GET['action'] == "get_list_products_shopify") {
    $auth = getShopifyShop($user_id);
    $pages = (int)(ShopifyApiCall1("GET", "/admin/products/count.json", NULL, $auth[1]) / 50) + 1;
    if ($pages > 5) {
        $pages = 5;
    }

    $all_products = array();
    for ($i = 1; $i <= $pages; $i++) {
        $resp = ShopifyApiCall1("GET", "/admin/products.json?page=" . $i, array('fields' => 'id,title'), $auth[1]);

        foreach ($resp as $product) {
            $all_products[] = array(
                "id" => $product["id"],
                "title" => $product["title"],
            );
        }
    }
    echo json_encode(array('products' => $all_products));
    exit();
}

//Search SC Product
if ($_GET['action'] == "sc_search_prd") {
    $auth = getShopifyShop($user_id);
    $pages = (int)(ShopifyApiCall1("GET", "/admin/products/count.json", NULL, $auth[1]) / 50) + 1;

    $prods = array();
    for ($i = 1; $i <= $pages; $i++) {
        $resp = ShopifyApiCall1("GET", "/admin/products.json?page=" . $i, array('fields' => 'id,title'), $auth[1]);

        foreach ($resp as $product) {
            //if(strtolower($_GET['sc_keyword']) == strtolower($product["title"])){
            if (strpos(strtolower($product["title"]), strtolower($_GET['sc_keyword'])) !== false) {
                $prods[] = array("id" => $product["id"], "title" => $product["title"]);
            }
        }
    }
    echo json_encode(array('products' => $prods));
    exit();
}

// shopify multishop
if ($_GET['action'] == "get_list_shopify_shop") {
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_shopify_test` WHERE `users_id` = $user_id");
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
    echo json_encode($shop_list);
    exit();
}
if ($_GET['action'] == "get_shopify_shop_data") {
    $shop_id = $_GET['shop_id'];
    $is_edit = isset($_GET['is_edit']) ? $_GET['is_edit'] : 0;
    $pa_product_id = isset($_GET['pa_product_id']) ? $_GET['pa_product_id'] : 0;
    $return = array("status" => 400, "error" => "");
    $products = array();
    $shopifyvendor = "";
    $shopifytype = "";
    $shopify_select_collection = "";
    $shopify_select_product = "";
    if (isset($shop_id)) {
        $auth = getShopifyShopbyId($shop_id);
        if ($is_edit == 1) {
            if ($pa_product_id != 0) {
                $shopify_select_product = get_product_meta_shop($pa_product_id, "shopify_id", $shop_id);
                if (isset($shopify_select_product) && ($shopify_select_product != "")) {
                    $shopifyvendor = get_product_meta_shop($pa_product_id, "shopifyvendor", $shop_id);
                    $shopifytype = get_product_meta_shop($pa_product_id, "shopifytype", $shop_id);
                    $shopify_select_collection = get_product_meta_shop($pa_product_id, "shopifycollection", $shop_id);
                } else {
                    $shopify_data = getCurrentShopifyData($pa_product_id);
                    $shopify_select_product = $shopify_data["shopify_id"];
                    $shopifytype = $shopify_data["shopifytype"];
                    $shopifyvendor = $shopify_data["shopifyvendor"];
                    $shopify_select_collection = $shopify_data["collection_id"];
                }
            }
        }
        if ($shopify_select_product == "" || $shopify_select_product == null) {
            //$products = ShopifyApiCall1("GET", '/admin/products.json?', array('page' => 1, 'limit' => 250, 'fields' => 'id,title'), $auth[1]);
	    $products = get_all_shopify_products_by_shop($auth[0]['shop'], $auth[0]['token'], $shop_id, $user_id);
        }
        //wp_mail('team@ryankikta.com','collections auth',var_export( $auth[1],true));

        $collections = ShopifyApiCall1("GET", '/admin/custom_collections.json?limit=250', array('page' => 1, 'limit' => 250, 'fields' => 'id,title'), $auth[1]);
        //$count = ShopifyApiCall1("GET", '/admin/custom_collections/count.json', array(), $auth[1]);

//wp_mail('team@ryankikta.com','collections return ajax',var_export( $count,true));

        $return = array("status" => 200,
            "data" => array(
                "shop" => $auth[0]["shop"],
                'products' => $products,
                'collections' => $collections,
                "shopifyvendor" => stripcslashes($shopifyvendor),
                "shopifytype" => stripcslashes($shopifytype),
                "select_product" => $shopify_select_product,
                "selected_collect" => $shopify_select_collection
            )
        );
    } else {
        $return = array("status" => 400, "error" => "shop not found");
    }

    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "list_products_shopify_by_shop") {
    $product_list = get_all_shopify_products_by_shop($_GET['shop'], $_GET['token'], $_GET['shop_id'], $user_id);
    echo json_encode($product_list);
    exit();
}
if ($_GET['action'] == "check_product_existe_shopify") {
    echo json_encode(check_product_existe_shopify($_GET['shopify_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
// storenvy multi shop
if ($_GET['action'] == "get_list_shops_storenvy") {
    $shop_list = array();
    $shop_list_query = mysql_query("SELECT `shop`,`id`,`active` FROM `wp_users_storenvy` WHERE `users_id` = $user_id");
    $numshops_storenvy = mysql_num_rows($shop_list_query);

    if ($numshops_storenvy == 0) {
        $shop_list[] = array(
            "id" => "0", "value" => "Select Shop"
        );
    } else {
        while ($row = mysql_fetch_assoc($shop_list_query)) {
            $shop_list[] = array(
                "id" => $row["id"], "value" => $row["shop"], "active" => 1 /*$row["active"]*/
            );
        }
    }
    echo json_encode($shop_list);
    exit();
}
if ($_GET['action'] == "get_storenvy_shop_data") {
    $shop_id = $_GET['shop_id'];
    $reload_data = $_GET['reload_data'];
    $is_edit = $_GET['is_edit'];
    $session_data = $_GET['session_data'];
    $product_id = $_GET['product_id'];

    $default_storenvy_on_sale = 0;
    $default_storenvy_preorder = 0;
    $default_storenvycollection = "";
    $default_storenvy_marketplace_id = 0;
    $default_newproduct = 0;
    $default_storenvy_shipping_id = 0;
    $default_storenvy_id = 0;
    $storenvytoken = getStorenvyShopById($shop_id);
    $path = "https://api.storenvy.com/v1/collections.json?access_token=" . $storenvytoken;
    $collections = send_request($path, 'GET');
    $path = "https://api.storenvy.com/v1/shipping_groups.json?access_token=" . $storenvytoken;
    $shippings = send_request($path, 'GET');
    $path = "https://api.storenvy.com/v1/store?access_token=" . $storenvytoken;
    $store = send_request($path, 'GET');
    $storenvy_marketplace = get_option('storenvy_marketplace');
    $products = getStorenvyProducts($storenvytoken);
    if (($reload_data == 1) && (($is_edit == 0) || ($is_edit == 1))) {
        if (!empty($session_data)) {
            extract($session_data);
            $default_storenvy_on_sale = (${"storenvy_on_sale_$shop_id"} == null) ? 0 : 1;
            $default_storenvy_preorder = (${"storenvy_preorder_$shop_id"} == null) ? 0 : 1;
            $default_storenvycollection = ${"storenvycollection_$shop_id"};
            $default_storenvy_marketplace_id = ${"storenvy_marketplace_id_$shop_id"};
            $default_storenvy_shipping_id = ${"storenvy_shipping_id_$shop_id"};
            $default_newproduct = ${"storenvynewproduct_$shop_id"};
            $collects = "";
            if ($default_storenvycollection != null) {
                foreach ($default_storenvycollection as $collect) {
                    $collects .= $collect . ",";
                }
                $default_storenvycollection = rtrim($collects, ",");
            }
        }
    } else if ($is_edit == 1) {
        $current_data = getCurrentStorenvyData_perShop($shop_id, $product_id);
        if ((!isset($current_data["storenvy_id"])) || ($current_data["storenvy_id"] == "") || ($current_data["storenvy_id"] == NULL)) {
            $current_data = getCurrentStorenvyData($product_id);
        }
        @extract($current_data);
        $default_storenvy_on_sale = $storenvy_on_sale;
        $default_storenvy_preorder = $storenvy_preorder;
        $default_storenvycollection = $storenvycollection;
        $default_storenvy_marketplace_id = $storenvy_marketplace_id;
        $default_storenvy_shipping_id = $storenvy_shipping_id;
        $default_storenvy_id = $storenvy_id;
    }


    $return = array(
        'collections' => $collections['data']['collections'],
        'shipping_groups' => $shippings['data']['shipping_groups'],
        'products' => $products,
        'store' => $store['data']['name'],
        'categories' => $storenvy_marketplace,
        'selected' => array(
            'storenvy_on_sale' => $default_storenvy_on_sale,
            'storenvy_preorder' => $default_storenvy_preorder,
            'storenvycollection' => $default_storenvycollection,
            'storenvy_marketplace_id' => $default_storenvy_marketplace_id,
            'newproduct' => $default_newproduct,
            'storenvy_shipping_id' => $default_storenvy_shipping_id,
            'storenvy_id' => $default_storenvy_id
        )
    );
    echo json_encode($return, true);
    exit();
}
if ($_GET['action'] == "check_product_existe_storenvy") {
    echo json_encode(check_product_existe_storenvy($_GET['storenvy_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
// etsy multi shop
if ($_GET['action'] == "get_etsy_shop_data") {
    //if($user_id == 479)
    //  $user_id = 83;
    $start = microtime(true);
    $shop_id = $_GET['shop_id'];
    $is_edit = isset($_GET['is_edit']) ? $_GET['is_edit'] : 0;
    $pa_product_id = isset($_GET['pa_product_id']) ? $_GET['pa_product_id'] : 0;
    $return = array("status" => 400, "error" => "");
    $products = array();
    $shippings = array();
    $categories = array();
    $sections = array();
    if (isset($shop_id)) {
        $etsy_auth = getEtsyShopById($user_id, $shop_id);

        $products = getListActiveProdctsByShop($etsy_auth);
        if (!isset($products) || (empty($products))) {
            $products = array();
        }
        $shippings = GetEtsyShippingTemplate($etsy_auth);
        //wp_mail('team@ryankikta.com','etsy shipping',var_export($shippings,true));
        if (empty($shippings)) {
            $shippings_template = CreateShippingTemplate($etsy_auth, "US", "5.75", "0.5");
            if ($shippings_template["status"] == "success") {
                $shipping_template_id = $shippings_template["response"]["results"][0]["shipping_template_id"];
                CreateShippingTemplateEntry($etsy_auth, $shipping_template_id, "CA", "8", "0.5");
                $shippings = GetEtsyShippingTemplate($etsy_auth);
            }
        }

        if (!isset($shippings) || empty($shippings)) {
            $shippings = array();
        }
        $categories = GetEtsyCategory($etsy_auth);
        if (!isset($categories) || (empty($categories))) {
            $categories = array();
        }
        $sections = GetEtsySection($etsy_auth);
        if (!isset($sections) || (empty($sections))) {
            $sections = array();
        }
        if ($is_edit == 1) {
            $etsy_id = get_product_meta_shop($pa_product_id, 'etsy_id', $shop_id);
            if ((!isset($etsy_id)) || ($etsy_id == "") || ($etsy_id == NULL)) {
                $shop_id = 0;
                $etsy_id = get_product_meta_shop($pa_product_id, 'etsy_id', $shop_id);
            }
            $etsy_category_id = get_product_meta_shop($pa_product_id, 'etsy_category_id', $shop_id);
            $etsy_sub1_category_id = get_product_meta_shop($pa_product_id, 'etsy_sub1_category_id', $shop_id);
            $etsy_sub2_category_id = get_product_meta_shop($pa_product_id, 'etsy_sub2_category_id', $shop_id);
            $etsy_sub3_category_id = get_product_meta_shop($pa_product_id, 'etsy_sub3_category_id', $shop_id);
            $etsy_section_id = get_product_meta_shop($pa_product_id, 'etsy_section_id', $shop_id);
            $etsy_shipping_id = get_product_meta_shop($pa_product_id, 'etsy_shipping_id', $shop_id);
            $occasion = get_product_meta_shop($pa_product_id, 'occasion', $shop_id);
            $etsy_style = get_product_meta_shop($pa_product_id, 'etsy_style', $shop_id);
            $etsy_recipient = get_product_meta_shop($pa_product_id, 'etsy_recipient', $shop_id);
            $etsymaterials = get_product_meta_shop($pa_product_id, 'etsymaterials', $shop_id);
        } else {
            $etsy_id = null;
            $etsy_category_id = null;
            $etsy_sub1_category_id = null;
            $etsy_sub2_category_id = null;
            $etsy_sub3_category_id = null;
            $etsy_section_id = null;
            $etsy_shipping_id = null;
            $occasion = null;
            $etsy_style = null;
            $etsy_recipient = null;
        }
        $return = array("status" => 200,
            "shop" => $etsy_auth,
            'shippings_template' => $shippings,
            'cats' => $categories,
            'sections' => $sections,
            "products" => $products,
            "etsy_data" => array(
                "etsy_id" => $etsy_id,
                "etsy_category_id" => $etsy_category_id,
                "etsy_sub1_category_id" => $etsy_sub1_category_id,
                "etsy_sub2_category_id" => $etsy_sub2_category_id,
                "etsy_sub3_category_id" => $etsy_sub3_category_id,
                "etsy_section_id" => $etsy_section_id,
                "etsy_shipping_id" => $etsy_shipping_id,
                "occasion" => $occasion,
                "etsy_style" => stripcslashes($etsy_style),
                "etsy_recipient" => $etsy_recipient,
                "etsymaterials" => stripcslashes($etsymaterials)
            )
        );
    } else {
        $return = array("status" => 400, "error" => "shop not found");
    }
    $end = microtime(true) - $start;
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_etsy_categories_by_shop") {
    $shop_id = $_GET['shop_id'];
    $is_edit = isset($_GET['is_edit']) ? $_GET['is_edit'] : 0;
    $pa_product_id = isset($_GET['pa_product_id']) ? $_GET['pa_product_id'] : 0;
    $level = isset($_GET['level']) ? $_GET['level'] : 0;
    $parents_id = isset($_GET['parents_id']) ? explode(",", $_GET['parents_id']) : array();

    $return = array("status" => 400, "error" => "");
    $categories = array();

    if (isset($shop_id)) {

        $etsy_auth = getEtsyShopById($user_id, $shop_id);

        $categories = GetEtsyCategory($etsy_auth, $level, $parents_id);
        if (!isset($categories) || $categories == null) {
            $categories = array();
        }
        $return = array("status" => 200,
            "shop" => $etsy_auth,
            'categories' => $categories,
        );
    } else {
        $return = array("status" => 400, "error" => "shop not found");
    }

    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "list_products_etsy_by_shop") {
    $auth = getEtsyShopById($user_id, $_GET['shop_id']);
    @extract($auth);
    $shop = EtsyApiCall($auth, "https://openapi.etsy.com/v2/shops/" . $etsyshop, NULL, OAUTH_HTTP_METHOD_GET);
    if (isset($shop['status']) && $shop['status'] == "failed") {
        echo json_encode($shop);
        exit();
    }

    $product_list = get_all_etsy_products_by_shop($_GET['shop_id'], $user_id);
    echo json_encode($product_list);
    exit();
}
// woocommerce multi shop
if ($_GET['action'] == "get_list_woocommerce_shop") {
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_shopify_test` WHERE `users_id` = $user_id");
    $numshops_shopifys = $wpdb->num_rows($shop_list_query);

    if ($numshops_wooc == 0) {
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
    echo json_encode($shop_list);
    exit();
}
if ($_GET['action'] == "get_list_products_woocommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $version = getWooPAVersionByshop($shop_id);
    $wc_auth = getWoocommerceShopbyId($shop_id);
    if ($version == 1) {
        @extract($wc_auth);
        $products = getWooDropdownproducts($woocommerceshop, $woocommercetoken);
    } else {
        $products = getProductsList($wc_auth, array('filter[limit]' => '50', 'filter[post_status]' => 'all', 'fields' => 'id,title'));
    }
    $return = array('products' => $products);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_woocommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $wc_auth = getWoocommerceShopbyId($shop_id);
    create_shipping_zones_woocommerce($wc_auth);
    @extract($wc_auth);
    $shippings = getWooShipping($wc_auth);
    $wc_cats = explode(",", $_GET['cats_id']);
    $wooc_cats = wc_categories($wc_auth, $wc_cats, $shop_id);
    $return = array('shop' => $woocommerceshop, 'shippings' => $shippings, 'cats' => $wooc_cats);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_selected_data_woocommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $field = $_GET['field'];
    $type = $_GET['type'];

    if ($type == 1) {
        $prod_id = (int)$_GET['product_data'];
        if ($field == "cats") {
            $data = get_product_meta_shop($prod_id, "woocommerce_cat_id", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = $wpdb->get_var("SELECT woocommerce_cat_id FROM `wp_users_products` WHERE `id` = $prod_id");
            }
        }
        if ($field == "shipping") {
            $data = get_product_meta_shop($prod_id, "woocommerce_shipping_id", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = $wpdb->get_var("SELECT woocommerce_shipping_id FROM `wp_users_products` WHERE `id` = $prod_id");
            }
        }

        if ($field == "shortdesc") {
            $data = get_product_meta_shop($prod_id, "woocommerce_shortdesc", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = $wpdb->get_var("SELECT woocommerce_shortdesc FROM `wp_users_products` WHERE `id` = $prod_id");
            }
            $data = base64_decode($data);
            //$data = '';
        }
    }
    if ($type == 2) {
        $session_data = json_decode(base64_decode($_GET['product_data']), true);
        @extract($session_data);
        if ($field == "cats") {
            $woocommercecategory_id = "";
            $wooc_cats = ${'woocommercecategory' . $shop_id};
            foreach ($wooc_cats as $cat_id) {
                $woocommercecategory_id .= $cat_id . ",";
            }
            $data = rtrim($woocommercecategory_id);
        }
        if ($field == "shipping") {
            $data = ${'woocommerceshippingid' . $shop_id};
        }
        if ($field == "shortdesc") {
            $data = ${'woocommerce_shortdesc' . $shop_id};
        }
        if ($field == "product") {
            $data = ${'woocommercenewproduct' . $shop_id};
        }
    }
    if ($data == null)
        $data = "";
    $data = stripcslashes($data);

    $return = array('data' => $data);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "check_product_existe_woocommerce") {
    echo json_encode(check_product_existe_woocommerce($_GET['woocommerce_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
// bigcommerce multi shop
if ($_GET['action'] == "get_list_bigcommerce_shop") {
    $shop_list = array();
    $shop_list_query = mysql_query("select `shop`,`id`,`active` from `wp_users_bigcommerce` where `users_id` = $user_id");
    $numshops_bigc = mysql_num_rows($shop_list_query);

    if ($numshops_bigc == 0) {
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
    echo json_encode($shop_list);
    exit();
}
if ($_GET['action'] == "get_list_products_bigcommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $big_auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($big_auth);
    $products = Bigcommerce::getProducts();
    $prods = array();
    foreach ($products as $prod) {
        $prod_id = $prod->id;
        $prod_name = $prod->name;
        $prods[$prod_id] = $prod_name;
    }
    $return = array('products' => $prods);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_bigcommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $big_auth = getBigcommerceShopbyId($shop_id);
    bigcommerce_connect($big_auth);
    @extract($big_auth);
    $big_cats = Bigcommerce::getCategories();
    $cats = array();
    foreach ($big_cats as $category) {
        $cat_id = $category['id'];
        $cat_name = $category['name'];
        $cats[$cat_id] = $cat_name;
        $count = count($category);
        if ($count == 3) {
            foreach ($category['child'] as $cat_child) {
                $cat_child_name = $cat_child['name'];
                $cat_child_id = $cat_child['id'];
                $cats[$cat_child_id] = $cat_name . "-->" . $cat_child_name;
            }
        }
    }
    $brands = Bigcommerce::getBrands();
    $all_brands = array();
    foreach ($brands as $brand) {
        $all_brands[$brand->id] = $brand->name;
    }
    asort($all_brands);
    $return = array('shop' => $storename, 'brands' => $all_brands, 'cats' => $cats);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_selected_data_bigcommerce_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $field = $_GET['field'];
    $type = $_GET['type'];

<<<<<<< HEAD
    if ($type == 1) {
        $prod_id = (int)$_GET['product_data'];
        if ($field == "featured_product") {
            $data = get_product_meta_shop($prod_id, "bigcommerce_featured", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = get_product_meta_shop($prod_id, "bigcommerce_featured", 0);
            }
        }


        if ($field == "brands") {
            $data = get_product_meta_shop($prod_id, "bigcommerce_brand", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = get_product_meta_shop($prod_id, "bigcommerce_brand", 0);
            }
        }


        if ($field == "cats") {
            $data = get_product_meta_shop($prod_id, "bigcommerce_cat", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = get_product_meta_shop($prod_id, "bigcommerce_cat", 0);
=======
        if ($is_edit == 1) {
            $selectproductquery = $wpdb->get_result("SELECT collection_id FROM `wp_users_products_shopify` WHERE `shopid` = $shop_id");
            $numshopsshopify = $wpdb->num_rows($selectproductquery);

            if ($numshopsshopify != 0) {
                $shoprow = $wpdb->get_row($selectproductquery);
                $selected_collect = $shoprow[0];
>>>>>>> d334c5267ff02059d98b8813e2866f71f27e98fb
            }
        }


        if ($field == "shortdesc") {
            $data = get_product_meta_shop($prod_id, "bigcommerce_warranty", $shop_id);
            if ((!isset($data)) || ($data == "") || ($data == NULL)) {
                $data = get_product_meta_shop($prod_id, "bigcommerce_warranty", 0);
            }
        }
    }
    if ($type == 2) {
        $session_data = json_decode(base64_decode($_GET['product_data']), true);
        @extract($session_data);
        if ($field == "featured_product")
            $data = ${'bigcommercefeaturedproduct' . $shop_id};

        if ($field == "brands")
            $data = ${'bigcommercebrand' . $shop_id};

        if ($field == "cats")
            $data = ${'bigcommercecategory' . $shop_id};

        if ($field == "shortdesc")
            $data = ${'bigcommerceinfo' . $shop_id};

        if ($field == "product")
            $data = ${'bigcommercenewproduct' . $shop_id};
    }
    if ($data == null)
        $data = "";
    $data = stripcslashes($data);
    $return = array('data' => $data);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "check_product_existe_bigcommerce") {
    echo json_encode(check_product_existe_bigcommerce($_GET['bigcommerce_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
// Opencart MultiShops
if ($_GET['action'] == "get_list_opencart_shop") {
    $shop_list = array();
    $shop_list_query = mysql_query("select `domain`,`id`,`active` from `wp_users_opencart` where `users_id` = $user_id");

    if (mysql_num_rows($shop_list_query) == 0)
        $shop_list[] = array("id" => "0", "value" => "Select Shop");
    else {
        while ($row = mysql_fetch_assoc($shop_list_query)) {
            $shop_list[] = array("id" => $row["id"], "value" => $row["domain"], "active" => $row["active"]);
        }
    }
    echo json_encode($shop_list);
    exit();
}
if ($_GET['action'] == "get_list_products_opencart_by_shop") {
    $shop_id = (int)$_GET['shop_id'];

    $oc_auth = getOpenCartShop($user_id, $shop_id);
    $products = OCGetProducts($oc_auth);
    $return = array('products' => ($products->data == NULL) ? array() : $products->data);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_opencart_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $oc_auth = getOpenCartShop($user_id, $shop_id);

    $oc_cats = oc_categories_desply_paths($oc_auth);
    $categs = '<select name="opencartCategory' . $shop_id . '" id="opencartcategory' . $shop_id . '">';
    $categs .= '<option value"0">None</option>';
    foreach ($oc_cats as $key => $ctg) {
        $selected = ($key == $_GET['cats_id']) ? "selected" : "";
        $categs .= '<option value="' . $key . '" ' . $selected . '>' . $ctg . '</option>';
    }
    $categs .= "</select>";

    $manufs = '<select class="oc_manufs" data-shop_id="' . $shop_id . '" name = "manufacturers' . $shop_id . '" id="opencartmanuf' . $shop_id . '">';
    $manufs .= '<option value="0">None</option>';
    $oc_manufs = OCManufacturers($oc_auth);

    if ($oc_manufs != NULL) {
        foreach ($oc_manufs as $value) {
            $selected = ($value->manufacturer_id == $_GET['manuf_id']) ? "selected" : "";
            $manufs .= '<option value="' . $value->manufacturer_id . '" ' . $selected . '>' . $value->name . '</option>';
        }
    }
    $manufs .= '</select>';

    $return = array('shop' => $oc_auth["opencart_domain"], 'manufs' => $manufs, 'cats' => $categs);

    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_selected_data_opencart_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $field = $_GET['field'];
    $type = $_GET['type'];
    $prod_id = ($type == 1) ? (int)$_GET['product_data'] : 0;

    if ($type == 1) {
        switch ($field) {
            case "product":
                {
                    $data = get_product_meta_shop($prod_id, "opencart_id", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'opencart_id', 0);
                    }
                }
                break;
            case "category":
                {
                    $data = get_product_meta_shop($prod_id, "opencartCategory", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'opencartCategory', 0);
                    }
                }
                break;
            case "model":
                {
                    $data = get_product_meta_shop($prod_id, "opencartModel", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'opencartModel', 0);
                    }
                }
                break;
            case "manuf":
                {
                    $data = get_product_meta_shop($prod_id, "manufactureur", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'manufactureur', 0);
                    }
                }
                break;
            case "meta_title":
                {
                    $data = get_product_meta_shop($prod_id, "meta_tags", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'meta_tags', 0);
                    }
                }
                break;
            case "meta_key":
                {
                    $data = get_product_meta_shop($prod_id, "meta_tag_keywords", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'meta_tag_keywords', 0);
                    }
                }
                break;
            case "meta_desc":
                {
                    $data = get_product_meta_shop($prod_id, "meta_tag_description", $shop_id);
                    if ((!isset($data)) || ($data == NULL) || ($data == "")) {
                        $data = get_product_meta_shop($prod_id, 'meta_tag_description', 0);
                    }
                }
                break;
            default:
                $data = "";
                break;
        }
    } else {
        $session_data = json_decode(base64_decode($_GET['product_data']), true);
        @extract($session_data);

        switch ($field) {
            case "product":
                $data = ${'opencartnewproduct' . $shop_id};
                break;
            case "category":
                $data = ${'opencartCategory' . $shop_id};
                break;
            case "model":
                $data = ${'opencartModel' . $shop_id};
                break;
            case "manuf":
                $data = ${'manufacturers' . $shop_id};
                break;
            case "manuf_name":
                $data = ${'manuf_name' . $shop_id};
                break;
            case "manuf_id":
                $data = ${'manuf_id' . $shop_id};
                break;
            case "meta_title":
                $data = ${'meta_tags' . $shop_id};
                break;
            case "meta_key":
                $data = ${'meta_tag_keywords' . $shop_id};
                break;
            case "meta_desc":
                $data = ${'meta_tag_description' . $shop_id};
                break;
            default:
                $data = "";
                break;
        }
    }
    echo json_encode(array('data' => ($data == NULL) ? "" : stripcslashes($data)));
    exit();
}
if ($_GET['action'] == "check_product_existe_opencarte") {
    echo json_encode(check_product_existe_opencarte($_GET['opencarte_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
//ebay
if ($_GET['action'] == "get_list_products_ebay") {
    $response = getListProductEbay($user_id, $_GET['page']);
    echo json_encode($response);
    exit();
}
if ($_GET['action'] == "get_list_categories_ebay") {
    $response = getListCategoryEbayShop($user_id, $_GET['categoryID'], $_GET['level']);
    echo json_encode($response);
    exit();
}
if ($_GET['action'] == "get_ebay_setting_store") {
    $response = GetUserEbaySettingProfile($user_id, "SHIPPING");
    echo json_encode($response);
    exit();
}

//amazon

if ($_GET['action'] == "get_list_amazon_shop_marketplace") {
    $return = getListMarketplaceForShop($user_id);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_products_amazon") {
    $marketplace = $_GET['marketplace'];
    $return = getListProductAmazonShop($user_id, $marketplace);
    echo json_encode($return);
    exit();
}

// Etsy
if ($_GET['action'] == "get_list_products_etsy") {
    $etsy_auth = getEtsyShop($user_id);
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
            if ($offset > $total)
                $continue = false;
            foreach ($result['response']['results'] as $pr) {
                $listing_id = $pr['listing_id'];
                $listing_name = $pr['title'];
                $products[] = array('id' => $listing_id, 'title' => $listing_name);
            }
        } else {
            $continue = false;
        }
    }
    $return = array('products' => $products);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_etsy") {
    $etsy_auth = getEtsyShop($user_id);
    $shippings = GetEtsyShippingTemplate($etsy_auth);
    $categories = GetEtsyCategory($etsy_auth);
    $sections = GetEtsySection($etsy_auth);
    $return = array('shippings_template' => $shippings, 'cats' => $categories, 'sections' => $sections);
    echo json_encode($return);
    exit();
}
/***New call etsy function***/
if ($_GET['action'] == "get_list_etsy_shop") {
    echo json_encode(ListShopsByUserId($user_id));
    exit();
}
if ($_GET['action'] == "get_list_products_etsy_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $etsy_auth = getEtsyShopById($user_id, $shop_id);
    @extract($etsy_auth);
    $continue = true;
    $offset = 0;
    $limit = 250;
    $products = array();
    while ($continue) {
        $params = array('limit' => $limit, "offset" => $offset);
        $url = "https://openapi.etsy.com/v2/shops/$etsyshop/listings/active";
        $result = EtsyApiCall($etsy_auth, $url, $params, OAUTH_HTTP_METHOD_GET);
        $arr = array("auth" => $etsy_auth, "url" => $url, "products" => $result);
        //mail("team@ryankikta.com","etsy call api products",var_export($arr,true));
        if ($result['status'] == "success") {
            $total = $result['response']['count'];
            $offset += 50;
            if ($offset > $total)
                $continue = false;
            foreach ($result['response']['results'] as $pr)
                $products[$pr['listing_id']] = $pr['title'];
        } else {
            $continue = false;
        }
    }
    $return = array('products' => $products);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_etsy_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $etsy_auth = getEtsyShopById($user_id, $shop_id);
    @extract($etsy_auth);
    $sections = GetEtsySection($etsy_auth);
    $shippings = GetEtsyShippingTemplate($etsy_auth);
    if (empty($shippings)) {
        $shippings_template = CreateShippingTemplate($etsy_auth, "US", "5.75", "0.5");
        if ($shippings_template["status"] == "success") {
            $shipping_template_id = $shippings_template["response"]["results"][0]["shipping_template_id"];
            CreateShippingTemplateEntry($etsy_auth, $shipping_template_id, "CA", "8", "0.5");
            $shippings = GetEtsyShippingTemplate($etsy_auth);
        }
    }
    $shippings_template = array();
    $etsy_sections = array();
    foreach ($sections as $val)
        $etsy_sections[$val['shop_section_id']] = $val['title'];

    foreach ($shippings as $val)
        $shippings_template[$val['shipping_template_id']] = $val['title'];

    $return = array('shop' => $etsyshop, 'sections' => $etsy_sections, 'shippings_template' => $shippings_template);

    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_selected_data_etsy_shop") {
    $shop_id = (int)$_GET['shop_id'];
    $field = $_GET['field'];
    $type = $_GET['type'];

    if ($type == 1) {
        $prod_id = (int)$_GET['product_data'];
        if ($field == "etsy_id")
            $data = get_product_meta_shop($prod_id, "etsy_id", $shop_id);
        if ($field == "whomadeit")
            $data = get_product_meta_shop($prod_id, "whomadeit", $shop_id);
        if ($field == "issupply")
            $data = get_product_meta_shop($prod_id, "issupply", $shop_id);
        if ($field == "whenmade")
            $data = get_product_meta_shop($prod_id, "whenmade", $shop_id);
        if ($field == "cat")
            $data = get_product_meta_shop($prod_id, "etsy_category_id", $shop_id);
        if ($field == "sub1cat")
            $data = get_product_meta_shop($prod_id, "etsy_sub1_category_id", $shop_id);
        if ($field == "sub2cat")
            $data = get_product_meta_shop($prod_id, "etsy_sub2_category_id", $shop_id);
        if ($field == "sub3cat")
            $data = get_product_meta_shop($prod_id, "etsy_sub3_category_id", $shop_id);
        if ($field == "section")
            $data = get_product_meta_shop($prod_id, "etsy_section_id", $shop_id);
        if ($field == "shipping")
            $data = get_product_meta_shop($prod_id, "etsy_shipping_id", $shop_id);
        if ($field == "occasion")
            $data = get_product_meta_shop($prod_id, "occasion", $shop_id);
        if ($field == "style")
            $data = get_product_meta_shop($prod_id, "etsy_style", $shop_id);
        if ($field == "recipient")
            $data = get_product_meta_shop($prod_id, "etsy_recipient", $shop_id);
        if ($field == "materials")
            $data = get_product_meta_shop($prod_id, "etsymaterials", $shop_id);
    }
    if ($type == 2) {
        $session_data = json_decode(base64_decode($_GET['product_data']), true);
        @extract($session_data);
        if ($field == "whomadeit")
            $data = ${'whomadeit' . $shop_id};
        if ($field == "issupply")
            $data = ${'issupply' . $shop_id};
        if ($field == "whenmade")
            $data = ${'whenmade' . $shop_id};
        if ($field == "cat")
            $data = ${'etsycategory' . $shop_id};
        if ($field == "sub1cat")
            $data = ${'etsysub1category' . $shop_id};
        if ($field == "sub2cat")
            $data = ${'etsysub2category' . $shop_id};
        if ($field == "sub3cat")
            $data = ${'etsysub3category' . $shop_id};
        if ($field == "section")
            $data = ${'etsysection' . $shop_id};
        if ($field == "shipping")
            $data = ${'etsyshipping' . $shop_id};
        if ($field == "occasion")
            $data = ${'etsyoccasion' . $shop_id};
        if ($field == "style")
            $data = ${'etsystyle' . $shop_id};
        if ($field == "recipient")
            $data = ${'etsyrecipient' . $shop_id};
        if ($field == "materials")
            $data = ${'etsymaterials' . $shop_id};
    }
    if ($data == null)
        $data = "";
    $data = stripcslashes($data);
    $return = array('data' => $data);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "check_product_existe_etsy") {
    echo json_encode(check_product_existe_etsy($_GET['etsy_id'], $_GET['shop_id'], $_GET['user_id']));
    exit();
}
// multi shop
if ($_GET['action'] == 'get_list_woocommerce_shop') {
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_woocommerce` WHERE `users_id` = $user_id");
    $numshops_wooc = $wpdb->num_rows($shop_list_query);

// woocommerce
if ($_GET['action'] == "get_list_products_woocommerce") {
    $version = getWooPAVersion($user_id);
    $wc_auth = getWoocommerceShop($user_id);
    if ($version == 1) {
        @extract($wc_auth);
        $products = getWooDropdownproducts($woocommerceshop, $woocommercetoken);
    } else {
        $products = getProductsList($wc_auth, array('filter[limit]' => '50', 'filter[post_status]' => 'all', 'fields' => 'id,title'));
    }
    $return = array('products' => $products);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_list_data_woocommerce") {
    $wc_auth = getWoocommerceShop($user_id);
    $shippings = getWooShipping($wc_auth);
    $return = array('shippings' => $shippings);
    echo json_encode($return);
    exit();
}
if ($_GET['action'] == "get_wc_categories") {
    $wc_cats = explode(",", $_GET['cats_id']);
    echo wc_categories(getWoocommerceShop($user_id), $wc_cats);
    exit();
}
/* * *********************Import shops products************************** */
if ($_GET['action'] == "shop_products") {
    if (isset($_GET["shop_count"])) {
        $shop_count = intval($_GET["shop_count"]);
        if ($shop_count == 1) {
            $method = "get_all_" . $_GET["shop_name"] . "_products";
            $field = "wp_users_" . $_GET["shop_name"];
            $shop_id = $wpdb->get_var("select id from $field where users_id=$user_id");
            echo json_encode(array('products' => $method($user_id, $shop_id)));
            exit();
        } else {
            $method = "get_all_" . $_GET["shop_name"] . "_shops";
            echo json_encode(array('products' => $method($user_id)));
            exit();
        }
    } else {
        $method = "get_all_" . $_GET["shop_name"] . "_products";
        $shop_id = (isset($_GET['shop_id']) && $_GET['shop_id'] != 0) ? (int)$_GET['shop_id'] : 0;
        echo json_encode(array('products' => $method($user_id, $shop_id)));
        exit();
    }
}

/* * ********************* <lemonstand calls> ************************** */
if ($_GET['action'] == "get_lemonstand_categories") {
    echo json_encode(lemonstand_categories(lemonstand_auth($user_id)));
    exit();
}

if ($_GET['action'] == "get_lemonstand_products") {
    echo json_encode(lemonstand_products(lemonstand_auth($user_id)));
    exit();
}

if ($_GET['action'] == "get_lemonstand_manufacturers") {
    echo json_encode(lemonstand_manufacturers(lemonstand_auth($user_id)));
    exit();
}
/* * ********************* </lemonstand calls> ************************** */

/* * ********************* <AmeriCommerce calls> ************************** */
if ($_GET['action'] == "get_americommerce_categories") {
    echo json_encode(americommerce_categories_live($user_id));
    exit();
}

if ($_GET['action'] == "get_americommerce_products") {
    echo json_encode(getListeAmericommerceProduct($user_id));
    exit();
}

if ($_GET['action'] == "get_americommerce_manufacturers") {
    echo json_encode(americommerce_manufacturers($user_id));
    exit();
}
/* * ********************* </Americommerce calls> ************************** */

/* * ********************* <Ecwid calls> ************************** */
if ($_GET['action'] == "get_ecwid_categories") {
    echo json_encode(ecwid_categories_live($user_id));
    exit();
}

if ($_GET['action'] == "get_ecwid_products") {
    echo json_encode(ecwid_products($user_id));
    exit();
}
/* * ********************* </Ecwid calls> ************************** */

// Opencart MultiShops
if ($_GET['action'] == "get_list_opencart_shop") {
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_storenvy` WHERE `users_id` = $user_id");
    $numshops_storenvy = $wpdb->num_rows($shop_list_query);

    if ($numshops_wooc == 0) {
        $shop_list[] = array(
            "id" => "0", "value" => "Select Shop"
        );
    } else {
        while ($row = mysql_fetch_assoc($shop_list_query)) {
            $shop_list[] = array(
                "id" => $row["id"], "value" => $row["domain"], "active" => $row["active"]
            );
        }
    }
    echo json_encode($shop_list);
    exit();
}
