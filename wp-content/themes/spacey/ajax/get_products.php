<?php
require_once('../../../../wp-config.php');
$current_user = wp_get_current_user();
$id = $current_user->ID;

$result = $wpdb->get_row("SELECT apikey, apihash from wp_users where ID = $id", ARRAY_A);
$key = $result['apikey'];
$hash = $result['apihash'];
$method = $_GET['method'];


if(isset($method)){
  echo call($method, $key, $hash);
}


function call($method, $key, $hash){
  $data['method'] = $method;
  $data['brand_id'] = (isset($_GET['brand_id'])) ? $_GET['brand_id'] : '';
  $data['product_id'] = (isset($_GET['product_id'])) ? $_GET['product_id'] : '';
  $data['color_id'] = (isset($_GET['color_id'])) ? $_GET['color_id'] : '';
  $data['has_front'] = 1;
  $data['has_back'] = 0;
  $data['front_id'] = 2158169;
  $data['back_id'] = '';
  $data['key'] = $key;
  $data['hash'] = $hash;
  $url = 'https://api.ryankikta.com/api.php';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 300);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1); // this is required.
  //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true); // FIXME when API is HTTPS
  //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,true); // FIXME when API is HTTPS
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $responseText = curl_exec($ch);  
  
  return $responseText;  

};


?>
	 
