;( function () {
    
    var pageSettings = {
        var : '',
        productInfo     : {
            'brand_id' : '',
            'product_id' : '',
        }
    }
    
    var addProduct = {
        
        settings : pageSettings,

        initWysiwyg:function(){
            $('#description').trumbowyg({
                btns: [
                    ['strong', 'em'],
                    ['justifyLeft', 'justifyCenter'],
                    ['link'],
                    ['unorderedList', 'orderedList'],
                    ['removeformat']
                ]
            });
        },
        brandSelect: function(){
            $('#brand').on('change', function(){
                if ($(this).find("option:selected").val() != 0) {
                    // if a brand is selected
                    // set var for brand id
                    addProduct.settings.productInfo.brand_id = $(this).find("option:selected").val();

                    // get products for selected brand
                    // returns [{"product_id":"45","product_name":"Fine Jersey Long Sleeve T-Shirt","brand_id":"2","shipping_id":"1","price":"8.00","color_price":"8.75","sku":"2007"},{"product_id":"28","product_name":"Fine Jersey Short Sleeve T-Shirt","brand_id":"2","shipping_id":"1","price":"5.50","color_price":"6.00","sku":"2001"},{"product_id":"29","product_name":"Fine Jersey Short Sleeve Womens T","brand_id":"2","shipping_id":"1","price":"5.50","color_price":"6.00","sku":"2102"},{"product_id":"50","product_name":"Fine Jersey Tank","brand_id":"2","shipping_id":"1","price":"7.00","color_price":"7.75","sku":"2408"},{"product_id":"207","product_name":"Tri-blend T-Shirt","brand_id":"2","shipping_id":"1","price":"14.00","color_price":"14.00","sku":"TR401"},{"product_id":"56","product_name":"Unisex fine jersey short sleeve v-neck","brand_id":"2","shipping_id":"1","price":"7.25","color_price":"7.75","sku":"2456"}]
                    
                    //var productList = [{"product_id":"45","product_name":"Fine Jersey Long Sleeve T-Shirt","brand_id":"2","shipping_id":"1","price":"8.00","color_price":"8.75","sku":"2007"},{"product_id":"28","product_name":"Fine Jersey Short Sleeve T-Shirt","brand_id":"2","shipping_id":"1","price":"5.50","color_price":"6.00","sku":"2001"},{"product_id":"29","product_name":"Fine Jersey Short Sleeve Womens T","brand_id":"2","shipping_id":"1","price":"5.50","color_price":"6.00","sku":"2102"},{"product_id":"50","product_name":"Fine Jersey Tank","brand_id":"2","shipping_id":"1","price":"7.00","color_price":"7.75","sku":"2408"},{"product_id":"207","product_name":"Tri-blend T-Shirt","brand_id":"2","shipping_id":"1","price":"14.00","color_price":"14.00","sku":"TR401"},{"product_id":"56","product_name":"Unisex fine jersey short sleeve v-neck","brand_id":"2","shipping_id":"1","price":"7.25","color_price":"7.75","sku":"2456"}]
                    
                    // disable ajax for dev
                     $.ajax({
                         type: "GET",
                         url: "/wp-content/themes/ryankikta/ajax/get_products.php",
                         contentType: "application/json; charset=utf-8",
			 data: {'method': 'listproducts', 'brand_id':  addProduct.settings.productInfo.brand_id},
                         dataType: "json",
                         success: function (productList) {
                            $("#product").html("");
                            
                            var selectoptions = '<option value="0">Choose a Product</option>';
                            $.each(productList.results, function (index, item) {
                                selectoptions += "<option value='" + item.product_id + "'>" + item.product_name + " (" + item.sku + ")</option>";
                            });
                            $("#product").html(selectoptions);
                         },
                         error: function (XMLHttpRequest, textStatus) {
                           alert("Sorry, Could not complete the request: " + textStatus);
                         }
                     })
                } else {
                    // reset page
                    $("#product").html('<option value="0">Please Choose a Brand First</option>');
                }

            })
        },
        productSelect: function () {
            $('#product').on("change",function () {
                // need to fetch product data
                // need to reset all fields
                // need to show next section if hidden

                if ($(this).find("option:selected").val() != 0) {
                    // if a product is selected

                    // set var for product id
                    addProduct.settings.productInfo.product_id = $(this).find("option:selected").val();
                    // populate colors
                    addProduct.getProductColors();

                    // show next section
                    $('#addProductsWizard').show();
                }
            });
        },
        getProductColors: function(){

            // https://ryankikta.com/ajaxcolors.php?actiontype=list_colors&product_id=45
            // returns  {"htm":" <span id=\ "rmpmmg_color_4\" class=\ "rmpmmg_color_selector \" style=\ "background-color: #424C57;\" data-color_id=\ "4\" title=\ "Asphalt\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #424C57;\"><\/span><\/span><span id=\"rmpmmg_color_5\" class=\"rmpmmg_color_selector \" style=\"background-color: #B6C8E0;\" data-color_id=\"5\" title=\"Baby Blue\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #B6C8E0;\"><\/span><\/span><span id=\"rmpmmg_color_7\" class=\"rmpmmg_color_selector \" style=\"background-color: #000000;\" data-color_id=\"7\" title=\"Black\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #000000;\"><\/span><\/span><span id=\"rmpmmg_color_138\" class=\"rmpmmg_color_selector \" style=\"background-color: #D4D4D4;\" data-color_id=\"138\" title=\"Heather Grey\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #D4D4D4;\"><\/span><\/span><span id=\"rmpmmg_color_41\" class=\"rmpmmg_color_selector \" style=\"background-color: #1f225c;\" data-color_id=\"41\" title=\"Navy Blue\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #1f225c;\"><\/span><\/span><span id=\"rmpmmg_color_1\" class=\"rmpmmg_color_selector \" style=\"background-color: #FFFFFF;\" data-color_id=\"1\" title=\"White\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #FFFFFF;\"><\/span><\/span>"}
            
            var colorslist2 = {'htm':' <span id=\ "rmpmmg_color_4\" class=\ "rmpmmg_color_selector \" style=\ "background-color: #424C57;\" data-color_id=\ "4\" title=\ "Asphalt\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #424C57;\"><\/span><\/span><span id=\"rmpmmg_color_5\" class=\"rmpmmg_color_selector \" style=\"background-color: #B6C8E0;\" data-color_id=\"5\" title=\"Baby Blue\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #B6C8E0;\"><\/span><\/span><span id=\"rmpmmg_color_7\" class=\"rmpmmg_color_selector \" style=\"background-color: #000000;\" data-color_id=\"7\" title=\"Black\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #000000;\"><\/span><\/span><span id=\"rmpmmg_color_138\" class=\"rmpmmg_color_selector \" style=\"background-color: #D4D4D4;\" data-color_id=\"138\" title=\"Heather Grey\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #D4D4D4;\"><\/span><\/span><span id=\"rmpmmg_color_41\" class=\"rmpmmg_color_selector \" style=\"background-color: #1f225c;\" data-color_id=\"41\" title=\"Navy Blue\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #1f225c;\"><\/span><\/span><span id=\"rmpmmg_color_1\" class=\"rmpmmg_color_selector \" style=\"background-color: #FFFFFF;\" data-color_id=\"1\" title=\"White\"><span style=\"height: 100%; position: absolute; right: 0px; top: 0px; width: 70%; display: block; background-color: #FFFFFF;\"><\/span><\/span>'};

            // no ajax for dev
             $.ajax({
                 type: "GET",
                 url: "/wp-content/themes/ryankikta/ajax/get_products.php",
                 data: {
		     method: 'listcolors',
                     product_id: addProduct.settings.productInfo.product_id, 
                 },
                 contentType: "application/json; charset=utf-8",
                 dataType: "json",
                 success: function (colorslist) {
		    console.log(colorslist);
			$.each(colorslist.results, function(key, value){
			  $('#rmpmmg_colors_selector').append('<span id="rmpmmg_color_' + value.color_id +  '" class="rmpmmg_color_selector" style="background-color: #' + value.color_code + ';" data-color_id="' + value.color_id + '" title="' + value.color_name + '"><span style="height:100%; position: absoulute; right: 0px; top: 0px; width: 70%; display: block; backgorund-color: #' + value.color_code + ';"></span></span>');
			});
                 }
             });
        },
        updateColors: function(){
            $(document).on('click','.rmpmmg_color_selector',function(){
                let $this = $(this)
                if (!$(this).hasClass('active')) {
                    // add new color to table
                    $this.addClass('active')
                    addProduct.addColorToTable($this);
                    var product_tables = $('#add_products_tables table').length;
                    if (product_tables > 0) {
                        $('.add_products_message').hide()
                    }
                } else {
                    // remove color from table
                    $this.removeClass('active')
                    addProduct.removeColorFromTable($this);
                    var product_tables = $('#add_products_tables table').length;
                    if (product_tables == 0) {
                        $('.add_products_message').show()
                    }
                }
            });
        },
        removeColorFromTable: function($color){
	    console.log($color);
            var color_id = $color.data('color_id');
            var color_table = document.getElementById('color_info_table_' + color_id);
            color_table.parentNode.removeChild(color_table);
        },
        addColorToTable: function($color){
            var color_id = $color.data('color_id');
            // we are only calling this function when you click the color button

            // https://ryankikta.com/ajaxcolors.php?&product_id=28&colors_ids=195&hasfront=1&hasback=0&front_id=&back_id=
            // Returns this
            // [{"color_id":"195","color_name":"Teal","html":"5ABAB0","color_swatch":"","group":"Color","print_price":9,"sizes1":[{"size_id":"1","size_name":"Small","size_plus_charge":"0.00","cost_price":15},{"size_id":"3","size_name":"Medium","size_plus_charge":"0.00","cost_price":15},{"size_id":"4","size_name":"Large","size_plus_charge":"0.00","cost_price":15},{"size_id":"5","size_name":"X-Large","size_plus_charge":"0.00","cost_price":15}],"sizes2":{"4":{"size_id":"6","size_name":"2X-Large","size_plus_charge":"1.50","cost_price":16.5}}}]

            /*var colorInfo = [{"color_id":"195", "color_name":"Teal", "html":"5ABAB0", "color_swatch":"", "group":"Color", "print_price":9, "sizes1":[{"size_id":"1", "size_name":"Small", "size_plus_charge":"0.00", "cost_price":15}, {"size_id":"3", "size_name":"Medium", "size_plus_charge":"0.00", "cost_price":15}, {"size_id":"4", "size_name":"Large", "size_plus_charge":"0.00", "cost_price":15}, {"size_id":"5", "size_name":"X-Large", "size_plus_charge":"0.00", "cost_price":15}], "sizes2": {"4": {"size_id":"6", "size_name":"2X-Large", "size_plus_charge":"1.50", "cost_price":16.5} } }];*/

             //disable for dev, disable ajax -- don't have permissions
             $.ajax({
                 type: "GET",
                 url: "/wp-content/themes/ryankikta/ajax/get_products.php",
                 data: {
		     'method': 'colorpricing',
                     'product_id': addProduct.settings.productInfo.product_id,
                     'color_id': color_id,
                     has_front: 1,
                     has_back: 0,
                     front_id: '',
                     back_id: ''
                 },
                 contentType: "application/json; charset=utf-8",
                 dataType: "json",
                 success: function (colorInfo) {
		console.log(colorInfo);
                $.each(colorInfo.results, function (index, item) {

                    var addColorHTML = "<table class='color_info_table' id='color_info_table_" + color_id + "'>" +
                        "<thead>" +
                            "<tr>" +
                                "<th>Color</th>" +
                                "<th>Default?</th>" +
                                "<th>RyanKikta Price</th>" +
                                "<th>Retail Price</th>" +
                                "<th>Profit</th>" +
                                "<th>Plus Sizes Cost</th>" +
                                "<th>Plus Sizes Retail Price</th>" +
                                "<th class='color_info_table_image'>Images</th>" +
                           "</tr>" +
                        "</thead>" +
                        
                        "<tbody>" +
                            "<tr>" +
                                "<td><div class='color_info_swatch' style='background-color:#" + item.color_code + "'></div>" + item.color_name + "</td>" +
                                "<td><input type='checkbox'  name='defaults[]' value=" + item.color_id + " class='defaultcolor defaultcolor' /></td>" +
                                "<td>$" + (item.print_price).toFixed(2) + "</td>" +
                                "<td><input class='normalsaleprice' type='number' value='" + (parseInt(item.print_price) + 4) + "'></td>" +
                                "<td>" + 4 + "</td>" +
                                "<td>$" + (item.print_price).toFixed(2) + "</td>" +
                                "<td><input type='number' value='" + (parseInt(item.print_price) + 4) + "'></td>" +
                                "<td class='color_info_table_image'><input name='image_" + item.color_id + "' id='image_id_" + item.color_id + "' value='0' type='hidden'>" +
                                    "<input name='image_url_" + item.color_id + "' id='image_url_'" + item.color_id + "' value='' type='hidden'>" +
                                    "<div class='image_color image_color" + item.color_id + "'></div>" +
                                    "<button id='image_upload_" + item.color_id + "' data-toggle='modal' data-target='#color_images_modal'  data-color_id='" + item.color_id + "' class='color_upload_image btn-primary mb-20'>Upload</button>" +
                                    "<div class='btn-secondary delete_img' style='display:none;' id='image_delete_" + item.color_id + "'>Delete</div>" +
                                    "</div>" +
                                "</td>" +
                                // "<td>" +  + "</td>" +
                           "</tr>" +
                        "</tbody>" +
                    "</table>";

                    $(addColorHTML).appendTo('#add_products_tables')
                });

         // no ajax for testing 
                 },
                 error: function (XMLHttpRequest, textStatus) {
                   alert("Sorry, Could not complete the request: " + textStatus);
                 }
             });

        },
        updatePrices: function(){
            $(document).on('keyup', '.normalsaleprice', function () {
                var $this = $(this);

                var this_sale_price = $this.val();
                var this_price = parseFloat($this.closest('td').prev('td').text().replace(/[^\d\.]/g, ''));
                var this_profit = this_sale_price - this_price;
                this_profit = Math.round(this_profit * 100) / 100;
                this_profit = this_profit.toFixed(2);
                $this.closest('td').next('td').html(this_profit);

            })
        },
        uploadColorImages: function(){
            $(document).on('click', '.color_upload_image', function () {
                var color_id = $(this).data("color_id");
                var src = "/image_colors.php?color_id=" + color_id;
                $("#iframe_colors").attr('src', src);
            });
        },
        deleteColorImages: function(){
            $(document).on('click', '.delete_img', function () {
                var elem_id = jQuery(this).attr('id');
                var color_id = elem_id.replace("image_delete_", "");

                $('#image_id_' + color_id).val("");
                $('#image_url_' + color_id).val("");
                $('#image_src_' + color_id).remove();
                $(this).hide();
                $(this).prev().show();
            });
        },
        removeImages: function(){
            $('.remove_button').on('click', function(){

                var imageLocation = $(this).data('file');

                $("#" + imageLocation + "text").html("Not Selected");
                $("#" + imageLocation + "remove").hide();
                $("#" + imageLocation).val("").trigger('change');

                if (imageLocation.indexOf("print") > -1) {
                    $('#' + imageLocation + 'image').attr('src', 'images/add_product_print.png');
                } else {
                    $('#' + imageLocation + 'image').attr('src', 'images/add_product_mockup.png');
                }

                $("#front_print_limit_error").remove();
                return false;
            })
        },
        init: function () {
            //ON LOAD
            this.brandSelect();
            this.productSelect();
            this.initWysiwyg();
            this.updateColors();
            this.removeImages();
            this.uploadColorImages();
            this.deleteColorImages();
            this.updatePrices();

            return this;
        }
    }
    window.addProduct = addProduct
})();




$(window).on('load', function(){        
    
    addProduct.init();

}) /* window load */  








