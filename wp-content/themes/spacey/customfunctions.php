<?php

require("product-functions-live/shopify.php");

define("Shopify_Key", "1989c326aaaf62c2694cae84866f1a91");
define("Shopify_Secret", "4840139646f6ab8199a601c80430c9e2");
define("Storenvy_Id", "e8ebafb6148d1e57c829d07a24805ff2dfb2071358cfd570f8153ede03353e8e");
define("Storenvy_Secret", "b78f406ba040413692d305a279108358114f800f9ec9e6317426dbf60baa5a12");
define("Bigcommerce_client_id", "gck7knt3cv6ugxbe1j9es2d9g7wdk6z");
define("Bigcommerce_client_secret", "m4gu2aoevrrbp25zo94i8ryumnkdhi7");
//define(OAUTH_CONSUMER_KEY, 'lik3i97t21djgyb7ynla0hy0');
//define(OAUTH_CONSUMER_SECRET, 'lp170ltilc');
/*define(OAUTH_CONSUMER_KEY, 'u42wjf8xxv9ykcs63nigd2i1');
define(OAUTH_CONSUMER_SECRET, 'm86jh8mdjl');*/
/*define(OAUTH_CONSUMER_KEY, '0xlr3eqvvxev6noxgjef6k9f');
define(OAUTH_CONSUMER_SECRET, '71i84qsjzu');*/
//$oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);

$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;

function ShopifyApiCall($action, $path, $data = NULL, $user_id = "")
{


    global $sc, $wpdb;
    $success = 1;
    $error_sh = array();
    $current_user = wp_get_current_user();
    $currentuserid = $current_user->ID;
    $currentusername = $current_user->user_login;
    if ($user_id != "" && is_numeric($user_id))
        $currentuserid = $user_id;

    try {
        check_shopify_call_limit($currentuserid);
        return $sc->call($action, $path, $data);

    } catch (ShopifyApiException $e) {

        // If you're here, either HTTP status code was >= 400 or response contained the key 'errors'
        $error_rt = array();
        $map_fields = array('vendor' => 'Vendor', 'product_type' => 'Product Type', 'title' => 'Title', 'body_html' => 'Description', 'tags' => 'Tags', 'base' => '');
        $return = array();
        $error_sh['user_id'] = $currentuserid;
        $error_sh['method'] = $e->getMethod();
        $error_sh['path'] = $e->getPath();
        $error_sh['responseHeader'] = $e->getResponseHeaders();
        $http_code = $error_sh['responseHeader']['http_status_code'];
        $error_rt['httpd_code'] = $error_sh['responseHeader']['http_status_message'];
        $error_sh['response'] = $e->getResponse();
        $error_sh['params'] = $e->getParams();
        $export = var_export($error_sh, TRUE);
        wp_insert_post(array(
            'post_title' => $wpdb->escape($error_sh['responseHeader']['http_status_message'] . '(user : ' . $currentuserid . ',' . $currentusername . ')'),
            'post_content' => $wpdb->escape($export),
            'post_status' => 'draft',
            'post_type' => 'systems'
        ));
        //file_put_contents("debug11",$export,FILE_APPEND);
        $sucess = 0;
        switch ($http_code) {
            case '401' :
                $return['errors'][] = 'Invalid API key or access token (unrecognized login or wrong password)<br />';
                break;
            case '402' :
                $return['errors'][] = 'Your Shopify plan is out of available SKUs to add additional products. Please upgrade to a higher plan to add more products. Please note that each color/size/product counts as one SKU on the Shopify service. <br />';
                break;
            case '423' :
                $return['errors'][] = 'You cannot delete the last variant of a product';
                break;
            case '400' :
                $return['errors'][] = 'error related to text format for description , title , product vendor, product type, sku ...';
                break;
            case '422' :
                if (is_array($error_sh['response']['errors'])) {
                    foreach ($error_sh['response']['errors'] as $key => $errors) {
                        foreach ($errors as $error) {
                            if ($error == 'Exceeded maximum number of variants allowed')
                                $return['errors'][] = 'Shopify only allows products to have up to 100 variants. Please note that each color and specific size counts as a variant. You are most likely getting this error if you choose too many color options for one product';
                            else if ($error == 'Option Color cannot be blank') {
                                $return['errors'][] = 'Please choose at least 1 color';
                            } else if ($error == '1 option values given but 2 options exist') {

                            } else   $return['errors'][] = $map_fields[$key] . ' ' . $error;
                        }
                    }
                } else $return['errors'][] = $error_sh['response']['errors'];
                break;

        }


        return $return;

    } catch (ShopifyCurlException $e) {

        $sucess = 0;
    }

    if ($sucess == 1) {
        return $returns;
    } else {
        return FALSE;
    }
}

