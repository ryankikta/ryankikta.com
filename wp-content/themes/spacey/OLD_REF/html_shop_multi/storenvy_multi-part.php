<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $has_edit = true;
    $has_import = 0;
    $productid = $_GET['id'];
    $storenvyactiveold = $wpdb->get_var("select storenvyactive from `wp_users_products` where `id` = $productid");
    $listshop_ids = "";
    $shopids = get_product_meta_shops($productid, "storenvy_id");
    if (count($shopids) > 0) {
        foreach ($shopids as $shop_id) {
            $listshop_ids .= $shop_id . ",";
        }
    } else {
        if ($storenvyactiveold == 1) {
            $listshop_ids = $wpdb->get_var("select id from `wp_users_storenvy` where `users_id` = $currentuserid");
        }
    }

    $shopids = rtrim($listshop_ids, ",");
    $selected = ($storenvyactiveold == 1) ? "selected='selected'" : "";
    $display = ($storenvyactiveold == 0) ? "style='display:none;'" : "";
} else {
    $has_edit = false;
    $selected = "";
    $has_import = (isset($_GET['action']) && $_GET['action'] == "import") ? 1 : 0;
    $display = ($has_import == 0) ? "style='display:none;'" : "";
}
if ($has_import == 1) {
    global $data_shop;
    @extract($data_shop);
}
$has_error = 0;
if (!empty($_SESSION['data'])) {
    extract($_SESSION['data']);

    $has_error = 1;
    $selected = ($storenvyactive == 1) ? "selected='selected'" : "";
    $display = ($storenvyactive == 0) ? "style='display:none;'" : "";
}
$price = ($has_edit || $has_error == 1 || $has_import == 1) ? "value='$storenvyprice'" : "value=''";
?>
<div id="storenvyspecific">
    <?php if ($has_import == 0) { ?>
        <div class="row mt-5">
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/storeenvy_tile.png">
                    <div class="input_checkbox add-shop">
                    <label>Active:</label>
                       <!-- <select name="storenvyactive" id="storenvyactive">
                            <option value="0">No</option>
                            <option value="1" <?php //echo $selected; ?>>Yes</option>
                        </select> -->
                        <input type="checkbox" name="storenvyactive" id="storenvyactive" value="1" checked>
                    </div>
                </div>
            </div>
    <?php } ?>
        <div id="storenvyholder" class="col-md-6 col-lg-5">
            <?php //$has_import = 1; ?>
            <?php if ($has_import == 0) { ?>
                <div>
                    <label for="storenvyshop[]">Storenvy Shops</label>
                    <div class="input_dropdown">
                        <select name="storenvyshop[]" style="width: 400px;" id="storenvyshop" multiple="multiple">
                            <option>Test</option>
                        </select>
                    </div>
                    <span id="storenvy_select_error"></span>
                </div>
            <?php } else { ?>
                <input type="hidden" name="storenvyactive" value="1">
                <input name="storenvyshop[]" type="hidden" value="<?php echo $shop_id; ?>">
                <input type="hidden" name="storenvynewproduct_<?php echo $shop_id; ?>" value="<?php echo (int)$_GET['shop_prd_id']; ?>">
            <?php } ?>

            <input type="hidden" <?php echo $price; ?> name="storenvyprice" id="storenvyprice"><br>
            <div id="storenvy_data_shops"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var list_products = {};
        var list_collections = {};
        var list_shipping_groups = {};

        var lance_list_data = 0;
        var user_id = <?php echo $currentuserid;?>;
        var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
        var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
        var is_import = <?php echo ($has_import == 1) ? 1 : 0 ?>;
        var storenvyactive = <?php echo ($storenvyactiveold == 1) ? 1 : 0; ?>;
        var reload_data = <?php echo ($has_error == 1 && $storenvyactive == 1) ? 1 : 0; ?>;
        var productId = <?php echo ($_GET['id'] == null) ? 0 : $_GET['id'] ?>;
        var storenvyactivenew = <?php echo ($storenvyactiveold == 0) ? 1 : 0; ?>;
        var selected_shops = "";

        if (reload_data == 1) {
            selected_shops = <?php echo json_encode(implode(",", (array)$storenvyshop)); ?>;
            get_list_shops_storenvy();

        } else if (storenvyactive == 1 && is_edit == 1) {
            selected_shops = <?php echo json_encode($shopids, true); ?>;
            get_list_shops_storenvy();
        } else if (is_import == 1) {
            load_storenvy_data(<?php echo ($shop_id == null) ? 0 : $shop_id; ?>, productId);
        }

        var vars = {};

        jQuery("#storenvyactive").on('change', function () {
            if (jQuery(this).val() == 1) {
                var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
                $("#storenvyholder").before(loading_shop);
                get_list_shops_storenvy();
                $("#loading_shop").remove();
                jQuery("#storenvyholder").slideDown("slow");
                storenvyactivenew = 1;
            } else
                jQuery("#storenvyholder").slideUp("slow");
        });

        function load_storenvy_data(shop_id, productId) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_storenvy_shop_data",
                    user_id:<?php echo $currentuserid; ?>,
                    shop_id: shop_id,
                    is_edit: is_edit,
                    product_id: productId,
                    reload_data: reload_data,
                    session_data:<?php echo json_encode($_SESSION['data']); ?>
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    console.log(data);
                    var list_products = data.products;
                    var list_collections = data.collections;
                    var list_shipping_groups = data.shipping_groups;
                    var list_marketplace_categories = data.categories;
                    var list_selected_items = data.selected;
                    var default_collections = "",
                        default_product_id = 0,
                        default_storenvy_marketplace_id = 0,
                        default_storenvy_shipping_id = 0,
                        default_storenvy_preorder = 0,
                        default_storenvy_on_sale = 0,
                        default_storenvy_id = 0;
                    if (is_import == 1) {
                        default_collections = "<?php echo $storenvycollection; ?>";
                        default_product_id = <?php echo ($_GET["shop_prd_id"] == null) ? 0 : $_GET["shop_prd_id"]; ?>;
                        default_storenvy_marketplace_id = <?php echo ($storenvy_marketplace_id == null) ? 0 : $storenvy_marketplace_id; ?>;
                        default_storenvy_shipping_id = <?php echo ($storenvy_shipping_id == null) ? 0 : $storenvy_shipping_id; ?>;
                        default_storenvy_preorder = <?php echo ($storenvy_preorder == null) ? 0 : $storenvy_preorder; ?>;
                        default_storenvy_on_sale = <?php echo ($storenvy_on_sale == null) ? 0 : $storenvy_on_sale; ?>;
                        default_storenvy_id = <?php echo ($_GET["shop_prd_id"] == null) ? 0 : $_GET["shop_prd_id"]; ?>;
                    } else {
                        default_collections = list_selected_items.storenvycollection;
                        default_product_id = list_selected_items.newproduct;
                        default_storenvy_marketplace_id = list_selected_items.storenvy_marketplace_id;
                        default_storenvy_shipping_id = list_selected_items.storenvy_shipping_id;
                        default_storenvy_preorder = list_selected_items.storenvy_preorder;
                        default_storenvy_on_sale = list_selected_items.storenvy_on_sale;
                        default_storenvy_id = list_selected_items.storenvy_id;
                    }


                    var shop = data.store;
                    var collections = '';
                    var collections_content = '';
                    var shipping_group = "";
                    var categories = "";

                    var str = default_collections;
                    if (default_collections != null) {
                        default_collections = str.split(',').map(function (str) {
                            return Number(str);
                        });
                    }
                    if (list_collections) {
                        if (list_collections.length > 0) {
                            jQuery.each(list_collections, function (i, val) {
                                var checked = "";
                                if (is_edit == 1 || is_error == 1 || is_import == 1) {
                                    if (jQuery.inArray(val.id, default_collections) != -1)
                                        checked = "checked='checked'";
                                }
                                collections += '<input type="checkbox" class="ajax_collect" name="storenvycollection_' + shop_id + '[]" style="margin-left: 5px;" value="' + val.id + '"' + checked + '>' + val.name + '';
                            });
                            var collections_content = '<div><strong>Collections</strong></div><div id="collection_' + shop_id + '">' + collections + '</div><br>';
                        }
                    }
                    var products_content = "";
                    if (((is_edit == 1) && (storenvyactivenew == 1)) || (is_edit == 0 && is_import == 0) || ((is_edit == 1) && (default_storenvy_id == null))) {
                        var products_select = "<option value='0' >New Product</option>";
                        if (list_products.length > 0) {
                            jQuery.each(list_products, function (i, val) {
                                var selected = "";
                                if (val.id == default_product_id)
                                    selected = "selected='selected'";
                                products_select += '<option value="' + val.id + '"' + selected + '">' + val.title + '</option>';
                            });
                            products_content = '<div><strong>New Product or Assign to an Existing Product</strong></div><div id="storenvy_newproduct_' + shop_id + '"> ';
                            products_content += '<select name="storenvynewproduct_' + shop_id + '" id="storenvynewproduct_' + shop_id + '" style="width: 400px">' + products_select + '</select>';
                            products_content += '<span id="product_storenvy_error_' + shop_id + '" style="color:red"></span></div><br>';
                        }
                    } else {
                        products_content = '<input type="hidden" name="storenvynewproduct_' + shop_id + '" value="' + default_storenvy_id + '">';
                    }
                    jQuery.each(list_shipping_groups, function (i, val) {
                        var selected = "";
                        if (val.id == default_storenvy_shipping_id)
                            selected = "selected";
                        shipping_group += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });

                    jQuery.each(list_marketplace_categories, function (id, val) {
                        var selected = "";
                        if (id == default_storenvy_marketplace_id)
                            selected = "selected";
                        categories += "<option value='" + id + "'" + selected + ">" + val + "</option>";
                    });

                    var storenvy_on_sale_checked = "";
                    var storenvy_preorder_checked = "";

                    if (default_storenvy_on_sale == 1)
                        storenvy_on_sale_checked = "checked='checked'";

                    if (default_storenvy_preorder == 1)
                        storenvy_preorder_checked = "checked='checked'";


                    var categories_content = '<div><strong>Marketplace Category</strong></div>';
                    categories_content += '<div id="storenvy_marketplace_' + shop_id + '"><select name="storenvy_marketplace_id_' + shop_id + '" id="str_marketplace_id_' + shop_id + '" class="str_marketplace">';
                    categories_content += '<option value="">...Please Choose Marketplace...</option>';
                    categories_content += categories;
                    categories_content += '</select></div><br>';

                    var labels_content = '<div><strong>Labels</strong></div>';
                    labels_content += '<div id="labels_' + shop_id + '"><input type="checkbox" value="1" name="storenvy_on_sale_' + shop_id + '" ' + storenvy_on_sale_checked + ' />  On Sale &nbsp;&nbsp;';
                    labels_content += '<input type="checkbox" value="1" name="storenvy_preorder_' + shop_id + '" ' + storenvy_preorder_checked + ' /> Preorder</div><br>';
                    var shipping_content = '<div><strong>Shipping Group</strong></div>';
                    shipping_content += '<div id="storenvyshipping_' + shop_id + '"><select name="storenvy_shipping_id_' + shop_id + '" id="storenvyshipping' + shop_id + '">' + shipping_group + '</select>';
                    shipping_content += '</div><br>';

                    var content = '<fieldset id="storenvy_shop_' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b>' + shop + '</b></legend><div class="content">';
                    content += products_content + collections_content + categories_content + labels_content + shipping_content;
                    content += '</fieldset>';
                    jQuery("#storenvy_data_shops").append(content);
                    jQuery("select[name^='storenvynewproduct']").select2({placeholder: "New Product"}).select2().on('change', function () {
                        var storenvy_product_id = jQuery(this).val();
                        var name_el = jQuery(this).attr("name");
                        var shop_id = name_el.replace("storenvynewproduct_", "");
                        jQuery("#storenvy_newproduct_" + shop_id).parent().find(".product_storenvy_existe").remove();
                        if (storenvy_product_id != 0) {
                            check_product_existe_storenvy(storenvy_product_id, shop_id, user_id);
                        }


                    });
                    ;
                    jQuery("select[name^='storenvy_marketplace_id']").select2();
                    jQuery("select[name^='storenvy_shipping_id']").select2();
                }, complete: function () {
                    setTimeout(function () {
                        jQuery.unblockUI();
                    }, 500);
                }, error: function (jqXHR, textStatus, errorThrown) {
                    var message = "";
                    if (textStatus == 'parsererror')
                        message += "Parsing request was failed – " + errorThrown;
                    else if (errorThrown == 'timeout')
                        message += "Request time out.";
                    else if (errorThrown == 'abort')
                        message += "Request was aborted.";
                    else if (jqXHR.status === 0)
                        message += "No connection.";
                    else if (jqXHR.status)
                        message += "HTTP Error " + jqXHR.status + " – " + jqXHR.statusText + ".";
                    else
                        message += "Unknown error.";
                    jQuery("#storenvy_select_error").show("fade");
                    jQuery("#storenvy_select_error").html("An error has occured while loading storenvy shop data. Please retry later");
                }
            });

        }

        function get_list_shops_storenvy() {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_list_shops_storenvy",
                    user_id:<?php echo $currentuserid; ?>
                },
                cache: false,
                dataType: 'json',
                async: false,
                success: function (data) {
                    var shopList = data;
                    var count_shops = shopList.length, disabled = 0;

                    if (count_shops > 0) {
                        var shops_options_select = "", disabled_count = 0;
                        jQuery.each(shopList, function (i, shop) {
                            var id = shop.id, val = shop.value, active = shop.active, selected = "", disabled = "";
                            if (active == "0") {
                                disabled = "disabled";
                                disabled_count++;
                            }

                            if ((selected_shops != null) && (selected_shops != "")) {
                                if (selected_shops.indexOf(id) > -1) {
                                    load_storenvy_data(id, productId);
                                    selected = "selected";
                                }
                            }
                            shops_options_select += "<option value='" + id + "' " + disabled + " " + selected + ">" + val + "</option>";
                        });
                    }
                    jQuery('#storenvyshop').append(shops_options_select);
                    jQuery('#storenvyshop').multiSelect({
                        keepOrder: true,
                        afterSelect: function (values) {
                            jQuery("#storenvy_select_error").slideUp("slow");
                            jQuery("#product_storenvy_error_" + values + "").html("");
                            jQuery.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Just a moment...</h5>'});
                            load_storenvy_data(parseInt(values), productId);
                        },
                        afterDeselect: function (values) {
                            jQuery("#product_storenvy_error_" + values + "").html("");
                            jQuery.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Just a moment...</h5>'});
                            remove_storenvy_shop_data(parseInt(values));
                            var nbre = jQuery("#ms-storenvyshop .ms-selection ul.ms-list > li:visible").length;
                            if (nbre == 0) {
                                jQuery("#storenvy_select_error").html("Please select at least one shop from list storenvy shops");
                                jQuery("#storenvy_select_error").slideDown("slow");
                            }

                        }
                    });

                }, error: function (data, textStatus, jqXHR) {
                    console.log(textStatus);
                }
            });
        }

        function check_product_existe_storenvy(productId, shop_id, user_id) {

            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "check_product_existe_storenvy",
                    storenvy_id: productId,
                    shop_id: shop_id,
                    user_id: user_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#storenvy_newproduct_" + shop_id).append('<span class="product_storenvy_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#storenvy_newproduct_" + shop_id).parent().find(".product_storenvy_existe").remove();
                        }
                    }
                }
            });
        }

        function remove_storenvy_shop_data(shop_id) {
            jQuery("#storenvy_shop_" + shop_id).remove();
            setTimeout(function () {
                jQuery.unblockUI();
            }, 500);
        }

    });
</script>