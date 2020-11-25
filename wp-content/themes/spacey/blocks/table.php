<?php
/**
 * Table
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
$content = get_field('content');
$num_col = get_field('number_of_columns');
$table_name = 'table_' . $num_col . '_columns';
$table = get_field($table_name);
if ( get_field('table_side') ):
	$table_side = get_field('table_side');
	$image = 'comet_' . get_field('table_side') . '.png';
else:
	$table_side = 'left';
	$image = 'comet_left.png';
endif;
?>

<section class="table-block">
	<?php if ( $table_side == 'left' ): ?>
		<div class="row flex-lg-row-reverse">
	<?php else: ?>
		<div class="row">
	<?php endif; ?>
		<div class="col-6 col-sm-4 col-md-3 mb-20 mb-lg-0">
			<img class="img-fluid" src="<?php echo esc_url(CLOUD_URL_Assets . '/uploads/solutions/' . $image); ?>" alt="Test">
		</div>
		<div class="col-lg-9 mx-auto table_base_wrapper">
			<h2 class="fs1"><?php echo esc_html( $title ); ?></h2>
			<div class="mb-20"><?php echo apply_filters( 'the_content', $content ); ?></div>
			<table class="table_base w-100">
				<tr>
					<?php for( $i = 0; $i < $num_col; $i++ ): ?>
						<th><?php echo esc_html($table['table_heading_row'][$i]['heading_cell']); ?></th>
					<?php endfor; ?>
				</tr>
				<?php foreach( $table['table_rows'] as $table_rows ): ?>
					<tr>
						<?php for ( $i = 0; $i < $num_col; $i++ ): ?>
							<td><?php echo esc_html($table_rows['row'][$i]['table_cell']); ?></td>
						<?php endfor; ?>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</div>
</section>