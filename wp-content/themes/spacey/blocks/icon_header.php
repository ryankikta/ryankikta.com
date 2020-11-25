<?php
/**
 * Icon header Block Template.
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
$icon = get_field('icon');
$heading = get_field('header_text');

?>
<?php if($background != 'none'): ?>
<section class="home_services">
	<div class="home_services_intro mt-40">
			<img class="home_services_intro_graphic" <?php if($background == 'right'): echo 'style="transform: translate(-50%, -50%) scaleX(-1);"'; endif; ?> src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/home_services_intro.svg">
<?php endif; ?>
		<div class="container-fluid">
			<div class="row">
				<div class="mx-auto col-md-8 col-xl-6 py-40 text-center <?php if($background != 'none'): echo 'text-white-container'; endif; ?>">
					<?php if ($icon) { ?>
						<img width="90" height="90" src="<?php echo CLOUD_URL_Assets . '/uploads/solutions/' . $icon . '.svg';?>" />
					<?php } ?>
					<?php if ($heading) { ?>
						<h2 class="fs3 mt-20"><?php echo $heading;?></h2>
					<?php } ?>
					
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
				</div>
			</div>
		</div>
<?php if($background != 'none'): ?>		
	</div>
</section>
<?php endif; ?>

<?php if (!$is_preview) { ?>
<!-- Reopen page template -->
<section class="basic-content py-40">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
<?php } ?>

