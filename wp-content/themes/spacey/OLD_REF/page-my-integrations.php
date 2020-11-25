<?php /* Template Name: Dashboard Stores */ ?>
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
			<img class="dashboard_graphic dashboard_graphic_default"
				src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
			<div class="row">
				<div class="col-lg-7 col-xl-6">
					<h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg"
							xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="120px" height="120px"
							viewBox="0 0 120 120" style="overflow:visible;enable-background:new 0 0 120 120;"
							xml:space="preserve">
							<style type="text/css">
								.st0 {
									opacity: 0.5;
								}

								.st1 {
									fill: #19CBC5;
								}

								.st2 {
									fill: none;
									stroke: #361181;
									stroke-width: 2.5;
								}
							</style>
							<defs> </defs>
							<g id="Group_11119_1_" transform="translate(-468 -282)">
								<g id="Group_11118_1_" transform="translate(468 282)">
									<g id="Group_7763_1_" transform="translate(0)" class="st0">
										<ellipse id="Ellipse_179_1_" class="st1" cx="60" cy="60" rx="60" ry="60" />
									</g>
									<g id="Group_7764_1_" transform="translate(14.374 14.374)">
										<ellipse id="Ellipse_180_1_" class="st1" cx="45.6" cy="45.6" rx="45.6"
											ry="45.6" />
									</g>
								</g>
								<path id="Path_10757_1_" class="st2"
									d="M556.2,357.7c-0.9-1.4-4.9-7.1-4.9-7.1v-25.9c0-1.5-1.7-3.2-3.8-3.2h-39 c-2.1,0-3.8,1.7-3.8,3.2v25.9c0,0-4,5.7-4.9,7.1c-1.7,2.7-0.6,5.3,2.6,5.3h51.2C556.8,363.1,557.9,360.4,556.2,357.7z M530.8,359.9 h-6.4c-0.6,0-1-0.5-1-1c0-0.6,0.5-1,1-1h6.4c0.6,0,1,0.5,1,1C531.8,359.5,531.3,359.9,530.8,359.9z M547.3,348.9 c0,0.9-0.8,1.7-1.7,1.7l0,0h-35.6c-0.9,0-1.7-0.8-1.7-1.7V327c0-0.9,0.8-1.7,1.7-1.7h35.6c0.9,0,1.7,0.8,1.7,1.7c0,0,0,0,0,0 L547.3,348.9z" />
							</g>
						</svg>Integrations</h1>
					<h2 class="fs1">E-COMMERCE</h2>
					<h3 class="fs2">Get Started</h3>
					<p>Nullam faucibus ut lectus vitae posuere. Nullam sollicitudin nunc ipsum, quis malesuada orci
						rutrum sit amet. Maecenas nulla justo, rutrum id iaculis at, elementum non lacus. Proin non
						molestie erat, vitae mattis elit. Donec ac euismod metus, ut efficitur nibh. Suspendisse magna
						est, pharetra sed hendrerit sit amet, blandit nec enim. Donec porttitor neque nec consectetur
						faucibus. Aliquam pellentesque leo eleifend, dictum turpis vitae, mattis mi. Curabitur luctus
						tortor ligula, vitae ultricies diam viverra id. Maecenas vitae sem quis nulla mollis euismod. Ut
						et cursus risus. Morbi pretium mi sit amet consectetur porta. Donec mi urna, accumsan eu augue
						sed, laoreet maximus eros. Maecenas sodales mi justo, vitae luctus purus semper vitae.</p>
				</div>
			</div>
			<div class="col-12 col-lg-6 p-0 text-center">
				<h2 class="fs3">BROWSE INTEGRATIONS</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8">
					<path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;"
						d="M2,2l15.7,12.3L33.1,2" /></svg>
			</div>
		</div>
	</div>
</div>

<section class="store-integrations dashboard_atmosphere py-lg-40">
	<div class="container-fluid">
		<div class="input_dropdown d-block d-md-none input_flex">
			<select name="" id="">
				<option value="Shopify">Shopify</option>
				<option value="Etsy">Etsy</option>
				<option value="Storenvy">Storenvy</option>
				<option value="Opencart">Opencart</option>
				<option value="WooCommerce">WooCommerce</option>
			</select>
		</div>
		<div class="row py-40 justify-content-between text-center d-none d-md-flex">
			<div class="col-12 col-md-2 mb-30">
				<a href="#">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/shopify_tile.png">
					<div class="fs3 mt-3">Shopify</div>
					<div class="mt-5"><svg version="1.1" xmƒlns="http://www.w3.org/2000/svg" width="36" height="17"
							viewBox="0 0 35.1 16.8">
							<path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;"
								d="M2,2l15.7,12.3L33.1,2" /></svg></div>
				</a>
			</div>
			<div class="col-12 col-md-2 mb-30 store-img">
				<a href="#">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/etsy_tile.png">
					<div class="fs3 mt-3">Etsy</div>
				</a>
			</div>
			<div class="col-12 col-md-2 mb-30">
				<a href="#">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/storeenvy_tile.png">
					<div class="fs3 mt-3">Storenvy</div>
				</a>
			</div>

			<div class="col-12 col-md-2 mb-30 store-img">
				<a href="#">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/opencart_tile.png">
					<div class="fs3 mt-3">Opencart</div>
				</a>
			</div>
			<div class="col-12 col-md-2 mb-30">
				<a href="#">
					<img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/woocom_tile.png">
					<div class="fs3 mt-3">Woocommerce</div>
				</a>
			</div>
			<!-- <div class="col-10 col-md-2 mb-30 store-img">
				<a href="#">
					<img class="img-fluid" src="<?php //echo CLOUD_URL_Assets; ?>/uploads/solutions/bigcommerce_tile.png">
					<div class="fs3 mt-3">Bigcommerce</div>
				</a>
            </div> -->
		</div>
		<!-- <div class="text-center">
            <a href="#" class="btn-primary">Learn More</a>
		</div> -->
		<div class="row">
			<?php include('integrations/page-shopify.php')?>
			<!--div class="col col-md-6">
					<h2 class="fs1">SHOPIFY</h2>
					<h3 class="fs2">GET STARTED</h3>
					<p>To begin Installation of our Shopify Module, Please enter your Shop URL below. If you have already installed <a href="#">Our APP</a> simply login to your Shop at Shopify and you can Manage products under or Add new Products under.</p>
					<p>If you have any issues getting your store setup please <a href="#">Contact Us.</a></p>
					<p class="small-caption">DON'T HAVE A SHOP TO INSTALL?</p><br>
					<a href="#" class="btn btn-primary">Sign up for Shopify</a>
				</div>
				<div class="col col-md-6 pt-40">
					<p class="small-caption">THE URL OF THE SHOP</p>
					<div class="input_outline">
						<input type="text" placeholder="SHOP.COM"><br>
						<a href="#" class="btn btn-primary">INSTALL</a>
					</div>
				</div-->
		</div>
</section>



<?php include('includes/manual_api.php'); ?>

<?php get_footer(); ?>