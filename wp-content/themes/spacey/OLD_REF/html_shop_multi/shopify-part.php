<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;

if (isset($_GET['id']) && intval($_GET['id']) != 0) {

    $has_edit = true;
    $productid = $_GET['id'];

    $shopify_current_data = ShopifyShopsProductData($productid);
    @extract($shopify_current_data);
    $selected = ($shopifyactiveold == 1) ? "selected='selected'" : "";
    $display = ($shopifyactiveold == 0) ? "style='display:none;'" : "";
    $has_import = 0;
} else {
    $productid = 0;
    $has_edit = false;
    $selected = "";
    $has_import = (isset($_GET['action']) && $_GET['action'] == "import") ? 1 : 0;
    $display = ($has_import == 0) ? "style='display:none;'" : "";
}

$has_error = 0;
$post_data = array();
$shopList = array();
if (!empty($_SESSION['data'])) {
    $post_data = $_SESSION['data'];
    $shopifyactiveold = ($post_data['shopifyactive']) ? intval(esc_sql($post_data['shopifyactive'])) : 0;
    $has_error = 1;
    $selected = ($shopifyactiveold == 1) ? "selected='selected'" : "";
    $display = ($shopifyactiveold == 0) ? "style='display:none;'" : "";
    $shopids = implode(",", $post_data["shopifyshop"]);
    $shopList = $post_data["shopifyshop"];
    $has_import = isset($post_data["has_import"]) ? 1 : 0;
}
if ($has_import == 1) {
    $shopify_id = $_GET['shop_prd_id'];
    $shop_id = $_GET["shop_id"];
    global $data_shop;
    @extract($data_shop);
    if (isset($shop_id) && ($shop_id != "")) {
        $shop_info = getShopifyShopbyId($shop_id);
        @extract($shop_info[0]);
        $collection_id = get_shopify_collections_per_shop($currentuserid, $shopify_id, $shop_id);
    } else {
        $collection_id = get_shopify_collections($currentuserid, $shopify_id);
    }
}
?>

