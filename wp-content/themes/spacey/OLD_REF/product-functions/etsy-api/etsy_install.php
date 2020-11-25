<?php

ob_start();
require_once('etsy_functions.php');

$current_url = curPageURL();

$current_url = rtrim($current_url, '/');
$errors_map = array();
$errors_map['oauth_problem=consumer_key_unknown'] = "There is an error with the consumer user key provided. Please try copy/pasting it again";
$errors_map['oauth_problem=token_rejected'] = "Please click <a href='https://www.etsy.com/your/apps' target='_blank'>here</a> remove the app from your shop.";
$has_error = false;
$etsy_install = 1;
$etsy_message = "";
$updated = 0;
if (isset($_POST['etsyconf']) && $_POST['etsyconf'] == 1) {

    $data['consumer_key'] = $_POST['consumer_key'];
    $data['consumer_secret'] = $_POST['consumer_secret'];
    $data['etsy_shop'] = $_POST['etsy_shop'];
    $data['username'] = $_POST['username'];
    $data['url_file'] = $_POST['url_file'];
    $client = new oauth_client_class;
    $client->debug = false;
    $client->debug_http = false;
    $client->server = 'Etsy';
    $client->client_id = $_POST['consumer_key'];
    $application_line = __LINE__;
    $client->client_secret = $_POST['consumer_secret'];
    $postdata = http_build_query($data);
    $client->resetAccessToken();
    $client->redirect_uri = $current_url . '?' . $postdata;

    if (($success = $client->Initialize())) {

        if (($success = $client->Process(2))) {

        } else {

            $etsy_install = 0;
            $etsy_message = $client->getRawError();
            if (array_key_exists($etsy_message, $errors_map) !== false) {
                $etsy_message = $errors_map[$etsy_message];
            } elseif (strpos($etsy_message, 'signature_invalid')) {
                $etsy_message = "Please check Your key/Secret";
            }
        }


    } else {

    }

}
if (isset($_GET['consumer_key']) && isset($_GET['consumer_secret']) && isset($_GET['etsy_shop']) && isset($_GET['username']) && isset($_GET['url_file'])) {
    $consumer_key = $_GET['consumer_key'];
    $consumer_secret = $_GET['consumer_secret'];
    $etsy_shop = $_GET['etsy_shop'];
    $username = $_GET['username'];
    $url_file = $_GET['url_file'];
    $client = new oauth_client_class;
    $client->debug = false;
    $client->debug_http = false;
    $client->server = 'Etsy';
    $client->client_id = $_GET['consumer_key'];
    $application_line = __LINE__;
    $client->client_secret = $_GET['consumer_secret'];

    if (($success = $client->Initialize())) {
        if (($success = $client->Process())) {


            $url = "https://openapi.etsy.com/v2/users/" . $_GET['etsy_shop'] . "/shops";
            $success = $client->CallAPI(
                $url,
                'GET', array(), array('FailOnAccessError' => true), $response);


            if ($success) {
                $token = $client->access_token;
                $secret = $client->access_token_secret;
                $data = array('username' => $username, 'action_curl' => 'checkApp');
                $result = get_content_from_url($data);
                if ($result['status'] == 'success') {
                    $shops = object_to_array($response);
                    $shop = $shops['results'][0]['shop_name'];
                    $etsy_user_id = $shops['results'][0]['user_id'];
                    if ($shop == "") {
                        $has_error = true;
                        $etsy_install = 0;
                        $etsy_message = "The Ryan Kikta app requires that your Etsy shop be created and OPEN. Please create a shop or set it to open and then install the app.";
                    }
                    if (!$has_error) {
                        $data = array('action_curl' => 'Add_shop', 'username' => $username, 'shop' => $shop, 'token' => $token, 'secret' => $secret, 'etsy_username' => $etsy_shop,
                            'etsy_user_id' => $etsy_user_id, 'url_file' => $url_file, 'consumer_key' => $consumer_key, 'consumer_secret' => $consumer_secret);
                        $result = get_content_from_url($data);
                        if ($result['status'] == 'failed') {
                            $etsy_install = 0;
                            $etsy_message = $result['error'];
                        } else
                            $updated = 1;
                    }
                } else {
                    $has_error = true;
                    $etsy_install = 0;
                    $etsy_message = $result['error'];
                }
            }
        } else {

        }

    }
}
ob_end_clean();
?>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <link type="text/css" href="/wp-content/themes/ryankikta-modernize/style.css" rel="stylesheet">
    <link href="/wp-content/themes/ryankikta-modernize/stylesheet/skeleton-responsive.css" rel="stylesheet">
    <link href="/wp-content/themes/ryankikta-modernize/stylesheet/layout-responsive.css" rel="stylesheet">
    <link media="all" type="text/css" href="/wp-content/themes/ryankikta-modernize/style-custom.css?ver=4.1.1"
          id="style-custom-css" rel="stylesheet">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
</head>
<body class="page page-id-610980 page-template page-template-etsy_v2 page-template-etsy_v2-php logged-in admin-bar  customize-support">
<div class="body-wrapper">
    <div class="container">
        <!-- header-wrapper -->
        <div class="content-wrapper">
            <br>
            <div class="page-wrapper">
                <?php
                $etsy_shop = (isset($_POST['etsy_shop'])) ? $_POST['etsy_shop'] : ((isset($_GET['etsy_shop'])) ? $_GET['etsy_shop'] : '');
                $username = (isset($_POST['username'])) ? $_POST['username'] : ((isset($_GET['username'])) ? $_GET['username'] : '');
                $consumer_key = (isset($_POST['consumer_key'])) ? $_POST['consumer_key'] : ((isset($_GET['consumer_key'])) ? $_GET['consumer_key'] : '');
                $consumer_secret = (isset($_POST['consumer_secret'])) ? $_POST['consumer_secret'] : ((isset($_GET['consumer_secret'])) ? $_GET['consumer_secret'] : '');
                $url_file = (isset($_POST['url_file'])) ? $_POST['url_file'] : ((isset($_GET['url_file'])) ? $_GET['url_file'] : '');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://ryankikta.com/etsy-install-instructions/');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $return = curl_exec($ch);

                $return = str_replace(array('[ETSY_SHOP]', '[USERNAME]', '[CONSUMER_KEY]', '[CONSUMER_SECRET]', '[URL_FILE]'),
                    array($etsy_shop, $username, $consumer_key, $consumer_secret, $url_file), $return);

                if ($updated != 1) {

                    $return = replace_between($return, '[UPDATE]', '[/UPDATE]', '');
                }
                if ($etsy_install == 0) {
                    $return = str_replace('[ETSY_MESSAGE]', $etsy_message, $return);
                } else $return = replace_between($return, '[INSTALL_ISSUE]', '[/INSTALL_ISSUE]', '');
                $return = str_replace(array('[ETSY_MESSAGE]', '[UPDATE]', '[/UPDATE]', '[INSTALL_ISSUE]', '[/INSTALL_ISSUE]'), array('', '', '', '', ''), $return);
                $return = str_replace('[CURRENT_URL]', $current_url, $return);
                echo $return;

                ?>

            </div>
        </div>
    </div>
</div>
</body>
</html>
