<?php

//require_once('product-functions');
global $wpdb;
$site_url = site_url();
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
$currentusername = $current_user->user_login;

// Only show if logged in
if ($currentuserid == 0) {
wp_redirect("/login");
exit();
}

get_header();
?>

<div class="container-fluid dashboard_content">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(-1025.422 -588.821)"><path style="fill:none;stroke:#361181;stroke-width:2.5;" d="M1099.1,671.6h-27.5c-0.4,0-0.7-0.3-0.7-0.7l0,0v-27.8l-4.2,4.7c-0.3,0.3-0.7,0.3-1,0.1l-7-5.1c-0.3-0.2-0.4-0.7-0.2-1l7.5-12c1.3-2.1,3.6-3.5,6-3.7c0,0,4.2-0.7,6.6-1.1c0.4-0.1,0.7,0.2,0.8,0.5c1,3.2,4.5,5.1,7.7,4c1.9-0.6,3.4-2.1,4-4c0.1-0.4,0.5-0.6,0.8-0.5c2.5,0.4,6.6,1.1,6.6,1.1c2.5,0.2,4.7,1.6,6,3.7l7.5,12c0.2,0.3,0.1,0.8-0.2,1l-7,5.1c-0.3,0.2-0.7,0.2-1-0.1l-4.2-4.7v27.8C1099.9,671.3,1099.5,671.6,1099.1,671.6C1099.1,671.6,1099.1,671.6,1099.1,671.6z"/></g></svg> Add Products</h1>

                    <h2 class="fs1">Share your dream</h2>
                    <h3 class="fs2">Customize your atmosphere with a galaxy of products</h3>
                    <p>This page is only for those who are using one of our apps such as Shopify, Storenvy, the API, or other integrations. All other manually placed orders do not use this system. To learn more about integrations, <a href="#">click here</a>.</p>
                </div>
                <div class="col-lg-9 col-xl-8 text-center my-40">
                    <h3>Add Products</h3>
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#361181;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
                </div>
                <div class="col-lg-9 col-xl-8 text-center">
                    <select class="mb-20" name="brand_id" id="brand">
                      <option name="brand" value="0">Choose a Brand</option>
                    </select> <br>
                    <select name="product_id" id="product">
                        <option value="0">Please Choose a Brand First</option>
                    </select>
                </div>
                </div>
            </div>
        </div>
    </div>


