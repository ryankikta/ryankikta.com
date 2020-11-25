<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
global $wpdb;
$user_shopids = $wpdb->get_results("select id from wp_users_woocommerce where users_id=$currentuserid order by id asc", ARRAY_A);
$list_shops = "";
foreach ($user_shopids as $shop) {
    $list_shops .= $shop['id'] . ",";
}
$user_shopids = rtrim($list_shops, ",");

if (isset($_GET['id']) && intval($_GET['id']) != 0) {

    $has_edit = true;
    $has_import = 0;
    $productid = $_GET['id'];
    $woocommerceactiveold = $wpdb->get_var("select woocommerceactive from `wp_users_products` where `id` = $productid");
    $listshop_ids = "";
    $shopids = get_product_meta_shops($productid, "woocommerce_id");
    if (count($shopids) > 0) {
        foreach ($shopids as $shop_id) {
            $listshop_ids .= $shop_id . ",";
        }
    } elseif ($woocommerceactiveold == 1) {
        $listshop_ids = $wpdb->get_var("select id from wp_users_woocommerce where users_id=$currentuserid");
    }

    $shopids = rtrim($listshop_ids, ",");
    $selected = ($woocommerceactiveold == 1) ? "selected='selected'" : "";
    $display = ($woocommerceactiveold == 0) ? "style='display:none;'" : "";
} else {
    $has_edit = false;
    $productid = 0;
    $has_import = (isset($_GET['action']) && $_GET['action'] == "import") ? 1 : 0;
    $selected = "";
    //commented out for dev purposes
    // $display = ($has_import == 0) ? "style='display:none;'" : "";
}
if ($has_import == 1) {
    global $data_shop;
    @extract($data_shop);
    $auth = getWoocommerceShopbyId($_GET['shop_id']);
    @extract($auth);
    $all_cats = getWooCategories($auth);
    $woocommercecategory_id = "";
    $wooc_cats = explode(',', $woocommercecategory);
    foreach ($wooc_cats as $cat) {
        $cat_id = array_keys($all_cats, $cat);
        $woocommercecategory_id .= $cat_id[0] . ",";
    }
    $woocommercecategory = rtrim($woocommercecategory_id);
    $woocommerceshortdesc = (isset($woocommerceshortdesc) && $woocommerceshortdesc != "") ? stripcslashes($woocommerceshortdesc) : "";
}

$has_error = 0;
if (!empty($_SESSION['data'])) {
    extract($_SESSION['data']);
    $has_error = 1;
    $selected = ($woocommerceactive == 1) ? "selected='selected'" : "";
    $display = ($woocommerceactive == 0) ? "style='display:none;'" : "";
    $shopids = implode(",", $woocommerceshop);
}
?>

