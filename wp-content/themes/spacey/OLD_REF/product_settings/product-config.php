<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
global $wpdb;

$removetag = $current_user->removetag;
$applytag = $current_user->applytag;

$applytag_location = get_user_meta($currentuserid, 'applytag_location', true);
$applytag_note = "";
$attach_hang_tag = get_user_meta($currentuserid, 'attach_hang_tag', true);
$hang_tag_location = get_user_meta($currentuserid, 'hang_tag_location', true);
$individual_bagging = get_user_meta($currentuserid, 'individual_bagging', true);

$access_heat_press_tag = get_user_meta($currentuserid, 'access_heat_press_tag', true);
$access_attach_hang_tag = get_user_meta($currentuserid, 'access_attach_hang_tag', true);
$always_use_underbase = get_user_meta($currentuserid, 'always_use_underbase', true);

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $productid = $_GET['id'];
    $product_id = $wpdb->get_var("select product_id from `wp_users_products` where `id` = $productid");

    @extract(access_product_additional_settings($product_id));

    if (get_product_meta($productid, "removetag") != NULL)
        $removetag = get_product_meta($productid, "removetag");

    if (get_product_meta($productid, "applytag") != NULL)
        $applytag = get_product_meta($productid, "applytag");

    if (get_product_meta($productid, "applytag_location") != NULL)
        $applytag_location = get_product_meta($productid, "applytag_location");

    if (get_product_meta($productid, "applytag_note") != NULL)
        $applytag_note = get_product_meta($productid, "applytag_note");

    if (get_product_meta($productid, "attach_hang_tag") != NULL)
        $attach_hang_tag = get_product_meta($productid, "attach_hang_tag");

    if (get_product_meta($productid, "hang_tag_location") != NULL)
        $hang_tag_location = get_product_meta($productid, "hang_tag_location");

    if (get_product_meta($productid, "individual_bagging") != NULL)
        $individual_bagging = get_product_meta($productid, "individual_bagging");
    /**
     * 0 :default
     * 1 : no underbase
     * 2 : Always use underbase
     */
    $underbase_setting = get_product_meta($productid, "underbase_setting");


}
?>
<fieldset class="multi_shop add_products_branding_form">
    <h2>Brand Settings</h2>

    <!--Neck Label Removal-->
    <div id="br_label_removal" class="<?php if ($inv_access_neck_label_removal != 1) echo 'hide'; ?>">
        <div class="d-flex">
            <label for="removetag">Remove Tag:</label>
            <select name="removetag">
                <option value="0">No</option>
                <option value="1" <?php if ($removetag == 1) echo "selected='selected'" ?> >Yes</option>
            </select>
        </div>
    </div>

    <!--Heat Press Label Application-->
    <?php if ($access_heat_press_tag == 1) { ?>
        <div id="br_heat_press_tag" class="<?php if ($inv_access_heat_press_tag != 1) echo 'hide'; ?>">
            <div class="d-flex">
                <label>Apply Tag:</label>
                <select name="applytag" class="check_select">
                    <option value="0">No</option>
                    <option value="1" <?php if ($applytag == 1) echo "selected='selected'" ?>>Yes</option>
                </select>
            </div>

            <div class="d-flex">
                <label for="applytag_location">Location option:</label>
                <?php
                $location_heat_press = array('115' => 'Inside neck', '116' => 'Outside Neck', '117' => 'Left Front Hem',
                    '118' => 'Right Front Hem', '119' => 'Left Sleeve', '120' => 'Right Sleeve');
                ?>
                <select name="applytag_location">
                    <?php
                    foreach ($location_heat_press as $key => $value) {
                        $selected = ($applytag_location == $key) ? 'selected="selected"' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    } ?>
                </select>
            </div>

            <div class="input_outline">
                <label for="applytag_note">Apply Tag Note:</label>
                <textarea name="applytag_note"><?php echo $applytag_note; ?></textarea>
            </div>
        </div>
    <?php } ?>

    <!--Hang Tag Application-->
    <?php if ($access_attach_hang_tag == 1) { ?>
        <div id="br_attach_hang_tag" class="<?php if ($inv_access_attach_hang_tag != 1) echo 'hide'; ?>">
            <div class="d-flex">
                <label for="attach_hang_tag">Attach hang Tag:</label>
                <select name="attach_hang_tag">
                    <option value="0">No</option>
                    <option value="1" <?php if ($attach_hang_tag == 1) echo "selected='selected'" ?>>Yes</option>
                </select>
            </div>

            <div class="d-flex">
                <label for="hang_tag_location">Location option:</label>
                <?php
                $locate_hang_tag = array('110' => 'Left Sleeve Cuff', '111' => 'Right Sleeve Cuff', '112' => 'Left Armpit', '113' => 'Right Armpit',
                    '114' => 'Collar Back', '115' => 'Inside Neck', '116' => 'Outside Neck', '117' => 'Left Front Hem',
                    '118' => 'Right Front Hem', '119' => 'Left Sleeve', '120' => 'Right Sleeve');
                ?>
                <select name="hang_tag_location">
                    <?php
                    foreach ($locate_hang_tag as $key => $value) {
                        $selected = ($hang_tag_location == $key) ? 'selected="selected"' : '';
                        echo "<option value='$key' $selected>$value</option>";
                    } ?>
                </select>
            </div>
        </div>
    <?php } ?>

    <!--Individual Bagging-->
    <div id="br_indiv_bagging" class="<?php if ($inv_access_individual_bagging != 1) echo 'hide'; ?>">
        <div class="d-flex">
            <label for="individual_bagging">Individual Bagging:</label>
            <select name="individual_bagging">
                <option value="0">No</option>
                <option value="1" <?php if ($individual_bagging == 1) echo "selected='selected'" ?>>Yes</option>
            </select>
        </div>
    </div>
    <!-- Underbase Setting-->
    <div id="br_always_underbase" class="<?php if ($inv_access_always_underbase != 1) echo 'hide'; ?>">
        <div class="d-flex">
            <label for="underbase_setting">Underbase :</label>
            <select name="underbase_setting">
                <option value="0">Default</option>
                <option value="1" <?php if ($underbase_setting == 1) echo "selected='selected'" ?>>No Underbase</option>
                <option value="2" <?php if ($underbase_setting == 2) echo "selected='selected'" ?>>Always use Underbase</option>
            </select>
        </div>
    </div>
</fieldset>


