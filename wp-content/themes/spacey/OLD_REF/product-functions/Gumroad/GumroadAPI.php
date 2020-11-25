<?php

//require('GumroadCall.php');

class GumroadCall
{
    public static $con;

    public static function GetResponse($url, $header, $request_type, $data)
    {
        self::$con = curl_init();

        curl_setopt(self::$con, CURLOPT_URL, $url);
        curl_setopt(self::$con, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(self::$con, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt(self::$con, CURLOPT_HTTPHEADER, $header);
        if ($request_type == 'POST') {
            curl_setopt(self::$con, CURLOPT_POST, 1);
        } else {
            curl_setopt(self::$con, CURLOPT_CUSTOMREQUEST, $request_type);
        }

        curl_setopt(self::$con, CURLOPT_POSTFIELDS, $data);

        curl_setopt(self::$con, CURLOPT_RETURNTRANSFER, 1);
//wp_mail('team@ryankikta.com','return gumroad',var_export(curl_exec(self::$con),true));
        return curl_exec(self::$con);
    }

}

class GumroadAPI
{

    // ###################################################################################
    // This class contains all necessary function to communicate with GumroadAPI:
    // 1)-addnewShop : to add new shop gumroad to user
    // 2)-getShopbyUserId : get gumroad shop by userID
    // 3)-DeleteUserShop : delete gumroad shop by userID
    // 4)-ListProductsbyUser : list of products gumroad by userId
    // 5)-CreateGumroadProduct : create product gumroad 
    // 6)-getProductbyId : get product by productID
    // 7)-product_data : get data of product in add
    // 8)-GumroadData
    // 9)-UpdateGumroadProduct : update  product
    // 10)-DeleteGumroadProductCatgeory : delete product variants
    // 11)-CreateNewVariantCategory : create variants for products
    // 12)-DeleteProduct : to delete product from gumroad shop
    // 13)-storeSubscribeResource : subscribed you will be notified of the user's sales
    // 14)-getGmuroadUserId : get userId in gurmoad shop
    // 15)-gumroad_shipping_address : extract adresse shop from order
    // 16)-get_all_item_gumroad : extract data from order
    // ###################################################################################
    protected $clientID;
    protected $redirect_uri;
    protected $client_secret;

    public function __construct()
    {
        $this->clientID = "c59db4da2246901fa40e7dcb62807ec6d292ecdd63fd5e60ec73f448ccfc7343";
        $this->redirect_uri = "https://ryankikta.com/gumroad/";
        $this->client_secret = "bd3d076ce835e706d96edc4e0988f341ccb0af51d2bb34d463f88700cf21bb57";
    }

    // ###################################################################################
    // @function : addnewShop
    // @param :$code,$userId
    // ###################################################################################
    function addnewShop($code, $userId)
    {
        $resultat = array();
        try {
            $request_type = 'POST';
            $header = array();
            $datasent = array(
                "code" => $code,
                "redirect_uri" => $this->redirect_uri,
                "client_id" => $this->clientID,
                "client_secret" => $this->client_secret
            );
            $url = "https://gumroad.com/oauth/token";
            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
            $decode_response = json_decode($response, true);
            if (isset($decode_response["error"])) {
                if (isset($decode_response["message"])) {
                    $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "CodeInvalid"];
                }
            } elseif (isset($decode_response["access_token"])) {
                $token = $decode_response["access_token"];
                $addshop = $wpdb->get_result("INSERT INTO  wp_users_gumroad (users_id ,token) VALUES ('$userId','$token')");
                //error
                if (!$addshop) {
                    $resultat = ["statut" => 400, "error" => mysql_error()];
                } else {
                    $resultat = ["statut" => 200, "data" => $token];
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
    // @function : getShopbyUserId
    // @param :$userId
    // ###################################################################################


    function getShopbyId($id)
    {
        $resultat = array();
        $token = null;
        try {
            $shop = $wpdb->get_result("SELECT token FROM wp_users_gumroad WHERE id='$id'");
            $shop_num = $wpdb->num_rows($shop);

            if ($shop_num != 0) {
                $shoprow = $wpdb->get_row($shop);
                $token = $shoprow[0];
            }
            $resultat = ["statut" => 200, "data" => $token];
        } catch (Exception $ex) {
            $resultat = ["statut" => 400, "error" => $ex->getMessage()];
        }

        return $resultat;
    }

    // ###################################################################################
    // @function : DeleteUserShop
    // @param :$userId
    // ###################################################################################


    function DeleteUserShop($userId)
    {
        $resultat = array();
        $token = null;
        try {
            $shop = $wpdb->get_result("DELETE FROM wp_users_gumroad WHERE users_id='$userId'");

            if (!$shop) {
                $resultat = ["statut" => 400, "error" => mysql_error()];
            } else {
                $resultat = ["statut" => 200, "data" => "sucess"];
            }
        } catch (Exception $ex) {
            $resultat = ["statut" => 400, "error" => $ex->getMessage()];
        }

        return $resultat;
    }

    // ###################################################################################
    // @function : ListProductsbyUser
    // @param :$userId
    // ###################################################################################
    function ListProductsbyUser($shop_id)
    {
        $resultat = array();
        $token = null;
        try {
            $shop = $wpdb->get_result("SELECT token FROM wp_users_gumroad WHERE id='$shop_id'");
            $shop_num = $wpdb->num_rows($shop);

            if ($shop_num != 0) {
                $shoprow = $wpdb->get_row($shop);
                $token = $shoprow[0];
            }
            if (isset($token)) {
                $request_type = 'GET';
                $header = array();
                $datasent = array();
                $url = "https://api.gumroad.com/v2/products/?access_token=" . $token;
                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                // return $url;
                $decode_response = json_decode($response, true);
                if (isset($decode_response["error"])) {
                    if (isset($decode_response["message"])) {
                        $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                    } else {
                        $resultat = ["statut" => 400, "error" => "ProductsNotFound"];
                    }
                } elseif (isset($decode_response["products"])) {
                    $resultat = ["statut" => 200, "list" => $decode_response["products"]];
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
    // @function : CreateGumroadProduct
    // @param :$title,$description,$min_max_price,$token, $variants,$images,$products_id
    // ###################################################################################

    function CreateGumroadProduct($title, $description, $min_max_price, $token, $variants, $images, $products_id)
    {

        $resultat = array();


        try {

            if (isset($token)) {
                $request_type = 'POST';
                $header = array();
                if (isset($images)) {
                    if (count($images) > 0) {
                        if (isset($images[0])) {
                            $path_img = $images[0]["path"];
                            $url_img = $images[0]["src"];
                            $min_max_price = $min_max_price * 100;

                            $datasent = array(
                                "name" => $title,
                                "access_token" => $token,
                                "url" => $url_img,
                                "price" => $min_max_price,
                                "description" => $description,
                                "shown_on_profile" => true,
                                "preview" => '@' . $path_img,
                                "require_shipping" => true,
                            );

                            $url = "https://api.gumroad.com/v2/products";
                            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                            $decode_response = json_decode($response, true);

                            if (isset($decode_response["success"])) {
                                if ($decode_response["success"] == true) {
                                    if (isset($decode_response["product"])) {
                                        $product = $decode_response["product"];
                                        $productID = $product["id"];
                                        if (count($variants) > 0) {
                                            $datasent = array(
                                                "title" => "colors - size",
                                                "access_token" => $token,
                                            );

                                            $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories";
                                            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                            $decode_response = json_decode($response, true);
                                            if (isset($decode_response["error"])) {
                                                if (isset($decode_response["message"])) {
                                                    $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                                                } else {
                                                    $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 215"];
                                                }
                                            } elseif (isset($decode_response["variant_category"])) {
                                                $variant_category = $decode_response["variant_category"];
                                                $variant_categoryID = $variant_category["id"];
                                                foreach ($variants as $variant) {
                                                    $diff_price = 0;
                                                    if (isset($variant["price"])) {
                                                        $price = $variant["price"] * 100;
                                                        $diff_price = $price - $min_max_price;
                                                    }
                                                    $name = $variant["color_name"] . " : " . $variant["size_name"];
                                                    $datasent = array(
                                                        "name" => $name,
                                                        "access_token" => $token,
                                                        "price_difference_cents" => $diff_price
                                                    );
                                                    $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories/" . $variant_categoryID . "/variants";
                                                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                                }

                                                $sql = "UPDATE wp_users_products SET gumroad_id = '$productID',gumroadactive = 1  WHERE id = '$products_id'";
                                                $query = $wpdb->get_result($sql);
                                                if (!$query) {
                                                    $resultat = ["statut" => 400, "error" => mysql_error()];
                                                } else {
                                                    $resultat = ["statut" => 200, "data" => $product];
                                                }
                                            } else {
                                                $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line : 247"];
                                            }
                                        }
                                    } else {
                                        $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 251"];
                                    }
                                } else {
                                    if (isset($decode_response["message"])) {
                                        $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                                    } else {
                                        $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 257"];
                                    }
                                }
                            } else {
                                $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 259" . json_encode($response)];
                            }
                        } else {
                            $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
                        }
                    } else {
                        $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
                    }
                } else {
                    $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
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
    // @function : getProductbyId
    // @param :$prodctID, $token
    // ###################################################################################
    function getProductbyId($prodctID, $token)
    {
        $resultat = array();
        try {

            if (isset($token)) {
                $request_type = 'GET';
                $header = array();
                $datasent = array();
                $url = "https://api.gumroad.com/v2/products/" . $prodctID . "?access_token=" . $token;

                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);

                //return $response;
                $decode_response = json_decode($response, true);
                if (isset($decode_response["error"])) {
                    if (isset($decode_response["message"])) {
                        $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                    } else {
                        $resultat = ["statut" => 400, "error" => "ProductNotFound"];
                    }
                } elseif (isset($decode_response["product"])) {
                    $resultat = ["statut" => 200, "data" => $decode_response["product"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "ProductNotFound"];
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
    // @function : product_data
    // @param :$prodctID,$token
    // ###################################################################################
    function product_data($prodctID)
    {
        $gumroadactiveold = 0;
        $gumroad_id = null;
        $selectproductquery = $wpdb->get_result("SELECT * FROM wp_users_products WHERE id='$prodctID'");
        $row = mysql_fetch_assoc($selectproductquery);
        $gumroadactiveold = (int)$row['gumroadactive'];
        $gumroad_id = $wpdb->get_var("select meta_value from wp_products_meta where meta_key='gumroad_id' and product_id='$productID' limit 1");;


        return array("gumroadactiveold" => $gumroadactiveold, "gumroad_id" => $gumroad_id);
    }

    // ###################################################################################
    // @function : GumroadData
    // @param : $data
    // ###################################################################################

    function GumroadData($data)
    {

        $gumroadactive = esc_sql($data['gumroadactive']);
        $new_gumroad_product = (isset($data["gumroadproducts"]) && ($data["gumroadproducts"] != "") && ($data["gumroadproducts"] != "0")) ? 0 : 1;
        $productID_gumroad = $data["gumroadproducts"];
        return array('gumroadactive' => $gumroadactive, "productID_gumroad" => $productID_gumroad, "new_gumroad_product" => $new_gumroad_product);
    }

    // ###################################################################################
    // @function : UpdateGumroadProduct
    // @param :$title,$description,$min_max_price,$token, $variants,$images,$products_id,$productID_gumroad
    // ###################################################################################

    function UpdateGumroadProduct($title, $description, $min_max_price, $token, $variants, $images, $products_id, $productID_gumroad)
    {

        $resultat = array();

        try {

            if (isset($token)) {

                $header = array();

                if (isset($images)) {
                    if (count($images) > 0) {
                        if (isset($images[0])) {
                            if (count($variants) > 0) {
                                $min_max_price = $min_max_price * 100;
                                $productID = str_replace(' ', '', $productID_gumroad);
                                $this->DeleteGumroadProductCatgeory($productID, $token);
                                $request_type = 'POST';
                                $datasent = array(
                                    "title" => "colors - size",
                                    "access_token" => $token,
                                );

                                $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories";
                                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                $decode_response = json_decode($response, true);

                                if (isset($decode_response["variant_category"])) {
                                    $variant_category = $decode_response["variant_category"];
                                    $variant_categoryID = $variant_category["id"];
                                    foreach ($variants as $variant) {
                                        $diff_price = 0;
                                        if (isset($variant["price"])) {
                                            $price = $variant["price"] * 100;
                                            $diff_price = $price - $min_max_price;
                                        }
                                        $name = $variant["color_name"] . " : " . $variant["size_name"];
                                        $request_type = 'POST';
                                        $datasent = array(
                                            "name" => $name,
                                            "access_token" => $token,
                                            "price_difference_cents" => $diff_price
                                        );
                                        $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories/" . $variant_categoryID . "/variants";
                                        $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                    }
                                    $request_type = 'PUT';
                                    $path_img = $images[0]["path"];
                                    $url_img = $images[0]["src"];

                                    $datasent = array(
                                        "name" => $title,
                                        "access_token" => $token,
                                        "url" => $url_img,
                                        "price" => $min_max_price,
                                        "description" => $description,
                                        "shown_on_profile" => true,
                                        "preview" => '@' . $path_img
                                    );

                                    $url = "https://api.gumroad.com/v2/products/" . $productID;
                                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                    $decode_response = json_decode($response, true);

                                    if (isset($decode_response["success"])) {
                                        if ($decode_response["success"] == true) {
                                            if (isset($decode_response["product"])) {
                                                $sql = "UPDATE wp_users_products SET gumroad_id = '$productID',gumroadactive = 1  WHERE id = '$products_id'";
                                                $query = $wpdb->get_result($sql);
                                                if (!$query) {
                                                    $resultat = ["statut" => 400, "error" => mysql_error()];
                                                } else {
                                                    $resultat = ["statut" => 200, "data" => $decode_response["product"]];
                                                }
                                            } else {
                                                $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 429"];
                                            }
                                        } else {
                                            if (isset($decode_response["message"])) {
                                                $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                                            } else {
                                                $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 434"];
                                            }
                                        }
                                    } else {
                                        $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 439"];
                                    }
                                } else {
                                    if (isset($decode_response["message"])) {
                                        $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                                    } else {
                                        $resultat = ["statut" => 400, "error" => "Add variant category product gumroad failed. Please try again later. Line 448"];
                                    }
                                }
                            }
                        } else {
                            $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
                        }
                    } else {
                        $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
                    }
                } else {
                    $resultat = ["statut" => 400, "error" => "Image not found. Please try again later"];
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
    // @function : DeleteGumroadProductCatgeory to delete all variants in existing product
    // @param: $productId, $token
    // ###################################################################################

    function DeleteGumroadProductCatgeory($productId, $token)
    {
        $resultat = array();
        try {
            if (isset($token)) {
                $request_type = 'GET';
                $header = array();
                $datasent = array();
                $url = "https://api.gumroad.com/v2/products/" . $productId . "/variant_categories?access_token=" . $token;
                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                $decode_response = json_decode($response, true);
                if (isset($decode_response["success"])) {
                    if ($decode_response["success"] == true) {
                        if (isset($decode_response["variant_categories"])) {
                            if (count($decode_response["variant_categories"]) > 0) {
                                $catgories = $decode_response["variant_categories"];
                                foreach ($catgories as $catgory) {

                                    $id = $catgory["id"];
                                    $request_type = 'GET';
                                    $url = "https://api.gumroad.com/v2/products/" . $productId . "/variant_categories/" . $id . "/variants" . "?access_token=" . $token;
                                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                    $decode_response = json_decode($response, true);

                                    if (isset($decode_response["success"])) {
                                        if ($decode_response["success"] == true) {
                                            if (isset($decode_response["variants"])) {
                                                if (count($decode_response["variants"]) > 0) {
                                                    $variants = $decode_response["variants"];

                                                    foreach ($variants as $variant) {
                                                        $id_variant = $variant["id"];
                                                        $request_type = 'DELETE';
                                                        $header = array();
                                                        $datasent = array();
                                                        $url = "https://api.gumroad.com/v2/products/" . $productId . "/variant_categories/" . $id . "/variants/" . $id_variant . "?access_token=" . $token;
                                                        $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $request_type = 'DELETE';
                                    $header = array();
                                    $datasent = array();
                                    $url = "https://api.gumroad.com/v2/products/" . $productId . "/variant_categories/" . $id . "?access_token=" . $token;
                                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                                }
                            }
                        }
                    }
                }
                $resultat = ["statut" => 200, "data" => "sucess"];
            } else {
                $resultat = ["statut" => 400, "error" => "TokenInvalid"];
            }
        } catch (Exception $ex) {
            $resultat = ["statut" => 400, "error" => $ex->getMessage()];
        }

        return $resultat;
    }

    // ###################################################################################
    // @function : CreateNewVariantCategory to create news variants in existing product
    // @param: $productId, $token,$variants
    // ###################################################################################
    function CreateNewVariantCategory($productID, $token, $variants, $price)
    {
        $resultat = array();
        try {

            if (isset($token)) {
                if (count($variants) > 0) {
                    $header = array();
                    $price = $price * 100;
                    $request_type = 'POST';
                    $datasent = array(
                        "title" => "colors--size",
                        "access_token" => $token,
                    );

                    $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories";
                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                    $decode_response = json_decode($response, true);
                    if (isset($decode_response["error"])) {
                        if (isset($decode_response["message"])) {
                            $resultat = ["statut" => 400, "error" => $decode_response["message"]];
                        } else {
                            $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line 215"];
                        }
                    } elseif (isset($decode_response["variant_category"])) {

                        foreach ($variants as $variant) {
                            $diff_price = 0;
                            if (isset($variant["price"])) {
                                $price = $variant["price"] * 100;
                                $diff_price = $price - $price;
                            }
                            $name = $variant["color_name"] . " : " . $variant["size_name"];
                            $request_type = 'POST';
                            $datasent = array(
                                "name" => $name,
                                "access_token" => $token,
                                "price_difference_cents" => $diff_price
                            );
                            $url = "https://api.gumroad.com/v2/products/" . $productID . "/variant_categories/" . $variant_categoryID . "/variants";
                            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                        }
                        $request_type = 'PUT';
                        $datasent = array("access_token" => $token, "test" => "test variants");
                        $url = "https://api.gumroad.com/v2/products/" . $productID;
                        $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                        $decode_response = json_decode($response, true);

                        $resultat = ["statut" => 200, "data" => "sucess"];
                    } else {
                        $resultat = ["statut" => 400, "error" => "Add product in gumroad failed. Please try again later. Line : 599"];
                    }
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
    // @function : DeleteProduct
    // @param : $productId ,$token,$productID_gumroad
    // ###################################################################################
    function DeleteProduct($productId, $token, $productID_gumroad)
    {

        $resultat = array();

        try {

            if (isset($token)) {
                $header = array();
                $productID = str_replace(' ', '', $productID_gumroad);
                $sql = "UPDATE wp_users_products SET gumroad_id =0,gumroadactive = 0  WHERE id = '$productId'";
                $query = $wpdb->get_result($sql);
                if (!$query) {
                    $resultat = ["statut" => 400, "error" => mysql_error()];
                } else {
                    $request_type = 'DELETE';
                    $header = array();
                    $datasent = array();
                    $url = "https://api.gumroad.com/v2/products/" . $productID . "?access_token=" . $token;
                    $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                    $decode_response = json_decode($response, true);
                    if ($decode_response["success"] == true) {
                        $resultat = ["statut" => 200, "data" => "The product has been deleted successfully."];
                    } else {
                        $resultat = ["statut" => 400, "error" => "Delete product failed. Please try again later."];
                    }
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
    // @function : DeleteProductGumroad
    // @param : $token,$productID_gumroad
    // ###################################################################################
    function DeleteProductFromGumroad($token, $productID_gumroad)
    {

        $resultat = array();

        try {

            if (isset($token)) {
                $header = array();
                $productID = str_replace(' ', '', $productID_gumroad);
                $request_type = 'DELETE';
                $datasent = array();
                $url = "https://api.gumroad.com/v2/products/" . $productID . "?access_token=" . $token;
                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                $decode_response = json_decode($response, true);
                if ($decode_response["success"] == true) {
                    $resultat = ["statut" => 200, "data" => "The product has been deleted successfully."];
                } else {
                    $resultat = ["statut" => 400, "error" => "Delete product failed. Please try again later."];
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
    // @function : storeSubscribeResource : subscribed you will be notified of the user's sales
    // @param:  $token,$currentuserid
    // ###################################################################################
    function storeSubscribeResource($token, $currentuserid)
    {

        $resultat = array();
        $userID = null;
        try {
            $userID = $this->getGmuroadUserId($token);
            if (isset($userID)) {
                $request_type = 'PUT';
                $header = array();
                $datasent = array(
                    "access_token" => $token,
                    "post_url" => "https://ryankikta.com/gumroad-order/",
                    "resource_name" => "sale"
                );
                $url = "https://api.gumroad.com/v2/resource_subscriptions";
                $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
                $decode_response = json_decode($response, true);
                if ($decode_response["success"] == true) {
                    if (isset($decode_response["resource_subscription"])) {
                        $resource_subscription = $decode_response["resource_subscription"];
                        $id_resource_subscription = $resource_subscription["id"];

                        $sql = "UPDATE wp_users_gumroad SET id_resource_subscription='$id_resource_subscription' , gumroad_user_id='$userID' WHERE users_id='$currentuserid'";
                        $query = $wpdb->get_result($sql);
                        if (!$query) {
                            $resultat = ["statut" => 400, "error" => mysql_error()];
                        } else {
                            $resultat = ["statut" => 200, "data" => "add resource subscriptions successfully."];
                        }
                    } else {
                        $resultat = ["statut" => 400, "error" => "add resource subscriptions failed. Please try again later."];
                    }
                } else {
                    $resultat = ["statut" => 400, "error" => "add resource subscriptions failed. Please try again later."];
                }
            } else {
                $resultat = ["statut" => 400, "error" => "Gmuroad User ID not found"];
            }
        } catch (Exception $ex) {
            $resultat = ["statut" => 400, "error" => $ex->getMessage()];
        }

        return $resultat;
    }

    // ###################################################################################
    // @function : getGmuroadUserId : to get userId gumroad
    // @param:  $token
    // ###################################################################################
    function getGmuroadUserId($token)
    {

        $userID = null;

        try {
            $request_type = 'GET';
            $header = array();
            $datasent = array();
            $url = "https://api.gumroad.com/v2/user?access_token=" . $token;
            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
            $decode_response = json_decode($response, true);
            if ($decode_response["success"] == true) {
                if (isset($decode_response["user"])) {
                    $user = $decode_response["user"];
                    $userID = $user["user_id"];
                }
            }
        } catch (Exception $ex) {

        }

        return $userID;
    }

    // ###################################################################################
    // @function gumroad_shipping_address : to extrac data from wehook reciveied
    // @param:  $order
    // ###################################################################################

    function gumroad_shipping_address($order)
    {
        $shippingaddress = null;

        $country = null;
        $shippingaddress1 = array();
        $shippingaddress1['country'] = "";
        $shippingaddress1['clientname'] = "";
        $shippingaddress1['address1'] = "";
        $shippingaddress1['city'] = "";
        $shippingaddress1['state'] = "";
        $shippingaddress1['zipcode'] = "";
        $shippingaddress1['address2'] = "";
        $countries = $this->get_all_countries();
        $country = "";
        $country_code = "";
        $state = "";
        $state_code = "";
        $shippingaddress_zip = "";
        if (isset($order["shipping_information[full_name]"])) {
            $shippingaddress1['clientname'] = $order["shipping_information[full_name]"];
        }
        if (isset($order["shipping_information[street_address]"])) {
            $shippingaddress1['address1'] = $order['shipping_information[street_address]'];
        }
        if (isset($order["shipping_information[city]"])) {
            $shippingaddress1['city'] = $order['shipping_information[city]'];
        }
        if (isset($order["shipping_information[state]"])) {
            $shippingaddress1['state'] = $order['shipping_information[state]'];
            $state = $order['shipping_information[state]'];
            $state_code = $state;
        }
        if (isset($order["shipping_information[zip_code]"])) {
            $shippingaddress1['zipcode'] = $order['shipping_information[zip_code]'];
            $shippingaddress_zip = $order['shipping_information[zip_code]'];
        }
        if (isset($order["shipping_information[country]"])) {
            $country = $order["shipping_information[country]"];
            $country_code = array_search($country, $countries);
            $shippingaddress1['country'] = $country_code;
        }


        $address2 = ($shippingaddress1['address2'] != "") ? $shippingaddress1['address2'] : "";


        $shippingaddress = $shippingaddress1['clientname'] . "\n" . $shippingaddress1['address1'] . "\n" . $address2 . "\n" . $shippingaddress1['city'] . " , " . $shippingaddress1['state'] . " " . $shippingaddress1['zipcode'] . "\n" . $country;
        $paypal_address = array('name' => $shippingaddress1['clientname'], 'street' => $shippingaddress1['address1'], "street2" => $address2, "city" => $shippingaddress1['city'], "state" => $shippingaddress1['state'], "zip" => $shippingaddress1['zipcode'], "country" => $shippingaddress1['country'], "phone" => "");
        $shippingaddress1 = serialize($shippingaddress1);


        if ($country_code == "US") {
            $shipping_id = 1;
        } elseif ($country_code == "CA") {
            $shipping_id = 2;
        } else {
            $shipping_id = 3;
        }

        return array('shippingaddress' => $shippingaddress, 'shippingaddress1' => $shippingaddress1, 'shippingaddress_country' => $country_code, 'shippingaddress_state' => $state, 'shippingaddress_state_code' => $state_code, 'shippingaddress_zip' => $shippingaddress_zip, 'shipping_id' => $shipping_id, 'paypal_address' => $paypal_address);
    }

    // ###################################################################################
    // @function get_all_item_gumroad : to get all order data
    // @param:  $order, $user_id
    // ###################################################################################

    function get_all_countries()
    {

        return array('AF' => 'Afghanistan', 'AX' => 'Åland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin',
            'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia, Plurinational State of', 'BQ' => 'Bonaire, Sint Eustatius and Saba', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic',
            'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, the Democratic Republic of the', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Côte d\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CW' => 'Curaçao', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt',
            'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe',
            'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and McDonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan',
            'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia, The Former Yugoslav Republic of', 'MG' => 'Madagascar',
            'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova, Republic of', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal',
            'NL' => 'Netherlands', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine, State of', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar',
            'RE' => 'Réunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthélemy', 'SH' => 'Saint Helena, Ascension and Tristan da Cunha', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin (French part)', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SX' => 'Sint Maarten (Dutch part)',
            'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia and the South Sandwich Islands', 'SS' => 'South Sudan', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard and Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan, Province of China', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania, United Republic of', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau',
            'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela, Bolivarian Republic of', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
    }

    // ###################################################################################
    // @function get_order_gumroad_data : to get all order data
    // @param:  $order, $user_id
    // ###################################################################################

    function get_all_item_gumroad($order, $user_id)
    {
        global $wpdb;
        $items = array();
        $item_id = "";
        if (isset($order['sale_id'])) {
            $item_id = $order['sale_id'];
        }

        foreach ($order as $key => $itm) {
            if (strpos($key, 'variants[') !== false) {
                $variant = $itm;
                break;
            }
        }

        $variant_id = base64_encode(str_replace(' ', '', $variant));
        $gr_prd_id = (isset($order['product_id'])) ? $order['product_id'] : "";

        $quantity = 1;
        if (isset($order["quantity"])) {
            $quantity = $order['quantity'];
        }
        $item_price = 00.00;
        if (isset($order["quantity"])) {
            $quantity = $order['quantity'];
        }
        if (isset($order["price"])) {
            $price_total = (int)$order['price'];
            $item_price = $price_total / $quantity;
            $item_price = $item_price / 100;
        }

        /*
        $res = $wpdb->get_results(
            "select up.id,up.users_id,up.brand_id,up.product_id,up.front,up.back from `wp_users_products` as up "
            . "left join `wp_users_products_colors` as pc on (pc.users_products_id = up.id) "
            . "where up.users_id = '$user_id' and up.gumroad_id = '$gr_prd_id' GROUP BY up.id",
            ARRAY_A
        );
        */

        /* $product = $wpdb->get_results(
             "SELECT pc.`color_id`,pc.`size_id`,pr.`id`,pr.`product_id`,pr.`brand_id`,pr.`front`,pr.`back` FROM `wp_users_products_colors` as pc "
             ."join `wp_users_products` as pr on (pr.id = pc.users_products_id) "
                 . "join `wp_variants_meta`"
             ."WHERE `pc`.`gumroad_id` = '$variant_id' and pr.`gumroad_id`='$gr_prd_id' and pr.users_id='$user_id'",
             ARRAY_A
         );
         */
        $user_product_id = $wpdb->get_var("select product_id from wp_products_meta where meta_key='gumroad_id' and meta_value='$gr_prd_id'");
        $product = $wpdb->get_row(
            "SELECT pr.`id`,pr.`product_id`,pr.`brand_id`,pr.`front`,pr.`back` FROM `wp_users_products` as pr "
            . "join `wp_users_products` as pr on (pr.id = pc.users_products_id) "

            . "WHERE  pr.`id`='$user_product_id' and pr.users_id='$user_id'",
            ARRAY_A
        );

        $pr_variant_id = $wpdb->get_var("select variant_id from wp_variants_meta where product_id=" . $product['id'] . " and meta_key='gumroad_id' and meta_value='$variant_id'");
        $variant = $wpdb->get_row("select color_id,size_id from wp_users_products_colors where id = " . $pr_variant_id, ARRAY_A);
        $pa_product_id = $product['id'];

        $inventory_id = $product['product_id'];
        $brand_id = $product['brand_id'];
        $hasfront = $product['front'];
        $hasback = $product['back'];
        $color_id = $variant['color_id'];
        $size_id = $variant['size_id'];

        if ($pa_product_id != null && $pa_product_id > 0) {
            $items [] = array('item_id' => $item_id, 'pa_product_id' => $pa_product_id, 'product_id' => $inventory_id, 'brand_id' => $brand_id, 'hasfront' => $hasfront, 'hasback' => $hasback, 'color_id' => $color_id, 'size_id' => $size_id, 'item_price' => $item_price, 'quantity' => $quantity);
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

    // ###################################################################################
    // @function : getOrderGumroad
    // @param: $orderID,$token
    // ###################################################################################

    function get_order_gumroad_data($order)
    {

        $shop_order_name = $order["order_number"];
        $email = $order['email'];
        $customerphone = "";
        $order_status = "processing";
        $shipping_addresses = "";
        if (isset($order["shipping_information[street_address]"])) {
            $shipping_addresses = $order["shipping_information[street_address]"];
        }

        return array('order_status' => $order_status, "shop_order_name" => $shop_order_name, 'email' => $email, 'customerphone' => $customerphone, 'shipping_addresses' => $shipping_addresses);
    }

    // ###################################################################################
    // @function : get_all_countries
    // @param:  
    // ###################################################################################

    function getOrderGumroad($orderID, $token)
    {
        $resultat = array();
        try {

            $request_type = 'GET';
            $header = array();
            $datasent = array();
            $url = "https://api.gumroad.com/v2/sales/" . $orderID . "?access_token=" . $token;
            $response = GumroadCall::GetResponse($url, $header, $request_type, $datasent);
            $decode_response = json_decode($response, true);
            if ($decode_response["success"] == true) {
                if (isset($decode_response["sale"])) {
                    $resultat = ["statut" => 200, "data" => $decode_response["sale"]];
                } else {
                    $resultat = ["statut" => 400, "error" => "Order Not Found. Please try again later."];
                }
            } else {
                $resultat = ["statut" => 400, "error" => "Order Not Found. Please try again later."];
            }
        } catch (Exception $ex) {
            $resultat = ["statut" => 400, "error" => $ex->getMessage()];
        }

        return $resultat;
    }

    function tst()
    {
        $codeAsString = "$variant = array(5,6);";
        $script = 'var code= ' . json_encode($codeAsString) . ';';
        debug($script);


        return;
        $shop = $wpdb->get_result("SELECT access_token FROM wp_users_gumroad WHERE users_id='479'");
        $shoprow = $wpdb->get_row($shop);
        $token = $shoprow[0];

        $resp = GumroadCall::GetResponse(
            "https://api.gumroad.com/v2/products/lqs0C5nCNqaMdsz1xHc0Iw==/custom_fields?access_token=" . $token,
            array(), "GET", array()
        );

        return json_decode($resp, TRUE);
    }

    function product_variant_categories($gumroad_prd_id, $token = "")
    {
        $shop = $wpdb->get_result("SELECT access_token FROM wp_users_gumroad WHERE users_id='479'");
        $shoprow = $wpdb->get_row($shop);
        $token = $shoprow[0];

        $resp = GumroadCall::GetResponse(
            "https://api.gumroad.com/v2/products/$gumroad_prd_id/variant_categories?access_token=" . $token,
            array(), "GET", array()
        );

        return json_decode($resp, TRUE);
    }

    function variant_category_variants($gumroad_prd_id, $variant_ctg_id, $token)
    {
        $resp = GumroadCall::GetResponse(
            "https://api.gumroad.com/v2/products/$gumroad_prd_id/variant_categories/$variant_ctg_id/variants?access_token=" . $token,
            array(), "GET", array()
        );

        return json_decode($resp, TRUE);
    }

    function product_variants($gumroad_prd_id, $token)
    {
        $variants = array();

        $variant_categories = GumroadCall::GetResponse(
            "https://api.gumroad.com/v2/products/$gumroad_prd_id/variant_categories?access_token=" . $token,
            array(), "GET", array()
        );
        $variant_categories = json_decode($variant_categories, TRUE);

        foreach ($variant_categories['variant_categories'] as $variant_category) {
            $category_variants = GumroadCall::GetResponse(
                "https://api.gumroad.com/v2/products/$gumroad_prd_id/variant_categories/" . $variant_category["id"] . "/variants?access_token=" . $token,
                array(), "GET", array()
            );

            $category_variants = json_decode($category_variants, TRUE);

            foreach ($category_variants['variants'] as $variant) {
                $variants[$variant_category['title']][] = $variant;
            }
        }
        return $variants;
    }

    function product_combinations($variants)
    {
        $options = array();

        if (count($variants) < 2) {
            foreach ($variants[array_keys($variants)[0]] as $vr) {
                $options[] = $vr['name'];
            }
            return $options;
        }

        if (count($variants) == 2) {
            $vrs1 = $variants[array_keys($variants)[0]];
            $vrs2 = $variants[array_keys($variants)[1]];

            foreach ($vrs1 as $vr1) {
                foreach ($vrs2 as $vr2) {
                    $options[] = $vr1['name'] . "-" . $vr2['name'];
                }
            }
            return $options;
        }

        if (count($variants) == 3) {
            $vrs1 = $variants[array_keys($variants)[0]];
            $vrs2 = $variants[array_keys($variants)[1]];
            $vrs3 = $variants[array_keys($variants)[2]];

            foreach ($vrs1 as $vr1) {
                foreach ($vrs2 as $vr2) {
                    foreach ($vrs3 as $vr3) {
                        $options[] = $vr1['name'] . "-" . $vr2['name'] . "-" . $vr3['name'];
                    }
                }
            }
        }

        $vrs1 = $variants[array_keys($variants)[0]];
        $vrs2 = $variants[array_keys($variants)[1]];
        $vrs3 = $variants[array_keys($variants)[2]];
        $vrs4 = $variants[array_keys($variants)[3]];

        foreach ($vrs1 as $vr1) {
            foreach ($vrs2 as $vr2) {
                foreach ($vrs3 as $vr3) {
                    foreach ($vrs4 as $vr4) {
                        $options[] = $vr1['name'] . "-" . $vr2['name'] . "-" . $vr3['name'] . "-" . $vr4['name'];
                    }
                }
            }
        }

        return $options;
    }

    function gumroad_slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