<div class="container-fluid" id="addProductsWizard" style="display:none;">
    <div class="row py-40">
        <div class="col-md-5 col-lg-6">
            <h2 class="fs1">Step 1: Select Print Images</h2>
            <p>First you need to upload/select files to be used by Ryan Kikta for printing on your products. You can also specify print location for the front and/or back. Make sure you have checked our print file requirements or download photoshop template file.</p>
        </div>
        <div class="col-md-7 col-lg-6">
            <div class="row">
                <div class="col-6">
                    <div class="add_products_preview_container">
                        <label>Front:</label>
                        <input type="hidden" id="dofront" name="dofront" value="0" checked='unset'>
                        <img class="add_products_preview_image" id="frontprintfileimage" src="/wp-content/themes/ryankikta/images/add_product_print.png">

                        <a href="#" class="btn-primary mb-20" data-toggle="modal" data-target="#images_modal" onclick="SetVar('frontprintfile');">Select file</a>

                        <div id="frontprintfileremove" style="display:none;">
                            <a class="button remove_button" data-file="frontprintfile" href="#">Remove</a>
                        </div>

                        <div id="frontprintfileinfo">
                            <span id="frontprintfiletext">No Image selected</span>
                          </div>
                        <input name="frontprintfile[]" id="frontprintfile" type="hidden" value="">
                    </div>
                </div>
                <div class="col-6">
                    <div class="add_products_preview_container">
                        <label>Back:</label>
                        <input name="backprintfile[]" id="backprintfile" type="hidden" value="">
                        <img class="add_products_preview_image" id="backprintfileimage" src="/wp-content/themes/ryankikta/images/add_product_print.png">

                        <a href="#" class="btn-primary mb-20" data-toggle="modal" data-target="#images_modal " onclick="SetVar('backprintfile');">Select file</a>

                        <div id="backprintfileinfo">
                            <span id="backprintfiletext">No Image Selected</span>
                        </div>

                        <div id="backprintfileremove" style="display:none;">
                            <a class="button remove_button" data-file="backprintfile" href="#">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row py-40">
        <div class="col-md-5 col-lg-6">
            <h2 class="fs1">Step 2: Select Mockup Images</h2>
            <p>Next you need to upload/select your files that we will use to determine placement of your provided print files. Mockup files are not mandatory but are recommended. You can find mockup files here. Please know that if we don't have the exact mockup file it's okay, you can use something similar, most shirts look similar.</p>
        </div>

        <div class="col-md-7 col-lg-6">
            <div class="row">
                <div class="col-6">
                    <div class="add_products_preview_container">
                        <label>Front:</label>
                        <input type="hidden" id="dofront" name="dofront" value="0" checked='unset'>
                        <img class="add_products_preview_image" id="mockupfrontimage" src="/wp-content/themes/ryankikta/images/add_product_mockup.png">

                        <a href="#" class="btn-primary mb-20" data-toggle="modal" data-target="#images_modal" onclick="SetVar('mockupfront');">Select file</a>

                        <div id="mockupfrontinfo">
                            <span id="mockupfronttext">No Image selected</span>
                          </div>
                        <input name="mockupfront[]" id="mockupfront" type="hidden" value="">

                        <div id="mockupfrontremove" style="display:none;">
                            <a class="button remove_button" data-file="mockupfront" href="#">Remove</a>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="add_products_preview_container">
                        <label>Back:</label>
                        <img class="add_products_preview_image" id="mockupbackimage" src="/wp-content/themes/ryankikta/images/add_product_mockup.png">

                        <a href="#" class="btn-primary mb-20" data-toggle="modal" data-target="#images_modal "onclick="SetVar('mockupback');">Select file</a>

                        <div id="mockupbackinfo">
                            <span id="mockupbacktext">No Image Selected</span>
                        </div>

                        <input name="mockupback[]" id="mockupback" type="hidden" value="">

                        <div id="mockupbackremove" style="display:none;">
                            <a class="button remove_button" data-file="mockupback" href="#">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row py-40 justify-content-between">
        <div class="col-12 pb-40">
            <h2 class="fs1">Step 3: Customize your product</h2>
        </div>

        <div class="col-lg-6 col-xl-5">
            <h2>Product Settings</h2>
            <div class="row input_outline input_flex">
                <div class="col-sm-6 col-lg-5">
                    <label for="title">Product title:</label>
                </div>
                <div class="col-sm-6 col-lg-7">
                    <input type="text" name="title" id="title" placeholder="TITLE" value="<?php if ($has_import == 1) { echo htmlentities(stripslashes($title)); } ?>">
                    <p class="small-caption">This is what your customers will see as the title. Include what the product is in the name, an example might be “Black Emblem T-Shirt”.</p>
                </div>
            </div>

            <div class="row input_outline input_flex">
                <div class="col-sm-6 col-lg-5">
                    <label for="sku">Sku:</label>
                </div>
                <div class="col-sm-6 col-lg-7">
                    <input type="text" name="sku" id="sku" placeholder="SKU" value="<?php if ($has_import == 1) { echo htmlentities(stripslashes($sku)); } ?>">
                    <p class="small-caption">Stock Keeping Unit. You can enter whatever for this. Other color/size info will automatically be attached to whatever you enter.</p>
                </div>
            </div>

            <div class="row input_outline input_flex">
                <div class="col-sm-6 col-lg-5">
                    <label for="tags">Product tags:</label>
                </div>
                <div class="col-sm-6 col-lg-7">
                    <input type="text" name="tags" id="tags" placeholder="PRODUCT TAGS" value="<?php if ($has_import == 1) { echo htmlentities(stripslashes($tags)); } ?>">
                </div>
            </div>

            <div class="row input_outline input_flex">
                <div class="col-sm-6 col-lg-5">
                    <label for="weight">Weight (lbs):</label>
                </div>
                <div class="col-sm-6 col-lg-7">
                    <input type="text" name="weight" id="weight" placeholder="WEIGHT" value="<?php if ($has_import == 1) echo htmlentities(stripslashes($weight)); ?>">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div id="bran-config">
              <?php get_template_part('product_settings/product', 'config'); ?>
            </div>
        </div>

        <div class="col-12 mt-4">
            <label class="fs3">Description:</label>
            <p class="small-caption">Use this field to provide additional information about your product. You can also include information on the quality, material, and design description etc.</p>

            <?php if (!empty($etsy_auth) && $has_import != 1) { ?>
                <div class="message text-center my-20">
                    <p class="fs3 mb-0">Etsy Users: Please note that Etsy does not support links, external images and some other html in their product descriptions so not all formatting available below may display in your Etsy listing.</p>
                </div>
              <?php } ?>
            <textarea name="description" id="description"><?php if ($has_import == 1) { echo $description; } ?></textarea>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/ui/trumbowyg.min.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/trumbowyg.min.js"></script>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-40">
            <h3>Select Product Colors</h3>
            <div id="rmpmmg_colors_selector"></div>
        </div>

        <div class="col-12 mb-80">
            <h3 class="mb-30">Customize Products</h3>
            <p class="add_products_message">Select a color to customize your product.</p>
            <!-- Messages for customizing products -->
            <?php if (!empty($etsy_auth)) { ?>
                <div class="message message_saved text-center my-20">
                    <p class="fs3 mb-0">Etsy Users: Please note that Etsy's API does not allow variable prices on both color AND size so we can't have a price for normal prices and another for plus sized items. As a result the form below currently just uses the "Plus Sizes Sale Price" as the default price that displays on Etsy.</p>
                </div>
            <?php } ?>

            <?php if ($plus_size_disabled == 1) { 
                // Show message if plus sizes disabled ?>
                <div class="message message_error text-center my-20">
                    <p class="fs3 mb-0">You currently have plus sizes disabled, they will not be displayed through our apps.</p>
                </div>
            <?php } ?> 

            <div class="product_options_table" id="add_products_tables">
                <!-- <table>
                    <thead>
                        <tr>
                            <th>Color</th>
                            <th>Default?</th>
                            <th>RyanKikta Price</th>
                            <th>Retail Price</th>
                            <th>Profit</th>
                            <th>Plus Sizes Cost</th>
                            <th>Plus Sizes Retail Price</th>
                            <th>Images</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>
                </table> -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="fs1">Step 4: Add to shop</h2>

            <?php 
            // if (!isset($_GET['app']) || $shoptype != "ecwid") {
            //     if ($shopify_check) {
            //         if (($has_import == 1 && $_GET['shop'] == "shopify") || !isset($_GET['action'])) {
            //             get_template_part('html_shop_multi/shopify', 'part');
            //         }
            //     }
            //     if ($storenvy_check) {
            //         if (($has_import == 1 && $_GET['shop'] == "storenvy") || !isset($_GET['action']))
            //             get_template_part('html_shop_multi/storenvy_multi', 'part');
            //         }
            //     }
            //     if ($etsy_check) {
            //         if (($has_import == 1 && strtolower($_GET['shop']) == "etsy") || !isset($_GET['action']))
            //             get_template_part('html_shop_multi/etsy_multi', 'part');
            //         }
            //     }
            //     if ($woocommerce_check) {
            //         if (($has_import == 1 && $_GET['shop'] == "woocommerce") || !isset($_GET['action']))
            //             get_template_part('html_shop_multi/woocommerce_multi', 'part');
            //         }
            //     }
            //     if ($bigcommerce_check) {
            //         if (($has_import == 1 && $_GET['shop'] == "bigcommerce") || !isset($_GET['action']))
            //             get_template_part('html_shop_multi/bigcommerce_multi', 'part');
            //         }
            //     }
            //     if ($opencart_check) {
            //         if (($has_import == 1 && $_GET['shop'] == "opencart") || !isset($_GET['action']))
            //             get_template_part('html_shop_multi/opencart_multi', 'part');
            //         }
            //     }
            // }

            //get_template_part('html_shop_multi/shopify', 'part'); ?>

            <?php //get_template_part('html_shop_multi/etsy_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/bigcommerce_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/woocommerce_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/storenvy_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/etsy_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/opencart_multi', 'part'); ?>
            <?php //get_template_part('html_shop_multi/gumgroad_multi', 'part'); ?>

        </div>
    </div>

