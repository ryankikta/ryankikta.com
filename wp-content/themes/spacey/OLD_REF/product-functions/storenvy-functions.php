<?php
define("Storenvy_Id", "e8ebafb6148d1e57c829d07a24805ff2dfb2071358cfd570f8153ede03353e8e");
define("Storenvy_Secret", "b78f406ba040413692d305a279108358114f800f9ec9e6317426dbf60baa5a12");

function send_request($path, $method)
{
    if ($method == 'POST')
        $method = HTTP_Request2::METHOD_POST;
    if ($method == 'PUT')
        $method = HTTP_Request2::METHOD_PUT;
    if ($method == 'GET')
        $method = HTTP_Request2::METHOD_GET;
    if ($method == 'DELETE')
        $method = HTTP_Request2::METHOD_DELETE;
    $request = new HTTP_Request2($path, $method);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $response = json_decode($body, TRUE);
    return $response;
}

function check_storenvy_call_api_pershop($shopid)
{

    $storenvytoken = getStorenvyShopById($shopid);
    if ($storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/store.json?access_token=" . $storenvytoken;
        $res = send_request($path, 'GET');
        if ($res == null)
            return false;
    }
    return true;
}

function Call_Storenvy_Api($path, $method, $params)
{
    if ($method == 'POST')
        $method = HTTP_Request2::METHOD_POST;
    if ($method == 'PUT')
        $method = HTTP_Request2::METHOD_PUT;
    if ($method == 'GET')
        $method = HTTP_Request2::METHOD_GET;
    if ($method == 'DELETE')
        $method = HTTP_Request2::METHOD_DELETE;
    $request = new HTTP_Request2($path, $method);
    if (($method == 'POST') || ($method == 'PUT')) {
        $request->addPostParameter($params);
    }
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));

    $body = $request->send()->getBody();
    $response = json_decode($body, TRUE);
    return $response;
}

function SaveShopData($code, $currentuserid, $currentusername)
{

    $response = array();
    $url = 'https://api.storenvy.com/oauth/token';
    $params = array(
        "code" => $code,
        "client_id" => Storenvy_Id,
        "client_secret" => Storenvy_Secret,
        "redirect_uri" => "https://ryankikta.com/storenvy",
        "grant_type" => "authorization_code"
    );

    $request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
    $request->addPostParameter($params);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE
    ));
    $body = $request->send()->getBody();
    $responseObj = json_decode($body);

    $access_token = $responseObj->access_token;
    $resfresh_token = $responseObj->refresh_token;

    if ($access_token != "") {
        $shopInfoResult = saveShopInfo($access_token, $currentuserid, $resfresh_token);
        if ($shopInfoResult['status'] == 200) {

            $response = array(
                'status' => 200,
                'data' => $shopInfoResult['data']
            );
        } else {
            $response = array(
                'status' => 400,
                'error' => $shopInfoResult['error']
            );
        }
    } else {
        $response = array(
            'status' => 400,
            'error' => $responseObj
        );
    }

    return $response;
}

function getShopInfo($access_token)
{
    echo("<script>console.log('getShopInfo');</script>");
    $response = array();
    $path = "https://api.storenvy.com/v1/store?access_token=" . $access_token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $store_data = json_decode($body, TRUE);

    if (isset($store_data['data']['url'])) {
        $response = array(
            'status' => 200,
            'data' => array(
                'url' => $store_data['data']['url'],
                'name' => $store_data['data']['name']
            )
        );
    } else {
        $response = array(
            'status' => 400,
            'error' => $body
        );
    }
    return $response;
}

function saveShopInfo($access_token, $currentuserid, $resfresh_token)
{
    echo("<script>console.log('saveShopInfo');</script>");
    global $wpdb;
    $response = array();
    $info = getShopInfo($access_token);
    if ($info['status'] == 200) {
        $shop = $info['data'];
        $shop_url = $shop['url'];
        $shop_id = 0;
        $check_exist_shop = $wpdb->get_result("select id from wp_users_storenvy where users_id=$currentuserid and shop='$shop_url'");
        if (isset($check_exist_shop) && ($wpdb->num_rows($check_exist_shop) > 0)) {
            $data = $wpdb->get_row($check_exist_shop);
            $shop_id = $data[0];
            echo("<script>console.log('shop exists');</script>");
            echo("<script>console.log('php:" . $data . "');</script>");
        /*} else {
            echo("<script>console.log('getting shop from trash');</script>");
            $old_url = str_replace("https:", "http:", $shop_url);
            $check_exist_shop = $wpdb->get_result("select shop_id from wp_users_shops_deleted where users_id=$currentuserid and shop_name='$old_url' and shop_type='storenvy'");
            if (isset($check_exist_shop) && ($wpdb->num_rows($check_exist_shop) > 0)) {
                echo("<script>console.log('getting shop from trash again');</script>");
                $data = $wpdb->get_row($check_exist_shop);
                $shop_id = $data[0];
                echo("<script>console.log('old url');</script>");
                echo("<script>console.log('php:" . $data . "');</script>");
            }*/
        }
        $checkQuery = "SELECT shop FROM wp_users_storenvy WHERE users_id = $currentuserid and shop = '$shop_url'";
        $shopInstalled = $wpdb->get_result($checkQuery);

        if ($wpdb->num_rows($shopInstalled) == 0) {
            $shop_id = ($shop_id == 0) ? "NULL" : (int)$shop_id;

            $saveShopQuery = "INSERT INTO wp_users_storenvy SET ";
            $saveShopQuery .= "id=" . $shop_id . ",";
            $saveShopQuery .= "users_id=" . $currentuserid . ",";
            $saveShopQuery .= "shop='" . $shop_url . "' , ";
            $saveShopQuery .= "shop_name='" . mysql_escape_string($shop['name']) . "' , ";
            $saveShopQuery .= "token= '" . mysql_escape_string($access_token) . "',";
            $saveShopQuery .= "refresh_token='" . mysql_escape_string($resfresh_token) . "',";
            $saveShopQuery .= "dateadded=CURRENT_TIMESTAMP,";
            $saveShopQuery .= "dateupdated='0000-00-00 00:00:00',active=1";
            $saved = $wpdb->get_result($saveShopQuery);
            if ($saved && $shop_id > 0)
                echo("<script>console.log('inserted into users storenvy');</script>");
                $wpdb->get_result("DELETE from wp_users_shops_deleted where shop_id=$shop_id and users_id= $currentuserid and shop_type='storenvy'");

            $shop_id = mysql_insert_id();
            if (mysql_errno()) {
                $response = array(
                    'status' => 400,
                    'error' => mysql_error()
                );
            } else {
                $id_shop = getStorenvyShopByName($currentuserid, $shop['url']);
                if ($id_shop != 0) {
                    /*$contact_id = get_user_meta($currentuserid, '_infusionsoft_contact_id', true);
                    $ob = new Infusionsoft_Examples();
                    $ob->add_tag($contact_id,166);
                    $ob->remove_tag($contact_id,168);*/
                    $response = array(
                        'status' => 200,
                        'data' => $id_shop
                    );
                    echo("<script>console.log('php:" . $id_shop . "');</script>");
                } else {
                    $response = array(
                        'status' => 400,
                        'error' => "ShopNotFound"
                    );
                }
            }
        } else {
            $response = array(
                'status' => 400,
                'error' => "ShopExist"
            );
        }
    } else {
        $response = array(
            'status' => 400,
            'error' => $info['error']
        );
    }
    return $response;
}

function checkExistingStorenvyWebhooks($storenvy_token, $shop_id, $user_id)
{
    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $storenvy_token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $webhooks = json_decode($body, TRUE);
    $webhook_ordercreated = false;
    $webhook_productupdated = false;
    $webhook_productdeleted = false;
    if ($user_id == 479) {
        echo "hooks";
        debug($webhooks);
        exit();
    }
    if ($webhooks['meta']['code'] == 200) {
        foreach ($webhooks['data']['webhooks'] as $webhook) {
            $webhook_id = $webhook['id'];
            $url = $webhook['url'];
            $pos = strpos($url, "ryankikta.com");
            $createwebook = strpos($url, "create&user_id=$user_id&shop_id=$shop_id");
            $deletewebook = strpos($url, "productdelete&user_id=$user_id&shop_id=$shop_id");
            $updatewebook = strpos($url, "productupdate&user_id=$user_id&shop_id=$shop_id");
            if ($pos !== false) {
                if ($createwebook !== false)
                    $webhook_ordercreated = true;
                if ($updatewebook !== false)
                    $webhook_productupdated = true;
                if ($deletewebook !== false)
                    $webhook_productdeleted = true;
            }
        }
    }
    $response = array(
        'create' => $webhook_ordercreated,
        'update' => $webhook_productupdated,
        'delete' => $webhook_productdeleted,
    );
    return $response;
}

function saveOrderPaidHook($token, $shop_id, $user_id)
{
    $response = array();
    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
    $newBody = '{"url" : "https://api.ryankikta.com/storenvy_webhook_old.php?action=create&user_id=' . $user_id . '&shop_id=' . $shop_id . '","events" : ["order/paid"]}';
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($newBody);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $SOPHookResponse = json_decode($body, TRUE);
    if (isset($SOPHookResponse['data']['id'])) {
        $response = array(
            'status' => 200,
            'data' => "SOPHookResponse"
        );
    } else {
        $response = array(
            'status' => 400,
            'error' => $body
        );
    }
    return $response;
}

function saveUpdateProductsHook($token, $shop_id, $user_id)
{
    echo("<script>console.log('saveUpdateProductsHook');</script>");
    $response = array();
    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
    $newBody = '{"url" : "https://api.ryankikta.com/storenvy_webhook_old.php?action=productupdate&user_id=' . $user_id . '&shop_id=' . $shop_id . '","events" : ["product/updated"]}';
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($newBody);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $SDPHookResponse = json_decode($body, TRUE);
    if (isset($SDPHookResponse['data']['id'])) {
        $response = array(
            'status' => 200,
            'data' => "SDPHookCreated"
        );
    } else {
        $response = array(
            'status' => 400,
            'error' => $body
        );
    }
    return $response;
}

