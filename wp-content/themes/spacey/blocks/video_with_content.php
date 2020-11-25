<?php
$button = get_field('button_1');
$button2 = get_field('button_2');
$title = get_field('title');
$video = get_field('video');
$videoSide = get_field('video_side');
$bgImage = get_field('background_graphic');
?>
<div class="video-content-block row align-items-center justify-content-center justify-content-lg-between <?php if ($videoSide == 'left') : echo ' flex-row-reverse'; endif; ?>">
    <div class="order-2 <?php if ($videoSide == 'Right') : echo 'order-lg-1'; endif; ?> col-lg-7 col-xl-5 py-40">
        <?php if ($title) : ?>
            <h2 class="fs1"><?php echo $title; ?></h2>
        <?php endif; ?>
        <?php the_field('content'); ?>
        <?php if ($button) : ?>
            <a class="btn btn-primary mt-20" href="<?php echo esc_url($button['url']); ?>" target="<?php echo $button['target']; ?>"><?php echo esc_attr($button['title']); ?></a>
        <?php endif; ?>
        <?php if ($button2) : ?>
            <a class="btn btn-secondary mt-20" href="<?php echo esc_url($button2['url']); ?>" target="<?php echo $button2['target']; ?>"><?php echo esc_attr($button2['title']); ?></a>
        <?php endif; ?>
    </div>
    <div class="order-1 order-lg-2 col-md-12 col-lg-5 video-blob-<?php echo $videoSide; ?>">
        <?php if ($bgImage == true & $videoSide == 'right') : ?>
            <img class="d-none d-lg-block" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_hat_bg.png">
        <?php endif; ?>
        <?php if ($bgImage == true & $videoSide == 'left') : ?>
            <img class="d-none d-lg-block" src="https://storage.googleapis.com/pa-assets/uploads/solutions/home_integrate_left.svg">
        <?php endif; ?>
        <?php if ($video) : ?>
            <div class="videoWrapper">
                <?php echo $video; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
