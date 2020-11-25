<?php
/**
 * Tabs
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
$content = get_field('content');
?>

<?php if ( have_rows('tabs') ): ?>
    <section class="tabs-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <?php if ( $title ): ?>
                        <h2 class="fs1"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>
                    <?php if ( $content ): ?>
                        <div><?php echo apply_filters( 'the_content', $content ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mt-50">
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    $i = 0;
                    while ( have_rows('tabs') ):
                        the_row();
                        $i++;
                        $tab = 'tab_' . $i;
                        if ( $i == 1 ):
                            $isSelected = 'true';
                        else:
                            $isSelected = 'false';
                        endif;
                        $title = esc_html( get_sub_field( 'tab_title' ) );
                        ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fs3<?php if ( $i == 1 ) { echo ' active show'; } ?>" id="add-tab" data-toggle="tab" href="#<?php echo $tab; ?>" role="tab" aria-selected="<?php echo $isSelected; ?>"><span class="fs3"><?php echo $title; ?></span></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <div class="tab-content tab_content_grey">
                    <?php
                    $i = 0;
                    while ( have_rows('tabs') ):
                        the_row();
                        $i++;
                        $tab = 'tab_' . $i;
                        $content = apply_filters( 'the_content', get_sub_field('tab_content') );
                        ?>
                        <div class="tab-pane fade<?php if ( $i == 1 ) { echo ' show active'; } ?>" id="<?php echo $tab ?>" role="tabpanel"><?php echo $content; ?></div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>