function saveDeleteProductsHook($token, $shop_id, $user_id)
{
    $response = array();
    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
    $newBody = '{"url" : "https://api.ryankikta.com/storenvy_webhook_old.php?action=productdelete&user_id=' . $user_id . '&shop_id=' . $shop_id . '","events" : ["product/deleted"]}';
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($newBody);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $SUPHookResponse = json_decode($body, TRUE);
    if (isset($SUPHookResponse['data']['id'])) {
        $response = array(
            'status' => 200,
            'data' => "SUPHookCreated"
        );
    } else {
        $response = array(
            'status' => 400,
            'error' => $body
        );
    }
    return $response;
}

function removeStorenvyShop($shop_id, $user_id)
{
    GLOBAL $wpdb;
    echo("<script>console.log('removeStorenvyshop');</script>");
    $check_shop = $wpdb->get_results("select * FROM `wp_users_storenvy` WHERE `id` = $shop_id and `users_id` = $user_id", ARRAY_A);
    if ($wpdb->num_rows > 0) {
        $shop_data = ($check_shop);
        $token = $shop_data["token"];
        $shop_name = $shop_data["shop"];
        $active = intval($shop_data["active"]);
        $id = intval($shop_data["id"]);
        if ($active > 0) {
            $response = delete_storenvy_webhook($token, $user_id, $shop_name, 'LIVE', $id);
	} else {
                $query = $wpdb->get_results("DELETE FROM `wp_users_storenvy` WHERE `id` = $shop_id and `users_id` = $user_id");
            }
    } else {
        $response = array("status" => 400, "error" => "Shop not Found with id:" . $id);
	return $response;
    }
}

function delete_storenvy_webhook($shop_token, $user_id, $shop_name, $from, $shop_id)
{

    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $shop_token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $webhooks = json_decode($body, TRUE);
    if ($webhooks['meta']['code'] == 200) {
        foreach ($webhooks['data']['webhooks'] as $webhook) {
            $webhook_id = $webhook['id'];
            $url = $webhook['url'];
            $pos = strpos($url, "ryankikta.com");
            $posdelete = strpos($url, "productdelete&user_id=$user_id&shop_id=$shop_id");
            if ($pos !== false) {
                if ($posdelete == false) {
                    $path = "https://api.storenvy.com/v1/webhooks/" . $webhook_id . "?access_token=" . $shop_token;
                    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
                    $request->setConfig(array(
                        'ssl_verify_peer' => FALSE,
                        'ssl_verify_host' => FALSE));
                    $body = $request->send()->getBody();
                }
            }
        }
        $query_insert = $wpdb->get_result("INSERT INTO `wp_users_shops_deleted` (`id`, `users_id`, `shop_name`, `shop_type`, `shop_id`,deleted_at,type) VALUES (NULL, $user_id, '$shop_name', 'storenvy', '$shop_id', CURRENT_TIMESTAMP,'LIVE')");
        if (!$query_insert) {
            $response = array("status" => 400, "error" => mysql_error());
        } else {
            $query = $wpdb->get_result("DELETE FROM `wp_users_storenvy` WHERE `id` = $shop_id and `users_id` = $user_id");
            if (!$query) {
                $response = array("status" => 400, "error" => mysql_error());
            } else {
                $wpdb->get_result("DELETE FROM `wp_users_storenvy` WHERE `users_id` = $user_id and id=$shop_id");
                $response = array("status" => 200, "data" => "true");
            }
        }
    } else
        $response = array("status" => 400, "error" => ($webhooks['error_message'] != "") ? $webhooks['error_message'] : "Error with delete webhook");

    return $response;
}

function remove_hooks_storenvy($shop_id)
{
    global $wpdb;
    $token = $wpdb->get_var("select token from wp_users_storenvy where id=$shop_id");

    $path = "https://api.storenvy.com/v1/webhooks.json?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    $body = $request->send()->getBody();
    $webhooks = json_decode($body, TRUE);
    if ($webhooks['meta']['code'] == 200) {
        foreach ($webhooks['data']['webhooks'] as $webhook) {
            $webhook_id = $webhook['id'];
            $url = $webhook['url'];
            $pos = strpos($url, "ryankikta.com");
            if ($pos !== false) {
                $path = "https://api.storenvy.com/v1/webhooks/" . $webhook_id . "?access_token=" . $token;
                $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
                $request->setConfig(array(
                    'ssl_verify_peer' => FALSE,
                    'ssl_verify_host' => FALSE));
                $body = $request->send()->getBody();
            }
        }
    }
}

function getStorenvyShopByName($user_id, $shop_name)
{
    echo("<script>console.log('getStorenvyShopByName');</script>");
    $shop_Id = 0;
    $shop = $wpdb->get_result("SELECT `id` FROM `wp_users_storenvy` WHERE `users_id` = $user_id and shop = '$shop_name'");
    $numshopsstorenvy = $wpdb->num_rows($shop);

    if ($numshopsstorenvy !== 0) {
        $shoprow = $wpdb->get_row($shop);
        $shop_Id = $shoprow[0];
    }

    return $shop_Id;
}

function check_list_Storenvy_Shops($user_id, $type = 1)
{
    global $wpdb;
    $count = $wpdb->get_var("select count(id) from wp_users_storenvy where users_id=$user_id");

    if ($type == 2)
        return (int)$count;
    if ($count > 0)
        return true;
    return false;
}

