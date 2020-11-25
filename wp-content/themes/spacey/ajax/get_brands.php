<?php
require("wp-config.php");
global $wpdb;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$product_id = mysql_real_escape_string($_GET['product_id']);

@extract(access_product_additional_settings($product_id));

$access_heat_press_tag = get_user_meta($user_id, 'access_heat_press_tag', true);
$access_attach_hang_tag = get_user_meta($user_id, 'access_attach_hang_tag', true);

$status = $show_neck_label_removal = $show_heat_press_tag = $show_attach_hang_tag = $show_individual_bagging = $show_always_underbase = 0;

if ($inv_access_neck_label_removal == 1 || $inv_access_individual_bagging == 1 ||
    $inv_access_heat_press_tag == 1 && $access_heat_press_tag == 1 ||
    $inv_access_attach_hang_tag == 1 && $access_attach_hang_tag == 1 || $inv_access_always_underbase == 1)
    $status = 1;

if ($status == 1) {

    if ($inv_access_neck_label_removal == 1)
        $show_neck_label_removal = 1;

    if ($inv_access_heat_press_tag == 1 && $access_heat_press_tag == 1)
        $show_heat_press_tag = 1;

    if ($inv_access_attach_hang_tag == 1 && $access_attach_hang_tag == 1)
        $show_attach_hang_tag = 1;

    if ($inv_access_individual_bagging == 1)
        $show_individual_bagging = 1;

    if ($inv_access_always_underbase == 1)
        $show_always_underbase = 1;

}

echo json_encode(array('status' => $status, 'show_neck_label_removal' => $show_neck_label_removal,
    'show_heat_press_tag' => $show_heat_press_tag, 'show_attach_hang_tag'
    => $show_attach_hang_tag,
    'show_individual_bagging' => $show_individual_bagging,
    'show_always_underbase' => $show_always_underbase));
exit();