<div id="woocommercespecific">

    <?php if ($has_import == 0) { ?>
        <div class="row mt-5">
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/woocom_tile.png">
                    <div class="input_checkbox add-shop">
                    <label>Active:</label>
                    <!-- <select name="woocommerceactive" id="woocommerceactive">
                        <option value="0">No</option>
                        <option value="1" <?php //echo $selected; ?>>Yes</option>
                    </select> -->
                    <input type="checkbox" name="woocommerceactive" id="woocommerceactive" value="1" checked>
                    </div>
                </div>
            </div>
    <?php } ?>
        <div id="woocommerceholder" class="col-md-6 col-lg-5" <?php // echo $display; ?>>
            <!-- the following is a manual override of the integration checker -->
                <?php $has_import = 1; ?>
            <!-- okay resume normal routine starting now  -->
            <?php if ($has_import == 0) { ?>
                <div>
                    <div>Woocommerce Shops</div>
                    <div>
                        <select name="woocommerceshop[]" multiple="" style="width: 400px;" id="woocommerceshop"></select>
                    </div>
                    <br class="clear"> <br class="clear">
                    <span id="woocommerce_select_error"></span>
                </div>
                <br/>
            <?php } ?>

            <div id="woocommerce_data_shops">
                
                <?php // if ($has_import == 1) { ?>
                    <fieldset id="woocommerce_data_shop<?php echo $shop_id; ?>" class="multi_shop">
                        <h2>SETTINGS: <?php echo $woocommerceshop; ?>Shop Title</h2>
                        <input type="hidden" name="woocommerceactive" value="1">
                        <input type="hidden" name="woocommerceshop[]" value="<?php echo (int)$_GET['shop_id']; ?>">
                        <input type="hidden" name="woocommercenewproduct<?php echo $shop_id; ?>" value="<?php echo (int)$_GET['shop_prd_id']; ?>">
                        <div id="wooc_desc<?php echo $shop_id; ?>">
                            <label>Product Short Description</label>
                            <span class="small-caption">Use this field to provide additional information about your product. You can also include information on the quality, material, and design description etc.</span>
                            <div class="input_outline">
                                <textarea name="woocommerce_shortdesc<?php echo $shop_id; ?>" id="short_description<?php echo $shop_id; ?>">
                                    <?php echo $woocommerceshortdesc; ?>
                                </textarea>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        </br>
                    </fieldset>
                <?php // } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        var is_import = <?php echo ($has_import == 1) ? 1 : 0; ?>;

        if (is_import == 1) {
            var wc_shop_id = <?php echo ($shop_id && $shop_id != 0) ? $shop_id : 0; ?>;
            var product_shop_id = <?php echo (int)$_GET['shop_prd_id']; ?>;
            ajax_woocommerce_data(wc_shop_id);
            get_woocommerce_data(wc_shop_id);
        }

        if (is_import == 0) {
            var vars = {};
            var user_shopids = <?php echo json_encode($user_shopids) ?>;
            var all_shop = user_shopids.split(",");
            $.each(all_shop, function (index, shop_id) {
                vars['show_list_products_' + shop_id] = 0;
                vars['lance_list_products_' + shop_id] = 0;
                vars['lance_list_data_' + shop_id] = 0;
            });
            var first_loaded = 0;

            $('#woocommerceshop').multiSelect({
                keepOrder: true,
                afterSelect: function (values) {
                    $("#woocommerce_select_error").slideUp("slow");
                    if (woocommerceactive == 0 || first_loaded == 1) {
                        $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait ...</h5>'});
                        if (vars['lance_list_data_' + parseInt(values)] == 0) {
                            ajax_woocommerce_data(parseInt(values));
                            vars['lance_list_data_' + parseInt(values)] = 1;
                        }
                        if (is_edit == 0) {
                            if (vars['lance_list_products_' + parseInt(values)] == 0) {
                                ajax_woocommerce_products(parseInt(values));
                                vars['lance_list_products_' + parseInt(values)] = 1;
                            }
                        }
                        deblock_ui();
                        get_woocommerce_data(parseInt(values));
                        if (is_edit == 0)
                            get_woocommerce_products(parseInt(values));
                    }
                    if (first_loaded == 0) {
                        if (woocommerceactive == 1 || relance_call == 1) {
                            var selected_shop = shopids.split(",");
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_woocommerce_data(shop_id);
                                get_woocommerce_data(shop_id);
                                vars['lance_list_data_' + shop_id] = 1;
                            });
                        }
                        if (relance_call == 1 && is_edit == 0) {
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_woocommerce_products(shop_id);
                                get_woocommerce_products(shop_id);
                                vars['lance_list_products_' + shop_id] = 1;
                            });
                        }
                    }
                },
                afterDeselect: function (values) {
                    $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                    $("#woocommerce_select_error").slideUp("slow");
                    var nbr = $("#ms-woocommerceshop .ms-selection ul.ms-list > li:visible").length;
                    if (nbr == 0) {
                        $("#woocommerce_select_error").html("Please select at least one shop from list woocommerce shops");
                        $("#woocommerce_select_error").slideDown("slow");
                    }
                    deblock_ui();
                    $("#woocommerce_data_shop" + parseInt(values)).slideUp("slow");
                }
            });

            var list_products = {};
            var list_shipping = {};
            var list_cats = {};
            var wooc_shop = "";
            var lance_list_shop = 0;
            var user_id = <?php echo $currentuserid;?>;
            var shopids = <?php echo json_encode($shopids) ?>;
            var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
            var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
            var woocommerceactive = <?php echo ($woocommerceactiveold == 1) ? 1 : 0; ?>;
            var relance_call = <?php echo ($has_error == 1 && $woocommerceactive == 1) ? 1 : 0; ?>;

            if (woocommerceactive == 1 || relance_call == 1) {
                ajax_get_list_woocommerce_shop();
                lance_list_shop = 1;
            }

            $("#woocommerceactive").on('change', function () {
                if ($(this).val() == 1) {
                    if (lance_list_shop == 0) {
                        var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
                        $("#woocommerceholder").before(loading_shop);
                        ajax_get_list_woocommerce_shop();
                        $("#loading_shop").remove();
                        lance_list_shop = 1;
                    }
                    $("#woocommerceholder").slideDown("slow");
                } else {
                    $("#woocommerceholder").slideUp("slow");
                }

            });

        }

        function ajax_get_list_woocommerce_shop() {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_woocommerce_shop", user_id:<?php echo $currentuserid; ?>},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    var selectoptions = "";
                    var Count_Parts = data.length;
                    var i = 0;
                    var disabled = 0;
                    for (i = 0; i < Count_Parts; i++) {
                        var id = data[i].id;
                        var val = data[i].value;
                        var active = data[i].active;
                        $('#woocommerceshop').multiSelect('addOption', {value: id, text: val});
                        if (shopids != null) {
                            if (shopids.indexOf(id) > -1) {
                                $('#woocommerceshop').multiSelect('select', id);
                            }
                        }
                        if (active == "0") {
                            $('#woocommerceshop option[value="' + id + '"]').attr("disabled", true);
                            $('#woocommerceshop').multiSelect('refresh');
                            disabled++;
                            var error = $("#woocommerce_select_error").html();
                            error = error + "RyanKikta can no longer access your Woocommerce Shop : " + val + "<br>";
                            $("#woocommerce_select_error").html(error);
                        }
                    }
                    first_loaded = 1;
                }
            });
        }

        function ajax_woocommerce_products(shop_id) {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_products_woocommerce_shop", shop_id: shop_id},
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_products = data.products;
                }
            });
        }

        function get_woocommerce_products(shop_id) {

            if (vars['show_list_products_' + shop_id] == 0) {
                var wooc_html = "";
                var selected_product = "0";
                if (is_error == 1) {
                    var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                    selected_product = get_select_data_wooc(shop_id, 'product', 2, session_data);
                }
                var firstime = 0;
                var select = "<select id='woocommercenewproduct" + shop_id + "' name='woocommercenewproduct" + shop_id + "' style='width: 400px;'>";
                select += "<option value='0' >New Product</option>";
                $.each(list_products, function (id, title) {
                    var selected = "";
                    if (firstime == 0) {
                        if (selected_product == id) {
                            selected = "selected='selected'";
                            firstime = 1;
                        }
                    }
                    select += "<option value='" + id + "' " + selected + ">" + title + "</option>";
                });
                select += "</select>";
                wooc_html += '<div><div style="width: 200px;float: left;"><strong>New Product or <br /> Assign to an Existing Product</strong></div><div style="float: left; ">' + select + '</div><br class="clear"></div><br />';
                $('#wooc_shipping' + shop_id).before(wooc_html);
                $("#woocommercenewproduct" + shop_id).select2().on('change', function () {
                    var woocommerce_id = jQuery(this).val();
                    var name_el = jQuery(this).attr("name");
                    var shop_id = name_el.replace("woocommercenewproduct", "");
                    jQuery("#woocommercenewproduct" + shop_id).parent().find(".product_woocommerce_existe").remove();
                    if (woocommerce_id != 0) {
                        check_product_existe_woocommerce(woocommerce_id, shop_id, user_id);
                    }
                });
                vars['show_list_products_' + shop_id] = 1;
            }
        }

        function ajax_woocommerce_data(shop_id) {

            var selected_cat = '0';
            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                selected_cat = get_select_data_wooc(shop_id, 'cats', 2, session_data);
            } else if (is_edit == 1) {
                selected_cat = get_select_data_wooc(shop_id, 'cats', 1,<?php echo $productid; ?>);
            } else if (is_import == 1)
                selected_cat = <?php echo "'" . $woocommercecategory . "'"; ?>;

            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_data_woocommerce_shop", shop_id: shop_id, cats_id: selected_cat},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
		    console.log(selected_cat);
                    list_shipping = data.shippings;
                    list_cats = $.trim(data.cats);
                    wooc_shop = data.shop;
                }
            });
        }

        function get_woocommerce_data(shop_id) {
            var wooc_html = "";
            var selected_shipping = '0';
            var current_short_desc = "";
            //list shippings
            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                selected_shipping = get_select_data_wooc(shop_id, 'shipping', 2, session_data);
                current_short_desc = get_select_data_wooc(shop_id, 'shortdesc', 2, session_data);
                current_short_desc = current_short_desc.replace("\\", "");
            } else if (is_edit == 1) {
                selected_shipping = get_select_data_wooc(shop_id, 'shipping', 1,<?php echo $productid; ?>);
                current_short_desc = get_select_data_wooc(shop_id, 'shortdesc', 1,<?php echo $productid; ?>);
            } else if (is_import == 1)
                selected_shipping = <?php echo ($woocommerceshippingid && $woocommerceshippingid != 0) ? $woocommerceshippingid : 0; ?>;
            var firstime = 0;
            var selectoptions = "";
            $.each(list_shipping, function (id, title) {
                var selected = "";
                if (firstime == 0) {
                    if (selected_shipping == id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                selectoptions += "<option value='" + id + "' " + selected + ">" + title + "</option>";
            });
            wooc_html += '<fieldset id="woocommerce_data_shop' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b style="color:green">' + wooc_shop + '</b></legend>';
            // shipping
            var data_html = '<div id="wooc_shipping' + shop_id + '"><div style="width: 200px;float: left;"><strong>Woocommerce Shipping Class</strong></div><div style="float: left; " id="woocommerce_shippings' + shop_id + '"><select name="woocommerceshippingid' + shop_id + '"  id="woocommerceshippiingclass' + shop_id + '" style="height: 25px;">' + selectoptions + '</select></div><br class="clear"></div>';
            // cats
            if (list_cats != "")
                data_html += '<div id="wooc_category' + shop_id + '"><div style="width: 200px;float: left;"><strong>Woocommerce Category</strong></div><br><div class="html-chunk" ><div class="acidjs-css3-treeview" id="wc_categs_tree' + shop_id + '">' + list_cats + '</div></div><br class="clear"></div>';
            wooc_html += data_html;
            // short description
            wooc_html += '<div style="width: 100%;float: left;margin-top:25px"><strong>Product Short Description</strong><br /><span class="italic_text">Use this field to provide additional information about your product. You can also include information<br />on the quality, material, and design description etc.</span><br /><br /><textarea style="height: 150px;width: 750px;" class="iEdit" name="woocommerce_shortdesc' + shop_id + '" id="short_description' + shop_id + '">' + current_short_desc + '</textarea></div><div class="clearfix"></div></br>';
            wooc_html += '</fieldset>';
            if ($("#woocommerce_data_shop" + shop_id).length > 0 && is_import == 0)
                $("#woocommerce_data_shop" + shop_id).slideDown("slow");
            else {
                if (is_import == 0)
                    $("#woocommerce_data_shops").append(wooc_html);
                else
                    $("#wooc_desc" + shop_id).before(data_html);
                $("#woocommerceshippiingclass" + shop_id).select2();
            }

            tinymce.init({
                selector: "textarea#short_description" + shop_id,
                extended_valid_elements: "script[charset|defer|language|src|type]",
            });
        }

        function get_select_data_wooc(shop_id, field, type, prd_data) {
            var selected_data = "";
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_selected_data_woocommerce_shop",
                    shop_id: shop_id,
                    field: field,
                    type: type,
                    product_data: prd_data
                },
                cache: false,
                dataType: "json",
                async: false,
                success: function (response) {
                    selected_data = response.data;
                }
            });
            return selected_data;
        }

        function deblock_ui() {
            setTimeout(function () {
                jQuery.unblockUI();
            }, 500);
        }

        $(".acidjs-css3-treeview").delegate("label input:checkbox", "change", function () {
            var checkbox = jQuery(this),
                nestedList = checkbox.parent().next().next(),
                selectNestedListCheckbox = nestedList.find("label:not([for]) input:checkbox");

            if (checkbox.is(":checked")) {
                return selectNestedListCheckbox.prop("checked", true);
            }
            selectNestedListCheckbox.prop("checked", false);
        });

        function check_product_existe_woocommerce(woocommerce_id, shop_id, user_id) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "check_product_existe_woocommerce",
                    woocommerce_id: woocommerce_id,
                    shop_id: shop_id,
                    user_id: user_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#woocommercenewproduct" + shop_id).parent().append('<span class="product_woocommerce_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#woocommercenewproduct" + shop_id).parent().find(".product_woocommerce_existe").remove();
                        }
                    }
                }
            });
        }
    });