function get_list_shops_storenvy($user_id)
{
    $shop_list = array();
    $shop_list_query = $wpdb->get_result("SELECT `shop`,`id`,`active` FROM `wp_users_storenvy` WHERE `users_id` = $user_id");
    $numshopsstorenvy = $wpdb->num_rows($shop_list_query);

    if ($numshopsstorenvy == 0) {
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

    return $shop_list;
}

function getStorenvyShop($user_id)
{

    $storenvytoken = '';
    $checkuser = $wpdb->get_result("SELECT `token` FROM `wp_users_storenvy` WHERE `users_id` = $user_id");
    $numshopsstorenvy = $wpdb->num_rows($checkuser);

    if ($numshopsstorenvy !== 0) {

        $shoprow = $wpdb->get_row($checkuser);
        $storenvytoken = $shoprow[0];
    }

    return $storenvytoken;
}

function getStorenvyShopById($shop_id)
{
    $shop_token = "";
    $shop = $wpdb->get_result("SELECT `token` FROM `wp_users_storenvy` WHERE `id`=$shop_id");
    $result = $wpdb->num_rows($shop);

    if ($result !== 0) {
        $shoprow = $wpdb->get_row($shop);
        $shop_token = $shoprow[0];
    }

    return $shop_token;
}

function getStorenvyShopName($user_id, $shop_id)
{
    $shop_name = null;
    $where = ($shop_id != 0) ? "and `id`=$shop_id" : "";
    $shop = $wpdb->get_result("SELECT `shop_name` FROM `wp_users_storenvy` WHERE `users_id` =$user_id $where");
    $result = $wpdb->num_rows($shop);

    if ($result !== 0) {
        $shoprow = $wpdb->get_row($shop);
        $shop_name = $shoprow[0];
    }

    return $shop_name;
}

function getStorenvyShopsData($data)
{
    $storenvyshops = $data['storenvyshop'];
    $storenvyactive = esc_sql($data['storenvyactive']);
    $storenvyprice = esc_sql($data['storenvyprice']);
    $storenvy_shops_data = array();
    foreach ($storenvyshops as $shop_id) {
        $storenvynewproduct = $data['storenvynewproduct_' . $shop_id];
        $storenvy_id = esc_sql($data['storenvy_id_' . $shop_id]);
        $str_marketplace_id = esc_sql($data['storenvy_marketplace_id_' . $shop_id]);
        $storenvy_preorder = esc_sql($data['storenvy_preorder_' . $shop_id]);
        $storenvy_on_sale = esc_sql($data['storenvy_on_sale_' . $shop_id]);
        if ($str_marketplace_id == "")
            $str_marketplace_id = 0;
        if ($storenvy_preorder == "")
            $storenvy_preorder = 0;
        if ($storenvy_on_sale == "")
            $storenvy_on_sale = 0;

        $storenvycollection = "";
        foreach ($data['storenvycollection_' . $shop_id] as $collection) {
            $storenvycollection .= $collection . ",";
        }
        $storenvycollection = rtrim($storenvycollection, ",");

        $storenvyshipping = esc_sql($data['storenvy_shipping_id_' . $shop_id]);
        $storenvy_shops_data[] = array(
            'shop_id' => $shop_id,
            'storenvynewproduct' => $storenvynewproduct,
            'storenvy_marketplace_id' => $str_marketplace_id,
            'storenvycollection' => $storenvycollection,
            'storenvy_shipping_id' => $storenvyshipping,
            'storenvy_on_sale' => $storenvy_on_sale,
            'storenvy_preorder' => $storenvy_preorder
        );
    }
    return array('storenvyactive' => $storenvyactive, 'storenvyprice' => $storenvyprice, 'storenvyshopsdata' => $storenvy_shops_data);
}

function getStorenvyDataPerShop($data, $shop_id)
{

    $storenvy_shops_data = array();

    $storenvynewproduct = $data['storenvynewproduct_' . $shop_id];
    $storenvy_id = esc_sql($data['storenvy_id_' . $shop_id]);
    $str_marketplace_id = esc_sql($data['storenvy_marketplace_id_' . $shop_id]);
    $storenvy_preorder = esc_sql($data['storenvy_preorder_' . $shop_id]);
    $storenvy_on_sale = esc_sql($data['storenvy_on_sale_' . $shop_id]);
    if ($str_marketplace_id == "")
        $str_marketplace_id = 0;
    if ($storenvy_preorder == "")
        $storenvy_preorder = 0;
    if ($storenvy_on_sale == "")
        $storenvy_on_sale = 0;

    $storenvycollection = "";
    foreach ($data['storenvycollection_' . $shop_id] as $collection) {
        $storenvycollection .= $collection . ",";
    }
    $storenvycollection = rtrim($storenvycollection, ",");

    $storenvyshipping = esc_sql($data['storenvy_shipping_id_' . $shop_id]);
    return $storenvy_shops_data = array(
        'shop_id' => $shop_id,
        'storenvynewproduct' => $storenvynewproduct,
        'storenvy_marketplace_id' => $str_marketplace_id,
        'storenvycollection' => $storenvycollection,
        'storenvy_shipping_id' => $storenvyshipping,
        'storenvy_on_sale' => $storenvy_on_sale,
        'storenvy_preorder' => $storenvy_preorder
    );
}

function addStorenvyProduct($POST, $product_data, $data_pershop, $variants, $storenvytoken, $currentuserid, $products_id, $shop_id)
{
    global $wpdb;
    @extract($data_pershop);
    @extract($product_data);

    $storenvyprice = $POST['storenvyprice'];

    $cents = round($storenvyprice * 100);
    $preorder = ($storenvy_preorder == 1) ? true : false;
    $onsale = ($storenvy_on_sale == 1) ? true : false;
    $path = "https://api.storenvy.com/v1/products.json?access_token=" . $storenvytoken;
    $description = str_replace(array('<br>', '<br />'), array("\n", "\n"), stripslashes($description));
    $description = strip_tags($description);
    $params = array(
        "name" => str_replace('"', '\"', stripslashes($title)),
        "cents" => $cents,
        "description" => $description,
        "shipping_group_id" => $storenvy_shipping_id,
        "preorder" => $preorder,
        "on_sale" => $onsale,
        "marketplace_category_id" => $storenvy_marketplace_id,
        "tag_list" => $tags
    );

    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
    $request->addPostParameter($params);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = trim($request->send()->getBody());

    $productinfo = json_decode($body, TRUE);
    if (isset($productinfo['error'])) {
        $errors = array();
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error ' . $text . ' product in storenvy ' . $shop_text . ':';
        $errors[] = $productinfo['error'];
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else if (isset($productinfo['meta']['error_message']) && $productinfo['meta']['error_message'] != "") {
        $errors = array();
        $text = ($POST['pagetype'] == 1) ? 'add' : (($POST['pagetype'] == 2) ? 'edit' : 'duplicate');
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error ' . $text . ' product in storenvy ' . $shop_text . ': ' . $productinfo['meta']['error_type'];
        $errors[] = $productinfo['meta']['error_message'];
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else {
        $storenvy_id = $productinfo['data']['id'];
        $_SESSION['shops']['storenvy_id'] = $storenvy_id;
        if ($storenvy_id == "") {
            if (strpos($body, "The server encountered an internal error") > 0) {
                $error = "A Storenvy service issue occurred";
            } else if (strpos($body, "Retry later") !== false) {
                $error = "Storenvy is experiencing heavy volume. Please try again in a few minutes";
            } else {
                if ($body == '{"error":"API Key is invalid"}')
                    $error = "Your API key is invalid, please reauthorize your store.";
                else if (strpos($productinfo['meta']['error_message'], "This API call requires a store") !== false) {
                    $error = "You need to set up a store to start using Storenvy App.";
                } else
                    $error = "An error occured in our end";
            }
            $errors = array();
            $errors[] = $error;
            $post = array_merge($product_data, $storenvy_data);
            $post['str_body'] = $body;
            $post['errors'] = $errors;
            $post['user_id'] = $currentuserid;
            $post['data'] = array_merge($product_data, $storenvy_data);

            $post['user_product_id'] = $products_id;
            $export = var_export($post, true);
            wp_insert_post(array(
                'post_content' => var_export($post, true),
                'post_title' => esc_sql("storenvy add product error ($currentuserid) "),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'
            ));
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, 'errors' => $errors);
            echo json_encode($return);
            exit();
        } else {
            $pa_product_id_old = get_product_id_meta_shop($storenvy_id, "storenvy_id", $shop_id);
            if ($pa_product_id_old && $pa_product_id_old != $products_id)
                delete_variants_product_meta_shop($pa_product_id_old, 'storenvy_id', $shop_id);

            $all_meta = array('storenvy_id' => 'NULL', 'storenvyprice' => 'NULL', 'storenvy_collection_id' => 'NULL', 'storenvy_shipping_id' => 'NULL', 'storenvy_marketplace_id' => 'NULL', 'storenvy_preorder' => 'NULL', 'storenvy_on_sale' => 'NULL');
            $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $products_id and shopid = $shop_id", ARRAY_A);
            foreach ($results as $res)
                $all_meta[$res['meta_key']] = $res['meta_id'];

            $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $products_id) ? $pa_product_id_old : 0;
            if ($prod_to_deconnect) {
                update_product_meta_shop($products_id, 'storenvy_id', $storenvy_id, $shop_id);
                update_product_meta_shop($products_id, 'storenvyprice', $cents, $shop_id);
                if (!in_array($storenvycollection, array('', NULL, 0, '0')))
                    update_product_meta_shop($products_id, 'storenvy_collection_id', $storenvycollection, $shop_id);
                update_product_meta_shop($products_id, 'storenvy_shipping_id', $storenvy_shipping_id, $shop_id);
                update_product_meta_shop($products_id, 'storenvy_marketplace_id', $storenvy_marketplace_id, $shop_id);
                update_product_meta_shop($products_id, 'storenvy_preorder', $storenvy_preorder, $shop_id);
                update_product_meta_shop($products_id, 'storenvy_on_sale', $storenvy_on_sale, $shop_id);
            } else {
                $sql = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values (NULL,$products_id,'storenvy_id','$storenvy_id',$shop_id),"
                    . " (NULL,$products_id,'storenvyprice','" . $cents . "',$shop_id),"
                    . " (NULL,$products_id,'storenvy_shipping_id','" . $storenvy_shipping_id . "',$shop_id), "
                    . " (NULL,$products_id,'storenvy_marketplace_id','" . $storenvy_marketplace_id . "',$shop_id),"
                    . " (NULL,$products_id,'storenvy_preorder','" . $storenvy_preorder . "',$shop_id),"
                    . " (NULL,$products_id,'storenvy_on_sale','" . $storenvy_on_sale . "',$shop_id) ";
                if (!in_array($storenvycollection, array('', NULL, 0, '0')))
                    $sql .= " ,(NULL,$products_id,'storenvy_collection_id','" . $storenvycollection . "',$shop_id) ";
                $wpdb->query($sql);
            }

            $wpdb->get_result("UPDATE `wp_users_products` SET `storenvyactive` = 1  WHERE `id` = $products_id");
            updateStorenvyVariants_pershop($POST, $storenvy_id, $products_id, $storenvytoken, $variants, $currentuserid, $shop_id);
            delete_default_variant_storenvy($storenvy_id, $storenvytoken);
            UpdateStorenvyCollection($POST, $storenvycollection, $storenvy_id, $storenvytoken, $products_id, $currentuserid, $shop_id);
            return $storenvy_id;
        }
    }
}

function updateStorenvyVariants_pershop($POST, $storenvy_id, $products_id, $storenvytoken, $variants, $currentuserid, $shop_id)
{
    global $wpdb;
    $all_vars = array();
    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "/variants.json?access_token=" . $storenvytoken;
    $errors = array();
    foreach ($variants as $variant) {
        $name = $variant['color_name'] . " (" . $variant['size_name'] . ")";
        $params = array(
            "name" => $name,
            "full_quantity" => 1000,
            "in_stock" => 1000,
            "sku" => $variant['sku'],
            "has_override_price" => true,
            "override_cents" => $variant['override_cents']
        );
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
        $request->addPostParameter($params);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();

        $body = $request->send()->getBody();
        $variantinfo = json_decode($body, TRUE);
        if (isset($variantinfo['error'])) {
            $errors[] = $variant['sku'] . ' : ' . $variantinfo['error'];
        }
        if (isset($variantinfo['meta']['error_message']) && $variantinfo['meta']['error_message'] != '') {
            $errors[] = $variant['sku'] . ': ' . $variantinfo['meta']['error_message'];
        }
        $storenvy_variant_id = $variantinfo['data']['id'];

        $this_size_id = $variant['size_id'];
        $this_color_id = $variant['color_id'];


        if ($storenvy_variant_id != "") {
            $variantid = $wpdb->get_var("select id from wp_users_products_colors where `color_id` = $this_color_id AND `size_id` = $this_size_id AND `users_products_id` = $products_id");
            $all_vars[] = array('variant_id' => $variantid, 'storenvy_id' => $storenvy_variant_id);
        } else {
            $error = "";
            $post = $POST;
            $post['errors'] = $body;
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $products_id;
            $export = var_export($post, true);
            $logs = array();
            $logs['variants'] = $variants;
            $logs['storenvy_body'] = mysql_escape_string($body);
            $logs['storenvy_response'] = $variantinfo;
            $logs['post'] = $POST;
            wp_insert_post(array(
                'post_content' => var_export($logs, true),
                'post_title' => esc_sql("Storenvy add variant Error: adding storenvy variant to database"),
                'post_status' => 'draft',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_type' => 'systems'
            ));
            if (strpos($body, "The server encountered an internal error") > 0) {
                $error = "A Storenvy service issue occurred";
            } else if (strpos($body, "Retry later") !== false) {
                $error = "Storenvy is experiencing heavy volume. Please try again in a few minutes";
            }
            if ($error != "")
                $errors[] = $error;
        }
    }
    $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
    $_tmp = array();
    foreach ($all_vars as $_var) {
        $_tmp[] = " (NULL,$products_id,'{$_var['variant_id']}','storenvy_id','{$_var['storenvy_id']}','$shop_id') ";
    }
    $sql_var .= implode(",", $_tmp);
    $wpdb->query($sql_var);
    if (!empty($errors)) {
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error update variants in storenvy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function UploadStorenvyStoreImages($POST, $images, $storenvytoken, $storenvy_id, $remove_old = 0)
{

    if ($remove_old == 1) {
        DeleteStorenvyImage($storenvytoken, $storenvy_id);
    }
    $path = "https://api.storenvy.com/v1/products/$storenvy_id/?access_token=" . $storenvytoken;
    $count = count($images);

    /*if ($count > 5) {
        for ($i = 5; $i < $count; $i++) {
            unset($images[$i]);
        }
    }*/
    $jsonencode['photos'] = array_map(function ($ar) {
        return $ar['src'];
    }, $images);

    $jsonencode = str_replace('\/', '/', json_encode($jsonencode));
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($jsonencode);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
}

function delete_default_variant_storenvy($storenvy_id, $storenvytoken)
{
    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "/?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $variantsar = json_decode($body, TRUE);
    $variantsar = $variantsar['data']['variants'];

    foreach ($variantsar as $thear) {

        if ($thear['variant']['name'] == "Default") {

            $storenvy_delete_id = $thear['variant']['id'];

            $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "/variants/$storenvy_delete_id/?access_token=" . $storenvytoken;
            $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
            $request->setConfig(array(
                'ssl_verify_peer' => FALSE,
                'ssl_verify_host' => FALSE));
            check_storenvy_throttle();
            $body = $request->send()->getBody();
            break;
        }
    }
}

function UpdateStorenvyCollection($POST, $storenvycollection, $storenvy_id, $storenvytoken, $products_id, $currentuserid, $shop_id)
{

// Collections
    $path = "https://api.storenvy.com/v1/collections.json?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $collections = json_decode($body, TRUE);
    $errors = array();
    foreach (explode(',', $storenvycollection) as $key => $theid) {

        $alreadyproducts = array();
        foreach ($collections['data']['collections'] as $thecollection) {
            if ($thecollection['id'] == $theid) {
                foreach ($thecollection['products'] as $theids) {
                    // Check if this product still exists since storenvy cant do their shit right
                    $path = "https://api.storenvy.com/v1/products/$theids?access_token=" . $storenvytoken;
                    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
                    $request->setConfig(array(
                        'ssl_verify_peer' => FALSE,
                        'ssl_verify_host' => FALSE));
                    $body = $request->send()->getBody();
                    $product = json_decode($body, TRUE);
                    if ($product['meta']['code'] !== 404) {
                        $alreadyproducts[] = $theids;
                    }
                }

                $alreadyproducts[] = $storenvy_id;
                // Update this Collection to add new product
                $path = "https://api.storenvy.com/v1/collections/$theid/?access_token=" . $storenvytoken;
                $jsonencode = array();
                $jsonencode['products'] = $alreadyproducts;
                $jsonencode = str_replace('\/', '/', json_encode($jsonencode));
                $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
                $newHeader = 'Content-type: application/json';
                $request->setHeader($newHeader);
                $request->setBody($jsonencode);
                $request->setConfig(array(
                    'ssl_verify_peer' => FALSE,
                    'ssl_verify_host' => FALSE));
                check_storenvy_throttle();
                $body = $request->send()->getBody();
                $collectioninfo = json_decode($body, TRUE);
                if (isset($collectioninfo['error'])) {
                    $errors[] = $collectioninfo['error'];
                }
                if (isset($collectioninfo['meta']['error_message']) && $variantinfo['meta']['error_message'] != '') {
                    $errors[] = $collectioninfo['meta']['error_message'];
                }
            }
        }
    }
    if (!empty($errors)) {
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error update collection in storenvy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }
}

function buildStorenvyVariants($variants, $old_storenvy_variants = array())
{

    $storenvy_variants_to_update = array();
    foreach ($variants as $variant) {
        $color_id = $variant['color_id'];
        $size_id = $variant['size_id'];
        $color_name = $variant['color_name'];
        $size_name = $variant['size_name'];
        $position = $variant['position'];
        $price = $variant['price'];
        $sku = $variant['sku'];
        $variant1 = array(
            "size_name" => $size_name,
            "color_name" => $color_name,
            "sku" => $sku,
            "has_override_price" => true,
            "override_cents" => $price * 100,
            "price" => $price,
            "position" => $position,
            "color_id" => $color_id,
            "size_id" => $size_id
        );
        if (array_key_exists($color_id . '_' . $size_id, $old_storenvy_variants)) {
            $storenvy_variants_to_update[] = $old_storenvy_variants[$color_id . '_' . $size_id]['id'];
            $variant1['id'] = $old_storenvy_variants[$color_id . '_' . $size_id]['id'];
            $variant1['old_sku'] = $old_storenvy_variants[$color_id . '_' . $size_id]['sku'];
            $variant1['old_cents'] = $old_storenvy_variants[$color_id . '_' . $size_id]['cents'];
        }
        $storenvy_variants[] = $variant1;
    }
    return array($storenvy_variants, $storenvy_variants_to_update);
}

function delete_variant_storenvy_pershop($storenvy_id, $variant_id, $storenvytoken, $shop_id)
{

    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "/variants/$variant_id/?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $response = json_decode($body);

    delete_variant_meta_value_shop('storenvy_id', $variant_id, $shop_id);
}

function updateStorenvyProduct($POST, $product_data, $storenvy_data, $currentuserid, $productid, $storenvy_product_id, $storenvytoken, $variants, $shop_id, $variant_to_delete = array())
{
    global $wpdb;
    $errors = array();
    @extract($product_data);
    @extract($storenvy_data);
    $storenvyprice = $POST['storenvyprice'];

    /************************************* add a default variant to product****************************/

    $path = "https://api.storenvy.com/v1/products/" . $storenvy_product_id . "/variants?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
    $request->addPostParameter(array(
        "name" => "tmpvrt",
        "full_quantity" => 1000,
        "in_stock" => 1000,
        "sku" => "tmp-vrt",
        "override_cents" => 2,
        "has_override_price" => true
    ));
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $variantinfo = json_decode($body, TRUE);
    $except_variant = $variantinfo['data']['id'];
    /*************************************delete unexisiting variants****************************/
    foreach ($variant_to_delete as $var)
        delete_variant_storenvy($storenvy_product_id, $var, $storenvytoken);
    /*************************************add new variants  to product****************************/
    $all_meta = array();
    $all_vars = array();
    //$all_vars_id = array();
    $results = $wpdb->get_results("select * from wp_variants_meta where shop_id= $shop_id and meta_key='storenvy_id' and product_id=$productid", ARRAY_A);
    foreach ($results as $res) {
        $all_meta[$res['variant_id']] = $res['id'];
    }
    $start = microtime(true);

    foreach ($variants as $variant) {
        $send_request = true;
        $name = $variant['color_name'] . " (" . $variant['size_name'] . ")";
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_product_id . "/variants.json?access_token=" . $storenvytoken;
        if (!isset($variant['id']) || $variant['id'] == 0 || $variant['id'] == "")
            $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
        else {
            if ($variant['old_sku'] != $variant['sku'] || $variant['old_cents'] != $variant['override_cents']) {
                $storenvy_variant_id = $variant['id'];
                $path = "https://api.storenvy.com/v1/products/$storenvy_product_id/variants/$storenvy_variant_id/?access_token=" . $storenvytoken;
                $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
            } else
                $send_request = false;
        }
        if ($send_request) {
            $newBody = '{"name" : "' . $name . '","full_quantity" : "1000","in_stock":"1000","sku" : "' . $variant['sku'] . '","override_cents": "' . (float)$variant['override_cents'] . '","has_override_price":"true"}';
            $newHeader = 'Content-type: application/json';
            $request->setHeader($newHeader);
            $request->setBody($newBody);
            $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
            check_storenvy_throttle();
            $body = $request->send()->getBody();
            $variantinfo = json_decode($body, TRUE);
            $arr[$name]['result'] = $variantinfo;
            if (isset($variantinfo['error'])) {
                $errors[] = $variant['sku'] . ' : ' . $variantinfo['error'];
            } else if (isset($variantinfo['meta']['error_message']) && $variantinfo['meta']['error_message'] != '') {
                $errors[] = $variant['sku'] . ' : ' . $variantinfo['meta']['error_message'];
            } else {
                $storenvy_variant_id = $variantinfo['data']['id'];
                $this_size_id = $variant['size_id'];
                $this_color_id = $variant['color_id'];
                if ($storenvy_variant_id != "") {
                    $variantid = $wpdb->get_var("select id from wp_users_products_colors where `color_id` = $this_color_id AND `size_id` = $this_size_id AND `users_products_id` = $productid");
                    // $meta_id = $wpdb->get_var("select id from wp_variants_meta where variant_id = $variantid and  ");
                    // update_variant_meta_shop($productid, $variantid, 'storenvy_id', $storenvy_variant_id, $shop_id);
                    $_tmp = array('variant_id' => $variantid, 'storenvy_id' => $storenvy_variant_id);
                    $_tmp['id'] = (isset($all_meta[$variantid])) ? $all_meta[$variantid] : 'NULL';
                    $all_vars[] = $_tmp;

                } else {
                    if (strpos($body, "The server encountered an internal error") > 0) {
                        $error = "A Storenvy service issue occurred";
                        $post = array_merge($product_data, $storenvy_data);
                        $post['errors'] = $error;
                        $post['user_id'] = $currentuserid;
                        $post['user_product_id'] = $productid;
                        $post['body'] = $body;
                        $export = var_export($post, true);
                        // return to manage products
                    } else if (strpos($body, "Retry later") !== false) {
                        $error = "Storenvy is experiencing heavy volume. Please try again in a few minutes";
                        $post = array_merge($product_data, $storenvy_data);
                        $post['errors'] = $error;
                        $post['user_id'] = $currentuserid;
                        $post['user_product_id'] = $productid;
                        $post['body'] = $body;
                        $export = var_export($post, true);
                    } else {
                        $logs = array();
                        $logs['storenvy_body'][] = $body;
                        $logs['storenvy_info'] = $variantinfo;
                        wp_insert_post(array(
                            'post_content' => var_export($logs, true),
                            'post_title' => esc_sql("could  not update storenvy variant  id (YES then YES) "),
                            'post_status' => 'draft',
                            'comment_status' => 'closed',
                            'ping_status' => 'closed',
                            'post_type' => 'systems'
                        ));
                    }
                    if ($error != "") {
                        $errors[] = $error;
                    }
                }
            }
        }
    }

    $sql_var = "insert into wp_variants_meta (id,product_id,variant_id,meta_key,meta_value,shop_id) values ";
    $_tmp = array();
    foreach ($all_vars as $_var) {
        $_tmp[] = " ({$_var['id']},'$productid','{$_var['variant_id']}','storenvy_id','{$_var['storenvy_id']}','$shop_id') ";

    }
    $sql_var .= implode(",", $_tmp) . " on duplicate key update variant_id = values(variant_id),meta_key=values(meta_key),meta_value=values(meta_value),shop_id=values(shop_id)";
    $wpdb->query($sql_var);

    /************************************* delete default  variant****************************/
    delete_variant_storenvy($storenvy_product_id, $except_variant, $storenvytoken);

    if (!empty($errors)) {
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error update variants in storenvy ' . $shop_text . ':';
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    }

    $path = "https://api.storenvy.com/v1/collections.json?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();

    $body = $request->send()->getBody();
    $collections = json_decode($body, TRUE);

    $path = "https://api.storenvy.com/v1/products/$storenvy_product_id?access_token=$storenvytoken";
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $product = json_decode($body, TRUE);
    $storenvycollectionold = $product['data']['collections'];

    $storenvy_collection_arr = explode(",", $storenvycollection);
    $removed_collections = array_diff($storenvycollectionold, $storenvy_collection_arr);
    //DELETE TEMP VARIANT

    /*******************Add New Collection To Storenvy*****************/
    $errors = array();
    if (!empty($storenvy_collection_arr)) {
        foreach ($storenvy_collection_arr as $theid) {
            $alreadyproducts = array();
            foreach ($collections['data']['collections'] as $thecollection) {
                if ($thecollection['id'] == $theid) {
                    foreach ($thecollection['products'] as $theids) {
                        // Check if this product still exists since storenvy cant do their shit right
                        $path = "https://api.storenvy.com/v1/products/$theids?access_token=" . $storenvytoken;
                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
                        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();

                        $body = $request->send()->getBody();
                        $product = json_decode($body, TRUE);
                        if ($product['meta']['code'] != 404) {
                            $alreadyproducts[] = $theids;
                        }
                    }

                    $alreadyproducts[] = $storenvy_product_id;
                    $alreadyproducts = array_values(array_unique($alreadyproducts));

                    // Update this Collection to add new product
                    $path = "https://api.storenvy.com/v1/collections/$theid/?access_token=" . $storenvytoken;
                    $jsonencode = array();
                    $jsonencode['products'] = $alreadyproducts;
                    $jsonencode = str_replace('\/', '/', json_encode($jsonencode));
                    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
                    $newHeader = 'Content-type: application/json';
                    $request->setHeader($newHeader);
                    $request->setBody($jsonencode);
                    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                    check_storenvy_throttle();

                    $body = $request->send()->getBody();
                    $collectioninfo = json_decode($body, TRUE);
                    if (isset($collectioninfo['error']))
                        $errors[] = $collectioninfo['error'];
                    if (isset($collectioninfo['meta']['error_message']) && $variantinfo['meta']['error_message'] != '')
                        $errors[] = $collectioninfo['meta']['error_message'];
                }
            }
        }

        if (!empty($errors)) {
            $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
            $error_title = 'Error Add New Collections To Storenvy ' . $shop_text . ':';
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
    }

    /**************************Remove old Collection from Storenvy**********************************/
    $errors = array();
    if (!empty($removed_collections)) {
        foreach ($removed_collections as $theid) {
            $alreadyproducts = array();
            foreach ($collections['data']['collections'] as $thecollection) {
                if ($thecollection['id'] == $theid) {
                    foreach ($thecollection['products'] as $prodid) {
                        $path = "https://api.storenvy.com/v1/products/$prodid?access_token=" . $storenvytoken;
                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
                        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();
                        $body = $request->send()->getBody();
                        $product = json_decode($body, TRUE);
                        if ($product['meta']['code'] != 404) {
                            if ($product['data']['id'] != $storenvy_product_id && !in_array($prodid, $alreadyproducts))
                                $alreadyproducts[] = $prodid;
                        }
                    }
                    $path = "https://api.storenvy.com/v1/collections/$theid/?access_token=" . $storenvytoken;

                    if (!empty($alreadyproducts)) {
                        $jsonencode = array();
                        $jsonencode['products'] = $alreadyproducts;
                        $jsonencode = str_replace('\/', '/', json_encode($jsonencode));
                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
                        $newHeader = 'Content-type: application/json';
                        $request->setHeader($newHeader);
                        $request->setBody($jsonencode);
                        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();

                        $body = $request->send()->getBody();
                        $collectioninfo = json_decode($body, TRUE);
                        if (isset($collectioninfo['error'])) {
                            $errors[] = $collectioninfo['error'];
                        }
                        if (isset($collectioninfo['meta']['error_message']) && $collectioninfo['meta']['error_message'] != '') {
                            $errors[] = $collectioninfo['meta']['error_message'];
                        }
                    } else {
                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
                        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();
                        $body = $request->send()->getBody();
                        $body = json_decode($body);
                        $collect_name = $body->data->name;
                        $collect_description = $body->data->description;

                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
                        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();

                        $body = $request->send()->getBody();
                        /***********create new collection************/
                        $path = "https://api.storenvy.com/v1/collections.json?access_token=" . $storenvytoken;
                        $params = array("name" => str_replace('"', '\"', stripslashes($collect_name)), "description" => str_replace('"', '\"', stripslashes($collect_description)));
                        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_POST);
                        $request->addPostParameter($params);
                        $request->setConfig(array(
                            'ssl_verify_peer' => FALSE,
                            'ssl_verify_host' => FALSE));
                        check_storenvy_throttle();
                        $request->send()->getBody();
                    }
                }
            }
        }
        if (!empty($errors)) {
            $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
            $error_title = 'Error incheck collection storenvy ' . $shop_text . ':';
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }
    }

    // Update the rest of details
    $pricecents = round($storenvyprice * 100);
    $path = "https://api.storenvy.com/v1/products/$storenvy_product_id/?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);

    $preorder = ($storenvy_preorder == 1) ? true : false;
    $onsale = ($storenvy_on_sale == 1) ? true : false;
    $description = str_replace(array('<br>', '<br />'), array("\n", "\n"), $description);
    $description = strip_tags($description);


    $newBody = '{"name" : "' . str_replace('"', '\"', stripslashes($title)) . '","description" : "' . $description . '","tag_list":"' . $tags . '","shipping_group_id" : "' . $storenvy_shipping_id . '","cents": "' . $pricecents . '","preorder":"' . $preorder . '","on_sale":"' . $onsale . '","marketplace_category_id":"' . $storenvy_marketplace_id . '"}';
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($newBody);

    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $productinfo = json_decode($body, TRUE);

    if ($productinfo == NULL) {
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
        $params = array(
            "name" => str_replace('"', '\"', stripslashes($title)),
            "cents" => $pricecents,
            "description" => $description,
            "shipping_group_id" => $storenvy_shipping_id,
            "preorder" => $preorder,
            "on_sale" => $onsale,
            "marketplace_category_id" => $storenvy_marketplace_id,
            "tag_list" => $tags
        );
        $request->addPostParameter($params);
        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        $productinfo = json_decode($body, TRUE);
    }

    if (isset($productinfo['error'])) {
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = $productinfo['error'];
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title);
        echo json_encode($return);
        exit();
    } else if (isset($productinfo['meta']['error_message']) && $productinfo['meta']['error_message'] != "") {
        $errors = array();
        $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
        $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
        $error_title = 'Error add product in storenvy ' . $shop_text . ': ' . $productinfo['meta']['error_type'];
        $errors[] = $productinfo['meta']['error_message'];
        $_SESSION['data'] = $POST;
        $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
        echo json_encode($return);
        exit();
    } else {
        $storenvy_return_id = $productinfo['data']['id'];
        if ($storenvy_return_id == "") {
            $errors = array();
            if (strpos($body, "The server encountered an internal error") !== false)
                $error = "A Storenvy service issue occurred";
            else if (strpos($body, "Retry later") !== false) {
                $error = "Storenvy is experiencing heavy volume. Please try again in a few minutes";
            } else
                $error = "Your changes to storenvy were rejected. Please check your data";

            $post = array_merge($product_data, $storenvy_data);
            $post['errors'] = $error;
            $post['user_id'] = $currentuserid;
            $post['user_product_id'] = $productid;
            $post['body'] = $body;

            $export = var_export($post, true);
            $count_shop = check_list_Storenvy_Shops($currentuserid, 2);
            $shop_text = ($count_shop == 1) ? '' : 'for "' . $wpdb->get_var("select shop from wp_users_storenvy where id=$shop_id") . '"';
            $error_title = 'Error edit product in storenvy ' . $shop_text . ':';
            $errors[] = $error;
            $_SESSION['data'] = $POST;
            $return = array("status" => 0, "error_title" => $error_title, 'errors' => $errors);
            echo json_encode($return);
            exit();
        }

        $pa_product_id_old = get_product_id_meta_shop($storenvy_product_id, "storenvy_id", $shop_id);
        if ($pa_product_id_old && $pa_product_id_old != $productid)
            delete_variants_product_meta_shop($pa_product_id_old, 'storenvy_id', $shop_id);

        $all_meta = array('storenvy_id' => 'NULL', 'storenvyprice' => 'NULL', 'storenvy_collection_id' => 'NULL', 'storenvy_shipping_id' => 'NULL', 'storenvy_marketplace_id' => 'NULL', 'storenvy_preorder' => 'NULL', 'storenvy_on_sale' => 'NULL');
        $results = $wpdb->get_results("select * from `wp_products_meta` where product_id = $productid and shopid = $shop_id", ARRAY_A);
        foreach ($results as $res) {
            $all_meta[$res['meta_key']] = $res['meta_id'];
        }

        $prod_to_deconnect = ($pa_product_id_old && $pa_product_id_old != $productid) ? $pa_product_id_old : 0;
        if ($prod_to_deconnect) {
            update_product_meta_shop($productid, 'storenvy_id', $storenvy_product_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'storenvyprice', $pricecents, $shop_id, 0, $prod_to_deconnect);
            if (!in_array($storenvycollection, array('', NULL, 0, '0')))
                update_product_meta_shop($productid, 'storenvy_collection_id', $storenvycollection, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'storenvy_shipping_id', $storenvy_shipping_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'storenvy_marketplace_id', $storenvy_marketplace_id, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'storenvy_preorder', $storenvy_preorder, $shop_id, 0, $prod_to_deconnect);
            update_product_meta_shop($productid, 'storenvy_on_sale', $storenvy_on_sale, $shop_id, 0, $prod_to_deconnect);

        } else {

            $sql4581 = "insert into `wp_products_meta` (meta_id,product_id,meta_key,meta_value,shopid) values ({$all_meta['storenvy_id']},$productid,'storenvy_id','$storenvy_product_id',$shop_id),"
                . " ({$all_meta['storenvyprice']},$productid,'storenvyprice','" . $pricecents . "',$shop_id),"
                . " ({$all_meta['storenvy_collection_id']},$productid,'storenvy_collection_id','" . $storenvycollection . "',$shop_id),"
                . " ({$all_meta['storenvy_shipping_id']},$productid,'storenvy_shipping_id','" . $storenvy_shipping_id . "',$shop_id), "
                . " ({$all_meta['storenvy_marketplace_id']},$productid,'storenvy_marketplace_id','" . $storenvy_marketplace_id . "',$shop_id),"
                . " ({$all_meta['storenvy_preorder']},$productid,'storenvy_preorder','" . $storenvy_preorder . "',$shop_id),"
                . " ({$all_meta['storenvy_on_sale']},$productid,'storenvy_on_sale','" . $storenvy_on_sale . "',$shop_id) "

                . "ON DUPLICATE KEY UPDATE product_id = VALUES(product_id),meta_key=values(meta_key),meta_value = values(meta_value),shopid=values(shopid)";
            //mail('team@ryankikta.com','sql str',$sql4581);

            $wpdb->query($sql4581);
        }
        $sql = "UPDATE `wp_users_products` SET `storenvyactive` = 1  WHERE `id` = $productid";
        $query = $wpdb->get_result($sql);
    }
}

function getCurrentStorenvyData_perShop($shop_id, $product_id)
{
    $storenvy_product_query = $wpdb->get_result("SELECT meta_key,meta_value FROM `wp_products_meta` WHERE `product_id` = $product_id and `shopid`=$shop_id ");
    $current_storenvy_on_sale = 0;
    $current_storenvy_preorder = 0;
    $current_storenvycollection = '';
    $current_storenvy_marketplace_id = 0;
    $current_storenvy_shipping_id = 0;
    $current_storenvy_price = 0;

    while ($row = mysql_fetch_array($storenvy_product_query)) {
        if ($row['meta_key'] == 'storenvy_id') {
            $current_storenvy_id = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvy_on_sale') {
            $current_storenvy_on_sale = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvy_preorder') {
            $current_storenvy_preorder = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvy_collection_id') {
            $current_storenvycollection = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvy_marketplace_id') {
            $current_storenvy_marketplace_id = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvy_shipping_id') {
            $current_storenvy_shipping_id = $row['meta_value'];
        }
        if ($row['meta_key'] == 'storenvyprice') {
            $current_storenvy_price = $row['meta_value'] / 100;
        }
    }
    $storenvy_data = array(
        'storenvy_id' => $current_storenvy_id,
        'storenvy_on_sale' => $current_storenvy_on_sale,
        'storenvy_preorder' => $current_storenvy_preorder,
        'storenvycollection' => $current_storenvycollection,
        'storenvy_marketplace_id' => $current_storenvy_marketplace_id,
        'storenvy_shipping_id' => $current_storenvy_shipping_id,
        'storenvyprice' => $current_storenvy_price,
    );
    return $storenvy_data;
}

function getStorenvyProductData($storenvy_id, $storenvytoken)
{

    global $wpdb;
    $update_storenvy = 0;
    $storenvy_variants = array();

    if ($storenvy_id != 0 && $storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        wp_mail('team@ryankikta.com', 'pr data', var_export($body, truef));
        $product = json_decode($body, TRUE);

        if ($product['meta']['code'] == 200) {
            $update_storenvy = 1;
            $all_variants = $product['data']['variants'];
            foreach ($all_variants as $var) {
                $row = $wpdb->get_row("select color_id,size_id from wp_users_products_colors where storenvy_id = " . $var['variant']['id'], ARRAY_A);
                if (is_array($row) && !empty($row)) {
                    $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['id'] = $var['variant']['id'];
                    $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['sku'] = $var['variant']['sku'];
                    $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['cents'] = $var['variant']['cents'];
                }
            }
        }
    }
    return array($update_storenvy, $storenvy_variants);
}

function getStorenvyProductDataPerShop($product_id, $storenvy_id, $storenvytoken, $shopid)
{
    global $wpdb;
    $update_storenvy = 0;
    $storenvy_variants = array();
    $storenvy_variants_ids = array();

    if ($storenvy_id != 0 && $storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        $product = json_decode($body, TRUE);
        if ($product['meta']['code'] == 200) {
            $update_storenvy = 1;
            $all_variants = $product['data']['variants'];
            foreach ($all_variants as $var) {
                $variant_id = $var['variant']['id'];
                $variant_sku = $var['variant']['sku'];
                $pa_variant_id = get_pa_variant_meta_shop($product_id, 'storenvy_id', $variant_id, $shopid);
                $storenvy_variants_ids[] = $var['variant']['id'];
                if ($pa_variant_id && $pa_variant_id > 0) {
                    $row = $wpdb->get_row("select color_id,size_id from wp_users_products_colors where id = " . $pa_variant_id, ARRAY_A);
                    if (is_array($row) && !empty($row)) {
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['id'] = $variant_id;
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['sku'] = $variant_sku;
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['cents'] = $var['variant']['cents'];
                    }
                } else {
                    $row = $wpdb->get_row("select color_id,size_id from wp_users_products_colors where sku='$variant_sku' and storenvy_id=$variant_id and users_products_id=$product_id", ARRAY_A);
                    if (is_array($row) && !empty($row)) {
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['id'] = $variant_id;
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['sku'] = $variant_sku;
                        $storenvy_variants[$row['color_id'] . '_' . $row['size_id']]['cents'] = $var['variant']['cents'];
                    }
                }
            }
        }
    }
    return array($update_storenvy, $storenvy_variants, $storenvy_variants_ids);
}

function deleteStorenvyProductPerShop($storenvy_id, $token, $product_id = 0, $shop_id)
{
    $path = "https://api.storenvy.com/v1/products/$storenvy_id/?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $result = json_decode($body, TRUE);
    if ($product_id != 0) {
        $prodid = get_product_id_meta_shop($storenvy_id, "storenvy_id", $shop_id);
        if (!$prodid)
            $shop_id = 0;
        delete_product_meta_multi_shop($product_id, "'storenvy_id','storenvyprice','storenvy_collection_id','storenvy_shipping_id','storenvy_marketplace_id','storenvy_preorder','storenvy_on_sale'", $shop_id);
        delete_variants_product_meta_shop($product_id, 'storenvy_id', $shop_id);
    }
}

function get_storenvy_product_import($user_id, $storenvy_id, $shop_id = 0)
{

    $data = array();
    $storenvytoken = ($shop_id != 0) ? getStorenvyShopById($shop_id) : getStorenvyShop($user_id);
    $shop_name = getStorenvyShopName($user_id, $shop_id);
    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $res = json_decode($request->send()->getBody(), TRUE);
    $res = $res["data"];

    $tags = "";
    foreach ($res["tag_list"] as $tag) {
        $tags .= $tag . ",";
    }
    $tags = rtrim($tags, ",");
    $preorder = ($res["preorder"]) ? 1 : 0;
    $on_sale = ($res["on_sale"]) ? 1 : 0;
    $collects = null;
    if (count($res["collections"]) > 0) {
        foreach ($res["collections"] as $collect) {
            $collects .= $collect . ",";
        }
        $collects = rtrim($collects, ",");
    }

    $images = array();
    foreach ($res['photos'] as $img) {
        $img_src = trim($img['photo']['original'], "//");
        $images[] = "https://" . $img_src;
    }

    $colors = array();
    foreach ($res['variants'] as $variant) {
        $variant_name = $variant["variant"]['name'];
        $variant_name = rtrim(substr_replace($variant_name, "", strpos($variant_name, "(")), " ");
        if (get_color_id($variant_name) != NULL) {
            $colors[$variant_name][] = $variant["variant"]["price"];
        }
    }

    $shop_colors = array();
    foreach ($colors as $color => $prices) {
        $shop_colors[$color][] = min($prices);
        $shop_colors[$color][] = max($prices);
    }

    $data = array(
        'title' => $res['name'],
        'description' => $res['description'],
        'tags' => $tags,
        'storenvyprice' => $res['price'],
        'storenvy_marketplace_id' => $res['marketplace_category_id'],
        'storenvy_on_sale' => $on_sale,
        'storenvy_preorder' => $preorder,
        'storenvycollection' => $collects,
        'storenvy_shipping_id' => $res["shipping_group"]["id"],
        'shop_images' => $images,
        'shop_colors' => $shop_colors,
        'shop_id' => $shop_id,
        'shop_name' => $shop_name,
    );
    return $data;
}

function get_storenvy_old_variants($user_id, $storenvy_id, $shop_id = 0)
{
    global $wpdb;
    $data = array();
    $storenvytoken = getStorenvyShopById($shop_id);
    $shop_name = getStorenvyShopName($user_id, $shop_id);
    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $res = json_decode($request->send()->getBody(), TRUE);
    $res = $res["data"];

    $storenvy_old_variants = array();

    foreach ($res['variants'] as $variant) {
        $variant_name = $variant["variant"]['name'];
        //$variant_name = str_replace(array("(",")"),"", $variant_name);
        $tmp = explode("(", $variant_name);
        $color_name = trim($tmp[0]);
        $size_name = trim(str_replace(")", "", $tmp[1]));
        $colors_id = $wpdb->get_results('select color_id from wp_rmproductmanagement_colors where color_name="' . $color_name . '"');
        $sizes_id = $wpdb->get_results('select size_id from wp_rmproductmanagement_sizes where size_name="' . $size_name . '"');
        foreach ($colors_id as $color) {
            $color_id = $color->color_id;
            foreach ($sizes_id as $size) {
                $size_id = $size->size_id;
                $storenvy_old_variants[$color_id . '_' . $size_id]['id'] = $variant["variant"]['id'];
                $storenvy_old_variants[$color_id . '_' . $size_id]['sku'] = $variant["variant"]['sku'];
                $storenvy_old_variants[$color_id . '_' . $size_id]['cents'] = $variant["variant"]['cents'];


            }
        }
    }


    //wp_mail('team@ryankikta.com','old variants',var_export($storenvy_old_variants,true));
    return $storenvy_old_variants;
}

function get_all_storenvy_shops($user_id)
{
    global $wpdb;
    return $wpdb->get_results("select `id`,`shop`,`dateadded`,`active` from `wp_users_storenvy` where `users_id` = $user_id");
}

function DeleteStorenvyImage($storenvytoken, $storenvy_id)
{

    $path = "https://api.storenvy.com/v1/products/$storenvy_id/?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_PUT);
    $newBody = '{"photos" : [{}]}';
    $newHeader = 'Content-type: application/json';
    $request->setHeader($newHeader);
    $request->setBody($newBody);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
}

function get_storenvy_product_data_pershop($storenvy_id, $storenvytoken)
{

    global $wpdb;
    $product = array();
    if ($storenvy_id != 0 && $storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        $product = json_decode($body, TRUE);
    }
    return $product;
}

function displayStorenvyNoticeBox_pershop($shopid)
{
    if (isset($shopid)) {
        if (!check_storenvy_call_api_pershop($shopid)) {
            ?>
            <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;margin-bottom: 20px;">
                <strong>RyanKikta can no longer access your Storenvy Shop </strong> Please click <a class="confirmshop"
                                                                                                    href="/storenvy-multishop/?action=deleteshop&id=<?php echo $shopid; ?>">here</a>
                to re-authorize your shop.
            </div>
            <?php
        }
    }
}

function get_storenvy_product_data($storenvy_id, $storenvytoken)
{

    global $wpdb;
    $product = array();
    if ($storenvy_id != 0 && $storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "?access_token=$storenvytoken";
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        $product = json_decode($body, TRUE);
    }
    return $product;
}

function getStorenvyData($data)
{

    $storenvyactive = esc_sql($data['storenvyactive']);
    $storenvyprice = esc_sql($data['storenvyprice']);
    $str_marketplace_id = esc_sql($data['storenvy_marketplace_id']);
    $storenvy_preorder = esc_sql($data['storenvy_preorder']);
    $storenvy_on_sale = esc_sql($data['storenvy_on_sale']);
    if ($str_marketplace_id == "")
        $str_marketplace_id = 0;
    if ($storenvy_preorder == "")
        $storenvy_preorder = 0;
    if ($storenvy_on_sale == "")
        $storenvy_on_sale = 0;


    $storenvycollection = "";
    foreach ($data['storenvycollection'] as $collection) {
        $storenvycollection .= $collection . ",";
    }
    $storenvycollection = rtrim($storenvycollection, ",");

    $storenvyshipping = esc_sql($data['storenvy_shipping_id']);

    return array('storenvyactive' => $storenvyactive, 'storenvyprice' => $storenvyprice, 'storenvy_marketplace_id' => $str_marketplace_id,
        'storenvycollection' => $storenvycollection, 'storenvy_shipping_id' => $storenvyshipping, 'storenvy_on_sale' => $storenvy_on_sale, 'storenvy_preorder' => $storenvy_preorder);
}

function get_all_product_storenvy($storenvytoken, $next_path = "")
{
    $products = array();
    if ($storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/products/?access_token=$storenvytoken";
        if ($next_path != "")
            $path = $next_path;
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        check_storenvy_throttle();
        $body = $request->send()->getBody();
        $products = json_decode($body, TRUE);
    }
    return $products;
}

function deleteStorenvyProduct($storenvy_id, $token, $shop_id, $product_id = 0)
{

    $path = "https://api.storenvy.com/v1/products/$storenvy_id/?access_token=" . $token;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
    $variantinfo = json_decode($body, TRUE);
    if ($product_id != 0) {
        $prodid = get_product_id_meta_shop($storenvy_id, "storenvy_id", $shop_id);
        if (!$prodid)
            $shop_id = 0;
        delete_product_meta_multi_shop($product_id, "'storenvy_id','storenvyprice','storenvy_collection_id','storenvy_shipping_id','storenvy_marketplace_id','storenvy_preorder','storenvy_on_sale'", $shop_id);
        delete_variants_product_meta_shop($product_id, 'storenvy_id', $shop_id);
    }
}

function delete_storenvy_product_from_all($productid)
{
    global $wpdb;
    $shop_array = get_product_meta_shops($productid, "storenvy_id");
    if (!$shop_array) {
        $user_id = $wpdb->get_var("select users_id from wp_users_products where id=$productid");
        $shop_array = array($wpdb->get_var("select id from wp_users_storenvy where users_id=$user_id"));
    }
    if (isset($shop_array) && (count($shop_array) > 0)) {
        foreach ($shop_array as $shop_id) {
            $storenvytoken = getStorenvyShopById($shop_id);
            $storenvy_id = get_product_meta_shop($productid, "storenvy_id", $shop_id);
            if (!$storenvy_id)
                $storenvy_id = $wpdb->get_var("select storenvy_id from wp_users_products where id=$productid");
            deleteStorenvyProduct($storenvy_id, $storenvytoken, $shop_id);
        }
    }
}

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

function delete_variant_storenvy($storenvy_id, $variant_id, $storenvytoken)
{

    $path = "https://api.storenvy.com/v1/products/" . $storenvy_id . "/variants/$variant_id/?access_token=" . $storenvytoken;
    $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
    $request->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE));
    check_storenvy_throttle();
    $body = $request->send()->getBody();
}

