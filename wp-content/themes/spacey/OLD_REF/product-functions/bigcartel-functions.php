<?php

define("bigcartel_Key", "jIWpqS52tiL0Uq2avo5xfKj9Z17FC2");
define("bigcartel_Secret", "EKtWMuRyu46snLt4WH2G68wegIZSAd2ZtrODNHbI7A5q4");
define("redirect_uri", "https://ryankikta.com/bigcartel/");

function GetResponse($url, $header, $request_type, $data)
{
    $con = curl_init();
    curl_setopt($con, CURLOPT_URL, $url);
    curl_setopt($con, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($con, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($con, CURLOPT_HTTPHEADER, $header);
    if ($request_type == 'POST') {
        curl_setopt($con, CURLOPT_POST, 1);
    } else {
        curl_setopt($con, CURLOPT_CUSTOMREQUEST, $request_type);
    }
    curl_setopt($con, CURLOPT_POSTFIELDS, $data);
    curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
    return curl_exec($con);
}

// ###################################################################################
// @function : Bigcartel auth new shop
// @param :
// ###################################################################################

function bigcartelAddShop($code = null, $currentuserid)
{
    $resultat = array();
    try {
        $request_type = 'POST';
        $header = array();
        $datasent = array(
            "code" => $code,
            "redirect_uri" => redirect_uri,
            "client_id" => bigcartel_Key,
            "client_secret" => bigcartel_Secret
        );

        $url = "https://api.bigcartel.com/oauth/token";
        $response = GetResponse($url, $header, $request_type, $datasent);
        debug($response);
        $decode_response = json_decode($response, true);

        if (isset($decode_response["error"])) {
            if (isset($decode_response["message"])) {
                $resultat = ["statut" => 400, "error" => $decode_response["message"]];
            } else {
                $resultat = ["statut" => 400, "error" => "CodeInvalid"];
            }
        } elseif (isset($decode_response["access_token"])) {
            $token = $decode_response["access_token"];
            $account_id = $decode_response["account_id"];

            $shop_data = getBigcartelShopInfo($token, $account_id);

            if ($shop_data['statut'] == 200) {
                $checkshop = checkExistantShop($shop_data['shop']['url'], $currentuserid);
                if ($checkshop['statut'] == 200) {
                    $shop_url = $shop_data['shop']['url'];
                    $shop_name = $shop_data['shop']['store_name'];
                    $description = $shop_data['shop']['description'];

                    $addshop_sql = "INSERT INTO wp_users_bigcartel SET ";
                    $addshop_sql .= "users_id=$currentuserid ,";
                    $addshop_sql .= "token='$token' ,";
                    $addshop_sql .= "shop_url='$shop_url' ,";
                    $addshop_sql .= "account_id=$account_id ,";
                    $addshop_sql .= "shop_name='$shop_name' ,";
                    $addshop_sql .= "description='$description'";
                    $addshop_sql .= ",firstimedone='1',active='1',last_call_time='" . time() . "',total_call='1'";
                    $addshop = $wpdb->get_result($addshop_sql);
                    if (!$addshop) {
                        $resultat = ["statut" => 400, "error" => mysql_error()];
                    } else {
                        $shop_id = mysql_insert_id();
                        $resultat = ["statut" => 200, "data" => $shop_id];
                    }
                } else {
                    $resultat = ["statut" => 400, "error" => "shopInstalled"];
                }
            } else {
                $resultat = ["statut" => 400, "error" => $shop_data['error']];
            }
        } else {
            $resultat = ["statut" => 400, "error" => "CodeInvalid"];
        }
    } catch (Exception $ex) {
        $resultat = ["statut" => 400, "error" => $ex->getMessage()];
    }

    return $resultat;
}

// ###################################################################################
// @function : Bigcartel get shop info
// @param : $token
// ###################################################################################

function getBigcartelShopInfo($token, $account_id)
{
    $resultat = [];
    $request_type = 'GET';
    $header = array(
        'Accept: application/vnd.api+json',
        'User-Agent: RyanKikta. (https://ryankikta.com)',
        'Content-type: application/vnd.api+json',
        'Authorization: Bearer ' . $token . ''
    );
    $datasent = array();
    $url = "https://api.bigcartel.com/v1/accounts/" . $account_id . "";
    $info = GetResponse($url, $header, $request_type, $datasent);
    $response_decoded = json_decode($info, true);
    if (isset($response_decoded['errors'])) {
        $resultat = ["statut" => 400, "error" => $response_decoded['errors']];
    } else {
        $resultat = ["statut" => 200, "shop" => $response_decoded['data']['attributes']];
    }
    return $resultat;
}

// ###################################################################################
// @function : Bigcartel check shop existance
// @param : $token
// ###################################################################################
function checkExistantShop($url, $userid)
{
    $resultat = array();
    global $wpdb;
    $response = $wpdb->get_results("SELECT * FROM wp_users_bigcartel WHERE users_id = $userid and shop_url='$url'", ARRAY_A);
    if (count($response) == 0) {
        $resultat = [
            "statut" => 200,
            "data" => "shopNotinstalled"
        ];
    } else {
        $resultat = [
            "statut" => 400,
            "error" => "shopInstalled"
        ];
    }
    return $resultat;
}

// ###################################################################################
// @function : get_all_bigcartel_products
// @param :$userId , $shop_id
// ###################################################################################

function get_all_bigcartel_products($user_id, $shop_id = 0)
{
    global $wpdb;

    $bc_products = array();
    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $user_shopids = $wpdb->get_results("select id from wp_users_bigcartel where users_id=$user_id $where order by id asc", ARRAY_A);
    foreach ($user_shopids as $shop) {

        $bigcartel_products = ListProductsbyShop($shop['id']);
        if ($bigcartel_products['statut'] == 200) {
            foreach ($bigcartel_products['products'] as $bigcartel_prd) {
                $pa_product_id = get_product_meta_shop_byfield("product_id", "bigcartel_id", $bigcartel_prd['id'], $shop_id);
                $all_products[] = array(
                    "id" => $bigcartel_prd['id'],
                    "title" => $bigcartel_prd['attributes']['name'],
                    "status" => $bigcartel_prd['attributes']['status'],
                    "url" => $bigcartel_prd['attributes']['url'],
                    "image" => $bigcartel_prd['attributes']['primary_image_url'],
                    "shop_id" => $shop['id'],
                    "imported" => ($pa_product_id == NULL) ? 0 : 1,
                    "pa_id" => ($pa_product_id == NULL) ? 0 : $pa_product_id
                );
            }

            $bc_products = $all_products;
        }
    }
    return $bc_products;
}

// ###################################################################################
// @function : ListProductsbyShop
// @param :$userId
// ###################################################################################
function ListProductsbyShop($shop_id)
{
    $resultat = array();
    $token = null;
    try {
        $shop = $wpdb->get_result("SELECT token,account_id FROM wp_users_bigcartel WHERE id=$shop_id");
        $shop_num = $wpdb->num_rows($shop);

        if ($shop_num != 0) {
            $shoprow = $wpdb->get_row($shop);
            $token = $shoprow[0];
            $account_id = $shoprow[1];
        }
        if (isset($token)) {
            $request_type = 'GET';
            $datasent = array();
            $url = "https://api.bigcartel.com/v1/accounts/" . $account_id . "/products";
            $header = array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $token . '');

            $products_list = GetResponse($url, $header, $request_type, $datasent);

            $decode_response = json_decode($products_list, true);
            if (isset($decode_response["errors"])) {
                if (isset($decode_response["errors"]["title"])) {
                    $resultat = ["statut" => 400, "error" => $decode_response["errors"]["title"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "ProductsNotFound"];
                }
            } elseif (isset($decode_response["data"])) {
                $resultat = ["statut" => 200, "products" => $decode_response["data"]];
            }
        } else {
            $resultat = ["statut" => 400, "error" => "TokenInvalid"];
        }
    } catch (Exception $ex) {
        $resultat = ["statut" => 400, "error" => $ex->getMessage()];
    }

    return $resultat;
}

// ###################################################################################
// @function : update_status_bigcartel_accepted
// @param :$userId,$shop_id
// ###################################################################################
function update_status_bigcartel_accepted($user_id, $shop_id = 0, $status, $ship_status)
{
    $resultat = array();
    global $wpdb;

    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $status_query = $wpdb->get_result("update wp_users_bigcartel set status_accepted='$status' where users_id=$user_id $where");
    $sql = "update wp_users_bigcartel set status_accepted='$status' where users_id=$user_id $where";
    //echo $sql;
    if ($status_query) {

        $ship_query = $wpdb->get_result("update wp_users_bigcartel set ship_all_order='$ship_status' where users_id= $user_id $where");
        if ($ship_query) {
            $resultat = ["statut" => 200, "message" => "Status updated successfully"];
        } else {
            $resultat = ["statut" => 400, "error" => "shippement order status not updated"];
        }
    } else {
        $resultat = ["statut" => 400, "error" => "Order status not updated"];
    }
    return $resultat;
}

// ###################################################################################
// @function : get_bigcartel_shop_data
// @param :$userId,$shop_id
// ###################################################################################

function get_bigcartel_shop_data($user_id, $shop_id)
{
    $resultat = array();
    $where = ($shop_id != 0) ? " and id=$shop_id" : "";
    $shop_data = $wpdb->get_result("SELECT account_id,token,shop_name,shop_url,status_accepted,ship_all_order FROM wp_users_bigcartel WHERE users_id = $user_id $where");
    $sql = "SELECT account_id,token,shop_name,shop_url,status_accepted,ship_all_order FROM wp_users_bigcartel WHERE users_id = $user_id $where";

    if ($wpdb->num_rows($shop_data) != 0) {
        $shoprow = mysql_fetch_array($shop_data);
        $shop_info = array(
            'bcartel_account_id' => $shoprow[0],
            'bcartel_token' => $shoprow[1],
        );
        $resultat = ["statut" => 200, "data" => $shop_info];
    } else {
        $resultat = ["statut" => 400, "error" => mysql_error()];
    }

    return $resultat;
}

// ###################################################################################
// @function : get_bigcartel_product_pershop
// @param :$userId,$shop_id
// ###################################################################################

function get_bigcartel_product_pershop($user_id, $shop_id, $id_bcartel)
{
    $resultat = array();
    $token = null;
    try {
        $where = ($shop_id != 0) ? " and id=$shop_id" : "";
        $shop_data = $wpdb->get_result("SELECT account_id,token FROM wp_users_bigcartel WHERE users_id = $user_id $where");

        if ($wpdb->num_rows($shop_data) != 0) {
            $shoprow = $wpdb->get_row($shop_data);
            $token = $shoprow[1];
            $account_id = $shoprow[0];
        }
        if (isset($token)) {
            $request_type = 'GET';
            $datasent = array();
            $url = "https://api.bigcartel.com/v1/accounts/" . $account_id . "/products/" . $id_bcartel . "";
            $header = array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $token . '');

            $product = GetResponse($url, $header, $request_type, $datasent);

            $decode_response = json_decode($product, true);
            if (isset($decode_response["errors"])) {
                if (isset($decode_response["errors"]["title"])) {
                    $resultat = ["statut" => 400, "error" => $decode_response["errors"]["title"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "ProductsNotFound"];
                }
            } elseif (isset($decode_response["data"])) {
                $resultat = ["statut" => 200, "product" => $decode_response];
            }
        } else {
            $resultat = ["statut" => 400, "error" => "TokenInvalid"];
        }
    } catch (Exception $ex) {
        $resultat = ["statut" => 400, "error" => $ex->getMessage()];
    }

    return $resultat;
}

// ###################################################################################
// @function : save_bigcartel_product_options
// @param :
// ###################################################################################
function save_bigcartel_product_options($POST, $products_id, $shop_id, $options, $bcartel_normal_price, $bcartel_plus_price)
{
    global $wpdb;
    foreach ($options as $bigcartel_option) {
        $bc_color_id = $POST["color_id_" . $bigcartel_option];
        $bc_size_id = $POST["size_id_" . $bigcartel_option][0];

        $variant_id = $wpdb->get_var("select id from wp_users_products_colors where color_id = $bc_color_id and size_id = $bc_size_id and users_products_id=$products_id ");
        $variant_id = ($variant_id) ? $variant_id : 'NULL';
        $sql = "INSERT INTO wp_users_products_colors (id,users_products_id,color_id,size_id,normalprice,plusprice,bigcartel_id) VALUES ($variant_id,$products_id,$bc_color_id,$bc_size_id,'$bcartel_normal_price','$bcartel_plus_price','$bigcartel_option')"
            . " on duplicate key update users_products_id = values(users_products_id),color_id=values(color_id),size_id=values(size_id),normalprice=values(normalprice),plusprice=values(plusprice),bigcartel_id=values(bigcartel_id)"
            . "";
        $wpdb->query($sql);

        $variantid = $wpdb->insert_id;
        if ($wpdb->last_error) {
            wp_mail('team@ryankikta.com', 'bigcartel add/edit error sql', $sql);
        }
        $all_vars = array();

        $var_meta_id = $wpdb->get_var("select id from wp_variants_meta where variant_id ='$variantid' and meta_key='bigcartel_id' and meta_value='$bigcartel_option'");
        $var_meta_id = ($var_meta_id) ? $var_meta_id : 'NULL';
        $_tmp = array('id' => $var_meta_id, 'variant_id' => $variantid, 'bigcartel_id' => $bigcartel_option);
        $all_vars[] = $_tmp;
        $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
        $_tmp = array();
        foreach ($all_vars as $_var) {
            $_tmp[] = " ({$_var['id']},'$products_id','{$_var['variant_id']}','bigcartel_id','{$_var['bigcartel_id']}','$shop_id') ";
        }
        $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
        $wpdb->query($sql_var);
        if ($wpdb->last_error) {
            wp_mail('team@ryankikta.com', 'gumroad add/edit error sql', $sql_var);
        }
    }
    $bigcartel_id = $_POST['bigcartel_id'];
    $wpdb->get_result("update wp_users_products set bigcartelactive=1 where id=" . $products_id);

    $_id = $wpdb->get_var("select meta_id from wp_products_meta where product_id=$products_id and meta_key='bigcartel_id' and meta_value=''");
    $_id = ($_id) ? $_id : 'NULL';
    $sql_pr = "insert into wp_products_meta (meta_id,product_id,meta_key,meta_value,shopid) values($_id,$products_id,'bigcartel_id',$bigcartel_id,$shop_id)"
        . " on duplicate key update product_id = values(product_id),meta_key=values(meta_key),meta_value=values(meta_value),shopid=values(shopid)";

    $wpdb->query($sql_pr);
}

// ###################################################################################
// @function : get_bigcartel_product_id
// @param :$user_product_id
// ###################################################################################

function get_bigcartel_product_id($user_product_id)
{
    global $wpdb;

    return $wpdb->get_var("select meta_value from wp_products_meta where meta_key='bigcartel_id' and product_id='$user_product_id' limit 1");
}

// ###################################################################################
// @function : get_all_bigcartel_order_items
// @param :$order,$userId,$shop_id
// ###################################################################################
function get_all_bigcartel_order_items($order, $user_id, $shop_id = 0)
{
    global $wpdb;
    $items = array();

    foreach ($order['data']["relationships"]['items']['data'] as $order_line_items) {
        $order_line_item = $order_line_items["id"];

        foreach ($order['included'] as $index => $included) {
            if ($included["id"] == $order_line_item && $included['type'] == "order_line_items") {
                $bigcartel_prodcut_id = $included["relationships"]['product']["data"]["id"];
                $variation_id = $included["relationships"]['product_option']["data"]["id"];
                $quantity = $included['attributes']["quantity"];
                $item_price = $included['attributes']["price"];
                $where = ($shop_id != 0) ? " and t1.shopid=$shop_id" : "";

                $user_product_id = $wpdb->get_var("select product_id from wp_products_meta where meta_key='bigcartel_id' and meta_value='$bigcartel_prodcut_id' and shopid=$shop_id");

                $product_sql = "select t2.id,t2.`product_id`,t2.`brand_id`,";
                $product_sql .= "t2.`front`,t2.`back` from wp_products_meta as t1";
                $product_sql .= " inner join wp_users_products as t2";
                $product_sql .= " on (t1.product_id = t2.id)";
                $product_sql .= " and t1.meta_key = 'bigcartel_id'";
                $product_sql .= " $where";
                $product_sql .= " and t2.users_id =" . $user_id;

                $product = $wpdb->get_row($product_sql, ARRAY_A);

                $pr_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where product_id=" . $product['id'] . " and meta_key='bigcartel_id' and meta_value='$variation_id' and shop_id=$shop_id");

                $variant = $wpdb->get_row("select color_id,size_id from wp_users_products_colors where id = " . $pr_variant_id, ARRAY_A);
                $pa_product_id = $product['id'];

                $inventory_id = $product['product_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                $color_id = $variant['color_id'];
                $size_id = $variant['size_id'];


                if ($pa_product_id > 0) {
                    $items [] = array(
                        'item_id' => $order_line_items["id"], //$item_id,
                        'pa_product_id' => $pa_product_id,
                        'product_id' => $inventory_id,
                        'brand_id' => $brand_id,
                        'hasfront' => $hasfront,
                        'hasback' => $hasback,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'quantity' => $quantity,
                        'item_price' => $item_price
                    );
                }
                break;
            }
        }
    }


    if (count($items) == 1) {
        if ($inventory_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $inventory_id);

            if (in_array($shippin_id1, array(1, 11, 12, 4))) {
                $items [0]['only_shirts'] = TRUE;
            } else {
                $items [0]['only_shirts'] = FALSE;
            }
        }
    }

    return $items;
}

// ###################################################################################
// @function : get_bigcartel_shops
// @param :
// ###################################################################################

function get_bigcartel_shops()
{
    global $wpdb;
    $shops = $wpdb->get_results('select distinct(account_id),token,shop_url,id from wp_users_bigcartel', ARRAY_A);
    return $shops;
}

// ###################################################################################
// @function : get_bigcartel_users_shop
// @param :
// ###################################################################################

function get_bigcartel_users_shop($shop_account)
{
    global $wpdb;
    return $wpdb->get_results("select id, users_id from wp_users_bigcartel where account_id=$shop_account", ARRAY_A);
}

// ###################################################################################
// @function : get_bigcartel_Orders_shop
// @param :
// ###################################################################################

function get_bigcartel_Orders_shop($shop_account, $token, $filter)
{
    $resultat = array();
    try {

        if (isset($token)) {
            $request_type = 'GET';
            $datasent = array();
            $url = "https://api.bigcartel.com/v1/accounts/" . $shop_account . "/orders" . $filter;
            $header = array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $token . '');

            $product = GetResponse($url, $header, $request_type, $datasent);
            $decode_response = json_decode($product, true);
            if (isset($decode_response["errors"])) {
                if (isset($decode_response["errors"]["title"])) {
                    $resultat = ["statut" => 400, "error" => $decode_response["errors"]["title"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "OrdersNotFound"];
                }
            } elseif (isset($decode_response["data"])) {
                $resultat = ["statut" => 200, "orders" => $decode_response];
            }
        } else {
            $resultat = ["statut" => 400, "error" => "TokenInvalid"];
        }
    } catch (Exception $ex) {
        $resultat = ["statut" => 400, "error" => $ex->getMessage()];
    }

    return $resultat;
}

// ###################################################################################
// @function : update_bigcartel_order_status
// @param :
// ###################################################################################

function update_bigcartel_order_status($all = false, $order_id, $status)
{
    $result = [];
    global $wpdb;
    $allowed_status = array("shipped", "unshipped");
    if ($all == false) {
        $order = $wpdb->get_row('select user_id , status , bigcartel_id,shop_id from wp_rmproductmanagement_orders where order_id=' . $order_id . '', ARRAY_A);
        debug($order);
        if (isset($order)) {
            $bigcartel_id = $order['bigcartel_id'];
            $user_id = $order['user_id'];
            $shop_id = $order['shop_id'];
            $shop_data = get_bigcartel_shop_data($user_id, $shop_id);

            $shop_account = $shop_data['data']['bcartel_account_id'];
            $shop_token = $shop_data['data']['bcartel_token'];
            if (in_array($status, $allowed_status)) {
                $request_type = 'PATCH';

                $datasent = json_encode(array(
                        "data" => array(
                            "id" => $bigcartel_id,
                            "type" => "orders",
                            "attributes" => array(
                                "shipping_status" => $status
                            ))
                    )
                );
                debug($datasent);
                $url = "https://api.bigcartel.com/v1/accounts/" . $shop_account . "/orders/" . $bigcartel_id;
                debug($url);

                $header = array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $shop_token . '');
                $orderupdated = GetResponse($url, $header, $request_type, $datasent);
                $decode_response = json_decode($orderupdated, true);
                if (!isset($decode_response['errors'])) {
                    $resultat = ["statut" => 200, "data" => "orderUpdated"];
                } else {
                    $resultat = ["statut" => 400, "error" => $decode_response['errors']];
                }
            } else {
                $resultat = ["statut" => 400, "error" => "statusNotAllowed"];
            }
        } else {
            $resultat = ["statut" => 400, "error" => "orderNotFound"];
        }
    } else {
        $resultat = ["statut" => 200, "data" => "TokenInvalid"];
    }
    return $resultat;
}

function getBigcartelShop($user_id)
{
    $checkuser = $wpdb->get_result("SELECT account_id,token,shop_name,shop_url,order_status,ship_all_order FROM wp_users_bigcartel WHERE users_id = $user_id");

    if ($wpdb->num_rows($checkuser) != 0) {
        $shoprow = mysql_fetch_array($checkuser);
        return array(
            'bcartel_account_id' => $shoprow[0],
            'bcartel_token' => $shoprow[1],
            'bcartel_shop_name' => $shoprow[2],
            'bcartel_shop_url' => $shoprow[3],
            'bcartel_order_status' => $shoprow[4],
            'bcartel_ship_status' => $shoprow[5],
        );
    }

    return array();
}

function bigcartelOrders($auth, $filter = "")
{
    $ch = curl_init("https://api.bigcartel.com/v1/accounts/" . $auth["bcartel_account_id"] . "/orders" . $filter);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $auth["bcartel_token"] . ''));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    return json_decode(curl_exec($ch), TRUE);
}

