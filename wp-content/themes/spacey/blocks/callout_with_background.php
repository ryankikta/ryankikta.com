<?php
/**
 * Callout with Background Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
 if (!$is_preview) { ?>

<!-- Close from page template -->
			</div><!-- col -->
		</div><!-- row -->
	</div><!-- /container -->
</section><!-- /basic content -->
<?php } ?>

<?php
$background = get_field('background');
$heading = get_field('heading');
$copy = get_field('copy');
$is_arrow = get_field('arrow');
?>

<section class="home_services">
	<div class="home_services_intro mt-40">
		<img class="home_services_intro_graphic" <?php if($background == 'right'): echo 'style="transform: translate(-50%, -50%) scaleX(-1);"'; endif; ?> src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/home_services_intro.svg">

		<div class="container-fluid">
			<div class="row">
				<div class="mx-auto col-md-8 col-xl-6 py-40 text-center text-white-container">
					<?php if ($heading) { ?>
						<h2 class="fs1"><?php echo $heading;?></h2>
					<?php } 
					if ($copy) { ?>
						<p class="mt-20"><?php echo $copy;?></p>
					<?php } 
					if ($is_arrow) { ?>
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>


<?php if (!$is_preview) { ?>
<!-- Reopen page template -->
<section class="basic-content py-40">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
<?php } ?>