function delete_storenvy_variant($product_id, $storenvy_prod_id, $user_id, $color_id, $size_id, $token, $variant_id)
{
    global $wpdb;
    if ($variant_id != 0) {
        $path = "https://api.storenvy.com/v1/products/" . $storenvy_prod_id . "/variants/" . $variant_id . "?access_token=" . $token;
        $request = new HTTP_Request2($path, HTTP_Request2::METHOD_DELETE);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE));
        $body = $request->send()->getBody();
        $response = json_decode($body);
        if ($response->meta->code !== 200) {
            $error['type'] = $response->meta->error_type;
            $error['message'] = $response->meta->error_message;
        }
    }
}

function displayStorenvyNoticeBox($user_id)
{
    if (!check_storenvy_call_api($user_id)) {
        ?>
        <div style="border: 1px solid #FF002B; padding: 10px; background-color: #FAE3E7;width: 95%;margin-bottom: 20px;">
            <strong>RyanKikta can no longer access your Storenvy Shop </strong> Please click <a
                    href="/storenvy?action=deleteshop">here</a> to re-authorize your shop.
        </div>
        <?php
    }
}

function check_storenvy_call_api($user_id)
{

    $storenvytoken = getStorenvyShop($user_id);
    if ($storenvytoken != "") {
        $path = "https://api.storenvy.com/v1/store.json?access_token=" . $storenvytoken;
        $res = send_request($path, 'GET');
        if ($res == null)
            return false;
    }
    return true;
}