function check_shopify_call_limit($currentuserid = "")
{
    if ($currentuserid == "" || !is_numeric($currentuserid)) {
        $current_user = wp_get_current_user();
        $currentuserid = $current_user->ID;
    }

    $query = mysql_query("SELECT `last_call_time`,`total_call` FROM `wp_users_shopify` WHERE `users_id` = $currentuserid");
    $rows = mysql_fetch_row($query);
    // debug($rows);
    $lastcall = $rows[0];
    $total_call = $rows[1];
    $now = time();
    if ($lastcall == '0') {
        $lastcall = $now;
        //echo 'first last call est now : '.$lastcall.'<br />';
    }
    $Diff_Time = $now - $lastcall;
    $recup_call = floor(2 * $Diff_Time);
    /*echo 'Now : '.$now.'<br />';
    echo 'last call : '.$lastcall.'<br />';
    echo 'Diff Time of call : '.$Diff_Time.'<br />';
    echo 'floor of Recup call : '.$recup_call.'<br />';*/
    if ($recup_call == 0) {
        //echo 'Diff Time < 0.5 :<br />';
        $total_call = ($total_call != 0) ? $rows[1] : 1;
        if ($total_call >= 41) {
            /*  echo 'Number of call > 40 <br />';
              echo 'wait 0.5 seconde <br />';*/
            $total_call++;
            mysql_query("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
            usleep(700000);
        } else {
            //echo 'Number of call <= 40 <br />';
            //echo 'Wait 0.5 s please <br />';
            //usleep(500000);
            //echo 'Total call : '.$total_call.'<br />';
            $total_call++;
            mysql_query("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        }
    } else if ($recup_call >= 40) {
        /*echo 'Remise à 0 for 40 call <br />';
        echo 'Recup call >= 40 : '.$recup_call.'<br />';*/
        mysql_query("UPDATE `wp_users_shopify` SET `total_call` = 1 WHERE `users_id` = $currentuserid");
        //mysql_query("UPDATE `wp_users_shopify` SET `last_call_time` = 0 WHERE `users_id` = $currentuserid");exit();
    } else {
        /*echo '0.5 <= Diff Time <= 20 :<br />';
        echo 'Total call : '.$total_call.'<br />';
        echo 'Recup call : '.$recup_call.'<br />';*/
        if ($total_call <= $recup_call) {
            //echo 'Total call <= Recup call :<br />';
            //mysql_query("UPDATE `wp_users_shopify` SET `total_call` = 1 WHERE `users_id` = $currentuserid");
            $total_call++;
            mysql_query("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        } else {
            /*echo 'Total call > Recup call :<br />';
            echo 'Total call before : '.$total_call.'<br />';
            echo 'Recup call before : '.$recup_call.'<br />';*/
            //$total_call-=$recup_call;
            $total_call++;
            //echo 'Total call after : '.$total_call.'<br />';
            mysql_query("UPDATE `wp_users_shopify` SET `total_call` = '$total_call' WHERE `users_id` = $currentuserid");
        }
    }
    mysql_query("UPDATE `wp_users_shopify` SET `last_call_time` = '$now' WHERE `users_id` = $currentuserid");
}

function CheckShopify($currentuserid)
{

    global $sc;
    $checkuser = mysql_query("SELECT `shop`,`token`,`active` FROM `wp_users_shopify` WHERE `users_id` = $currentuserid");
    $numshopsshopify = mysql_num_rows($checkuser);
    $shoprow = mysql_fetch_row($checkuser);
    $shop = $shoprow[0];
    $token = $shoprow[1];
    $active = $shoprow[2];

    if ($numshopsshopify !== 0 && $active !== "0") {

        // Add to sellers shop using the details.
        $sc = new ShopifyClient($shop, $token, Shopify_Key, Shopify_Secret);
        $shopdetails = ShopifyApiCall("GET", '/admin/countries.json');
        $i = 0;
        while (empty($shopdetails)) {
            $shopdetails = ShopifyApiCall("GET", '/admin/countries.json');
            if ($i = 3)
                break;
            $i++;
        }
        if (empty($shopdetails)) {

            // No Connection

            // Update Database
            //mysql_query("UPDATE `wp_users_shopify` SET `active` = 0 WHERE `users_id` = $currentuserid");
            //mysql_query("UPDATE `wp_users_shopify` SET `active` = 2 WHERE `users_id` = $currentuserid AND `active` = 1");

            // Get User Email
            $select = mysql_query("SELECT `user_email` FROM `wp_users` WHERE `ID` = $currentuserid");
            $rows = mysql_fetch_row($select);
            $user_email = $rows[0];

            // Send Email
            $headers = 'From: Ryan Kikta <matt@ryankikta.com>' . "\r\n" .
                'Reply-To: Ryan Kikta <matt@ryankikta.com>' . "\r\n" .
                'Bcc: matt@ryankikta.com,aladin@ryankikta.com' . "\r\n";

            //mail($user_email, 'Please re-authorize your Shopify shop at RyanKikta', "Just a heads up , We have noticed that RyanKikta is no longer Authorized to access your Shopify Shop ($shop) as a result, We have temporarily disabled your shop which means products ordered meanwhile from your shop will not be processed, \n\n You may re-authorize your shop by visiting https://ryankikta.com/shopify/ \n\n Let us know if you have any questions, \n Ryan Kikta Team",$headers);

            return 2;

        } else {

            return 1;

        }

    } else {

        return 0;

    }

}


if (!class_exists('SFTPConnection')) {

    class SFTPConnection
    {
        private $connection;
        private $sftp;

        public function __construct($host, $port = 22)
        {
            $this->connection = ssh2_connect($host, $port);
            if (!$this->connection)
                throw new Exception("Could not connect to $host on port $port.");
        }

        public function login($username, $password)
        {
            if (!ssh2_auth_password($this->connection, $username, $password))
                throw new Exception("Could not authenticate with username $username " .
                    "and password $password.");

            $this->sftp = ssh2_sftp($this->connection);
            if (!$this->sftp)
                throw new Exception("Could not initialize SFTP subsystem.");
        }

        public function createDirectory($remote_directory)
        {

            $sftp = $this->sftp;
            if (!ssh2_sftp_mkdir($sftp, $remote_directory))
                throw new Exception("Could not create SFTP remote directory ." . $remote_directory);

        }

        public function uploadFile($local_file, $remote_file)
        {
            $sftp = $this->sftp;
            $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');

            if (!$stream)
                throw new Exception("Could not open file: $remote_file");

            $data_to_send = @file_get_contents($local_file);
            if ($data_to_send === false)
                throw new Exception("Could not open local file: $local_file.");

            if (@fwrite($stream, $data_to_send) === false)
                throw new Exception("Could not send data from file: $local_file.");

            @fclose($stream);
        }

        public function fileExist($remote_file)
        {
            $sftp = $this->sftp;
            return @file_exists('ssh2.sftp://' . $sftp . $remote_file);
        }

        public function unlinkFile($remote_file)
        {
            $sftp = $this->sftp;
            $unlink = ssh2_sftp_unlink($sftp, $remote_file);
            //debug($unlink);
            if (!$unlink)
                throw new Exception("Could not remove remote file : $remote_file.");
        }
    }
}
?>

