<?php
/*
 *  Template Name: RyanKikta Products
 *
 */

    global $wpdb;
    $site_url = site_url();
    get_header();

    $default_int = array('options' => array('default' => 0));

    console_log ("my error", $category_id);
    // CAVEAT: the main variables are set from shortcodes that emit from the below block!
    // Return the page data but don't display it
    ?>	
        <div style="display:none;">	
        <?php if (have_posts()) {	
            while (have_posts()):	
            the_post();	
            the_content();	
        endwhile;	
            } else {	
        echo 'Sorry, no posts matched your criteria.';	
            } ?>	
        </div>	
    <?php
    //inhale defaults set from shortcode as if these are nonempty then something else set them.
    $defaults = [];

    $default['country_id'] = !empty($brand_id) ? process_default($country_id) : 0;
    if (!empty($country_id)) {
        console_log(json_encode($country_id),"country_id defaults:");
    }
    $default['brand_id'] = !empty($brand_id) ? process_default($brand_id) : 0;
    if (!empty($brand_id)) {
        console_log(json_encode($brand_id),"brand_id defaults:");
    }
    $default['category_id'] = !empty($category_id) ? process_default($category_id) : 0;
    if (!empty($category_id)) {
        console_log(json_encode($category_id),"category_id defaults:");
    }
    $default['product_subcategory_id'] = !empty($product_subcategory_id) ? process_default($product_subcategory_id) : 0;
    if (!empty($product_subcategory_id)) {
        console_log(json_encode($product_subcategory_id),"product_subcategory_id defaults: ");
    }
    $default['product_style_type_id'] = !empty($product_style_type_id) ? process_default($product_style_type_id) : 0;
    if (!empty($product_style_type_id)) {
        console_log(json_encode($product_style_type_id), "product_style_type_id defaults: ");
    }

    // tolerate various theoretical inputs and convert to array
    function process_default($value, $recurse = true) {
        if (empty($value)) {
            return [];
        } elseif (is_array($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            $has_comma = (strpos(strval($value),',') !== false);
            $is_json = (strpos(strval($value),'{') !== false);
            if (!$has_comma && !$is_json) {
                if ($recurse && mb_strlen($value) >= 4 && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', strval($value))) {
                    return process_default(base64_decode(strval($value)), false);
                } else {
                    return $value;
                }
            } elseif ($has_comma && !$is_json){
                return explode(',',strval($value));
            } elseif ($is_json) {
                return json_decode(strval($value));
            }
        }

        return [];
    }

    //properly sanitize incoming user data
    $country_id = filter_var($_POST['country_id'], FILTER_VALIDATE_INT, $default_int);
    $p_country_id = filter_var($_POST['p_country_id'], FILTER_VALIDATE_INT, $default_int);
    console_log("country: {$country_id}/{$p_country_id}");
    $brand_id = filter_var($_POST['brand_id'], FILTER_VALIDATE_INT, $default_int);
    $p_brand_id = filter_var($_POST['p_brand_id'], FILTER_VALIDATE_INT, $default_int);
    console_log("brand: {$brand_id}/{$p_brand_id}");
    $category_id = filter_var($_REQUEST['category_id'], FILTER_VALIDATE_INT, $default_int);
    $p_category_id = filter_var($_REQUEST['p_category_id'], FILTER_VALIDATE_INT, $default_int);
    console_log("category: {$category_id}/{$p_category_id}");
    $product_subcategory_id = filter_var($_REQUEST['product_subcategory_id'], FILTER_VALIDATE_INT, $default_int);
    $p_product_subcategory_id = filter_var($_REQUEST['p_product_subcategory_id'], FILTER_VALIDATE_INT, $default_int);
    console_log("product_subcategory: {$product_subcategory_id}/{$p_product_subcategory_id}");
    $product_style_type_id = filter_var($_POST['product_style_type_id'], FILTER_VALIDATE_INT, $default_int);
    $p_product_style_type_id = filter_var($_POST['p_product_style_type_id'], FILTER_VALIDATE_INT, $default_int);
    console_log("product_style_type: {$product_style_type_id}/{$p_product_style_type_id}");

    //override empties with defaults should the post data be otherwise invalid
    $country_id = !isset($_POST['country_id']) ? $default['country_id'] : $country_id;
    $brand_id = !isset($_POST['brand_id']) ? $brand_id : $default['brand_id'];
    $category_id = !isset($_POST['category_id']) ? $default['category_id'] : $category_id;
    $product_subcategory_id = !isset($_POST['product_subcategory_id']) ? $default['product_subcategory_id'] : $product_subcategory_id;
    $product_style_type_id = !isset($_POST['product_style_type_id']) ? $default['product_style_type_id'] : $product_style_type_id;

    // p_ should contain whatever the previous state was to the page load

    $top_change = [];
    //note: mind your variable order. these are appended at the end of the main where conditions
    if (!empty($country_id) ) {
        $in_grouping = '(' . implode(',',array_fill(0,count($country_id),'%d')) . ')';
        $top_change[] = 'p.country_id IN ' . $in_grouping;
        $top_change_vars = array_merge((array) $top_change_vars, (array) $country_id);
    }

    if (!empty($brand_id)) {
        $in_grouping = '(' . implode(',',array_fill(0,count($brand_id),'%d')) . ')';
        $top_change[] = 'p.brand_id IN ' . $in_grouping;
        $top_change_vars = array_merge((array) $top_change_vars, (array) $brand_id);
    }

    if (!empty($category_id)) {
        $in_grouping = '(' . implode(',',array_fill(0,count($category_id),'%d')) . ')';
        $top_change[] = 'p.category_id IN ' . $in_grouping;
        $top_change_vars = array_merge((array) $top_change_vars, (array) $category_id);
    }

    if (!empty($product_subcategory_id)) {
        $in_grouping = '(' . implode(',',array_fill(0,count($product_subcategory_id),'%d')) . ')';
        $top_change[] = 'p.product_subcategory_id IN ' . $in_grouping;
        $top_change_vars = array_merge((array) $top_change_vars, (array) $product_subcategory_id);
    }

    if (!empty($product_style_type_id)) {
        $in_grouping = '(' . implode(',',array_fill(0,count($product_style_type_id),'%d')) . ')';
        $top_change[] = 'p.product_style_type_id IN ' . $in_grouping;
        $top_change_vars = array_merge((array) $top_change_vars, (array) $product_style_type_id);
    }

    //generate selector data
    //excluders
    $exclusion_where = [];
    $exclusion_where[] = 'p.`brand_id` NOT IN (34, 40, 41, 42, 43, 44, 45)'; //excluders from selector loop
    $exclusion_where[] = 'p.`brand_id` NOT IN (26,37)'; //additional brands banned in display loop
    $exclusion_where[] = 'p.`category_id` NOT IN (16,17)'; //banned in display loop
    $exclusion_where[] = 'p.`inventory_id` NOT IN (516,517,518)'; //also banned in display loop

    //exists test from category costs 325x less even after adding some indexes
    $exists_where = [];
    $exists_vars = [];

    $exists_where[] = 'p.`category_id` = c.`id`';
    $exists_where[] = 'p.`active` = 1';
    $exists_where[] = 'p.`category_id` NOT IN (17,0)';
    $exists_where = array_merge($exists_where, (array) $exclusion_where);
    if (!empty($top_change)) {
        $exists_where = array_merge($exists_where, (array) $top_change);
        $exists_vars = array_merge($exists_vars, (array) $top_change_vars);
    }
    $exists_where_text = implode(' AND ', $exists_where);

    $valid_categories = "
    	select c.`id` AS `category_id`, c.product_category_name 
    	from wp_product_category c
    	where EXISTS(
    	    SELECT 1
    	    FROM wp_rmproductmanagement p
    	    WHERE {$exists_where_text}
    	)";

    if (!empty($exists_vars)) {
        $valid_categories = $wpdb->prepare($valid_categories, $exists_vars);
    }
    $category_data = $wpdb->get_results($valid_categories, ARRAY_A);

    $exists_where = [];
    $exists_vars = [];

    $exists_where[] = 'p.product_subcategory_id = ps.id';
    $exists_where[] = 'p.active = 1';
    $exists_where = array_merge($exists_where, (array) $exclusion_where);
    if (!empty($top_change)) {
        $exists_where = array_merge($exists_where, (array) $top_change);
        $exists_vars = array_merge($exists_vars, (array) $top_change_vars);
    }
    $exists_where_text = implode(' AND ', $exists_where);

    $valid_subcategories = "
    	SELECT ps.`id` as `product_subcategory_id`, ps.product_subcategory_name
    	FROM wp_product_subcategory ps
    	WHERE EXISTS(
    	    SELECT 1
    	    FROM wp_rmproductmanagement p
    	    WHERE {$exists_where_text}
    	)";

    if (!empty($exists_vars)) {
        $valid_subcategories = $wpdb->prepare($valid_subcategories, $exists_vars);
    }
    $subcategory_data = $wpdb->get_results($valid_subcategories, ARRAY_A);

    $exists_where = [];
    $exists_vars = [];

    $exists_where[] = 'p.`product_style_type_id` = s.`id`';
    $exists_where[] = 'p.`active` = 1';
    $exists_where = array_merge($exists_where, (array) $exclusion_where);
    if (!empty($top_change)) {
        $exists_where = array_merge($exists_where, (array) $top_change);
        $exists_vars = array_merge($exists_vars, (array) $top_change_vars);
    }
    $exists_where_text = implode(' AND ', $exists_where);

    $valid_product_style_type = "
    	SELECT s.id AS `product_style_type_id`, s.`product_style_type_name`
    	FROM `wp_product_style_type` s
    	WHERE EXISTS(
    		SELECT 1
    		FROM `wp_rmproductmanagement` p
    		WHERE {$exists_where_text}
    	)";
    if (!empty($exists_vars)) {
        $valid_product_style_type = $wpdb->prepare($valid_product_style_type, $exists_vars);
    }
    $product_style_type_data = $wpdb->get_results($valid_product_style_type, ARRAY_A);

    $exists_where = [];
    $exists_vars = [];

    $exists_where[] = 'p.`brand_id` = b.`brand_id`';
    $exists_where[] = 'p.`active` = 1';
    $exists_where = array_merge($exists_where, (array) $exclusion_where);
    if (!empty($top_change)) {
        $exists_where = array_merge($exists_where, (array) $top_change);
        $exists_vars = array_merge($exists_vars, (array) $top_change_vars);
    }
    $exists_where_text = implode(' AND ', $exists_where);

    $valid_brands = "
    	SELECT b.brand_id, b.brand_name 
    	FROM wp_rmproductmanagement_brands b
    	WHERE EXISTS(
    		SELECT 1
    		FROM `wp_rmproductmanagement` p
    		WHERE {$exists_where_text}
    	)
    	AND b.`brand_id` NOT IN (34, 40, 41, 42, 43, 44, 45)";

    if (!empty($exists_vars)) {
        $valid_brands = $wpdb->prepare($valid_brands, $exists_vars);
    }
    $brand_data = $wpdb->get_results($valid_brands, ARRAY_A);

    // this isn't going to work on the test server until it even has this table.
    // the query should otherwise tolerate the data in there as is though.
    $exists_where = [];
    $exists_vars = [];

    //the like ensures case insensitivity without a % wildcard and will still use the indexes
    $exists_where[] = '((p.`country_origin` like c.`name` OR p.`country_origin` like c.`alpha_2` OR p.`country_origin` like c.`alpha_3`) OR (p.`country_id` = c.`country_id` AND p.`country_id` != 1))';
    $exists_where[] = 'p.`active` = 1';
    $exists_where = array_merge($exists_where, (array) $exclusion_where);
    if (!empty($top_change)) {
        $exists_where = array_merge($exists_where, (array) $top_change);
        $exists_vars = array_merge($exists_vars, (array) $top_change_vars);
    }
    $exists_where_text = implode(' AND ', $exists_where);

    $valid_countries = "
    	SELECT c.`country_id`, c.`name`
    	FROM cfs_countries c
    	WHERE EXISTS(
    		SELECT 1
    		FROM `wp_rmproductmanagement` p
    		WHERE {$exists_where_text}
    	)";

    /* doesn't work on test server, don't do this.
    if (!empty($exists_vars)) { $valid_countries = $wpdb->prepare($valid_countries, $exists_vars); }
    $countries_data = $wpdb->get_results($valid_countries, ARRAY_A);
    */

    // should be fed results in [key, value] column pairs. the index is ignored.
    function generate_option(&$selector, $index, $gen_data = 0)
    {
        $keys = array_keys($selector);
        $selected_item = is_array($gen_data['selected']) ? $gen_data['selected'][0] : $gen_data['selected'];
        $selected_text = $selected_item == $selector[$keys[0]] ? ' Selected' : '';
        //preserve indentation of first row
        $prefix = isset($gen_data['prefix']) && $index != 0 ? $gen_data['prefix'] : '';

        $option_text = $prefix . '<option value="%u"%s>%s</option>' . PHP_EOL;
        echo sprintf($option_text, $selector[$keys[0]], $selected_text, $selector[$keys[1]]);
    }

    function console_log($message, $prefix = '')
    {
        $empty_text = empty($message) ? 'empty' : '!empty';
        $message_text = is_array($message) ? implode(',', $message) : $message;
        $message_text = base64_encode($message_text);
        echo "<script>console.log('{$empty_text} {$prefix}' + atob('{$message_text}'));</script>";
    }
?>

<div class="header graphic_bg text-white-container">
    <img class="header_graphic" src="<?php echo get_template_directory_uri(); ?>/images/header_graphic.svg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-lg-8 text-center mx-auto py-40">
                <h1>Put your designs on a galaxy of products</h1>
               <?php atlas();?>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="text-center mx-auto py-40 product_filter">
            <form action="" method="post" name="frm" id="frm">

                <div class="row align-items-center">
                    <div class="product_filter_item col">
                        <label class="screen-reader-text" for="category_id">Category</label>
                        <!-- UPDATE CATEGORY ID -->
    		            <input type="hidden" id="p_category_id" name="p_category_id" value="<?php echo $category_id; ?>">
                        <select class="product-page-select-box" name="category_id" id="category_id" onChange="showStyle(this.value);">
                            <option value="">Category</option>
                            <!--USER SELECT CATEGORY-->'
                            <?php array_walk($category_data, 'generate_option', array('selected' => $category_id, 'prefix' => '')); ?>
                        </select>
                    </div>

                    <div class="product_filter_item col">
                        <label class="screen-reader-text" for="product_subcategory_id">Subcategory</label>
                        <!-- UPDATE STYLE ID -->
                        <input type="hidden" id="p_subcategory_id" name="p_subcategory_id" value="<?php echo $subcategory_id; ?>">
                        <select class="product-page-select-box" name="product_subcategory_id" id="product_subcategory_id" onChange="showSubcategory(this.value);">
                            <option value="">Subcategory</option>
                            <?php array_walk($subcategory_data, 'generate_option', array('selected' => $product_subcategory_id, 'prefix' => '')); ?>
                        </select>
                    </div>

                    <div class="product_filter_item col">
                        <label class="screen-reader-text" for="product_style_type_id">Style</label>
                        <!-- UPDATE STYLE ID -->
                        <input type="hidden" id="p_product_style_type_id" name="p_product_style_type_id" value="<?php echo $product_style_type_id; ?>">
                        <select class="product-page-select-box" name="product_style_type_id" id="product_style_type_id" onChange="showBrand(this.value);">
                            <option value="">Style</option>
                            <?php array_walk($product_style_type_data, 'generate_option', array('selected' => $product_style_type_id, 'prefix' => '')); ?>
                        </select>
                    </div>

                    <div class="product_filter_item col">
                        <label class="screen-reader-text" for="brand_id">Brand</label>
                        <!-- UPDATE SELECTED BRAND -->
                        <input type="hidden" id="p_brand_id" name="p_brand_id" value="<?php echo $brand_id; ?>">
                        <select class="product-page-select-box" name="brand_id" id="brand_id" onChange="showSelection(this.value);">
                            <option value="">Brand</option>
                            <?php array_walk($brand_data, 'generate_option', array('selected' => $brand_id, 'prefix' => '')); ?>
                        </select>
                    </div>

                    <div class="product_filter_item col">
                        <!-- UPDATE SELECTED COUNTRY ... SOMEDAY SOMEONE WILL NOTICE THIS IS AN ARRAY NOT A BOOL -->
                        <input type="hidden" id="p_country_id" name="p_country_id" value="<?php echo $country_id; ?>">
                        <input type="checkbox" name="country_id" id="country_id" value="1" onChange="showCountry(this.value);" <?php if ($country_id == 1) { echo 'checked'; } ?>>
                        <label class="d-inline-block ml-2" for="country_id">Made in USA</label>
                        <?php
                            /* and someday this can be a combo box
                            <!-- UPDATE SELECTED BRAND -->
                            <select name="country_id" id="country_id" onChange="showCountry(this.value);">
                                <option value="">--Select--</option>
                                <?php array_walk($country_data, 'generate_option', array('selected' => $country_id, 'prefix' => ''));?>
                            </select>
                            */
                        ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>


             
<?php
//PAGENATION//
    if (isset($_GET['pageno'])) {
        $pageno = $_GET['pageno'];
    } else {
        $pageno = 1;
    }
    $no_of_records_per_page = 21;
    $offset = ($pageno - 1) * $no_of_records_per_page;
?>

<?php
    /*
    foreach ($products as $product) {
        if ($i > 0) {
            $class = '';
        } else {
            $class = ' class="active" ';
            $i++;
        }
        print('<li data-href="' . str_replace(' ', '', str_replace('/', '', $products->inventory_model)) . '" ' . $class . '>');
    */

    //base the viewed products by selecting against the IDs seen in the selector, where exclusions happen as well
    //
    $where_sql = [];
    if (!empty($category_data)) {
        $list_categories = implode(',', array_column($category_data, 'category_id'));
        console_log('categories: ' . $list_categories);
        $where_sql[] = "i.`category_id` IN ({$list_categories})";
    }
    if (!empty($subcategory_data)) {
        $list_subcategories = implode(',', array_column($subcategory_data, 'product_subcategory_id'));
        console_log('subcategories: ' . $list_subcategories);
        $where_sql[] = "i.`product_subcategory_id` IN ({$list_subcategories})";
    }
    if (!empty($product_style_type_data)) {
        $list_product_style_type = implode(',', array_column($product_style_type_data, 'product_style_type_id'));
        console_log('style types: ' . $list_product_style_type);
        $where_sql[] = "i.`product_style_type_id` IN ({$list_product_style_type})";
    }
    if (!empty($brand_data)) {
        $list_brand = implode(',', array_column($brand_data, 'brand_id'));
        console_log('categories: ' . $list_categories);
        $where_sql[] = "i.`brand_id` IN ({$list_brand})";
    }
    /*
    // This does not currently work due to missing db structures and lack of selector expressing this data
    if (!empty($country_data)) {
        $list_country = implode(',',array_column($country_data, 'country_id'));
        $where_sql[] = "i.`country_id` IN ({$list_country})";
    }
    */
    if ($country_id === 1) {
        //this is a manual triage for bad input data. A country_id of 0 is presumed to be undefined, and therefore null.
        $where_sql[] = "(i.`country_id` = 1 OR i.`country_origin` = 'US' OR i.`country_origin` = 'USA')";
    }

    $where_sql[] = 'i.`active` = 1';
    $where_text = implode(' AND ', $where_sql);

    $products_query[] = "
        FROM wp_rmproductmanagement i
        LEFT JOIN wp_rmproductmanagement_brands b ON b.brand_id = i.brand_id
        WHERE {$where_text}";
    $count_query = [];
    $count_query = 'SELECT count(1)';
    $count_query = array_merge(array('SELECT count(1)'), $products_query);
    $count_query = implode(' ', $count_query);
    console_log(json_encode($count_query), 'view count query:');

    //DO MATHS//
    $total_rows = $wpdb->get_var($count_query);
    $total_rows = empty($total_rows) ? 0 : $total_rows; //eat nulls
    $total_pages = ceil($total_rows / $no_of_records_per_page);
    console_log("Total Rows: {$total_rows}, Total Pages: {$total_pages}");

    $view_query = [];
    $view_query[] = "
        SELECT i.inventory_id, i.inventory_image, i.inventory_description,
        b.brand_name, i.inventory_model,
        i.inventory_price + i.white_first_side AS base_price";
    $view_query = array_merge($view_query, $products_query);
    $view_query[] = 'ORDER BY b.`brand_name` ASC, i.`date_added` DESC';
    $view_query[] = "LIMIT {$offset},{$no_of_records_per_page}";
    $view_query = implode(' ', $view_query);
    console_log(json_encode($view_query), 'view query: ');
    $products = $wpdb->get_results($view_query);
?>
<div class="container-fluid">
    <div class="row py-40">
    <?php //DISPLAY ACTIVE PRODUCTS//
    foreach ($products as $product) { ?>

        <div class="col-sm-6 col-lg-4 text-center py-20 mb-40">
            <img class="mb-20 img-fluid" src="<?php echo site_url(); ?>/wp-content/uploads/prd_disply_200_260/<?php echo $product->inventory_image; ?>">
            <a class="fs3 d-block mb-10" href="<?php echo get_site_url(); ?>/product-view?v=1&hdn=<?php echo base64_encode($product->inventory_id); ?>">From $<?php echo $product->base_price; ?></a>
            <a href="<?php echo get_site_url(); ?>/product-view?v=1&hdn=<?php echo base64_encode($product->inventory_id); ?>" class="btn-primary"><?php echo $product->brand_name . '&nbsp' . $product->inventory_model; ?></a>
        </div>

    <?php } ?>
    </div>
</div>


<?php
$pagination_prev_class = $pageno <= 1 ? 'disabled' : '';
$pagination_prev_pageno = $pageno <= 1 ? '#' : '?pageno=' . ($pageno - 1);
$pagination_next_class = $pageno >= $total_pages ? 'disabled' : '';
$pagination_next_pageno = $pageno >= $total_pages ? '#' : '?pageno=' . ($pageno + 1);
?>


  <!-- <div class="pagenation-wrapper">
    <ul class="pagination">
        <li><a href="?pageno=1">First</a></li>
        <li class="<?php echo $pagination_prev_class; ?>">
            <a href="<?php echo $pagination_prev_pageno; ?>">Prev</a>
        </li>
        <li class="<?php echo $pagination_next_class; ?>">
            <a href="<?php echo $pagination_next_pageno; ?>">Next</a>
        </li>
        <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
    </ul>
  </div> -->

<!--THE JAVASCRIPT-->
<script language="javascript" type="text/javascript" defer>

    function setFormAction(){
        document.frm.action = "/t-shirts";	
    }

    function showStyle(category_id) {
        document.frm.submit();
    }

    function showSubcategory(category_id) {
        document.frm.submit();
    }

    function showBrand(product_style_type_id) {
        document.frm.submit();
    }

    function showSelection(brand_id) {
        setFormAction();
        document.frm.submit();
    }

    function showCountry(country_id) {
        document.frm.submit();
    }

    //SHOW THE FORM ON TSHIRTS PAGE// 
    // GET URL
    var url = window.location.href;
    // GET DIV
    var hideform = document.getElementById('products-form-selector');
    // CHECK IF URL CONTAINS STRING
    if( url.search( 'products' ) > 0 ) {
      // DISPLAY THE FORM
      hideform.style.display = "initial";
    }

</script>
<!--END JAVASCRIPT-->

<?php get_footer(); ?>