//stoenvy request limit handler
function check_storenvy_throttle()
{
    $now = time();
    if ($_SESSION['str_first_call_time'] == "")
        $_SESSION['str_first_call_time'] = $now;
    $lastcall_time = ($_SESSION['str_first_call_time'] != "") ? $_SESSION['str_first_call_time'] : $now;
    $diff_time = $now - $lastcall_time;
    if ($diff_time >= 59) {
        $_SESSION['str_first_call_time'] = time();
        $_SESSION['str_total_call'] = 1;
    } else {
        $total_call = ($_SESSION['str_total_call'] != "") ? $_SESSION['str_total_call'] : 1;
        if ($total_call >= 98) {
            sleep(10);
            $_SESSION['str_first_call_time'] = time() - 12;
            $_SESSION['str_total_call'] = $total_call - 10;
        } else {
            $total_call++;
            $_SESSION['str_total_call'] = $total_call;
        }
    }
}

/***************************************************Order functions*************************/

function get_storenvy_shop($user_id)
{
    global $wpdb;
    return $wpdb->get_var("select shop from wp_users_storenvy where users_id=$user_id");
}

function get_storenvy_shop_per_shop($user_id, $shop_id)
{
    global $wpdb;
    return $wpdb->get_var("select shop from wp_users_storenvy where users_id=$user_id and id=$shop_id");
}

