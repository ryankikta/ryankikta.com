<?php
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
global $wpdb;

$whomadeitselect = array('I did' => 'i_did', 'A member of my shop' => 'collective', 'Another company or person' => 'someone_else');

$issupply_select = array('A finished product' => 0, 'A supply or tool to make things' => 1);

$whenmade_recently = array('2010  - 2018' => '2010_2018', '2000s' => '2000_2009', '1999  - 1999' => '1999_1999');

$whenmade_vintage = array('Before 1999' => 'before_1999', '1990  - 1998' => '1990_1998', '1980s' => '1980s', '1970s' => '1970s', '1960s' => '1960s', '1950s' => '1950s', '1940s' => '1940s', '1930s' => '1930s', '1920s' => '1920s', '1910s' => '1910s', '1900 - 1909' => '1900s');

$style = array('Abstract', 'African', 'Art Deco', 'Art Nouveau', 'Asian', 'Athletic', 'Avant Garde', 'Beach', 'Boho', 'Burlesque', 'Cottage Chic', 'Country Western',
    'Edwardian', 'Fantasy', 'Folk', 'Goth', 'High Fashion', 'Hip Hop', 'Hippie', 'Hipster', 'Historical', 'Hollywood Regency', 'Industrial', 'Kawaii', 'Kitsch',
    'Mediterranean', 'Mid Century', 'Military', 'Minimalist', 'Mod', 'Modern', 'Nautical', 'Neoclassical', 'Preppy', 'Primitive', 'Regency', 'Renaissance',
    'Resort', 'Retro', 'Rocker', 'Rustic', 'Sci Fi', 'Southwestern', 'Spooky', 'Steampunk', 'Techie', 'Traditional', 'Tribal', 'Victorian', 'Waldorf', 'Woodland', 'Zen');

$recipientselect = array('Babies' => 'babies', 'Baby Boys' => 'baby_boys', 'Baby Girls' => 'baby_girls', 'Birds' => 'birds', 'Boys' => 'boys', 'Cats' => 'cats',
    'Children' => 'children', 'Dogs' => 'dogs', 'Girls' => 'girls', 'Men' => 'men', 'Pets' => 'pets', 'Teen Boys' => 'teen_boys',
    'Teen Girls' => 'teen_girls', 'Teens' => 'teens', 'Unisex Adults' => 'unisex_adults', 'Women' => 'women');

$occasionselect = array('Anniversary' => 'anniversary', 'Baptism' => 'baptism', 'Bar or Bat Mitzvah' => 'bar_or_bat_mitzvah', 'Birthday' => 'birthday', 'Canada Day' => 'canada_day',
    'Chinese New Year' => 'chinese_new_year', 'Christmas' => 'christmas', 'Cinco de Mayo' => 'cinco_de_mayo', 'Confirmation' => 'confirmation', 'Day of the Dead' => 'day_of_the_dead',
    'Easter' => 'easter', 'Eid' => 'eid', 'Engagement' => 'engagement', "Father's Day" => "fathers_day", 'Get Well' => 'get_well', 'Graduation' => 'graduation', 'Halloween' => 'halloween',
    'Hanukkah' => 'hanukkah', 'Housewarming' => 'housewarming', 'July 4th' => 'july_4th', 'Kwanza' => 'kwanza', "Mother's Day" => "mothers_day", 'New Baby' => 'new_baby', "New Year's" => "new_years",
    'Prom' => 'prom', 'Quinceanera' => 'quinceanera', 'Retirement' => 'retirement', "St. Patrick's Day" => "st_patricks_day", 'Sweet 16' => 'sweet_16', "Sympathy's" => "sympathy", 'Thanksgiving' => 'thanksgiving', 'Valentine' => 'valentines', 'Wedding' => 'wedding');

$user_shopids = $wpdb->get_results("select id from wp_users_etsy where users_id=$currentuserid order by id asc", ARRAY_A);
$list_shops = "";
$cats = array();
foreach ($user_shopids as $shop) {
    $list_shops .= $shop['id'] . ",";
    $shop_id = $shop['id'];
    $etsy_auth = getEtsyShopById($currentuserid, $shop_id);
    $cats['cats'][$shop_id] = GetEtsyCategory($etsy_auth);
    $cats['cats_childs'][$shop_id] = getEtsyCategoryChildren($etsy_auth);
}
$user_shopids = rtrim($list_shops, ",");

