<?php
$button = get_field('button');
$button2 = get_field('button_2');
$title = get_field('title');
$image = get_field('image');
$imageSide = get_field('image_side');
?>

<div class="row align-items-center justify-content-center justify-content-lg-between">
    <div class="order-2 <?php if ($imageSide == 'Right') : echo 'order-lg-1'; endif; ?> offset-lg-1 col-12 col-lg-6 col-xl-5 py-40">
        <?php if ($title) : ?>
            <h2 class="fs1"><?php echo $title; ?></h2>
        <?php endif; ?>
        <?php the_field('content'); ?>
        <?php if ($button) : ?>
            <a class="btn-primary mt-20" href="<?php echo esc_url($button['url']); ?>" target="<?php echo $button['target']; ?>"><?php echo esc_attr($button['title']); ?></a>
        <?php endif; ?>
        <?php if ($button2) : ?>
            <a class="btn-secondary mt-20" href="<?php echo esc_url($button2['url']); ?>" target="<?php echo $button2['target']; ?>"><?php echo esc_attr($button2['title']); ?></a>
        <?php endif; ?>
    </div>
    <div class="order-1 text-center <?php if ($imageSide == 'Right') : echo 'order-lg-2'; endif; ?> col-12 col-lg-5">
        <?php if ($image) : ?>
            <img class="img-fluid" src="<?php echo esc_url(CLOUD_URL_Assets . '/uploads/solutions/' . $image); ?>">
        <?php endif; ?>
    </div>
</div>
