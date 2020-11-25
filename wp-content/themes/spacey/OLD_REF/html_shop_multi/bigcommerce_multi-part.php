<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;

$user_shopids = $wpdb->get_results("select id from wp_users_bigcommerce where users_id=$currentuserid order by id asc", ARRAY_A);
$list_shops = "";
foreach ($user_shopids as $shop) {
    $list_shops .= $shop['id'] . ",";
}
$user_shopids = rtrim($list_shops, ",");
$is_big_embeded = (isset($_GET['app']) && $_GET['app'] == "bigcommerce") ? 1 : 0;
if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $has_edit = true;
    $has_import = 0;
    $productid = $_GET['id'];
    $bigcommerceactiveold = $wpdb->get_var("select bigcommerceactive from `wp_users_products` where `id` = $productid");
    $listshop_ids = "";
    $shopids = get_product_meta_shops($productid, "bigcommerce_id");
    foreach ($shopids as $shop_id) {
        $listshop_ids .= $shop_id . ",";
    }
    $shopids = rtrim($listshop_ids, ",");
    $selected = ($bigcommerceactiveold == 1) ? "selected='selected'" : "";
    $display = ($bigcommerceactiveold == 0) ? "style='display:none;'" : "";
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
    $bigcommerceinfo = trim($bigcommerceinfo);
    $auth = getBigcommerceShopbyId($_GET['shop_id']);
    @extract($auth);
}
$has_error = 0;
if (!empty($_SESSION['data'])) {
    @extract($_SESSION['data']);
    $has_error = 1;
    $selected = ($bigcommerceactive == 1) ? "selected='selected'" : "";
    $display = ($bigcommerceactive == 0) ? "style='display:none;'" : "";
    $shopids = implode(",", $bigcommerceshop);
}
if ($is_big_embeded == 1) {
    $display = "";
    $store_hash = $_SESSION['store_hash' . $currentuserid];
    if ($store_hash == "")
        $store_hash = $wpdb->get_var("select store_hash from wp_users_bigcommerce where `users_id` = '$currentuserid'");
    $user_shopids = $wpdb->get_var("select id from wp_users_bigcommerce where `store_hash` = '$store_hash'");
}
?>
<div id="bigcommercespecific">
    <?php if ($is_big_embeded == 0) { ?>
        <?php if ($has_import == 0) { ?>
            <div class="row mt-5">
                <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/bigcommerce_tile.png">
                        <div class="input_checkbox add-shop">
                            <label>Active:</label>
                            <!-- <select name="bigcommerceactive" id="bigcommerceactive">
                                <option value="0">No</option>
                                <option value="1" <?php echo $selected; ?>>Yes</option>
                            </select> -->
                            <input type="checkbox" name="bigcommerceactive" id="bigcommerceactive" value="1" checked>
                        </div>
                    </div>
                </div>
        <?php }
    } ?>
    <div id="bigcommerceholder" class="col-md-6 col-lg-5" <?php echo $display; ?>>
        <!-- the following is a manual override of the integration checker -->
        <?php $has_import = 1; ?>
        <!-- okay resume normal routine starting now  -->
        <?php if ($has_import == 0) {
            if ($is_big_embeded == 0) { ?>
                
                    <div>Bigcommerce Shops </div>
                    <div>
                        <select name="bigcommerceshop[]" multiple="" style="width: 400px;" id="bigcommerceshop"></select>
                    </div>
                    <span id="bigcommerce_select_error"></span>
                </?>
            <?php } else { ?>
                <input type="hidden" name="bigcommerceactive" value="1">
                <input type="hidden" name="bigcommerceshop[]" value="<?php echo (int)$user_shopids; ?>">
            <?php }
        } ?>
        <div id="bigcommerce_data_shops">
            <?php if ($has_import == 1) { ?>
                <fieldset id="bigcommerce_data_shop<?php echo $shop_id; ?>" class="multi_shop">
                    <h2>SETTINGS: <?php echo stripcslashes($storename); ?>Shop Title</h2>
                    <input type="hidden" name="bigcommerceactive" value="1">
                    <input type="hidden" name="bigcommerceshop[]" value="<?php echo (int)$_GET['shop_id']; ?>">
                    <input type="hidden" name="bigcommercenewproduct<?php echo $shop_id; ?>" value="<?php echo (int)$_GET['shop_prd_id']; ?>">
                    <div id="big_desc<?php echo $shop_id; ?>">
                        <label>Warranty Information</label>
                        <span class="small-caption">Use this field to provide additional information about your product. You can also include information on the quality, material, and design description etc.</span>
                        <div class="input_outline">
                            <textarea name="bigcommerceinfo<?php echo $shop_id; ?>" id="bigcmmerce_info<?php echo $shop_id; ?>"> <?php echo $bigcommerceinfo; ?> </textarea>
                        </div>
                    </div>
                </fieldset>
            <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        var is_import = <?php echo ($has_import == 1) ? 1 : 0;?>;

        if (is_import == 1) {
            var big_shop_id = <?php echo ($shop_id && $shop_id != 0) ? $shop_id : 0;?>;
            var product_shop_id = <?php echo (int)$_GET['shop_prd_id'];?>;
            ajax_bigcommerce_data(big_shop_id);
            get_bigcommerce_data(big_shop_id);
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

            $('#bigcommerceshop').multiSelect({
                keepOrder: true,
                afterSelect: function (values) {
                    $("#bigcommerce_select_error").slideUp("slow");
                    if (bigcommerceactive == 0 || first_loaded == 1) {
                        $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                        if (vars['lance_list_data_' + parseInt(values)] == 0) {
                            ajax_bigcommerce_data(parseInt(values));
                            vars['lance_list_data_' + parseInt(values)] = 1;
                        }
                        if (is_edit == 0) {
                            if (vars['lance_list_products_' + parseInt(values)] == 0) {
                                ajax_bigcommerce_products(parseInt(values));
                                vars['lance_list_products_' + parseInt(values)] = 1;
                            }
                        }
                        deblock_ui();
                        get_bigcommerce_data(parseInt(values));
                        if (is_edit == 0)
                            get_bigcommerce_products(parseInt(values));
                    }
                    if (first_loaded == 0) {
                        if (bigcommerceactive == 1 || relance_call == 1) {
                            var selected_shop = shopids.split(",");
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_bigcommerce_data(shop_id);
                                get_bigcommerce_data(shop_id);
                                vars['lance_list_data_' + shop_id] = 1;
                            });
                        }
                        if (relance_call == 1 && is_edit == 0) {
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_bigcommerce_products(shop_id);
                                get_bigcommerce_products(shop_id);
                                vars['lance_list_products_' + shop_id] = 1;
                            });
                        }
                    }
                },
                afterDeselect: function (values) {
                    $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                    $("#bigcommerce_select_error").slideUp("slow");
                    var nbr = $("#ms-bigcommerceshop .ms-selection ul.ms-list > li:visible").length;
                    if (nbr == 0) {
                        $("#bigcommerce_select_error").html("Please select at least one shop from list bigcommerce shops");
                        $("#bigcommerce_select_error").slideDown("slow");
                    }
                    deblock_ui();
                    $("#bigcommerce_data_shop" + parseInt(values)).slideUp("slow");
                }
            });

            var list_products = {};
            var list_brand = {};
            var list_cats = {};
            var big_shop = "";
            var lance_list_shop = 0;
            var user_id = <?php echo $currentuserid;?>;
            var shopids = <?php echo json_encode($shopids) ?>;
            var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
            var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
            var is_embeded = <?php echo ($is_big_embeded == 1) ? 1 : 0 ?>;
            var bigcommerceactive = <?php echo ($bigcommerceactiveold == 1) ? 1 : 0; ?>;
            var relance_call = <?php echo ($has_error == 1 && $bigcommerceactive == 1) ? 1 : 0;?>;

            if (bigcommerceactive == 1 || relance_call == 1) {
                ajax_get_list_bigcommerce_shop();
                lance_list_shop = 1;
            }

            if (is_embeded == 1) {
                ajax_bigcommerce_data(user_shopids);
                get_bigcommerce_data(user_shopids);
                vars['lance_list_data_' + user_shopids] = 1;
                if (is_edit == 0) {
                    ajax_bigcommerce_products(user_shopids);
                    get_bigcommerce_products(user_shopids);
                    vars['lance_list_products_' + user_shopids] = 1;
                }
            }

            $("#bigcommerceactive").on('change', function () {
                if ($(this).val() == 1) {
                    if (lance_list_shop == 0) {
                        var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
                        $("#bigcommerceholder").before(loading_shop);
                        ajax_get_list_bigcommerce_shop();
                        $("#loading_shop").remove();
                        lance_list_shop = 1;
                    }
                    $("#bigcommerceholder").slideDown("slow");
                } else {
                    $("#bigcommerceholder").slideUp("slow");
                }

            });

        }

        function ajax_get_list_bigcommerce_shop() {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_bigcommerce_shop", user_id:<?php echo $currentuserid; ?>},
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
                        $('#bigcommerceshop').multiSelect('addOption', {value: id, text: val});
                        if (shopids != null) {
                            if (shopids.indexOf(id) > -1) {
                                $('#bigcommerceshop').multiSelect('select', id);
                            }
                        }
                        if (active == "0") {
                            $('#bigcommerceshop option[value="' + id + '"]').attr("disabled", true);
                            $('#bigcommerceshop').multiSelect('refresh');
                            disabled++;
                            var error = $("#bigcommerce_select_error").html();
                            error = error + "RyanKikta can no longer access your Bigcommerce Shop : " + val + "<br>";
                            $("#bigcommerce_select_error").html(error);
                        }
                    }
                    first_loaded = 1;
                }
            });
        }

        function ajax_bigcommerce_products(shop_id) {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_products_bigcommerce_shop", shop_id: shop_id},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_products = data.products;
                }
            });
        }

        function get_bigcommerce_products(shop_id) {

            if (vars['show_list_products_' + shop_id] == 0) {
                var big_html = "";
                var selected_product = "0";
                if (is_error == 1) {
                    var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true));?>';
                    selected_product = get_select_data_big(shop_id, 'product', 2, session_data);
                }
                var firstime = 0;
                var selectproduct = "<option value='0' >New Product</option>";
                $.each(list_products, function (id, name) {
                    var selected = "";
                    if (firstime == 0) {
                        if (selected_product == id) {
                            selected = "selected='selected'";
                            firstime = 1;
                        }
                    }
                    selectproduct += "<option value='" + id + "' " + selected + ">" + name + "</option>";
                });

                big_html += '<div><div style="width: 200px;float: left;">New Product or <br /> Assign to an Existing Product</div><div style="float: left; "><select name="bigcommercenewproduct' + shop_id + '" style="width: 400px;" id="bigcommercenewproduct' + shop_id + '">' + selectproduct + '</select></div><br class="clear"></div>';
                $('#big_featured' + shop_id).before(big_html);
                $("#bigcommercenewproduct" + shop_id).select2().on('change', function () {
                    var bigcommerce_id = jQuery(this).val();
                    var name_el = jQuery(this).attr("name");
                    var shop_id = name_el.replace("bigcommercenewproduct", "");
                    jQuery("#bigcommercenewproduct" + shop_id).parent().find(".product_bigcommerce_existe").remove();
                    if (bigcommerce_id != 0) {
                        check_product_existe_bigcommerce(bigcommerce_id, shop_id, user_id);
                    }
                });
                vars['show_list_products_' + shop_id] = 1;
            }
        }

        function ajax_bigcommerce_data(shop_id) {

            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_data_bigcommerce_shop", shop_id: shop_id},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_brand = data.brands;
                    list_cats = data.cats;
                    big_shop = data.shop;
                }
            });
        }

        function get_bigcommerce_data(shop_id) {

            var big_html = "";
            var selected_featured = "0";
            var selected_brand = '0';
            var selected_cat = '0';
            var current_short_desc = "";
            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true));?>';
                selected_featured = get_select_data_big(shop_id, 'featured_product', 2, session_data);
                selected_brand = get_select_data_big(shop_id, 'brands', 2, session_data);
                selected_cat = get_select_data_big(shop_id, 'cats', 2, session_data);
                current_short_desc = get_select_data_big(shop_id, 'shortdesc', 2, session_data);
                current_short_desc = current_short_desc.replace("\\", "");
            } else if (is_edit == 1) {
                selected_featured = get_select_data_big(shop_id, 'featured_product', 1,<?php echo $productid; ?>);
                selected_brand = get_select_data_big(shop_id, 'brands', 1,<?php echo $productid; ?>);
                selected_cat = get_select_data_big(shop_id, 'cats', 1,<?php echo $productid; ?>);
                current_short_desc = get_select_data_big(shop_id, 'shortdesc', 1,<?php echo $productid; ?>);
            } else if (is_import == 1) {
                selected_featured = <?php echo ($bigcommercefeaturedproduct && $bigcommercefeaturedproduct != 0) ? $bigcommercefeaturedproduct : 0;?>;
                selected_brand = <?php echo ($bigcommercebrand && $bigcommercebrand != 0) ? $bigcommercebrand : 0;?>;
                selected_cat = <?php echo ($bigcommercecategory && $bigcommercecategory != 0) ? $bigcommercecategory : 0;?>;
            }
            var checked_featured = (selected_featured == 1) ? "checked='checked'" : "";
            // list brands
            var firstime = 0;
            var selectbrands = "<option value='0'>-- Choose an Existing Brand --</option>";

            $.each(list_brand, function (id, title) {
                var selected = "";
                if (firstime == 0) {
                    if (selected_brand == id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                selectbrands += "<option value='" + id + "' " + selected + ">" + title + "</option>";
            });
            // list cats        
            var firstime = 0;
            var selectcats = "<option value='0'>-- Choose an Existing Category --</option>";
            $.each(list_cats, function (id, title) {
                var selected = "";
                if (firstime == 0) {
                    if (selected_cat == id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                selectcats += "<option value='" + id + "' " + selected + ">" + title + "</option>";
            });
            big_html += '<fieldset id="bigcommerce_data_shop' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b style="color:green">' + big_shop + '</b></legend>';
            // featured product
            var data_html = '<div id="big_featured' + shop_id + '"><div style="width: 200px;float: left;line-height: 40px;">Featured Product</div><div style="float: left;line-height: 40px;"><input type="checkbox" value="1" name="bigcommercefeaturedproduct' + shop_id + '" ' + checked_featured + '/> Yes, this is a featured product</div><br class="clear"></div><br>';
            // brand
            data_html += '<div id="big_brand' + shop_id + '"><div style="width: 200px;float: left;">Brand Name</div><div style="float: left; "><select name="bigcommercebrand' + shop_id + '"  id="bigcommercebrand' + shop_id + '" style="height: 25px;">' + selectbrands + '</select></div><br class="clear"></div><br>';
            // cats
            if (list_cats != "")
                data_html += '<div id="big_category' + shop_id + '"><div style="width: 200px;float: left;">Category</div><div style="float: left; "><select name="bigcommercecategory' + shop_id + '"  id="bigcommerce_cats' + shop_id + '" class="bigcommerce_cats" style="height: 25px;">' + selectcats + '</select></div><br class="clear"></div></div>';
            big_html += data_html;
            // short description
            big_html += '<div style="width: 100%;float: left;margin-top:25px"><strong>Warranty Information</strong><br /><span class="italic_text">Use this field to provide additional information about your product. You can also include information.<br /></span><br /><br /><textarea style="height: 150px;width: 750px;" name="bigcommerceinfo' + shop_id + '" id="bigcmmerce_info' + shop_id + '">' + current_short_desc + '</textarea></div><br class="clear">';
            big_html += '</fieldset>';
            if ($("#bigcommerce_data_shop" + shop_id).length > 0 && is_import == 0)
                $("#bigcommerce_data_shop" + shop_id).slideDown("slow");
            else {
                if (is_import == 0)
                    $("#bigcommerce_data_shops").append(big_html);
                else
                    $("#big_desc" + shop_id).before(data_html);
                $("#bigcommercebrand" + shop_id).select2();
                $("#bigcommerce_cats" + shop_id).select2();
            }
        }

        function get_select_data_big(shop_id, field, type, prd_data) {
            var selected_data = "";
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_selected_data_bigcommerce_shop",
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

        function check_product_existe_bigcommerce(bigcommerce_id, shop_id, user_id) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "check_product_existe_bigcommerce",
                    bigcommerce_id: bigcommerce_id,
                    shop_id: shop_id,
                    user_id: user_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#bigcommercenewproduct" + shop_id).parent().append('<span class="product_bigcommerce_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#bigcommercenewproduct" + shop_id).parent().find(".product_bigcommerce_existe").remove();
                        }
                    }
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