<?php get_header(); ?>

<?php
//wp_set_current_user("615");
echo apply_filters('the_content', $post->post_content);
?>

		</div><!-- col -->
	</div><!-- row -->
</div><!-- /container -->

<div class="header graphic_bg text-white-container">
	<img class="header_graphic" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/header_graphic.svg">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-lg-8 text-center mx-auto py-40">
				<h1>Print On Demand Blog</h1>
			</div>
		</div>
	</div>
</div>

<section class="blog-posts py-40">
	<div class="container-fluid">

		<div class="row blog-posts">
			<?php
                        $pageNumber = ($paged) ? $paged : 1;
                        //echo ($pageNumber);
			$args = array(
				'post_type' => 'post',
				'posts_per_page' => 12,
				'post_status' => 'publish',
				'paged' => $pageNumber,
			);
			//print_r ($args);
			$query = new WP_Query($args);
			//print_r ($query);
			$posts = $query->posts;
			//print_r ($posts);
			foreach($posts as $post):
				$pid = $post->ID;
			        //echo ($post->ID);
				$featuredImage = wp_get_attachment_image_src( get_post_thumbnail_id($pid), 'medium' );
				$featuredImageURL = $featuredImage[0];
				//$cats = get_the_category($pid); // get post cat
				//$cats = get_the_terms($pid, 'CUSTOM_CAT'); // get custom taxonomy cat
				$content = wp_trim_words( $p->post_content, 25, '...' );
				?>
				<article class="col-md-6 col-lg-4 col-xl-3 mb-30" id="post-<?php echo $pid; ?>">
					<a href="<?php echo get_permalink($pid); ?>" class="card">
						<?php if($featuredImage): ?>
							<img class="img-fluid mb-20" src="<?php echo $featuredImageURL; ?>" alt="<?php echo $p->post_title; ?>">
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

<?php get_footer(); ?>
