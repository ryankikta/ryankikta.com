<?php /* Template Name: Dashboard Stores */ ?>
<?php
// Only show if logged in
$current_user = wp_get_current_user();
$currentuserid = $current_user->ID;
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
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="120px" height="120px" viewBox="0 0 120 120" style="overflow:visible;enable-background:new 0 0 120 120;" xml:space="preserve"> <style type="text/css"> .st0{opacity:0.5;} .st1{fill:#19CBC5;} .st2{fill:none;stroke:#361181;stroke-width:2.5;} </style> <defs> </defs> <g id="Group_11119_1_" transform="translate(-468 -282)"> <g id="Group_11118_1_" transform="translate(468 282)"> <g id="Group_7763_1_" transform="translate(0)" class="st0"> <ellipse id="Ellipse_179_1_" class="st1" cx="60" cy="60" rx="60" ry="60"/> </g> <g id="Group_7764_1_" transform="translate(14.374 14.374)"> <ellipse id="Ellipse_180_1_" class="st1" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/> </g> </g> <path id="Path_10757_1_" class="st2" d="M556.2,357.7c-0.9-1.4-4.9-7.1-4.9-7.1v-25.9c0-1.5-1.7-3.2-3.8-3.2h-39 c-2.1,0-3.8,1.7-3.8,3.2v25.9c0,0-4,5.7-4.9,7.1c-1.7,2.7-0.6,5.3,2.6,5.3h51.2C556.8,363.1,557.9,360.4,556.2,357.7z M530.8,359.9 h-6.4c-0.6,0-1-0.5-1-1c0-0.6,0.5-1,1-1h6.4c0.6,0,1,0.5,1,1C531.8,359.5,531.3,359.9,530.8,359.9z M547.3,348.9 c0,0.9-0.8,1.7-1.7,1.7l0,0h-35.6c-0.9,0-1.7-0.8-1.7-1.7V327c0-0.9,0.8-1.7,1.7-1.7h35.6c0.9,0,1.7,0.8,1.7,1.7c0,0,0,0,0,0 L547.3,348.9z"/> </g> </svg>My Stores</h1>
                    <h2 class="fs1">YOUR ATMOSPHERE</h2>
                    <h3 class="fs2">Get Started</h3>
                <p>The difference between a dream and reality is action! Make a plan and start taking steps to get to where you want to be. If you are dreaming of owning your own online store, let RyanKikta help you get started.
                </div>
			</div>
			<div class="col-12 col-lg-6 p-0">
				<table class="stores-table">
					<tr>
						<th>Type</th>
						<th>Shop</th>
						<th>Active</th>
					</tr>
                                        <?php
                                        $sql = ("SELECT os.source_name, e.shop, active FROM wp_users_shopify e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid
                                        UNION ALL
                                        SELECT os.source_name, e.shop, active FROM wp_users_storenvy e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid
                                        UNION ALL
                                        SELECT os.source_name, CONCAT('https://etsy.com/shop/', e.shop) AS shop, active FROM wp_users_etsy e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid
                                        UNION ALL
                                        SELECT os.source_name, e.shop, active FROM wp_users_woocommerce e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid
                                        UNION ALL
                                        SELECT os.source_name, e.shop, active FROM wp_users_bigcommerce e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid
                                        UNION ALL
                                        SELECT os.source_name, e.domain, active FROM wp_users_opencart e
                                        LEFT JOIN wp_rmproductmanagement_order_sources os ON os.source_id = e.shop_type
                                        WHERE e.users_id = $currentuserid;");

                                        $query = $wpdb->get_results(($sql), ARRAY_A);
                                             foreach ($query as $key=> $rows) {
						     echo "<tr>
							     <td><a href='my-integrations'>$rows[source_name]</a></td>
                                                             <td><a href='$rows[shop]'>$rows[shop]</a></td>
							     <td>
                                                                 <input type='hidden' name='active' value='$rows[active]' />
								 <input type='checkbox' name='my_name_visual_dummy' value='1' checked='checked' disabled='disabled' />
                                                            </td>
                                                           </tr>"; 
					     }
                                        ?>
				</table>
				<div class="text-center mt-5">
					<a href="integrations" class="btn-primary">Add New Store</a>
				</div>
			</div>
        </div>
    </div>
</div>

<section class="home_atmosphere dashboard_atmosphere py-lg-40">
	<img class="home_atmosphere_right" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/home_integrate_right.svg">
	<div class="home_atmosphere_intro py-40">
		<div class="container-fluid">
			<div class="row">
				<div class="mr-auto col-md-8 text-left">
					<h2 class="fs1">Integrate your atmosphere</h2>
					<?php atlas(); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row py-40 justify-content-between">
			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="shopify-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_shopify.png">
					<span class="fs3">Shopify</span>
				</a>
			</div>
			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="etsy-t-shirt-fulfillment-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_etsy.png">
					<span class="fs3">Etsy</span>
				</a>
			</div>
			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="storenvy-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_storenvy.png">
					<span class="fs3">Storenvy</span>
				</a>
			</div>

			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="opencart-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_opencart.png">
					<span class="fs3">Opencart</span>
				</a>
			</div>
			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="woocommerce-t-shirt-fulfillment-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_woocommerce.png">
					<span class="fs3">Woocommerce</span>
				</a>
			</div>
			<div class="col-10 col-md-4 mb-30 home_atmosphere_card">
				<a href="bigcommerce-app">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/integration_bigcommerce.png">
					<span class="fs3">Bigcommerce</span>
				</a>
            </div>
        </div>
        <!-- <div class="text-center">
            <a href="#" class="btn-primary">Learn More</a>
		</div> -->
</section>

<?php include('includes/manual_api.php'); ?>

<?php get_footer(); ?>