</div>

<?php include('includes/graphic_design.php'); ?>

<div class="modal fade" id="images_modal" tabindex="-1" aria-labelledby="images_modal_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content py-40">
            <div class="modal-body">
                <iframe src="" id="iframe1" marginheight="0" frameborder="0" width="100%" height="100%"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="color_images_modal" tabindex="-1" aria-labelledby="color_images_modal_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content py-40">
            <div class="modal-body">
                <iframe src='' id="iframe_colors" marginheight="0" frameborder="0" width="100%" height="100%"></iframe>
            </div>
        </div>
    </div>
</div>


<script>
function abc(imageid, imageurl, imagename, extension) {
    $("#" + imageselection + "text").html('Selected Image: ' + imagename + '.' + extension);
    $("#" + imageselection + "text2").html('Selected Image: ' + imagename + '.' + extension);

    $('#' + imageselection + 'image').attr('src', imageurl);

    $('#images_modal').modal('hide');

    $("#" + imageselection).val(imageid).trigger('change');
    $("#" + imageselection + "remove").show();
}

function SetVar(variable) {
    if (variable != "") {
        if (variable.indexOf("backprintfile") > -1) {
            if (!$("#doback").is(':checked')) {
                $("#doback").prop('checked', true);
                $("#doback").val('1');
            }
        } else if (variable.indexOf("frontprintfile") > -1) {
            if (!$("#dofront").is(':checked')) {
                $("#dofront").prop('checked', true);
                $("#dofront").val('1');
            }
        }
    }

    if ($("#iframe1").attr('src') == "") {
        $("#iframe1").attr('src', "/user-images/");
    }

    imageselection = variable;
}

function upload_image_mockup(color_id, imageid, img_url) {
    var inputfield = $('#image_id_' + color_id);
    var imagefield = $('#image_src_' + color_id);
    var imageurlfield = $('#image_url_' + color_id);
    var tmpImg = new Image();
    tmpImg.src = img_url;

    tmpImg.onload = function () {
      var img = '<img id="image_src_' + color_id + '" src="' + img_url + '">';
      $(".image_color" + color_id).html(img);
      inputfield.val(imageid);
      imageurlfield.val(img_url);


      $("#image_delete_" + color_id).show();
      $("#image_upload_" + color_id).hide();

      $('#color_images_modal').modal('hide');
    };
}

//Populate the brand list with all of the brands avaiable to the user
$(document).ready(function(){
  $.ajax({
    url: '/wp-content/themes/ryankikta/ajax/get_products.php',
    type: 'get',
    dataType: 'json',
    data: {'method': 'listbrands'},
    success: function(resp){
	  $.each(resp.results, function(key, value){
	  $('#brand').append('<option value=' + value.brand_id + '>' + value.brand_name + '</option>');
	});
    }
  });
});

</script>

<?php get_footer(); ?>