if (isset($_GET['id']) && intval($_GET['id']) != 0) {
    $has_edit = true;
    $has_import = 0;
    $productid = $_GET['id'];
    $etsyactiveold = $wpdb->get_var("select etsyactive from `wp_users_products` where `id` = $productid");
    $listshop_ids = "";
    $shopids = get_product_meta_shops($productid, "etsy_id");
    if (count($shopids) > 0) {
        foreach ($shopids as $shop_id) {
            $listshop_ids .= $shop_id . ",";
        }
    } elseif ($etsyactiveold == 1) {
        $listshop_ids = $wpdb->get_var("select id from wp_users_etsy where users_id=$currentuserid");
    }

    $shopids = rtrim($listshop_ids, ",");
    $selected = ($etsyactiveold == 1) ? "selected='selected'" : "";
    $display = ($etsyactiveold == 0) ? "style='display:none;'" : "";
} else {
    $has_edit = false;
    $productid = 0;
    $has_import = (isset($_GET['action']) && $_GET['action'] == "import") ? 1 : 0;
    $selected = "";
    //commenting out for dev purposes
    // $display = ($has_import == 0) ? "style='display:none;'" : "";
}
if ($has_import == 1) {
    global $data_shop;
    @extract($data_shop);
    $shop_id = $_GET['shop_id'];
}

$has_error = 0;
if (!empty($_SESSION['data'])) {
    @extract($_SESSION['data']);
    $has_error = 1;
    $selected = ($etsyactive == 1) ? "selected='selected'" : "";
    $display = ($etsyactive == 0) ? "style='display:none;'" : "";
    $shopids = implode(",", $etsyshop);
}
?>