function get_order_storenvy_data($json)
{
    $your_order_id = $json['id'];
    if ($json['address']['country'] == "US") {
        $shipping_id = 1;
    } elseif ($json['address']['country'] == "CA") {
        $shipping_id = 2;
    } else {
        $shipping_id = 3;
    }

    return array('order_id' => $your_order_id, 'customerphone' => '', 'shipping_id' => $shipping_id);
}

function get_all_item_storenvy($json)
{
    global $wpdb;
    $items = array();
    $user_id = 0;
    foreach ($json['items'] as $key => $value) {
        $product = array();
        $item_id = $value['item']['id'];
        $order_product_id = $value['item']['product_id'];
        $item_sku = $value['item']['sku'];
        $variant_id = $value['item']['variant_id'];
        $item_price = ($value['item']['price_in_cents']) / 100;
        $quantity = $value['item']['quantity'];
        $total_quantity = 0;
        $product = $wpdb->get_results("select id,users_id,brand_id,product_id,front,back from wp_users_products where storenvy_id = $order_product_id order by id asc", ARRAY_A);
        if ($product) {
            $product = end($product);
            $pa_product_id = $product['id'];
            $product_id = $product['product_id'];
            $brand_id = $product['brand_id'];
            $hasfront = $product['front'];
            $hasback = $product['back'];
            if ($pa_product_id > 0) {
                // Let's find the variant id from sku title
                $user_id = $product['users_id'];
                $query = $wpdb->get_result("select color_id,size_id from wp_users_products_colors where storenvy_id = $variant_id or ( users_products_id = $pa_product_id and sku ='" . $item_sku . "')");
                $row = mysql_fetch_array($query);
                $color_id = $row['color_id'];
                $size_id = $row['size_id'];
                $items[] = array('item_id' => $item_id, 'variant_id' => $variant_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
            }
        } else {
            $pa_variants = $wpdb->get_row("select users_products_id,color_id,size_id from wp_users_products_colors where (storenvy_id=$variant_id or sku='$item_sku')");
            $pa_product_id = $pa_variants->users_products_id;
            $color_id = $pa_variants->color_id;
            $size_id = $pa_variants->size_id;
            $products_fb = $wpdb->get_results("select type from wp_users_products_images where type<>4 and users_products_id=$pa_product_id order by type asc");
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
                $items[] = array('item_id' => $item_id, 'variant_id' => $variant_id, 'pa_product_id' => $pa_product_id, 'product_id' => $product_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
        }
    }
    if (count($items) == 1) {
        if ($product_id != 0) {
            $shippin_id1 = $wpdb->get_var("select shipping_id from wp_rmproductmanagement where inventory_id=" . $product_id);
            if ($shippin_id1 == 1 || $shippin_id1 == 11 || $shippin_id1 == 12)
                $items[0]['only_shirts'] = true;
            else
                $items[0]['only_shirts'] = false;
        }
    }
    return $items;
}

function get_all_item_storenvy2($json)
{
    $items = array();
    foreach ($json['items'] as $item) {
        $item_id = $item['item']['id'];
        $variant_id = $item['item']['variant_id'];
        $items[$item_id] = $variant_id;
    }
    return $items;
}

function storenvy_Shipping_address($json, $all_countries)
{

    $shippingaddress1 = array();
    $shippingaddress1['clientname'] = trim($json['address']['name']);
    $shippingaddress1['address1'] = $json['address']['address_1'];
    $shippingaddress1['address2'] = $json['address']['address_2'];
    $shippingaddress1['city'] = $json['address']['city'];
    $shippingaddress1['state'] = $json['address']['state'];
    $shippingaddress1['zipcode'] = $json['address']['postal'];
    $shippingaddress1['country'] = $json['address']['country'];
    $address2 = ($shippingaddress1['address2'] != "") ? $shippingaddress1['address2'] : "";
    $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . "\n" . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $all_countries[$json['address']['country']];
    $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
    $shippingaddress1 = serialize($shippingaddress1);

    $shippingaddress_country = $json['address']['country'];

    return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $shippingaddress_country, 'shippingaddress_state' => $json['address']['state'], 'shippingaddress_state_code' => $json['address']['state_abbrev'], 'shippingaddress_zip' => $json['address']['postal'], 'paypal_address' => $paypal_address);
}

function get_all_storenvy_products($user_id, $shop_id = 0)
{
    global $wpdb;
    $storenvytoken = ($shop_id != 0) ? getStorenvyShopById($shop_id) : getStorenvyShop($user_id);
    $all_products = array();

    $request = new HTTP_Request2("https://api.storenvy.com/v1/products.json?access_token=" . $storenvytoken . "&after_id=0", HTTP_Request2::METHOD_GET);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));

    do {
        check_storenvy_throttle();
        $resp = json_decode($request->send()->getBody(), TRUE);
        foreach ($resp['data']['products'] as $product) {
            $pa_product_id = get_product_id_meta_shop($product["id"], 'storenvy_id', $shop_id);
            if (!$pa_product_id)
                $pa_product_id = $wpdb->get_var("select id from wp_users_products where storenvy_id = " . $product["id"]);
            $all_products[] = array(
                "id" => $product["id"],
                "title" => $product["name"],
                "status" => $product["status"],
                "url" => $product["storefront_url"],
                "image" => $product["photos"][0]["photo"]["original"],
                "imported" => ($pa_product_id != null) ? 1 : 0,
                "pa_id" => ($pa_product_id != null) ? $pa_product_id : 0
            );
        }

        $request = new HTTP_Request2($resp['pagination']["next_url"], HTTP_Request2::METHOD_GET);
        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    } while (!empty($resp['data']['products']));
    return $all_products;
}

