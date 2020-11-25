<?php
/**
 * Icon Tabs
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
?>

<?php if ( have_rows('icon_tabs') ): ?>
    <section class="icon-tabs">
        <div class="container-fluid">
            <?php if ( $title ): ?>
                <h2 class="fs1 text-center mb-50"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <div class="icon-tabs-row">
                <?php 
                $i = 0;
                while ( have_rows('icon_tabs') ): the_row(); 
                    $i++;
                    $icon = CLOUD_URL_Assets . '/uploads/solutions/' . get_sub_field('icon');
                    $heading = esc_html( get_sub_field('heading') );
                    ?>
                    <a class="icon-tabs-target <?php if ($i == 1) { echo 'icon-tabs-active'; } ?>" href="javascript:void(0);" data-icon-tab-target="accordion_<?php echo $i; ?>">
                        <img class="icon-tabs-target-icon" src="<?php echo $icon; ?>" alt="<?php echo $heading; ?>">
                        <p class="icon-tabs-target-label fs3"><?php echo $heading; ?></p>
                        <svg class="icon-tabs-target-arrow" xmlns="http://www.w3.org/2000/svg" width="36.704" height="17.648" viewBox="0 0 36.704 17.648"><path id="Path_8964" data-name="Path 8964" d="M684,793l15.716,12.287L715.086,793" transform="translate(-681.193 -790.189)" fill="none" stroke="#dedede" stroke-linecap="round" stroke-width="4"/></svg>
                    </a>
                <?php endwhile; ?>
            </div>
            <div class="icon-tabs-content-container">
                <?php
                $i = 0;
                while ( have_rows('icon_tabs') ): the_row();
                    $i++;
                    $heading = esc_html( get_sub_field('heading') );
                    $content = apply_filters( 'the_content', get_sub_field('content') );
                    ?>
                    <div class="icon-tabs-content col-lg-8 mx-auto <?php if ($i == 1) { echo 'icon-tabs-active'; } ?>" id="accordion_<?php echo $i; ?>">
                        <h3 class="fs2"><?php echo $heading; ?></h3>
                        <div class="small-heading-container"><?php echo $content; ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
<?php endif; ?>