<?php
/**
 * Ordered List with Image
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
$button = get_field('button');
$image = get_field('image');
$imageSide = get_field('image_side');
$bgImage = get_field('background_graphic');
?>

<div class="ordered-list-with-image row align-items-center justify-content-center justify-content-lg-between <?php if ($imageSide == 'left') : echo ' flex-row-reverse'; endif; ?>">
    <div class="order-2 <?php if ($imageSide == 'Right') : echo 'order-lg-1'; endif; ?> col-lg-7 col-xl-5 py-40">
        <?php if ($title) : ?>
            <h2 class="fs1"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ( have_rows('ordered_list') ): ?>
            <ol class="ordered-list">
                <?php while ( have_rows('ordered_list') ): the_row();
                    $heading = esc_html( get_sub_field('heading') );
                    $content = apply_filters( 'the_content', get_sub_field('content') );
                    ?>
                    <li>
                        <div>
                            <h3 class="fs"><?php echo $heading; ?></h3>
                            <div><?php echo $content; ?></div>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ol>
        <?php endif; ?>
        <?php if ($button) : ?>
            <a class="btn-primary" href="<?php echo esc_url($button['url']); ?>" target="<?php echo $button['target']; ?>"><?php echo esc_attr($button['title']); ?></a>
        <?php endif; ?>
    </div>
    <div class="order-1 <?php if ($imageSide == 'right') : echo 'order-lg-2'; endif; ?> order-1 order-lg-2 col-lg-5 blob-container-<?php echo $imageSide; ?>">
        <?php if ($bgImage == true & $imageSide == 'right') : ?>
            <img class="d-none d-lg-block" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_integrate_right.svg">
        <?php endif; ?>
        <?php if ($bgImage == true & $imageSide == 'left') : ?>
            <img class="d-none d-lg-block" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_integrate_left.svg">
        <?php endif; ?>
        <?php if ($image): ?>
            <img class="img-fluid" src="<?php echo esc_url(CLOUD_URL_Assets . '/uploads/solutions/' . $image); ?>" alt="<?php echo esc_html($title); ?>">
        <?php endif; ?>
    </div>
</div>