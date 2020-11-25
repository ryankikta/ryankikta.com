<?php
/**
 * Accordions
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
$content = get_field('content');
?>

<?php if ( have_rows('accordions') ): ?>
    <section class="accordions">
        <div class="col-lg-10 mx-auto">
            <?php if ( $title ): ?>
                <h2 class="fs1"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ( $content ): ?>
                <div class="mb-30"><?php echo apply_filters( 'the_content', $content ); ?></div>
            <?php endif; ?>
            <?php while ( have_rows('accordions') ): 
                the_row();
                $heading = get_sub_field( 'heading' );
                $content = get_sub_field( 'content' );
                ?>
                <div class="accordion">
                    <a class="accordion-target" href="javascript:void(0);">
                        <?php echo esc_html( $heading ); ?>
                        <svg class="accordion-target-arrow" xmlns="http://www.w3.org/2000/svg" width="36.704" height="17.648" viewBox="0 0 36.704 17.648"><path id="Path_8964" data-name="Path 8964" d="M684,793l15.716,12.287L715.086,793" transform="translate(-681.193 -790.189)" fill="none" stroke="#dedede" stroke-linecap="round" stroke-width="4"/></svg>
                    </a>
                    <div class="accordion-content"><?php echo apply_filters( 'the_content', $content ); ?></div>
                </div>
            <?php endwhile; ?>            
        </div>
    </section>
<?php endif; ?>