function get_user_storenvy_orders($user_id)
{
    global $wpdb;

    $orders = array();
    $storenvytoken = $wpdb->get_var("select token from wp_users_storenvy where users_id = $user_id");
    if ($storenvytoken) {
        try {
            $request = new HTTP_Request2("https://api.storenvy.com/v1/orders.json?access_token=" . $storenvytoken . "&after_id=0", HTTP_Request2::METHOD_GET);
            $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
            $k = 0;
            do {
                check_storenvy_throttle();
                $resp = json_decode($request->send()->getBody(), TRUE);

                foreach ($resp['data']['orders'] as $order) {
                    $order_id = $wpdb->get_var("select count(order_id) from wp_rmproductmanagement_orders  where sourec=4 and external_id=" . $order["id"]);
                    //if(!$order_id)
                    $orders[] = $order;
                }

                $request = new HTTP_Request2($resp['pagination']["next_url"], HTTP_Request2::METHOD_GET);
                $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
                $k++;
            } while (!empty($resp['data']['orders']) || $k < 5);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    return $orders;
}

function analyze_user_storenvy_orders($user_id)
{
    global $wpdb;

    $orders = get_user_storenvy_orders($user_id);
    foreach ($orders as $order) {
        echo $order['id'] . '<br />';
        //if( $order['id'] != 15066901) continue;
        $data_to_send = json_encode($order);
        // exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        $headers = array(
            "Cache-Control: no-cache",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://api.ryankikta.com/storenvy_webhook.php?action=create');
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
        /*  foreach($order['items'] as $item){
          $product_id = $item['item']['product_id'];
          $variant_id = $item['item']['variant_id'];
          $sku        = $item['item']['sku'];
          $count      = $wpdb->get_var("select count(order_id) from wp_rmproductmanagement_orders where source=4 and external_id = ".$order['id']);
          echo  $count  .'<br />';
          if(!$count){
          echo $sku.'<br />';
          echo 'PA product _id '.$wpdb->get_var("select users_products_id from wp_users_products_colors where storenvy_id = $variant_id");
          echo 'product exists '.$wpdb->get_var("select id from wp_users_products where id = $product_id");

          }
          } */
    }
}

function getStorenvyProducts($storenvytoken)
{
    $all_products = array();

    $request = new HTTP_Request2("https://api.storenvy.com/v1/products.json?access_token=" . $storenvytoken . "&after_id=0", HTTP_Request2::METHOD_GET);
    $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));

    do {
        check_storenvy_throttle();
        $res = $request->send()->getBody();
        $resp = json_decode($res, TRUE);
        foreach ($resp['data']['products'] as $product) {
            $all_products[] = array('id' => $product['id'], 'title' => $product['name']);
        }

        $request = new HTTP_Request2($resp['pagination']["next_url"], HTTP_Request2::METHOD_GET);
        $request->setConfig(array('ssl_verify_peer' => FALSE, 'ssl_verify_host' => FALSE));
    } while (!empty($resp['data']['products']));
    return $all_products;
}

