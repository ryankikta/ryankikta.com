<?php
/**
 * Buckets Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

?>

<!-- <section>
	<div class="container-fluid"> -->
		<div class="row my-60">
			<?php 
			$alignment = get_field('alignment');
			$columns = get_field('columns');
			$buckets = get_field('buckets');

			foreach ($buckets as $bucket) { ?>

				<div class="bucket <?php if ($columns == '4') : echo 'col-lg-3'; else : echo 'col-lg-4'; endif; ?> col-sm-6<?php if($alignment == 'center') { echo ' text-center'; } ?>">
					<?php if ($bucket['image']) { ?>
						<img class="img-fluid mb-20" src="<?php echo $bucket['image']['url']; ?>" alt="<?php echo $bucket['image']['alt']; ?>">
					<?php } 

					if ($bucket['heading']) { ?>
						<h2><?php echo $bucket['heading'];?></h2>
					<?php } 

					if ($bucket['subheading']) { ?>
						<h3><?php echo $bucket['subheading'];?></h3>
					<?php } 

					if ($bucket['copy']) { ?>
						<p><?php echo $bucket['copy'];?></p>
					<?php } 

					if ($bucket['cta']['text'] && $bucket['cta']['url']) { ?>
						<a class="btn-primary" href="<?php echo $bucket['cta']['url']; ?>"><?php echo $bucket['cta']['text'];?></a>
					<?php }  ?>

				</div>

			<?php } ?>
		</div>
<!-- 	</div>
</section> -->