</script>
<style>
    .acidjs-css3-treeview, .acidjs-css3-treeview * {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .acidjs-css3-treeview label[for]::before, .acidjs-css3-treeview label span::before {
        content: "\25b6";
        display: inline-block;
        margin: 2px 0 0;
        width: 13px;
        height: 13px;
        vertical-align: top;
        text-align: center;
        color: #e74c3c;
        font-size: 8px;
        line-height: 13px;
        margin-right: -5px;
    }

    .acidjs-css3-treeview li ul {
        margin: 0 0 0 22px;
    }

    .acidjs-css3-treeview * {
        vertical-align: middle;
    }

    .acidjs-css3-treeview {
        font: normal 11px/16px "Segoe UI", Arial, Sans-serif;
    }

    .acidjs-css3-treeview li {
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    .acidjs-css3-treeview input[type="checkbox"] {
        display: none;
    }

    .acidjs-css3-treeview label {
        cursor: pointer;
        display: inline;
        margin-left: 15px;
    }

    .acidjs-css3-treeview label[for]::before {
        -webkit-transform: translatex(-24px);
        -moz-transform: translatex(-24px);
        -ms-transform: translatex(-24px);
        -o-transform: translatex(-24px);
        transform: translatex(-24px);
    }

    .acidjs-css3-treeview label span::before {
        -webkit-transform: translatex(16px);
        -moz-transform: translatex(16px);
        -ms-transform: translatex(16px);
        -o-transform: translatex(16px);
        transform: translatex(16px);
    }

    .acidjs-css3-treeview input[type="checkbox"][id]:checked ~ label[for]::before {
        content: "\25bc";
    }

    .acidjs-css3-treeview input[type="checkbox"][id]:not(:checked) ~ ul {
        display: none;
    }

    .acidjs-css3-treeview label span::before {
        content: "";
        border: solid 1px #1375b3;
        color: #1375b3;
        opacity: .50;
    }

    .acidjs-css3-treeview label input:checked + span::before {
        content: "\2714";
        box-shadow: 0 0 2px rgba(0, 0, 0, .25) inset;
        opacity: 1;
    }
</style>
