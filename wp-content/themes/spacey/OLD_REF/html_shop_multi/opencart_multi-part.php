<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
global $wpdb;

$user_shopids = $wpdb->get_results("select id from wp_users_opencart where users_id=$currentuserid order by id asc", ARRAY_A);
$list_shops = "";
foreach ($user_shopids as $shop) {
    $list_shops .= $shop['id'] . ",";
}
$user_shopids = rtrim($list_shops, ",");

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $has_import = 0;
    $has_edit = true;
    $productid = $_GET['id'];
    $opencartactiveold = $wpdb->get_var("select opencartactive from `wp_users_products` where `id` = $productid");
    $listshop_ids = "";
    $shopids = get_product_meta_shops($productid, "opencart_id");
    if (count($shopids) > 0) {
        foreach ($shopids as $shop_id)
            $listshop_ids .= $shop_id . ",";
    } else {
        $listshop_ids = $wpdb->get_var("select id from wp_users_opencart where users_id=$currentuserid");
    }

    $shopids = rtrim($listshop_ids, ",");
    $selected = ($opencartactiveold == 1) ? "selected='selected'" : "";
    $display = ($opencartactiveold == 0) ? "style='display:none;'" : "";
} else {
    $has_import = (isset($_GET['action']) && $_GET['action'] == "import") ? 1 : 0;
    $has_edit = false;
    $productid = 0;
    $selected = "";
    $display = ($has_import == 0) ? "style='display:none;'" : "";
}

if ($has_import == 1) {
    global $data_shop;
    @extract($data_shop);
    $shop_id = $_GET['shop_id'];
    $op_auth = getOpenCartShop($currentuserid, $shop_id);
    @extract($op_auth);

    $oc_categs = oc_categories_desply_paths($op_auth);
    $oc_manufs = OCManufacturers($op_auth);
}

$has_error = 0;
if (!empty($_SESSION['data'])) {
    extract($_SESSION['data']);
    $has_error = 1;
    $selected = ($opencartactive == 1) ? "selected='selected'" : "";
    $display = ($opencartactive == 0) ? "style='display:none;'" : "";
    $shopids = implode(",", $opencartshop);
}
?>

