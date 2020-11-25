<?php
/*
*
*Template Name: RyanKikta
*  
*/

global $wpdb;
/* If User Must Be Logged In To View */
/*$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/membership-account");
    exit();
}*/
$site_url = site_url();
get_header();
?>
<!--stuff goes here-->
    <div id="wrapper-404" class="d-none d-lg-block">
        <div id="container" class="container-404">
            <ul id="scene" class="scene">
                <li class="layer" data-depth="0.10"><img src="<?php echo CLOUD_URL_Assets?>/images/404-1.png"></li>
                <li class="layer" data-depth="0.40"><img src="<?php echo CLOUD_URL_Assets?>/images/404-2a.png"></li>
                <li class="layer" data-depth="0.25"><img src="<?php echo CLOUD_URL_Assets?>/images/404-3a.png"></li>
                <li class="layer" data-depth="0.35"><img src="<?php echo CLOUD_URL_Assets?>/images/404-4a.png"></li>
                <li class="layer" data-depth="0.15"><img src="<?php echo CLOUD_URL_Assets?>/images/404-5a.png"></li>
                <li class="layer" data-depth="0.20"><img src="<?php echo CLOUD_URL_Assets?>/images/404-6alt.png"></li>
                <li class="layer" data-depth="1.7"><img src="<?php echo CLOUD_URL_Assets?>/images/pa-astro-small.png"></li>
            </ul>
        </div>
    </div>

    <div class="header graphic_bg text-white-container mb-20 d-block d-lg-none">
        <img class="header_graphic" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/header_graphic.svg">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 text-center mx-auto py-40">
                        <h1 class="fs1">404</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-5">
        <div>
            <h1>Oops! Seems like you drifted away.</h1>
            <h3>Let's chart a course <a href="/" class="btn">Back home.</a></h3>
            </br>
            <script async src="https://cse.google.com/cse.js?cx=011903827041403045776:yskyqitapt7"></script> <div id="search-404" class="gcse-search"></div>
            <div class="mt-3">
                <p>You might be searching for:</p>
                <ul>
                    <li><a href="/support/">Support</a></li>
                    <li><a href="/user-home/">My Account</a></li>
                    <li><a href="https://blog.ryankikta.com/">Knowledge Base</a></li>
                    <li><a href="/advantage/">Advantage</a></li>
                    <li><a href="/tshirts/">Products</a></li>
                    <li><a href="/api/">API Docs</a></li>
                </ul>
            </div>
            </div>
        <!-- <div id="body-wrapper-404">
            <div id="mc4wp-404" class="my-5">
                <h3>Subscribe to our newsletter</h3>
                <p>Get product updates, company news, and more.</p>
                <?php
                //echo do_shortcode('[mc4wp_form id="12791939"]');
                ?>
            </div>

        </div> -->
    </div>
<section>
	<div class="container-fluid py-40">
		<div class="row align-items-center justify-content-between">
			<div class="col-md-6 col-lg-5 mb-40">
				<h2 class="fs1">Dare to dream?</h2>
				<h3 class="fs2">Sign up to get started</h3>
                                <?php atlas(); ?>
				<form>
					<div class="input_outline">
						<input type="email" placeholder="johndoe@gmail.com">
					</div>
					<input class="btn-primary" type="submit" value="Sign Up">
				</form>
			</div><!-- /col -->
			<div class="col-md-6 mb-40">
				<img src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/home_get_started.svg">
			</div>
        </div><!-- /row -->
	</div><!-- /container -->
</section>

<section>

</section>

<!-- Start Javascript -->
<script src="wp-content/themes/ryankikta/js/parallax.js"></script>
<!--script async src="https://cse.google.com/cse.js?cx=011903827041403045776:yskyqitapt7"></script-->
<script>
var scene = document.getElementById('scene');
var parallax = new Parallax(scene);
</script>
<?php
get_footer();
?>
