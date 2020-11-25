<?php
if (isset($_GET['version']) && $_GET['version']) {
    return json_encode(array('status' => 1));
    exit;
}
require_once('etsy_functions.php');
$apps = json_decode(base64_decode($_POST['app_oauth']));
$consumer_key = $apps->consumer_key;
$consumer_secret = $apps->consumer_secret;
define('OAUTH_CONSUMER_KEY', $consumer_key);
define('OAUTH_CONSUMER_SECRET', $consumer_secret);
$client = new oauth_client_class;
$client->debug = true;
$client->debug_http = false;
$client->server = 'Etsy';
$client->client_id = OAUTH_CONSUMER_KEY;
$application_line = __LINE__;
$client->client_secret = OAUTH_CONSUMER_SECRET;
$auth = json_decode(base64_decode($_POST['auth']));
$url = $_POST['url'];
$etsy_auth = array('etsytoken' => $auth->etsytoken, 'etsysecret' => $auth->etsysecret);
$client->StoreAccessToken($auth->etsytoken);
$client->access_token = $auth->etsytoken;
$client->access_token_secret = $auth->etsysecret;
$body = json_decode(base64_decode($_POST['data']));
$body = object_to_array($body);
$method = $_POST['method'];
$action = $_POST['action'];
$call_type = $_POST['call_type'];
if ($call_type == 1) {
    $body = (is_array($body) && !empty($body)) ? $body : array();
    $result = EtsyApiCall($url, $body, $method);
}
if ($call_type == 2) {
    $position = $_POST['position'];
    $img_url = $_POST['img_url'];
    $file_name = basename($img_url);
    $file = dirname(__FILE__) . '/' . $file_name;
    save_image($img_url, $file);
    $result = EtsyImageCall($method, $url, $file, $position);
}
echo json_encode($result);
die();

function EtsyApiCall($url, $data, $method)
{
    global $client;
    if (($success = $client->Initialize())) {
        $response = array();
        $success = $client->CallAPI($url, $method, $data, array('FailOnAccessError' => true), $response);
        if ($success) {
            $result = array(
                'status' => 'success',
                'response' => object_to_array($response)
            );
        } else {
            $result = array(
                'status' => 'failed',
                'errors' => $client->getRawError(),
            );
        }
    }
    return $result;
}

function EtsyImageCall($method, $url, $file, $position)
{
    global $client;
    $deleted_img = false;
    if ($file !== null) {
        $image_type = image_type_to_mime_type(exif_imagetype($file));
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $name_image = basename($file, '.' . $ext);
        $deleted_img = true;
        if ($ext == "png") {
            $file = create_imgpng($file, $name_image);
        }
        $params = array();
        if (($success = $client->Initialize())) {
            $files = array($name_image => array('Type' => 'FileName'));
            $response = array();
            $params[$name_image] = $file;
            $success = $client->CallAPI($url, 'POST', $params, array('FailOnAccessError' => true, 'Files' => $files), $response);
            if ($success === true) {
                $result = array(
                    'status' => 'success',
                    'response' => object_to_array($response)
                );
            } else {
                $ch = curl_init();
                $files_data = array('image' => "@$file", 'rank' => (int)$position);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $verbose = fopen('php://temp', 'rw+');
                curl_setopt($ch, CURLOPT_STDERR, $verbose);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: ' . $client->getAuthorization(),
                    'MIME-Version: 1.0',
                    'Host: openapi.etsy.com',
                    'X-Target-URI:  https://openapi.etsy.com',
                    'Connection:    Keep-Alive',
                ));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $files_data);
                $json = curl_exec($ch);
                rewind($verbose);
                $verboseLog = stream_get_contents($verbose);
                $headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
                if (curl_errno($ch)) {
                    $error = 'Curl Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
                    $result = array(
                        'status' => 'failed',
                        'errors' => $error
                    );
                } else {
                    $response = json_decode($json, true);
                    $result = array(
                        'status' => 'success',
                        'response' => $response
                    );
                }
            }
        }
    }
    if ($deleted_img) {
        @unlink($file);
    }
    return $result;
}

function save_image($url, $saveto)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    $raw = curl_exec($ch);
    curl_close($ch);
    if (file_exists($saveto)) {
        @unlink($saveto);
    }
    $fp = fopen($saveto, 'x');
    fwrite($fp, $raw);
    fclose($fp);
}

function html2rgb($color)
{
    $c = ($color['0'] == '#') ? substr($color, 1) : $color;
    $strlen = strlen($c);
    if ($strlen === 6) {
        $color = array($c[0] . $c[1], $c[2] . $c[3], $c[4] . $c[5]);
    } elseif ($strlen === 3) {
        $color = array($c[0] . $c[0], $c[1] . $c[1], $c[2] . $c[2]);
    } else {
        return false;
    }
    foreach ($color as &$v) {
        $v = hexdec($v);
    }
    return $color;
}

function create_imgpng($filePath, $new_name)
{
    $image = @imagecreatefrompng($filePath);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    list($r_bck, $g_bck, $b_bck) = html2rgb("#fff");
    imagefill($bg, 0, 0, imagecolorallocate($bg, $r_bck, $g_bck, $b_bck));
    imagealphablending($bg, true);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagedestroy($image);
    $quality = 100;
    $file = dirname(__FILE__) . '/' . $new_name . ".jpg";
    imagejpeg($bg, $file, $quality);
    imagedestroy($bg);
    return $file;
}
