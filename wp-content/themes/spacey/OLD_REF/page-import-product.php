<?php
// Only show if logged in
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
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
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(-1025.422 -588.821)"><path style="fill:none;stroke:#361181;stroke-width:2.5;" d="M1099.1,671.6h-27.5c-0.4,0-0.7-0.3-0.7-0.7l0,0v-27.8l-4.2,4.7c-0.3,0.3-0.7,0.3-1,0.1l-7-5.1c-0.3-0.2-0.4-0.7-0.2-1l7.5-12c1.3-2.1,3.6-3.5,6-3.7c0,0,4.2-0.7,6.6-1.1c0.4-0.1,0.7,0.2,0.8,0.5c1,3.2,4.5,5.1,7.7,4c1.9-0.6,3.4-2.1,4-4c0.1-0.4,0.5-0.6,0.8-0.5c2.5,0.4,6.6,1.1,6.6,1.1c2.5,0.2,4.7,1.6,6,3.7l7.5,12c0.2,0.3,0.1,0.8-0.2,1l-7,5.1c-0.3,0.2-0.7,0.2-1-0.1l-4.2-4.7v27.8C1099.9,671.3,1099.5,671.6,1099.1,671.6C1099.1,671.6,1099.1,671.6,1099.1,671.6z"/></g></svg> My Products</h1>

                    <h2 class="fs1">Import Products</h2>
                    <h3 class="fs2">Customize your atmosphere with a galaxy of products</h3>
                    <p>This page is only for those who are using one of our apps such as Shopify, Storenvy, the API, or other integrations. All other manually placed orders do not use this system. To learn more about integrations, <a href="#">click here</a>.</p>
                </div>
            </div>
            <div class="col-12 col-lg-6 p-0 text-center">
                <h2 class="fs3">Get Started</h2>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid">
	<div class="table_base_wrapper product_import_table">
		<h2 class="fs1">Etsy</h2>
		<table class="table_base">
			<tr>
				<th>Image</th>
				<th>Product Title</th>
				<th>Status</th>
				<th>URL</th>
				<th>Action</th>

			</tr>
			<tr>
				<td><img class="img-fluid" width="auto" height="40px" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_hat.png"></td>
				<td>This is a product title</td>
				<td>Edit</td>
				<td><a href="#">Click to see product</a></td>
				<td><a href="#">Import</a></td>
			</tr>

			<tr>
				<td><img class="img-fluid" width="auto" height="40px" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_hat.png"></td>
				<td>This is a product title</td>
				<td>Edit</td>
				<td><a href="#">Click to see product</a></td>
				<td><a href="#">Import</a></td>
			</tr>
		</table>
	</div>
</div>

<?php include('includes/manual_api.php'); ?>

<?php get_footer(); ?>