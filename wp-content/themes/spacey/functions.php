<?php
/* Helper functions for debugging and hit tracking.
-------------------------------------------------------------------*/
//require_once 'includes/pdo-functions.php'; // prerequeisite of debug-functions.php
//require_once 'includes/debug-functions.php';

// add css and scripts
function bigcity_add_css_scripts() {
    // css
    wp_enqueue_style( 'dropzone', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.css');
    wp_enqueue_style( 'main', get_template_directory_uri().'/css/main.css');

	// scripts
	wp_enqueue_script( 'bootstrap', get_template_directory_uri().'/js/bootstrap.bundle.min.js', array(), '', true );
    wp_enqueue_script( 'slick', get_template_directory_uri().'/js/slick.min.js', array(), '', true );
    wp_enqueue_script( 'dropzone', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js', array(), '', true );
    wp_enqueue_script( 'main', get_template_directory_uri().'/js/main.js', array(), '', true );

    // custom js files
    if (is_page('add-products')) {
        wp_enqueue_script( 'addproducts', get_template_directory_uri().'/js/addproducts.js', array(), '', true );
    }

    if(is_page(array('my-images', 'user-images'))){
	    wp_enqueue_script('userimages', get_template_directory_uri() . '/js/userimages.js', array(), '', true);
    }
}
add_action( 'wp_enqueue_scripts', 'bigcity_add_css_scripts' );


/* stuff for the client and defaults for wordpress
-------------------------------------------------------------------*/
include('includes/func_defaults.php');

/* custom post types
-------------------------------------------------------------------*/
include('includes/func_post_types.php');

/* custom blocks
-------------------------------------------------------------------*/
include('includes/func_blocks.php');

/* custom functions
-------------------------------------------------------------------*/
include('includes/ryankikta-functions.php');

/* custom functions
-------------------------------------------------------------------*/
include('includes/ryankikta-shortcodes.php');// call custom shortcodes


//add_theme_support( 'post-thumbnails' );

// add custom logo to login page
function bigcity_login_logo_css() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png);
            height: 170px;
            width: inherit;
            background-size: contain;
        }
        <?php // style login page ?>
/*
        body.login { background: #333; }
        .login #backtoblog a,
        .login #nav a { color: #fff !important; }
*/
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'bigcity_login_logo_css' );


// add custom css or js to admin
function bigcity_custom_admin_css() { ?>
    <style type="text/css">
	    /* limit annoying category spacing description field */
        .edit-tags-php .description.column-description { white-space: nowrap; overflow: auto; }

        /* client help dashboard widget */
        #client_help .inside h2 { font-size: 16px; line-height: inherit; padding: 0 0 10px 0; font-weight: bold; }
        #client_help .inside ul { list-style-type: disc; padding-left: 25px; }
        #client_help .inside ul,
        #client_help .inside ol { margin-bottom: 25px; margin-top: 0; }
        #client_help .inside p,
        #client_help .inside li { font-size: 14px; }
        #client_help .inside a { text-decoration: underline; }

        /* make nav menus box bigger */
        .posttypediv div.tabs-panel { max-height: 800px !important; }

        /* hide post categories */
/*
        #categorydiv,
        .column-categories { display: none; }
*/
        /* hide post tags - linked with "my_remove_sub_menus" below */
/*
        #tagsdiv-post_tag,
        .column-tags { display: none; }
*/
    </style>
<?php }
add_action( 'admin_head', 'bigcity_custom_admin_css' );
function getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
{
    //defaults
    if (!$adjacents)
        $adjacents = 1;
    if (!$limit)
        $limit = 15;
    if (!$page)
        $page = 1;
    if (!$targetpage)
        $targetpage = "/";

    //other vars
    $prev = $page - 1;         //previous page is page - 1
    $next = $page + 1;         //next page is page + 1
    $lastpage = ceil($totalitems / $limit);    //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;        //last page minus 1

    /*
      Now we apply our rules and draw the pagination object.
      We're actually saving the code to a variable in case we want to draw it more than once.
     */
    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<div style=\"margin-top:5px;\" class=\"pagination\"";
        if ($margin || $padding) {
            $pagination .= " style=\"";
            if ($margin)
                $pagination .= "margin: $margin;";
            if ($padding)
                $pagination .= "padding: $padding;";
            $pagination .= "\"";
        }
        $pagination .= ">";

        //previous button
        if ($page > 1)
            $pagination .= "<a class=\"orange\" href=\"$targetpage$pagestring$prev\">« prev</a>";
        else
            $pagination .= "<span class=\"disabled\">« prev</span>";

        //pages
        if ($lastpage < 7 + ($adjacents * 2)) { //not enough pages to bother breaking it up
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= "<span class=\"current\">$counter</span>";
                else
                    $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
            }
        } elseif ($lastpage >= 7 + ($adjacents * 2)) { //enough pages to hide some
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 3)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                }
                $pagination .= "<span class=\"elipses\">...</span>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";
            } //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . "1\">1</a>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . "2\">2</a>";
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                }
                $pagination .= "<span class=\"elipses\">...</span>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";
            } //close to end; only hide early pages
            else {
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . "1\">1</a>";
                $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . "2\">2</a>";
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";
                }
            }
        }

        //next button
        if ($page < $lastpage)
            $pagination .= "<a class=\"orange\" href=\"" . $targetpage . $pagestring . $next . "\">next »</a>";
        else
            $pagination .= "<span class=\"disabled\">next »</span>";
        $pagination .= "</div>\n";
    }

    return $pagination;
}

function bigcity_enqueue_gutenberg() {
    // Make sure you link this to your actual file.
    wp_register_style( 'bigcity_gutenberg', get_template_directory_uri() . '/css/wp.css' );
    wp_enqueue_style( 'bigcity_gutenberg' );
}
add_action( 'enqueue_block_editor_assets', 'bigcity_enqueue_gutenberg' );

function enqueue_block_styles() {
    wp_enqueue_style( 'main', get_template_directory_uri().'/css/main.css');
    wp_enqueue_style( 'buckets', get_template_directory_uri().'/css/buckets.css');
    wp_enqueue_style( 'hero_banner', get_template_directory_uri().'/css/hero_banner.css');
    wp_enqueue_style( 'icon_header', get_template_directory_uri().'/css/icon_header.css');
    wp_enqueue_style( 'image_with_content', get_template_directory_uri().'/css/image_with_content.css');
    wp_enqueue_style( 'video_with_content', get_template_directory_uri().'/css/video_with_content.css');
    wp_enqueue_style( 'video_with_content', get_template_directory_uri().'/css/accordions.css');
}
add_action( 'enqueue_block_editor_assets', 'enqueue_block_styles' );

/**
 * Display a Member Profile Form that allows members to edit their information on the front end.
 * Supports the core WordPress User fields that PMPro uses.
 * Add Ons and other plugins can hook into this form using the pmpro_show_user_profile action.
 *
 */
function edit_profile( $atts, $content=null, $code='' ) {
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_member_profile_edit]

	ob_start();

	// Get the current action for the view.
	if ( ! empty( $_REQUEST[ 'view' ] ) ) {
		$view = sanitize_text_field( $_REQUEST[ 'view' ] );
	} else {
		$view = NULL;
	}

	if ( ! empty( $view ) && $view == 'change-password' ) {
		// Display the Change Password form.
		pmpro_change_password_form();
	} else {
		// Display the Member Profile Edit form.
		pmpro_member_profile_edit_form();
	}

	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'pmpro_member_profile_edit', 'edit_profile' );
