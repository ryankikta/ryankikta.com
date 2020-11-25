<?php
/**
 * Two Column Form + Image
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

$title = get_field('title');
$content = get_field('content');
$action = get_field('form_action');
$image = get_field('image');
$image_side = get_field('image_side');
?>

<section class="two-col-form-image">
    <?php if ( $image_side == 'Right' ): ?>
        <div class="row">
            <div class="col-md-6 col-lg-4 offset-lg-1 two-col-form-image-content">
    <?php else: ?>
        <div class="row flex-md-row-reverse">
            <div class="col-md-6 col-lg-4 offset-lg-2 two-col-form-image-content">
    <?php endif; ?>
            <?php if ($title): ?>
                <h2 class="fs1"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($content): ?>
            <div class="mb-15">
                <?php the_field('content'); ?>
            </div>
            <?php endif; ?>
            <form action="<?php echo $action; ?>">
                <div class="input_outline">
                    <input type="email" name="two-col-form-image-input" placeholder="JOHNDOE@GMAIL.COM" class="w-100" />
                </div> 
                <input type="submit" value="Sign Up" class="btn-primary" />
            </form>
        </div>
        <?php if ( $image_side == 'Right' ): ?>
            <div class="col-md-6 col-lg-5 offset-lg-1 two-col-form-image-image">
        <?php else: ?>
            <div class="col-md-6 two-col-form-image-image">
        <?php endif; ?>
            <img src="<?php echo esc_url(CLOUD_URL_Assets . '/uploads/solutions/' . $image); ?>" alt="<?php echo esc_html($title); ?>" class="img-fluid">
        </div>
    </div>
</section>