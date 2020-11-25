<?php get_header(); ?>

<section class="header graphic_bg text-white-container mb-40">
	<img class="header_graphic" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/header_graphic.svg">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-lg-8 text-center mx-auto py-40">
				<h1><?php echo $post->post_title; ?></h1>
			</div>
		</div>
	</div>
</section>

<section class="basic-content mb-80">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<?php echo apply_filters('the_content', $post->post_content); ?>
			</div><!-- col -->
		</div><!-- row -->
	</div><!-- /container -->
</section><!-- /basic content -->

<section class="two-col-form-image">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6 col-lg-4 offset-lg-1 two-col-form-image-content">
				<h2 class="fs1">DARE TO DREAM?</h2>
				<div class="mb-15">
					<h3 class="fs2">SIGN UP TO GET STARTED</h3>
					<p>And if nobody does? Somebody will turn up sooner or later. How long do you propose to wait? The engineer shrugged. Who is John Gait? He means, said the fireman, don't ask questions nobody can answer. She looked at the red light and at the rail that went off into the black, untouched distance. She said, Proceed with caution to the next signal.</p>
				</div>
				<form>
					<div class="input_outline">
						<input type="email" name="two-col-form-image-input" placeholder="JOHNDOE@GMAIL.COM" class="w-100" />
					</div> 
					<input type="submit" value="Sign Up" class="btn-primary" />
				</form>
			</div>
			<div class="col-md-6 col-lg-5 offset-lg-1 two-col-form-image-image">
				<img src="https://storage.googleapis.com/pa-assets/uploads/solutions/galaxy_hair_with_plants.png" alt="galaxy_hair_with_plants" class="img-fluid">
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>