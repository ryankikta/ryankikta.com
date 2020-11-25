 <?php
 $title = get_field('title');
 $content = get_field('content');
 $button = get_field('button');
 ?>

        </div>
    </div>
</div>
<div class="header header_block graphic_bg text-white-container mb-20">
	<img class="header_graphic" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/header_graphic.svg">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-lg-8 text-center mx-auto py-40">
				<?php if ($title) : ?>
                    <h1><?php echo $title; ?></h1>
                <?php endif; ?>
                <?php if ($content) : ?>
				    <p><?php echo $content; ?></p>
                <?php endif; ?>
				<?php if ($button) : ?>
                    <a href="<?php echo $button['url']; ?>" class="btn-primary mt-5" target="<?php echo $button['target']; ?>"><?php echo $button['title']; ?></a>
                <?php endif; ?>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