<div id="shopifyspecific">

    <?php if ($has_import != 1) { ?>
    <div class="row">
        <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
            <div class="d-flex align-items-center">
                <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/shopify_tile.png">
                <div class="input_checkbox add-shop">
                    <label for="shopifyactive">Active:</label>
                        <!-- <select name="shopifyactive" id="shopifyactive">
                            <option value="0">No</option>
                            <option value="1" <?php //echo $selected; ?>>Yes</option>
                        </select> -->
                    
                    <input type="checkbox" name="shopifyactive" id="shopifyactive" value="1">

                </div>
            </div>
        </div>
    <?php } ?>

        <div id="shopifyholder" class="col-md-6 col-lg-5" <?php // echo $display; ?>>
            <?php // if ($has_import == 1) { ?>
                <fieldset id="shopify_shop_shop_id<?php echo $shop_id; ?>" class="multi_shop">
                    <h2 class="mb-20">SETTINGS: <?php echo $shop; ?>Shop Title</h2>
                    <div class="shop_settings">
                        <input type="hidden" name="shopifyactive" value="1">
                        <input type="hidden" name="shopifyshop[]" value="<?php echo $shop_id; ?>">
                        <input name="newproduct<?php echo $shop_id; ?>" value="<?php echo $shopify_id ?>" type="hidden">

                        <div class="input_outline">
                            <label for="shopifytype<?php echo $shop_id; ?>">Product Type</label>
                            <input value="<?php echo $shopifytype ?>" name="shopifytype<?php echo $shop_id; ?>" type="text" placeholder="Hat">
                        </div>
                        <div class="input_outline">
                            <label for="shopifyvendor<?php echo $shop_id; ?>">Product Vendor</label>
                            <input value="<?php echo $shopifyvendor ?>" name="shopifyvendor<?php echo $shop_id; ?>" type="text" placeholder="Vendor name here">
                        </div>

                        <div class="">
                            <label>Collections</label>
                            <select>
                                <option>Choose Collection</option>
                                <option>Collection 1</option>
                            </select>
                        </div>
                        <a class="checkbox_content">
                            <?php foreach ($collection_id as $index => $colle) { ?>
                                <input id="<?php echo $shop_id . $index; ?>" class="css-checkbox" name="collection<?php echo $shop_id; ?>[]" style="margin-left: 5px;" value="<?php echo $colle["title"]; ?>" checked="" type="checkbox">
                                <label class="css-label" for="<?php echo $shop_id . $index; ?>"><?php echo $colle["title"]; ?></label>&nbsp;&nbsp;
                            <?php } ?>
                        </a>
                    </div>
                </fieldset>
            <?php // } else { ?>
                <div>
                    <label for="shopifyshop[]">Shopify shops </label>
                    <select name="shopifyshop[]" multiple="" id="shopifyshop"></select>

                    <span id="shopify_select_error"></span>
                </div>

                <div style="" id="shoppify_shops_data">
                </div>
            <?php // } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

        var list_products = {};
        var list_collections = {};
        var user_id = <?php echo $currentuserid;?>;
        var shopids = <?php echo json_encode($shopids) ?>;
        var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
        var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
        var shopifyactive = <?php echo ($shopifyactiveold == 1) ? 1 : 0; ?>;
        var pa_product_id = <?php echo $productid; ?>;
        var post_data = <?php echo json_encode($post_data); ?>;

        if (shopifyactive == 1) {
            ajax_get_list_shopify_shop();
        }


        // jQuery("#shopifyactive").on('change', function () {
        //     if (jQuery(this).val() == 1) {
        //         if (jQuery('#shopifyshop option').length == 0) {
        //             var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
        //             $("#shopifyholder").before(loading_shop);
        //             ajax_get_list_shopify_shop();
        //             $("#loading_shop").remove();
        //             jQuery("#shopifyholder").show();
        //         }
        //     } else {
        //         jQuery("#shopifyholder").hide();
        //     }

        // });

        function ajax_get_list_shopify_shop() {
            jQuery.ajax({
                type: "GET",
                url: "ajax_shop_call.php?",
                data: {action: "get_list_shopify_shop", user_id:<?php echo $currentuserid; ?>},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {

                    var count_shops = data.length, disabled = 0;

                    if (count_shops > 0) {
                        var shops_options_select = "", disabled_count = 0;
                        jQuery.each(data, function (i, shop) {

                            var id = shop.id, val = shop.value, active = shop.active, selected = "", disabled = "";
                            if (active == "0") {
                                disabled = "disabled";
                                disabled_count++;
                                var error = jQuery("#shopify_select_error").html();
                                error = error + "RyanKikta can no longer access your Shopify Shop : " + val + "<br>";
                                jQuery("#shopify_select_error").html(error);
                            }
                            if ((shopids != null) && (shopids != "")) {
                                if (shopids.indexOf(id) > -1) {
                                    ajax_load_shopify_shop_data(id);
                                    selected = "selected";
                                }
                            }

                            shops_options_select += "<option value='" + id + "' " + disabled + " " + selected + ">" + val + "</option>";
                        });
                    }

                    jQuery('#shopifyshop').append(shops_options_select);
                    jQuery('#shopifyshop').multiSelect({
                        keepOrder: true,
                        afterSelect: function (values) {
                            jQuery("#shopify_select_error").slideUp("slow");
                            jQuery("#product_shopify_error").html("");
                            jQuery.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Just a moment...</h5>'});
                            ajax_load_shopify_shop_data(parseInt(values));
                        },
                        afterDeselect: function (values) {
                            jQuery("#product_shopify_error").html("");
                            jQuery.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Just a moment...</h5>'});
                            jQuery("#shopify_shop_shop_id" + parseInt(values)).hide();
                            var nbre = jQuery("#ms-shopifyshop .ms-selection ul.ms-list > li:visible").length;
                            if (nbre == 0) {
                                jQuery("#shopify_select_error").html("Please select at least one shop from list shopify shops");
                                jQuery("#shopify_select_error").slideDown("slow");
                            }
                            deblock_ui();
                        }
                    });

                },
                complete: function () {
                    deblock_ui();
                }
            });
        }

        function ajax_load_shopify_shop_data(shop_id) {
            if (jQuery("#shopify_shop_shop_id" + shop_id).length) {
                jQuery("#shopify_shop_shop_id" + shop_id).show("fade");
                deblock_ui();
            } else {
                jQuery.ajax({
                    type: "GET",
                    url: "/ajax_shop_call.php?",
                    data: {
                        action: "get_shopify_shop_data",
                        user_id:<?php echo $currentuserid; ?>,
                        shop_id: shop_id,
                        is_edit: is_edit,
                        pa_product_id: pa_product_id
                    },
                    cache: true,
                    dataType: "json",
                    async: false,
                    success: function (data) {

                        if ((data.status == 400) || (data.status == "400")) {
                            if (data.error != undefined) {
                                jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">' + data.error + '</span>');

                            } else {
                                jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">An error has occurred while trying to load shopify shop data. Please try again later</span>');

                            }
                        } else if ((data.status == 200) || (data.status == "200")) {
                            var list_products = data.data.products,
                                list_collections = data.data.collections;
                            if (is_error == 1) {

                                var select_product = post_data["newproduct" + shop_id],
                                    shopifyvendor = post_data["shopifyvendor" + shop_id],
                                    shopifytype = post_data["shopifytype" + shop_id];
                                if ((post_data["collection" + shop_id] != undefined) && (post_data["collection" + shop_id] != "") && (post_data["collection" + shop_id] != null)) {
                                    var selected_collections = post_data["collection" + shop_id].join(",");
                                } else {
                                    var selected_collections = "";
                                }

                            } else {
                                var select_product = data.data.select_product,
                                    shopifyvendor = data.data.shopifyvendor,
                                    shopifytype = data.data.shopifytype,
                                    selected_collections = data.data.selected_collect;
                            }

                            if (shopifyvendor == null || shopifyvendor == "null") {
                                shopifyvendor = "";
                            }
                            if (shopifytype == null || shopifytype == "null") {
                                shopifytype = "";
                            }
                            var product_shop_select = "<select name='newproduct" + shop_id + "' style='width: 400px;'><option value='0' >New Product</option>";

                            jQuery.each(list_products, function (i, product) {
                                var selected = "";
                                if (is_error == 1) {
                                    if ((select_product != null) && (select_product != "")) {
                                        if (select_product.indexOf(product.id) > -1) {
                                            selected = "selected";
                                        }
                                    }
                                }
                                product_shop_select += "<option " + selected + " value='" + product.id + "' " + ">" + product.title + "</option>";
                            });
                            product_shop_select += "</select>";

                            var collections_checkbox = "";
                            jQuery.each(list_collections, function (i, collection) {
                                var checked = "";
                                if (selected_collections != null) {
                                    if (selected_collections.indexOf(collection.id) > -1) {
                                        checked = "checked";
                                    }
                                }
                                collections_checkbox += "<input type='checkbox' id='collection" + shop_id + i + "' class='css-checkbox' name='collection" + shop_id + "[]' style='margin-left: 5px;' value='" + collection.id + "'" + checked + " ><label class='css-label' for='" + shop_id + i + "'>" + collection.title + "</label>&nbsp;&nbsp;";
                            });
                            var shop_html = '<fieldset id="shopify_shop_shop_id' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b style="color:green">' + data.data.shop + '</b></legend><div class="content">';
                            if (is_edit == 1) {
                                if (select_product == "" || select_product == null)
                                    shop_html += '<div style="width: 200px;float: left;"><strong>New Product or <br> Assign to an Existing Product</strong></div><div id="newproduct_' + shop_id + '">' + product_shop_select + '</div><br>';
                                else
                                    shop_html += '<input type="hidden" name="newproduct' + shop_id + '" value="' + select_product + '">';
                            } else
                                shop_html += '<div style="width: 200px;float: left;"><strong>New Product or <br> Assign to an Existing Product</strong> </div><div id="newproduct_' + shop_id + '">' + product_shop_select + '</div><br>';

                            shop_html += '<div style="width: 200px;float: left;"><strong>Product Type </strong> </div><div id="shopify_type_' + shop_id + '"> <input type="text"  value="' + shopifytype + '" name="shopifytype' + shop_id + '" id="shopifytype' + shop_id + '"><br class="clear"></div>'
                                + '<div style="width: 200px;float: left;"><strong>Product Vendor </strong> </div><div id="shopify_vendor_' + shop_id + '"><input type="text" value="' + shopifyvendor + '" name="shopifyvendor' + shop_id + '" id="shopifyvendor' + shop_id + '"><br class="clear"></div>'
                                + '<div style="width: 200px;float: left;"><strong>Collections </strong> </div><div id="shopify_collections_' + shop_id + '">' + collections_checkbox + '<br class="clear"></div>'
                                + '</div></fieldset>';
                            jQuery("#shoppify_shops_data").append(shop_html);
                            jQuery("select[name^='newproduct']").select2({placeholder: "New Product"});
                            jQuery("select[name^='newproduct']").select2().on('change', function () {

                                var shopify_product_id = jQuery(this).val();
                                var name_el = jQuery(this).attr("name");
                                var shop_id = name_el.replace("newproduct", "");
                                jQuery("#newproduct_" + shop_id).parent().find(".product_shopify_existe").remove();
                                if (shopify_product_id != 0) {
                                    check_product_existe(shopify_product_id, shop_id);
                                }


                            });
                        } else {
                            jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">An error has occurred while trying to load shopify shop data. Please try again later</span>');
                        }

                    }, error: function () {
                        jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">An error has occurred while trying to load shopify shop data. Please try again later</span>');
                        deblock_ui();
                    }, complete: function () {
                        deblock_ui();
                    }
                });
            }


        }

        jQuery("select[name^='newproduct']").on('change', function () {
            var shopify_product_id = jQuery(this).val();
            if (shopify_product_id != 0) {
                var name_el = jQuery(this).attr("name");
                var shop_id = name_el.replace("newproduct", "");
                check_product_existe(shopify_product_id, shop_id, user_id);
            }


        });

        function check_product_existe(productId, shop_id, user_id) {

            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "check_product_existe_shopify",
                    shopify_id: productId,
                    shop_id: shop_id,
                    user_id: user_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {

                    if ((data.status == 400) || (data.status == "400")) {
                        if (data.error != undefined) {
                            jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">' + data.error + '</span>');

                        } else {
                            jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">An error has occurred while trying to load shopify shop data. Please try again later</span>');

                        }
                    } else if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#newproduct_" + shop_id).append('<span class="product_shopify_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#newproduct_" + shop_id).parent().find(".product_shopify_existe").remove();
                        }
                    }


                }, error: function () {
                    jQuery("#shoppify_shops_data").append('<span class="product_shopify_error" style="color:red">An error has occurred while trying to load shopify shop data. Please try again later</span>');

                }, complete: function () {
                }
            });
        }

        function deblock_ui() {
            setTimeout(function () {
                jQuery.unblockUI();
            }, 500);
        }

    });
</script>