<div id="etsyspecific">

    <?php if ($has_import == 0) { ?>
        <div class="row">
            <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <img class="mr-4" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/etsy_tile.png">
                    <div class="input_checkbox add-shop">
                        <label>Active:</label>
                    <!-- <select name="etsyactive" id="etsyactive">
                        <option value="0">No</option>
                        <option value="1" <?php //echo $selected; ?>>Yes</option>
                    </select></div> -->
                    <input class="" type="checkbox" name="etsyactive" id="etsyactive" value="1">
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="etsyholder" <?php echo $display; ?>>
        <!-- the following is a manual override of the integration checker -->
        <?php $has_import = 1; ?>
        <!-- okay resume normal routine starting now  -->
        <?php if ($has_import == 0) { ?>
            <div>
                <div>Etsy Shops</div>
                <div>
                    <select name="etsyshop[]" multiple="" style="width: 400px;" id="etsyshop"></select>
                </div>
                <span id="etsy_select_error"></span>
            </div>
            <br/>
        <?php } ?>

        <div id="etsy_data_shops"></div>
    </div>
</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        var is_import = <?php echo ($has_import == 1) ? 1 : 0; ?>;

        var vars = {};
        var cats = <?php echo json_encode($cats['cats']) ?>;
        var cats_childs = <?php echo json_encode($cats['cats_childs']) ?>;
        var allcats = JSON.stringify(cats);
        var allcats_childs = JSON.stringify(cats_childs);
        $.each($.parseJSON(allcats), function (shop_id, elem) {
            vars['cats_etsy_' + shop_id] = JSON.stringify(elem);
        });
        $.each($.parseJSON(allcats_childs), function (shop_id, elem) {
            vars['cats_childs_etsy_' + shop_id] = JSON.stringify(elem);
        });

        if (is_import == 1) {
            var etsy_shop_id = <?php echo ($shop_id && $shop_id != 0) ? $shop_id : 0; ?>;
            ajax_etsy_data(etsy_shop_id);
            get_etsy_data(etsy_shop_id);
        }

        if (is_import == 0) {
            var user_shopids = <?php echo json_encode($user_shopids) ?>;
            var all_shop = user_shopids.split(",");
            $.each(all_shop, function (index, shop_id) {
                vars['show_list_products_' + shop_id] = 0;
                vars['lance_list_products_' + shop_id] = 0;
                vars['lance_list_data_' + shop_id] = 0;
            });
            var first_loaded = 0;

            $('#etsyshop').multiSelect({
                keepOrder: true,
                afterSelect: function (values) {
                    $("#etsy_select_error").slideUp("slow");
                    if (etsyactive == 0 || first_loaded == 1) {
                        $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait ...</h5>'});
                        if (vars['lance_list_data_' + parseInt(values)] == 0) {
                            ajax_etsy_data(parseInt(values));
                            vars['lance_list_data_' + parseInt(values)] = 1;
                        }
                        if (is_edit == 0) {
                            if (vars['lance_list_products_' + parseInt(values)] == 0) {
                                ajax_etsy_products(parseInt(values));
                                vars['lance_list_products_' + parseInt(values)] = 1;
                            }
                        }
                        deblock_ui();
                        get_etsy_data(parseInt(values));
                        if (is_edit == 0)
                            get_etsy_products(parseInt(values));
                    }
                    if (first_loaded == 0) {
                        if (etsyactive == 1 || relance_call == 1) {
                            var selected_shop = shopids.split(",");
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_etsy_data(shop_id);
                                get_etsy_data(shop_id);
                                vars['lance_list_data_' + shop_id] = 1;
                            });
                        }
                        if (relance_call == 1 && is_edit == 0) {
                            $.each(selected_shop, function (index, shop_id) {
                                ajax_etsy_products(shop_id);
                                get_etsy_products(shop_id);
                                vars['lance_list_products_' + shop_id] = 1;
                            });
                        }
                    }
                },
                afterDeselect: function (values) {
                    $.blockUI({message: '<h5 style="padding-top: 13px;"><img style="display: inline-block;" src="<?php echo get_stylesheet_directory_uri(); ?>/css/busy.gif" /> Please wait...</h5>'});
                    $("#etsy_select_error").slideUp("slow");
                    var nbr = $("#ms-etsyshop .ms-selection ul.ms-list > li:visible").length;
                    if (nbr == 0) {
                        $("#etsy_select_error").html("Please select at least one shop from list etsy shops");
                        $("#etsy_select_error").slideDown("slow");
                    }
                    deblock_ui();
                    $("#etsy_data_shop" + parseInt(values)).slideUp("slow");
                }
            });

            var list_products = {};
            var list_sections = {};
            var list_shipping = {};
            var etsy_shop = "";
            var lance_list_shop = 0;
            var user_id = <?php echo $currentuserid;?>;
            var shopids = <?php echo json_encode($shopids) ?>;
            var is_error = <?php echo ($has_error == 1) ? 1 : 0 ?>;
            var is_edit = <?php echo ($has_edit) ? 1 : 0 ?>;
            var etsyactive = <?php echo ($etsyactiveold == 1) ? 1 : 0; ?>;
            var relance_call = <?php echo ($has_error == 1 && $etsyactive == 1) ? 1 : 0; ?>;

            if (etsyactive == 1 || relance_call == 1) {
                ajax_get_list_etsy_shop();
                lance_list_shop = 1;
            }

            $("#etsyactive").on('change', function () {
                if ($(this).val() == 1) {
                    if (lance_list_shop == 0) {
                        var loading_shop = '<div id="loading_shop" style="display: inline-block;"><center><img src="/img/ajax-loader.gif">Please wait</center></div>';
                        $("#etsyholder").before(loading_shop);
                        ajax_get_list_etsy_shop();
                        $("#loading_shop").remove();
                        lance_list_shop = 1;
                    }
                    $("#etsyholder").slideDown("slow");
                } else {
                    $("#etsyholder").slideUp("slow");
                }

            });

        }

        function ajax_get_list_etsy_shop() {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_etsy_shop", user_id:<?php echo $currentuserid; ?>},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
		    console.log('data', data);
                    var selectoptions = "";
                    var Count_Parts = data.length;
                    var i = 0;
                    var disabled = 0;
                    for (i = 0; i < Count_Parts; i++) {
                        var id = data[i].id;
			if(data[i].val == "" || data[i].val == null){
			    var val = 'My Etsy Shop';
			}else{
			    var val = data[i].val;	
			}
                        var active = data[i].active;
                        $('#etsyshop').multiSelect('addOption', {value: id, text: val});
                        if (shopids != null) {
                            if (shopids.indexOf(id) > -1) {
                                $('#etsyshop').multiSelect('select', id);
                            }
                        }
                        if (active == "0") {
                            $('#etsyshop option[value="' + id + '"]').attr("disabled", true);
                            $('#etsyshop').multiSelect('refresh');
                            disabled++;
                            var error = $("#etsy_select_error").html();
                            error = error + "RyanKikta can no longer access your etsy Shop : " + val + "<br>";
                            $("#etsy_select_error").html(error);
                        }
                    }
                    first_loaded = 1;
                }
            });
        }

        function ajax_etsy_products(shop_id) {
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_products_etsy_shop", shop_id: shop_id, user_id:<?php echo $currentuserid; ?>},
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_products = data.products;
                }
            });
        }

        function get_etsy_products(shop_id) {

            if (vars['show_list_products_' + shop_id] == 0) {
                var etsy_html = "";
                var selected_product = "0";
                if (is_error == 1) {
                    var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                    selected_product = get_select_data_etsy(shop_id, 'product', 2, session_data);
                }
                var firstime = 0;
                var select = "<select id='etsynewproduct" + shop_id + "' name='etsynewproduct" + shop_id + "' style='width: 400px;'>";
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
                etsy_html += '<div><div style="width: 175px;float: left;">New Product or <br /> Assign to an Existing Product</div><div class="product" style="float: left; ">' + select + '</div></div><br class="clear"><br class="clearfix">';

                $('#etsy_data_shop' + shop_id).children("legend").after(etsy_html);
                $("#etsynewproduct" + shop_id).select2().on('change', function () {
                    var etsy_id = jQuery(this).val();
                    var name_el = jQuery(this).attr("name");
                    var shop_id = name_el.replace("etsynewproduct", "");
                    jQuery("#etsynewproduct" + shop_id).parent().find(".product_etsy_existe").remove();
                    if (etsy_id != 0) {
                        check_product_existe_etsy(etsy_id, shop_id, user_id);
                    }
                });
                vars['show_list_products_' + shop_id] = 1;
            }
        }

        function ajax_etsy_data(shop_id) {

            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "get_list_data_etsy_shop", shop_id: shop_id, user_id:<?php echo $currentuserid; ?>},
                cache: false,
                dataType: "json",
                async: false,
                success: function (data) {
                    list_sections = data.sections;
                    list_shipping = data.shippings_template;
                    etsy_shop = data.shop;
                }
            });
        }

        function get_etsy_data(shop_id) {
            var etsy_html = '';
            var selected_whomadeit = '';
            var selected_issupply = '';
            var selected_whenmade = '';
            var selected_cat = 0;
            var selected_sub1cat = 0;
            var selected_sub2cat = 0;
            var selected_sub3cat = 0;
            var selected_section = '';
            var selected_shipping = '';
            var selected_occasion = '';
            var style = '';
            var selected_recipient = '';
            var materials = '';

            if (is_error == 1) {
                var session_data = '<?php echo base64_encode(json_encode($_SESSION['data'], true)); ?>';
                selected_whomadeit = get_select_data_etsy(shop_id, 'whomadeit', 2, session_data);
                selected_issupply = get_select_data_etsy(shop_id, 'issupply', 2, session_data);
                selected_whenmade = get_select_data_etsy(shop_id, 'whenmade', 2, session_data);
                selected_cat = get_select_data_etsy(shop_id, 'cat', 2, session_data);
                selected_sub1cat = get_select_data_etsy(shop_id, 'sub1cat', 2, session_data);
                selected_sub2cat = get_select_data_etsy(shop_id, 'sub2cat', 2, session_data);
                selected_sub3cat = get_select_data_etsy(shop_id, 'sub3cat', 2, session_data);
                selected_section = get_select_data_etsy(shop_id, 'section', 2, session_data);
                selected_shipping = get_select_data_etsy(shop_id, 'shipping', 2, session_data);
                selected_occasion = get_select_data_etsy(shop_id, 'occasion', 2, session_data);
                style = get_select_data_etsy(shop_id, 'style', 2, session_data);
                selected_recipient = get_select_data_etsy(shop_id, 'recipient', 2, session_data);
                materials = get_select_data_etsy(shop_id, 'materials', 2, session_data);
            } else if (is_edit == 1) {
                etsy_id = get_select_data_etsy(shop_id, 'etsy_id', 1, <?php echo $productid; ?>);
                selected_whomadeit = get_select_data_etsy(shop_id, 'whomadeit', 1, <?php echo $productid; ?>);
                selected_issupply = get_select_data_etsy(shop_id, 'issupply', 1, <?php echo $productid; ?>);
                selected_whenmade = get_select_data_etsy(shop_id, 'whenmade', 1, <?php echo $productid; ?>);
                selected_cat = get_select_data_etsy(shop_id, 'cat', 1, <?php echo $productid; ?>);
                selected_sub1cat = get_select_data_etsy(shop_id, 'sub1cat', 1, <?php echo $productid; ?>);
                selected_sub2cat = get_select_data_etsy(shop_id, 'sub2cat', 1, <?php echo $productid; ?>);
                selected_sub3cat = get_select_data_etsy(shop_id, 'sub3cat', 1, <?php echo $productid; ?>);
                selected_section = get_select_data_etsy(shop_id, 'section', 1, <?php echo $productid; ?>);
                selected_shipping = get_select_data_etsy(shop_id, 'shipping', 1,<?php echo $productid; ?>);
                selected_occasion = get_select_data_etsy(shop_id, 'occasion', 1, <?php echo $productid; ?>);
                style = get_select_data_etsy(shop_id, 'style', 1, <?php echo $productid; ?>);
                selected_recipient = get_select_data_etsy(shop_id, 'recipient', 1, <?php echo $productid; ?>);
                materials = get_select_data_etsy(shop_id, 'materials', 1, <?php echo $productid; ?>);
            } else if (is_import == 1) {
                etsy_id = <?php echo (int)$_GET['shop_prd_id'];?>;
                selected_whomadeit = '<?php echo ($who_made && $who_made != "") ? $who_made : '';?>';
                selected_issupply = '<?php echo $is_supply;?>';
                selected_whenmade = '<?php echo ($when_made && $when_made != "") ? $when_made : '';?>';
                selected_cat = <?php echo ($etsycategory && $etsycategory != 0) ? $etsycategory : 0; ?>;
                selected_sub1cat = <?php echo ($etsysub1category && $etsysub1category != 0) ? $etsysub1category : 0; ?>;
                selected_sub2cat = <?php echo ($etsysub2category && $etsysub2category != 0) ? $etsysub2category : 0; ?>;
                selected_sub3cat = <?php echo ($etsysub3category && $etsysub3category != 0) ? $etsysub3category : 0; ?>;
                selected_section = <?php echo ($etsysection && $etsysection != 0) ? $etsysection : 0; ?>;
                selected_shipping = <?php echo ($etsyshippingid && $etsyshippingid != 0) ? $etsyshippingid : 0; ?>;
                selected_occasion = '<?php echo ($etsyoccasion && $etsyoccasion != '') ? $etsyoccasion : ''; ?>';
                style = '<?php echo ($etsystyle && $etsystyle != "") ? $etsystyle : ''; ?>';
                selected_recipient = '<?php echo ($etsyrecipient && $etsyrecipient != '') ? $etsyrecipient : ''; ?>';
                materials = '<?php echo ($etsymaterials && $etsymaterials != "") ? $etsymaterials : ''; ?>';
            }

            //list cats
            var firstime = 0;
            var cat_options = "<option value='0'>None</option>";

            var all_cats_json = vars['cats_etsy_' + shop_id];
            var json_cats = $.parseJSON(all_cats_json);
            jQuery.each(json_cats, function (i, val) {

                var selected = "";
                if (firstime == 0) {
                    if (selected_cat == val.id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                cat_options += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";

            });

            var firstime = 0;
            var sections_options = "";
            $.each(list_sections, function (id, title) {
                var selected = "";
                if (firstime == 0) {
                    if (selected_section == id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                sections_options += "<option value='" + id + "' " + selected + ">" + title + "</option>";
            });

            var firstime = 0;
            var shippings_options = "";
            $.each(list_shipping, function (id, title) {
                var selected = "";
                if (firstime == 0) {
                    if (selected_shipping == id) {
                        selected = "selected='selected'";
                        firstime = 1;
                    }
                }
                shippings_options += "<option value='" + id + "' " + selected + ">" + title + "</option>";
            });

            etsy_html += '<fieldset id="etsy_data_shop' + shop_id + '" class="multi_shop"><legend><b>Shop : </b><b style="color:green">' + etsy_shop + '</b></legend>';
            var data_html = '';
            if (is_edit == 1 || is_import == 1)
                data_html += '<input type="hidden" name="etsynewproduct' + shop_id + '" value="' + etsy_id + '">'
            if (is_import == 1) {
                data_html += '<input type="hidden" name="etsyactive" value="1">';
                data_html += '<input type="hidden" name="etsyshop[]" value="' + shop_id + '">';
            }
            // About product
            data_html += '<div style="width: 175px;float: left;line-height: 20px;">About this product</div><div class="about" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="whomadeit' + shop_id + '" class="whomadeit"><optgroup label="Select a maker">';
            var whomadeitselect = <?php echo json_encode($whomadeitselect) ?>;
            $.each(whomadeitselect, function (i, elem) {
                selected = (selected_whomadeit == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</optgroup></select></div>';
            data_html += '<div class="about" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="issupply' + shop_id + '" class="issupply"><optgroup label="Select a use">';
            var issupply_select = <?php echo json_encode($issupply_select) ?>;
            $.each(issupply_select, function (i, elem) {
                selected = (selected_issupply == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</optgroup></select></div>';
            data_html += '<div class="about" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="whenmade' + shop_id + '" class="whenmade"><optgroup label="Not yet made">';
            selected = (selected_whenmade == "made_to_order") ? "selected" : "";
            data_html += '<option value="made_to_order" ' + selected + '>Made To Order</option></optgroup><optgroup label="Recently">';
            var whenmade_recently = <?php echo json_encode($whenmade_recently) ?>;
            $.each(whenmade_recently, function (i, elem) {
                selected = (selected_whenmade == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</optgroup><optgroup label="Vintage">';
            var whenmade_vintage = <?php echo json_encode($whenmade_vintage) ?>;
            $.each(whenmade_vintage, function (i, elem) {
                selected = (selected_whenmade == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</optgroup></select></div>';
            data_html += '<br class="clear"><br class="clearfix">';
            // Cats
            data_html += '<div><div style="width: 175px;float: left;line-height: 20px;">Category</div><div class="category" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="etsycategory' + shop_id + '" id="etsycategory' + shop_id + '" class="etsycategory">' + cat_options + '</select></div>';

            data_html += '<div class="category" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="etsysub1category' + shop_id + '" id="etsysub1category' + shop_id + '" class="etsysub1category"></select></div>';

            data_html += '<div class="category" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="etsysub2category' + shop_id + '" id="etsysub2category' + shop_id + '" class="etsysub2category"></select></div>';

            data_html += '<div class="category" style="float:left;padding-right:5px;width:180px;display: inline-block;"><select name="etsysub3category' + shop_id + '" id="etsysub3category' + shop_id + '" class="etsysub3category"></select></div></div>';

            data_html += '<br class="clear"><br class="clearfix">';
            // Sections
            data_html += '<div style="width: 175px;float: left;">Shop Section</div><div style="float: left; "><select name="etsysection' + shop_id + '" class="etsysection" style="height: 25px;">' + sections_options + '</select></div><br class="clear"><br class="clearfix">';
            // Style
            data_html += '<div><div style="width: 175px;float: left;">Style</div><div style="float: left;"><select class="addstyle"><option value="select" >Add a style...</option>';
            var etsystyle = <?php echo json_encode($style) ?>;
            $.each(etsystyle, function (i, elem) {
                data_html += '<option>' + elem + '</option>';
            });
            data_html += '</select><input type="text" name="etsystyle' + shop_id + '" style="margin-bottom:5px;margin-top:10px;" class="etsystyle" value="' + style + '"/>(You can add up to 2 styles, you can add custom style Styles are separated by comma)</div></div><br class="clear"><br class="clearfix">';
            // Recipient
            data_html += '<div><div style="width: 175px;float: left;">Recipient</div><div style="float: left;"><select name="etsyrecipient' + shop_id + '" class="etsyrecipient"  ><option value="">Select a recipient...</option>';
            var recipients = <?php echo json_encode($recipientselect) ?>;

            $.each(recipients, function (i, elem) {
                selected = (selected_recipient == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</select></div></div><br class="clear"><br class="clearfix">';
            // Shippings
            data_html += '<div><div style="width: 175px;float: left;">Shipping Template</div><div style="float: left;"><select name="etsyshipping' + shop_id + '" class="etsyshipping" style="height: 25px;margin-bottom:5px">' + shippings_options + '</select><br>(You need a create a shipping profile from etsy admin, <a id="link_to_ship" href="#" target="_blank">click here </a> to create one )</div></div><br class="clear"><br class="clearfix">';
            // Occasion    =
            data_html += '<div><div style="width: 175px;float: left;">Occasion</div><div style="float: left;"><select name="etsyoccasion' + shop_id + '" class="etsyoccasion"><option value="">Select an occasion...</option>';
            var occasions = <?php echo json_encode($occasionselect) ?>;
            $.each(occasions, function (i, elem) {
                selected = (selected_occasion == elem) ? "selected" : "";
                data_html += '<option value="' + elem + '" ' + selected + '>' + i + '</option>';
            });
            data_html += '</select></div></div><br class="clear"><br class="clearfix">';
            // Materials
            data_html += '<div><div><div style="width: 175px;float: left;">Materials</div><div style="float: left; "><input type="text" name="etsymaterials' + shop_id + '" class="etsymaterials" value="' + materials + '">(Materials can only include spaces, letters, and numbers)</div></div></div><br class="clear"><br class="clearfix">';

            etsy_html += data_html;

            etsy_html += '</fieldset>';
            if ($("#etsy_data_shop" + shop_id).length > 0 && is_import == 0)
                $("#etsy_data_shop" + shop_id).slideDown("slow");
            else {
                $("#etsy_data_shops").append(etsy_html);
                update_sub_etsycategory(1, shop_id, selected_sub1cat);
                update_sub_etsycategory(2, shop_id, selected_sub2cat);
                update_sub_etsycategory(3, shop_id, selected_sub3cat);
                $(".whomadeit,.issupply,.whenmade,.etsycategory,.etsysub1category,.etsysub2category,.etsysub3category,.etsyrecipient,.etsyshipping,.addstyle,.etsysection,.etsyoccasion").select2();
            }
        }

        function get_select_data_etsy(shop_id, field, type, prd_data) {
            var selected_data = "";
            $.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {
                    action: "get_selected_data_etsy_shop",
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

        function check_product_existe_etsy(etsy_id, shop_id, user_id) {
            jQuery.ajax({
                type: "GET",
                url: "/ajax_shop_call.php?",
                data: {action: "check_product_existe_etsy", etsy_id: etsy_id, shop_id: shop_id, user_id: user_id},
                cache: true,
                dataType: "json",
                async: false,
                success: function (data) {
                    if ((data.status == 200) || (data.status == "200")) {
                        if ((data.data == true) || (data.data == "true")) {
                            jQuery("#etsynewproduct" + shop_id).parent().append('<span class="product_etsy_existe" style="color:red"><br>WARNING : this product is related to another product in print aura .</span> ');
                        } else {
                            jQuery("#etsynewproduct" + shop_id).parent().find(".product_etsy_existe").remove();
                        }
                    }
                }
            });
        }

        function update_sub_etsycategory(level, shop_id, etsy_sub_category_id) {
            var firstime = 0;
            var cat_id = 0;
            var selected = (etsy_sub_category_id == 0) ? "selected='selected'" : "";
            var selectoptions = "<option value='0' " + selected + ">None</option>";
            if (level == 1)
                var cat_id = jQuery("#etsycategory" + shop_id + " option:selected").val();
            if (level == 2)
                var cat_id = jQuery("#etsysub1category" + shop_id + " option:selected").val();
            if (level == 3)
                var cat_id = jQuery("#etsysub2category" + shop_id + " option:selected").val();

            if (cat_id != 0) {
                var subs = [];
                var all_cats_chils_json = vars['cats_childs_etsy_' + shop_id];
                var json_cats_childs = $.parseJSON(all_cats_chils_json);
                $.each(json_cats_childs, function (key, elems) {
                    if (level == key) {
                        var cats_sub = JSON.stringify(elems);
                        $.each($.parseJSON(cats_sub), function (key1, sub) {
                            if (cat_id == key1)
                                subs = JSON.stringify(sub);
                        });
                    }
                });

                if (subs.length > 0) {
                    jQuery.each($.parseJSON(subs), function (i, val) {
                        var selected = "";
                        if (firstime == 0) {
                            if (etsy_sub_category_id == val.id) {
                                selected = "selected='selected'";
                                firstime = 1;
                            }
                        }
                        selectoptions += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    jQuery("#etsysub" + level + "category" + shop_id).html(selectoptions);
                    jQuery("#etsysub" + level + "category" + shop_id).parent('div').show();
                    //if(etsy_sub_category_id == "0")
                    //$("#etsysub" + level + "category"+shop_id).select2('destroy').select2();
                    if (level == 1) {
                        jQuery("#etsysub2category" + shop_id).parent('div').hide();
                        jQuery("#etsysub2category" + shop_id).html("<option value='0'>None</option>");
                        jQuery("#etsysub3category" + shop_id).parent('div').hide();
                        jQuery("#etsysub3category" + shop_id).html("<option value='0'>None</option>");
                    }
                    if (level == 2) {
                        jQuery("#etsysub3category" + shop_id).parent('div').hide();
                        jQuery("#etsysub3category" + shop_id).html("<option value='0'>None</option>");
                    }
                } else {
                    jQuery("#etsysub" + level + "category" + shop_id).html(selectoptions);
                    jQuery("#etsysub" + level + "category" + shop_id).parent('div').hide();
                }
            } else {
                jQuery("#etsysub" + level + "category" + shop_id).html(selectoptions);
                jQuery("#etsysub" + level + "category" + shop_id).parent('div').hide();
                if (level == 1) {
                    jQuery("#etsysub2category" + shop_id).parent('div').hide();
                    jQuery("#etsysub2category" + shop_id).html("<option value='0'>None</option>");
                    jQuery("#etsysub3category" + shop_id).parent('div').hide();
                    jQuery("#etsysub3category" + shop_id).html("<option value='0'>None</option>");
                }
                if (level == 2) {
                    jQuery("#etsysub3category" + shop_id).parent('div').hide();
                    jQuery("#etsysub3category" + shop_id).html("<option value='0'>None</option>");
                }
            }
        }

        jQuery(document).on('change', '.etsycategory', function () {
            var shop_id = parseInt(jQuery(this).attr("id").replace("etsycategory", ""));
            update_sub_etsycategory(1, shop_id, 0);
        });

        jQuery(document).on('change', '.etsysub1category', function () {
            var shop_id = parseInt(jQuery(this).attr("id").replace("etsysub1category", ""));
            update_sub_etsycategory(2, shop_id, 0);
        });
        jQuery(document).on('change', '.etsysub2category', function () {
            var shop_id = parseInt(jQuery(this).attr("id").replace("etsysub2category", ""));
            update_sub_etsycategory(3, shop_id, 0);
        });
        var stylecount = 0;
        var delimiter = "";
        jQuery(document).on("change", ".addstyle", function () {
            var inp = jQuery(this).next().next(".etsystyle");
            var data = inp.val();
            if (data.length > 0)
                var styles = data.split(",");
            else
                styles = new Array()
            stylecount = styles.length;
            if (stylecount == 2) {
                alert("You can add up to 2 styles");
                return false;
            }
            if (stylecount == 1)
                delimiter = ",";
            else
                delimiter = "";
            var value = inp.val() + delimiter + jQuery(this).val();
            inp.val(value);

        });

        jQuery(document).on("blur", ".etsystyle", function () {
            var data = jQuery(this).val();
            if (data.length > 0)
                var styles = data.split(",");
            else
                styles = new Array()

            if (styles.length > 2) {
                alert("You can add up to 2 styles");
                var styleval = styles[0] + "," + styles[1];
                jQuery(this).val(styleval);
            }

        });

    });
</script>