<div id="opencartspecific">
    <div class="row mt-5">
        <?php if ($has_import == 1) { ?>
            <input type="hidden" name="opencartactive" value="1">
        <?php } else { ?>
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/opencart_tile.png">
                    <div class="input_checkbox add-shop">
                        <label for="opencartactive">Active:</label>
                        <!-- <select name="opencartactive" id="opencartactive">
                            <option value="0">No</option>
                            <option value="1" <?php //echo $selected; ?>>Yes</option>
                        </select> -->
                        <input type="checkbox" name="opencartactive" id="opencartactive" value="1" checked>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div id="opencartholder" class="col-md-6 col-lg-5" <?php // echo $display; ?>>

            <?php if ($has_import == 0) { ?>
                <h3>Opencart Shops</h3>
                <select name="opencartshop[]" multiple="" id="opencartshop"></select>
                <span id="opencart_select_error"></span>

            <?php } else { ?>
                <input type="hidden" name="opencartshop[]" value="<?php echo (int)$_GET['shop_id']; ?>">
            <?php } ?>


            <div id="opencart_data_shops">
                <?php $has_import = 1; ?>
                <?php if ($has_import == 1) { ?>
                    <fieldset id="opencart_data_shop<?php echo $shop_id; ?>" class="multi_shop">
                        <h2>Shop : <?php echo $opencart_domain; ?></h2>
                        <input type="hidden" name="opencartnewproduct<?php echo $shop_id; ?>" value="<?php echo (int)$_GET['shop_prd_id']; ?>">

                        <div class="input_outline">
                            <label for="opencartModel<?php echo $shop_id; ?>">Model</label>
                            <input type="text" name="opencartModel<?php echo $shop_id; ?>" id="model" class="opencart_model" value="<?php echo $opencartModel; ?>"/>
                        </div>


                        <label for="opencartCategory<?php echo $shop_id; ?>">Category</label>
                        <select name="opencartCategory<?php echo $shop_id; ?>" id="opencartcategory<?php echo $shop_id; ?>">
                            <?php
                            foreach ($oc_categs as $ctg_id => $ctg) {
                                $selected_ctg = ($ctg_id == $opencartCategory) ? "selected" : "";
                                echo '<option ' . $selected_ctg . ' value="' . $ctg_id . '">' . $ctg . '</option>';
                            }
                            ?>
                        </select>

                        <label for="manufacturers<?php echo $shop_id; ?>">Manufacturers</label>
                        <select class="oc_manufs" name="manufacturers<?php echo $shop_id; ?>" id="opencartmanuf<?php echo $shop_id; ?>">
                            <?php foreach ($oc_manufs as $oc_manuf) {
                                $selected_mnf = ($oc_manuf->manufacturer_id == $manuf_id) ? "selected" : "";
                                echo '<option data-shop_id="' . $shop_id . '" ' . $selected_mnf . ' value="' . $oc_manuf->manufacturer_id . '">' . $oc_manuf->name . '</option>';
                            } ?>
                        </select>


                        <script>
                            jQuery(document).ready(function ($) {
                                jQuery(".oc_manufs").on('change', function () {
                                    var id_oc_shop = jQuery(this).find(':selected').attr('data-shop_id');
                                    var name = jQuery('#opencartmanuf' + id_oc_shop + ' :selected').text();
                                    var id = jQuery(this).val();

                                    if (id != '0') {
                                        jQuery('#new-manuf' + id_oc_shop).val(name);
                                        jQuery('#new-manuf-id' + id_oc_shop).val(id);
                                    } else {
                                        jQuery('#new-manuf' + id_oc_shop).val('');
                                        jQuery('#new-manuf-id' + id_oc_shop).val('');
                                    }
                                });
                            });
                        </script>

                        <div class="input_outline">
                            <label for="manuf_name<?php echo $shop_id; ?>">Enter New Manufacturer</label>
                            <input type="text" name="manuf_name<?php echo $shop_id; ?>" class="oc_new_manufs" data-shop_id="<?php echo $shop_id; ?>" id="new-manuf<?php echo $shop_id; ?>" placeholder="new manufacture"/>
                            <input type="hidden" name="manuf_id<?php echo $shop_id; ?>" id="new-manuf-id<?php echo $shop_id; ?>" value="<?php echo $manuf_id; ?>"/>
                        </div>

                        <div class="input_outline">
                            <label for="meta_tags<?php echo $shop_id; ?>">Meta Title</label>
                            <input type="text" name="meta_tags<?php echo $shop_id; ?>" value="<?php echo $meta_tags; ?>"/>
                        </div>

                        <div class="input_outline">
                            <label for="meta_tag_keywords<?php echo $shop_id; ?>">Meta Tag Keywords</label>
                            <textarea name="meta_tag_keywords<?php echo $shop_id; ?>"><?php echo $meta_tag_keywords; ?></textarea>
                        </div>

                        <div class="input_outline">
                            <label for="meta_tag_description<?php echo $shop_id; ?>">Meta Tag Description</label>
                            <textarea name="meta_tag_description<?php echo $shop_id; ?>"><?php echo $meta_tag_description; ?></textarea>
                        </div>

                    </fieldset>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        var vars = {};
        var user_shopids = <?php echo json_encode($user_shopids) ?>;
        var all_shop = user_shopids.split(",");
        $.each(all_shop, function (index, shop_id) {
            vars['show_list_products_' + shop_id] = 0;
            vars['lance_list_products_' + shop_id] = 0;
            vars['lance_list_data_' + shop_id] = 0;
        });
        var first_loaded = 0;

        $('#opencartshop').multiSelect({
            keepOrder: true,
            afterSelect: function (values) {
                $("#product_opencart_error").html("");
                $("#opencart_select_error").slideUp("slow");
                if (opencartactive == 0 || first_loaded == 1) {
                    $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                    if (vars['lance_list_data_' + parseInt(values)] == 0) {
                        ajax_opencart_data(parseInt(values));
                        vars['lance_list_data_' + parseInt(values)] = 1;
                    }
                    if (is_edit == 0) {
                        if (vars['lance_list_products_' + parseInt(values)] == 0) {
                            ajax_opencart_products(parseInt(values));
                            vars['lance_list_products_' + parseInt(values)] = 1;
                        }
                    }
                    deblock_ui();
                    get_opencart_data(parseInt(values));
                    if (is_edit == 0)
                        get_opencart_products(parseInt(values));
                }
                if (first_loaded == 0) {
                    if (opencartactive == 1 || relance_call == 1) {
                        var selected_shop = shopids.split(",");
                        $.each(selected_shop, function (index, shop_id) {
                            ajax_opencart_data(shop_id);
                            get_opencart_data(shop_id);
                            vars['lance_list_data_' + shop_id] = 1;
                        });
                    }
                    if (relance_call == 1 && is_edit == 0) {
                        $.each(selected_shop, function (index, shop_id) {
                            ajax_opencart_products(shop_id);
                            get_opencart_products(shop_id);
                            vars['lance_list_products_' + shop_id] = 1;
                        });
                    }
                }
            },
            afterDeselect: function (values) {
                $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                $("#product_opencart_error").html("");
                $("#opencart_select_error").slideUp("slow");
                var nbr = $("#ms-opencartshop .ms-selection ul.ms-list > li:visible").length;
                if (nbr == 0) {
                    $("#opencart_select_error").html("Please select at least one shop from list opencart shops");
                    $("#opencart_select_error").slideDown("slow");
                }
                $("#opencart_data_shop" + parseInt(values)).slideUp("slow");
                deblock_ui();
            }
        });

        var list_products = {};
        var list_cats = {};
        var list_manufs = {};
        var oc_shop = "";
        var lance_list_shop = 0;
        var user_id = <?php echo $currentuserid;?>;
        var shopids = <?php echo json_encode($shopids) ?>;
        var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
        var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
        var opencartactive = <?php echo ($opencartactiveold == 1) ? 1 : 0; ?>;
        var relance_call = <?php echo ($has_error == 1 && $opencartactive == 1) ? 1 : 0; ?>;

        if (opencartactive == 1 || relance_call == 1) {
            ajax_get_list_opencart_shop();
            lance_list_shop = 1;
        }

        $("#opencartactive").on('change', function () {
            if ($(this).val() == 1) {
                if (lance_list_shop == 0) {
                    var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
                    $("#opencartholder").before(loading_shop);
                    ajax_get_list_opencart_shop();
                    $("#loading_shop").remove();
                    lance_list_shop = 1;
                }
                $("#opencartholder").slideDown("slow");
            } else {
                $("#opencartholder").slideUp("slow");
            }
        });

        function ajax_get_list_opencart_shop() {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_opencart_shop", user_id:<?php echo $currentuserid; ?>},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    var Count_Parts = data.length;
                    var i = 0;
                    var disabled = 0;
                    for (i = 0; i < Count_Parts; i++) {
                        var id = data[i].id;
                        var val = data[i].value;
                        var active = data[i].active;
                        $('#opencartshop').multiSelect('addOption', {value: id, text: val});
                        if (shopids != null) {
                            if (shopids.indexOf(id) > -1) {
                                $('#opencartshop').multiSelect('select', id);
                            }
                        }
                        if (active == "0") {
                            $('#opencartshop option[value="' + id + '"]').attr("disabled", true);
                            $('#opencartshop').multiSelect('refresh');
                            disabled++;
                            var error = $("#opencart_select_error").html();
                            error = error + "RyanKikta can no longer access your Woocommerce Shop : " + val + "<br>";
                            $("#opencart_select_error").html(error);
                        }
                    }
                    first_loaded = 1;
                }
            });
        }

        function ajax_opencart_products(shop_id) {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_list_products_opencart_by_shop",
                    user_id: <?php echo $currentuserid; ?>,
                    shop_id: shop_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_products = data.products;
                }, error: function () {
                    $("#product_opencart_error").html("An error has occurred while loading products list");
                }
            });
        }

        function get_opencart_products(shop_id) {
            if (vars['show_list_products_' + shop_id] == 0) {
                var oc_html = "";
                var selected_product = "0";
                if (is_error == 1) {
                    var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                    selected_product = get_select_data_oc(shop_id, 'product', 2, session_data);
                }
                var firstime = 0;
                var select = "<select id='opencartnewproduct" + shop_id + "' name='opencartnewproduct" + shop_id + "' style='width: 400px;'>";
                select += "<option value='0' >New Product</option>";


                $.each(list_products, function (i) {
                    var selected = "";
                    if (firstime == 0) {
                        if (selected_product == list_products[i].id) {
                            selected = "selected='selected'";
                            firstime = 1;
                        }
                    }
                    select += "<option value='" + list_products[i].id + "' " + selected + ">" + list_products[i].name + "</option>";
                });
                select += "</select>";

                oc_html += '<div><div style="width: 200px;float: left;"><strong>New Product or <br /> Assign to an Existing Product</strong></div><div style="float: left; ">' + select + '</div><br class="clear"></div><br />';
                $('#div_model' + shop_id).before(oc_html);
                $("#opencartnewproduct" + shop_id).select2().on('change', function () {
                    var opencarte_id = jQuery(this).val();
                    var name_el = jQuery(this).attr("name");
                    var shop_id = name_el.replace("opencartnewproduct", "");
                    jQuery("#opencartnewproduct" + shop_id).parent().find(".product_opencarte_existe").remove();
                    if (opencarte_id != 0) {
                        check_product_existe_opencarte(opencarte_id, shop_id, user_id);
                    }
                });
                ;
                vars['show_list_products_' + shop_id] = 1;
            }
        }

        function ajax_opencart_data(shop_id) {
            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                var selected_cat = get_select_data_oc(shop_id, 'category', 2, session_data);
                var selected_manuf = get_select_data_oc(shop_id, 'manuf', 2, session_data);
            } else if (is_edit == 1) {
                var selected_cat = get_select_data_oc(shop_id, 'category', 1, <?php echo $productid; ?>);
                var selected_manuf = get_select_data_oc(shop_id, 'manuf', 1, <?php echo $productid; ?>);
            } else {
                var selected_cat = '0';
                var selected_manuf = '0';
            }

            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_list_data_opencart_shop",
                    user_id:<?php echo $currentuserid; ?>,
                    shop_id: shop_id,
                    cats_id: selected_cat,
                    manuf_id: selected_manuf
                },
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_manufs = $.trim(data.manufs);
                    list_cats = $.trim(data.cats);
                    oc_shop = data.shop;
                }, error: function (e) {

                }
            });
        }

        function get_opencart_data(shop_id) {
            var oc_html = "";
            var selected_manuf = '0';
            var current_meta_title = "";
            var current_meta_key = "";
            var current_meta_desc = "";
            var selected_model = "";
            var selected_newmanuf_name = "";
            var selected_newmanuf_id = "";

            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                selected_newmanuf_name = get_select_data_oc(shop_id, 'manuf_name', 2, session_data);
                selected_newmanuf_id = get_select_data_oc(shop_id, 'manuf_id', 2, session_data);
                selected_model = get_select_data_oc(shop_id, 'model', 2, session_data);
                selected_manuf = get_select_data_oc(shop_id, 'manuf', 2, session_data);
                current_meta_title = get_select_data_oc(shop_id, 'meta_title', 2, session_data);
                current_meta_key = get_select_data_oc(shop_id, 'meta_key', 2, session_data);
                current_meta_desc = get_select_data_oc(shop_id, 'meta_desc', 2, session_data);
            } else if (is_edit == 1) {
                selected_model = get_select_data_oc(shop_id, 'model', 1, <?php echo $productid; ?>);
                selected_manuf = get_select_data_oc(shop_id, 'manuf', 1, <?php echo $productid; ?>);
                current_meta_title = get_select_data_oc(shop_id, 'meta_title', 1, <?php echo $productid; ?>);
                current_meta_key = get_select_data_oc(shop_id, 'meta_key', 1, <?php echo $productid; ?>);
                current_meta_desc = get_select_data_oc(shop_id, 'meta_desc', 1, <?php echo $productid; ?>);
            }

            oc_html += '<fieldset id="opencart_data_shop' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b style="color:green">' + oc_shop + '</b></legend>';

            //MODEL
            oc_html += '<div id="div_model' + shop_id + '" style="width: 200px;float: left;"><strong>Model</strong></div><input type="text" name="opencartModel' + shop_id + '" id="model' + shop_id + '" class="opencart_model" value="' + selected_model + '"/>';

            // cats
            if (list_cats != "")
                oc_html += '<br><div id="opencartCategory' + shop_id + '"><div style="width: 200px;float: left;"><strong>OpenCart Category</strong></div>' + list_cats + '<br class="clear"></div>';

            if (list_manufs != "")
                oc_html += '<br><div id="opencartManufacturer' + shop_id + '"><div style="width: 200px;float: left;"><strong>OpenCart Manufacturers</strong></div>' + list_manufs + '<br class="clear"></div>';

            //Manufacturers
            oc_html += '<br><div class="clearfix"></div>'
                + '<div style = "width: 200px; float: left;"><strong>Enter New Manufacturer</strong></div>'
                + '<div style="float: left; ">'
                + '<input type="text" class="oc_new_manufs" data-shop_id="' + shop_id + '" value="' + selected_newmanuf_name + '" name="manuf_name' + shop_id + '" id="new-manuf' + shop_id + '" placeholder="new manufacture" />'
                + '<input type="hidden" name="manuf_id' + shop_id + '" value="' + selected_manuf + '" id="new-manuf-id' + shop_id + '"/>'
                + '</div>';

            //Meta Title, Meta Tag, Meta Description, 
            oc_html += '<br><div class="clearfix"></div>'
                + '<div style="width: 200px;float: left;"><strong>Meta Title</strong></div>'
                + '<div style="float: left; "></div>'
                + '<input type="text" name="meta_tags' + shop_id + '" value="' + current_meta_title + '"/>'

                + '<br><div class="clearfix"></div>'
                + '<div style="width: 200px;float: left;"><strong>Meta Tag Keywords</strong></div>'
                + '<div style="float: left; ">'
                + '<textarea name="meta_tag_keywords' + shop_id + '">' + current_meta_key + '</textarea> '
                + '</div>'

                + '<br><div class="clearfix"></div>'
                + '<div style="width: 200px;float: left;"><strong>Meta Tag Description</strong></div>'
                + '<div style="float: left; ">'
                + '<textarea name="meta_tag_description' + shop_id + '">' + current_meta_desc + '</textarea> '
                + '</div><div class="clearfix"></div>';

            oc_html += '</fieldset>';
            if ($("#opencart_data_shop" + shop_id).length > 0)
                $("#opencart_data_shop" + shop_id).slideDown("slow");
            else {
                $("#opencart_data_shops").append(oc_html);
                $("#opencartshippiingclass" + shop_id).select2();
                $("#opencartcategory" + shop_id).select2();
                $("#opencartmanuf" + shop_id).select2();

                $(".oc_manufs").on('change', function () {
                    var id_oc_shop = jQuery(this).attr('data-shop_id');
                    var name = jQuery('#opencartmanuf' + id_oc_shop + ' :selected').text();
                    var id = jQuery(this).val();

                    if (id != '0') {
                        jQuery('#new-manuf' + id_oc_shop).val(name);
                        jQuery('#new-manuf-id' + id_oc_shop).val(id);
                    } else {
                        jQuery('#new-manuf' + id_oc_shop).val('');
                        jQuery('#new-manuf-id' + id_oc_shop).val('');
                    }
                });
            }
        }

        function get_select_data_oc(shop_id, field, type, prd_data) {
            var selected_data = "";
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_selected_data_opencart_shop",
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

        function check_product_existe_opencarte(opencarte_id, shop_id, user_id) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "check_product_existe_opencarte",
                    opencarte_id: opencarte_id,
                    shop_id: shop_id,
                    user_id: user_id
                },
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#opencartnewproduct" + shop_id).parent().append('<span class="product_opencarte_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#opencartnewproduct" + shop_id).parent().find(".product_opencarte_existe").remove();
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