function bigcartelOrder($auth, $order_id)
{
    $ch = curl_init("https://api.bigcartel.com/v1/accounts/" . $auth["bcartel_account_id"] . "/orders/" . $order_id);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.api+json', 'User-Agent: RyanKikta. (https://ryankikta.com)', 'Content-type: application/vnd.api+json', 'Authorization: Bearer ' . $auth["bcartel_token"] . ''));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return json_decode(curl_exec($ch), TRUE);
}

function get_AllItem_bigcartel($orders, $order_data, $user_id)
{
    global $wpdb;
    $items = array();

    foreach ($order_data["relationships"]['items']['data'] as $order_line_items) {
        $order_line_item = $order_line_items["id"];

        foreach ($orders['included'] as $included) {
            if ($included["id"] == $order_line_item && $included['type'] == "order_line_items") {
                $bigcartel_prodcut_id = $included["relationships"]['product']["data"]["id"];
                $variation_id = $included["relationships"]['product_option']["data"]["id"];

                $quantity = $included['attributes']["quantity"];
                $item_price = $included['attributes']["price"];
                /*
                  $product        = $wpdb->get_results("SELECT t1.color_id,t1.size_id,pr.id,pr.product_id,pr.brand_id,pr.front,pr.back FROM wp_users_products_colors as t1
                  left join wp_users_products as pr on (pr.id = t1.users_products_id)
                  WHERE t1.bigcartel_id = '$variation_id' and pr.users_id='$user_id'", ARRAY_A);
                 */
                $product = $wpdb->get_results("SELECT t1.color_id,t1.size_id,pr.id,pr.product_id,pr.brand_id,pr.front,pr.back FROM wp_users_products_colors as t1 
                                                 join wp_users_products as pr on (pr.id = t1.users_products_id)
                                                 join wp_products_meta as meta on (meta.product_id = t1.users_products_id)   
                                                WHERE t1.bigcartel_id = '$variation_id' and meta.meta_key='bigcartel_id' and meta.meta_value='$bigcartel_prodcut_id' and pr.users_id='$user_id'", ARRAY_A);

                $product = end($product);
                $pa_product_id = $product['id'];
                $inventory_id = $product['product_id'];
                $brand_id = $product['brand_id'];
                $hasfront = $product['front'];
                $hasback = $product['back'];
                $color_id = $product['color_id'];
                $size_id = $product['size_id'];


                if ($pa_product_id > 0) {
                    $items [] = array(
                        'item_id' => $order_line_items["id"], //$item_id,
                        'pa_product_id' => $pa_product_id,
                        'product_id' => $inventory_id,
                        'brand_id' => $brand_id,
                        'hasfront' => $hasfront,
                        'hasback' => $hasback,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                        'quantity' => $quantity,
                        'item_price' => $item_price
                    );
                }
                break;
            }
        }
    }


    if (count($items) == 1) {
        if ($inventory_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $inventory_id);

            if (in_array($shippin_id1, array(1, 11, 12))) {
                $items [0]['only_shirts'] = TRUE;
            } else {
                $items [0]['only_shirts'] = FALSE;
            }
        }
    }

    return $items;
}

function bigcqrtel_shipping_address($orders, $order)
{
    $shippingaddress1 = array();

    $shippingaddress1['clientname'] = $order["attributes"]["customer_first_name"] . " " . $attr["customer_last_name"];
    $shippingaddress1['address1'] = $order["attributes"]["shipping_address_1"];
    $shippingaddress1['address2'] = $order["attributes"]["shipping_address_2"];
    $shippingaddress1['city'] = $order["attributes"]["shipping_city"];
    $shippingaddress1['state'] = $order["attributes"]["shipping_state"];
    $shippingaddress1['zit1ode'] = $order["attributes"]["shipping_zip"];
    $shippingaddress1['country'] = $order["relationships"]["shipping_country"]["data"]['id'];

    $country_code = $order["relationships"]["shipping_country"]["data"]['id'];
    $country = "";
    foreach ($orders['included'] as $included) {
        if ($included['id'] == $country_code) {
            $country = $included['attributes']['name'];
            break;
        }
    }

    return array(
        'shippingaddress' => trim($shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $shippingaddress1['address2'] . "\n" . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zit1ode'] . "\n" . $country),
        'shippingaddress1' => serialize($shippingaddress1),
        'shippingaddress_country' => $country_code,
        'shippingaddress_country_iso' => $country_code,
        'shippingaddress_state_code' => $country_code,
        'shippingaddress_zip' => $order["attributes"]["shipping_zip"],
        //'customer_id'                   => $customer_id,
        'country_code' => $country_code,
        //"country"                       => $country
    );
}

function bigcartel_shipping_address_webhook($order)
{
    $shippingaddress1 = array();

    $shippingaddress1['clientname'] = $order["data"]["attributes"]["customer_first_name"] . " " . $order["data"]["attributes"]["customer_last_name"];
    $shippingaddress1['address1'] = $order["data"]["attributes"]["shipping_address_1"];
    $shippingaddress1['address2'] = $order["data"]["attributes"]["shipping_address_2"];
    $shippingaddress1['city'] = $order["data"]["attributes"]["shipping_city"];
    $shippingaddress1['state'] = $order["data"]["attributes"]["shipping_state"];
    $shippingaddress1['zipcode'] = $order["data"]["attributes"]["shipping_zip"];
    $shippingaddress1['country'] = $order["data"]["relationships"]["shipping_country"]["data"]['id'];

    $country_code = $shippingaddress1['country'];
    $country = "";
    foreach ($order['included'] as $included) {
        if ($included['id'] == $country_code) {
            $country = $included['attributes']['name'];
            break;
        }
    }
    $address2 = ($shippingaddress1['address2'] != "") ? $shippingaddress1['address2'] : "";
    $shippingaddress = trim($shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . "\n" . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zit1ode'] . "\n" . $country);
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zit1ode'], "country" => $shippingaddress1['country'], "phone" => "");

    if ($country_code == "US") {
        $shipping_id = 1;
    } elseif ($country_code == "CA") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }
    return array(
        'shippingaddress' => $shippingaddress,
        'shippingaddress1' => serialize($shippingaddress1),
        'shippingaddress_country' => $country_code,
        'shippingaddress_state' => $shippingaddress1['state'],
        'shippingaddress_state_code' => $country_code,
        'shippingaddress_zip' => $shippingaddress1['zit1ode'],
        'shipping_id' => $shipping_id,
        'paypal_address' => $paypal_address
    );
}

function bcartel_change_shipp_status($order_id, $user_id)
{
    $auth = getBigcartelShop($user_id);
    $ch = curl_init("https://api.bigcartel.com/v1/accounts/" . $auth["bcartel_account_id"] . "/orders/" . $order_id);

    curl_setopt(
        $ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/vnd.api+json',
            'User-Agent: RyanKikta. (https://ryankikta.com)',
            'Content-type: application/vnd.api+json',
            'Authorization: Bearer ' . $auth["bcartel_token"]
        )
    );

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
        'data' => array(
            "id" => $order_id,
            "type" => "orders",
            "attributes" => array("shipping_status" => "shipped")
        ))));

    //curl_setopt($ch, CURLOPT_POSTFIELDS, "shipping_status=shipped");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    curl_setopt($ch, CURLOPT_HEADER, 1);
    return curl_exec($ch);

    return json_decode(curl_exec($ch), TRUE);
}
