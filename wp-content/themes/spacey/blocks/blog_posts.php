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

<section class="blog-posts">
	<div class="container-fluid">

		<div class="row blog-posts">
			<?php
			$pageNumber = ($paged) ? $paged : 1;
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => 12,
				'post_status' => 'publish',
				'paged' => $pageNumber,
			);
			$query = new WP_Query($args);
			foreach($query->posts as $p):
				$pid = $p->ID;
				$featuredImage = wp_get_attachment_image_src( get_post_thumbnail_id($pid), 'medium' );
				$featuredImageURL = $featuredImage[0];
				//$cats = get_the_category($pid); // get post cat
				//$cats = get_the_terms($pid, 'CUSTOM_CAT'); // get custom taxonomy cat
				$content = wp_trim_words( $p->post_content, 25, '...' );
				?>
				<article class="col-md-6 col-lg-4 mx-auto mb-30" id="post-<?php echo $pid; ?>">
					<a href="<?php echo get_permalink($pid); ?>" class="card">
						<?php if($featuredImage): ?>
							<!-- <img class="img-fluid mb-20" src="<?php echo $featuredImageURL; ?>" alt="<?php echo $p->post_title; ?>"> -->
							<img class="img-fluid mb-20" src="http://placehold.it/600x450">
						<?php else: ?>
							<img class="img-fluid mb-20" src="http://placehold.it/600x450">
						<?php endif; ?>
						<div class="card-body">
							<h2 class="fs3"><?php echo $p->post_title; ?></h2>
							<em class="post-date d-block mb-10"><?php echo get_the_date('', $pid); ?></em>
							<p><?php echo $content; ?></p>
							<button class="btn-primary">Read More</button>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div><!-- /row -->

		<div class="blog-pagination">
			<?php
				echo paginate_links( array(
					'current' => max( 1, $paged ),
					'total' => $query->max_num_pages
				) );
			?>
		</div><!-- /pagination -->

	</div><!-- /container -->
</section><!-- /page content -->