function check_product_existe_storenvy($storenvy_id, $shop_id, $user_id)
{
    global $wpdb;
    $existe = false;
    $pa_product_id = get_product_id_meta_shop($storenvy_id, "storenvy_id", $shop_id);
    if (!$pa_product_id)
        $pa_product_id = $wpdb->get_var("select id from wp_users_products where users_id=$user_id and storenvy_id=" . $storenvy_id);
    if ($pa_product_id)
        $existe = true;
    return array("status" => 200, "data" => $existe);
}

function getCurrentStorenvyData($prodid)
{
    $selectproductquery = $wpdb->get_result("SELECT * FROM `wp_users_products` WHERE `id` = $prodid");
    $row = mysql_fetch_assoc($selectproductquery);
    $storenvyactive = $row['storenvyactive'];
    $storenvy_id = $row['storenvy_id'];
    $storenvyprice = round($row['storenvyprice'] / 100, 2);
    $storenvy_collection_id = $row['storenvy_collection_id'];
    $storenvy_shipping_id = $row['storenvy_shipping_id'];
    $onsale = $row['storenvy_on_sale'];
    $preorder = $row['storenvy_preorder'];
    $storenvy_marketplace_id = $row['storenvy_marketplace_id'];
    $storenvy_collections = explode(",", $storenvy_collection_id);
    $storenvy_data = array(
        'storenvyactiveold' => $storenvyactive,
        'storenvy_id' => $storenvy_id,
        'storenvyprice' => $storenvyprice,
        'storenvy_marketplace_id' => $storenvy_marketplace_id,
        'storenvycollection' => $storenvy_collection_id,
        'storenvycollectionold' => $storenvy_collections,
        'storenvy_shipping_id' => $storenvy_shipping_id,
        'storenvy_on_sale' => $onsale,
        'storenvy_preorder' => $preorder
    );
    return $storenvy_data;
}

function refresh_storenvy_token($shop_id)
{
    global $wpdb;
    $users = $wpdb->get_results("select * from wp_users_storenvy where id =$shop_id ");
    foreach ($users as $user) {
        $refresh_token = $user->refresh_token;
        $shop_id = $user->id;


        $url = 'https://api.storenvy.com/oauth/token';
        $params = array(
            "refresh_token" => $refresh_token,
            "client_id" => Storenvy_Id,
            "client_secret" => Storenvy_Secret,
            "grant_type" => "refresh_token"
        );
        $request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
        $request->addPostParameter($params);
        $request->setConfig(array(
            'ssl_verify_peer' => FALSE,
            'ssl_verify_host' => FALSE
        ));

        $body = $request->send()->getBody();

        $responseObj = json_decode($body);
        $token = $responseObj->access_token;
        $refresh_token = $responseObj->refresh_token;
        if ($token != "" && $refresh_token != "")
            $wpdb->query("update wp_users_storenvy set active=1,token='$token',refresh_token='$refresh_token',dateupdated='" . date("Y-m-d H:i:s", time()) . "' where id=$shop_id");
    }
}

function refresh_all_users_token()
{
    global $wpdb;
    $last_year = date("Y-m-d H:i:s", strtotime("-1 year"));
    // debug($last_year);
    $shops = $wpdb->get_col("select id from wp_users_storenvy where dateadded < '$last_year' and users_id not in (83,479)");
    //debug($shops );exit;
    foreach ($shops as $shop_id) {
        refresh_storenvy_token($shop_id);
        //exit;
    }


}
