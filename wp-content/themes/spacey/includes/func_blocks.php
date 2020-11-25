<?php 
function pa_block_categories( $categories, $post ) {
    if ( $post->post_type !== 'post' ) {
        return $categories;
    }
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'ryankikta-blocks',
                'title' => __( 'RyanKikta Blocks', 'ryankikta-custom' ),
                'icon'  => 'smiley',
            ),
        )
    );
}
add_filter( 'block_categories', 'pa_block_categories', 10, 2 );

function register_acf_block_types() {


    $name = 'test';
    acf_register_block_type(array(
        'name'              => $name,
        'title'             => __('Test'),
        'description'       => __(''),
        'render_template'   => 'blocks/'. $name .'.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'admin-comments',
        'keywords'          => array( 'list', 'columns' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/'. $name .'.css',
        // 'enqueue_script' => get_template_directory_uri() . 'js/blocks/'. $name .'.js',
    ));

    acf_register_block_type(array(
        'name'              => 'buckets',
        'mode'              => 'auto',
        'title'             => __('3 Column Buckets'),
        'description'       => __(''),
        'render_template'   => 'blocks/buckets.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'editor-alignleft',
        'keywords'          => array( 'list', 'columns' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/buckets.css',
    ));

    acf_register_block_type(array(
        'name'              => 'icon_header',
        'mode'              => 'auto',
        'title'             => __('Icon Header'),
        'description'       => __(''),
        'render_template'   => 'blocks/icon_header.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'carrot',
        'keywords'          => array( 'list', 'columns' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/icon_header.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));

    acf_register_block_type(array(
        'name'              => 'image_with_content',
        'mode'              => 'auto',
        'title'             => __('Image with Content'),
        'description'       => __(''),
        'render_template'   => 'blocks/image_with_content.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-pull-right',
        'keywords'          => array( 'image', 'content' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/image_with_content.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'video_with_content',
        'mode'              => 'auto',
        'title'             => __('Video with Content'),
        'description'       => __(''),
        'render_template'   => 'blocks/video_with_content.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'playlist-video',
        'keywords'          => array( 'video', 'content' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/video_with_content.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'hero_banner',
        'mode'              => 'auto',
        'title'             => __('Hero Banner'),
        'description'       => __(''),
        'render_template'   => 'blocks/hero_banner.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'hero', 'banner', 'header' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/hero_banner.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'two_column_form_image',
        'mode'              => 'auto',
        'title'             => __('Two Column Form + Image'),
        'description'       => __(''),
        'render_template'   => 'blocks/two_col_form_image.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'form', 'image' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/two_col_form_image.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'icon_tabs',
        'mode'              => 'auto',
        'title'             => __('Icon Tabs'),
        'description'       => __(''),
        'render_template'   => 'blocks/icon_tabs.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'icon', 'tabs' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/icon_tabs.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'ordered_list_with_image',
        'mode'              => 'auto',
        'title'             => __('Ordered List with Image'),
        'description'       => __(''),
        'render_template'   => 'blocks/ordered_list_with_image.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'ordered', 'list', 'image' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/ordered_list_with_image.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'table',
        'mode'              => 'auto',
        'title'             => __('Table'),
        'description'       => __(''),
        'render_template'   => 'blocks/table.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'table' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/table.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'accordions',
        'mode'              => 'auto',
        'title'             => __('Accordions'),
        'description'       => __(''),
        'render_template'   => 'blocks/accordions.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'form', 'image' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/accordions.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
    acf_register_block_type(array(
        'name'              => 'tabs',
        'mode'              => 'auto',
        'title'             => __('Tabs'),
        'description'       => __(''),
        'render_template'   => 'blocks/tabs.php',
        'category'          => 'ryankikta-custom',
        'icon'              => 'align-full-width',
        'keywords'          => array( 'tabs' ),
        'enqueue_style' => get_template_directory_uri() . '/css/blocks/tabs.css',
        'supports' => array(
            'align' => false,
            'align_text' => false,
            'align_content' => false,
        ),
    ));
}

// Check if function exists and hook into setup.
if( function_exists('acf_register_block_type') ) {
    add_action('acf/init', 'register_acf_block_types');
}