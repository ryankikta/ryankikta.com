<?php 
// If logged in, redirect to dashboard
$current_user = wp_get_current_user();
if (0 != $current_user->ID) {
    wp_redirect("/user-home");
    exit();
}

get_header(); ?>

<section class="basic-content py-40">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-10 col-lg-8 mx-auto text-center">
				<h1>Welcome to your atmosphere</h1>
				<h2 class="fs3">Login to your account</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#361181;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-lg-6 text-center mx-auto">
				<?php echo apply_filters('the_content', $post->post_content); ?>
			</div><!-- col -->
		</div><!-- row -->
	</div><!-- /container -->
</section><!-- /basic content -->

<?php get_footer(); ?>