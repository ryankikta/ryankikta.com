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

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>Orders</h1>
                    <h2 class="fs1">Import Orders</h2>
                    <h3 class="fs2">Get Started</h3>
                    <?php atlas(); ?>
                </div>
			</div>
			<div class="col-12 col-lg-6 p-0 text-center">
				<h2 class="fs3">IMPORT ORDERS</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
        </div>
    </div>
</div>

<div class="container-fluid py-40">
    <div id="importOrderStepOne" class="row align-items-center justify-content-center justify-content-lg-between">
        <div class="col-12 col-lg-6 py-40">
            <h2 class="fs1">Step 1 of 2</h2>
            <p>Welcome to Ryan Kikta's CSV order import tool. The CSV Upload Tool allows you to upload an order in the form of a CSV file. A CSV file, or <a href="http://en.wikipedia.org/wiki/Comma-separated_values" target="_blank">comma-separated values</a> file, is perhaps the first common way to exchange large sets of data between different systems. The file itself is nothing more than a text file with some agreed-upon rules. Most data storage systems provide CSV export functionality.</p>
            <p>In order to get started, we first need to get hooked in to the API. <a href="/seller-api/" target="_blank">Your API key and hash can be found here.</a></p>
            <div class="input_outline input_flex">
                <label for="">What is your Ryan Kikta API Key?</label>
                <input type="text" name="" id="" placeholder="API Key">
            </div>
            <div class="input_outline input_flex">
                <label for="">What is your Ryan Kikta API Hash?</label>
                <input type="text" name="" id="" placeholder="API Hash">
            </div>

            <div class="btn btn-primary" id="importOrderContinue">Continue</div>
        </div>
    </div>
    <div id="importOrderStepTwo" class="row align-items-center justify-content-center justify-content-lg-between">
        <div class="col-12 col-md-6 py-40">
            <h2 class="fs1">Step 2 of 2</h2>
            <p>Great! Now we need to establish some things about your CSV file.</p>
            <p>Your file <b>must</b> contain only one record per line, and the first record <b>must</b> consist of field
                names that map subsequent records to our system. That is to say, the first row in your file should be
                the column names.</p>
            <p>The Ryan Kikta field/column names, in no particular order, are:</p>
            <ul>
                <li><b>order_id</b>: Your order ID identifier for the order, unique for each order.</li>
                <li><b>rush </b>: Rush Order ($2.00 each) rush processing is typically 2 business days instead of 4-5
                    business days. Daily cutoff is 12:30ET <b><a>Value : </a></b> ( Yes : 1 ; No : 0 ).
                </li>
                <li><b>shipping_method</b>: <a>Priority,Standard Canada,Standard International,First Class</a>.</li>
            </ul>
            <br>
            <h4>Shipping Address (required)</h4>
            <ul>
                <li><b>customer_name (required)</b>: Customer name.</li>
                <li><b>address1 (required)</b>: address1 .</li>
                <li><b>address2 (optional)</b>: address2 .</li>
                <li><b>city (required)</b>: City .</li>
                <li><b>state (required)</b>: State/Province.</li>
                <li><b>zip_code (required)</b>: Zip Code.</li>
                <li><b>country (required)</b>: Country.</li>
                <li><b>customer_phone (required)</b>: Customer Phone (required only for International orders).</li>
            </ul>
            <br>
            <h4>Order details (required) For EACH order please specify: </h4>
            <ul>
                <li><b>parent_order_id (required)</b>: to map to a parent order.</li>
                <li><b>brand (required)</b>: Brand name .</li>
                <li><b>product (optional)</b>: Product name .</li>
                <li><b>color (required)</b>: color .</li>
                <li><b>size (required)</b>: size.</li>
                <li><b>quantity (required)</b>: quantity.</li>
                <li>front_print: File name of the front design image for printing.</li>
                <li>back_print: File name of the back design image for printing.</li>
                <li>front_mockup: File name of the mockup image for showcasing the front.</li>
                <li>back_mockup: File name of the mockup image for showcasing the back.</li>
            </ul>
            <div class="bs-callout bs-callout-info">
                <ul>
                    <li><b>Bold</b> fields are required.</li>
                    <li><a href="/csv-help/" target="_blank">A table of available brands, products, and colors is available here.</a></li>
                    <li>Brand names, their SKUs, and colors are not case sensitive.</li>
                    <li>Unknown or empty fields are ignored.</li>
                    <li>Image file names should exactly match those in the <a href="/upload-images/" target="_blank">Ryan Kikta image manager</a>.
                    </li>
                </ul>
            </div>
            <label for="">Select your CSV file (maximum size: 500M)</label>
            <form action="/file-upload" class="dropzone">
                <div class="fallback">
                    <input class="input_grey file_upload" type="file">
                </div>
            </form>
            <div><a href="/pa.example.orderimport.csv">Example CSV</a></div>
            <div class="btn btn-primary mt-5">Import CSV to Ryan Kikta</div>
        </div>
    </div> <!-- content-wrapper -->
</div>
<?php get_footer();?>

