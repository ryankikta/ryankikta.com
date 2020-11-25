<?php
/* Template Name: Template Usage */
$current_user = wp_get_current_user();
if (user_can( $current_user, 'administrator' )) {
    $templates = wp_get_theme()->get_page_templates();
    $report = array();

    echo '<h1>Template Usage</h1>';
    echo "<p>This report will show pages in your WordPress site that are using one of your theme's custom templates.</p>";
        print_r($templates);
    foreach ( $templates as $file => $name ) {
        $q = new WP_Query( array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'meta_query' => array( array(
                'key' => '_wp_page_template',
                'value' => $file
            ) )
        ) );

        $page_count = sizeof( $q->posts );

        if ( $page_count > 0 ) {
            echo '<p>' . $file . ': <strong>' . sizeof( $q->posts ) . '</strong> pages are using this template:</p>';
            echo "<ul>";
            foreach ( $q->posts as $p ) {
                echo '<li><a href="' . get_permalink( $p, false ) . '">' . $p->post_title . '</a></li>';
            }
            echo "</ul>";
        } else {
            echo '<p style="color:red">' . $file . ': <strong>0</strong> pages are using this template.</p>';
        }

        foreach ( $q->posts as $p ) {
            $report[$file][$p->ID] = $p->post_title;
        }
     }
}
?>
